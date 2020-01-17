<?php

namespace hdvianna\Concurrent;

use parallel\{Runtime,Channel};

class WorkerPool implements Runnable
{
    /**
     * @var WorkerFactory
     */
    private $workFactory;
    private $workerClosures = [];
    private $started = false;

    public function __construct(WorkFactory $workFactory, int $startingNumberOfWorkers = 0)
    {
        $this->checkIfParallelExtensionIsAvailable();
        $this->workFactory = $workFactory;
        for($i = 0; $i < $startingNumberOfWorkers; $i++) {
            $this->appendWorker();
        }
    }

    private function checkIfParallelExtensionIsAvailable()
    {
        if (!extension_loaded("parallel")) {
            throw new ParallelExtensionNotAvailableException();
        }
    }

    public function appendWorker() : WorkerPool {
        $this->throwExceptionIfStarted(new WorkerAdditionException());
        $this->workerClosures[] = $this->workFactory->createWorkerClosure();
        return $this;
    }

    public function run()
    {
        $this->throwExceptionIfStarted(new PoolAlreadyStarted());
        $channel = new Channel();
        $futures = array_merge($this->createArrayOfWorkGeneratorFutures($channel), $this->createArrayOfWorkerFutures($channel));
        $this->wait($futures);
        $this->started = false;
    }

    private function throwExceptionIfStarted(\Exception $exception):void
    {
        if ($this->started) {
            throw $exception;
        }
    }

    private function createArrayOfWorkGeneratorFutures($channel)
    {
        $producer = new Runtime();
        $workGeneratorClosure = $this->workFactory->createWorkGeneratorClosure();
        $producerFuture = $producer->run(function (Channel $channel, \Closure $workGeneratorClosure) {
            $workGenerator = $workGeneratorClosure();
            if (is_a($workGenerator, "\Generator")) {
                foreach($workGeneratorClosure() as $product) {
                    $channel->send($product);
                }
                $channel->close();
            } else {
                throw new GeneratorExpectedException();
            }
        }, [$channel, $workGeneratorClosure]);
        return [$producerFuture];
    }

    private function createArrayOfWorkerFutures($channel)
    {
        $workerFutures = array_map(function($workerClosure) use ($channel) {
            $worker = new Runtime();
            $workerFuture = $worker->run(function($channel, $workerClosure) {
                while(true) {
                    try {
                        $work = $channel->recv();
                        $workerClosure($work);
                    } catch (\parallel\Channel\Error\Closed $ex) {
                        break;
                    }
                }
            }, [$channel, $workerClosure]);
            return $workerFuture;
        }, $this->workerClosures);
        return $workerFutures;
    }

    private function wait(array $futures):void
    {
        array_map(function ($future) {
            return $future->value();
        }, $futures);
    }

}