<?php


namespace hdvianna\Concurrent;


use Throwable;

class ParallelExtensionNotAvailableException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Parallel extension is not available. For instructions about how to install and configure it visit www.php.net/manual/en/parallel.setup.php", $code, $previous);
    }
}