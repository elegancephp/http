<?php

namespace Elegance;

use Closure;
use Elegance\Interface\Middleware as InterfaceMiddleware;

abstract class Middleware
{
    protected static array $registred = [];

    /** Executa a lista de middlewares */
    static function run(array $middlewares = [], mixed $action = null)
    {
        if (!is_closure($action))
            $action = fn () => $action;

        $middlewares[] = $action;

        return self::execute($middlewares);
    }

    /** Registra uma middleware para ser chamada via string */
    static function register(string $name, Closure|string|array $middleware)
    {
        self::$registred[$name] = $middleware;
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

            if (!is_implement($middleware, InterfaceMiddleware::class))
                $middleware = null;
        }

        if (is_closure($middleware))
            return $middleware;

        if (is_null($middleware))
            return fn ($next) => $next();

        return fn () => $middleware;
    }
}