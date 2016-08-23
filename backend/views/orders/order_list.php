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
        <a href="index.php?r=orders/detail&id=<?php echo $order['order_id']?>">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">订单号：<?php echo $order['order_sn'];?></h3>
            </div>
            <div class="panel-body">
                <p><?php echo $order['customer_id']['customer_name'];?></p>
                <p>订单金额：￥<?php echo $order['order_amount']?></p>
                <p>下单时间：<?php echo date("Y-m-d",$order['add_time']);?></p>
            </div>

        </div>
        </a>
    <?php endforeach; ?>
</div>
