<?php


namespace hdvianna\Concurrent;


interface WorkFactoryInterface
{
    public function createWorkGeneratorClosure() : \Closure;
    public function createWorkConsumerClosure() : \Closure;
}