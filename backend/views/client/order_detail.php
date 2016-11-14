<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'MicroMessenger') === false) {
    //非微信浏览器打开
}else {
    //微信打开
    ini_set('date.timezone', 'Asia/Shanghai');
    error_reporting(E_ERROR);
    require_once dirname(__FILE__) . "/../../weixin_pay/lib/WxPay.Api.php";
    require_once dirname(__FILE__) . "/../../weixin_pay/example/WxPay.JsApiPay.php";
    require_once dirname(__FILE__) . '/../../weixin_pay/example/log.php';

//初始化日志
    $logHandler = new CLogFileHandler(dirname(__FILE__) . "/../../weixin_pay/logs/" . date('Y-m-d') . '.log');
    $log = Log::Init($logHandler, 15);

//打印输出数组信息
    function printf_info($data)
    {
        foreach ($data as $key => $value) {
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }

//
////①、获取用户openid
    $tools = new JsApiPay();
    $openId = $tools->GetOpenid();
//
////②、统一下单
    $input = new WxPayUnifiedOrder();
    $input->SetBody("中建科技");
    $input->SetAttach("中建商品金额");
    $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
    $pay = round($order['order_amount']*100);
    $input->SetTotal_fee($pay);  //款项
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("中建商品金额");
    $member_id = Yii::$app->session['member_id'];
    $pay_notify_url = "";
    $input->SetNotify_url($pay_notify_url);
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openId);
    $order = WxPayApi::unifiedOrder($input);
// echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
// printf_info($order);
    $jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
    $editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
    /**
     * 注意：
     * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
     * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
     * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
     */
}
?>
<script type="text/javascript">
    function to_pay(order_id){
        var member_id = "<?php echo Yii::$app->session['member_id'];?>";
        if(member_id == ""){
            location.href="index.php?r=member_login/login";
        }else{
            callpay();
        }
    }

    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $jsApiParameters; ?>,
            function(res){
                WeixinJSBridge.log(res.err_msg);
//                    alert(res.err_code+res.err_desc+res.err_msg);
                if(res.err_msg == "get_brand_wcpay_request:ok"){
                    alert("支付成功！");

                }else{
                    alert("支付失败，请重新支付！");
                }
            }
        );
    }

    function callpay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
</script>
<script type="text/javascript">
    //获取共享地址
    function editAddress()
    {
        // WeixinJSBridge.invoke(
        //     'editAddress',
        //     <?php echo $editAddress; ?>,
        //     function(res){
        //         var value1 = res.proviceFirstStageName;
        //         var value2 = res.addressCitySecondStageName;
        //         var value3 = res.addressCountiesThirdStageName;
        //         var value4 = res.addressDetailInfo;
        //         var tel = res.telNumber;

        //         alert(value1 + value2 + value3 + value4 + ":" + tel);
        //     }
        // );
    }

    window.onload = function(){
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', editAddress, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', editAddress);
                document.attachEvent('onWeixinJSBridgeReady', editAddress);
            }
        }else{
            editAddress();
        }
    };

</script>
<script>
    function del_order(id){
        if(confirm("是否确定作废订单？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=orders/del',
                data : {'id' : id},
                success : function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }else{
                        alert("服务器繁忙，请稍后重试！");
                    }
                }
            });
        }
    }
    function cancel_order(id){
        if(confirm("是否确定取消订单？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=client/cancel_order',
                data : {'id' : id},
                success : function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }else{
                        alert("订单处于不能取消状态/您没有权限取消该订单！");
                    }
                }
            });
        }
    }
</script>
<div>
    <div class="panel panel-info">
        <div class="panel-body">
            <p>公司名称：<?php echo $customer['customer_name'];?></p>
            <p>订单编号：<?php echo $order['order_sn'];?></p>
            <p>订单状态：
            <?php
            switch($order['order_status']){
                case 0 : echo "未确认";
                    break;
                case 1 : echo "已确认";
                    break;
                case 2 : echo "<span style='color:red;'>已取消</span>";
                    break;
            }
            echo "|";
            switch($order['pay_status']){
                case 0 : echo "未支付";
                    break;
                case 1 : echo "支付中";
                    break;
                case 2 : echo "已支付";
                    break;
            }
            echo "|";
            switch($order['shipping_status']){
                case 0 : echo "未发货";
                    break;
                case 1 : echo "已发货";
                    break;
                case 2 : echo "收获确认";
                    break;
            }
            ?>
            </p>
            <p>付款方式：<?php if($order['customer_pay'] == 1){ echo "现销";}else{ echo "赊销";};?></p>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <p>商品总金额：￥<?php echo $order['goods_amount'];?></p>
            <p>运费：￥<?php echo $order['clog_price'];?></p>
            <?php if($order['discount'] > 0){
              echo "<p style='color:red;'>整单优惠：￥".$order['discount']."</p>";
            }
            ?>
            <p>订单总金额：￥<?php echo $order['order_amount'];?></p>

        </div>
    </div>

    <div class="panel panel-info">
        <a data-toggle="modal" data-target="#myModal">
            <div class="panel-body">
                <ul  class="nav nav-pills" role="tablist">
                    <li role="presentation">
                        商品清单
                        <span class="badge">
                            <?php echo $count;?>
                        </span>
                    </li>
                </ul>
            </div>
        </a>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <p>备注信息：<?php echo empty($order['how_oos']) ?'无':$order['how_oos'];?></p>
        </div>
    </div>
    <?php if($order['shipping_id'] == 1){ ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">收货信息</h3>
        </div>
        <div class="panel-body">
            <p>收货地址：<?php echo $order['address']?></p>
            <p>收货人：<?php echo $order['consignee']?></p>
            <p>电话：<?php echo $order['tel']?></p>
<!--            <p>送货方式：--><?php //echo $order['shipping_name']?><!--</p>-->
        </div>
    </div>
        <?php if(!empty($order['get_time'])){ ?>
        <div class="panel panel-info">
            <div class="panel-body">
                <p>要求交货日期：<?php echo $order['get_time'];?></p>
            </div>
        </div>
        <?php } ?>
    <?php }else{ ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">客户自提</h3>
        </div>
    </div>
    <?php } ?>
    <!-- good_list -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">商品列表</h4>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>商品</th>
                            <th>单价</th>
                            <th>数量</th>
                            <th>金额</th>
                        </tr>
                        <?php foreach($goods as $good): ?>
                        <tr>
                            <td><?php echo $good['goods_name'];?></td>
                            <td><?php echo $good['goods_price'];?></td>
                            <td><?php echo $good['goods_number'];?></td>
                            <td><?php echo ($good['goods_number']*$good['goods_price']);?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4" align="right">
                                小计：￥<?php echo $order['goods_amount'];?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
<!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        </div>
    </div>
    <div align="right">
        <?php if($order['pay_status'] == 0 && $order['shipping_status'] == 0 && $order['order_status'] != 2){ ?>
        <input type="button" class="btn-danger" style="font-size: 25px;" value="取消" onclick="cancel_order(<?php echo $order['order_id'];?>);"  />
            &nbsp;&nbsp;
        <?php } ?>

        <?php if($order['pay_status'] == 0 && $order['customer_pay'] == 1){ ?>
            <input type="button" class="btn-success" style="font-size: 25px;" value="微信支付" onclick="to_pay(<?php echo $order['order_id'];?>);"  />
            &nbsp;&nbsp;
        <?php } ?>
        <?php if($order['shipping_status'] == 1 && $order['order_status'] == 1){ ?>
            <input type="button" class="btn-success" style="font-size: 25px;" value="确认收货" onclick="get_goods(<?php echo $order['order_id'];?>);"  />
            &nbsp;&nbsp;
        <?php } ?>
            <input type="button" value="返回" class="btn-info" style="font-size: 25px;" onClick="javascript:history.go(-1);" />
    </div>
</div>
<script>
    function get_goods(order_id){
        if(confirm("是否确认收到货？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=client/get_goods',
                data : {'order_id' : order_id},
                success : function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }else{
                        alert("你没有权限操作搞订单");
                    }
                }
            });
        }
    }
</script>