<?php

namespace app\commands;

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

    public function actionPublish()
    {
        \Yii::$app->rabbitmq->load();
        $producer = \Yii::$container->get(sprintf('rabbit_mq.producer.%s', 'import_data'));
        $msg = serialize([$this->message]);
        while (true) {
            echo $this->message . "\n";
            $producer->publish($msg, 'import_data');
            sleep(1);
        }
    }
}
