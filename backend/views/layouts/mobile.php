<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;


/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content ="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">

    <title>F&E汽车快速养护连锁</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sticky-footer-navbar.css" rel="stylesheet">
</head>

<body >

<?php $this->beginBody() ?>
<header>
	<img src='images/logo.jpg' style='width:100%' />
</header>

<div class="main" style="padding-top:50px;">
    <?php

    ?>

    <div class="container">
        <?= $content ?>
    </div>
</div>

<footer class="footer">
	<div style="width:100%;border-style:solid; border-width:18px; border-color:#A1A1A1">
	</div>
    <nav class="navbar navbar-fixed-bottom">

        <a href="index.php?r=member" class="new-tbl-cell"> <span class="glyphicon glyphicon-home" aria-hidden="true"></span><p>首页</p></a>
        <a href="index.php?r=member/my_order" class="new-tbl-cell"> <span class="glyphicon glyphicon-file" aria-hidden="true"></span><p>工单池</p></a>
        <a href="index.php?r=member/my_cars" class="new-tbl-cell"> <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span><p>车辆信息</p></a>
        <a href="index.php?r=member/core" class="new-tbl-cell"> <span class="glyphicon glyphicon-user" aria-hidden="true"></span><p>个人中心</p></a>

    </nav>
</footer>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
