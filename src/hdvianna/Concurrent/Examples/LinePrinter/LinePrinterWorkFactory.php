<?php


namespace hdvianna\Concurrent\Examples\LinePrinter;

use hdvianna\Concurrent\Work;
use hdvianna\Concurrent\WorkFactory;

class LinePrinterWorkFactory implements WorkFactory
{

    private $fileHandler;
    private $ended = false;
    private $lineNumber = 0;
    private $lineString = "";


    public function __construct(string $filePath)
    {
        $this->fileHandler = fopen($filePath, "r");
        $this->moveNext();
    }

    public function createWork(): Work
    {
        $fileLineData = new FileLineData();
        $fileLineData
                ->setLineNumber($this->lineNumber)
                ->setLineString($this->lineString);
        $this->moveNext();
        return new LinePrinterWork($fileLineData);
    }

    public function hasWork(): bool
    {
        return !$this->ended;
    }

    private function moveNext() {
        $this->lineString = fgets($this->fileHandler);
        if ($this->lineString === FALSE) {
            $this->ended = true;
            fclose($this->fileHandler);
        } else {
            $this->lineNumber += 1;
        }
    }


}