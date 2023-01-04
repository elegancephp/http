<?php

namespace Elegance;

use Elegance\Trait\RouterAction;
use Elegance\Trait\RouterData;
use Elegance\Trait\RouterMiddleware;
use Elegance\Trait\RouterUtil;

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

        $middlewares = [];
        $routes = [];
        $error = [];

        foreach ($map as $file) {
            if (str_ends_with($file, '.php')) {
                $item = substr($file, 0, -4);

                $file = path("$path/$file");

                $item = str_replace(['_index'], '', $item);
                $item = str_replace_all(['//'], '/', $item);

                if (str_ends_with($item, '_error')) {
                    $item = substr($item, 0, -6) . '...';
                    // IMPLEMENTAR ERROR
                } else if (str_ends_with($item, '_')) {
                    $item = substr($item, 0, -1) . '...';
                    self::middleware($item, "=$file");
                } else {
                    self::add($item, "=$file");
                }
            }
        }

        jsonFile('map', [
            'middlewares' => $middlewares,
            'routes' => $routes,
            'error' => $error,
        ]);
    }

    /** Adiciona uma rota */
    static function add($route, $response)
    {
        $route = self::clsRoute($route);

        list($template, $params) = self::explodeRoute($route);

        self::$routes[$template] = [$params, $response];
    }

    /** Executa a rota correspondente a URL atual */
    static function solve($autoSend = true)
    {
        self::organize(self::$routes);

        jsonFile('route_solve', [
            'routes' => array_keys(self::$routes),
            'middlewares' => self::$middlewares
        ]);

        $templateMatch = self::getTemplateMatch();

        list($params, $response) = self::$routes[$templateMatch] ?? [null, STS_NOT_FOUND];

        self::setParamnsData($templateMatch, $params);

        self::setMiddlewares();

        $action = self::getAction($response);

        $result = Middleware::run($action);

        jsonFile('result', [
            'template' => $templateMatch,
            'data' => self::data(),
            'middleware' => Middleware::queue()
        ]);

        dd($result);
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