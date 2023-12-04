<?php

/** @var yii\web\View $this */

use app\models\NapiegyenlegSearch;
use gabordikan\cor4\datatables\DataTables;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;

$this->title = 'Napi egyenlegek';

if (empty($idoszak)) {
    $idoszak = date('Y-m');
}

?>
<div class="site-index">
<?php
if (Yii::$app->user->isGuest) {
?>
    Lépjen be a funkciók eléréséhez
<?php
}
else {

    $searchModel = new NapiegyenlegSearch('napiegyenleg');
    $dataProvider = $searchModel->search();
    echo  DataTables::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'clientOptions' => [
            'prefix' => 'napiegyenleg',
            'order' => [
                [0, 'desc'],
                [1, 'asc'],
            ],
        ],
        'columns' => [
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'datum',
                'label' => 'Dátum',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'penztarca_nev',
                'label' => 'Pénztárca',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    //var_dump($model); die();
                    return str_replace('&nbsp;',' ',Yii::$app->formatter->asCurrency($model->nyito, $model->deviza)); 
                },
                'label' => 'Nyitó',
                'contentOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    //var_dump($model); die();
                    return str_replace('&nbsp;',' ',Yii::$app->formatter->asCurrency($model->bevetel, $model->deviza)); 
                },
                'label' => 'Bevétel',
                'contentOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    //var_dump($model); die();
                    return str_replace('&nbsp;',' ',Yii::$app->formatter->asCurrency($model->kiadas, $model->deviza)); 
                },
                'label' => 'Kiadás',
                'contentOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    //var_dump($model); die();
                    return str_replace('&nbsp;',' ',Yii::$app->formatter->asCurrency($model->egyenleg, $model->deviza)); 
                },
                'label' => 'Egyenleg',
                'contentOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
            ],
	    [
                'class' => ActionColumn::class,
                'visibleButtons' => [
                    'view' => true,
                    'update' => false,
                    'delete' => false,
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    switch ($action) {
                        case "view":
                            return '/site/listkess?search[1]='.$model->datum.'&search[0]='.$model->penztarca_nev.'&search[2]=&search[3]=&search[4]=&search[5]=';
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
    ]);
}
?>
