<?php

use Elegance\Assets;
use Elegance\Router;

Router::add(
    'favicon.ico',
    fn () => Assets::send(dirname(__DIR__, 2) . '/library/assets/favicon.ico')
);