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


$this->title = 'Kess';
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

    echo Html::dropDownList('penztarca', Yii::$app->request->get('penztarca_id'), $penztarcak);

    if($penztarca_id != null) {

        echo "<BR/><BR/>";

        echo "<H1>".Penztarca::findOne(['id' => $penztarca_id, 'felhasznalo' => Yii::$app->user->id])->nev."</H1>";

        $dataProvider = new ActiveDataProvider([
            'query' => Mozgas::find()->where(
                [
                    'felhasznalo' => Yii::$app->user->id,
                    'torolt' => 0,
                    'penztarca_id' => $penztarca_id,
                ]
            )->orderBy(['datum' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        echo GridView::widget([
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
                        return Kategoriak::findOne([ 'id' => $model->kategoria_id])->nev; 
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
                ],
                [
                    'class' => ActionColumn::class,
                    'visibleButtons' => [
                        'view' => false,
                        'update' => false,
                        'delete' => true,
                    ],
                    'urlCreator' => function ($action, $model, $key, $index, $column) {
                        return 'index.php?r=site%2flistkess&penztarca_id='.$model->penztarca_id.'&delete_id='.$model->id;
                    },
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
        window.location.href = 'index.php?r=site%2Flistkess&penztarca_id=' + evt.target.value;
    });
</script>