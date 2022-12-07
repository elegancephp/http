<?php

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
    '&',
    fn ($response) => view($response, Router::data()),
    true
);

Router::actionPrefix(
    '@',
    fn ($response) => "controller.$response",
    false
);