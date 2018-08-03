<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
   
    'supportsCredentials' => false,
    'allowedOrigins' => [ 'http://localhost:4200/', 'http://egwk-front.localhost/', ],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'], //GET', 'POST', 'OPTIONS
    'exposedHeaders' => [],
    'maxAge' => 0,

];
