<?php


namespace hdvianna\Concurrent\Examples\ImageDownloader;


use hdvianna\Concurrent\Work;
use hdvianna\Concurrent\WorkFactory;

class ImageDownloaderWorkFactory implements WorkFactory
{

    private $maximumImages;
    private $imageSavePath;
    private $imagesProduced = 0;

    /**
     * ImageDownloaderWorkFactory constructor.
     * @param $maximumImages
     * @param $imageSavePath
     */
    public function __construct($maximumImages, $imageSavePath)
    {
        $this->maximumImages = $maximumImages;
        $this->imageSavePath = $imageSavePath;
    }

    public function createWork(): Work
    {
        $this->imagesProduced = $this->imagesProduced + 1;
        $imagePath = "$this->imageSavePath".DIRECTORY_SEPARATOR.uniqid().".jpg";
        return new ImageDownloaderWork($imagePath);
    }

    public function hasWork(): bool
    {
        return $this->imagesProduced < $this->maximumImages;
    }

}