<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/17
 * Time: 17:24
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
    function add_goods(){
        var goods_id = $("#goods_id").val();
        if(goods_id != 0){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/add_to_cart',
                data : {'goods_id' : goods_id},
                success : function(data){
                    if(data == 111){
                        get_cart_data();
                    }
                }
            });
        }
        get_cart_data();
    }
    function get_cart_data(){
        var customer_id = $("#customer_id").val();
        $.ajax({
            type : 'post',
            url : 'index.php?r=orders/get_cart_data',
            data : {'customer_id' : customer_id},
            success : function(data){
                $("#goods_data").html(data);
            }
        });
    }
    function del_cart(id){
        if(confirm("是否确定取消？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/del_cart',
                data : {'id' : id},
                success : function(data){
                    if(data == 111){
                        get_cart_data();
                    }
                }
            });
        }
    }
    function to_change_nums(id){
        var input_htm = "<input type='text' style='width:40px;' id='new_nums'onblur='change_nums("+id+");'>";
        $("#cart"+id).html(input_htm);
    }
    function change_nums(id){
        var new_num = $("#new_nums").val().trim();
        if(new_num != '' && !isNaN(new_num)) {
            $.ajax({
                type: 'post',
                url: 'index.php?r=orders/change_cat_num',
                data: {'id': id , 'new_num' : new_num},
                success: function (data) {
                    if (data == 111) {
                        get_cart_data();
                    }
                }
            });
        }else{
            alert("请输入正确内容");
        }
    }
    function get_city(){
        var province_id = $("#province").val();
        $("#city").html('');
        if(province_id != 0){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/get_city',
                data : {'id' : province_id},
                success : function(data){
                    $("#city").html(data);
                }
            });
        }
    }
    function check_data(){
        var province = $("#province").val();
        var city = $("#city").val();
        var address = $("#address").val();
        var contacts = $("#contacts").val();
        var phone = $("#phone").val();
        if(province == 0 || city == 0 || address == '' || contacts == '' || phone == ''){
            alert("请填写相关内容！");
        }else{
            $("#form").submit();
        }
    }
    function change_customer(){
        get_cart_data();
    }
</script>
<div>
    <div>
        <h3>订单信息</h3>
        <div>
            <form class="form-horizontal" id="form" method="post" id="form">
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>客户：</label>
                    <div class="col-sm-10">
                        <input type="text" readonly="readonly"  class="form-control" value="<?php echo $customer['customer_name']?>" />
                        <input type="hidden" value="<?php echo $customer['id']?>" name="customer_id"  />
                    </div>
                </div>
                <h3>发货信息</h3>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>发货地址：</label>
                    <div class="col-sm-10">
                        <select name="province" id="province" class="form-control" style="width: 180px;" onchange="get_city();">
                            <option value="0">请选择客户所在省份</option>
                            <?php foreach($provinces as $province): ?>
                                <option value="<?php echo $province['region_id']?>" <?php if($province['region_id'] == $customer['province_id']){echo "selected";}?> >
                                    <?php echo $province['region_name'];?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br />
                        <select name="city" id="city" class="form-control" style="width: 180px;">
                            <?php if(!empty($citys)){ ?>
                                <?php foreach($citys as $val): ?>
                                <option value="<?php echo $val['region_id']?>" <?php if($val['region_id'] == $customer['city_id']){echo "selected";} ?>>
                                    <?php echo $val['region_name']?>
                                </option>
                                <?php endforeach; ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>详细地址：</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" value="<?php echo $customer['address']?>"  name="address" placeholder="详细地址">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>联系人：</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="contacts" value="<?php echo $customer['name']?>" name="contacts" placeholder="联系人">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>联系电话：</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="phone" name="phone" value="<?php echo $customer['phone']?>" placeholder="联系电话">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">出货形式：</label>
                    <div class="col-sm-10">
                        <select name="clog" class="form-control" >
                            <option value="1">送货</option>
                            <option value="2">自提</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">卸车费用：</label>
                    <div class="col-sm-10">
                        <select name="unload_price" class="form-control" >
                            <option value="1">包含</option>
                            <option value="2">不包含</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">要求到货时间：</label>
                    <div class="col-sm-10">
                        <input type="date" name="get_time" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">收款方式：</label>
                    <div class="col-sm-10">
                        <select name="pay_type" class="form-control" >
                            <option value="1">现销</option>
                            <option value="2">赊销</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>订单备注：</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control"  name="remarks" placeholder="订单备注">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" onclick="check_data();" class="btn btn-primary">保存</button>
                        <a href="index.php?r=orders">
                            <button type="button" class="btn btn-default">取消</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

