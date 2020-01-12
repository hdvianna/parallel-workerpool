<?php


namespace hdvianna\Concurrent;

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

    public function run()
    {
        while($this->workFactory->hasWork()) {
            $work = $this->workFactory->createWork();
            $work->run();
        }
    }


}