<?php

require_once __DIR__.'/../vendor/autoload.php';

use hdvianna\Concurrent\Examples\ImageDownloader\ImageDownloaderWorkFactory;
use hdvianna\Concurrent\WorkerPool;

if (count($argv) !== 3) {
    echo "Type: image-downloader-workers-test.php <number-of-images> <number-of-tests>".PHP_EOL;
    echo "Example: image-downloader-workers-test.php 15 20".PHP_EOL;
    die;
} else {
    $numberOfImages = intval($argv[1]);
    $numberOfTests = intval($argv[2]);
}

testWorkers($numberOfImages, 1, $numberOfTests);
testWorkers($numberOfImages, 3, $numberOfTests);
testWorkers($numberOfImages, 5, $numberOfTests);
testWorkers($numberOfImages, 10, $numberOfTests);

function testWorkers($numberOfImages, $workersNumber, $numberOfTests)
{
    $results = array();
    for($i = 0; $i < $numberOfTests; $i++) {
        $result = runTestWorkers($numberOfImages, $workersNumber);
        echo "Finished [" . ($i + 1) ."/$numberOfTests]: $numberOfImages image(s) with $workersNumber worker(s) in {$result}s".PHP_EOL;
        $results[] = $result;
    }
    $averageTime = array_sum($results)/$numberOfTests;
    echo "The average time for $workersNumber worker(s) was {$averageTime}s".PHP_EOL.PHP_EOL;
}

function runTestWorkers($numberOfImages, $workersNumber)
{
    $t1 = microtime(true);
    $imageDownloaderWorkFactory = new ImageDownloaderWorkFactory($numberOfImages, __DIR__.DIRECTORY_SEPARATOR."downloads");
    $workerPool = new WorkerPool($imageDownloaderWorkFactory, $workersNumber);
    $workerPool->run();
    $endTime = microtime(true) - $t1;
    return $endTime;
}