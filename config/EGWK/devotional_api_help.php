<?php

return [
    'all' => [
        'description' => 'List of devotionals (last 500 entry)',
        'uri' => '/devotional/{id}',
        'uri_example' => '/devotional/huc-reggelidicseret',
        'uri_example_description' => 'List of Hungarion Union\'s official morning devotionals.',
    ],
    'year' => [
        'description' => 'List of devotionals for a whole year',
        'uri' => '/devotional/year/{year}/{id}',
        'uri_example' => '/devotional/year/2019/huc-reggelidicseret',
        'uri_example_description' => 'List of 2019 Hungarion Union\'s official morning devotionals.',
    ],
    'today' => [
        'description' => 'Today\'s devotional entry',
        'uri' => '/devotional/today/{id}',
        'uri_example' => '/devotional/today/huc-reggelidicseret',
        'uri_example_description' => 'Hungarion Union\'s official morning devotional for today.',
    ],
    'date' => [
        'description' => 'Devotional entry for a specific date',
        'uri' => '/devotional/{date}/{id}',
        'uri_example' => '/devotional/2019-02-28/huc-reggelidicseret',
        'uri_example_description' => 'HUC devotional entry of February 28, 2019.',
    ],
];
