<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 15:01
 */
use yii\widgets\LinkPager;
?>
<script>
    function add_to_cart(id){
        var number = $("#number"+id).val();
        if(number >= 1 ) {
            $.ajax({
                type: 'post',
                url: 'index.php?r=client/add_to_cart',
                data: {'goods_id': id, 'number' : number},
                success: function (data) {
                    if (data == 111) {
                        alert("添加成功！");
                    } else if (data == 222) {
                        alert("已经存在于购物车");
                        location.href = "index.php?r=client/my_cart";
                    } else if (data == 333) {
                        alert("添加失败，请重试！");
                    }
                }
            });
        }
    }
</script>
<body style="background-color: #f5f5f5;">

<div align="center">
<!--    <a href="index.php?r=goods/category">-->
<!--        <button class="btn-success" style="width: 45%;">分类</button>-->
<!--    </a>-->
<!--    &nbsp;&nbsp;-->
<!--    <a href="index.php?r=goods&promote=1">-->
<!--        <button class="btn-info" style="width: 45%;">促销</button>-->
<!--    </a>-->
</div>
<?php if($goods){ ?>
<?php foreach($goods as $good): ?>

        <div class="row" style="padding-top: 10px;padding-bottom:10px;background-color: white;margin:10px;">
            <div class="col-xs-5 col-sm-4">
                <img src="http://jmzjtech.vicp.net:81/<?php echo $good['goods_img']?>" style="width:100%;" />
            </div>
            <div class="col-xs-7 col-sm-4">
                <p style="padding-top:20px;">
                    <?php echo $good['goods_name']; ?>
                </p>
                <p style="color:#00a2d4">
                    ￥<?php echo $good['shop_price']?>

                </p>
                <p style="color:#00a2d4">
                    购买数量：<input type="text" value="1" style="width:40px;" id="number<?php echo $good['goods_id']?>" />
                </p>

            </div>
            <?php if(!empty($good['seller_note'])){ ?>
            <div style="padding-left: 10px;color:red;">
                <?php foreach($good['seller_note'] as $val){ ?>
                <p><?php echo $val['title'];?></p>
                <?php } ?>
            </div>
            <?php } ?>
            <div style="float: left;">
                <a href="index.php?r=client/goods_detail&id=<?php echo $good['goods_id']?>">
                    <input type="button" class="btn-info" value="查看详细" />
                </a>
                &nbsp;&nbsp;
                <input type="button" value="加入购物车" class="btn-warning" onclick="add_to_cart(<?php echo $good['goods_id'];?>)" />
            </div>
        </div>
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
