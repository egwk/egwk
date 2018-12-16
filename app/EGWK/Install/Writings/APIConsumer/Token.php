<?php

namespace App\EGWK\Install\Writings\APIConsumer;

use App\EGWK\Install\Writings\APIConsumer;
use App\EGWK\Install\Writings\APIConsumer\TokenStore;

/**
 * Token
 *
 * @author Peter
 */
class Token
{

    const TOKEN_URL = 'https://cpanel.egwwritings.org/o/token/';

    /**
     * 
     * @var APIConsumer API consumer object
     */
    protected $apiConsumer = null;

    /**
     * 
     * @var TokenStore Store Token
     */
    protected $tokenStore = null;

    /**
     * Class constructor
     *
     * @access public
     * @param APIConsumer $apiConsumer EGWWritings API consumer object
     * @return void
     */
    public function __construct(APIConsumer $apiConsumer, TokenStore $store)
    {
        $this->apiConsumer = $apiConsumer;
        $this->tokenStore = $store;
    }

    /**
     * Gets API
     *
     * @access public
     * @return APIConsumer EGWWritings API consumer object
     */
    public function getAPIConsumer(): APIConsumer
    {
        return $this->apiConsumer;
    }

    /**
     * Gets Access Token from Redis, or Requests a new one if not exists or expired
     *
     * @access public
     * @return string Access Token
     */
    public function getAccessToken(): string
    {
        if (null === $this->tokenStore->read())
        {
            $responseObject = $this->requestAccessToken();
            return $this->storeAccessToken($responseObject);
        }
        return $this->tokenStore->read();
    }

    /**
     * Stores Access Token in Redis
     *
     * @access public
     * @param StdClass $tokenObject StdClass Token Request result
     * @return null|string
     */
    public function storeAccessToken($tokenObject)
    {
        if (isset($tokenObject->access_token))
        {
            $this->tokenStore->store($tokenObject->access_token, $tokenObject->expires_in);
            return (string) $tokenObject->access_token;
        }
        return null;
    }

    /**
     * Requests Access Token
     *
     * @access protected
     * @return StdClass Token Request result
     */
    protected function requestAccessToken()
    {
        return $this->getAPIConsumer()->request('POST', config('install.token_url', self::TOKEN_URL), [
                    'form_params' => [
                        'client_id' => config('services.egwwritings.client_id'),
                        'client_secret' => config('services.egwwritings.client_secret'),
                        'redirect_uri' => config('services.egwwritings.redirect_uri'),
                        'grant_type' => 'client_credentials',
                        'response_type' => 'id_token',
                    ],
                    'headers' => [
                    ]
        ]);
    }

}
