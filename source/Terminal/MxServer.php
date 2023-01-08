<?php

namespace Terminal;

use Elegance\MxCmd;

abstract class MxServer extends MxCmd
{
    protected static function execute($port = null)
    {
        $port = $port ?? env('SERVER_PORT');

        MxCmd::run('create.index');

        MxCmd::show('-------------------------------------------------');
        MxCmd::show('| Iniciando servidor PHP');
        MxCmd::show('| Acesse: [#]', "http://127.0.0.1:$port/");
        MxCmd::show('| Use: [#] para finalizar o servidor', "CLTR + C");
        MxCmd::show("| Escutando porta [#]", $port);
        MxCmd::show('-------------------------------------------------');
        MxCmd::show('');

        echo shell_exec("php -S 127.0.0.1:$port index.php");
    }
}