<?php

/** @var yii\web\View $this */

use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Terv;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;

use dosamigos\chartjs\ChartJs;

$this->title = 'Kess';
?>
<div class="site-index">
<?php
if (Yii::$app->user->isGuest) {
?>
    Lépjen be a funkciók eléréséhez
<?php
}
else {
    $dataProvider = new ActiveDataProvider([
        'query' => Penztarca::find()->where(['felhasznalo' => Yii::$app->user->id]),
        'pagination' => [
            'pageSize' => 20,
        ],
    ]);

    echo GridView::widget([
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return $model->nev; 
                },
                'format' => 'text',
                'label' => 'Név',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return Penztarca::getEgyenleg($model->id); 
                },
                'format' => ['currency','HUF'],
                'label' => 'Egyenleg',
                'contentOptions' => ['style'=>'text-align: right'],
            ],
            [
                'class' => ActionColumn::class,
                'visibleButtons' => [
                    'view' => true,
                    'update' => true,
                    'delete' => false,
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    switch ($action) {
                        case "view":
                            return 'index.php?r=site%2Flistkess&penztarca_id='.$model->id;
                        case "update":
                            return 'index.php?r=site%2Frecordkess&penztarca_id='.$model->id;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],

            ],
        ],
        'dataProvider' => $dataProvider,
    ]);

    echo "<div><H1>Összesen (".number_format(Penztarca::getOsszEgyenleg(),0,',',' ').")</H1></div>";


    $dataProvider = new ActiveDataProvider([
        'query' => Kategoriak::find()
            ->where(['felhasznalo' => Yii::$app->user->id])
            ->groupBy('tipus'),
        'pagination' => [
            'pageSize' => 20,
        ],
    ]);

    echo GridView::widget([
        'columns' => [
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return $model->tipus; 
                },
                'format' => 'text',
                'label' => '',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return Terv::getTervSum($model->tipus, date('Y-m'), date('Y-m'));
                },
                'format' => ['currency','HUF'],
                'label' => 'Terv',
                'contentOptions' => ['style'=>'text-align: right'],
            ],
        ],
        'dataProvider' => $dataProvider,
    ]);

    //todo hónap választás
    $tol = date('Y-m-01');
    $ig = date('Y-m-31');

    echo "</div><div>";

    echo ChartJs::widget([
        'type' => 'doughnut',
        'id' => 'structurePie',
        'options' => [
            'width' => 300,
            'height' => 400,
        ],
        'data' => [
            'radius' =>  "90%",
            'labels' => Kategoriak::getFokategoriakLista(), // Your labels
            'datasets' => [
                [
                    'data' => Kategoriak::getFokategoriakListaEgyenleg($tol, $ig, 1), // Your dataset
                    'label' => '',
                    'backgroundColor' => Kategoriak::getFokategoriakSzinek(),
                    'borderColor' => '#FFFFFF',
                    'borderWidth' => 1,
                    'hoverBorderColor'=>"#999",
                ],
                [
                    'data' => Kategoriak::getFokategoriakListaEgyenleg($tol, $ig, -1), // Your dataset
                    'label' => '',
                    'backgroundColor' => Kategoriak::getFokategoriakSzinek(),
                    'borderColor' =>  '#fff',
                    'borderWidth' => 1,
                    'hoverBorderColor'=>"#999",
                ]
            ]
        ],
        'clientOptions' => [
            'legend' => [
                'display' => true,
                'position' => 'bottom',
                'labels' => [
                    'fontSize' => 14,
                    'fontColor' => "#425062",
                ]
            ],
            'tooltips' => [
                'enabled' => true,
                'intersect' => true
            ],
            'hover' => [
                'mode' => false
            ],
            'maintainAspectRatio' => false,

        ]]);

    echo "</div><div>";

    echo ChartJs::widget([
        'type' => 'bar',
        'id' => 'structurePie2',
        'options' => [
            'indexAxis' => 'y',
        ],
        'data' => [
            'labels' => Kategoriak::getKategoriakLista('Kiadás'),
            'datasets' => [
                [
                    'label' => "Terv",
                    'backgroundColor' => "rgba(255,10,0,0.5)",
                    'borderColor' => "rgba(255,10,0,1)",
                    'pointBackgroundColor' => "rgba(255,10,0,1)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(255,10,0,1)",
                    'data' => Kategoriak::getSumTerv('Kiadás', $tol, $ig)
                ],
                [
                    'label' => "Tény",
                    'backgroundColor' => "rgba(10,255,0,0.5)",
                    'borderColor' => "rgba(10,255,0,1)",
                    'pointBackgroundColor' => "rgba(10,255,0,1)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(10,255,0,1)",
                    'data' => Kategoriak::getSumTeny('Kiadás', $tol, $ig)
                ]
            ]
        ]
    ]);

    echo "</div><div>";

    echo ChartJs::widget([
        'type' => 'bar',
        'id' => 'structurePie3',
        'options' => [
            'indexAxis' => 'y',
        ],
        'data' => [
            'labels' => Kategoriak::getKategoriakLista('Bevétel'),
            'datasets' => [
                [
                    'label' => "Terv",
                    'backgroundColor' => "rgba(255,10,0,0.5)",
                    'borderColor' => "rgba(255,10,0,1)",
                    'pointBackgroundColor' => "rgba(255,10,0,1)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(255,10,0,1)",
                    'data' => Kategoriak::getSumTerv('Bevétel', $tol, $ig)
                ],
                [
                    'label' => "Tény",
                    'backgroundColor' => "rgba(10,255,0,0.5)",
                    'borderColor' => "rgba(10,255,0,1)",
                    'pointBackgroundColor' => "rgba(10,255,0,1)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(10,255,0,1)",
                    'data' => Kategoriak::getSumTeny('Bevétel', $tol, $ig)
                ]
            ]
        ]
    ]);
}
?>
</div>
