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
        location.href="index.php?r=orders&start="+start+"&end="+end;
    }
</script>
<div>
    <div align="right">
        <a href="index.php?r=orders/add">
            <input type="button" value="新建订单" class="btn-success" />
        </a>
    </div>
    <div align="center">
        下单时间<input type="date" id="start" />&nbsp;--
        <input type="date" id="end" />
        <input type="button" value="查找" onclick="find();" />
    </div>
    <div style="padding-top:28px;">
        <table class="table table-hover">
            <tr>
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
                <td><?php echo $order['customer_id']['customer_name']?></td>
                <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" title="<?php echo date("Y-m-d H:i:s",$order['add_time']);?>">
                    <?php echo date("Y-m-d",$order['add_time']);?>
                </td>
                <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" title="<?php echo $order['address']?>">
                    <?php echo $order['address']?>
                </td>
                <td><?php echo $order['get_time']?></td>
                <td><?php echo $order['contacts']?></td>
                <td><?php echo $order['phone']?></td>
                <td><?php if($order['pay_type'] == 1){echo "现销";}else{echo "赊销";}?></td>
                <td><?php if($order['clog'] == 1){echo "送货";}else{echo "自提";}?></td>
                <td>￥<?php echo $order['order_amount']?></td>
                <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" title="<?php echo $order['remarks']?>">
                    <?php echo $order['remarks']?>
                </td>
                <td>
                    <?php
                    switch($order['status']){
                        case 10 : echo "未支付";
                            break;
                        case 20 : echo "待发货";
                            break;
                        case 30 : echo "已签收";
                            break;
                        case 0 : echo "已取消";
                            break;
                    }
                    ?>
                </td>
                <td>
                    <a href="index.php?r=orders/detail&id=<?php echo $order['id']?>">查看订单</a>
                    <a href="#">取消订单</a>
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
