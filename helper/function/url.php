<?php

use Elegance\Request;

if (!function_exists('url')) {

    /** Retorna uma string de URL */
    function url(...$params): string
    {
        $ssl = null;
        $host = null;
        $port = null;
        $path = [];
        $query = [];

        if (func_num_args()) {
            if (str_contains($params[0] ?? '', '://')) {
                $url = parse_url(array_shift($params));

                $ssl = boolval($url['scheme'] == 'https');

                $host = $url['host'];

                $port = $url['port'] ?? null;

                $path = $url['path'] ?? '';
                $path = trim($path, '/');
                $path = str_replace_all([' /', '/ ', '//'], '/', $path);
                $path = explode('/', $path);

                parse_str(($url['query'] ?? ''), $query);
            } else if (is_numeric($params[0])) {
                $mod = array_shift($params);
                $path = Request::path();
                if ($mod)
                    $path = array_slice($path, 0, intval($mod));
            } else if ($params[0] === TRUE || $params[0] == 'TRUE') {
                array_shift($params);
                $path = Request::path();
                $query = Request::query();
            } else if ($params[0] === FALSE || $params[0] == 'FALSE') {
                array_shift($params);
            } else if (is_string($params[0]) && str_starts_with($params[0], '.')) {
                $params[0] = substr($params[0], 1);
                $path = Request::path();
            }
        }

        foreach ($params as $parm) {
            if (is_string($parm) && str_starts_with($parm, '?')) {
                $tmp = substr($parm, 1);
                parse_str($tmp, $parm);
            }
            if (is_array($parm)) {
                $query = [...$query, ...$parm];
            } else {
                $path[] = $parm;
            }
        }

        $ssl = ($ssl ?? Request::ssl()) ? 'https' : 'http';

        $host = $host ?? Request::host();

        $port = $port ? ":$port" : '';

        $path = implode('/', $path);
        $path = trim($path, '/');
        $path = str_replace_all([' /', '/ ', '//'], '/', $path);

        $query = empty($query) ? '' : '?' . urldecode(http_build_query($query));

        return prepare('[#ssl]://[#host][#port]/[#path][#query]', [
            'ssl' => $ssl,
            'host' => $host,
            'port' => $port,
            'path' => $path,
            'query' => $query
        ]);
    }
}