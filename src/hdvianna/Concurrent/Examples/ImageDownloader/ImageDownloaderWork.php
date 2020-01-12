<?php


namespace hdvianna\Concurrent\Examples\ImageDownloader;


use Curl\Curl;
use hdvianna\Concurrent\Runnable;

class ImageDownloaderWork implements Runnable
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

    public function run()
    {
        $url = "https://picsum.photos/800/600/?blur=2";
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->download($url, $this->savePath);
        $curl->close();
    }

}