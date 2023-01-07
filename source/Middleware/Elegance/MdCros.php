<?php

namespace Middleware\Elegance;

use Closure;
use Elegance\Instance\Response;

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

        return $response;
    }
}