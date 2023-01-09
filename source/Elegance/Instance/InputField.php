<?php

namespace Elegance\Instance;

use Closure;
use Elegance\Exception\InputException;
use Elegance\Prepare;

class InputField
{
    protected string $name;
    protected mixed $value;

    protected bool $required = true;
    protected bool $useNullValue = false;

    protected ?bool $check = null;
    protected array $validate = [];
    protected array $error = [];

    protected bool $sanitazed = false;
    protected array $sanitaze = [];
    protected mixed $valueSanizate = null;

    protected string $preventTag;
    protected ?array $scapePrepare;

    protected static array $message = [
        FILTER_VALIDATE_IP => 'O campo [#name] precisa ser um endereço IP',
        FILTER_VALIDATE_INT => 'O campo [#name] precisa ser um numero inteiro',
        FILTER_VALIDATE_MAC => 'O campo [#name] precisa ser um endereço MAC',
        FILTER_VALIDATE_URL => 'O campo [#name] precisa ser uma URL',
        FILTER_VALIDATE_EMAIL => 'O campo [#name] precisa ser um email',
        FILTER_VALIDATE_FLOAT => 'O campo [#name] precisa ser um numero',
        FILTER_VALIDATE_DOMAIN => 'O campo [#name] precisa ser um dominio',
        FILTER_VALIDATE_REGEXP => 'O campo [#name] precisa ser um a expressão regular',
        FILTER_VALIDATE_BOOLEAN => 'O campo [#name] precisa ser um valor booleano',
        'required' => 'O campo [#name] é obrigatório',
        'preventTag' => 'O campo [#name] contem um valor inválido',
        'default' => 'O campo [#name] contem um erro',
        'equal' => 'O campo [#name] deve ser igual o campo [#equal]'
    ];

    function __construct(string $name, mixed $value = null)
    {
        $this->name = $name;
        $this->value = is_blank($value) ? null : $value;

        $this->preventTag(true);
        $this->scapePrepare(true);
    }

    /** Recupera o valor do campo */
    function get(): mixed
    {
        if ($this->check() && $this->recived())
            return $this->getSanitaze();

        return null;
    }

    /** Vefifica se todas as regras de validação se aplicam ao valor do campo */
    function check($throw = true): bool
    {
        $this->check = $this->check ?? $this->runValidate();

        if (!$this->check && $throw)
            throw new InputException($this->error());

        return $this->check;
    }

    /** Adiciona regras de validação do campo */
    function validate(mixed $rule, ?string $message = null): static
    {
        $this->check = null;

        if (is_bool($rule)) {
            $this->required = $rule ? ($message ?? true) : false;
            $this->useNullValue = false;
        } elseif (is_null($rule)) {
            $this->required = false;
            $this->useNullValue = true;
        } elseif (match ($rule) {
            FILTER_VALIDATE_IP,
            FILTER_VALIDATE_MAC,
            FILTER_VALIDATE_URL,
            FILTER_VALIDATE_EMAIL,
            FILTER_VALIDATE_DOMAIN,
            FILTER_VALIDATE_REGEXP,
            FILTER_VALIDATE_BOOLEAN => true,
            default => false
        }) {
            $this->validate[] = [fn ($value) => filter_var($value, $rule), [$message ?? $rule]];
        } elseif (is_class($rule, static::class)) {
            $this->validate[] = [fn ($v) => $v == $rule->value, [$message ?? 'equal', ['equal' => $rule->name]]];
        } elseif ($rule == FILTER_VALIDATE_INT) {
            $this->validate[] = [fn ($value) => intval($value) == $value, [$message ?? $rule]];
        } elseif ($rule == FILTER_VALIDATE_FLOAT) {
            $this->validate[] = [fn ($value) => floatval($value) == $value, [$message ?? $rule]];
        }

        return $this;
    }

    /** Modo de sanitização do campo */
    function sanitaze(Closure|int $sanitaze): static
    {
        $this->sanitazed = false;
        $this->sanitaze[] = $sanitaze;
        return $this;
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

    /** Verifica se o campo foi recebido */
    function recived(): bool
    {
        return !is_blank($this->value) || $this->useNullValue;
    }

    /** Retorna a mensagem de erro */
    function error(): string
    {
        if (!is_blank($this->error)) {
            list($message, $prepare) = [...$this->error, []];

            $message = self::$message[$message] ?? $message;

            if (!isset($prepare['name']))
                $prepare['name'] = $this->name;

            return prepare($message, $prepare);
        }
        return null;
    }

    /** Rodas as regras de validação do campo */
    protected function runValidate()
    {
        $this->error = [];

        if (!$this->recived()) {
            if ($this->required) {
                $messageError = is_string($this->required) ? $this->required : 'required';
                $this->error = [$messageError];
                return false;
            } else {
                return true;
            }
        }


        $value = is_array($this->value) ? $this->value : [$this->value];

        foreach ($value as $p => $v) {
            if ($this->preventTag && is_string($v) && strip_tags($v) != $v) {
                $this->error = [$this->preventTag];
                return false;
            }

            foreach ($this->validate as $validate) {
                list($rule, $error) = $validate;
                if (!$rule($v)) {
                    if (is_array($this->value)) {
                        $prepare = count($error) > 1 ? array_pop($error) : [];
                        $prepare['name'] = "$this->name($p)";
                        $error[] = $prepare;
                    }
                    $this->error = $error;
                    return false;
                }
            }
        }

        return true;
    }

    /** Retorna o valor do campo sanitizado */
    protected function getSanitaze()
    {
        if (!$this->sanitazed) {
            $this->valueSanizate = $this->applySanitaze($this->value);
            $this->sanitazed = true;
        }
        return $this->valueSanizate;
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