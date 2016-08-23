<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/16
 * Time: 14:56
 */
?>
<script>
    function del_order(id){
        if(confirm("是否确定作废订单？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/del',
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
    }
</script>
<div>
    <div class="panel panel-info">
        <div class="panel-body">
            <p>公司名称：<?php echo $customer['customer_name'];?></p>
            <p>订单编号：<?php echo $order['order_sn'];?></p>
            <p>订单状态：
            <?php
            switch($order['order_status']){
                case 0 : echo "未确认";
                    break;
                case 1 : echo "已确认";
                    break;
                case 2 : echo "已取消";
                    break;
            }
            echo "|";
            switch($order['pay_status']){
                case 0 : echo "未支付";
                    break;
                case 1 : echo "支付中";
                    break;
                case 2 : echo "已支付";
                    break;
            }
            echo "|";
            switch($order['shipping_status']){
                case 0 : echo "未发货";
                    break;
                case 1 : echo "已发货";
                    break;
                case 2 : echo "收获确认";
                    break;
            }
            ?>
            </p>
            <p>付款方式：<?php if($order['customer_pay'] == 1){ echo "现销";}else{ echo "赊销";};?></p>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <p>订单金额：￥<?php echo $order['order_amount'];?></p>

        </div>
    </div>

    <div class="panel panel-info">
        <a data-toggle="modal" data-target="#myModal">
            <div class="panel-body">
                <ul  class="nav nav-pills" role="tablist">
                    <li role="presentation">
                        商品清单
                        <span class="badge">
                            <?php echo $count;?>
                        </span>
                    </li>
                </ul>
            </div>
        </a>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <p>交货日期：<?php echo $order['get_time'];?></p>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-body">
            <p>备注信息：<?php echo $order['best_time'];?></p>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">收货信息</h3>
        </div>
        <div class="panel-body">
            <p>收货地址：<?php echo $order['address']?></p>
            <p>收货人：<?php echo $order['consignee']?></p>
            <p>电话：<?php echo $order['tel']?></p>
            <p>送货方式：<?php echo $order['shipping_name']?></p>
        </div>
    </div>
    <div align="center">
        <?php if($order['order_status'] == 1){ ?>
        <button onclick="del_order(<?php echo $order['order_id']?>);" class="btn-default" style="width:45%;padding:3px;">订单作废</button>
        &nbsp;&nbsp;
        <button class="btn-primary" style="width:45%;padding:3px;">确认收款</button>
        <?php }elseif($order[order_status] == 2){ ?>
            <span class="label label-danger" style="font-size: 20px;">订单已取消</span>
        <?php } ?>
    </div>


    <!-- good_list -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">商品列表</h4>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>商品</th>
                            <th>价格</th>
                            <th>数量</th>
                        </tr>
                        <?php foreach($goods as $good): ?>
                        <tr>
                            <td><?php echo $good['goods_name'];?></td>
                            <td><?php echo $good['goods_price'];?></td>
                            <td><?php echo $good['goods_number'];?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" align="right">
                                小计：￥<?php echo $order['goods_amount'];?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
<!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        </div>
    </div>
</div>