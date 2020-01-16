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

    public function __construct(WorkFactory $workFactory)
    {
        $this->workFactory = $workFactory;
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

        $producer = new Runtime();
        $producerFuture = $producer->run(function (Channel $channel, \Closure $producer) {
            foreach($producer() as $product) {
                $channel->send($product);
            }
            $channel->close();
        }, [$channel, $this->workFactory->createGeneratorClosure()]);

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

        $futures = array_merge([$producerFuture], $workerFutures);
        $this->wait($futures);
        $this->started = false;
    }

    private function throwExceptionIfStarted(\Exception $exception):void
    {
        if ($this->started) {
            throw $exception;
        }
    }

    private function wait(array $futures):void
    {
        array_map(function ($future) {
            return $future->value();
        }, $futures);
    }

}