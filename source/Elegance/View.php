<?php

namespace Elegance;

use Elegance\Trait\ViewCurrent;
use Elegance\Trait\ViewLoad;
use Elegance\Trait\ViewMap;
use Elegance\Trait\ViewPrepare;

class View
{
    use ViewCurrent;
    use ViewLoad;
    use ViewMap;
    use ViewPrepare;

    static function render(string|array $viewRef, array $prepare = [], ?bool $encaps = null): string
    {
        if (self::map($viewRef)) {

            self::setCurrent($viewRef, $prepare);

            $prepare = [
                ...self::prepare(),
                ...self::getCurrentPrepare()
            ];

            $content = self::load_content($viewRef, $prepare);
            $script = self::load_script($viewRef, $prepare);
            $style = self::load_style($viewRef, $prepare);

            $incorp = [];

            if ($script && strpos($content, '[#this.script') !== false) {
                $incorpScript = $script;
                $script = '';
                $incorp['this.script'] = function (bool $encaps = true) use ($incorpScript) {
                    return $encaps ? "<script>$incorpScript</script>" : $incorpScript;
                };
            }

            if ($style && strpos($content, '[#this.style') !== false) {
                $incorpStyle = $style;
                $style = '';
                $incorp['this.style'] = function (bool $encaps = true) use ($incorpStyle) {
                    return $encaps ? "<style>$incorpStyle</style>" : $incorpStyle;
                };
            }

            if (!empty($incorp)) $content = prepare($content, $incorp);

            if ($encaps ?? intval(boolval($content)) + intval(boolval($script)) + intval(boolval($style)) > 1) {
                if ($script) $script = "<script>$script</script>";
                if ($style) $style = "<style>$style</style>";
            }

            $content = "$style$content$script";

            self::setCurrent();
        }

        return $content ?? '';
    }
}