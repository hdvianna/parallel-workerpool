<?php

require_once __DIR__.'/../vendor/autoload.php';

use hdvianna\Concurrent\Examples\LinePrinter\LinePrinterWorkFactory;
use hdvianna\Concurrent\WorkerPool;

if (count($argv) !== 3) {
    echo "Type: file-recorder-workers-test.php <file-path> <number-of-tests>" . PHP_EOL;
    echo "Example: file-recorder-workers-test.php ./data/test-data_x1000.csv 20" . PHP_EOL;
    die;
} else {
    $filePath = $argv[1];
    $numberOfTests = intval($argv[2]);
}

testWorkers($filePath, 1, $numberOfTests);
testWorkers($filePath, 3, $numberOfTests);
testWorkers($filePath, 5, $numberOfTests);
testWorkers($filePath, 10, $numberOfTests);

function testWorkers($filePath, $workersNumber, $numberOfTests)
{
    $results = array();
    for ($i = 0; $i < $numberOfTests; $i++) {
        $result = runTestWorkers($filePath, $workersNumber);
        echo "Finished [" . ($i + 1) . "/$numberOfTests]: $filePath with $workersNumber worker(s) in {$result}s" . PHP_EOL;
        $results[] = $result;
    }
    $averageTime = array_sum($results) / $numberOfTests;
    echo "The average time for $workersNumber worker(s) was {$averageTime}s" . PHP_EOL . PHP_EOL;
}

function runTestWorkers($filePath, $workersNumber)
{
    $t1 = microtime(true);
    $lineRecorderWorkFactory = new LinePrinterWorkFactory($filePath, "_{$workersNumber}workers_");
    $workerPool = new WorkerPool($lineRecorderWorkFactory, $workersNumber);
    $workerPool->run();
    $endTime = microtime(true) - $t1;
    return $endTime;
}