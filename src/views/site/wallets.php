<?php

/** @var yii\web\View $this */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\jui\DatePicker;

use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Terv;

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;


$this->title = 'Pénztárcák';
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
        'action' => ['site/wallets','update_id' => $model->id],
        'id' => 'recordplan-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'nev')->textInput() ?>
        <?= $form->field($model, 'deviza')->textInput(['maxlength' => '3']) ?>
        <?= $form->field($model, 'megtakaritas')->checkbox(['maxlength' => '3']) ?>

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
        'query' => Penztarca::find()
            ->where(
            [
                'felhasznalo' => Yii::$app->user->id,
            ]
        )->orderBy(['torolt' => SORT_ASC, 'nev' => SORT_ASC]),
        'pagination' => [
            'pageSize' => 100,
        ],
    ]);

    echo GridView::widget([
        'showFooter' => false,
        'footerRowOptions'=>['style'=>'text-align: right'],
        'summary' => '{begin}-{end}, Összesen: {totalCount}',
        'columns' => [
            [
                'class' => DataColumn::class,
                'value' => function ($model, $key, $index, $column) {
                    return Penztarca::getLogo($model->nev).$model->nev;
                },
                'format' => 'raw',
                'label' => 'Név',
            ],
            [
                'class' => DataColumn::class,
                'attribute' => 'deviza',
                'format' => 'text',
            ],
            [
                'class' => DataColumn::class,
                'value' => function ($model, $key, $index, $column) {
                    return $model->megtakaritas ? 'Igen' : ''; 
                },
                'format' => 'text',
                'label' => 'Megtakarítás'
            ],
            [
                'class' => DataColumn::class,
                'value' => function ($model, $key, $index, $column) {
                    return $model->torolt ? 'Igen' : ''; 
                },
                'format' => 'text',
                'label' => 'Törölt'
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
                            return '/site/wallets?update_id='.$model->id;
                        case "delete":
                            return '/site/wallets?delete_id='.$model->id;
                    }

                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
        'dataProvider' => $dataProvider,
    ]);
    ?>
    </div>
    <?php
}
?>
