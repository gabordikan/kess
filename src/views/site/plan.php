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


$this->title = 'Kess';

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
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
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
        ]) ?>

        <?= $form->field($model, 'kategoria_id')->dropDownList(
                $kategoriak,
            []) ?>

        <?= $form->field($model, 'osszeg')->textInput() ?>

        <div class="form-group">
            <div class="offset-lg-1 col-lg-11">
                <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
    </div>
    <div class="site-planlist">
    <?php $dataProvider = new ActiveDataProvider([
        'query' => Terv::find()->where(
            [
                'felhasznalo' => Yii::$app->user->id,
                'torolt' => 0,
                'idoszak' => date('Y-m'),
            ]
        )->orderBy(['id' => SORT_DESC]),
        'pagination' => [
            'pageSize' => 100,
        ],
    ]);

    echo GridView::widget([
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
                    return Kategoriak::findOne([ 'id' => $model->kategoria_id])->nev; 
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
            ],
            [
                'class' => ActionColumn::class,
                'visibleButtons' => [
                    'view' => false,
                    'update' => false,
                    'delete' => true,
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return 'index.php?r=site%2Fplan&delete_id='.$model->id;
                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
        'dataProvider' => $dataProvider,
    ]);
}
?>
</div>