<?php

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
    '=',
    fn ($response) => Import::return($response),
    false
);