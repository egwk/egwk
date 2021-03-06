<?php

return [
    /*
      |--------------------------------------------------------------------------
      | EGW Writings Token URL
      |--------------------------------------------------------------------------
      |
      |
     */
    'token_url'       => 'https://cpanel.egwwritings.org/o/token/',
    /*
      |--------------------------------------------------------------------------
      | EGW Writings API URL
      |--------------------------------------------------------------------------
      |
      |
     */
    'api_url'         => 'https://a.egwwritings.org/',
    /*
      |--------------------------------------------------------------------------
      | EGW Writings CSV path
      |--------------------------------------------------------------------------
      |
      |
     */
    'egwwritings_csv' => storage_path("egwwritings.csv"),
    /*
      |--------------------------------------------------------------------------
      | Skip filters
      |--------------------------------------------------------------------------
      |
      |
     */
    'skip'            => [
        /*
          |--------------------------------------------------------------------------
          | Skip these folders
          |--------------------------------------------------------------------------
          |
          | 'Biography' has non-EGW books, except LSMS.
          |
         */
        'folders'     => ['Indexes', 'Annotated',],
        /*
          |--------------------------------------------------------------------------
          | Skip these book titles
          |--------------------------------------------------------------------------
          |
          |
         */
        'titles'      => [],
        /*
          |--------------------------------------------------------------------------
          | Skip these books codes
          |--------------------------------------------------------------------------
          |
          | 'Biography' folder has non-EGW books, except LSMS. Skipping everything else.
          |
         */
        'codes'       => [
            'EGWE',
            '1BIO',
            '2BIO',
            '3BIO',
            '4BIO',
            '5BIO',
            '6BIO',
            'WV',
        ],
        /*
          |--------------------------------------------------------------------------
          | Skip these authors
          |--------------------------------------------------------------------------
          |
          |
         */
        'authors'     => [],
    ],
    /*
      |--------------------------------------------------------------------------
      | Enable only filters. Keep them empty to enable all.
      |--------------------------------------------------------------------------
      |
      |
     */
    'enable'          => [
        /*
         |--------------------------------------------------------------------------
         | Enable only these top folders
         |--------------------------------------------------------------------------
         |
         | For pioneer authors, add 'Adventist Pioneer Library'. Mind the author key, when adding this!
         | There are more EGW compilations under the 'Reference' top folder
         |
        */
        'top'             => ['EGW Writings'],
        /*
          |--------------------------------------------------------------------------
          | Enable only these folders
          |--------------------------------------------------------------------------
          |
          | There are more EGW compilations in 'EGW Research Documents' (under the 'Reference' top folder), eg. MRQI
          |
         */
        'folders'         => [],
        /*
          |--------------------------------------------------------------------------
          | Enable only these book titles
          |--------------------------------------------------------------------------
          |
          |
         */
        'titles'          => [],
        /*
          |--------------------------------------------------------------------------
          | Enable only these books codes
          |--------------------------------------------------------------------------
          |
          |
         */
        'codes'           => [],
        /*
           |--------------------------------------------------------------------------
           |  Enable only these authors
           |--------------------------------------------------------------------------
           |
           |
          */
        'authors'         => ['Ellen Gould White'],
    ],
    /*
      |--------------------------------------------------------------------------
      | Writings language
      |--------------------------------------------------------------------------
      |
      |
     */
    'language'        => 'en',
];
