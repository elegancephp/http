<?php

namespace Elegance;

use Elegance\Instance\Cif as InstanceCif;

abstract class Cif
{
    /** InstanceCif com chave padrão */
    protected static ?InstanceCif $instance;

    /** Retorna a cifra de uma variavel */
    static function on(mixed $var, ?string $key = null): string
    {
        return self::instance()->on(...func_get_args());
    }

    /** Retorna a variavel de uma cifra */
    static function off(mixed $var): mixed
    {
        return self::instance()->off(...func_get_args());
    }

    /** Verifica se uma variavel atende os requisitos para ser uma cifra */
    static function check(mixed $var): bool
    {
        return self::instance()->check(...func_get_args());
    }

    /** Verifica se todas as variaveis tem a mesma cifra */
    static function compare(mixed $initial, mixed ...$compare): bool
    {
        return self::instance()->compare(...func_get_args());
    }

    /** Retorna a InstanceCif com chave padrão */
    protected static function &instance(): InstanceCif
    {
        self::$instance = self::$instance ?? new InstanceCif;
        return self::$instance;
    }
}