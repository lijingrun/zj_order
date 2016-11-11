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
            <h3 class="panel-title">价格政策</h3>
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
    <?php if(!empty($promotion)){ ?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">优惠活动</h3>
        </div>
        <div class="panel-body">
                <?php foreach($promotion as $val): ?>
                    <div class="row">
                        <div class="col-xs-9 col-md-4">
<!--                            <p>活动时间：</p>-->
                            <?php echo date("Y-m-d H:i:s",$val['start_time'])."<br/>".date("Y-m-d H:i:s",$val['end_time'])?>
                        </div>
                        <div class="col-xs-3 col-md-4">
<!--                            <p>优惠内容</p>-->
                            <span style="color:#00a2d4">
                            <?php echo $val['title'];?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
        </div>
    </div>
<?php } ?>
</div>
</body>