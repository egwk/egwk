<?php

namespace App\EGWK\Install\Writings\APIConsumer\TokenStore;

use App\EGWK\Install\Writings\APIConsumer\TokenStore;
use Illuminate\Support\Facades\Redis as RedisFacade;

/**
 * TokenStore
 *
 * @author Peter
 */
class Redis implements TokenStore
{

    /**
     * Stores Access token
     * 
     * @param string $token Access token
     * @param int $expiresIn Token expires in seconds
     * @return void
     */
    public function store(string $token, int $expiresIn)
    {
        RedisFacade::set('egwk:egwwritings:token', $token);
        RedisFacade::command('expire', ['egwk:egwwritings:token', $expiresIn]);
    }

    /**
     * Read Access token
     * 
     * @return null|string
     */
    public function read(): ?string
    {
        return RedisFacade::get('egwk:egwwritings:token');
    }

}
