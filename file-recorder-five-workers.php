<?php

require_once './vendor/autoload.php';

use hdvianna\Concurrent\Examples\LinePrinter\LinePrinterWorkFactory;
use hdvianna\Concurrent\WorkerPool;

testFiveWorkers( './test-data_x1000.csv');

function testFiveWorkers( string $filePath) {
    $t1 = microtime(true);
    $lineRecorderWorkFactory = new LinePrinterWorkFactory($filePath);
    $workerPool = new WorkerPool($lineRecorderWorkFactory);
    $workerPool
        ->appendWorker()
        ->appendWorker()
        ->appendWorker()
        ->appendWorker()
        ->appendWorker()
        ->run();
    $endTime = microtime(true) - $t1;
    var_dump("Five workers ended in {$endTime}s");
}