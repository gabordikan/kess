<?php

/** @var yii\web\View $this */

use app\models\Penztarca;
use app\models\Mozgas;

use yii\grid\GridView;
use yii\data\ActiveDataProvider;


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
    foreach($penztarcak as $penztarca) {
        echo "<div>".$penztarca."</div>";
    }

    echo "<div>Összesen (".number_format(Penztarca::getOsszEgyenleg(),0,',',' ').")</div>";
}

$dataProvider = new ActiveDataProvider([
    'query' => Mozgas::find()->where(
        [
            'felhasznalo' => Yii::$app->user->id,
            'torolt' => 0,
            'penztarca_id' => 1,
        ]
    )->orderBy(['datum' => SORT_DESC]),
    'pagination' => [
        'pageSize' => 20,
    ],
]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
]);

?>
</div>
