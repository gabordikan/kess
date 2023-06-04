<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Névjegy';
?>
<div class="site-about">
    <h2>Mi az a Kess?</h2>

    <p align="justify">A Kess<img src="/favicon.png" width="24px", height="24px"> egy személyes használatra készült költségregisztráló és elemző alkalmazás. Rögzítheted benne kategóriánként a pénzügyi terveid, naplózhatod a megvalósult bevételeidet és költéseidet.
    <p align="justify">A program kezel több devizát a tranzakciók logolásához (pénztárcánként egyet), a tervezést egyelőre csak HUF alapon lehet megadni.

    <h2>Bejelentkezés</h2>

    <p align="justify">Ha már felhasználói fiókod, akkor jelentkezz be <a href="/site/login/">ITT</a>

    <h2>Regisztráció</h2>

    <p align="justify">Ha még nincs felhasználói fiókod, akkor <a href="/site/register/">ITT</a> tudsz majd regisztrálni

    <h2>Kapcsolat</h2>
        Dikán Gábor<br/>
        <a href="https://t.me/gabordikan">Telegram</a>

    <h2>Hívj meg egy kávéra!</h2>

    <p align="justify">A program személyes használatra ingyenes, de amennyiben szeretnéd támogatni a fejlesztést, akkor hívj meg egy kávéra!

    <form action="https://www.paypal.com/donate" method="post" target="_top">
        <input type="hidden" name="hosted_button_id" value="GLVR7KVBFVRX8" />
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
        <img alt="" border="0" src="https://www.paypal.com/en_HU/i/scr/pixel.gif" width="1" height="1" />
    </form>

</div>
