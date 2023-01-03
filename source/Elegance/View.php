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

    static function render(string|array $viewRef, array $prepare = []): string
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
                    if ($encaps) $incorpScript = "<script>$incorpScript</script>";
                    return $incorpScript;
                };
            }

            if ($style && strpos($content, '[#this.style') !== false) {
                $incorpStyle = $style;
                $style = '';
                $incorp['this.style'] = function (bool $encaps = true) use ($incorpStyle) {
                    if ($encaps) $incorpStyle = "<style>$incorpStyle</style>";
                    return $incorpStyle;
                };
            }

            if (!empty($incorp))
                $content = prepare($content, $incorp);

            if ($script)
                $script = "<script>$script</script>";

            if ($style)
                $style = "<style>$style</style>";

            $content = "$style$content$script";

            self::setCurrent();
        }

        return $content ?? '';
    }
}