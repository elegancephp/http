<?php

namespace Elegance;

abstract class Cookie extends Session
{
    /** Lista de cookies controlados */
    protected static $cookies = [];

    /** Lista de cookies removidos na requisição */
    protected static $unlinked = [];

    /** Retorna o valor de um cookie */
    static function get(string $name): mixed
    {
        $secureCookie = substr($name, 0, 1) == '#';

        if ($secureCookie)
            $name = Code::on("COOKIE_CODE_${name}");

        if (!isset(self::$cookies[$name]))
            self::$cookies[$name] = self::getPHPCookie($name);

        $value = self::$cookies[$name] ?? null;

        if ($secureCookie)
            $value = self::valueCodeOff($value);

        return $value;
    }

    /** Define um valor para um cookie */
    static function set(string $name, mixed $value): void
    {
        $secureCookie = substr($name, 0, 1) == '#';

        if ($secureCookie)
            $name = Code::on("COOKIE_CODE_${name}");

        if (!is_null($value)) {
            if ($secureCookie)
                $value = self::valueCodeOn($value);

            if (isset(self::$unlinked[$name]))
                unset(self::$unlinked[$name]);

            self::$cookies[$name] = $value;
            self::setPHPCookie($name, $value);
        } else {
            self::$unlinked[$name] = true;
            self::setPHPCookie($name, '', time() - 3600);
        }
    }

    /** Verifica se um cookie existe ou se tem um valor igual ao fornecido */
    static function check(string $name): bool
    {
        if (func_num_args() > 1) {
            return boolval(self::get($name) == func_get_arg(1));
        } else {
            return !is_null(self::get($name));
        }
    }

    /** Remove um cookie */
    static function remove(string $name): void
    {
        self::set($name, null);
    }

    /** Captura o valor de um cookie real do PHP */
    protected static function getPHPCookie(string $name): mixed
    {
        return filter_input(INPUT_COOKIE, $name);
    }

    /** Altera o valor de um cookie real do PHP */
    protected static function setPHPCookie(string $name, mixed $value, int $time = 0): void
    {
        setcookie($name, $value, $time, '/');
        $_COOKIE[$name] = $_COOKIE[$name] ?? $value;
    }

    /** Codifica uma variavel para ser escrita em cookie */
    protected static function valueCodeOn(mixed $value): string
    {
        $value = serialize($value);
        return Cif::on($value);
    }

    /** Decodifica uma variavel escrita em cookie para o valor normal */
    protected static function valueCodeOff(?string $value): mixed
    {
        if (!is_null($value)) {
            $value = Cif::off($value);
            $value = unserialize($value);
        }
        return $value;
    }
}