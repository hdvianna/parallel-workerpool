<?php


namespace hdvianna\Concurrent;


use Throwable;

class PoolAlreadyStarted extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Pool already started.", $code, $previous);
    }
}