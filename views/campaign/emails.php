<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = $title;
$this->params['breadcrumbs'][] = 'Campaigns';
?>

<div class="blog-index">
    <h1><?= Html::encode($title) ?></h1>
    
    <div class="clearfix"></div>
    <div class="table-responsive">
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'email',
            ],
        ]); ?>
    </div>
</div>