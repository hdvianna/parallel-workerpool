<?php

require_once './vendor/autoload.php';

use hdvianna\Concurrent\Examples\LinePrinter\LinePrinterWorkFactory;
use hdvianna\Concurrent\WorkerPool;

testOneWorker( './test-data_x1000.csv');

function testOneWorker(string $filePath) {
    $t1 = microtime(true);
    $lineRecorderWorkFactory = new LinePrinterWorkFactory($filePath);
    $workerPool = new WorkerPool($lineRecorderWorkFactory);
    $workerPool
        ->appendWorker()
        ->run();
    $endTime = microtime(true) - $t1;
    var_dump("One worker ended in {$endTime}s");
}