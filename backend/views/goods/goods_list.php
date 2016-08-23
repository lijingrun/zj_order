<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 15:01
 */
use yii\widgets\LinkPager;
?>
<body style="background-color: #f5f5f5;">

<div align="center">
    <a href="index.php?r=goods/category">
        <button class="btn-success" style="width: 45%;">分类</button>
    </a>
    &nbsp;&nbsp;
    <a href="index.php?r=goods&promote=1">
        <button class="btn-info" style="width: 45%;">促销</button>
    </a>
</div>
<?php if($goods){ ?>
<?php foreach($goods as $good): ?>
    <a href="index.php?r=goods/detail&id=<?php echo $good['goods_id']?>">
        <div class="row" style="padding-top: 10px;padding-bottom:10px;background-color: white;margin:10px;">
            <div class="col-xs-5 col-sm-4">
                <img src="#" style="width:100%;" />
            </div>
            <div class="col-xs-7 col-sm-4">
                <p style="padding-top:20px;">
                    <?php echo $good['goods_name']; ?>
                </p>
                <p style="color:#00a2d4">
                    ￥<?php echo $good['shop_price']?>
                </p>

            </div>
        </div>
    </a>
<?php endforeach; ?>
    <div align="center">
        <?= LinkPager::widget(['pagination' => $pages]); ?>
    </div>
<?php }else{ ?>
    <div style="padding:10px;padding-top: 20px;">
        <a href="index.php?r=goods">
            <div class="alert alert-danger" role="alert">
                <strong>查不到相关的商品信息!</strong> 点我试试
            </div>
        </a>
    </div>
<?php } ?>

</body>
