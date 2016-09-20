<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/24
 * Time: 9:58
 */
?>
<style>
    .form-control{
        width:300px;
    }
    h3{
        color : #00a2d4;
    }
</style>
<script>
    function get_customer_message(){
        var customer_id = $("#customer_id").val();
        if(customer_id != 0){
            $.ajax({
                type : 'post',
                url : 'index.php?r=invoice/get_customer',
                data : {'customer_id' : customer_id},
                success : function(data){
                    var return_data = JSON.parse(data);
                    $("#ban_no").val(return_data.ban+"("+return_data.ban_no+")");
                    $("#company_name").val(return_data.invoice);
                    $("#invoice_no").val(return_data.taxes);
                    $("#phone").val(return_data.phone);
                    $("#address").val(return_data.province+return_data.city+return_data.address);
                }
            });
        }
    }
    function check_data(){
        $("#form").submit();
    }
</script>
<div>

    <form class="form-horizontal" id="form" method="post">
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>客户</label>
            <div class="col-sm-10">
                <select name="customer_id" id="customer_id" class="form-control" style="width: 180px;" onchange="get_customer_message();">
                    <option valeu="0">请选择客户</option>
                    <?php foreach($customers as $customer): ?>
                    <option value="<?php echo $customer['id'] ?>">
                        <?php echo $customer['customer_name'];?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>发票类型</label>
            <div class="col-sm-10">
                <select name="invoice_type" id="customer_id" class="form-control" style="width: 180px;" >
                    <option value="普通票">普通票</option>
                    <option value="增值税票">增值税票</option>
                </select>
            </div>
        </div>
<!--        <div class="form-group">-->
<!--            <label class="col-sm-2 control-label"><span style="color:red;">*</span>发票号码</label>-->
<!--            <div class="col-sm-10">-->
<!--                <input type="text"  class="form-control" id="invoice_sn" name="invoice_sn" placeholder="发票号码">-->
<!--            </div>-->
<!--        </div>-->
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>发票点数（票面金额）</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text"  class="form-control" id="invoice_den" name="invoice_den" placeholder="发票点数">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="button">%</button>
                      </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>货款处理方式</label>
            <div class="col-sm-10">
                <select name="good_price_type" id="good_price_type" class="form-control" style="width: 180px;" >
                    <option value="开票单位汇款冲数">开票单位汇款冲数</option>
                    <option value="公司现金冲数">公司现金冲数</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>税金处理方式</label>
            <div class="col-sm-10">
                <select name="money_type" id="money_type" class="form-control" style="width: 180px;" >
                    <option value="记入应收账款">记入应收账款</option>
                    <option value="支付现金">支付现金</option>
                    <option value="单价已含税金">单价已含税金</option>
                </select>
            </div>
        </div>
        <h4>
            开票资料
        </h4>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>开票单位</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="company_name" name="company_name" placeholder="开票单位">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>物料名</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="goods_name" name="goods_name" placeholder="物料名">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>单价</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="price" name="price" placeholder="单价">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>数量</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="goods_numbers" name="goods_numbers" placeholder="数量">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>金额</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="goods_amount" name="goods_amount" placeholder="金额">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>应收税金</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="invoice_money" name="invoice_money" placeholder="应收税金">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>纳税人识别号</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="invoice_no" name="invoice_no" placeholder="纳税人识别号">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>电话</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="phone" name="phone" placeholder="电话">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>地址</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="address" name="address" placeholder="地址">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>开户银行以及账号</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" id="ban_no" name="ban_no" placeholder="开户银行以及账号">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><span style="color:red;">*</span>填票人</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control"  value="<?php echo $user['username'];?>" readonly="readonly">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" onclick="check_data();" class="btn btn-primary">保存</button>
                <a href="index.php?r=customer">
                    <button type="button" class="btn btn-default">取消</button>
                </a>
            </div>
        </div>
    </form>
</div>
