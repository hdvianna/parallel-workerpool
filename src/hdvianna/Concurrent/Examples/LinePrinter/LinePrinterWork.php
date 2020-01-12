<?php


namespace hdvianna\Concurrent\Examples\LinePrinter;

use hdvianna\Concurrent\Runnable;

class LinePrinterWork implements Runnable
{

    /**
     * @var FileLineData
     */
    private $fileLineData;

    public function __construct(FileLineData $fileLineData)
    {
        $this->fileLineData = $fileLineData;
    }

    public function run()
    {
        $outputData = "Line number: {$this->fileLineData->getLineNumber()}; value: {$this->fileLineData->getLineString()}";
        $this->fileLineData->getOutputStream()->write($outputData);
    }

}