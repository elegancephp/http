<?php

namespace Elegance;

use Elegance\Instance\Response;
use Exception;

abstract class Assets
{
    /** Envia um arquivo assets como resposta da requisição */
    static function send(string $path, array $allowTypes = []): never
    {
        self::getResponseFile($path, $allowTypes)
            ->send();
    }

    /** Realiza o download de um arquivo assets como resposta da requisição */
    static function download(string $path, array $allowTypes = []): never
    {
        self::getResponseFile($path, $allowTypes)
            ->download(true)
            ->send();
    }

    /** Retorna o arquivo de resposta de um arquivo assets */
    static function get(string $path, array $allowTypes = []): Response
    {
        return self::getResponseFile($path, $allowTypes);
    }

    /** Retorna o ResponseFile do arquivo */
    protected static function getResponseFile(string $path, array $allowTypes): Response
    {
        $path = path($path);

        if (!File::check($path) || !self::checkAllowType($path, $allowTypes))
            throw new Exception("file not found", STS_NOT_FOUND);

        $response = new Response();

        $response->cache(true);
        $response->content(Import::content($path));
        $response->type(File::getEx($path));
        $response->download(File::getOnly($path));
        $response->download(false);

        return $response;
    }

    /** Verifica se o arquivo é de alguma extensão permitida */
    protected static function checkAllowType($path, $allowTypes)
    {
        if (!empty($allowTypes)) {
            $ex = explode('.', $path);
            $ex = array_pop($ex);
            $ex = strtolower($ex);

            return in_array($ex, $allowTypes);
        }
        return true;
    }
}