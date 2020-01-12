<?php

namespace hdvianna\Concurrent;

use function Amp\Parallel\Worker\enqueueCallable;
use Amp\Promise;

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

    public function run()
    {
        $this->throwExceptionIfStarted(new PoolAlreadyStarted());
        $this->started = true;
        $workerPromises = array_map(function ($worker) {
            return enqueueCallable([$worker, "run"]);
        }, $this->workers);
        Promise\wait(Promise\all($workerPromises));
        $this->started = false;
    }

    private function throwExceptionIfStarted(\Exception $exception):void {
        if ($this->started) {
            throw $exception;
        }
    }


}