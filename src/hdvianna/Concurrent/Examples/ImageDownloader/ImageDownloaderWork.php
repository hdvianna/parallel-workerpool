<?php


namespace hdvianna\Concurrent\Examples\ImageDownloader;


use Curl\Curl;
use hdvianna\Concurrent\Work;

class ImageDownloaderWork implements Work
{

    private $savePath;

    /**
     * ImageDownloaderWork constructor.
     * @param $savePath
     */
    public function __construct($savePath)
    {
        $this->savePath = $savePath;
    }

    public function complete()
    {
        $url = "https://picsum.photos/800/600/?blur=2";
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->download($url, $this->savePath);
        $curl->close();
    }

}