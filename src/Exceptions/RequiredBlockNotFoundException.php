<?php

namespace MASNathan\BladePartials\Exceptions;

class RequiredBlockNotFoundException extends \Exception
{
    public function __construct($blockName, $code = 0, \Exception $previous = null)
    {
        $message = "The block '$blockName' is required so it needs to be set.";

        parent::__construct($message, $code, $previous);
    }
}
