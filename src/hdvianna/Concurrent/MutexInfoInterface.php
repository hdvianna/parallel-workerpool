<?php


namespace hdvianna\Concurrent;


interface MutexInfoInterface
{
    /**
     * @return mixed
     */
    public function getLastValue();
}