<?php

namespace Tests;

use hdvianna\Concurrent\WorkFactoryInterface;
use hdvianna\Concurrent\WorkerPool;

function createWorkerConsumerClosureNoSharedData(): \Closure
{
    return function ($work) {
        $work->value;
        file_put_contents("./tests/outputs/work_$work->id.json", json_encode($work));
    };
}

function createWorkerConsumerClosureWithSharedData($initialData): \Closure
{
    return function ($work, $lock, $unlock) use ($initialData) {
        $shared = $lock();
        if (!isset($shared)) {
            $shared = $initialData;
        }
        $shared += $work->value;
        $unlock($shared);
    };
}

function createWorkerPool($numberOfWorkers, $numberOfWorks, $workerConsumerClosure): WorkerPool
{
    return new WorkerPool((new class ($numberOfWorks, $workerConsumerClosure) implements WorkFactoryInterface {

        /**
         * @var int
         */
        private $works;

        private $workerConsumerClosure;

        /***
         *  constructor.
         * @param int $sharedData
         * @param int $works
         */
        public function __construct($works, $workerConsumerClosure)
        {
            $this->works = $works;
            $this->workerConsumerClosure = $workerConsumerClosure;
        }

        public function createWorkGeneratorClosure(): \Closure
        {
            $workers = $this->works;
            return function () use ($workers) {
                for ($i = 0; $i < $workers; $i++) {
                    $work = new \stdClass();
                    $work->id = $i;
                    $work->value = 1;
                    yield $work;
                }
            };
        }

        public function createWorkConsumerClosure(): \Closure
        {
            return $this->workerConsumerClosure;
        }

    }), $numberOfWorkers);
}
