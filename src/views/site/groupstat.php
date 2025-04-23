<?php

/** @var yii\web\View $this */

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Terv;

use gabordikan\datepicker\DixDatePicker;
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

    if (!$tol) {
        $tol = date("Y-m")."-01";
    }

    if (!$ig) {
        $ig = date("Y-m")."-31";
    }

    echo "<BR/>"
    .Html::beginForm('/site/groupstat', 'get')
    .Html::hiddenInput('tol', $tol, ['id' => 'tol'])
    .Html::hiddenInput('ig', $ig, ['id' => 'ig'])
    ."<div>"
    .Html::label('Csoportkód: ')
    ."</div><div>"
    .Html::textInput('csoport_kod', $csoport_kod, ['type' => 'number'])
    ."</div><div>"
    .Html::label('Dátumtól: ')
    .DixDatePicker::widget([
        'id' => 'tolselector',
        'interval' => 1,
        'value' => $tol,
        'language' => 'hu',
        'dateFormat' => 'yyyy-MM-dd',
        'onChange' => "function(evt) {
            document.getElementById('tol').value = $(evt.target).val();
            }",
        'clientOptions' => [
            'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                document.getElementById('tol').value = dateText;
                }"),
        ],
    ])
    ."Dátumig: "
    .DixDatePicker::widget([
        'id' => 'igselector',
        'interval' => 1,
        'value' => $ig,
        'language' => 'hu',
        'dateFormat' => 'yyyy-MM-dd',
        'onChange' => "function(evt) {
            document.getElementById('ig').value = $(evt.target).val();
            }",
        'clientOptions' => [
            'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                document.getElementById('ig').value = dateText;
                }"),
        ],
    ])
    .Html::submitButton('Frissít')
    .Html::endForm()
    ."</div>";

    echo "<BR><BR><div>";


    $devizak = Penztarca::getDevizak();

    foreach ($devizak as $deviza) {

        $dataProvider = new ActiveDataProvider([
            'query' => Kategoriak::find()
                ->where(['felhasznalo' => Yii::$app->user->id])
                ->andWhere(['csoport_kod' => $csoport_kod]),
        ]);

        echo GridView::widget([
            'showHeader' => true,
            'summary' => '',
            'columns' => [
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        return $model->tipus; 
                    },
                    'format' => 'text',
                    'label' => 'Típus',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        return $model->fokategoria; 
                    },
                    'format' => 'text',
                    'label' => 'Főkategória',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        return $model->nev; 
                    },
                    'format' => 'text',
                    'label' => 'Kategória',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) use ($deviza, $tol, $ig) {
                        return  ($model->tipus=='Bevétel' ? 1 : -1)*Kategoriak::getKategoriaSumTeny($model->id, $tol, $ig, $deviza);
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
            Kategoriak::getCsoportEgyenleg($csoport_kod, $tol, $ig, $deviza), $deviza
    )."</H3></div>";

        echo "</div>";
    }
}
?>

</div>