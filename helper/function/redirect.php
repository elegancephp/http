<?php

use Elegance\Instance\Response;

if (!function_exists('redirect')) {

    /** Redireciona o backend para uma url */
    function redirect()
    {
        redirectResponse(...func_get_args())
            ->send();
    }
}

if (!function_exists('redirectResponse')) {

    /** Retorna um objeto de resposta com um redirecionamento */
    function redirectResponse(): Response
    {
        $url = url(...func_get_args());

        $response = new Response($url);
        $response->header('Location', $url);
        $response->status(STS_REDIRECT);

        return $response;
    }
}