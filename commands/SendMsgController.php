<?php

namespace app\commands;

use mikemadisonweb\rabbitmq\components\ConsumerInterface;
use mikemadisonweb\rabbitmq\components\Producer;
use mikemadisonweb\rabbitmq\Configuration;
use yii\console\Controller;

class SendMsgController extends Controller
{
    public $message = "Hi RabbitMQ!";

    public function options($actionID)
    {
        return ['message'];
    }

    public function optionAliases()
    {
        return ['m' => 'message'];
    }

    public function actionPublish($period = 1)
    {
        /** @var Producer $producer */
        $producer = \Yii::$app->rabbitmq->getProducer('producer-name');
        while (true) {
            $producer->publish($this->message, 'exchange-name');
            sleep($period);
        }
    }
}
