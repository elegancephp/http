<?php


namespace Elegance\Trait;

use Elegance\Cif;
use Elegance\Code;
use Elegance\Request;

trait RouterUtil
{
    /** Limpa uma referencia de rota */
    protected static function clsRoute($route)
    {
        $route = "/$route/";

        $route = str_replace('+', '/', $route);
        $route = str_replace('[...]', '...', $route);
        $route = str_replace('[', '[#', $route);

        $route = str_replace(
            ['[##', '[#@', '[#%', '[#$', '[#&', '[#='],
            ['[#', '[@', '[%', '[$', '[&', '[='],
            $route
        );

        $route = str_replace_all(['...', '.../', '......'], '/...', $route);
        $route = str_replace_all('//', '/', $route);

        return $route;
    }

    /** Explode uma rota em um array de template e params */
    protected static function explodeRoute(string $template): array
    {
        $params = [];

        $template = explode('/', $template);

        foreach ($template as $n => $param)
            if (
                str_starts_with($param, '[#')
                ||
                str_starts_with($param, '[@')
                ||
                str_starts_with($param, '[%')
                ||
                str_starts_with($param, '[$')
                ||
                str_starts_with($param, '[&')
            ) {
                $template[$n] = substr($param, 1, 1);
                $params[] = substr($param, 2, -1);
            } else if (str_starts_with($param, '[=')) {
                $template[$n] = substr($param, 2, -1);
            }

        $template = implode('/', $template);

        $params = (empty($params) && !str_ends_with($template, '...')) ? null : $params;

        return [$template, $params];
    }

    /** Verifica se uma rota é compativel com uma lista de caminhos */
    protected static function checkTemplateMatch(string $template): bool
    {
        $uri = Request::path();

        $template = trim($template, '/');
        $template = explode('/', $template);

        while (count($template)) {
            $esperado = array_shift($template);
            $recebido = array_shift($uri) ?? '';

            if ($recebido != $esperado) {
                if (is_blank($recebido)) return $esperado == '...';

                if ($esperado == '@') return is_numeric($recebido) && intval($recebido) == $recebido;

                if ($esperado == '%') return is_numeric($recebido);

                if ($esperado == '$') return Code::check($recebido);

                if ($esperado == '&') return Cif::check($recebido);

                if ($esperado != '#' && $esperado != '...') return false;
            }

            if ($esperado == '...' && count($uri))
                $template[] = '...';
        }

        if (count($uri))
            return false;

        return true;
    }

    /** Organiza um array de templates para interpretação */
    protected static function organize(array &$array): void
    {
        uksort($array, function ($a, $b) {
            $nBarrA = substr_count($a, '/');
            $nBarrB = substr_count($b, '/');

            if ($nBarrA != $nBarrB) return $nBarrB <=> $nBarrA;

            $arrayA = explode('/', $a);
            $arrayB = explode('/', $b);
            $na = '';
            $nb = '';
            $max = max(count($arrayA), count($arrayB));

            for ($i = 0; $i < $max; $i++) {
                $na .= match (true) {
                    (($arrayA[$i] ?? '@') == '@') => '1',
                    (($arrayA[$i] ?? '%') == '%') => '2',
                    (($arrayA[$i] ?? '$') == '$') => '6',
                    (($arrayA[$i] ?? '&') == '&') => '7',
                    (($arrayA[$i] ?? '#') == '#') => '8',
                    (($arrayA[$i] ?? '') == '...') => '9',
                    default => '0'
                };
                $nb .= match (true) {
                    (($arrayB[$i] ?? '@') == '@') => '1',
                    (($arrayB[$i] ?? '%') == '%') => '2',
                    (($arrayB[$i] ?? '$') == '$') => '6',
                    (($arrayB[$i] ?? '&') == '&') => '7',
                    (($arrayB[$i] ?? '#') == '#') => '8',
                    (($arrayB[$i] ?? '') == '...') => '9',
                    default => '0'
                };
            }

            $result = intval($na) <=> intval($nb);

            if ($result) return $result;

            $result = count($arrayA) <=> count($arrayB);

            if ($result) return $result * -1;

            $result = strlen($a) <=> strlen($b);

            if ($result) return $result * -1;
        });
    }
}