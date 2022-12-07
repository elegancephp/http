<?php

namespace Terminal\Create;

use Elegance\File;
use Elegance\Instance\Cif;
use Elegance\MxCmd;
use Exception;

abstract class MxCif extends MxCmd
{
    protected static function execute($name = null)
    {
        if (!$name)
            throw new Exception('Informe um nome para o certificado');

        $fileName = env('PATH_CIF') . "/$name";

        File::ensure_extension($fileName, 'crt');
        $fileName = path($fileName);

        if (File::check($fileName))
            throw new Exception("Certificado [$name] já existe");

        $allowChar = Cif::BASE;

        $content = [];
        while (count($content) < 63) {
            $charKey = str_shuffle($allowChar);

            while ($charKey == $allowChar || in_array($charKey, $content))
                $charKey = str_shuffle($allowChar);

            $charKey = implode(' ', str_split($charKey, 2));
            $content[] = $charKey;
        }

        $content = implode(' ', $content);

        $content = str_split($content, 21);

        $content = array_map(fn ($value) => trim($value), $content);

        $content = implode("\n", $content);

        File::create($fileName, $content, true);

        MxCmd::show('Certificado [[#].crt] criado com sucesso.', $name);
        MxCmd::show('Adicione [CIF_FILE=[#]] em suas variaveis de ambiente para defini-lo como padrão', $name);
        MxCmd::show('Para chama-lo diretamente, use o codigo [new InstanceCif("[#]")]', $name);
    }
}
