<?php


namespace hdvianna\Concurrent;


use Amp\Promise;

interface Runnable
{
    public function run() : Promise;
}