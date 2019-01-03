<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGWK Web settings
    |--------------------------------------------------------------------------
    */

    'web' => [
        'domain' => env('DOMAIN_WEB', 'www.white-konyvtar.hu'),
    ],

    /*
    |--------------------------------------------------------------------------
    | EGWK API settings
    |--------------------------------------------------------------------------
    */

    'api' => [
        'domain' => env('DOMAIN_API', 'api.white-konyvtar.hu'),
        'query_limit' => env('API_QUERY_LIMIT', 1000),
        'pagination_limit' => env('API_PAGINATION', 25),
    ],

];
