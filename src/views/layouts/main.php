<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
//$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/favicon.png']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            Yii::$app->user->isGuest ? '' : ['label' => 'Kezdőlap', 'url' => ['/site/index']],
            Yii::$app->user->isGuest ? '' : ['label' => 'Rögzítés', 'url' => ['/site/recordkess']],
            Yii::$app->user->isGuest ? '' : ['label' => 'Tételek', 'url' => ['/site/listkess']],
            Yii::$app->user->isGuest ? '' : ['label' => 'Terv', 'url' => ['/site/plan']],
            Yii::$app->user->isGuest ? '' : ['label' => 'Kategóriák', 'url' => ['/site/categories']],
            Yii::$app->user->isGuest ? '' : ['label' => 'Pénztárcák', 'url' => ['/site/wallets']],
            ['label' => 'Beállítások', 'items' => [
                Yii::$app->user->isGuest ? '' : ['label' => 'Beállítások', 'url' => ['/site/settings']],
            ]],
            Yii::$app->user->id == 1
                ? ['label' => 'Admin', 'url' => ['/site/admin']]
                : ['label' => 'Névjegy', 'url' => ['/site/about']],
            Yii::$app->user->isGuest
                ? ['label' => 'Belépés', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        'Kilépés (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>',
            
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; Dikán Gábor</div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
