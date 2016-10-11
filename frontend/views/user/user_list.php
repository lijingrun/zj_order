<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 21:08
 */
?>
<script>
    function del_user(id){
        if(confirm("是否确定删除账号？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=user/del_user',
                data : {'id' : id},
                success : function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }else if(data == 222){
                        alert("账号还有关联客户，需要取消关联之后才可以删除！");
                    }
                }
            });
        }
    }
</script>
<div>
    <?php  ?>
    <a href="index.php?r=user/add">添加账号</a>
    <?php ?>
    <a href="index.php?r=user/change_password" >修改个人密码</a>
</div>
<div>
    <table class="table">
        <tr>
            <th style="width:100px;">账号名</th>
            <th style="width:150px;">注册时间</th>
            <th style="width:100px;">权限分配</th>
<!--            <th style="width:100px;">所属店铺</th>-->
<!--            <th>操作</th>-->
        </tr>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?php echo $user['username']?></td>
            <td><?php echo date('Y-m-d',$user['created_at'])?></td>
            <td>
                <a href="index.php?r=user/rule&user_id=<?php echo $user['id'];?>">
                    设置权限
                </a>
                <a href="#" onclick="del_user(<?php echo $user['id'];?>);">
                    删除
                </a>
                <a href="index.php?r=user/edit&id=<?php echo $user['id']?>">
                    修改
                </a>
            </td>
<!--            <td>--><?php //echo $user['store']['store_name']?><!--</td>-->
<!--            <td>-->
<!--                <a href="index.php?r=user/role&id=--><?php //echo $user['id']?><!--">权限设置</a>-->
<!--            </td>-->
        </tr>
        <?php endforeach; ?>
    </table>
</div>
