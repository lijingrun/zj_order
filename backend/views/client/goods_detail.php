<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/17
 * Time: 11:57
 */
?>
<body style="background-color: #f5f5f5;">
<div>
    <div>
        <img src="<?php echo Yii::$app->params['url'].$goods['goods_img'];?>" style="width: 100%"; />
    </div>
    <div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4><?php echo $goods['goods_name'];?></h4>
                <p >
                    编码：<?php echo $goods['goods_sn']?>
                    <span style="float: right;color:#00a2d4">￥<?php echo $goods['shop_price']?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <p >
                库存数：<?php echo $goods['goods_number']?>
            </p>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">等级价格</h3>
        </div>
        <div class="panel-body">
<!--            <div class="row">-->
<!--                <div class="col-xs-6 col-md-4">普通账号</div>-->
<!--                <div class="col-xs-6 col-md-4">-->
<!--                    <span style="color:#00a2d4">-->
<!--                    ￥--><?php //echo $goods['shop_price']?>
<!--                    </span>-->
<!--                </div>-->
<!--            </div>-->
                    <?php foreach($member_price as $val): ?>
                    <div class="row">
                        <div class="col-xs-6 col-md-4"><?php echo $val['rank_name']?>:</div>
                        <div class="col-xs-6 col-md-4">
                            <span style="color:#00a2d4">
                            ￥<?php echo $val['user_price']?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
        </div>
    </div>

    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">产品详情</h3>
        </div>
        <div class="panel-body">
            <?php echo $goods['goods_desc']; ?>
        </div>
    </div>
</div>
</body>