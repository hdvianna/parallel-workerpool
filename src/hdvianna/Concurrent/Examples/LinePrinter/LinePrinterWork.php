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
        $outputData = "Line number: {$this->fileLineData->getLineNumber()}; value: {$this->fileLineData->getLineString()}";
        $fileHandler = fopen($this->fileLineData->getOutputPath(), 'a');
        flock($fileHandler, LOCK_EX );
        fwrite($fileHandler, $outputData);
        flock($fileHandler, LOCK_UN  );
        fclose($fileHandler);
    }

}