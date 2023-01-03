<?php

namespace Terminal;

use Elegance\File;
use Elegance\Import;
use Elegance\MxCmd;
use Exception;

abstract class MxMiddleware extends MxCmd
{
    protected static function execute($middlewareName = null)
    {
        if (!$middlewareName)
            throw new Exception("Informe o nome da middleware");

        $tmp = $middlewareName;
        $tmp = explode('.', $tmp);
        $tmp = array_map(fn ($value) => ucfirst($value), $tmp);

        $class = "Md" . array_pop($tmp);

        $namespace = implode('\\', $tmp);
        $namespace = trim("Middleware\\$namespace", '\\');

        $filePath = path(
            env('PATH_CLASS'),
            str_replace('\\', '/', $namespace),
            "$class.php"
        );

        if (File::check($filePath))
            throw new Exception("Arquivo [$filePath] já existe");

        $prepare = [
            '[#]',
            'class' => $class,
            'namespace' => $namespace,
            'PHP' => '<?php'
        ];

        $base = path(dirname(__DIR__, 2) . '/library/template/mx-create-middleware.txt');

        $content = Import::content($base, $prepare);

        File::create($filePath, $content);

        MxCmd::show('Middleware [[#]] criada com sucesso.', $middlewareName);
    }
}