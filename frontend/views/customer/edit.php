<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 15:06
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
    function reset_password(user_id){
        if(confirm("是否将可以账号密码重置为‘123456’？")){
            $.ajax({
                type : 'post',
                url : "index.php?r=customer/reset_password",
                data : {'user_id' : user_id},
                success : function(data){
                    if(data == 111){
                        alert("重置成功！");
                    }else{
                        alert("服务器繁忙，请稍后重试！");
                    }
                }
            });
        }
    }
    function get_city(){
        var province_id = $("#province").val();
        $("#customer_code").val('');
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
        var customer_name = $("#customer_name").val().trim();
        var phone = $("#phone").val().trim();
        var ec_account = $("#ec_account").is(':checked');
        var customer_code = $("#customer_code").val();
        var new_password = $("#new_password").val();
        var con_password = $("#con_password").val();
        if(ec_account){
            var user_name = $("#user_name").val().trim();
            var password = $("#password").val();
            var c_password = $("#c_password").val();
            if(customer_name == '' || phone == '' || user_name == '' || password == ''){
                alert("请填写相关内容！");
            }else if(password != c_password){
                alert("2次密码输入不一致，请重新填写！");
            }else{
                $("#form").submit();
            }
        }else{
            if(customer_name == '' || phone == '' || license_id == '' || customer_code == ''){
                alert("请填写相关内容！");
            }else{
                if(new_password != con_password){
                    alert("2次输入的密码不一致！");
                }else{
                    $("#form").submit();
                }
            }
        }
    }
    function input_ec(){
        var ec_account = $("#ec_account").is(':checked');
        if(ec_account){
            $("#user_name").removeAttr("readonly");
            $("#password").removeAttr("readonly");
            $("#c_password").removeAttr("readonly");
        }else{
            $("#user_name").attr('readonly' , 'readonly');
            $("#password").attr('readonly' , 'readonly');
            $("#c_password").attr('readonly' , 'readonly');
        }
    }
    function get_code(){
        var province_id = $("#province").val();
        var city_id = $("#city").val();
        if(province_id > 0 && city_id > 0){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/get_code',
                data : {'province_id' : province_id, 'city_id' : city_id},
                success : function(data){
                    $("#customer_code").val(data);
                }
            });
        }else{
            alert("请选择客户所在省份和地区");
        }
    }
</script>
<div>
    <h3>基础资料</h3>
    <div>
        <form class="form-horizontal" id="form" method="post">
            <div class="form-group">
                <label class="col-sm-2 control-label"><span style="color:red;">*</span>客户名称</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="customer_name" value="<?php echo $customer['customer_name']?>" name="customer_name" placeholder="客户名称">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">新密码</label>
                <div class="col-sm-10">
                    <input type="password"  class="form-control" id="new_password" name="new_password" placeholder="新密码">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">确认密码</label>
                <div class="col-sm-10">
                    <input type="password"  class="form-control" id="con_password" name="con_password" placeholder="确认密码">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><span style="color:red;">*</span>营业执照号</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="license_id" name="license_id" value="<?php echo $customer['license_id']?>" placeholder="营业执照号">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">客户编码</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="customer_code" value="<?php echo $customer['customer_code']?>" name="customer_code" placeholder="客户编码">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">业务员</label>
                <div class="col-sm-10">
                    <select name="sale_id"  class="form-control" style="width: 180px;">
                        <!--                        <option value="0">请选择业务员</option>-->
                        <?php foreach($sales as $sale): ?>
                            <option value="<?php echo $sale['id']?>" <?php if($sale['id'] == $customer['user_id']){echo "selected";}?>>
                                <?php echo $sale['username'];?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">所在地区</label>
                <div class="col-sm-10">
                    <select name="province" id="province" class="form-control" style="width: 180px;" onchange="get_city();">
                        <option value="0">请选择客户所在省份</option>
                        <?php foreach($provinces as $province): ?>
                            <option value="<?php echo $province['region_id']?>" <?php if($province['region_id'] == $customer['province_id']){ echo "selected";}?>>
                                <?php echo $province['region_name'];?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br />
                    <select name="city" id="city" class="form-control" style="width: 180px;" onchange="get_code();">
                        <?php foreach($citys as $city): ?>
                        <option value="<?php echo $city['region_id']?>" <?php if($city['region_id'] == $customer['city_id']){ echo "selected";}?>>
                            <?php echo $city['region_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">详细地址</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="address" value="<?php echo $customer['address']?>" name="address" placeholder="详细地址">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">邮编</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="zip_code" name="zip_code" value="<?php echo $customer['zip_code']?>" placeholder="邮编">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><span style="color:red;">*</span>电话</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="phone" name="phone" value="<?php echo $customer['phone']?>" placeholder="电话">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">传真</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="fax" name="fax" value="<?php echo $customer['fax']?>" placeholder="传真">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">客户级别</label>
                <div class="col-sm-10">
                    <select id="type_id" name="type_id" class="form-control" style="width: 180px;">
                        <?php foreach($customer_types as $type): ?>
                            <option value="<?php echo $type['rank_id']?>" <?php if($type['rank_id'] == $customer['type_id']){echo "selected";}?>>
                                <?php echo $type['rank_name']?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">签约时间</label>
                <div class="col-sm-10">
                    <input type="date"   class="form-control" id="start_time" name="start_time" value="<?php echo date("Y-m-d",$customer['start_time']); ?>" >
                    <h4>到</h4>
                    <input type="date"  class="form-control" id="end_time" name="end_time" value="<?php echo date("Y-m-d",$customer['end_time']); ?>" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="name" value="<?php echo $customer['name']?>" name="name" placeholder="姓名">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">职位</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="position" name="position" value="<?php echo $customer['position']?>"  placeholder="职位">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">手机</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="telephone" name="telephone" value="<?php echo $customer['telephone']?>" placeholder="手机">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input type="email"  class="form-control" id="email" name="email" value="<?php echo $customer['email']?>" placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">QQ</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="qq" name="qq" value="<?php echo $customer['qq']?>" placeholder="QQ">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">物流编码</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="log_code" name="log_code" value="<?php echo $customer['log_code']?>" placeholder="物流编码">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">备用信息</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="spare" name="spare" value="<?php echo $customer['spare']?>" placeholder="备用信息">
                </div>
            </div>
<!--            --><?php //if(empty($customer['customer_id'])){ ?>
<!--            <div class="form-group">-->
<!--                <div class="col-sm-offset-2 col-sm-10">-->
<!--                    <div class="checkbox">-->
<!--                        <label>-->
<!--                            <input type="checkbox" id="ec_account" onclick="input_ec();" />-->
<!--                            <span style="color:red;">开通订货账号</span>-->
<!--                            (开通订货账号,代理商才能进入系统订货）-->
<!--                        </label>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-2 control-label">账号</label>-->
<!--                <div class="col-sm-10">-->
<!--                    <input type="text"  class="form-control" readonly="readonly" id="user_name" name="user_name" placeholder="账号">-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-2 control-label">密码</label>-->
<!--                <div class="col-sm-10">-->
<!--                    <input type="password"  class="form-control" readonly="readonly" id="password" name="password" placeholder="密码">-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-2 control-label">确认密码</label>-->
<!--                <div class="col-sm-10">-->
<!--                    <input type="password"  class="form-control" readonly="readonly" id="c_password" placeholder="确认密码">-->
<!--                </div>-->
<!--            </div>-->
<!--            --><?php //}else{ ?>
<!--                <div class="form-group">-->
<!--                    <label class="col-sm-2 control-label">账号</label>-->
<!--                    <div class="col-sm-10">-->
<!--                        <input type="text"  class="form-control" readonly="readonly" id="user_name" value="--><?php //echo $ecs_user['user_name'];?><!--" placeholder="账号">-->
<!--                    </div>-->
<!--                </div>-->
<!--            --><?php //} ?>
            <h3>财务信息</h3>
            <div class="form-group">
                <label class="col-sm-2 control-label">开户名称</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" value="<?php echo $customer['ban_name']?>" id="ban_name" name="ban_name" placeholder="开户名称">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">开户银行</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="ban"  value="<?php echo $customer['ban']?>" name="ban" placeholder="开户银行">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">银行账号</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="ban_no" name="ban_no"  value="<?php echo $customer['ban_no']?>" placeholder="银行账号">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">发票抬头</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="invoice"  value="<?php echo $customer['invoice']?>" name="invoice" placeholder="发票抬头">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">纳税人识别号</label>
                <div class="col-sm-10">
                    <input type="text"  class="form-control" id="taxes_no"  value="<?php echo $customer['taxes']?>" name="taxes" placeholder="纳税人识别号">
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
</div>
