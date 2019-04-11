<?php

return [
    'definitions' => [],
    'singletons' => [
        'rabbitmq.example.consumer' =>  \app\components\rabbitmq\ImportDataConsumer::class,
    ],
];
