<?php

require_once __DIR__ . '/../vendor/autoload.php';

use hdvianna\Concurrent\WorkFactoryInterface;
use hdvianna\Concurrent\WorkerPool;

$sharedData = 700;
$works = 1000;

$pool = new WorkerPool((new class ($sharedData, $works) implements WorkFactoryInterface {


    /**
     * @var int
     */
    private $sharedData;

    /**
     * @var int
     */
    private $works;

    /***
     *  constructor.
     * @param int $sharedData
     * @param int $works
     */
    public function __construct($sharedData, $works)
    {
        $this->works = $works;
        $this->sharedData = $sharedData;
    }

    public function createWorkGeneratorClosure(): \Closure
    {
        $workers = $this->works;
        return function () use ($workers) {
            for ($i = 0; $i < $workers; $i++) {
                $work = new \stdClass();
                $work->value = 1;
                yield $work;
            }
        };
    }

    public function createWorkConsumerClosure(): \Closure
    {
        $initialData = $this->sharedData;
        return function ($work, $lock, $unlock) use ($initialData) {
            $shared = $lock();
            if (!isset($shared)) {
                $shared = $initialData;
            }
            $shared += $work->value;
            $unlock($shared);
        };
    }

}), 10);
$pool->run();
$result = $pool->lastValue();
echo("\$result equals to \$works + \$sharedData?" . PHP_EOL);
echo("($result equals to $works + $sharedData?)" . PHP_EOL);
echo(assert($result === ($works + $sharedData)) ? "Yes!": "No =(").PHP_EOL;
