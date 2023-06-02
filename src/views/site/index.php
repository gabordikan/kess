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
        'query' => Penztarca::find()
            ->where(['felhasznalo' => Yii::$app->user->id, 'torolt' => 0])
            ->orderBy(['nev' => SORT_ASC]),
    ]);

    echo GridView::widget([
        'showHeader' => false,
        'summary' => '',
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
                            return '/site/listkess?penztarca_id='.$model->id;
                        case "update":
                            return '/site/recordkess?penztarca_id='.$model->id;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],

            ],
        ],
        'dataProvider' => $dataProvider,
    ]);

    echo "<div><H1>Összesen: ".
        Yii::$app->formatter->asCurrency(
            Penztarca::getOsszEgyenleg(), 'HUF'
    )."</H1></div>";


    $dataProvider = new ActiveDataProvider([
        'query' => Kategoriak::find()
            ->where(['felhasznalo' => Yii::$app->user->id])
            ->groupBy('tipus'),
    ]);

    echo GridView::widget([
        'showHeader' => false,
        'summary' => '',
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

    echo "<div><H1>Összesen: ".
        Yii::$app->formatter->asCurrency(
            Terv::getTervSum('Bevétel', date('Y-m'), date('Y-m')) - Terv::getTervSum('Kiadás', date('Y-m'), date('Y-m')), 'HUF'
    )."</H1></div>";

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
            'labels' => Kategoriak::getFokategoriakLista(true), // Your labels
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
                'position' => 'top',
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

    $kategoriakKiadasList = Kategoriak::getKategoriakLista('Kiadás');
    $kategoriakKiadasSumTerv = Kategoriak::getSumTerv('Kiadás', $tol, $ig);
    $kategoriakKiadasSumTeny = Kategoriak::getSumTeny('Kiadás', $tol, $ig);

    foreach ($kategoriakKiadasList as $key=>$value) {
        if ($kategoriakKiadasSumTerv[$key] == 0 && $kategoriakKiadasSumTeny[$key] == 0) {
            unset($kategoriakKiadasList[$key]);
            unset($kategoriakKiadasSumTerv[$key]);
            unset($kategoriakKiadasSumTeny[$key]);
        }
    }

    $kategoriakKiadasList = array_values($kategoriakKiadasList);
    $kategoriakKiadasSumTerv = array_values($kategoriakKiadasSumTerv);
    $kategoriakKiadasSumTeny = array_values($kategoriakKiadasSumTeny);

    if (count($kategoriakKiadasList)>0) {

        echo ChartJs::widget([
            'type' => 'horizontalBar',
            'id' => 'structurePie2',
            'clientOptions' => [
                'responsive' => true,
                'aspectRatio' => 10/count($kategoriakKiadasList) < 0.7 ? 0.7 : 10/count($kategoriakKiadasList),
            ],
            'data' => [
                'labels' => $kategoriakKiadasList,
                'datasets' => [
                    [
                        'label' => "Terv",
                        'backgroundColor' => "rgba(255,10,0,0.5)",
                        'borderColor' => "rgba(255,10,0,1)",
                        'pointBackgroundColor' => "rgba(255,10,0,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(255,10,0,1)",
                        'data' => $kategoriakKiadasSumTerv,
                    ],
                    [
                        'label' => "Tény",
                        'backgroundColor' => "rgba(10,255,0,0.5)",
                        'borderColor' => "rgba(10,255,0,1)",
                        'pointBackgroundColor' => "rgba(10,255,0,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(10,255,0,1)",
                        'data' => $kategoriakKiadasSumTeny,
                    ]
                ]
            ]
        ]);
    }

    echo "</div><div>";

    $kategoriakBevetelList = Kategoriak::getKategoriakLista('Bevétel');
    $kategoriakBevetelSumTerv = Kategoriak::getSumTerv('Bevétel', $tol, $ig);
    $kategoriakBevetelSumTeny = Kategoriak::getSumTeny('Bevétel', $tol, $ig);

    foreach ($kategoriakBevetelList as $key=>$value) {
        if ($kategoriakBevetelSumTerv[$key] == 0 && $kategoriakBevetelSumTeny[$key] == 0) {
            unset($kategoriakBevetelList[$key]);
            unset($kategoriakBevetelSumTerv[$key]);
            unset($kategoriakBevetelSumTeny[$key]);
        }
    }

    $kategoriakBevetelList = array_values($kategoriakBevetelList);
    $kategoriakBevetelSumTerv = array_values($kategoriakBevetelSumTerv);
    $kategoriakBevetelSumTeny = array_values($kategoriakBevetelSumTeny);

    if(count($kategoriakBevetelList)>0) {

        echo ChartJs::widget([
            'type' => 'horizontalBar',
            'id' => 'structurePie3',
            'clientOptions' => [
                'responsive' => true,
                'aspectRatio' => 10/count($kategoriakBevetelList) < 0.7 ? 0.7 : 10/count($kategoriakBevetelList),
            ],
            'data' => [
                'labels' => $kategoriakBevetelList,
                'datasets' => [
                    [
                        'label' => "Terv",
                        'backgroundColor' => "rgba(255,10,0,0.5)",
                        'borderColor' => "rgba(255,10,0,1)",
                        'pointBackgroundColor' => "rgba(255,10,0,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(255,10,0,1)",
                        'data' => $kategoriakBevetelSumTerv,
                    ],
                    [
                        'label' => "Tény",
                        'backgroundColor' => "rgba(10,255,0,0.5)",
                        'borderColor' => "rgba(10,255,0,1)",
                        'pointBackgroundColor' => "rgba(10,255,0,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(10,255,0,1)",
                        'data' => $kategoriakBevetelSumTeny,
                    ]
                ]
            ]
        ]);
    }
}
?>
</div>
