<?php

namespace Elegance\Instance;

use Elegance\File;
use Elegance\Import;
use Error;

class Cif
{
    protected array $cif;
    protected array $ensure;
    protected string $currentKey;

    final const BASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    function __construct(?string $certificate = null)
    {
        $certificate = $certificate ?? env('CIF_FILE');

        if ($certificate) {
            $certificate = path(env('PATH_CIF'), $certificate);
            File::ensure_extension($certificate, 'crt');
            $certificate = path($certificate);
        } else {
            $certificate = (dirname(__DIR__, 3) . '/library/certificate/base.crt');
        }

        if (!File::check($certificate))
            throw new Error("Impossivel localizar o arquivo de certificado [$certificate].");

        $content = Import::content($certificate);
        $content = str_replace([" ", "\t", "\n", "\r", "\0", "\x0B"], '', $content);
        $cif = str_split($content, 62);

        $this->ensure = str_split(array_pop($cif));
        $this->cif = $cif;
    }

    /** Retorna a cifra de uma variavel */
    function on(mixed $var, ?string $key = null): string
    {
        if ($this->check($var))
            return $var;

        $key = $key ?? env('CIF_KEY');

        if ($key !== false) {

            $key = ($key !== null && $key !== true)
                ? $this->get_key_char(substr("$key", 0, 1))
                : $this->get_key();

            $stringCif = serialize($var);

            $stringCif = gzdeflate($stringCif);

            $stringCif = base64_encode($stringCif);

            $stringCif = str_replace('=', '', $stringCif);

            $stringCif = strrev($stringCif);

            $stringCif = $this->replace($stringCif, self::BASE, $this->cif[$key]);

            $stringCif = $this->get_char_key($key) . $stringCif . $this->get_char_key($key, true);

            $stringCif = str_replace('/', '-', $stringCif);

            $stringCif = "-$stringCif-";
        }

        return $stringCif ?? $var;
    }

    /** Retorna a variavel de uma cifra */
    function off(mixed $var): mixed
    {
        if (!$this->check($var))
            return $var;

        $key = $this->get_key_char(substr($var, 1, 1));

        $var = substr($var, 2, -2);

        $var = str_replace('-', '/', $var);

        $var = $this->replace($var, $this->cif[$key], self::BASE);

        $var = base64_decode(strrev($var));

        $var = gzinflate($var);

        $var = unserialize($var);

        return $var;
    }

    /** Verifica se uma variavel atende os requisitos para ser uma cifra */
    function check(mixed $var): bool
    {
        if (is_base64($var))
            if (substr($var, 0, 1) == '-')
                if (substr($var, -1) == '-') {
                    $key = $this->get_key_char(substr($var, 1, 1));
                    return substr($var, -2, 1) == $this->get_char_key($key, true);
                }

        return false;
    }

    /** Verifica se todas as variaveis tem a mesma cifra */
    function compare(mixed $initial, mixed ...$compare): bool
    {
        $result = true;

        while ($result && count($compare))
            $result = boolval(
                $this->off($initial) == $this->off(array_shift($compare))
            );

        return $result;
    }

    /** Realiza o replace interno de uma string */
    protected function replace(string $string, string $in, string $out): string
    {
        for ($i = 0; $i < strlen($string); $i++)
            if (strpos($in, $string[$i]) !== false)
                $string[$i] = $out[strpos($in, $string[$i])];

        return $string;
    }

    /** Retorna uma chave */
    protected function get_key(bool $random = true): string
    {
        if ($random) {
            return random_int(0, 61);
        } else {
            $this->currentKey = $this->currentKey ?? random_int(0, 61);
            return $this->currentKey;
        }
    }

    /** Retorna a chave de um caracter */
    protected function get_key_char(string $char): string
    {
        return array_flip($this->ensure)[$char] ?? 0;
    }

    /** Retorna o caracter de uma chave */
    protected function get_char_key(string $key, bool $inverse = false): string
    {
        $key = $inverse ? 61 - $key : $key;
        $key = $this->ensure[$key] ?? 0;
        return $key;
    }
}