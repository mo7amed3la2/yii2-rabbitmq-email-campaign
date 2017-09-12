<?php

return [
    'definitions' => [],
    'singletons' => [
        'rabbitmq.import-data.consumer' =>  \app\components\rabbitmq\ImportDataConsumer::class,
    ],
];