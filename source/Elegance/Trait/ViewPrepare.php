<?php

namespace Elegance\Trait;

trait ViewPrepare
{
    protected static array $prepare = [];

    /** Adiciona ou retorna um ou todos os valors do prepare global */
    static function prepare(string $name = '', mixed $value = null)
    {
        return match (func_num_args()) {
            0 => (self::$prepare),
            1 => (self::$prepare[$name] ?? null),
            default => (self::$prepare[$name] = $value)
        };
    }
}