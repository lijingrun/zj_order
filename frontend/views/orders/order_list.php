<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 10:52
 */
use yii\widgets\LinkPager;
?>
<script>
    function find(){
        var start = $("#start").val();
        var end = $("#end").val();
        var status = $("#status").val();
        location.href="index.php?r=orders&start="+start+"&end="+end+"&status="+status;
    }
    function confirm_order(order_id){
        $.ajax({
            type : 'post',
            url : 'index.php?r=orders/confirm_order',
            data : {'order_id' : order_id},
            success : function(data){
                if(data == 111){
                    alert("操作成功！");
                    location.reload();
                }
            }
        });
    }
    function get_money(order_id){
        if(confirm("是否确定收到了货款？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/get_money',
                data : {'order_id' : order_id},
                success :  function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }else{
                        alert("订单所在状态不能确认收款！");
                    }
                }
            });
        }
    }
    function out_push(order_id){
        if(confirm("是否确定出库？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/out_push',
                data : {'order_id' : order_id},
                success :  function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }else{
                        alert("订单所在状态不能确认收款！");
                    }
                }
            });
        }
    }
</script>
<div>
<!--    <div align="right">-->
<!--        <a href="index.php?r=orders/add">-->
<!--            <input type="button" value="新建订单" class="btn-success" />-->
<!--        </a>-->
<!--    </div>-->
    <div align="center">
        下单时间<input type="date" id="start" />&nbsp;--
        <input type="date" id="end" />
        状态
        <select id="status">
            <option value="0">所有</option>
            <option value="1">未确认</option>
            <option value="2">未支付</option>
            <option value="3">未出库</option>
            <option value="4">已取消</option>
            <option value="5">已收货</option>
        </select>
        <input type="button" value="查找" onclick="find();" />
    </div>
    <div style="padding-top:28px;">
        <table class="table table-hover">
            <tr>
                <th>订单编号</th>
                <th>客户名称</th>
                <th>下单时间</th>
                <th>收货地址</th>
                <th>要求收货时间</th>
                <th>联系人</th>
                <th>联系电话</th>
                <th>收款方式</th>
                <th>发货方式</th>
                <th>订单金额</th>
                <th>订单备注</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?php echo $order['order_sn']?></td>
                <td><?php echo $order['customer_id']['customer_name']?></td>
                <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" title="<?php echo date("Y-m-d H:i:s",$order['add_time']);?>">
                    <?php echo date("Y-m-d",$order['add_time']);?>
                </td>
                <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" title="<?php echo $order['address']?>">
                    <?php echo $order['address']?>
                </td>
                <td><?php echo $order['get_time']?></td>
                <td><?php echo $order['consignee']?></td>
                <td><?php echo $order['tel']?></td>
                <td><?php if($order['customer_pay'] == 1){echo "现销";}else{echo "赊销";}?></td>
                <td><?php if($order['shipping_id'] == 1){echo "送货";}else{echo "自提";}?></td>
                <td>￥<?php echo $order['order_amount']?></td>
                <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" title="<?php echo $order['how_oos']?>">
                    <?php echo $order['how_oos']?>
                </td>
                <td>
                    <?php
                    switch($order['order_status']){
                        case 0 : echo "未确认";
                            break;
                        case 1 : echo "已确认";
                            break;
                        case 2 : echo "<span style='color:red;'>已取消</span>";
                            break;
                        case 3 : echo "已收货";
                            break;
                        default : echo "未知状态";
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
                </td>
                <td>
                    <a href="index.php?r=orders/detail&id=<?php echo $order['order_id']?>">查看</a>
                    <?php if($order['order_status'] == 0){ ?>
                    |&nbsp;<a href="#" onclick="confirm_order(<?php echo $order['order_id'];?>);">确认</a>
                    <?php } ?>
                    <?php if($order['pay_status'] == 0){ ?>
                        |&nbsp;<a href="#" onclick="get_money(<?php echo $order['order_id'];?>);">收到货款</a>
                    <?php } ?>
                    <?php if($order['shipping_status'] == 0){ ?>
                    |&nbsp;<a href="#" onclick="out_push(<?php echo $order['order_id'];?>)">出库</a>
                    <?php } ?>
<!--                    <a href="#">取消订单</a>-->
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="12">
                    <?= LinkPager::widget(['pagination' => $pages]); ?>
                </td>
            </tr>
        </table>
    </div>
</div>
