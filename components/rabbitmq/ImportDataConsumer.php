<?php

namespace app\components\rabbitmq;

use mikemadisonweb\rabbitmq\components\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ImportDataConsumer implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        var_dump($msg->body);
        return ConsumerInterface::MSG_ACK;
    }
}