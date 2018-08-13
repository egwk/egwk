<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EGWK\Install\Writings\APIConsumer;

/**
 * TokenStore
 *
 * @author Peter
 */
interface TokenStore
{

    /**
     * Stores Access token
     * 
     * @param string $token Access token
     * @param int $expiresIn Token expires in seconds
     * @return void
     */
    public function store(string $token, int $expiresIn);

    /**
     * Read Access token
     * 
     * @return null|string
     */
    public function read(): ?string;
}
