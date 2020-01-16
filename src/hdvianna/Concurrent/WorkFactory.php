<?php


namespace hdvianna\Concurrent;


interface WorkFactory
{
    public function createGeneratorClosure() : \Closure;
    public function createWorkerClosure() : \Closure;
}