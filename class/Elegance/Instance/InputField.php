<?php

namespace Elegance\Instance;

use Closure;
use Elegance\Exception\InputException;
use Elegance\Prepare;

class InputField
{
    protected string $name;
    protected mixed $value;
    protected mixed $sanitazeValue;

    protected string $required;
    protected string $preventTag;

    protected ?array $scapePrepare = [];

    protected bool $checked = false;

    protected array $validate = [];
    protected array $sanitaze = [];

    function __construct(string $name, mixed $value)
    {
        $this->name = $name;
        $this->value = $value;

        $this->sanitazeValue = null;

        $this->required(true);
        $this->preventTag(true);
    }

    /** Se o valor do input deve ser tratado com preventTag tags */
    function preventTag(bool|string $preventTag): static
    {
        if (is_bool($preventTag))
            $preventTag = $preventTag ? 'preventTag' : '';

        $this->preventTag = $preventTag;

        return $this;
    }

    /** Se o input deve escapar as tags de prepare */
    function scapePrepare(bool|array $scapePrepare): static
    {
        if (is_bool($scapePrepare))
            $scapePrepare = $scapePrepare ? [] : null;

        $this->scapePrepare = $scapePrepare;

        return $this;
    }

    /** Define se o campo é obrigatório */
    function required(bool|string $required): static
    {
        if (is_bool($required))
            $required = $required ? 'required' : '';

        $this->required = $required;

        return $this;
    }

    /** Define uma regra de validação do campo */
    function validate(mixed $rule, ?string $message = null): static
    {
        if (is_closure($rule)) {
            $this->validate[] = [$rule, $message];
        } else if (is_bool($rule)) {
            $this->required($message ?? $rule);
        } else if (match ($rule) {
            FILTER_VALIDATE_IP,
            FILTER_VALIDATE_INT,
            FILTER_VALIDATE_MAC,
            FILTER_VALIDATE_URL,
            FILTER_VALIDATE_EMAIL,
            FILTER_VALIDATE_FLOAT,
            FILTER_VALIDATE_DOMAIN,
            FILTER_VALIDATE_REGEXP,
            FILTER_VALIDATE_BOOLEAN => true,
            default => false
        }) {
            $message = $message ?? $rule;
            $rule = fn ($value) => filter_var($value, $rule);
            $this->validate[] = [$rule, $message];
        } else if (is_class($rule, InputField::class)) {
            $message = $message ?? 'O campo [#] deve ser igual o campo ' . $rule->name;
            $rule = fn ($v) => $v == $rule->get();
            $this->validate[] = [$rule, $message];
        }

        $this->checked = false;
        return $this;
    }

    /** Define u modo de limpeza do campo */
    function sanitaze(Closure|int $sanitaze): static
    {
        $this->sanitaze[] = $sanitaze;

        $this->checked = false;
        return $this;
    }

    /** Captura o valor do campo */
    function get(): mixed
    {
        if (!$this->checked) {
            $this->checkValidate($this->value);
            $this->checked = true;
            $this->sanitazeValue = $this->applySanitaze($this->value);
        }
        return $this->sanitazeValue;
    }

    /** Verifica todas as regras de valudação */
    protected function checkValidate($value)
    {
        if ($this->required && is_blank($value))
            $this->error($this->required);

        $value = is_array($value) ? $value : [$value];

        foreach ($value as $v) {
            if ($this->preventTag)
                if (is_string($v) && strip_tags($v) != $v)
                    $this->error($this->preventTag);

            foreach ($this->validate as $validate) {
                list($rule, $message) = $validate;
                if (!$rule($v))
                    $this->error($message);
            }
        }
    }

    /** Aplica as funções de limpeza */
    protected function applySanitaze($value)
    {
        if (is_array($value)) {
            $value = array_map(fn ($v) => $this->applySanitaze($v), $value);
        } else {
            foreach ($this->sanitaze as $sanitaze) {
                if (is_closure($sanitaze)) {
                    $value = $sanitaze($value);
                } else {
                    $value = (match ($sanitaze) {
                        FILTER_SANITIZE_EMAIL => fn ($v) => strtolower(filter_var($v, FILTER_SANITIZE_EMAIL)),
                        FILTER_SANITIZE_NUMBER_FLOAT => fn ($v) => floatval(filter_var($v, FILTER_SANITIZE_NUMBER_FLOAT)),
                        FILTER_SANITIZE_NUMBER_INT => fn ($v) => intval(filter_var($v, FILTER_SANITIZE_NUMBER_INT)),
                        FILTER_SANITIZE_ENCODED,
                        FILTER_SANITIZE_ADD_SLASHES,
                        FILTER_SANITIZE_SPECIAL_CHARS,
                        FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                        FILTER_SANITIZE_URL,
                        FILTER_UNSAFE_RAW => fn ($v) => filter_var($v, $sanitaze),
                        default => fn ($v) => $v
                    })($value);
                }
            }

            if (is_string($value) && is_array($this->scapePrepare))
                $value = Prepare::scape($value, $this->scapePrepare);
        }
        return $value;
    }

    protected function error($message)
    {
        $message = Input::message($message);
        $message = trim($message);
        $message = prepare($message, $this->name);

        throw new InputException($message, 400);
    }
}