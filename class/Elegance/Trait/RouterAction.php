<?php

namespace Elegance\Trait;

use Closure;
use Elegance\Instance\Response;
use Elegance\Request;
use Elegance\Router;
use Error;
use Exception;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

trait RouterAction
{
    protected static array $prefix = [];

    /** Adiciona um prefixo para tratamento de rotas */
    static function actionPrefix($prefix, Closure $action, bool $finalAction = false)
    {
        self::$prefix[$prefix] = [$action, $finalAction];
        uksort(self::$prefix, function ($a, $b) {
            return strlen($a) <=> strlen($b);
        });
    }

    protected static function getAction($response, bool $usePrefix = true): callable
    {
        if (is_string($response)) {
            if ($usePrefix) {
                foreach (self::$prefix as $prefix => $action) {
                    if (str_starts_with($response, $prefix)) {
                        list($action, $finalAction) = $action;
                        $response = substr($response, strlen($prefix));
                        return $finalAction ?
                            fn () => $action($response) :
                            self::getAction($action($response), false);
                    }
                }
            }

            $response = explode(':', $response);
            $className = array_shift($response);
            $method = array_pop($response) ?? null;

            $className = str_replace(['.', '\\', '/'], '.', $className);
            $className = explode('.', $className);
            $className = array_map(fn ($v) => ucfirst($v), $className);
            $className = implode('\\', $className);

            if (class_exists($className))
                return fn () => self::action_className(new $className, $method);

            $response = STS_NOT_IMPLEMENTED;
        }


        if (is_int($response))
            return fn () => self::action_int($response);

        if (is_closure($response))
            return fn () => self::action_closure($response);

        if (is_object($response) && !is_class($response, Response::class))
            return fn () => self::action_object($response);

        return fn () => $response;
    }

    protected static function action_string($response)
    {
        return new Response(prepare($response, self::data()));
    }

    protected static function action_int($response)
    {
        if ($response >= 200 && $response < 600)
            $response = (new Response())->status($response);

        return $response;
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
        $params = Router::data();

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
                throw new Error("parameter [$name] required", STS_BAD_REQUEST);
            }
        }

        return $params;
    }
}