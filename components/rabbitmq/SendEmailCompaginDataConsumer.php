<?php

namespace app\components\rabbitmq;

use Yii;
use PhpAmqpLib\Message\AMQPMessage;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;

class SendEmailCompaginDataConsumer implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $data = $msg->getBody();
        $subject = $data['subject'];
        $body = $data['body'];
        $email = $data['email'];


        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['adminName']])
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();

        echo "Done Send Email" . PHP_EOL;
        return ConsumerInterface::MSG_ACK;
    }
}
