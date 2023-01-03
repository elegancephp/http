<?php

namespace Elegance\Trait;

trait RouterMiddleware
{
    protected static array $middlewares = [];

    /** Adiciona middlewares para rotas */
    static function middleware($routes, $middlewares)
    {
        $routes = is_array($routes) ? $routes : [$routes];
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

        $and = [];
        $or = [];

        foreach ($routes as $route) {
            if (!str_ends_with($route, '/') && !str_ends_with($route, '...'))
                $route = "$route/...";

            list($template) = self::explodeTemplate($route);

            if (self::checkValidRoute($template)) {
                if (str_starts_with($template, '!')) {
                    $template = substr($template, 1);
                    $and[] = self::clsRoute($template);
                } else {
                    $or[] = self::clsRoute($template);
                }
            }
        }

        self::$middlewares[] = [$and,  $or, $middlewares];
    }

    /** Retorna as middlewares que devem ser executadas na URL atual */
    protected static function getMiddlewares()
    {
        $middlewareQueue = [];
        $middlewareMod = [];

        foreach (self::$middlewares as $item) {
            list($and, $or, $mod) = $item;

            $check_and = true;

            while ($check_and && count($and)) {
                $template = array_shift($and);
                $check_and = !self::checkTemplateMatch($template);
            }

            $check_or = count($or) ? false : true;

            while (!$check_or && count($or)) {
                $template = array_shift($or);
                $check = self::checkTemplateMatch($template);
                $check_or = boolval($check_or || $check);
            }

            if ($check_and && $check_or)
                $middlewareMod = [...$middlewareMod, ...$mod];
        }

        foreach ($middlewareMod as $mod) {
            if (is_string($mod) && substr($mod, 0, 1) == '-') {
                $mod = substr($mod, 1);
                foreach ($middlewareQueue as $position => $item)
                    if ($item == $mod)
                        unset($middlewareQueue[$position]);
            } else {
                $middlewareQueue[] = $mod;
            }
        }

        return $middlewareQueue;
    }
}