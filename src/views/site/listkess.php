<?php

/** @var yii\web\View $this */

use app\models\MozgasSearch;
use gabordikan\cor4\datatables\DataTables;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;

$this->title = 'Tételek';

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

    $searchModel = new MozgasSearch('mozgas');
    $dataProvider = $searchModel->search();

    $data = $dataProvider->query->all();
    $sum = 0;

    foreach ($data as $row) {
        $sum += $row['tipus'] * $row['osszeg'];
    }

    echo  DataTables::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'text-align: right'],
        'clientOptions' => [
            'prefix' => 'mozgas',
            'order' => [
                [0, 'desc'],
            ],
        ],
        'columns' => [
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return $model->datum; 
                },
                'label' => 'Dátum',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'penztarca.nev',
                'label' => 'Pénztárca',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'kategoriak.fokategoria',
                'label' => 'Kategória',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'kategoriak.nev',
                'label' => 'Tétel',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return str_replace('&nbsp;',' ',Yii::$app->formatter->asCurrency($model->tipus * $model->osszeg, $model->penztarca->deviza)); 
                },
                //'format' => ['currency', $deviza = Penztarca::findOne($model->penztarca_id)->deviza],
                'label' => 'Összeg',
                'contentOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
                'footer' => '<B>'.number_format($sum, 0, ',',' ').'</B>',
                'footerOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'label' => 'Megjegyzés',
                'attribute' => 'megjegyzes',
            ],
            [
                'class' => ActionColumn::class,
                'visibleButtons' => [
                    'view' => false,
                    'update' => true,
                    'delete' => true,
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    switch ($action) {
                        case "update":
                            return '/site/recordkess?update_id='.$model->id.'&from_list=1';
                        case "delete":
                            return '/site/listkess?&delete_id='.$model->id;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
    ]);
}
?>