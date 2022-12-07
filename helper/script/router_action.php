<?php

use Elegance\Router;

Router::actionPrefix('>', function ($reponse) {
    return "REDIRECIONAR PARA [$reponse]";
});

Router::actionPrefix('#', function ($reponse) {
    return prepare($reponse, Router::data());
}, true);

Router::actionPrefix('@', function ($reponse) {
    return "controller.$reponse";
});