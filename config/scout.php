<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that gets used while
    | using Laravel Scout. This connection is used when syncing all models
    | to the search service. You should adjust this based on your needs.
    |
    | Supported: "algolia", "elasticsearch", "sphinxsearch", "null"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'sphinxsearch'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. This prefix may be useful if you have multiple
    | "tenants" or applications sharing the same search infrastructure.
    |
    */

    'prefix' => env('SCOUT_PREFIX', 'e3si_'),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search engines are queued. When this is set to "true" then
    | all automatic data syncing will get queued for better performance.
    |
    */

    'queue' => false,

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a cloud hosted
    | search engine which works great with Scout out of the box. Just plug
    | in your application ID and admin API key to get started searching.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],
    'elasticsearch' => [
        'index' => env('ELASTICSEARCH_INDEX', 'laravel'),
        'hosts' => [
            env('ELASTICSEARCH_HOST', 'localhost'),
        ],
    ],
    'sphinx' => [
        'host' => env('SCOUT_HOST', 'sphinx'),
        'port' => env('SCOUT_PORT', '9306'),
//        'charset' => env('SCOUT_CHARSET', ''),
//        'socket' => env('SCOUT_SOCKET', ''),
    ],
];
