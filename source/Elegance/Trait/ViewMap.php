<?php

namespace Elegance\Trait;

use Elegance\Dir;
use Elegance\File;

trait ViewMap
{

    protected static array $pathMap = [];
    protected static array $map = [];

    /** Retorna o mapa de uma view */
    static function map(string $viewRef, string $property = null, mixed $value = null): string|array|null
    {
        $viewMap = self::loadMap($viewRef);

        return match (func_num_args()) {
            1 => ($viewMap),
            2 => ($viewMap[$property] ?? null),
            default => (self::$map[$viewRef][$property] = $value)
        };
    }

    /** Define o diretório de uma referencia de view */
    static function mapPath($viewRef, $path): void
    {
        if (isset(self::$map[$viewRef]))
            unset(self::$map[$viewRef]);

        self::$pathMap[$viewRef] = $path;
    }

    /** Carrega o mapa de uma view */
    protected static function loadMap(string $viewRef): array
    {
        if (!isset(self::$map[$viewRef])) {
            $path = self::$pathMap[$viewRef] ?? env('PATH_VIEW');
            $path .= '/' . str_replace('.', '/', $viewRef);
            $path = path($path);

            $map = ['path' => $path];

            foreach (Dir::seek_for_file($path) as $file) {
                $filePath = path("$path/$file");
                $fileName = File::getOnly($file);

                $fileType = match ($fileName) {
                    'content.html', 'content.php' => 'content',
                    'data.json', 'data.php' => 'data',
                    'script.js' => 'script',
                    'style.css' => 'style',
                    default => false
                };

                if ($fileType) $map[$fileType] = $filePath;
            }

            self::$map[$viewRef] = $map;
        }

        return self::$map[$viewRef];
    }
}