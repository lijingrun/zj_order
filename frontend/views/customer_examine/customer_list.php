<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/23
 * Time: 9:47
 */
?>
<script>
    function pass(id,type){
        $.ajax({
            type : "post",
            url : "index.php?r=customer_examine/pass",
            data : {"id" : id, "type" : type},
            success : function(data){
                if(data == 111){
                    alert("操作成功！");
                    location.reload();
                }else if(data == 222){
                    alert("服务器繁忙，请稍后重试");
                }else if(data == 333){
                    alert("商城客户已经存在");
                }else{
                    alert(data);
                }
            }
        });
    }
</script>
<div>
    <table class="table" >
        <tr>
            <th>客户名称</th>
            <th>业务员</th>
            <th>地址</th>
            <th>联系人</th>
            <th>客户级别</th>
            <th>客户编码</th>
            <th>操作</th>
        </tr>
        <?php foreach($customer as $val): ?>
        <tr>
            <td><?php echo $val['customer_name']?></td>
            <td><?php echo $val['user_id']['username']?></td>
            <td><?php echo $val['province'].$val['city'].$val['address']?></td>
            <td><?php echo $val['name']."(".$val['telephone'].")";?></td>
            <td><?php echo $val['type_id']['rank_name']?></td>
            <td><?php echo $val['customer_code']?></td>
            <td>
                <button class="btn-success" onclick="pass(<?php echo $val['id']?>,2);">通过</button>
                <button class="btn-danger" onclick="pass(<?php echo $val['id']?>,3)">不通过</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
