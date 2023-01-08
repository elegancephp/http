<?php

namespace Elegance\Instance;

use Elegance\Exception\InputException;
use Elegance\Request;
use Error;

class Input
{
    protected array $data = [];

    protected array $field = [];

    protected static array $message = [
        FILTER_VALIDATE_IP => 'O campo [#] precisa ser um endereço IP',
        FILTER_VALIDATE_INT => 'O campo [#] precisa ser um numero inteiro',
        FILTER_VALIDATE_MAC => 'O campo [#] precisa ser um endereço MAC',
        FILTER_VALIDATE_URL => 'O campo [#] precisa ser uma URL',
        FILTER_VALIDATE_EMAIL => 'O campo [#] precisa ser um email',
        FILTER_VALIDATE_FLOAT => 'O campo [#] precisa ser um numero',
        FILTER_VALIDATE_DOMAIN => 'O campo [#] precisa ser um dominio',
        FILTER_VALIDATE_REGEXP => 'O campo [#] precisa ser um a expressão regular',
        FILTER_VALIDATE_BOOLEAN => 'O campo [#] precisa ser um valor booleano',
        'required' => 'O campo [#] é obrigatório',
        'preventTag' => 'O campo [#] contem um valor inválido',
        'default' => 'O campo [#] contem um erro',
        'equal' => 'O campo [#] deve ser igual o campo [#]'
    ];

    function __construct(?array $data = null)
    {
        $this->data = $data ?? Request::data();
    }

    /** Cria/Retorna um campo do input */
    function &field(string $name, ?string $alias = null): InputField
    {
        $alias = $alias ?? $name;
        $value = $this->data[$name] ?? null;

        $this->field[$name] = $this->field[$name] ?? new InputField($alias, $value);

        return $this->field[$name];
    }

    /** Retorna um valor do input */
    function data(string|array $ref): mixed
    {
        if (func_num_args() > 1)
            $ref = func_get_args();

        if (is_array($ref)) {
            $data = [];

            foreach ($ref as $item)
                $data[] = $this->data($item);

            return $data;
        }

        return isset($this->field[$ref]) ? $this->field[$ref]->get() : null;
    }

    /** Retorna varios valores do input em forma de array*/
    function dataValues(bool|string|array $ref): array
    {
        if (func_num_args() > 1)
            $ref = func_get_args();

        if (is_bool($ref)) {
            $data = array_map(fn ($i) => $i->get(), $this->field);
            $data = $ref ? $data : array_filter($data, fn ($v) => !is_null($v));
            return $data;
        }

        $data = [];

        $ref = is_array($ref) ? $ref : [$ref];

        foreach ($ref as $item)
            $data[$item] = $this->data($item);

        return $data;
    }

    /** Executa a validação de todos os campos do Input retornando o resultado */
    function check(bool $trow = true): array|bool
    {
        $errors = [];

        foreach (array_keys($this->field) as $fieldName) {
            $error = $this->field($fieldName)->check(false);
            if ($error)
                $errors[$fieldName] = $error;
        }

        $errors = count($errors) ? $errors : false;

        if ($errors && $trow)
            throw new InputException(json_encode($errors), STS_BAD_REQUEST);

        return $errors;
    }

    /** Adiciona ou altera uma mensagem do input */
    static function message(?string $message = null, ?string $value = null): array|string|null
    {
        return match (func_num_args()) {
            0 => self::$message,
            1 => self::$message[$message]
                ?? $message
                ?? self::$message['default']
                ?? 'input error',
            default => self::$message[$message] = $value
        };
    }
}