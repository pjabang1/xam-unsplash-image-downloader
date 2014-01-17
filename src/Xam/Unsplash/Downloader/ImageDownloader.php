<?php

namespace Xam\Unsplash\Downloader;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ImageDownloader {

    /**
     *
     * @var type 
     */
    private $client;

    /**
     *
     * @var string 
     */
    private $path;


    /**
     * 
     * @param type $client
     */
    public function __construct($client = null) {
        $this->client = $client;
    }

    /**
     * 
     * @param type $total
     * @return string
     */
    private function getUrl($start) {
        if ($start) {
            return $this->getPath() . '?start=' . $start;
        }
        return $this->getPath();
    }

    /**
     * 
     * @param mixed null|int $start
     * @return \DOMDocument
     */
    public function getDocument($start = null) {
        $dom = new \DOMDocument();
        $dom->strictErrorChecking = FALSE;

        try {
            @$dom->loadHTMLFile($this->getUrl($start));
        } catch (\Exception $e) {
            
        }

        return $dom;
    }
    

    /**
     * 
     * @param \DOMDocument $document
     * @param type $path
     */
    public function getImageUrls(\DOMDocument $document, $path = '//photo-link-url') {
        $finder = new \DomXPath($document);
        $return = array();

        $nodes = $finder->query($path);
        foreach ($nodes AS $node) {
            $return[] = $node->nodeValue;
        }
        return $return;
    }

    /**
     * 
     * @param type $from
     * @param type $to
     */
    public function download($src, $downloadDirectory) {
        if (!file_exists($downloadDirectory)) {
            throw new \Unsplash\Exception\UnsplashException($downloadDirectory . ' does not exist');
        }
        $temp = $downloadDirectory . '/' . uniqid();
        $response = $this->getClient()->get($src)
                ->setResponseBody($temp)
                ->send();
        
        $toFile = $downloadDirectory . '/' . basename($response->getEffectiveUrl());
        rename($temp, $toFile);
    }

    public function getClient() {
        return $this->client;
    }

    /**
     * 
     * @param \Guzzle\Http\Client $client
     * @return \Unsplash\Downloader\ImageDownloader
     */
    public function setClient(\Guzzle\Http\Client $client) {
        $this->client = $client;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getTotal() {
        $document = $this->getDocument();
        $finder = new \DomXPath($document);
        $totalNode = $finder->query('//posts/@total');
        foreach ($totalNode AS $node) {
            $total = (int) $node->nodeValue;
        }
        return  $total;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * 
     * @param string $path
     * @return \Xam\Unsplash\Downloader\ImageDownloader
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

}

?>
