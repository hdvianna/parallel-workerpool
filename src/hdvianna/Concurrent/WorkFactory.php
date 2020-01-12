<?php


namespace hdvianna\Concurrent;


interface WorkFactory
{
    public function createWork() : Runnable;
    public function hasWork() : bool;
}