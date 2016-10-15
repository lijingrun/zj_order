<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '江门中建订单系统',
        'brandUrl' => 'index.php?r=customer',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '我是客户', 'url' => ['/site/customer_login']],
        ['label' => '我是业务员', 'url' => ['/site/login']],
//        ['label' => '统计', 'url' => ['/client/statistic']],
//        ['label' => '客户', 'url' => ['/customer']],
//        ['label' => '发票', 'url' => ['/invoice']],
//        ['label' => '重置密码', 'url' => ['/client/reset_password']],
    ];
//    if (empty(Yii::$app->session['customer_id'])) {
//        $menuItems[] = ['label' => '登录', 'url' => ['/site/customer_login']];
//    } else {
//        $menuItems[] = [
//            'label' => '登出 (' . Yii::$app->session['user_name'] . ')',
//            'url' => ['/client/logout'],
//            'linkOptions' => ['data-method' => 'post']
//        ];
//    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left"></p>

        <p class="pull-right">开发团队@Rium-Lin</p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
