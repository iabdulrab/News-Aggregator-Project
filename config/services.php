<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'newsapi' => [
        'base_url' => env('NEWSAPI_BASE_URL', 'https://newsapi.org/v2'),
        'api_key' => env('NEWSAPI_KEY')
    ], 

    'guardian_api' => [
        'base_url' => env('GUARDIAN_BASE_URL', 'https://content.guardianapis.com'),
        'api_key' => env('GUARDIAN_API_KEY')
    ],

    'nyt_api' => [
        'base_url' => env('NYT_BASE_URL', 'https://api.nytimes.com/svc/search/v2'),
        'api_key' => env('NYT_API_KEY')
    ]

];
