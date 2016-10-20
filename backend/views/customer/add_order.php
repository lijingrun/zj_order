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
    function choose_clog_type(type){
        //客户要求送货的时候，直接获取客户地址信息
        if(type == 1){
            $("#address_list").show();
            $("#get_time").show();
        }else{
            $("#new_address").hide();
            $("#address_list").hide();
            $("#get_time").hide();
        }
    }
    function new_address(){
        $("#address_list").hide();
        $("#new_address").show();
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
    function get_district(){
        var city_id = $("#city").val();
        $("#district").html('');
        if(city_id != 0){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/get_city',
                data : {'id' : city_id},
                success : function(data){
                    $("#district").html(data);
                }
            });
            $.ajax({
                type : 'post',
                url : 'index.php?r=client/f_price',
                data : {'id' : city_id},
                success : function(data){
                    $("#f_price").val(data);
                }
            });
        }
    }
    function get_f_price(address_id){
        $.ajax({
            type : 'post',
            url : 'index.php?r=client/get_f_price',
            data : {'id' : address_id},
            success : function(data){
                $("#f_price").val(data);
            }
        });
    }
    function check_data(){
//        var province = $("#province").val();
//        var city = $("#city").val();
//        var address = $("#address").val();
//        var contacts = $("#contacts").val();
//        var phone = $("#phone").val();
//        if(province == 0 || city == 0 || address == '' || contacts == '' || phone == ''){
//            alert("请填写相关内容！");
//        }else{
        $("#form").submit();
//        }
    }
</script>
<div>
    <div>
        <h3>请填写相关信息</h3>
        <div style="font-size: 20px;">
            <form class="form-horizontal" id="form" method="post" id="form">
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span style="color:red;">*</span>送货方式：</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label onclick="choose_clog_type(2);">
                                <input type="radio" name="clog"  value="2" checked>
                                客户自提
                            </label>
                        </div>
                        <div class="radio">
                            <label onclick="choose_clog_type(1);">
                                <input type="radio" name="clog" value="1">
                                送货
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 收货地址列表 -->
                <div id="address_list" style="display: none;">
                    <?php if(!empty($address)){  ?>
                        <?php foreach($address as $val): ?>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="address_id"  value="<?php echo $val['address_id']?>" checked>
                                    <div class="alert alert-success" role="alert" role="alert" onclick="get_f_price(<?php echo $val['address_id']?>);">
                                        <p><?php echo $val['province'].$val['city'].$val['district'].$val['address']?></p>
                                        <p><?php echo $val['consignee']."(".$val['tel'].")"; ?></p>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div class="alert alert-success" role="alert" onclick="new_address();">
                            <strong>新增地址!</strong>
                        </div>
                    <?php }else{ ?>
                        <div class="alert alert-danger" role="alert" onclick="new_address();">
                            <strong>您还未添加过任何地址!</strong> 点击添加新地址
                        </div>
                    <?php } ?>
                </div>




                <!-- 新增地址 -->
                <div id="new_address" style="display: none;">
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
                            <select name="city" id="city" class="form-control" style="width: 180px;" onchange="get_district();">

                            </select>
                            <br />
                            <select name="district" id="district" class="form-control" style="width: 180px;">

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><span style="color:red;">*</span>详细地址：</label>
                        <div class="col-sm-10">
                            <input type="text"  class="form-control"  name="address" placeholder="详细地址">
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
                </div>
                <div id="get_time" style="display: none;">
                    <div class="form-group" >
                        <label class="col-sm-2 control-label">要求到货时间：</label>
                        <div class="col-sm-10">
                            <input type="date" name="get_time" />
                        </div>
                    </div>
                    <div class="form-group"  >
                        <label class="col-sm-2 control-label">运费：</label>
                        <div class="col-sm-10">
                            <input type="text" name="f_price" id="f_price" value="<?php echo $price_default;?>" readonly="readonly" style="width:80px;" />
                        </div>
                    </div>
                </div>
                <!-- 新增地址结束 -->

                <!--                <div class="form-group">-->
                <!--                    <label class="col-sm-2 control-label">卸车费用：</label>-->
                <!--                    <div class="col-sm-10">-->
                <!--                        <select name="unload_price" class="form-control" >-->
                <!--                            <option value="1">包含</option>-->
                <!--                            <option value="2">不包含</option>-->
                <!--                        </select>-->
                <!--                    </div>-->
                <!--                </div>-->

                <div class="form-group">
                    <label class="col-sm-2 control-label">付款方式：</label>
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
                        <button type="button" onclick="check_data();" class="btn btn-primary">提交订单</button>
                        <a href="index.php?r=orders">
                            <button type="button" class="btn btn-default">取消</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

