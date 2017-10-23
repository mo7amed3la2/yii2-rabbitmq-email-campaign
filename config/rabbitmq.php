<?php

return [
    'class' => \mikemadisonweb\rabbitmq\Configuration::class,
    //'autoDeclare' => false,
    'connections' => [
        [
            'host' => 'rabbitmq',
            'port' => '5672',
            'user' => 'rabbitmq',
            'password' => 'rabbitmq',
            'vhost' => '/',
            'heartbeat' => 0,
        ]
    ],
    'exchanges' => [
        [
            'name' => 'import',
            'type' => 'direct'
        ],
    ],
    'queues' => [
        [
            'name' => 'import',
            'durable' => true,
        ],
    ],
    'bindings' => [
        [
            'queue' => 'import',
            'exchange' => 'import',
            'routingKeys' => ['import'],
        ],
    ],
    'producers' => [
        [
            'name' => 'import',
        ],
    ],
    'consumers' => [
        [
            'name' => 'import',
            'callbacks' => [
                'import' => 'rabbitmq.import-data.consumer'
            ],
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