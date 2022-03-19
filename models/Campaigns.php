<?php

namespace app\models;

use Yii;
use app\models\Email;
use trntv\filekit\behaviors\UploadBehavior;

/**
 * This is the model class for table "campaigns".
 *
 * @property int $id
 * @property string $subject
 * @property string $body
 * @property string $file_path
 * @property string $file_base_url
 * @property string $created_at
 *
 * @property Emails[] $emails
 */
class Campaigns extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaigns';
    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_INSERT, [$this, 'fireNewCompaign']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject', 'body', 'file'], 'required'],
            [['subject', 'body'], 'string', 'min' => 10],
            [['subject'], 'string', 'max' => 100],
            [['body'], 'string', 'max' => 500],
            [['created_at'], 'safe'],
            [['file_path', 'file_base_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'body' => 'Body',
            'file_path' => 'File Path',
            'file_base_url' => 'File Base Url',
            'created_at' => 'Created At',
        ];
    }

    public function fireNewCompaign()
    {
        $producer = \Yii::$app->rabbitmq->getProducer('campaign');
        $producer->publish(['campaign_id' => $this->id, 'subject' => $this->subject, 'body' => $this->body, 'file_path' => $this->file_path], 'new-campaign');
    }

    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::class,
                'attribute' => 'file',
                'pathAttribute' => 'file_path',
                'baseUrlAttribute' => 'file_base_url',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::className(), ['campaign_id' => 'id']);
    }
}
