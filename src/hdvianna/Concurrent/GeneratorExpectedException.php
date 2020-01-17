<?php


namespace hdvianna\Concurrent;


use Throwable;

class GeneratorExpectedException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("The closure from the method WorkFactory::createWorkGeneratorClosure() must return a Generator.", $code, $previous);
    }
}