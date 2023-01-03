<?php

namespace Elegance\Trait;

trait ViewCurrent
{
    protected static array $currentView = [];
    protected static array $currentPrepare = [];

    /** Retorna a referencia da view atual */
    static function getCurrent(): ?string
    {
        $currentView = end(self::$currentView);
        return $currentView ? $currentView : null;
    }

    /** Retorna o prepare data atual */
    static function getCurrentPrepare(): array
    {
        $currentPrepare = end(self::$currentPrepare);
        return $currentPrepare ? $currentPrepare : [];
    }

    /** Define ou remove uma view da lista de views atuais */
    protected static function setCurrent(?string $viewRef = null, array $prepare = [])
    {
        if ($viewRef) {
            self::$currentView[] = $viewRef;
            self::$currentPrepare[] = [
                ...self::load_data($viewRef),
                ...self::getCurrentPrepare(),
                ...$prepare
            ];
        } else {
            array_pop(self::$currentView);
            array_pop(self::$currentPrepare);
        }
    }
}