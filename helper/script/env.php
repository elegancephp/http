<?php

use Elegance\Env;

Env::default('SERVER_PORT', 8081);

Env::default('PATH_CIF', 'library/certificate');

Env::default('CODE_KEY', 'elegnace');


Env::default('RELATIVE_METHOD', true);


Env::default('RESPONSE_CACHE', 672);


Env::default('PATH_VIEW', 'view');

Env::default('VIEW_MINIFY', true);


Env::default('SESSION_TIME', 672);

Env::default('COOKIE_TIME', Env::get('SESSION_TIME'));