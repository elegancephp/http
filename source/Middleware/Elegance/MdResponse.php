<?php

namespace Middleware\Elegance;

use Closure;
use Elegance\Exception\InputException;
use Elegance\Instance\Response;
use Error;
use Exception;

/** Garante que o retorno da rota seja um objeto Response */
class MdResponse
{
    function __invoke(Closure $next)
    {
        try {
            $reponse = $next();

            if (is_int($reponse) && $reponse >= 200 && $reponse <= 599)
                $reponse = $this->getResponseStatus($reponse, 'friendly');

            $reponse = new Response($reponse);
        } catch (InputException $e) {
            $reponse = $this->getResponseStatus($e->getCode(), $e->getMessage());
            $reponse->type('json');
            $reponse->content($e->getMessage());
        } catch (Exception | Error $e) {
            $reponse = $this->getResponseStatus($e->getCode(), $e->getMessage());
        }
        return $reponse;
    }

    protected function getHttpStatus(int $code, int $default): int
    {
        if ($code >= 200 && $code <= 599) return $code;
        return $default;
    }

    protected function getResponseStatus(int $code, string $message): Response
    {
        $reponse = new Response();

        $reponse->status($this->getHttpStatus($code, STS_INTERNAL_SERVER_ERROR));

        $reponse->header('El-Status-Code', $code);
        $reponse->header('El-Status-Message', $message);

        return $reponse;
    }
}