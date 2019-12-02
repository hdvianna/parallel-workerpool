<?php


namespace hdvianna\Concurrent;


interface WorkFactory
{
    public function createWork() : Work;
    public function hasWork() : bool;
}