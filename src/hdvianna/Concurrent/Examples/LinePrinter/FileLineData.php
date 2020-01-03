<?php


namespace hdvianna\Concurrent\Examples\LinePrinter;


use Amp\ByteStream\OutputStream;

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
     * @var
     */
    private $outputStream;

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

    /**
     * @return OutputStream
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * @param OutputStream $outputStream
     */
    public function setOutputStream($outputStream): void
    {
        $this->outputStream = $outputStream;
    }

}