<?php


namespace hdvianna\Concurrent;


class PoolRunningException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("The pool is running.", $code, $previous);
    }
}