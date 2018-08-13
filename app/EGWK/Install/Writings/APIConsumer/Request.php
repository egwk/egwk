<?php

namespace App\EGWK\Install\Writings\APIConsumer;

use App\EGWK\Install\Writings\APIConsumer\Token;

/**
 * Request
 *
 * @author Peter
 */
class Request extends Token
{

    /**
     * Default API URL
     */
    const API_URL = 'https://a.egwwritings.org/';

    /**
     * Performs GET API Request
     *
     * @access public
     * @param string $command Command
     * @param array $parameters Parameters
     * @return array StdClass Result object
     */
    public function get($command, $parameters = [])
    {
        return $this->apiRequest('GET', $command, $parameters);
    }

    /**
     * Performs general API Request
     *
     * @access public
     * @param string $verb HTTP verb
     * @param string $command Command
     * @param array $parameters Parameters
     * @return array StdClass Result object
     */
    protected function apiRequest($verb, $command, $parameters = [])
    {
        $token = $this->getAccessToken();
        if (null !== $token)
        {
            $parameters['query'] = array_merge(array_get($parameters, 'query', []), ['access_token' => $token]);
            return $this->getAPIConsumer()->request($verb, config('install.api_url', self::API_URL) . $command, $parameters);
        }
        return null;
    }

}
