<?php

namespace Elegance\Instance;

use Elegance\Exception\InputException;
use Elegance\Request;

class Input
{
    protected array $dataRecived;

    protected array $field = [];

    protected array $error = [];

    function __construct(?array $data = null)
    {
        $this->dataRecived = array_map(fn ($v) => is_blank($v) ? null : $v, $data ?? Request::data());
    }

    /** Retorna um objeto de um campo do input */
    function &field(string $name, ?string $alias = null): Inputfield
    {
        $alias = $alias ?? $name;
        $value = $this->dataRecived[$name] ?? null;

        $this->field[$name] = $this->field[$name] ?? new Inputfield($alias, $value);

        return $this->field[$name];
    }

    /** Adiciona multiplos campos ao input */
    function fields(string|array $field): static
    {
        foreach (func_get_args() as $field) {
            if (is_array($field)) {
                $this->fields(...$field);
            } else {
                $this->field($field);
            }
        }
        return $this;
    }

    /** Vefifica se todos os campos do input passam nas regras de validação */
    function check($throw = true)
    {
        $this->error = [];
        foreach (array_keys($this->field) as $fieldName)
            if (!$this->field($fieldName)->check(false))
                $this->error[$fieldName] = $this->field($fieldName)->error();

        $check = is_blank($this->error);

        if (!$check && $throw)
            throw new InputException(json_encode($this->error));

        return $check;
    }

    /** Retorna o arrray com as mensagens de erro do input */
    function error(): ?array
    {
        return is_blank($this->error) ? null : $this->error;
    }

    /** Retorna o valor de um ou mais campos do input */
    function get(string|array $fields): mixed
    {
        $data = $this->data(...func_get_args());

        if (count($data) == 1) $data = array_shift($data);

        return $data;
    }

    /** Retorna os valores dos campos do input em forma de array */
    function data(null|string|array $fields = null): array
    {
        if (!is_array($fields) && func_num_args() > 1)
            $fields = func_get_args();

        $fields = $fields ?? array_keys($this->field);
        $fields = is_array($fields) ? $fields : [$fields];
        $fields = array_values($fields);

        foreach ($fields as $fieldName)
            $this->field($fieldName);

        $this->check();

        $data = [];
        foreach ($fields as $fieldName)
            $data[$fieldName] = $this->field($fieldName)->get();

        return $data;
    }

    /** Retorna os valores dos campos recebidos do input em forma de array */
    function dataRecived(null|string|array $fields = null): array
    {
        $data = $this->data(...func_get_args());

        foreach (array_keys($data) as $fieldName)
            if (!$this->field($fieldName)->recived())
                unset($data[$fieldName]);

        return $data;
    }
}