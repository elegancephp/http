<?php

namespace Elegance;

abstract class Session
{
    /** Retorna o valor de uma variavel de sessão */
    static function get(string $name): mixed
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            $return = $_SESSION[$name] ?? null;
            if (str_starts_with($name, '#'))
                static::remove($name);
        }

        return $return ?? null;
    }

    /** Define um valor para uma variavel de sessão */
    static function set(string $name, mixed $value): void
    {
        if (session_status() == PHP_SESSION_ACTIVE)
            $_SESSION[$name] = $value;
    }

    /** Verifica se uma variavel de sessão existe ou se tem um valor igual ao fornecido */
    static function check(string $name): bool
    {
        if (func_num_args() > 1) {
            return boolval(static::get($name) == func_get_arg(1));
        } else {
            return !is_null(static::get($name));
        }
    }

    /** Remove uma variavel de sessão */
    static function remove(string $name): void
    {
        static::set($name, null);
    }

    /** Inicia ou reinicia uma sessão */
    static function init(bool $reload = false): void
    {
        if (!IS_TERMINAL) {
            if (session_status() != PHP_SESSION_ACTIVE) {
                $SESSION_TIME = env('SESSION_TIME') * 60;
                $COOKIE_TIME = env('COOKIE_TIME') * 60 * 60;

                session_cache_expire($SESSION_TIME);
                session_set_cookie_params($COOKIE_TIME, '/', '', true, true);

                session_start();
            }

            if (
                $reload ||
                !Cif::check(session_id()) ||
                !static::check('___SESSION___') ||
                !Cif::compare(static::get('___SESSION___'), session_id())
            ) {
                static::reload();
            }
        }
    }

    /** Recria a sessão atual */
    static function reload()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            $SESSION_ID = Code::on(session_id() . '__' . uniqid());
            session_destroy();
            session_id(Cif::on($SESSION_ID));
            session_start();
            static::set('___SESSION___', $SESSION_ID);
        }
    }
}

Session::init();