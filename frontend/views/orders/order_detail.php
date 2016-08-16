<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/16
 * Time: 14:56
 */
?>
<div>
    <div>


        <blockquote>
            <p>客户：<?php echo $customer['customer_name'];?></p>
        </blockquote>
        <blockquote>
            <p>客户编码：<?php echo $customer['customer_code'];?></p>
        </blockquote>
        <div style="padding-left: 20px; ">
            <table class="table table-hover" style="width:30%;">
                <tr>
                    <th>产品</th>
                    <th>客户价格</th>
                    <th>数量</th>
                </tr>
                <?php foreach($goods as $good): ?>
                <tr>
                    <td><?php echo $good['goods_name']?></td>
                    <td>￥<?php echo $good['customer_price']?></td>
                    <td><?php echo $good['num']?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <blockquote>
            <p>订单总额：￥<?php echo $order['order_amount']?></p>
        </blockquote>
        <blockquote>
            <p>收货地址：<?php echo $order['address'];?></p>
        </blockquote>
        <blockquote>
            <p>联系人：<?php echo $order['contacts']."(".$order['phone'].")";?></p>
        </blockquote>
        <blockquote>
            <p>发货方式：<?php if($order['clog'] == 1){echo "送货";}else{echo "自提";}?></p>
        </blockquote>
        <blockquote>
            <p>要求到货时间：<?php echo $order['get_time']?></p>
        </blockquote>
        <blockquote>
            <p>付款方式：<?php if($order['pay_type'] == 1){echo "现销";}else{echo "赊销";}?></p>
        </blockquote>
        <blockquote>
            <p>备注：<?php echo $order['remarks']?></p>
        </blockquote>
    </div>
</div>
