<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 21:08
 */
?>

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
            </td>
<!--            <td>--><?php //echo $user['store']['store_name']?><!--</td>-->
<!--            <td>-->
<!--                <a href="index.php?r=user/role&id=--><?php //echo $user['id']?><!--">权限设置</a>-->
<!--            </td>-->
        </tr>
        <?php endforeach; ?>
    </table>
</div>
