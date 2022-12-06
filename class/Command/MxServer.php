<?php

namespace Command;

use Elegance\File;
use Elegance\MxCmd;
use Exception;

abstract class MxServer extends MxCmd
{
    protected static function execute($port = null, $file = 'index.php')
    {
        $port = $port ?? env('SERVER_PORT');

        if ($file == 'index.php')
            MxCmd::run('create.index');

        if (!File::check($file))
            throw new Exception("file [$file] not found");

        MxCmd::show('-------------------------------------------------');
        MxCmd::show('| Iniciando servidor PHP');
        MxCmd::show('| Acesse: [#]', "http://127.0.0.1:$port/");
        MxCmd::show('| Use: [#] para finalizar o servidor', "CLTR + C");
        MxCmd::show("| Escutando porta [#]", $port);
        MxCmd::show('-------------------------------------------------');
        MxCmd::show('');

        echo shell_exec("php -S 0.0.0.0:$port $file");
    }
}