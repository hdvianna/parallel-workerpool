<?php


namespace hdvianna\Concurrent\Examples\LinePrinter;

use hdvianna\Concurrent\Work;
use Amp\Mysql\Pool;

class LinePrinterWork implements Work
{

    /**
     * @var FileLineData
     */
    private $fileLineData;

    public function __construct(FileLineData $fileLineData)
    {
        $this->fileLineData = $fileLineData;
    }

    public function complete()
    {
        var_dump("Line number: {$this->fileLineData->getLineNumber()}; value: {$this->fileLineData->getLineString()}");
    }

}