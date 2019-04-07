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
    'allowedOrigins' => ['http://*.localhost', 'http://localhost:4200', 'http://localhost:4201', 'https://*.white-konyvtar.hu', 'https://*.whitekonyvtar.hu', 'https://*.egw.hu'],
    // 'allowedOriginsPatterns' => ['*'],
    'allowedHeaders' => ['*'],
    // 'allowedHeaders' => ['Content-Type', 'X-Requested-With'],
    'allowedMethods' => ['GET', 'PUT', 'POST', 'OPTIONS', 'DELETE'],
    'exposedHeaders' => [],
    'maxAge' => 0,
];
