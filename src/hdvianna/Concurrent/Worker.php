<?php


namespace hdvianna\Concurrent;


use Amp\Deferred;
use Amp\Promise;
use hdvianna\hdvianna\Concurrent\WorkFactory;

class Worker implements Runnable
{

    /**
     * @var WorkFactory
     */
    private $workFactory;

    public function __construct(WorkFactory $workFactory)
    {
        $this->workFactory = $workFactory;
    }

    public function run(): Promise
    {
        $deferred =  new Deferred();
        $workerPool = $this->workFactory;
        Loop::run(function () use($deferred, $workerPool) {
            $work = $workerPool->createWork();
            if ($work) {
                $work->complete();
            } else {
                $deferred->resolve();
            }
        });
        return $deferred->promise();
    }


}