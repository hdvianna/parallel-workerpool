<?php

use function Tests\createWorkerConsumerClosureNoSharedData;
use function Tests\createWorkerConsumerClosureWithSharedData;
use function Tests\createWorkerPool;

it('runs 1000 works', function () {
    array_map(fn($file) => unlink($file), glob("tests/outputs/work_*.json"));
    $workerClosure = createWorkerConsumerClosureNoSharedData();
    $pool = createWorkerPool(10, 1000, $workerClosure);
    $pool->run();
    $files = glob("tests/outputs/work_*.json");
    array_map(fn($file) => unlink($file), $files);
    expect($files)->toHaveCount(1000);
});

it('synchronizes shared data', function () {
    $workerClosure = createWorkerConsumerClosureWithSharedData(700);
    $pool = createWorkerPool(10, 1000, $workerClosure);
    $pool->run();
    expect($pool->lastValue())->toBe(1700);
});