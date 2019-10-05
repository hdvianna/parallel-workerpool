<?php


namespace hdvianna\Concurrent;


interface WorkFactory
{
    public function  createWork() : Work;
}