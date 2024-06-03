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

    ],
    'domain_root'=> env('DOMAIN_ROOT','#'),
    'domain_link_home'=> env('DOMAIN_LINK_HOME','#'),
    'workflow_subdomain_name'=> 'request',
];