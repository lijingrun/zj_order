<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/17
 * Time: 9:01
 */
?>
<style>
    .data-div{
        border-bottom: solid 1px #00b3ee;
        padding:10px;
    }
</style>
<div>
    <div style="padding:10px;font-size: 18px;">
        <div class="data-div">客户名称：<div style="float: right;padding-right: 10px;"><?php echo $customer['customer_name'];?></div></div>
        <div class="data-div">客户等级：<div style="float: right;padding-right: 10px;"><?php echo $rank['rank_name'];?></div></div>
        <div class="data-div">详细地址：
            <div >
                <?php echo $customer['province'].$customer['city'].$customer['address'];?>
            </div>
        </div>
        <div class="data-div">开通账号：<div style="float: right;padding-right: 10px;"><?php echo $user_name;?></div></div>
        </div>
    <div class="data-div"><span style="font-size: 18px;padding-left: 10px;">联系人名片：</span>
        <div class="panel panel-info" style="margin:10px;">
            <div class="panel-body">
                <strong><?php echo $customer['name']?></strong><br>
                职位：<?php echo $customer['position']?><br/>
                手机：<?php echo $customer['telephone']?><br>
                Email：<?php echo $customer['email']?><br>
                QQ:<?php echo $customer['qq']; ?>
            </div>
        </div>
    </div>
    <div style="padding:10px;font-size: 18px;">
        <div class="data-div">开户名称：<div style="float: right;padding-right: 10px;"><?php echo $customer['ban_name'];?></div></div>
        <div class="data-div">开户银行：<div style="float: right;padding-right: 10px;"><?php echo $customer['ban'];?></div></div>
        <div class="data-div">银行账号：<div style="float: right;padding-right: 10px;"><?php echo $customer['ban_no'];?></div></div>
        <div class="data-div">纳税号：<div style="float: right;padding-right: 10px;"><?php echo $customer['taxes_no'];?></div></div>
        <div class="data-div">发票抬头：<div style="float: right;padding-right: 10px;"><?php echo $customer['invoice'];?></div></div>
        <div class="data-div">客户编码：<div style="float: right;padding-right: 10px;"><?php echo $customer['customer_code'];?></div></div>
        <div class="data-div">签约时间：<div style="float: right;padding-right: 10px;"><?php echo date("Y-m-d",$customer['start_time']);?></div></div>
        <div class="data-div">公司电话：<div style="float: right;padding-right: 10px;"><?php echo $customer['phone'];?></div></div>
        <div class="data-div">公司传真：<div style="float: right;padding-right: 10px;"><?php echo $customer['fax'];?></div></div>
        <div style="padding-top: 20px;">
            <a href="index.php?r=customer/edit&id=<?php echo $customer['id']?>">
                <input type="button" value="修改客户资料" class="btn-success" />
            </a>
            <a href="index.php?r=orders&id=<?php echo $customer['id']?>">
                <input type="button" value="查看客户订单" class="btn-info" />
            </a>
        </div>
    </div>
</div>
