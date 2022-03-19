<?php

return [
    'definitions' => [],
    'singletons' => [
        'rabbitmq.consumer-new-campaign' =>  \app\components\rabbitmq\ImportCompaginDataConsumer::class,
        'rabbitmq.consumer-email-campaign' =>  \app\components\rabbitmq\SendEmailCompaginDataConsumer::class,
    ],
];
