<?php

namespace Elegance\Trait;

use Elegance\Import;
use Elegance\Middleware;

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

            list($template) = self::explodeRoute($route);

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

    /** Adiciona a fila as middlewares que devem ser executadas na URL atual */
    protected static function setMiddlewares()
    {
        foreach (self::$middlewares as $item) {
            list($and, $or, $middlewares) = $item;

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
                foreach ($middlewares as $mod) {
                    if (is_closure($mod)) {
                        Middleware::add(md5(uniqid()), $mod);
                    } else if (is_string($mod)) {
                        if (str_starts_with($mod, '-')) {
                            $mod = substr($mod, 1);
                            Middleware::remove($mod);
                        } else if (str_starts_with($mod, 'import:')) {
                            Import::return(substr($mod, 7));
                        } else {
                            Middleware::add($mod);
                        }
                    }
                }
        }
    }
}