<?php
return [
    'backend' => [
        'frontName' => 'admin_acfzza'
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => 'ihYDknDssrNQ04XTRqIBvJ7EfBwJ7rE7'
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => '34c_'
            ],
            'page_cache' => [
                'id_prefix' => '34c_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'crypt' => [
        'key' => 'fa79821b939cc3a76026631be07d1551'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'hm_246p1',
                'username' => 'msp',
                'password' => 'msp@123',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'default',
    'session' => [
        'save' => 'files'
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1
    ],
    'downloadable_domains' => [
        'devhm246p1.com'
    ],
    'install' => [
        'date' => 'Mon, 24 Jul 2023 12:45:55 +0000'
    ]
];
