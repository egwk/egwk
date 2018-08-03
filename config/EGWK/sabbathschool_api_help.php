<?php

/*
  |--------------------------------------------------------------------------
  | Reader API Endpoints help
  |--------------------------------------------------------------------------
  |
  |
 */

/*
/
/books
/devotionals
/book/{code}
/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}
/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}
/metadata/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}
/metadata/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}
/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}
/parallel/{code}/{lang?}/{publisher?}/{year?}/{no?}
/paragraph/{refcode_short}/{lang?}/{publisher?}/{year?}/{no?}
/search
/trsearch
/similarity/{para_id}
/merge
 */

return [
    'available' => [
        'description' => 'List of available Sabbath School quarters.',
        'uri' => '/sabbathschool/list',
        'uri_example' => '/sabbathschool/list',
    ],
    'quarter' => [
        'description' => 'Sabbath School Quarterly list of lesson URIs of a given quarter.',
        'uri' => '/sabbathschool/{year}/{quarter}/',
        'uri_example' => '/sabbathschool/2018/1',
    ],
    'date' => [
        'description' => 'Sabbath School Lesson for a specific date.',
        'uri' => '/sabbathschool/date/{date}/',
        'uri_example' => '/sabbathschool/date/20180226',
    ],
    'html' => [
        'description' => 'Sabbath School Lesson in HTML format for a specific date.',
        'uri' => '/sabbathschool/html/{date}/',
        'uri_example' => '/sabbathschool/html/20180226',
    ],
];
