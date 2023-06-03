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

if (Yii::$app->user->isGuest) {
?>
    Lépjen be a funkciók eléréséhez
<?php
}
else {

    $kategoriak = Kategoriak::getKategoriak($tipus);

    ?>
    <div class="site-recordkess">
        <?php $form = ActiveForm::begin([
            'action' => ['site/recordkess','update_id' => $model->id],
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
                    ['Bevétel' => 'Bevétel', 'Kiadás' => 'Kiadás'],
                []) ?>

            <?= $form->field($model, 'kategoria_id')->dropDownList(
                    $kategoriak,
                []) ?>

            <?= $form->field($model, 'osszeg')->textInput() ?>

            <div class="form-group">
                <div>
                    <?= Html::button('Töröl', ['class' => 'btn btn-secondary', 'name' => '1000-button', 'value' => 0]) ?> 
                    <?= Html::button('500', ['class' => 'btn btn-secondary', 'name' => '1000-button', 'value' => 500]) ?>
                    <?= Html::button('1 000', ['class' => 'btn btn-secondary', 'name' => '1000-button', 'value' => 1000]) ?>
                    <?= Html::button('2 000', ['class' => 'btn btn-secondary', 'name' => '1000-button', 'value' => 2000]) ?> 
                    <?= Html::button('5 000', ['class' => 'btn btn-secondary', 'name' => '5000-button', 'value' => 5000]) ?>
            </div>
            <div>
                    <?= Html::button('10 000', ['class' => 'btn btn-secondary', 'name' => '10000-button', 'value' => 10000]) ?>
                    <?= Html::button('20 000', ['class' => 'btn btn-secondary', 'name' => '10000-button', 'value' => 20000]) ?>
                    <?= Html::button('50 000', ['class' => 'btn btn-secondary', 'name' => '50000-button', 'value' => 50000]) ?>
                    <?= Html::button('100 000', ['class' => 'btn btn-secondary', 'name' => '100000-button', 'value' => 100000]) ?>
                    <?php
                        echo Html::button('Plan', ['style'=>'display: none', 'class' => 'btn btn-secondary', 'name' => 'plan-button', 'value' => 0]);
                    ?>
                </div>
            </div>

            <br/><br/>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

    <script>
        var penztarca = document.getElementsByName('Mozgas[tipus]')[0];
        penztarca.addEventListener("change", function(evt) {
            var penztarca_id = document.getElementsByName('Mozgas[penztarca_id]')[0].value;
            window.location.href = '/site/recordkess?penztarca_id='+penztarca_id+'&tipus=' + evt.target.value;
        });

        var buttons = document.getElementsByClassName('btn btn-secondary');
        for (btn of buttons) {
            if(btn.name != 'plan-button') {
                btn.addEventListener("click", function(evt) {
                    var osszeg_selector = document.getElementsByName('Mozgas[osszeg]')[0];
                    if (parseInt(evt.target.value) == 0) {
                        osszeg_selector.value = 0;
                    } else {
                        var osszeg = osszeg_selector.value;
                        if (osszeg == "") {
                            osszeg = 0;
                        } else {
                            osszeg = parseInt(osszeg);
                        }
                        osszeg += parseInt(evt.target.value);
                        osszeg_selector.value = osszeg;
                    }
                });
            }
        }

        var planValues = {
            63:134652,
        };

        document.getElementsByName('Mozgas[kategoria_id]')[0].addEventListener("change",function(evt) {
            console.log(parseInt(planValues[evt.target.value]));
            if (parseInt(planValues[evt.target.value]) != 0
                && !isNaN(parseInt(planValues[evt.target.value]))
                ) {
                document.getElementsByName('plan-button')[0].value = planValues[evt.target.value];
                document.getElementsByName('plan-button')[0].text = planValues[evt.target.value];
                document.getElementsByName('plan-button')[0].style.display = '';
            } else {
                console.log('Hide');
                document.getElementsByName('plan-button')[0].style.display = 'none';
                document.getElementsByName('plan-button')[0].text = '';
                document.getElementsByName('plan-button')[0].value = 0;
            }
        });

        document.getElementsByName('plan-button')[0].addEventListener("click", function(evt) {
                    var osszeg_selector = document.getElementsByName('Mozgas[osszeg]')[0];
                    var osszeg = osszeg_selector.value;
                    if (osszeg == "") {
                        osszeg = 0;
                    } else {
                        osszeg = parseInt(osszeg);
                    }
                    osszeg += parseInt(evt.target.value);
                    osszeg_selector.value = osszeg;
                });
    </script>
    <?php
}
?>
