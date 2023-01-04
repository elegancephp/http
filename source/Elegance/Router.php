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

    /** Mapeia um diretório transformando o conteúdo em rotas */
    static function map(string $path)
    {
        $map = Dir::seek_for_all($path, true);

        foreach ($map as $file) {
            if (str_ends_with($file, '.php')) {
                $item = substr($file, 0, -4);

                $file = path("$path/$file");

                $item = str_replace(['_index'], '', $item);
                $item = str_replace_all(['//'], '/', $item);

                if ($item == '_') {
                    $item = substr($item, 0, -1) . '...';
                    self::middleware($item, "import:$file");
                } else {
                    self::add($item, "import:$file");
                }
            }
        }
    }

    /** Adiciona uma rota */
    static function add($route, $response)
    {
        $route = self::clsRoute($route);

        list($template, $params) = self::explodeRoute($route);

        self::$routes[$template] = [$params, $response];
    }

    /** Adiciona uma rota em um metodo de requisição */
    static function addIn(string $method, $route, $response)
    {
        if (Request::method() == strtoupper($method))
            self::add(...func_get_args());
    }

    /** Executa a rota correspondente a URL atual */
    static function solve($autoSend = true)
    {
        try {
            self::organize(self::$routes);

            $templateMatch = self::getTemplateMatch();

            list($routeParams, $routeResponse) = self::$routes[$templateMatch] ?? [
                null,
                fn () => throw new Exception('route not found', STS_NOT_FOUND)
            ];

            self::setParamnsData($templateMatch, $routeParams);

            self::setMiddlewares();

            $action = self::getAction($routeResponse);

            $response = Middleware::run($action);

            $response = new Response($response);
        } catch (Error | Exception  $e) {
            $reponse = new Response();
            $reponse->status(STS_INTERNAL_SERVER_ERROR);
            $reponse->header('El-Error-Code', $e->getCode());
            $reponse->header('El-Error-Type', 'throw');
            $reponse->header('El-Error-Message', $e->getMessage());
        }

        if ($autoSend)
            $response->send();

        return $response;
    }

    /** Retorna o template registrado que corresponde a URL atual */
    protected static function getTemplateMatch(): ?string
    {
        $templates = array_keys(self::$routes);

        foreach ($templates as $template)
            if (self::checkTemplateMatch($template))
                return $template;

        return null;
    }

    /** Verifica se um template de rota é válido */
    protected static function checkValidRoute(string $template): bool
    {
        $nMore = substr_count($template, '...');
        return boolval($nMore == 0 || ($nMore == 1 && str_ends_with($template, '...')));
    }
}