<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<script>
    function check_work(order_sn){
        if(confirm("是否确定已经验收完毕？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=worker/check_work',
                data : {'order_sn' : order_sn},
                success : function(data){
                    if(data == 111){
                        alert("审核成功！");
                        location.reload();
                    }else if(data == 222){
                        alert("您不能审核自己的工单！");
                    }else{
                        alert("系统错误，请稍后重试！");
                    }
                }
            });
        }
    }
</script>
<script language="JavaScript">
    function myrefresh(){
        window.location.reload();
    }
    setTimeout('myrefresh()',10000); //指定10秒刷新一次
</script>

<div class="site-index">

    <div class="jumbotron">
        <?php if(!empty($orders)){?>
        <table>
            <tr >
<!--                <th style="width:140px;">工单号</th>-->
                <th style="width:150px;" align='center'>车牌号码</th>
                <th style="width:250px;" align='center'>建单日期</th>
                <th style="width:200px;" align='center'>服务类型</th>
                <th style="width:180px;" align='center'>状态</th>
                <th style="width:100px;" align='center'>操作</th>
            </tr>
            <?php foreach($orders as $order): ?>
            <tr>
<!--                <th>--><?php //echo $order['order_no'];?><!--</th>-->
                <th><?php echo $order['car']['car_no'];?></th>
                <th><?php echo date('Y-m-d h:i:s',$order['create_time']);?></th>
                <th><?php echo $order['service_name'];?></th>
                <th>
                    <?php
                    switch($order['status']){
                    case 11 : echo '待开工';
                    break;
                    case 20 : echo '施工中';
                    break;
                    case 21 : echo '待审验';
                    break;
                    case 30 : echo '已完工';
                    break;
                    case 40 : echo '已付款';
                    break;
                    case 50 : echo '已评价';
                    break;
                    case 90 : echo '已取消';
                    break;
                    case 10 : echo '预约单';
                    break;
                    }
                    ?>
                </th>
                <th>
                    <?php
                    if($order['status'] == 11){
                    ?>
                        <?php if(empty($the_first)){
                            $the_first = 1;
                        ?>
                        <a href="index.php?r=worker/order_taking&id=<?php echo $order['order_no']?>"><input type="button" value="接单"></a>
                            <?php } ?>
                    <?php }elseif($order['status'] == 21){?>
                        <input onclick="check_work(<?php echo $order['order_no']?>);" type="button" value="验收">
                    <?php }?>
                </th>
            </tr>
            <?php endforeach;?>
        </table>
        <?php }else{?>
        <h2>暂时未有待开工工单！</h2>
        <?php }?>
    </div>

<!--    <div class="body-content">-->
<!---->
<!--        工单-->
<!---->
<!--    </div>-->
</div>
