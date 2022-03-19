<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Campaign';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-index">
    <p class="viewactions">
        <?php echo Html::a('Add Campaign', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="clearfix"></div>
    <div class="table-responsive">
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'subject',
                'body',
                'created_at',
                [
                    'header' => 'Valid Emails',
                    'value' => function ($model) {
                        return '<a href="/campaign/emails?id=' . $model->id . '"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg></a>';
                    },
                    'format' => 'raw'
                ],
                [
                    'header' => 'Invalid Emails',
                    'value' => function ($model) {
                        return '<a href="/campaign/emails?id=' . $model->id . '&type=invalid"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg></a>';
                    },
                    'format' => 'raw'
                ],
            ],
        ]); ?>
    </div>
</div>