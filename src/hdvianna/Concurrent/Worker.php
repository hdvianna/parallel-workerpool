<?php


namespace hdvianna\Concurrent;


use function Amp\asyncCall;
use Amp\Deferred;
use Amp\Promise;
use Amp\Loop;

class Worker implements Runnable
{

    /**
     * @var WorkFactory
     */
    private $workFactory;

    /**
     * @var string
     */
    private $id;

    public function __construct(WorkFactory $workFactory)
    {
        $this->workFactory = $workFactory;
        $this->id = uniqid();
    }

    public function run(): Promise
    {
        $deferred =  new Deferred();
        $workerPool = $this->workFactory;
        asyncCall(function() use($deferred, $workerPool) {
            Loop::repeat(1, function ($watcherId) use($deferred, $workerPool) {
                if ($workerPool->hasWork()) {
                    $work = $workerPool->createWork();
                    $work->complete();
                } else {
                    $deferred->resolve();
                    Loop::cancel($watcherId);
                }
            });
        });
        return $deferred->promise();
    }


}