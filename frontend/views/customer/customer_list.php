<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 15:05
 */
use yii\widgets\LinkPager;
?>
<script>
    function del_customer(id){
        if(confirm("是否确定删除该客户？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/del_customer',
                data : {'id' : id},
                success : function(data){
                    return_data = JSON.parse(data);
                    if(return_data.error_code == 0){
                        alert(return_data.error_data);
                        location.reload();
                    }else{
                        alert(return_data.error_data);
                    }
                }
            });
        }
    }
</script>

<div align="right">
    <a href="index.php?r=customer/add">
        <button class="btn-primary">添加新客户</button>
    </a>
    <a href="index.php?r=customer/type">
        <button class="btn-primary">客户等级</button>
    </a>

</div>
<div>

</div>
<div style="padding-top: 30px;">
    <table class="table table-hover">
        <tr>
            <th>客户名称</th>
<!--            <th>登陆账号</th>-->
            <th>业务员</th>
            <th>地区</th>
            <th>级别</th>
            <th>联系人</th>
            <th>联系方式</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php foreach($customers as $customer): ?>
        <tr>
            <td>
                <a href="index.php?r=customer/detail&id=<?php echo $customer['id'];?>">
                <?php echo $customer['customer_name'];?>
                </a>
            </td>
<!--            <td>--><?php //echo $customer['customer_id']['user_name'];?><!--</td>-->
            <td><?php echo $customer['user_id']['username'];?></td>
            <td><?php echo $customer['province'].$customer['city'];?></td>
            <td><?php echo $customer['type_id']['rank_name']."(折扣:".$customer['type_id']['discount']."%)";?></td>
            <td><?php echo $customer['name'];?></td>
            <td><?php echo $customer['telephone'];?></td>
            <td>
                <?php
                    switch($customer['status']){
                        case 1 : echo '待审客户';
                            break;
                        case 2 : echo "正常客户";
                            break;
                        case 3 : echo "审核不通过客户";
                            break;
                        case 0 : echo "已删除客户";
                            break;
                        default : echo "未知状态客户";
                            break;
                    }
                ?>
            </td>
            <td>
                <a href="index.php?r=customer/edit&id=<?php echo $customer['id'];?>">修改</a>
                <a href="#" onclick="del_customer(<?php echo $customer['id'];?>);">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="8" align="center">
                <?= LinkPager::widget(['pagination' => $pages]); ?>
            </td>
        </tr>
    </table>
</div>
