<?php

return [
    'class' => \mikemadisonweb\rabbitmq\Configuration::class,
    'connections' => [
        'default' => [
            'host' => '127.0.0.1',
            'port' => '5672',
            'user' => 'rabbitmq',
            'password' => 'rabbitmq',
            'vhost' => '/',
            'heartbeat' => 0,
        ],
    ],
    'producers' => [
        'import_data' => [
            'connection' => 'default',
            'exchange_options' => [
                'name' => 'import_data',
                'type' => 'direct',
            ],
        ],
    ],
    'consumers' => [
        'import_data' => [
            'connection' => 'default',
            'exchange_options' => [
                'name' => 'import_data', // Name of exchange to declare
                'type' => 'direct', // Type of exchange
            ],
            'queue_options' => [
                'name' => 'import_data', // Queue name which will be binded to the exchange adove
                'routing_keys' => ['import_data'], // Your custom options
                'durable' => true,
                'auto_delete' => false,
            ],
            'callback' => 'rabbitmq.import-data.consumer',
        ],
    ],
];