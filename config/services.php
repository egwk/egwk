<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_KEY'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_KEY'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_KEY'),
        'client_secret' => env('TWITTER_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI'),
    ],

    /*
   |--------------------------------------------------------------------------
   | Lilypond score server
   |--------------------------------------------------------------------------
   */
    'lily' => [
        'server' => env('LILY_HOST', 'lily'),
        'port' => env('LILY_PORT', '8008'),
        'script' => env('LILY_SCRIPT', 'lilyserver.php'),
    ],

    /*
   |--------------------------------------------------------------------------
   | EGWWritings API
   |--------------------------------------------------------------------------
   */
    'egwwritings' => [

        'client_id' => env('EGWWRITINGS_KEY'),
        'client_secret' => env('EGWWRITINGS_SECRET'),
        'redirect_uri' => env('EGWWRITINGS_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sabbath School API
    |--------------------------------------------------------------------------
    |
    | NOTE: It depends on the church field
    |
    */
    'ssq' => [
        'url' => env('SSQ_API', '/ssq-api'),
    ],


];
