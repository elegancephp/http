<?php

use Elegance\View;

View::prepare('view', fn ($viweRef, $encaps = null) => view($viweRef, [], $encaps));

View::prepare('this.view', fn ($viweRef, $encaps = null) => viewIn($viweRef, [], $encaps));

View::prepare('url', fn () => url(...func_get_args()));