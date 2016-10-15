<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 10:52
 */
use yii\widgets\LinkPager;
?>
<div>
    <?php foreach($orders as $order): ?>
        <a href="index.php?r=client/order_detail&id=<?php echo $order['order_id']?>">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">订单号：<?php echo $order['order_sn'];?></h3>
            </div>
            <div class="panel-body">
                <p><?php echo $order['customer_id']['customer_name'];?></p>
                <p>订单金额：￥<?php echo $order['order_amount']?></p>
                <p>下单时间：<?php echo date("Y-m-d",$order['add_time']);?></p>
                <p>订单状态：
                    <?php
                    switch($order['order_status']){
                        case 0 : echo "未确认";
                            break;
                        case 1 : echo "已确认";
                            break;
                        case 2 : echo "<span style='color:red;'>已取消</span>";
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
                    ?></p>
            </div>

        </div>
        </a>
    <?php endforeach; ?>
</div>
