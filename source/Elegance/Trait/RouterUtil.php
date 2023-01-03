<?php


namespace Elegance\Trait;

use Elegance\Request;

trait RouterUtil
{
    /** Verifica se uma rota é compativel com uma lista de caminhos */
    protected static function checkTemplateMatch(string $template): bool
    {
        $uri = Request::path();

        if (substr_count($template, ':')) {
            $template = explode(':', $template);
            $method = array_shift($template);
            $template = array_shift($template);

            if ($method != Request::method())
                return false;
        }

        $template = trim($template, '/');
        $template = explode('/', $template);

        while (count($template)) {
            $esperado = array_shift($template);
            $recebido = array_shift($uri) ?? '';

            if ($recebido != $esperado) {
                if (is_blank($recebido))
                    return $esperado == '...';

                if ($esperado != '#' && $esperado != '...')
                    return false;
            }

            if ($esperado == '...' && count($uri))
                $template[] = '...';
        }

        if (count($uri))
            return false;

        return true;
    }

    /** Explode uma rota em um array de template e params */
    protected static function explodeTemplate(string $template): array
    {
        $params = [];
        $template = explode('/', $template);

        foreach ($template as $n => $param)
            if (str_starts_with($param, '[#')) {
                $template[$n] = '#';
                $params[] = substr($param, 2, -1);
            }

        $template = implode('/', $template);

        $params = (empty($params) && !str_ends_with($template, '...')) ? null : $params;

        return [$template, $params];
    }

    /** Limpa uma referencia de rota */
    protected static function clsRoute($route)
    {
        $method = '';

        if (substr_count($route, ':')) {
            $route = explode(':', $route);
            $method = array_shift($route);
            $route = array_shift($route);
            if (!empty($method))
                $method = strtoupper("$method:");
        }

        $route = "$route/";

        $route = str_replace('[', '[#', $route);
        $route = str_replace_all('[##', '[#', $route);
        $route = str_replace_all(['...', '.../', '......'], '/...', $route);
        $route = str_replace_all('//', '/', "/$route");

        return "$method$route";
    }

    /** Verifica se um template de rota é válido */
    protected static function checkValidRoute(string $template): bool
    {
        $nMore = substr_count($template, '...');
        return boolval($nMore == 0 || ($nMore == 1 && str_ends_with($template, '...')));
    }

    /** Organiza um array de templates para interpretação */
    protected static function organize(array $array): array
    {
        usort($array, function ($a, $b) {
            if (substr_count($a, '/') != substr_count($b, '/'))
                return substr_count($b, '/') <=> substr_count($a, '/');

            $arrayA = explode('/', $a);
            $arrayB = explode('/', $b);
            $na = '';
            $nb = '';
            $max = max(count($arrayA), count($arrayB));

            for ($i = 0; $i < $max; $i++) {
                $na .= ($arrayA[$i] ?? '#') == '#' ? '1' : (($arrayA[$i] ?? '') == '...' ? '2' : '0');
                $nb .= ($arrayB[$i] ?? '#') == '#' ? '1' : (($arrayB[$i] ?? '') == '...' ? '2' : '0');
            }

            $result = intval($na) <=> intval($nb);

            if ($result)
                return $result;

            $result = count($arrayA) <=> count($arrayB);

            if ($result)
                return $result * -1;

            $result = strlen($a) <=> strlen($b);

            if ($result)
                return $result * -1;
        });

        return $array;
    }
}