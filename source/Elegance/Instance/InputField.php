<?php

namespace Elegance\Instance;

use Closure;
use Elegance\Exception\InputException;
use Elegance\Prepare;

class InputField
{
    protected string $name;
    protected mixed $value;

    protected mixed $sanitazeValue = null;

    protected string $required;

    protected string $preventTag;
    protected ?array $scapePrepare;

    protected array $validate = [];
    protected array $sanitaze = [];
    protected bool $forceSanitaze = false;

    function __construct(string $name, mixed $value)
    {
        $this->name = $name;
        $this->value = $value;

        $this->sanitazeValue = null;

        $this->required(true);
        $this->preventTag(true);
        $this->scapePrepare(true);
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
            $this->validate[] = [$rule, $message, [$this->name]];
        } else if (is_bool($rule)) {
            $this->required($message ?? $rule);
        } else if (
            match ($rule) {
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
            }
        ) {
            $message = $message ?? $rule;
            $rule = fn ($value) => filter_var($value, $rule);
            $this->validate[] = [$rule, $message, [$this->name]];
        } else if (is_class($rule, InputField::class)) {
            $message = $message ?? 'equal';
            $equalName = $rule->name;
            $rule = fn ($v) => $v == $rule->get(false, false);
            $this->validate[] = [$rule, $message, [$this->name, $equalName]];
        }

        return $this;
    }

    /** Define u modo de limpeza do campo */
    function sanitaze(Closure|int|bool $sanitaze): static
    {
        if (is_bool($sanitaze))
            $this->forceSanitaze = $sanitaze;
        else
            $this->sanitaze[] = $sanitaze;

        return $this;
    }

    /** Captura o valor do campo */
    function get(bool $trow = true, bool $sanitaze = true): mixed
    {
        $error = $this->check($trow);

        if (!$error)
            return $sanitaze || $this->forceSanitaze ? $this->applySanitaze($this->value) : $this->value;

        return null;
    }

    /** Verifica se o objeto passa nas regras de validação */
    function check(bool $trow = true): string|bool
    {
        $error = $this->runValidate();

        if ($error) {
            list($message, $prepare) = [...$error, [$this->name]];

            $message = Input::message($message);

            $message = prepare($message, $prepare);

            if ($trow)
                throw new InputException($message, STS_BAD_REQUEST);
        }

        return $message ?? false;
    }

    /** Executa as regras de validação retornando o array de erro */
    protected function runValidate(): array|bool
    {
        $value = $this->value;

        if ($this->required && is_blank($value))
            return [$this->required];

        if (!$this->required && is_blank($value))
            return false;

        $value = is_array($value) ? $value : [$value];

        foreach ($value as $v) {
            if ($this->preventTag)
                if (is_string($v) && strip_tags($v) != $v)
                    return [$this->preventTag];

            foreach ($this->validate as $validate) {
                list($rule, $message, $prepare) = $validate;
                if (!$rule($v))
                    return [$message, $prepare];
            }
        }

        return false;
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
}