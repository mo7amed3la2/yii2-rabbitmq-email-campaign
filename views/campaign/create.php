<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;

$this->title = 'Campaign';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="site-campaigns">
    <h1>Add New <?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('campaignsFormSubmitted')) : ?>

        <div class="alert alert-success">
            Thank you Campaigns Form Submitted. We Will Notify You After Campaign is Ready.
        </div>

    <?php else : ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'subject')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'body')->textarea() ?>
                <?= $form->field($model, 'file')->widget(
                    '\trntv\filekit\widget\Upload',
                    [
                        'url' => ['upload'],
                        'uploadPath' => 'compagin', // optional, for storing files in storage subfolder
                        'sortable' => true,
                        'acceptFileTypes' => new JsExpression('/(\.|\/)(xls|xlsx)$/i'),
                        'maxFileSize' => 10 * 1024 * 1024, // almost 10 MiB
                    ]
                ); ?>

                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'campaigns-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>
</div>