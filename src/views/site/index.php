<?php

/** @var yii\web\View $this */

use app\models\Penztarca;

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
?>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<div>Tervezett/eddigi bevétel</div>
<div>Tervezett/eddigi kiadás</div>

<div>Tervezett/eddigi egyenleg</div>
</div>
