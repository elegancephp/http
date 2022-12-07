<?php

namespace Elegance;

use Elegance\Instance\Response;
use Elegance\Trait\RouterAction;
use Elegance\Trait\RouterData;
use Elegance\Trait\RouterMiddleware;
use Elegance\Trait\RouterUtil;
use Error;
use Exception;

abstract class Router
{
    use RouterAction;
    use RouterData;
    use RouterMiddleware;
    use RouterUtil;

    protected static array $routes = [];
    protected static array $prefixAction = [];
    protected static array $status = [];

    /** Adiciona uma rota */
    static function add($route, $response)
    {
        $route = self::clsRoute($route);

        list($template, $params) = self::explodeTemplate($route);

        self::$routes[$template] = [$params, $response];
    }

    /** Executa a rota correspondente a URL atual */
    static function solve($autosend = true)
    {
        try {
            $templateMatch = self::getTemplateMatch();

            list($params, $response) = self::$routes[$templateMatch] ?? [null, STS_NOT_FOUND];

            self::setParamnsData($templateMatch, $params);

            $middlewares = self::getMiddlewares();

            $action = self::getAction($response);

            $result = Middleware::run($middlewares, $action);


            $result = new Response($result);
        } catch (Exception | Error $e) {
            $result = new Response;
            $result->status($e->getCode() ? $e->getCode() : STS_INTERNAL_SERVER_ERROR);
            $result->header('ELEGANCE-ERROR-CODE', $e->getCode());
            $result->header('ELEGANCE-ERROR-MESSAGE', $e->getMessage());
        }

        if ($autosend)
            $result->send();

        return $result;
    }

    /** Rotas que devem ser executadas em caso de status HTTP */
    static function status($status, $response)
    {
        if (is_array($status)) {
            foreach ($status as $STS)
                self::status($STS, $response);
            return;
        }
        self::$status[$status] = $response;
    }

    /** Retorna a resposta padrãp de um status HTTP */
    protected static function getResponseStatus(int $status)
    {
        if (isset(self::$status[$status]))
            return self::$status[$status];

        if ($status >= 400 && $status <= 599)
            return self::$status[0] ?? null;
    }

    /** Retorna o template registrado que corresponde a URL atual */
    protected static function getTemplateMatch(): ?string
    {
        $templates = array_keys(self::$routes);
        $templates = self::organize($templates);

        foreach ($templates as $template)
            if (self::checkTemplateMatch($template))
                return $template;

        return null;
    }
}