<?php

namespace app\controllers;

use Yii;
use app\models\Email;
use yii\web\Controller;
use app\models\Campaigns;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CampaignController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'fileStorage' => 'fileStorage',
            ],
            'delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction',
            ]
        ];
    }

    /**
     * Displays Campaign form.
     *
     * @return string
     */
    public function actionIndex()
    {
        $campaigns = Campaigns::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $campaigns,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $campaign = new Campaigns();

        if ($campaign->load(Yii::$app->request->post()) && $campaign->save()) {
            Yii::$app->session->setFlash('campaignsFormSubmitted');
            return $this->refresh();
        }
        return $this->render('create', [
            'model' => $campaign,
        ]);
    }

    public function actionEmails($id, $type = 'valid')
    {
        $campaign = Campaigns::findOne($id);

        if (!$campaign) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $emails = Email::find()->where(['campaign_id' => $id]);

        if ($type == 'valid') {
            $emails->andWhere(['is_valid' => Email::STATUS_VAILD]);
            $title = 'Campaign ' . $campaign->body . ' List Valid Emails';
        } else {
            $emails->andWhere(['is_valid' => Email::STATUS_NOT_VALID]);
            $title = 'Campaign ' . $campaign->body . ' List Invalid Emails';
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $emails,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        return $this->render('emails', [
            'dataProvider' => $dataProvider,
            'title' => $title,
        ]);
    }
}
