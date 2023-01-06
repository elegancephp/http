<?php

use Elegance\Assets;
use Elegance\Import;
use Elegance\Router;

Router::actionPrefix(
    '>',
    fn ($response) => redirect($response),
    true
);

Router::actionPrefix(
    '#',
    fn ($response) => prepare($response, Router::data()),
    true
);

Router::actionPrefix(
    'import:',
    fn ($response) => Import::return($response),
    false
);

Router::actionPrefix(
    'assets:',
    fn ($response) => Assets::get($response),
    true
);

Router::add('favicon.ico', 'assets:' . dirname(__DIR__, 2) . '/library/assets/favicon.ico');