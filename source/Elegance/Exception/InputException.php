<?php

namespace Elegance\Exception;

use Exception;
use Throwable;

class InputException extends Exception
{
    function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $code = $code ? $code : STS_BAD_REQUEST;
        parent::__construct($message, $code, $previous);
    }
}