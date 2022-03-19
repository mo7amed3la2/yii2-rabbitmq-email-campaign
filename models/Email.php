<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "emails".
 *
 * @property integer             $id
 * @property string              $email
 * @property integer             $is_valid
 
 */
class Email extends ActiveRecord
{
    const STATUS_VAILD = 1;
    const STATUS_NOT_VALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%emails}}';
    }


    /**
     * @return array statuses list
     */
    public static function statuses()
    {
        return [
            self::STATUS_VAILD => Yii::t('app', 'Valid'),
            self::STATUS_NOT_VALID => Yii::t('app', 'Not Valid'),
        ];
    }


    public function beforeSave($event)
    {

        return parent::beforeSave($event);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'campaign_id', 'is_valid'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
        ];
    }
}
