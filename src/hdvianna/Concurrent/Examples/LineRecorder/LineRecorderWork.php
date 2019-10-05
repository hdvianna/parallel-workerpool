<?php


namespace hdvianna\Concurrent\Examples\LineRecorder;

use hdvianna\Concurrent\Work;

class LineRecorderWork implements Work
{

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var FileLineData
     */
    private $fileLineData;

    public function __construct(\PDO $pdo, FileLineData $fileLineData)
    {
        $this->pdo = $pdo;
        $this->fileLineData = $fileLineData;
    }

    public function complete(): void
    {
        $this->pdo->prepare(
            "INSERT INTO files_line(line_string, line_number) VALUES (?,?)",
            [$this->fileLineData->getLineString(), $this->fileLineData->getLineNumber()]
        )->execute();
    }

}