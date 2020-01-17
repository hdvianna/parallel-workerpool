<?php


namespace hdvianna\Concurrent\Examples\LinePrinter;

use hdvianna\Concurrent\WorkFactory;

class LinePrinterWorkFactory implements WorkFactory
{

    private $inputFilePath;
    private $outputFilePath;

    public function __construct(string $filePath, string $identifier = "")
    {
        $this->inputFilePath = $filePath;
        $pathInfo = pathinfo($this->inputFilePath);
        $this->outputFilePath = "{$pathInfo['dirname']}/{$pathInfo['filename']}{$identifier}.out.{$pathInfo['extension']}";
        @unlink($this->outputFilePath);
    }

    public function createWorkGeneratorClosure(): \Closure
    {
        $inputFilePath = $this->inputFilePath;
        return function () use ($inputFilePath) {
            $fileHandler = fopen($inputFilePath, "r");
            $lineNumber = 0;
            while (true) {
                $lineString = fgets($fileHandler);
                $lineNumber++;
                if ($lineString !== FALSE) {
                    $work = new \stdClass();
                    $work->value = $lineString;
                    $work->number = $lineNumber;
                    yield $work;
                } else {
                    fclose($fileHandler);
                    break;
                }
            }
        };
    }

    public function createWorkerClosure(): \Closure
    {
        $outputFilePath = $this->outputFilePath;
        return function ($work) use ($outputFilePath) {
            $fileHandler = fopen($outputFilePath, "a+");
            $outputData = "Line number: {$work->number}; value: {$work->value}";
            flock($fileHandler, LOCK_EX);
            fwrite($fileHandler, $outputData);
            flock($fileHandler, LOCK_UN);
            fclose($fileHandler);
        };
    }


}