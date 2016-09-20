<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/24
 * Time: 13:38
 */
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
    function change_sn(){
        if(confirm("是否确定修改发票号？")){
            var new_sn = $("#invoice_sn").val();
            var invoice_id = <?php echo $invoice['id'];?>;
            if(new_sn != ''){
                $.ajax({
                    type : 'post',
                    url : 'index.php?r=invoice/change_invoice_sn',
                    data : {'new_sn' : new_sn , 'id' : invoice_id},
                    success : function(data){
                        if(data == 111){
                            alert("操作成功！");
                            location.reload();
                        }
                    }
                });
            }
        }
    }
</script>
<div>
    <div align="center">
        <h4>客户开票申请单</h4>
    </div>
    <ul class="list-group">
        <li class="list-group-item">
            发票类型：<?php echo $invoice['invoice_type'];?>
        </li>
        <li class="list-group-item">
            发票号：<input type="text" value="<?php echo $invoice['invoice_sn'];?>" id="invoice_sn" />
            <input type="button" value="修改" onclick="change_sn();" />
        </li>
        <li class="list-group-item">
            发票点数：<?php echo $invoice['invoice_den'];?>%
        </li>
        <li class="list-group-item">
            发票类型：<?php echo $invoice['invoice_type'];?>
        </li>
        <li class="list-group-item">
            货款处理方式：<?php echo $invoice['good_price_type'];?>
        </li>
        <li class="list-group-item">
            税金处理方式：<?php echo $invoice['money_type'];?>
        </li>
        <li class="list-group-item">
            开票单位：<?php echo $invoice['company_name'];?>
        </li>
        <li class="list-group-item">
            物料名：<?php echo $invoice['goods_name'];?>
        </li>
        <li class="list-group-item">
            单价：<?php echo $invoice['price'];?>
        </li>
        <li class="list-group-item">
            数量：<?php echo $invoice['goods_numbers'];?>
        </li>
        <li class="list-group-item">
            金额：<?php echo $invoice['goods_amount'];?>
        </li>
        <li class="list-group-item">
            应收税金：<?php echo $invoice['invoice_money'];?>
        </li>
        <li class="list-group-item">
            纳税人识别号：<?php echo $invoice['invoice_no'];?>
        </li>
        <li class="list-group-item">
            电话：<?php echo $invoice['phone'];?>
        </li>
        <li class="list-group-item">
            地址：<?php echo $invoice['address'];?>
        </li>
        <li class="list-group-item">
            开户银行及账号：<?php echo $invoice['ban_no'];?>
        </li>
        <li class="list-group-item">
            业务员：<?php echo $invoice['seal_id']['username'];?>
        </li>
        <li class="list-group-item">
            提交时间：<?php echo date("Y-m-d H:i:s",$invoice['add_time']);?>
        </li>
        <li class="list-group-item">
            状态：
            <?php
            switch($invoice['status']){
                case 0 : echo "未审核";
                    break;
                case 1 : echo "已审核";
                    break;
                case 2 : echo "审核不通过";
                    break;
            }
            ?>
        </li>
        <?php if($invoice['status'] > 0){ ?>
        <li class="list-group-item">
            审核人：<?php echo $invoice['examine_id']['username']?>
        </li>
            <li class="list-group-item">
                审核时间：<?php echo date("Y-m-d H:i:s",$invoice['examine_time']);?>
            </li>
        <?php }else{ ?>
        <li class="list-group-item">
            <button class="btn-success" onclick="pass(<?php echo $invoice['id']; ?>,1);">通过</button>
            <button class="btn-danger" onclick="pass(<?php echo $invoice['id']; ?>,2);" >不通过</button>
        </li>
        <?php } ?>
    </ul>
</div>
