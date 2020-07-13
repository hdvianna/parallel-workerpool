<?php

namespace hdvianna\Concurrent;

use Exception;
use parallel\{Runtime,Channel};
use parallel\Channel\Error\Closed;

class WorkerPool implements RunnableInterface
{
    /**
     * @var WorkerFactory
     */
    private $workFactory;
    private $workerClosures = [];
    private $started = false;

    /**
     * WorkerPool constructor.
     * @param WorkFactoryInterface $workFactory
     * @param int $startingNumberOfWorkers
     * @throws ParallelExtensionNotAvailableException
     * @throws Exception
     */
    public function __construct(WorkFactoryInterface $workFactory, int $startingNumberOfWorkers = 0)
    {
        $this->checkIfParallelExtensionIsAvailable();
        $this->workFactory = $workFactory;
        for($i = 0; $i < $startingNumberOfWorkers; $i++) {
            $this->appendWorker();
        }
    }

    /**
     * @throws ParallelExtensionNotAvailableException
     */
    private function checkIfParallelExtensionIsAvailable()
    {
        if (!extension_loaded("parallel")) {
            throw new ParallelExtensionNotAvailableException();
        }
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function appendWorker() : WorkerPool {
        $this->throwExceptionIfStarted(new WorkerAdditionException());
        $this->workerClosures[] = $this->workFactory->createWorkConsumerClosure();
        return $this;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->throwExceptionIfStarted(new PoolAlreadyStarted());
        $channel = new Channel();
        $futures = array_merge($this->createArrayOfWorkGeneratorFutures($channel), $this->createArrayOfWorkConsumerFutures($channel));
        $this->wait($futures);
        $this->started = false;
    }

    /**
     * @param Exception $exception
     * @throws Exception
     */
    private function throwExceptionIfStarted(Exception $exception):void
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

    private function createArrayOfWorkConsumerFutures($channel)
    {
        return array_map(function($workerClosure) use ($channel) {
            $worker = new Runtime();
            return $worker->run(function($channel, $workerConsumerClosure) {
                while(true) {
                    try {
                        $work = $channel->recv();
                        $workerConsumerClosure($work);
                    } catch (Closed $ex) {
                        break;
                    }
                }
            }, [$channel, $workerClosure]);
        }, $this->workerClosures);
    }

    private function wait(array $futures):void
    {
        array_map(function ($future) {
            return $future->value();
        }, $futures);
    }

}