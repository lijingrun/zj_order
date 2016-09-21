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
    function sales_ex(){
        $("#sales_examine").show();
        $("#customer_examine").hide();
//        $("#s_btn").attr("class",'btn-primary');
//        $("#s_btn").attr("class",'btn-default');
    }
    function customer_ex(){
        $("#sales_examine").hide();
        $("#customer_examine").show();
//        $("#s_btn").attr("class",'btn-default');
//        $("#s_btn").attr("class",'btn-primary');
    }
    function del_user(user_id){
        if(confirm("是否确定删除客户？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer_examine/del_customer',
                data : {'user_id' : user_id},
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
    <div style="padding:10px;">
        <button id="s_btn" class="btn-primary" onclick="sales_ex();">业务员新增&nbsp;&nbsp;<span class="badge"><?php echo $s_count;?></span></button>
        <button id="c_btn" class="btn-primary"  onclick="customer_ex();">客户自建&nbsp;&nbsp;<span class="badge"><?php echo $c_count;?></span></button>
    </div>
    <div id="sales_examine">
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
                    <a href="index.php?r=customer_examine/detail&id=<?php echo $val['id'];?>">
                        <button class="btn-info">查看详细</button>
                    </a>
                    <button class="btn-success" onclick="pass(<?php echo $val['id']?>,2);">通过</button>
                    <button class="btn-danger" onclick="pass(<?php echo $val['id']?>,3)">不通过</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div id="customer_examine" style="display: none;">
        <table class="table" >
            <tr>
                <th>客户名称</th>
                <th>营业执照号</th>
                <th>联系人</th>
                <th>联系电话</th>
                <th>联系地址</th>
                <th>操作</th>
            </tr>
            <?php foreach($user_exa as $val): ?>
            <tr>
                <td><?php echo $val['user_name'];?></td>
                <td><?php echo $val['license_id'];?></td>
                <td><?php echo $val['contacts'];?></td>
                <td><?php echo $val['telephone'];?></td>
                <td><?php echo $val['address'];?></td>
                <td>
                    <a href="index.php?r=customer_examine/perfect&user_id=<?php echo $val['user_id']?>">完善客户资料</a>
                    <a onclick="del_user(<?php echo $val['user_id']?>);">删除记录</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
