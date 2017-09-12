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
    'on before_consume' => function ($event) {
        echo "before_consume!\n";
    },
    'on after_consume' => function ($event) {
        echo "after_consume!\n";
    },
    'on before_publish' => function ($event) {
        echo "before_publish!\n";
    },
    'on after_publish' => function ($event) {
        echo "after_publish!\n";
    },
];