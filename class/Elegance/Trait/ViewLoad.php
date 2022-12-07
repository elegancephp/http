<?php

namespace Elegance\Trait;

use Elegance\Import;

trait ViewLoad
{
    /** Retorna o conteúdo HTML de uma view */
    protected static function load_content(string $viewRef, array $prepare): string
    {
        $file = self::map($viewRef, 'content');

        $content = $file ? Import::output($file) : '';

        $content = prepare($content, $prepare);

        if (env('VIEW_MINIFY'))
            $content = minify_html($content);

        return $content;
    }

    /** Retorna o script JS de uma view */
    protected static function load_script(string $viewRef, array $prepare): string
    {
        $file = self::map($viewRef, 'script');

        $content = $file ? Import::output($file) : '';

        $content = prepare($content, $prepare);

        if (env('VIEW_MINIFY'))
            $content = minify_js($content);

        return $content;
    }

    /** Retorna o style CSS de uma view */
    protected static function load_style(string $viewRef, array $prepare): string
    {
        $file = self::map($viewRef, 'style');

        $content = $file ? Import::output($file) : '';

        $content = prepare($content, $prepare);

        if (env('VIEW_MINIFY'))
            $content = minify_css($content);

        return $content;
    }

    /** Retorna o prepare data de uma view */
    protected static function load_data(string $viewRef): array
    {
        $data = self::map($viewRef, 'data');

        if (is_string($data)) {
            $data = str_ends_with($data, '.json') ? jsonFile($data) : Import::return($data);

            if (!is_array($data)) $data = null;

            self::map($viewRef, 'data', $data);
        }

        return $data ?? [];
    }
}