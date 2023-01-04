<?php

namespace Elegance\Trait;

use Closure;
use Elegance\Request;
use Error;
use Exception;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

trait RouterAction
{
    protected static array $prefix = [];

    /** Adiciona um prefixo para tratamento de rotas */
    static function actionPrefix($prefix, Closure $action, bool $finalAction)
    {
        self::$prefix[$prefix] = [$action, $finalAction];

        uksort(self::$prefix, function ($a, $b) {
            return strlen($a) <=> strlen($b);
        });
    }

    /** Retorna a action que deve ser executada como resposta */
    protected static function getAction($response, bool $usePrefix = true): callable
    {
        if (is_string($response)) {
            if ($usePrefix)
                foreach (self::$prefix as $prefix => $action)
                    if (str_starts_with($response, $prefix)) {
                        list($action, $finalAction) = $action;
                        $response = substr($response, strlen($prefix));
                        return $finalAction ?
                            fn () => $action($response) :
                            self::getAction($action($response), false);
                    }
        }

        if (is_closure($response))
            return fn () => self::action_closure($response);

        if (is_object($response) && !is_class($response, Response::class))
            return fn () => self::action_object($response);

        return fn () => $response;
    }

    protected static function action_closure($response)
    {
        if ($response instanceof Closure) {
            $params = self::getUseParams(new ReflectionFunction($response));
        } else {
            $params = self::getUseParams(new ReflectionMethod($response, '__invoke'));
        }
        return $response(...$params);
    }

    protected static function action_className($response, $method = null)
    {
        $params = [];

        if (method_exists($response, '__construct'))
            $params = self::getUseParams(new ReflectionMethod($response, '__construct'));

        return self::action_object(new $response(...$params), $method);
    }

    protected static function action_object($response, $method = null)
    {
        $method = $method ?? strtolower(Request::method());

        if (!method_exists($response, $method))
            throw new Exception("method [$method] not found", STS_NOT_IMPLEMENTED);

        $paramsMethod = self::getUseParams(new ReflectionMethod($response, $method));

        return $response->{$method}(...$paramsMethod);
    }

    /** Retorna os parametros que devem ser usados em um metodo refletido */
    protected static function getUseParams(ReflectionFunctionAbstract $reflection): array
    {
        $params = [];
        $data = self::data();

        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            if (isset($data[$name])) {
                $params[] = $data[$name];
            } else if ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new Error("parameter [$name] is required", STS_BAD_REQUEST);
            }
        }

        return $params;
    }
}