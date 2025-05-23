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


$this->title = 'Kategóriák';

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
        'action' => ['site/categories','update_id' => $model->id],
        'id' => 'recordplan-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-form-label'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'tipus')->dropDownList(
                ['Bevétel' => 'Bevétel', 'Kiadás' => 'Kiadás'],
            []) ?>

        <?= $form->field($model, 'fokategoria')->dropDownList(
                Kategoriak::getFokategoriakLista(false, $tipus),
            []) ?>

        <?= $form->field($model, 'fokategoria_')->textInput() ?>

        <?= $form->field($model, 'nev')->textInput() ?>

        <?= $form->field($model, 'technikai')->checkbox() ?>

        <?= $form->field($model, 'csoport_kod')->textInput([
                                 'type' => 'number'
                            ]) ?>

        <div class="form-group">
            <div">
                <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
            </div>
        </div>
        <BR/>

    <?php ActiveForm::end(); ?>
    </div>
    <div class="site-planlist">
    <?php $dataProvider = new ActiveDataProvider([
        'query' => Kategoriak::find()
            ->where(
            [
                'felhasznalo' => Yii::$app->user->id,
                'torolt' => 0,
                'tipus' => $tipus,
            ]
        )->orderBy(['fokategoria' => SORT_ASC, 'nev' => SORT_ASC]),
        'pagination' => [
            'pageSize' => 100,
        ],
    ]);

    echo GridView::widget([
        'showFooter' => false,
        'summary' => '{begin}-{end}, Összesen: {totalCount}',
        'footerRowOptions'=>['style'=>'text-align: right'],
        'columns' => [
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'fokategoria',
                'format' => 'text',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'nev',
                'format' => 'text',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    $technikai = $model->technikai;
                    return $technikai==1 ? 'Igen' : '';
                },
                'format' => 'text',
                'label' => 'Technikai',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return $model->csoport_kod == 0 ? '' : $model->csoport_kod; 
                },
                'format' => 'text',
                'label' => 'Csoportkód',
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
                            return '/site/categories?update_id='.$model->id;
                        case "delete":
                            return '/site/categories?delete_id='.$model->id;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
        'dataProvider' => $dataProvider,
    ]);
    ?>
    </div>
    <script>
        var penztarca = document.getElementsByName('Kategoriak[tipus]')[0];
        penztarca.addEventListener("change", function(evt) {
            var penztarca_id = document.getElementsByName('Kategoriak[tipus]')[0].value;
            window.location.href = '/site/categories?&tipus=' + evt.target.value;
        });
    </script>
    <?php
}
?>
