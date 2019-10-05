<?php


namespace hdvianna\Concurrent\Examples\LineRecorder;

use hdvianna\Concurrent\Work;
use hdvianna\Concurrent\WorkFactory;

class LineRecorderWorkFactory implements WorkFactory
{

    private $fileHandler;
    private $ended = false;
    private $lineNumber = 0;

    /**
     * @var \PDO
     */
    private $pdo;


    public function __construct(\PDO $pdo, string $filePath)
    {
        $this->pdo = $pdo;
        $this->fileHandler = fopen($filePath, "r");
    }

    public function createWork(): Work
    {
        $line = fgets($this->fileHandler);
        $this->lineNumber += 1;
        if ($line === false) {
            $this->ended = true;
            fclose($this->fileHandler);
        }

        $fileLinaData = new FileLineData();
        $fileLinaData
            ->setLineNumber($this->lineNumber)
            ->setLineString($line);

        return new LineRecorderWork($this->pdo, $fileLinaData);
    }

}