<?php

namespace App\EGWK\Install\Writings;

use GuzzleHttp;

/**
 * API
 *
 * @author Peter
 */
class APIConsumer
{

    /**
     *
     * @var GuzzleHttp Guzzle Http Client
     */
    protected $client = null;

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->client = new GuzzleHttp\Client();
    }

    /**
     * Performs API Request
     *
     * @access public
     * @param string $verb HTTP verb
     * @param string $url URL
     * @param array $parameters Parameters
     * @return array StdClass Result object
     */
    public function request($verb, $url, $parameters = [])
    {
        $response = $this->client->request($verb, $url, $parameters);
        if (200 == $response->getStatusCode() && in_array('application/json', $response->getHeader('content-type')))
        {
            return json_decode($response->getBody());
        }
        return null;
    }

}
