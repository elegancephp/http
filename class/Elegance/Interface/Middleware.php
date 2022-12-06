<?php

namespace Elegance\Interface;

use Closure;

interface Middleware
{
    function __invoke(Closure $next);
}