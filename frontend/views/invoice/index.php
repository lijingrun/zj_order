<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/24
 * Time: 11:31
 */
use yii\widgets\LinkPager;
?>
<script>
    function pass(id,type){
        $.ajax({
            type : 'post',
            url : 'index.php?r=invoice/pass',
            data : {'id' : id , 'type' : type},
            success : function(data){
                if(data == 111){
                    location.reload();
                }
            }
        });
    }
    function find(){
        var type = $("#type").val();
        if(type == 0){
            location.href="index.php?r=invoice";
        }else{
            location.href="index.php?r=invoice&type="+type;
        }

    }
</script>
<div>
    <div style="padding:10px;" onchange="find();">
        审核情况
        <select id="type">
            <option value="0">全部</option>
            <option value="3" <?php if($type == 3){echo "selected";}?> >不通过</option>
            <option value="2" <?php if($type == 2){echo "selected";}?> >通过</option>
            <option value="1" <?php if($type == 1){echo "selected";}?>>未审核</option>
        </select>
    </div>
    <table class="table">
        <tr>
            <th>客户名称</th>
            <th>发票类型</th>
            <th>发票号</th>
            <th>发票点数</th>
            <th>货款处理方式</th>
            <th>金额</th>
            <th>税金处理方式</th>
            <th>纳税人识别号</th>
            <th>开户银行账号</th>
            <th>操作</th>
        </tr>
        <?php foreach($invoices as $val): ?>
        <tr>
            <td><?php echo $val['company_name']?></td>
            <td><?php echo $val['invoice_type']?></td>
            <td><?php echo $val['invoice_sn']?></td>
            <td><?php echo $val['invoice_den']?>%</td>
            <td><?php echo $val['good_price_type']?></td>
            <td><?php echo $val['goods_amount']?></td>
            <td><?php echo $val['money_type']?></td>
            <td><?php echo $val['invoice_no']?></td>
            <td><?php echo $val['ban_no']?></td>
            <td>
                <?php if($val['status'] == 0){ ?>
                <button class="btn-success" onclick="pass(<?php echo $val['id']?>,1);">通过</button>
                <button class="btn-danger" onclick="pass(<?php echo $val['id']?>,2)">不通过</button>
                <?php }elseif($val['status'] == 1){ ?>
                已通过
                <?php }else{ ?>
                不通过
                <?php } ?>
                <a href="index.php?r=invoice/detail&id=<?php echo $val['id']?>">
                    <button class="btn-info">查看</button>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="10">
                <?= LinkPager::widget(['pagination' => $pages]); ?>
            </td>
        </tr>
    </table>
</div>
