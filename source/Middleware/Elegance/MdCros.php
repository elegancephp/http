<?php

namespace Middleware\Elegance;

use Closure;
use Elegance\Instance\Response;
use Elegance\Request;

class MdCros
{
    function __invoke(Closure $next)
    {
        $response = new Response($next());

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $response->header("Access-Control-Allow-Origin", $_SERVER['HTTP_ORIGIN']);
            $response->header("Access-Control-Allow-Credentials", "true");
            $response->header("Access-Control-Max-Age", "86400");
        }

        if (Request::method() == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                $response->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                $response->header("Access-Control-Allow-Headers", $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
        }

        return $response;
    }
}
