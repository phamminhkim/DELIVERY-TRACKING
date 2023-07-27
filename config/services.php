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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    //Thêm login google
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => env('FACEBOOK_APP_CALLBACK_URL'),
    ],
    'onetl' => [
        'client_id' => env('ONETL_APP_ID'),
        'client_secret' => env('ONETL_APP_SECRET'),
        'redirect' => env('ONETL_APP_CALLBACK_URL'),
        'url' => env('ONETL_APP_URL'),
    ],
    'zalo' => [
        'client_id' => env('ZALO_APP_ID'),
        'client_secret' => env('ZALO_APP_SECRET'),
        'redirect' => env('ZALO_APP_CALLBACK_URL'),
        'redirect_oa' => env('ZALO_OA_APP_CALLBACK_URL'),

        'driver_app_env' => env('ZALO_DRIVER_APP_ENV'), // 'production' or 'development
        'driver_app_id' => env('ZALO_DRIVER_APP_ID'),
        'driver_app_version' => env('ZALO_DRIVER_APP_VERSION'),

        'customer_app_env' => env('ZALO_CUSTOMER_APP_ENV'), // 'production' or 'development
        'customer_app_id' => env('ZALO_CUSTOMER_APP_ID'),
        'customer_app_version' => env('ZALO_CUSTOMER_APP_VERSION'),
    ],

];
