<?php

require_once __DIR__ . '/../vendor/autoload.php';

use hdvianna\Concurrent\WorkFactoryInterface;
use hdvianna\Concurrent\WorkerPool;
use parallel\Channel;

$sharedData = 700;
$works = 1000;

$factory = new class ($sharedData, $works) implements WorkFactoryInterface {

    /**
     * @var parallel\Channel;
     */
    private $channel;

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
        $this->channel = new Channel(1);
        $this->channel->send($sharedData);
        $this->works = $works;
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
        $channel = $this->channel;
        return function ($work) use ($channel) {
            $shared = $channel->recv();
            $channel->send($shared + $work->value);
        };
    }

    public function result()
    {
        return $this->channel->recv();
    }

};
(new WorkerPool($factory, 10))->run();
$result = $factory->result();
echo("\$result is equals to \$works + \$sharedData".PHP_EOL);
echo("$result = $works + $sharedData".PHP_EOL);
assert($result === ($works + $sharedData));