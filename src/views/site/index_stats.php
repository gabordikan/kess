<?php 

/** @var yii\web\View $this */

use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Terv;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;

use app\models\ChartJs;


    echo "<BR><H1><i class='fa-solid fa-arrow-right-arrow-left'>&nbsp;</i>Bevétel/Kiadás</H1>";

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
                'value' => function ($model, $key, $index, $column) use ($deviza) {
                    $idoszak = empty(Yii::$app->request->get('idoszak'))? date('Y-m') : Yii::$app->request->get('idoszak');
                    return Terv::getTenySum($model->tipus, $idoszak.'-01', $idoszak.'-31', $deviza);
                },
                'format' => ['currency',$deviza],
                'label' => 'Terv',
                'contentOptions' => ['style'=>'text-align: right'],
            ],
        ],
        'dataProvider' => $dataProvider,
    ]);

    echo "<div><H3>Összesen: ".
        Yii::$app->formatter->asCurrency(
            Terv::getTenySum('Bevétel', $tol, $ig, $deviza) - Terv::getTenySum('Kiadás', $tol, $ig, $deviza), $deviza
    )."</H3></div>";

    $bevetelData = Kategoriak::getFokategoriakListaEgyenleg($tol, $ig, 1, $deviza);
    $kiadasData = Kategoriak::getFokategoriakListaEgyenleg($tol, $ig, -1, $deviza);

    $vanAdat = false;

    foreach ($bevetelData as $adat) {
        if ($adat != 0) {
            $vanAdat = true;
        }
    }

    foreach ($kiadasData as $adat) {
        if ($adat != 0) {
            $vanAdat = true;
        }
    }

    if ($vanAdat) {

        echo "<div style='max-width: 600px; margin: auto;'>";

        echo ChartJs::widget([
            'type' => 'doughnut',
            'id' => 'structurePie'.$deviza,
            'options' => [
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
                'maintainAspectRatio' => true,
                'responsive' => true,
                'aspectRatio' => 1,

            ],
            'data' => [
                'radius' =>  "90%",
                'labels' => Kategoriak::getFokategoriakLista(true, null), // Your labels
                'datasets' => [
                    [
                        'data' => $bevetelData, // Your dataset
                        'label' => '',
                        'backgroundColor' => Kategoriak::getFokategoriakSzinek(),
                        'borderColor' => '#FFFFFF',
                        'borderWidth' => 1,
                        'hoverBorderColor'=>"#999",
                    ],
                    [
                        'data' => $kiadasData, // Your dataset
                        'label' => '',
                        'backgroundColor' => Kategoriak::getFokategoriakSzinek(),
                        'borderColor' =>  '#fff',
                        'borderWidth' => 1,
                        'hoverBorderColor'=>"#999",
                    ]
                ]
            ],
        ], []);

        echo "</div>";
    } else {
        echo "<p>Nincs adat";
    }

    echo "<div>";

    if (date('Y-m') == $idoszak) {

        echo "<BR><H1><i class='fa-solid fa-chart-line'>&nbsp;</i>Prognózis</H1>";

        $tervezettbevetel = 0;

        $kategoriakBevetelSumTerv = Kategoriak::getSumTerv('Bevétel', $tol, $ig, $deviza);
        $kategoriakBevetelSumTeny = Kategoriak::getSumTeny('Bevétel', $tol, $ig, $deviza);

        foreach( array_values($kategoriakBevetelSumTerv) as $index => $value) {
            $kat_egyenleg = $value - array_values($kategoriakBevetelSumTeny)[$index];
            if ($kat_egyenleg < 0) $kat_egyenleg = 0;
            $tervezettbevetel += $kat_egyenleg;
        }

        $tervezettkiadas = 0;

        $kategoriakKiadasSumTerv = Kategoriak::getSumTerv('Kiadás', $tol, $ig, $deviza);
        $kategoriakKiadasSumTeny = Kategoriak::getSumTeny('Kiadás', $tol, $ig, $deviza);

        foreach( array_values($kategoriakKiadasSumTerv) as $index => $value) {
            $kat_egyenleg = $value - array_values($kategoriakKiadasSumTeny)[$index];
            if ($kat_egyenleg < 0) $kat_egyenleg = 0;
            $tervezettkiadas += $kat_egyenleg;
        }

        $tervezettegyenleg = Penztarca::getOsszEgyenleg($deviza) + $tervezettbevetel - $tervezettkiadas;

        $dataProvider = new ArrayDataProvider([
            'allModels' => [
                [
                    'tipus' => 'Jelenlegi egyenleg',
                    'osszeg' => Penztarca::getOsszEgyenleg($deviza),
                ],
                [
                    'tipus' => 'Bevétel',
                    'osszeg' => $tervezettbevetel,
                ],
                [
                    'tipus' => 'Kiadás',
                    'osszeg' => $tervezettkiadas,

                ],
                [
                    'tipus' => 'Várható egyenleg',
                    'osszeg' => $tervezettegyenleg,

                ],
            ],
        ]);

        echo GridView::widget([
            'showHeader' => false,
            'id' => 'planChart'.$deviza,
            'summary' => '',
            'columns' => [
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        return $model['tipus']; 
                    },
                    'format' => 'text',
                    'label' => '',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) use ($deviza) {
                        return $model['osszeg'];
                    },
                    'format' => ['currency',$deviza],
                    'label' => 'Terv',
                    'contentOptions' => ['style'=>'text-align: right'],
                ],
            ],
            'dataProvider' => $dataProvider,
        ]);

    } else {

        echo "<BR><H1><i class='fa-solid fa-pen-to-square'>&nbsp;</i>Terv</H1>";

        $dataProvider = new ActiveDataProvider([
            'query' => Kategoriak::find()
                ->where(['felhasznalo' => Yii::$app->user->id])
                ->groupBy('tipus'),
        ]);

        echo GridView::widget([
            'showHeader' => false,
            'id' => 'planChart'.$deviza,
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
                    'value' => function ($model, $key, $index, $column) use ($deviza) {
                        $idoszak = empty(Yii::$app->request->get('idoszak'))? date('Y-m') : Yii::$app->request->get('idoszak');
                        return Terv::getTervSum($model->tipus, $idoszak, $idoszak, $deviza);
                    },
                    'format' => ['currency',$deviza],
                    'label' => 'Terv',
                    'contentOptions' => ['style'=>'text-align: right'],
                ],
            ],
            'dataProvider' => $dataProvider,
        ]);

        echo "<div><H3>Összesen: ".
            Yii::$app->formatter->asCurrency(
                Terv::getTervSum('Bevétel', $idoszak, $idoszak, $deviza) - Terv::getTervSum('Kiadás', $idoszak, $idoszak, $deviza), $deviza
        )."</H3></div>";
    }
    echo "</div<div>";

    echo "<BR><H1><i class='fa-solid fa-arrow-left'>&nbsp;</i>Terv/Tény (Bevétel)</H1>";

    $kategoriakBevetelList = Kategoriak::getKategoriakLista('Bevétel');
    $kategoriakBevetelSumTerv = Kategoriak::getSumTerv('Bevétel', $tol, $ig, $deviza);
    $kategoriakBevetelSumTeny = Kategoriak::getSumTeny('Bevétel', $tol, $ig, $deviza);

    foreach ($kategoriakBevetelList as $key=>$value) {
        if ($kategoriakBevetelSumTerv[$key] == 0 && $kategoriakBevetelSumTeny[$key] == 0) {
            unset($kategoriakBevetelList[$key]);
            unset($kategoriakBevetelSumTerv[$key]);
            unset($kategoriakBevetelSumTeny[$key]);
        }

        if ($kategoriakBevetelSumTerv[$key] == $kategoriakBevetelSumTeny[$key]) {
            unset($kategoriakBevetelList[$key]);
            unset($kategoriakBevetelSumTerv[$key]);
            unset($kategoriakBevetelSumTeny[$key]);
        }
    }

    $kategoriakBevetelList = array_values($kategoriakBevetelList);
    $kategoriakBevetelSumTerv = array_values($kategoriakBevetelSumTerv);
    $kategoriakBevetelSumTeny = array_values($kategoriakBevetelSumTeny);

    if(count($kategoriakBevetelList)>0) {

        echo "<div style='max-width: 1200px; margin: auto;'>";

        echo ChartJs::widget([
            'type' => 'bar',
            'id' => 'structurePie3'.$deviza,
	        'options' => [
                'style' => 'width: 90vw; max-width:1200px; height: '.(80+20*count($kategoriakBevetelList)).'px',
            ],
            'clientOptions' => [
                'indexAxis' => 'y',
		        'responsive' => false,
		        'maitainAspectRatio' => false,
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

        echo "</div>";
    } else {
        echo "<p>Nincs adat";
    }

    echo "</div><div>";

    echo "<BR><H1><i class='fa-solid fa-arrow-right'>&nbsp;</i>Terv/Tény (Kiadás)</H1>";

    $kategoriakKiadasList = Kategoriak::getKategoriakLista('Kiadás');
    $kategoriakKiadasSumTerv = Kategoriak::getSumTerv('Kiadás', $tol, $ig, $deviza);
    $kategoriakKiadasSumTeny = Kategoriak::getSumTeny('Kiadás', $tol, $ig, $deviza);

    foreach ($kategoriakKiadasList as $key=>$value) {
        if ($kategoriakKiadasSumTerv[$key] == 0 && $kategoriakKiadasSumTeny[$key] == 0) {
            unset($kategoriakKiadasList[$key]);
            unset($kategoriakKiadasSumTerv[$key]);
            unset($kategoriakKiadasSumTeny[$key]);
        }

        if ($kategoriakKiadasSumTerv[$key] == $kategoriakKiadasSumTeny[$key]) {
            unset($kategoriakKiadasList[$key]);
            unset($kategoriakKiadasSumTerv[$key]);
            unset($kategoriakKiadasSumTeny[$key]);
        }
    }

    $kategoriakKiadasList = array_values($kategoriakKiadasList);
    $kategoriakKiadasSumTerv = array_values($kategoriakKiadasSumTerv);
    $kategoriakKiadasSumTeny = array_values($kategoriakKiadasSumTeny);

    if (count($kategoriakKiadasList)>0) {

        echo "<div style='max-width: 1200px; margin: auto;'>";

        echo ChartJs::widget([
            'type' => 'bar',
            'id' => 'structurePie2'.$deviza,
            'options' => [
                'style' => 'width: 90vw; max-width:1200px; height: '.(80+20*count($kategoriakKiadasList)).'px',
            ],
            'clientOptions' => [
                'indexAxis' => 'y',
                'responsive' => false,
                'maintainAspectRatio' => false,
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

        echo "</div>";
    } else {
        echo "<p>Nincs adat";
    }
    echo "</div>";
?>
