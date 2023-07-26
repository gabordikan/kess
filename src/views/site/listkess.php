<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Mozgas;
use Codeception\PHPUnit\ResultPrinter\HTML as ResultPrinterHTML;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;

use yii\jui\DatePicker;

$this->title = 'Kess';

if (empty($idoszak)) {
    $idoszak = date('Y-m');
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
    $penztarcak = Penztarca::getPenztarcak();
    $penztarca_id = $penztarca_id ?? array_key_first($penztarcak);

    echo Html::label('Pénztárca:').'&nbsp;'.Html::dropDownList('penztarca', $penztarca_id , $penztarcak, ['style' => 'width:300px !important; display: ;']);
    echo "&nbsp;&nbsp;&nbsp;";
    echo Html::label('Statisztika időszak: ')."&nbsp;".DatePicker::widget([
        'id' => 'idoszakselector',
        'value' => $idoszak,
        'language' => 'hu',
        'dateFormat' => 'yyyy-MM',
        'clientOptions' => [
            'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                window.location = '/site/listkess?penztarca_id=".$penztarca_id."&idoszak='+dateText+'&searchText='+document.getElementsByName('searchText')[0].value;
                }"),
        ],
    ],  ['class' => 'form-select']);
    echo "&nbsp;&nbsp;&nbsp;";
    echo Html::label('Keresés: ').'&nbsp;'
        .Html::textInput('searchText', $searchText, ['style' => 'width:300px'])
        .Html::Button('Keresés', ['id' => 'searchButton']);

    echo "<BR/><BR/>";

    if($penztarca_id != null) {

        switch (substr($searchText,0,1)) {
            case '<':
                $searchOpreator = '<';
                $searchText2 = substr($searchText,1);
                break;
            case '>':
                $searchOperator = '>';
                $searchText2 = substr($searchText,1);
                break;
            case '=':
                $searchOperator = '=';
                $searchText2 = substr($searchText,1);
                break;
            default:
                $searchOperator = 'like';
                $searchText2 = $searchText ?? '';
            break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Mozgas::find()->where(
                [
                    'felhasznalo' => Yii::$app->user->id,
                    'torolt' => 0,
                    'penztarca_id' => $penztarca_id,
                ]
            )
            ->andWhere(['>=','datum',$idoszak.'-01'])
            ->andWhere(['<=','datum',$idoszak.'-31'])
            ->andWhere([$searchOperator, 'osszeg', $searchText2])
            ->orderBy(['datum' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        echo GridView::widget([
            'showFooter' => true,
            'footerRowOptions' => ['style' => 'text-align: right;'],
            'summary' => '{begin}-{end}, Összesen: {totalCount}',
            'columns' => [
                ['class' => SerialColumn::class],
                [
                    'class' => DataColumn::class, // this line is optional
                    'attribute' => 'datum',
                    'format' => 'text',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        $kategoria = Kategoriak::findOne([ 'id' => $model->kategoria_id]);
                        return $kategoria->fokategoria."/".$kategoria->nev; 
                    },
                    'format' => 'text',
                    'label' => 'Kategória',
                ],
                [
                    'class' => DataColumn::class, // this line is optional
                    'value' => function ($model, $key, $index, $column) {
                        return $model->tipus * $model->osszeg; 
                    },
                    'format' => ['currency', 'HUF'],
                    'label' => 'Összeg',
                    'contentOptions' => ['style'=>'text-align: right'],
                ],
                [
                    'class' => ActionColumn::class,
                    'visibleButtons' => [
                        'view' => false,
                        'update' => true,
                        'delete' => true,
                    ],
                    'urlCreator' => function ($action, $model, $key, $index, $column) use ($searchText) {
                        switch ($action) {
                            case "update":
                                return '/site/recordkess?update_id='.$model->id;
                            case "delete":
                                return '/site/listkess?penztarca_id='.$model->penztarca_id.'&delete_id='.$model->id.'&searchText='.$searchText;
                        }
                    },
                    'contentOptions' => ['style'=>'text-align: center'],
                ],
            ],
            'dataProvider' => $dataProvider,
        ]);
    }
}
?>
</div>

<script>
    var penztarca = document.getElementsByName('penztarca')[0];
    penztarca.addEventListener("change", function(evt) {
        window.location.href = '/site/listkess?penztarca_id=' + evt.target.value + '&idoszak=' + idoszakSelector.value + '&searchText=' + searchTextField.value;
    });

    var idoszakSelector = document.getElementById('idoszakselector');

    var searchButton = document.getElementById('searchButton');
    var searchTextField = document.getElementsByName('searchText')[0];
    searchButton.addEventListener('click', function (evt) {
        window.location.href = '/site/listkess?penztarca_id=' + penztarca.value + '&idoszak=' + idoszakSelector.value + '&searchText=' + searchTextField.value;
    })
</script>