<?php

namespace hdvianna\Concurrent;

use Amp\Deferred;
use Amp\Promise;
use Amp\Loop;

class WorkerPool implements Runnable
{
    /**
     * @var WorkerFactory
     */
    private $workFactory;
    private $workers = [];
    private $started = false;

    public function __construct(WorkFactory $workFactory)
    {
        $this->workFactory = $workFactory;
    }

    public function appendWorker() : WorkerPool {
        $this->throwExceptionIfStarted(new WorkerAdditionException());
        $this->workers[] = new Worker($this->workFactory);
        return $this;
    }

    public function run(): Promise
    {
        $this->throwExceptionIfStarted(new PoolAlreadyStarted());
        $deferred =  new Deferred();
        $workers = $this->workers;
        $this->started = true;
        $started = &$this->started;
        Loop::run(function () use($workers, $deferred, &$started) {
            Promise\all(array_map(function ($worker) {
                return $worker->run();
            }, $workers));

            $deferred->resolve();
            $started = false;
        });
        return $deferred->promise();
    }

    private function throwExceptionIfStarted(\Exception $exception):void {
        if ($this->started) {
            throw $exception;
        }
    }


}