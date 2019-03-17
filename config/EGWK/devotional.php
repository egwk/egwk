<?php

return [
    'hu-reggelidicseret' => [
        'class' => \App\EGWK\Devotional\Driver\Blogger\HucReggeliDicseret::class,
        'url' => 'http://reggelidicseret.blogspot.com/',
        'timezone' => '+03:00', // +1 enough, but +3 for sure
        'keyfile' => storage_path(env('GOOGLE_API_KEYFILE', 'egwk.json')),
        'appname' => 'HUC Reggeli Dicseret',
        'scopes' => ['https://www.googleapis.com/auth/blogger']
    ]
];
