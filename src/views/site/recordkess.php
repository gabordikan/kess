<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\jui\DatePicker;
use app\widgets\MyDatePicker;
use app\models\Kategoriak;
use app\models\Penztarca;

$this->title = 'Rögzítés';

if (Yii::$app->user->isGuest) {
?>
    Lépjen be a funkciók eléréséhez
<?php
}
else {

    $kategoriak = Kategoriak::getKategoriak($tipus == 1 ? 'Bevétel' : 'Kiadás');

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
                    [1 => 'Bevétel', -1 => 'Kiadás'],
                []) ?>

            <?= $form->field($model, 'kategoria_id')->dropDownList(
                    $kategoriak,
                []) ?>

            <div class="form-group">
                <div>
                <?php 
                    $most_used_categories = Kategoriak::getMostUsedKategoriak($tipus);
                    $cat_buttons = "";
                    foreach ($most_used_categories as $category) {
                        $cat_name = $category['nev']."(".$category['c'].")";
                        $cat_buttons .= " ".Html::button($cat_name, ['class' => 'btn btn-secondary mb-3', 'name' => 'category-button', 'value' => $category['id']]);
                    }
                    echo $cat_buttons;
                ?>
                </div>
            </div>

            <?= $form->field($model, 'osszeg')->textInput() ?>

            <div class="form-group">
                <div>
                    <?= Html::button('Töröl', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 0]) ?>
                    <?= Html::button('Plan', ['style'=>'display: none', 'class' => 'btn btn-success mb-3', 'name' => 'plan-button', 'value' => 0]) ?>
                    <?= Html::button('500', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 500]) ?>
                    <?= Html::button('1 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 1000]) ?>
                    <?= Html::button('2 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 2000]) ?> 
                    <?= Html::button('5 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 5000]) ?>
                    <?= Html::button('10 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 10000]) ?>
                    <?= Html::button('20 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 20000]) ?>
                    <?= Html::button('50 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 50000]) ?>
                    <?= Html::button('100 000', ['class' => 'btn btn-secondary mb-3', 'name' => 'amount-button', 'value' => 100000]) ?>
                </div>
            </div>
            <?= $form->field($model, 'megjegyzes')->textarea() ?>
            <br/>

            <div class="form-group">
                <div>
                    <?= Html::button('<i class="fa-solid fa-message"></i>', ['style'=>'', 'class' => 'btn btn-success mb-3', 'name' => 'comment-button', 'value' => 0]) ?>
                    
                    <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary mb-3', 'name' => 'save-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

    <script>

        var comment_button = document.getElementsByName('comment-button')[0];
        var comment_textarea = document.querySelector('#recordkess-form > div.mb-3.row.field-mozgas-megjegyzes');
        comment_textarea.style.display = 'none';

        comment_button.addEventListener('click', function(evt) {
            if (comment_textarea.style.display == '') {
                comment_textarea.style.display = 'none';
            } else {
                comment_textarea.style.display = '';
            }
        });

        var penztarca = document.getElementsByName('Mozgas[tipus]')[0];
        penztarca.addEventListener("change", function(evt) {
            var penztarca_id = document.getElementsByName('Mozgas[penztarca_id]')[0].value;
            window.location.href = '/site/recordkess?penztarca_id='+penztarca_id+'&tipus=' + evt.target.value <?php
                if ($update_id) {
                    echo '&update_id='.$update_id;
                }
            ?>;
        });

        var buttons = document.getElementsByName('amount-button');
        for (btn of buttons) {
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

        var planValues = 
<?php
    $planValues = [];

    foreach ($kategoriak as $tipusok) {
        foreach ($tipusok as $fokategoriak) {
            foreach ($fokategoriak as $id => $kategoria) {
                $planValues[$id] = 
                    Kategoriak::getKategoriaSumTerv($id, date('Y-m'), date('Y-m'))
                        - Kategoriak::getKategoriaSumTeny($id, date('Y-m-01'), date('Y-m-31')) < 0 
                    ? 0
                    : Kategoriak::getKategoriaSumTerv($id, date('Y-m'), date('Y-m'))
                        - Kategoriak::getKategoriaSumTeny($id, date('Y-m-01'), date('Y-m-31'));
            }
        }
    }
    echo json_encode($planValues);
?>;

        document.getElementsByName('Mozgas[kategoria_id]')[0].addEventListener("change",function(evt) {
            if (parseInt(planValues[evt.target.value]) != 0
                && !isNaN(parseInt(planValues[evt.target.value]))
                ) {
                document.getElementsByName('plan-button')[0].value = planValues[evt.target.value];
                document.getElementsByName('plan-button')[0].innerText = planValues[evt.target.value];
                document.getElementsByName('plan-button')[0].style.display = '';
            } else {
                document.getElementsByName('plan-button')[0].style.display = 'none';
                document.getElementsByName('plan-button')[0].innerText = '';
                document.getElementsByName('plan-button')[0].value = 0;
            }
        });

        document.getElementsByName('plan-button')[0].addEventListener("click", function(evt) {
                    var osszeg_selector = document.getElementsByName('Mozgas[osszeg]')[0];
                    osszeg = parseInt(evt.target.value);
                    osszeg_selector.value = osszeg;
        });


        document.getElementsByName('category-button').forEach(function(item) {
            item.addEventListener("click", function(evt) {
                    var category_selector = document.getElementsByName('Mozgas[kategoria_id]')[0];
                    console.log(evt.target.value);
                    category_selector.value = evt.target.value;
            });
        });
    </script>
    <?php
}
?>
