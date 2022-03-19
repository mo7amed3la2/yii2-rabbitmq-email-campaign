<?php

namespace app\commands;

use yii\console\Controller;

class SendMsgController extends Controller
{
    public $message = "Hi RabbitMQ!";

    public function actionPublish($period = 1)
    {
        $producer = \Yii::$app->rabbitmq->getProducer('send-mail');
        $producer->publish(['subject' => 'Mail Subject', 'body' => 'Mail Body', 'email' => 'm.3laa.95@gmail.com'], 'send-mail-campaign');
    }
}
