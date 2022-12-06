<?php

namespace Elegance\Instance;

class Code
{
    protected string $strKey;
    protected string $preKey;
    protected string $posKey;
    protected array $key;

    function __construct(?string $key = null)
    {
        $baseChar = 'mxsiqjngplvouytwrh';
        $stringKey = $key ?? env('CODE_KEY');
        $stringKey = strtolower($stringKey);

        $stringKey = preg_replace("/[^$baseChar]/", '', $stringKey);

        $stringKey = str_split($stringKey);
        $key = '';

        while (strlen($key) < 18 && count($stringKey)) {
            $char = array_shift($stringKey);
            if ($key == '' || strpos($key, $char) === false) {
                $key .= $char;
                $baseChar = str_replace($char, '', $baseChar);
            }
        }

        $key = substr("$key$baseChar", 0, 18);
        $this->preKey = substr($key, 0, 1);
        $this->posKey = substr($key, 1, 1);
        $this->key = str_split(substr($key, 2));
    }

    /** Retorna o codigo de uma string */
    function on(string $string): string
    {
        if (!$this->check($string)) {
            $string = is_md5($string) ? $string : md5($string);
            $in = str_split('1234567890abcdef');
            $out = $this->key;
            $string = str_replace($in, $out, $string);
            $string = $this->preKey . $string . $this->posKey;
        }

        return $string;
    }

    /** Retonra o MD5 usado para gerar uma string codificada */
    function off(string $string): string
    {
        if ($this->check($string)) {
            $in = str_split('1234567890abcdef');
            $out = $this->key;
            $string = str_replace($out, $in, substr($string, 1, -1));
        } else if (!is_md5($string)) {
            $string = md5($string);
        }

        return $string;
    }

    /** Verifica se uma string é uma string codificada */
    function check(string $string): bool
    {
        return boolval(
            strlen($string) == 34 &&
                substr($string, 0, 1) == $this->preKey &&
                substr($string, -1) == $this->posKey &&
                empty(str_replace($this->key, '', substr($string, 1, -1)))
        );
    }

    /** Verifica se todas as strings tem a mesma string codificada */
    function compare(string $initial, string ...$compare): bool
    {
        $result = true;

        while ($result && count($compare))
            $result = boolval(
                $this->off($initial) == $this->off(array_shift($compare))
            );

        return $result;
    }
}