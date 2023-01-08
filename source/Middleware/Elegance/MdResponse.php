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
                throw new Exception('friendly', $reponse);
            if (is_array($reponse) || is_json($reponse))
                $reponse = (new Response($reponse))->type('json');
            $reponse = new Response($reponse);
        } catch (InputException $e) {
            $reponse = new Response();
            $reponse->status($this->getHttpStatus($e->getCode(), STS_BAD_REQUEST));
            $reponse->type('json');
            $reponse->content($e->getMessage());
        } catch (Exception | Error $e) {
            $reponse = new Response();
            $reponse->status($this->getHttpStatus($e->getCode(), STS_BAD_REQUEST));
            $reponse->header('El-Status-Code', $e->getCode());
            $reponse->header('El-Status-Message', $e->getMessage());
        }
        return $reponse;
    }

    protected function getHttpStatus(int $code, int $default): int
    {
        return num_interval($code, 200, 599) == $code ? $code : $default;
    }
}
