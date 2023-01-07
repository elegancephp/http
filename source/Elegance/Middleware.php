<?php

namespace Elegance;

use Closure;

abstract class Middleware
{
    protected static array $registred = [];
    protected static array $queue = [];

    /** Adiciona uma middleware a fila de execução */
    static function add(string|Closure $name, null|string|Closure $middleware = null)
    {
        if (is_closure($name)) {
            $middleware = $name;
            $name = md5(uniqid());
        }

        if ($middleware)
            self::register($name, $middleware);

        self::$queue[] = $name;
    }

    /** Adiciona uma middleware no inicio da fila de execução */
    static function addPre(string|Closure $name, null|string|Closure $middleware = null)
    {
        if (is_closure($name)) {
            $middleware = $name;
            $name = md5(uniqid());
        }

        if ($middleware)
            self::register($name, $middleware);

        self::$queue = [$name, ...self::$queue];
    }

    /** Remove uma middleware a fila de execução */
    static function remove(string $name)
    {
        foreach (self::$queue as $pos => $middleware)
            if ($middleware == $name)
                unset(self::$queue[$pos]);

        self::$queue = array_values(self::$queue);
    }

    /** Registra uma middleware para ser chamada via string */
    static function register(string $name, string|Closure $middleware)
    {
        self::$registred[$name] = $middleware;
    }

    /** Executa a fila de middlewares */
    static function run(mixed $action = null)
    {
        if (!is_closure($action)) $action = fn () => $action;

        $middlewares = [...self::$queue, $action];

        return self::execute($middlewares);
    }

    /** Retorna as middlewares na fila de execução */
    static function queue()
    {
        return self::$queue;
    }

    /** Execute uma fila de middlewares */
    protected static function execute(mixed &$queue): mixed
    {
        if (count($queue)) {
            $middleware = array_shift($queue);
            $middleware = self::getCallable($middleware);
            $next = fn () => self::execute($queue);
            return $middleware($next);
        }
        return null;
    }

    /** Retorna o objeto callable de uma middleware */
    protected static function getCallable(Closure|string|array|null $middleware)
    {
        if (is_array($middleware))
            return function ($next) use ($middleware) {
                $queue = [...$middleware, $next];
                return self::execute($queue);
            };

        if (is_string($middleware)) {
            if (isset(self::$registred[$middleware]))
                return self::getCallable(self::$registred[$middleware]);

            $className = explode('.', $middleware);
            $className = array_map(fn ($value) => ucfirst($value), $className);
            $className[] = 'Md' . array_pop($className);
            $className = implode('\\', $className);
            $className = trim("Middleware\\$className", '\\');

            if (class_exists($className))
                $middleware = new $className;

            if (!is_closure($middleware))
                $middleware = null;
        }

        if (is_closure($middleware))
            return $middleware;

        if (is_null($middleware))
            return fn ($next) => $next();

        return fn () => $middleware;
    }
}