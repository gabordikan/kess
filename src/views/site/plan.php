<?php

/** @var yii\web\View $this */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\jui\DatePicker;

use app\models\Kategoriak;
use app\models\Terv;

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;


$this->title = 'Terv';

$kategoriak = Kategoriak::getKategoriak();
?>
<div class="site-index">
<?php
if (Yii::$app->user->isGuest) {
?>
    Lépjen be a funkciók eléréséhez
<?php
}
else {
?>
    <div class="site-recordplan">
    <?php $form = ActiveForm::begin([
        'action' => ['site/plan','update_id' => $model->id],
        'id' => 'recordplan-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'idoszak')->widget(DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM',
            'clientOptions' => [
                'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                    window.location = '/site/plan?update_id=' + update_id + '&idoszak='+dateText;
                    }"),
            ],
        ]) ?>

        <?= $form->field($model, 'kategoria_id')->dropDownList(
                $kategoriak,
            []) ?>

        <?= $form->field($model, 'osszeg')->textInput() ?>

        <div class="form-group">
            <div">
                <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                <?= Html::button('Előző időszak másolása', ['class' => 'btn btn-secondary', 'name' => 'copyplan-button']) ?>
            </div>
        </div>
        <BR/>

    <?php ActiveForm::end(); ?>
    </div>
    <div class="site-planlist">
    <?php $dataProvider = new ActiveDataProvider([
        'query' => Terv::find()
            ->joinWith('kategoria')
            ->where(
            [
                'terv.felhasznalo' => Yii::$app->user->id,
                'terv.torolt' => 0,
                'terv.idoszak' => $idoszak,
            ]
        )->orderBy(['tipus' => SORT_ASC, 'fokategoria' => SORT_ASC, 'nev' => SORT_ASC]),
        'pagination' => [
            'pageSize' => 100,
        ],
    ]);

    echo GridView::widget([
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'text-align: right'],
        'summary' => '{begin}-{end}, Összesen: {totalCount}',
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'idoszak_tipus',
                'format' => 'text',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'idoszak',
                'format' => 'text',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    $kategoria = Kategoriak::findOne([ 'id' => $model->kategoria_id]);
                    return $kategoria->tipus;
                },
                'format' => 'text',
                'label' => 'Típus',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    $kategoria = Kategoriak::findOne([ 'id' => $model->kategoria_id]);
                    return $kategoria->fokategoria."/".$kategoria->nev;
                },
                'format' => 'text',
                'label' => 'Kategória',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return $model->osszeg; 
                },
                'format' => ['currency', 'HUF'],
                'label' => 'Összeg',
                'contentOptions' => ['style'=>'text-align: right'],
                'footer' => 
                    Yii::$app->formatter->asCurrency(
                        Terv::getTervSum('Bevétel', $idoszak, $idoszak) - Terv::getTervSum('Kiadás', $idoszak, $idoszak), 'HUF'
                    ),
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
                            return '/site/plan?update_id='.$model->id;
                        case "delete":
                            return '/site/plan?delete_id='.$model->id;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
        'dataProvider' => $dataProvider,
    ]);
}
?>
</div>
<script>
    var idoszakselector = document.getElementsByName('Terv[idoszak]')[0];

    var update_id = '<?= $update_id ?>';

    idoszakselector.addEventListener('change', function (evt) {
        window.location = '/site/plan?update_id=' + update_id + '&idoszak='+evt.target.value;
    });

    idoszakselector.addEventListener('select', function (evt) {
        window.location = '/site/plan?update_id=' + update_id + '&idoszak='+evt.target.value;
    });

    document.getElementsByName('copyplan-button')[0].addEventListener('click', function() {
        window.location = "/site/copyplan?idoszak="+idoszakselector.value;
    });
</script>