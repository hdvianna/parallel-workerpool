<?php


namespace hdvianna\Concurrent\Examples\ImageDownloader;


use hdvianna\Concurrent\WorkFactory;

class ImageDownloaderWorkFactory implements WorkFactory
{

    private $maximumImages;
    private $imageSavePath;

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

    public function createGeneratorClosure(): \Closure
    {
        $maximumImages = $this->maximumImages;
        $imageSavePath = $this->imageSavePath;
        return function () use ($maximumImages, $imageSavePath) {
            $imagesProduced = 0;
            while ($imagesProduced < $maximumImages) {
                $imagesProduced++;
                $imagePath = "$imageSavePath".DIRECTORY_SEPARATOR.uniqid().".jpg";
                yield $imagePath;
            }
        };
    }

    public function createWorkerClosure(): \Closure
    {
        return function($savePath) {
            $url = "https://picsum.photos/800/600/?blur=2";
            $curlHandler = curl_init($url);
            curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curlHandler);
            curl_close($curlHandler);
            $fileHandler = fopen($savePath, "w+");
            fwrite($fileHandler, $result);
            fclose($fileHandler);
        };
    }

}