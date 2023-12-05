<?php

/** @var yii\web\View $this */

use app\models\Penztarca;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;
use yii\bootstrap5\Tabs;

use app\widgets\MyDatePicker;
use yii\helpers\Html;

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

    if (!$idoszak) {
        $idoszak = date('Y-m');
    }

    $tol = $idoszak.'-01';
    $ig = $idoszak.'-31';

    echo "<div style='width: 300px'><H1><i class='fa-solid fa-bars'>&nbsp;</i>Egyenleg</H1></div>";
        
    $dataProvider = new ActiveDataProvider([
        'query' => Penztarca::find()
            ->where(['felhasznalo' => Yii::$app->user->id, 'torolt' => 0])
            ->orderBy(['nev' => SORT_ASC]),
    ]);

    echo GridView::widget([
        'showHeader' => false,
        'summary' => '',
        'columns' => [
            //['class' => SerialColumn::class],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return Penztarca::getLogo($model->nev).$model->nev;
                },
                'format' => 'raw',
                'label' => 'Név',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return Yii::$app->formatter->asCurrency(Penztarca::getEgyenleg($model->id), $model->deviza); 
                },
                'format' => 'raw',
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
                            return '/site/listkess?search[1]='.$model->nev;
                        case "update":
                            return '/site/recordkess?penztarca_id='.$model->id;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],

            ],
        ],
        'dataProvider' => $dataProvider,
    ]);

    $devizak = Penztarca::getDevizaList();

    foreach ($devizak as $deviza) {

        echo "<div><H3>Összesen: ".
            Yii::$app->formatter->asCurrency(
                Penztarca::getOsszEgyenleg($deviza->deviza), $deviza->deviza
        )."</H3></div>";

    }

    echo "<BR/><div>"
    .Html::label('Statisztika időszak: ')
    ."&nbsp;"
    .MyDatePicker::widget([
        'id' => 'idoszakselector',
        'interval' => 30,
        'value' => $idoszak,
        'language' => 'hu',
        'dateFormat' => 'yyyy-MM',
        'onChange' => "function(evt) {
            window.location = '/site/index?idoszak='+$(evt.target).val();
            }",
        'clientOptions' => [
            'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                window.location = '/site/index?idoszak='+dateText;
                }"),
        ],
    ])
    ."</div>";

    $items = [];

    foreach ($devizak as $deviza) {

        $items[] = [
            'label' => $deviza->deviza,
            'content' => $this->render('index_stats', [
                'idoszak' => $idoszak,
                'tol' => $tol,
                'ig' => $ig,
                'deviza' => $deviza->deviza,
            ]),
            'active' => ($deviza->deviza == 'HUF'),
        ];
    }

    echo Tabs::widget([
        'items' => $items
    ]);
}
?>

</div>