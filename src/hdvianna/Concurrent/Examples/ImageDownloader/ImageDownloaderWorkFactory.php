<?php


namespace hdvianna\Concurrent\Examples\ImageDownloader;


use hdvianna\Concurrent\Runnable;
use hdvianna\Concurrent\WorkFactory;

class ImageDownloaderWorkFactory implements WorkFactory
{

    private $maximumImages;
    private $imageSavePath;
    private $imagesProduced = 0;
    private $lockFileName;

    /**
     * ImageDownloaderWorkFactory constructor.
     * @param $maximumImages
     * @param $imageSavePath
     */
    public function __construct($maximumImages, $imageSavePath)
    {
        $this->maximumImages = $maximumImages;
        $this->imageSavePath = $imageSavePath;
        $this->lockFileName = $this->imageSavePath.DIRECTORY_SEPARATOR.uniqid().".lock";
    }

    public function __destruct()
    {
        @unlink($this->lockFileName);
    }

    public function createWork(): Runnable
    {
        $this->incrementImagesProduced();
        $imagePath = "$this->imageSavePath".DIRECTORY_SEPARATOR.uniqid().".jpg";
        return new ImageDownloaderWork($imagePath);
    }

    private function  incrementImagesProduced()
    {
        $lockFile = fopen($this->lockFileName, "w+");
        flock($lockFile, LOCK_EX);
        $this->imagesProduced = $this->imagesProduced + 1;
        flock($lockFile, LOCK_UN );
        fclose($lockFile);
    }

    public function hasWork(): bool
    {
        $hasWork = $this->imagesProduced < $this->maximumImages;
        return $hasWork;
    }

}