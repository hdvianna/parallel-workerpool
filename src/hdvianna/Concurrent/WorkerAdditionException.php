<?php


namespace hdvianna\Concurrent;


use Throwable;

class WorkerAdditionException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("It is not possible to append workers while the pool is running.", $code, $previous);
    }

}