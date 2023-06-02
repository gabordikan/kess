<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Beállítások';

if (Yii::$app->user->isGuest) {
?>
    Lépjen be a funkciók eléréséhez
<?php
}
else {

    ?>
    <div class="site-recordkess">
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

            <?= $form->field($model, 'email')->textInput() ?>
            <?= $form->field($model, 'phone')->textInput() ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="site-recordkess">
        <?php $form = ActiveForm::begin([
            'id' => 'recordkess-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'col-form-label'],
                'inputOptions' => ['class' => 'col-lg-3 form-control'],
                'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
            ],
        ]); ?>

            <?= $form->field($model, 'oldpassword')->passwordInput() ?>
            <?= $form->field($model, 'newpassword')->passwordInput() ?>
            <?= $form->field($model, 'newpassword2')->passwordInput() ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
    <?php
}
?>

