<?php

use Elegance\View;

if (!function_exists('view')) {

    /** Retorna a string do conteúdo de uma view */
    function view(string|array $viewRef, array $prepare = []): string
    {
        return View::render($viewRef, $prepare);
    }
}


if (!function_exists('viewIn')) {

    /** Retorna a string do conteúdo de uma view dentro da view original */
    function viewIn(string $viewRef, array|string $prepare = []): string
    {
        $currentViewRef = View::getCurrent() ?? '';
        return $currentViewRef ? View::render("$currentViewRef.$viewRef", $prepare) : '';
    }
}