<?php

return [
    'class'             => \mikemadisonweb\rabbitmq\Configuration::class,
    //'auto_declare'      => false,
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
            'name' => 'exchange-name',
            'type' => 'direct',
        ],
    ],
    'queues'            => [
        [
            'name'    => 'queue-name',
            'durable' => true,
        ],
        [
            'name'    => 'queue-name2',
            'durable' => true,
        ],
        [
            'name'    => 'queue-name3',
            'durable' => true,
        ],
    ],
    'bindings'          => [
        [
            'queue'    => 'queue-name',
            'exchange' => 'exchange-name',
        ],
    ],
    'producers'         => [
        [
            'name' => 'producer-name',
        ],
    ],
    'consumers'         => [
        [
            'name'      => 'consumer-name',
            'callbacks' => [
                'queue-name' => 'rabbitmq.example.consumer',
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
