<?php

return [
    'class' => \mikemadisonweb\rabbitmq\Configuration::class,
    //'auto_declare'=> false,
    'connections' => [
        [
            'host' => 'localhost',
            'port' => '5672',
            'user' => 'guest',
            'password'  => 'guest',
            'vhost' => '/',
            'heartbeat' => 0,
        ],
    ],
    'exchanges' => [
        [
            'name' => 'new-campaign',
            'type' => 'direct',
        ],
        [
            'name' => 'send-mail-campaign',
            'type' => 'direct',
        ],
    ],
    'queues' => [
        [
            'name' => 'queue-new-campaign',
            'durable' => true,
        ],
        [
            'name' => 'queue-mail-campaign',
            'durable' => true,
        ],
    ],
    'bindings' => [
        [
            'queue' => 'queue-new-campaign',
            'exchange' => 'new-campaign',
        ],
        [
            'queue' => 'queue-mail-campaign',
            'exchange' => 'send-mail-campaign',
        ],
    ],
    'producers' => [
        [
            'name' => 'campaign',
        ],
        [
            'name' => 'send-mail',
        ],
    ],
    'consumers' => [
        [
            'name' => 'consumer-campaign',
            'callbacks' => [
                'queue-new-campaign' => 'rabbitmq.consumer-new-campaign',
            ],
        ],
        [
            'name' => 'consumer-email',
            'callbacks' => [
                'queue-mail-campaign' => 'rabbitmq.consumer-email-campaign',
            ],
        ],
    ],
];
