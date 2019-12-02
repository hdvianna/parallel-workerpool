<?php


namespace hdvianna\Concurrent\Examples\LinePrinter;


class FileLineData
{
    /**
     * @var int
     */
    private $lineNumber;

    /**
     * @var string
     */
    private $lineString;

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function setLineNumber(int $lineNumber): FileLineData
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getLineString(): string
    {
        return $this->lineString;
    }

    /**
     * @param string $lineString
     */
    public function setLineString(string $lineString): FileLineData
    {
        $this->lineString = $lineString;
        return $this;
    }


}