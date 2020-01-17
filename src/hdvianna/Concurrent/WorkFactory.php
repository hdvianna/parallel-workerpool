<?php


namespace hdvianna\Concurrent;


interface WorkFactory
{
    public function createWorkGeneratorClosure() : \Closure;
    public function createWorkConsumerClosure() : \Closure;
}