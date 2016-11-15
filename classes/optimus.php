<?php
namespace Grav\Plugin;

/**
 * PHP Library for the Optimus API
 *
 * optimus.io - Lossless compression and optimization of your images
 *
 * @author KeyCDN
 * @version 0.1
 */

class Optimus {

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @param string $apiKey
     * @param string|null $endpoint
     */
    public function __construct($apiKey, $endpoint = null) {
        if($endpoint === null) {
            $endpoint = 'https://api.optimus.io';
        }
        $this->setApiKey($apiKey);
        $this->setEndpoint($endpoint);
    }

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey) {
        $this->apiKey = (string) $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndpoint() {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint) {
        $this->endpoint = (string) $endpoint;
        return $this;
    }

    /**
     * @param string $image
     * @param string|null
     * @return string
     * @throws Exception
     */
    public function optimize($image, $option = null) {

        // optimize: image optimization in the same format
        // webp: converts the image to the WebP image format
        if($option === null) {
            $option = 'optimize';
        }

        $endpoint = $this->endpoint.'/'.$this->apiKey.'?'.$option;

        $headers = array(
            'User-Agent: Optimus-API',
            'Accept: image/*'
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => file_get_contents($image),
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ));

        $response = curl_exec($ch);
        $curlError = curl_error($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);

        // error catching
        if (!empty($curlError) || empty($body)) {
            throw new \Exception("Optimus-Error: {$curlError}, Output: {$body}");
        }

        return $body;

    }

}
