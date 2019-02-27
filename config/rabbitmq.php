<?php

return [
    'class'             => \mikemadisonweb\rabbitmq\Configuration::class,
    'auto_declare'      => false,
    'connections'       => [
        [
            'host'      => 'rabbitmq',
            'port'      => '5672',
            'user'      => 'rabbitmq',
            'password'  => 'rabbitmq',
            'vhost'     => '/',
            'heartbeat' => 0,
        ],
    ],
    'exchanges'         => [
        [
            'name' => 'import',
            'type' => 'fanout',
        ],
    ],
    'queues'            => [
        [
            'name'    => 'import',
            'durable' => true,
        ],
        [
            'name'    => 'import2',
            'durable' => true,
        ],
        [
            'name'    => 'import3',
            'durable' => true,
        ],
    ],
    'bindings'          => [
        [
            'queue'    => 'import',
            'exchange' => 'import',
        ],
    ],
    'producers'         => [
        [
            'name' => 'import',
        ],
    ],
    'consumers'         => [
        [
            'name'      => 'import',
            'callbacks' => [
                'import' => 'rabbitmq.import-data.consumer',
            ],
        ],
    ],
    'on before_consume' => function ($event)
    {
        echo "before_consume!\n";
    },
    'on after_consume'  => function ($event)
    {
        echo "after_consume!\n";
    },
    'on before_publish' => function ($event)
    {
        echo "before_publish!\n";
    },
    'on after_publish'  => function ($event)
    {
        echo "after_publish!\n";
    },
];
