<?php

namespace Command\Create;

use Elegance\File;
use Elegance\Import;
use Elegance\MxCmd;

abstract class MxIndex extends MxCmd
{
    protected static function execute()
    {
        $fileName = "./index.php";

        if (!File::check($fileName)) {
            $base = path(dirname(__DIR__, 3) . '/library/template/mx-create-index.txt');

            $base = Import::content($base);

            $content = prepare($base, ['PHP' => '<?php']);

            File::create($fileName, $content);

            MxCmd::show('Arquivo de index instalado');
        } else {
            MxCmd::show('Arquivo de index encontrado');
        }
    }
}