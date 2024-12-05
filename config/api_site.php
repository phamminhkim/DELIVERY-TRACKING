<?php

return [
    'connections' => [
        'saphana' => [
            'username' => env('SAP_USER',''),
            'password' => env('SAP_PASS',''),
            'url' => env('SAP_URL',''),
        ],
        'eoffice' => [
            'username' => env('EOFFICE_USER',''),
            'password' => env('EOFFICE_PASS',''),
            'url' => env('EOFFICE_URL',''),
        ],
        'jst' => [
            'app_key' => env('JST_APP_KEY',''),
            'app_secret' => env('JST_APP_SECRET',''),
            'app_address_auth' => env('JST_APP_ADDRESS_AUTH_URL',''),
            'app_openapi_url' => env('JST_APP_OPENAPI_URL',''),
            'app_callback_url' => env('JST_APP_CALLBACK_URL',''),
        ],

    ],
    'domain_root'=> env('DOMAIN_ROOT','#'),
    'domain_link_home'=> env('DOMAIN_LINK_HOME','#'),
    'workflow_subdomain_name'=> 'request',
];
