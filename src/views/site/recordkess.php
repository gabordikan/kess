<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\Kategoriak;

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

        <?= $form->field($model, 'penztarca')->dropDownList(
            array(
                'raiffeisen' => 'Raiffeisen Privát',
                'otp' => 'OTP',
                'raiffeisenceges' => 'Raiffeisen Céges',
                'kp' => 'Készpénz',
            ),
            ['autofocus' => true]) ?>

        <?= $form->field($model, 'tipus')->dropDownList(
            array(
                0 => 'Kiadás',
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
