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
use app\models\Mozgas;
use yii\data\ActiveDataProvider;

use yii\grid\GridView;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;
use kartik\select2\Select2;
use yii\web\JsExpression;

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
            'layout' => 'inline',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                'inputOptions' => ['class' => 'col-lg-3 form-control'],
                'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
            ],
        ]); ?>

            <?= Html::hiddenInput('from_list', $from_list) ?>

            <?= $form->field($model, 'datum')->widget(MyDatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
                'onChange' => "function(evt) {
                    }",
                'options' => [
                    'style' => 'width: 120px;'
                ],
            ]) ?>

            <?= //$form->field($model, 'penztarca_id')->dropDownList(
                //Penztarca::getPenztarcak(),
                //['autofocus' => true]) 
                ''?>

            <?php
                $penztarcak = Penztarca::getPenztarcak();
                $model->penztarca_id = $penztarca_id ?? array_key_first($penztarcak);
                foreach ($penztarcak as $key => $penztarca) {
                    $penztarcak[$key] = Penztarca::getLogo($penztarca).$penztarca;
                }
            ?>
            <?= $form->field($model, 'penztarca_id')->widget(Select2::classname(),[
                'data' => $penztarcak,
                'value' => $model->penztarca_id,
                'hideSearch' => true,
                'options' => [
                ],
                'pluginOptions' => [
                    'theme' => Select2::THEME_KRAJEE,
                    'width' => '100%;',
                    'allowClear' => false,
                    //'minimumInputLength' => 1,
                    //'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function (state) {if (!state.id) return state.text; // optgroup
                        return state.text;}'),
                    'escapeMarkup' => new JsExpression('function (m) {return m;}'),
                    //'templateSelection' => new JsExpression('function () {}'),
                ],
                'pluginEvents' => [
                    'change' => "function(evt) {
                        var penztarca_id = document.getElementsByName('Mozgas[penztarca_id]')[0].value;
                        var tipus = document.getElementsByName('Mozgas[tipus]')[0].value;
                        window.location.href = '/site/recordkess?penztarca_id='+penztarca_id+'&tipus=' + tipus" . ($update_id != null ? "+'&update_id=".$update_id."'" : "") . "}",
                ],
            ]) ?>

            <?= $form->field($model, 'tipus')->widget(Select2::classname(),[
                'data' => [1 => "<i class='fa-solid fa-arrow-left'></i>&nbsp;Bevétel", -1 => "<i class='fa-solid fa-arrow-right'>&nbsp;</i>Kiadás"],
                'value' => $model->tipus,
                'hideSearch' => true,
                'pluginOptions' => [
                    'escapeMarkup' => new JsExpression('function (m) {return m;}'),

                ],
                'pluginEvents' => [
                    'change' => "function(evt) {
                        var penztarca_id = document.getElementsByName('Mozgas[penztarca_id]')[0].value;
                        var tipus = document.getElementsByName('Mozgas[tipus]')[0].value;
                        window.location.href = '/site/recordkess?penztarca_id='+penztarca_id+'&tipus=' + tipus" . ($update_id != null ? "+'&update_id=".$update_id."'" : "") . "}",
                ],
            ]) ?>

            <?php 
                //var_dump($kategoriak); die();
            ?>

            <?= Html::label("Kategória","Mozgas[kategoria_id]", ['class' => 'col-lg-1 col-form-label mr-lg-3'])
            .Select2::widget([
                'name' => 'Mozgas[kategoria_id]',
                'data' => $kategoriak,
                'value' => $model->kategoria_id,
                'pluginEvents' => [
                    'change' => "function(evt) {
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
                    }",
                ],
            ], ['class' => 'col-lg-3 form-control'])
            .Html::error($model, 'kategoria_id', ['class' => 'col-lg-7 invalid-feedback'])."<BR/>" ?>

            <div class="form-group">
                <div>
                <?php 
                    $most_used_categories = Kategoriak::getMostUsedKategoriak($tipus, $model->penztarca_id);
                    $cat_buttons = "";
                    foreach ($most_used_categories as $category) {
                        $cat_name = $category['nev'];
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
<?php
    if($model->penztarca_id != null) {
        $idoszak = substr($model->datum, 0 ,7);
        $deviza = Penztarca::findOne($model->penztarca_id)->deviza;

        $dataProvider = new ActiveDataProvider([
            'query' => Mozgas::find()
            ->joinWith('kategoriak')
            ->andWhere(
                [
                    'mozgas.felhasznalo' => Yii::$app->user->id,
                    'mozgas.torolt' => 0,
                    'mozgas.penztarca_id' => $model->penztarca_id,
                ]
            )
            ->andWhere(['>=','mozgas.datum',$idoszak.'-01'])
            ->andWhere(['<=','mozgas.datum',$idoszak.'-31'])
            ->orderBy(['mozgas.datum' => SORT_DESC, 'mozgas.id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        echo GridView::widget([
            'showFooter' => true,
            'footerRowOptions' => ['style' => 'text-align: right;'],
            'summary' => '{begin}-{end}, Összesen: {totalCount}',
            'columns' => [
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        $kategoria = Kategoriak::findOne([ 'id' => $model->kategoria_id]);
                        return $model->datum.' <BR><b>'.$kategoria->fokategoria."/".$kategoria->nev.'</B><BR><i>'.$model->megjegyzes.'</i>'; 
                    },
                    'format' => 'raw',
                    'label' => 'Tétel',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        return $model->tipus * $model->osszeg; 
                    },
                    'format' => ['currency', $deviza],
                    'label' => 'Összeg',
                    'contentOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
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
                                return '/site/recordkess?update_id='.$model->id;
                            case "delete":
                                return '/site/recordkess?delete_id='.$model->id;
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

        var comment_button = document.getElementsByName('comment-button')[0];
        var comment_textarea = document.querySelector('#recordkess-form > div.mb-3.field-mozgas-megjegyzes');
        comment_textarea.style.display = 'none';

        comment_button.addEventListener('click', function(evt) {
            if (comment_textarea.style.display == '') {
                comment_textarea.style.display = 'none';
            } else {
                comment_textarea.style.display = '';
            }
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

    foreach ($kategoriak as $fokategoriak) {
        foreach ($fokategoriak as $id => $kategoria) {
            $planValues[$id] = 
                Kategoriak::getKategoriaSumTerv($id, date('Y-m'), date('Y-m'))
                    - Kategoriak::getKategoriaSumTeny($id, date('Y-m-01'), date('Y-m-31')) < 0 
                ? 0
                : Kategoriak::getKategoriaSumTerv($id, date('Y-m'), date('Y-m'))
                    - Kategoriak::getKategoriaSumTeny($id, date('Y-m-01'), date('Y-m-31'));
        }
    }
    echo json_encode($planValues);
?>;

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
                    category_selector.dispatchEvent(new Event('change'));
            });
        });
    </script>
    <?php
}
?>
