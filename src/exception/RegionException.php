<?php
namespace afiqiqmal\MalaysiaHoliday\exception;

use Throwable;

class RegionException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}