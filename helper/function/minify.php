<?php

if (!function_exists('minify_html')) {

    /** Minifica uma string HTML */
    function minify_html($input): string
    {
        if (trim($input) === "") return $input;
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        if (strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        if (strpos($input, '</style>') !== false) {
            $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function ($matches) {
                return '<style' . $matches[1] . '>' . minify_css($matches[2]) . '</style>';
            }, $input);
        }
        if (strpos($input, '</script>') !== false) {
            $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function ($matches) {
                return '<script' . $matches[1] . '>' . minify_js($matches[2]) . '</script>';
            }, $input);
        }

        $minify = $input;
        $minify = str_replace_all('  ', ' ', $minify);
        $minify = str_replace_all(["\n ", "\n\n"], "\n", $minify);
        return $minify;
    }
}

if (!function_exists('minify_css')) {

    /** Minifica uma string CSS */
    function minify_css($input): string
    {
        $minify = $input;
        $minify = str_replace_all('  ', ' ', $minify);
        $minify = str_replace(["\n ", "\n\n"], "\n", $minify);
        $minify = str_replace([";\n", "{\n", "\n}", ",\n"], [";", "{", "}", ","], $minify);

        return $minify;
    }
}

if (!function_exists('minify_js')) {

    /** Minifica uma string Javascript */
    function minify_js($input): string
    {
        $minify = $input;
        $minify = str_replace_all("//\n", "\n", $minify);
        $minify = str_replace_all('  ', ' ', $minify);
        $minify = str_replace(["\n ", "\n\n"], "\n", $minify);
        $minify = str_replace(["\n}", "{\n", ",\n", "}\n", ")\n"], ["}", "{", ",", "};", ");"], $minify);

        return $minify;
    }
}