<?php

namespace app\commands;

use mikemadisonweb\rabbitmq\components\ConsumerInterface;
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
        $producer = \Yii::$app->rabbitmq->getProducer('import');
        while (true) {
            $producer->publish($this->message, 'import', 'import');
            sleep($period);
        }
    }
}
