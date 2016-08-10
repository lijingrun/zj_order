<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 21:20
 */
?>
<style>
    li{
        float: left;
        list-style-type:none;
    }
    span{
        padding:10px;
    }
</style>
<div style="padding-top:50px;">
    包含权限
    <form method="post">
        <ul>
            <li>
                <span><input type="checkbox" name="orders" <?php if($role['orders'] == 'on'){ echo 'checked';}?>>工单</span>
            </li>
            <li>
                <span><input type="checkbox" name="member" <?php if($role['member'] == 'on'){ echo 'checked';}?>>会员</span>
            </li>
            <li>
                <span><input type="checkbox" name="car" <?php if($role['car'] == 'on'){ echo 'checked';}?>>车辆信息</span>
            </li>
            <li>
                <span><input type="checkbox" name="goods" <?php if($role['goods'] == 'on'){ echo 'checked';}?>>产品</span>
            </li>
            <li>
                <span><input type="checkbox" name="service" <?php if($role['service'] == 'on'){ echo 'checked';}?>>服务</span>
            </li>
            <li>
                <span><input type="checkbox" name="store" <?php if($role['store'] == 'on'){ echo 'checked';}?>>店铺</span>
            </li>
            <li>
                <span><input type="checkbox" name="worker" <?php if($role['worker'] == 'on'){ echo 'checked';}?>>工人</span>
            </li>
            <li>
                <span><input type="checkbox" name="user" <?php if($role['user'] == 'on'){ echo 'checked';}?>>账号</span>
            </li>
            <li>
                <span><input type="checkbox" name="statistics" <?php if($role['statistics'] == 'on'){ echo 'checked';}?>>统计</span>
            </li>
        </ul>
        <input type="hidden" value="1" name="user_id" />
        <input type="submit" value="修改" />
    </form>
</div>
