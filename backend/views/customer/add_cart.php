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
    function find_goods(){
        var name = $("#name").val().trim();
        var customer_id = <?php echo $customer_id ?>;
        location.href="index.php?r=customer/add_cart&customer_id="+customer_id+"&key_word="+name;
    }
    function add_to_cart(goods_id){
        var customer_id = '<?php echo $customer_id ?>';
        var number = $("#number"+goods_id).val();
        if(number < 1){
            alert("请填写正确数量");
        }else {
            $.ajax({
                type: 'post',
                url: 'index.php?r=customer/add_to_cart',
                data: {'goods_id': goods_id, 'customer_id': customer_id, 'number': number},
                success: function (data) {
                        if (data == 111) {
                            alert("添加成功！");
                        } else if (data == 222) {
                            alert("商品已经存在购物车中！");
                        } else {
                            alert("服务器繁忙，请稍后重试！");
                        }
                }
            });
        }
    }
</script>
<body style="background-color: #f5f5f5;">
<div class="input-group" style="padding-top: 10px;">
    <input type="text" id="name" class="form-control" value="<?php echo $key_word;?>" placeholder="搜索产品名称/编码" aria-describedby="basic-addon2">
    <span class="input-group-addon" id="basic-addon2" onclick="find_goods();">搜索</span>
</div>
    <div style="padding: 10px;margin-right: 10px;" align="right">
        <button class="btn-success" style="float: left;" data-toggle="modal" data-target="#myModal">产品分类</button>
        <a href="index.php?r=customer/cart&customer_id=<?php echo $customer_id;?>">
            <span class="glyphicon glyphicon-share" aria-hidden="true"></span>查看购物车
        </a>
    </div>
<?php if($goods){ ?>
    <?php foreach($goods as $good): ?>
<!--        <a href="index.php?r=goods/detail&id=--><?php //echo $good['goods_id']?><!--">-->
            <div class="row" style="padding-top: 10px;padding-bottom:10px;background-color: white;margin:10px;">
                <div class="col-xs-5 col-sm-4">
                    <img src="http://jmzjtech.vicp.net:81/<?php echo $good['goods_img'];?>" style="width:100%;" />
                </div>
                <div class="col-xs-7 col-sm-4">
                    <p style="padding-top:20px;">
                        <?php echo $good['goods_name']; ?>
                    </p>
                    <p style="color:#00a2d4">
                        ￥<?php echo $good['shop_price']?>
                        &nbsp;&nbsp;
                        <input type="text" value="1" style="width:30px;" id="number<?php echo $good['goods_id'];?>" />
                    </p>
                    <a href="#">
                        <div onclick="add_to_cart(<?php echo $good['goods_id']?>);">
                            <span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>放入物车
                        </div>
                    </a>
                </div>
                <?php if(!empty($good['seller_note'])){ ?>
                    促销优惠
                <div style="padding-left: 10px;color:red;">
                    <?php foreach($good['seller_note'] as $val){ ?>
                        <p><?php echo $val['title']?></p>
                    <?php } ?>
                </div>
            <?php } ?>
            </div>
<!--        </a>-->
    <?php endforeach; ?>
    <div align="center">
        <?= LinkPager::widget(['pagination' => $pages]); ?>
    </div>
<?php }else{ ?>
    <div style="padding:10px;padding-top: 20px;">
        <a href="index.php?r=customer/add_cart&customer_id=<?php echo $customer_id?>">
            <div class="alert alert-danger" role="alert">
                <strong>查不到相关的商品信息!</strong> 点我试试
            </div>
        </a>
    </div>
<?php } ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">分类列表</h4>
            </div>
            <div class="modal-body">
                <ul>
                    <?php foreach($category as $children): ?>
                        <?php if(empty($children['child'])){ ?>
                            <a href="index.php?r=customer/add_cart&customer_id=<?php echo $customer_id?>&cat_id=<?php echo $children['cat_id']?>">
                        <?php } ?>
                        <li>
                            <?php echo $children['cat_name']?>
                            <?php if($children['child']){ ?>
                                <ul>
                                    <?php foreach($children['child'] as $child): ?>
                                        <?php if(empty($child['child'])){ ?>
                                            <a href="index.php?r=customer/add_cart&customer_id=<?php echo $customer_id?>&cat_id=<?php echo $children['cat_id']?>">

                                        <?php } ?>
                                        <li>
                                            <?php echo $child['cat_name']; ?>
                                        </li>
                                        <?php if(empty($child['child'])){ ?>
                                            </a>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php } ?>
                        </li>
                        <?php if(empty($children['chile'])){ ?>
                            </a>
                        <?php } ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div>
    </div>
</div>
</body>
