<?php

namespace Coreproc\MsisdnPh\Exceptions;

class InvalidMsisdnException extends \Exception
{
    public static function create($message)
    {
        return new self('The supplied mobile number is invalid.');
    }
}
