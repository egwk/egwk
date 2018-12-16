<?php

/*
  |--------------------------------------------------------------------------
  | API Endpoints help
  |--------------------------------------------------------------------------
  |
  |
 */

return [
    'name' => 'EGWK API',
    'version' => '1.6',
    'notes' => 'Always use the first translation (translations[0]) for the official / most important translation.',
    'changelog' => [
        '1.6' => [
            'date' => '2018-12-13',
            'note' => 'Added Synch API (todo doc)',
        ],
        '1.5' => [
            'date' => '2018-08-09',
            'note' => 'Added search result clustering, routes updated',
        ],
        '1.4.1' => [
            'date' => '2018-02-27',
            'note' => 'Added sample Romanian language books (SC, TA). Language codecan be added to /books, eg. /books/ro lists Romanian language books.',
        ],
        '1.4' => [
            'date' => '2018-02-26',
            'note' => 'Added Sabbath School (2005/II-): /sabbathschool. Ellen White quotes attached (2013-), where available.',
        ],
        '1.3' => [
            'date' => '2018-02-25',
            'note' => 'Added hymnals: /hymnal',
        ],
        '1.2' => [
            'date' => '2018-01-21',
            'note' => 'Added metadata queries: /metadata/toc/ and /metadata/chapter/',
        ],
        '1.1' => [
            'date' => '2018-01-08',
            'note' => 'Added new API endpoints: /toc, /chapter and /parallel. Book list extended with URIs.',
        ],
        '1.0' => [
            'date' => '2018-01-07',
            'note' => 'First release.',
        ],
    ],
];

