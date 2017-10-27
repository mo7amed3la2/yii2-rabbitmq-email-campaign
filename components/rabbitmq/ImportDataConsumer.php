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
        sleep(1);
        var_dump($msg->getBody());
        return ConsumerInterface::MSG_ACK;
    }
}