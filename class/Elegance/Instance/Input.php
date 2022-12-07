<?php

namespace Elegance\Instance;

use Elegance\Request;
use Elegance\Trait\InputMessage;

class Input
{
    use InputMessage;

    protected array $data = [];
    protected array $field = [];

    function __construct(?array $data = null)
    {
        $this->data = $data ?? Request::data();
    }

    /** Cria/Retorna um campo do input */
    function &field(string $name, ?string $alias = null): InputField
    {
        $alias = $alias ?? $name;

        if (!isset($this->field[$name]))
            $this->field[$name] = new InputField($alias, $this->data[$name] ?? null);

        return $this->field[$name];
    }

    /** Retorna um ou todos os valores do input */
    function data(?string $name = null)
    {
        if (!func_num_args())
            return array_map(fn ($i) => $i->get(), $this->field);

        return isset($this->field[$name]) ? $this->field[$name]->get() : null;
    }
}