<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

use app\models\Kategoriak;
use app\models\Penztarca;
use app\models\Mozgas;
use app\widgets\MyDatePicker;
use Codeception\PHPUnit\ResultPrinter\HTML as ResultPrinterHTML;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;

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

    echo '<span style="display: inline-block; margin-right: 10px; margin-bottom: 5px;">'.Html::label('Pénztárca:').'&nbsp;'.Html::dropDownList('penztarca', $penztarca_id , $penztarcak, ['style' => 'width:240px !important; display: ;']).'</span>';
    echo '<span style="display: inline-block; margin-right: 10px; margin-bottom: 5px;">'.Html::label('Időszak: ')."&nbsp;".MyDatePicker::widget([
        'id' => 'idoszakselector',
        'value' => $idoszak,
        'language' => 'hu',
        'dateFormat' => 'yyyy-MM',
        'clientOptions' => [
            'onSelect' => new \yii\web\JsExpression("function(dateText, inst) {
                window.location = '/site/listkess?penztarca_id=".$penztarca_id."&idoszak='+dateText+'&searchText='+document.getElementsByName('searchText')[0].value;
                }"),
        ],
    ],  ['class' => 'form-select']).'</span>';
    echo '<span style="display: inline-block; margin-right: 10px; margin-bottom: 5px;">'.Html::label('Keresés: ', 'searchText').'&nbsp;'
        .Html::textInput('searchText', $searchText, ['id' => 'searchText', 'style' => 'width:200px'])
        .Html::Button('Keresés', ['id' => 'searchButton']).'</span>';

    echo "<BR/><BR/>";

    if($penztarca_id != null) {

        switch (substr($searchText,0,1)) {
            case '<':
                $searchOperator = '<';
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
            'query' => Mozgas::find()
            ->joinWith('kategoria')
            ->andWhere([$searchOperator, 'osszeg', $searchText2])
            ->orFilterWhere([$searchOperator, 'kategoriak.nev', $searchText2])
            ->orFilterWhere([$searchOperator, 'kategoriak.fokategoria', $searchText2])
            ->orFilterWhere([$searchOperator, 'megjegyzes', $searchText2])
            ->andWhere(
                [
                    'mozgas.felhasznalo' => Yii::$app->user->id,
                    'mozgas.torolt' => 0,
                    'mozgas.penztarca_id' => $penztarca_id,
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
                    'format' => ['currency', 'HUF'],
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

    searchTextField.addEventListener('keyup', function(evt) {
        if (evt.key === 'Enter' || evt.keyCode === 13) {
            window.location.href = '/site/listkess?penztarca_id=' + penztarca.value + '&idoszak=' + idoszakSelector.value + '&searchText=' + searchTextField.value;
        }
    });
</script>