<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\jui\DatePicker;
use app\models\Kategoriak;
use app\models\Penztarca;

$this->title = 'Rögzítés';
$this->params['breadcrumbs'][] = $this->title;

$kategoriak = Kategoriak::getKategoriak();

?>
<div class="site-recordkess">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'recordkess-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'datum')->widget(DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>

        <?= $form->field($model, 'penztarca_id')->dropDownList(
            Penztarca::getPenztarcak(),
            ['autofocus' => true]) ?>

        <?= $form->field($model, 'tipus')->dropDownList(
            array(
                -1 => 'Kiadás',
                1 => 'Bevétel',
            ),
            []) ?>

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
