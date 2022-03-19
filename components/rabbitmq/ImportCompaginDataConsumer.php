<?php

namespace app\components\rabbitmq;

use PhpAmqpLib\Message\AMQPMessage;
use app\helper\ImporterExtend as Importer;
use app\models\Email;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;

class ImportCompaginDataConsumer implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $data = $msg->getBody();
        $campaign_id = $data['campaign_id'];
        $file_path = $data['file_path'];

        $file = \Yii::getAlias('@upload/' . $file_path);
        if (file_exists($file)) {
            $importer = new Importer([
                'filePath' => $file,
                'activeRecord' => Email::class,
                'skipFirstRow' => true,
                'fields' => [
                    [
                        'attribute' => 'campaign_id',
                        'value' => function ($row) use ($campaign_id) {
                            return $campaign_id;
                        },
                    ],
                    [
                        'attribute' => 'email',
                        'value' => function ($row) {
                            return strval($row[0]) ? strval($row[0]) : null;
                        },
                    ],
                    [
                        'attribute' => 'is_valid',
                        'value' => function ($row) {
                            if (filter_var(strval($row[0]), FILTER_VALIDATE_EMAIL)) {
                                return 1;
                            }
                            return 0;
                        },
                    ],
                ],

            ]);

            if (!$importer->validate()) {
                // here we can handle errors scenario
            } else {
                $importer->save();
                $subject = $data['subject'];
                $body = $data['body'];

                // here we can handle success scenario. such as send email to queue.
                $emails = Email::find()->where(['campaign_id' => $campaign_id, 'is_valid' => Email::STATUS_VAILD])->all();
                foreach ($emails as $email) {
                    $producer = \Yii::$app->rabbitmq->getProducer('send-mail');
                    $producer->publish(['subject' => $subject, 'body' => $body, 'email' => $email->email], 'send-mail-campaign');
                }
            }
        }

        echo "Done". PHP_EOL;
        return ConsumerInterface::MSG_ACK;
    }
}
