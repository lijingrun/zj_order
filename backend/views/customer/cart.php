<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/17
 * Time: 16:36
 */
?>
<script>
    function change_num(id){
        var new_num = $("#num"+id).val();
        if(new_num > 0){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/change_cat_num',
                data : {'id' : id, 'new_num' : new_num},
                success : function(data){
                    if(data == 111){
                        alert("修改成功！");
                        location.reload();
                    }else{
                        alert("服务器繁忙，请稍后重试！");
                    }
                }
            });
        }
    }
    function del(id){
        $.ajax({
            type : 'post',
            url : 'index.php?r=orders/del_cart',
            data : {'id' : id},
            success : function(data){
                if(data == 111){
                    alert("操作成功！");
                    location.reload();
                }else{
                    alert("服务器繁忙，请稍后重试！");
                }
            }
        });
    }
</script>
<div>
    <?php foreach($cart_data as $val): ?>
        <div class="panel panel-primary" style="padding: 10px;">
            <div class="row">
                <div class="col-xs-4 col-md-4">
                    <img src="<?php echo $val['goods_img'];?>" style="width: 100%;" />
                </div>
                <div class="col-xs-8 col-md-4">
                    <div style="padding-top: 15px;">
                        <p><?php echo $val['goods_name'];?></p>
                        <p>￥<?php echo $val['price']?></p>
                    </div>
                </div>
            </div>
                    <div class="panel-body">
                        数量：
                        <input type="text" id="num<?php echo $val['cart_id'];?>" value="<?php echo $val['num'];?>" style="width:30px;" <?php if($val['is_gift'] == 1){echo "readonly='readonly'";}else{ echo "onblur='change_num(".$val['cart_id'].");'"; }?> />
                        <span style="color:red;">
                            (库存<?php echo $val['goods_num']?>)
                        </span>
                        <span class="glyphicon glyphicon-trash" aria-hidden="true" style="float: right;" onclick="del(<?php echo $val['cart_id']?>);"></span>
                    </div>
            <?php if(!empty($val['promotion'])){ ?>
            <div style="padding-left: 10px;color:red;">
                <?php foreach($val['promotion'] as $p){?>
                    <p><?php echo $p['title'];?></p>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    <?php endforeach; ?>
    <div>
        总价：￥<?php echo $total_price;?>
    </div>
    <div style="float:right;">
        <a href="index.php?r=customer/add_order&customer_id=<?php echo $customer_id;?>">
            <button class="btn-success">确认订单</button>
        </a>
    </div>
</div>
