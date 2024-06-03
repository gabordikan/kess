<?php

/** @var yii\web\View $this */

use app\models\Kategoriak;
use app\models\Terv;
use app\models\Penztarca;
use app\models\TervSearch;
use app\widgets\MyDatePicker;
use gabordikan\cor4\datatables\DataTables;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;
use kartik\select2\Select2;

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Terv';

$kategoriak = Kategoriak::getKategoriak();

if (empty($idoszak)) {
    $idoszak = date('Y-m');
}

if (empty($deviza)) {
    $deviza = 'HUF';
}
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
        'action' => ['site/plan','update_id' => $model->id, 'idoszak' => $idoszak],
        'id' => 'recordplan-form',
        'layout' => 'inline',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'idoszak')->widget(MyDatePicker::classname(), [
            'options' => [
                'style' => 'width: 120px',
            ],
            'interval' => 30,
            'onChange' => "function (evt) {
                window.location = '/site/plan?update_id=' + update_id + '&idoszak='+$(evt.target).val();
            }",
            'dateFormat' => 'yyyy-MM',
            'clientOptions' => [
                'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                    window.location = '/site/plan?update_id=' + update_id + '&idoszak='+dateText;
                    }"),
            ],
        ]) ?>
        
        <?= $form->field($model, 'deviza')->dropDownList(
                Penztarca::getDevizak(),
            []) ?>

        <?= Html::label("Kategória","Terv[kategoria_id]", ['class' => 'col-lg-1 col-form-label mr-lg-3'])
            .Select2::widget([
                'name' => 'Terv[kategoria_id]',
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

        <?= $form->field($model, 'osszeg')->textInput() ?>

        <div class="form-group">
            <div">
                <?= Html::button('Plan', ['style'=>'display: none', 'class' => 'btn btn-success mb-3', 'name' => 'plan-button', 'value' => 0]) ?>                
                <?= Html::submitButton('Mentés', ['class' => 'btn btn-primary mb-3', 'name' => 'save-button']) ?>
                <?= Html::button('Előző időszak másolása', ['class' => 'btn btn-secondary mb-3', 'name' => 'copyplan-button']) ?>
            </div>
        </div>
        <BR/>

    <?php ActiveForm::end(); ?>
    </div>
    <div class="site-planlist">
    <?php 
    $searchModel = new TervSearch('plan');
    $searchModel->_idoszak = $idoszak;
    $searchModel->_deviza = $deviza;
    $dataProvider = $searchModel->search();

    echo DataTables::widget([
        'clientOptions' => [
            'prefix' => 'plan',
            'order' => [
                [1, 'asc'],
                [2, 'asc'],
                [3, 'asc'],
            ],
        ],
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'text-align: right'],
        'summary' => '{begin}-{end}, Összesen: {totalCount}',
        'columns' => [
            /*[
                'class' => DataColumn::class, // this line is optional
                'attribute' => 'idoszak_tipus',
                'format' => 'text',
            ],*/
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    return $model->idoszak;
                },
                'format' => 'text',
                'label' => 'Időszak',
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
                    return $kategoria->fokategoria;
                },
                'format' => 'text',
                'label' => 'Főkategória',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    $kategoria = Kategoriak::findOne([ 'id' => $model->kategoria_id]);
                    return $kategoria->nev;
                },
                'format' => 'text',
                'label' => 'Kategória',
            ],
            [
                'class' => DataColumn::class, // this line is optional
                'value' => function ($model, $key, $index, $column) {
                    $kategoria = Kategoriak::findOne([ 'id' => $model->kategoria_id]);
                    return $model->osszeg * ($kategoria->tipus == 'Bevétel' ? 1 : -1); 
                },
                'format' => ['currency', $deviza],
                'label' => 'Összeg',
                'contentOptions' => ['style'=>'text-align: right'],
                'footer' => 
                    Yii::$app->formatter->asCurrency(
                        Terv::getTervSum('Bevétel', $idoszak, $idoszak, $deviza) - Terv::getTervSum('Kiadás', $idoszak, $idoszak, $deviza), $deviza
                    ),
                'footerOptions' => ['style'=>'text-align: right; white-space: nowrap !important'],
            ],
            [
                'class' => ActionColumn::class,
                'visibleButtons' => [
                    'view' => false,
                    'update' => true,
                    'delete' => true,
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    $idoszak = empty(Yii::$app->request->get('idoszak'))? date('Y-m') : Yii::$app->request->get('idoszak');
                    switch ($action) {
                        case "update":
                            return '/site/plan?update_id='.$model->id.'&idoszak='.$idoszak;
                        case "delete":
                            return '/site/plan?delete_id='.$model->id.'&idoszak='.$idoszak;
                    }
                },
                'contentOptions' => ['style'=>'text-align: center'],
            ],
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    ]);
}
?>
</div>
<script>
    var idoszakselector = document.getElementsByName('Terv[idoszak]')[0];

    var idoszak = '<?= $idoszak ?>';
    var update_id = '<?= $update_id ?>';
    var deviza = '<?= $deviza ?? 'HUF' ?>';

    idoszakselector.addEventListener('change', function (evt) {
        window.location = '/site/plan?deviza=' + deviza + '&update_id=' + update_id + '&idoszak=' + evt.target.value;
    });

    idoszakselector.addEventListener('select', function (evt) {
        window.location = '/site/plan?deviza=' + deviza + '&update_id=' + update_id + '&idoszak=' + evt.target.value;
    });

    document.getElementsByName('copyplan-button')[0].addEventListener('click', function() {
        window.location = '/site/copyplan?deviza=' + deviza + '&idoszak=' + idoszakselector.value;
    });

    var devizaselector = document.getElementsByName('Terv[deviza]')[0];

    devizaselector.addEventListener('change', function (evt) {
        window.location = '/site/plan?deviza=' + evt.target.value + '&update_id=' + update_id + '&idoszak=' + idoszak ;
    });

    var planValues = 
<?php
    $planValues = [];

    foreach ($kategoriak as $fokategoriak) {
        foreach ($fokategoriak as $id => $kategoria) {
            $planValues[$id] = 
                number_format(Kategoriak::getKategoriaUtolsoTerv($id, $deviza), 0, ',', ' ');
        }
    }
    echo json_encode($planValues);
?>;

        document.getElementsByName('plan-button')[0].addEventListener("click", function(evt) {
                    var osszeg_selector = document.getElementsByName('Terv[osszeg]')[0];
                    osszeg = parseInt(evt.target.value.replace(' ',''));
                    osszeg_selector.value = osszeg;
        });

</script>