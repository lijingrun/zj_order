<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/11
 * Time: 14:19
 */

namespace backend\controllers;
use common\models\Address;
use common\models\Customer;
use common\models\Customer_cart;
use common\models\Customer_type;
use common\models\Ecs_user;
use common\models\Freight;
use common\models\Goods;
use common\models\Member_price;
use common\models\Order_goods;
use common\models\Order_info;
use common\models\Promotion;
use common\models\Promotion_goods;
use common\models\Region;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class ClientController extends Controller{
    public $enableCsrfValidation = false;
    public $layout = 'customer_mobile';

    public function beforeAction($action)
    {
        if(empty(Yii::$app->session['customer_id'])){
            return $this->redirect("index.php?r=site/customer_login");
        }else{
            return $action;
        }
    }

    public function actionIndex(){


        return $this->render("index");
    }

//    public function actionLogin(){
//        if(Yii::$app->request->post()){
//            $user_name = trim($_POST['user_name']);
//            $password = md5(trim($_POST['password']));
//            $customer = Ecs_user::find()->where("user_name like '".$user_name."'")->andWhere("password like '".$password."'")->asArray()->one();
//            if(empty($customer)){
//                Yii::$app->getSession()->setFlash('error','账号/密码错误！');
//                return $this->redirect('index.php?r=client/login');
//            }else{
//                Yii::$app->session['customer_id'] = $customer['user_id'];
//                Yii::$app->session['user_name'] = $customer['user_name'];
//                return $this->redirect("index.php?r=client");
//            }
//        }else{
//            return $this->render('login');
//        }
//    }

    public function actionLogout(){
        Yii::$app->session['customer_id'] = null;
        Yii::$app->session['user_name'] = null;
        return $this->redirect("index.php?r=site/customer_login");
    }

    public function actionReset_password(){
        if(Yii::$app->request->post()){
            $new_password = md5($_POST['new_password']);
            $customer_id = Yii::$app->session['customer_id'];
            $customer = Ecs_user::find()->where("user_id =".$customer_id)->one();
            $customer->password = $new_password;
            if($customer->save()){
                return $this->redirect("index.php?r=client");
            }
        }else{

            return $this->render("reset_password");
        }
    }

    public function actionGoods(){
        $all_goods = Goods::find();
        $pages = new Pagination(['totalCount' => $all_goods->count(), 'pageSize' => '10']);
        $goods = $all_goods->offset($pages->offset)->limit($pages->limit)->all();
        //查等级，显示等级价格
        $customer_id = Yii::$app->session['customer_id'];
        $user = Ecs_user::find()->where("user_id =".$customer_id)->asArray()->one();
        $user_rank = $user['user_rank'];
        foreach($goods as $key=>$good):
            echo $good['goods_id'];
            $goods_promotion = $this->get_promotion($good['goods_id'],$user_rank);
            if(!empty($goods_promotion)){
                $goods[$key]['seller_note'] = $goods_promotion;
            }
            $m_price = Member_price::find()->where("goods_id =".$good['goods_id'])->andWhere("user_rank =".$user_rank)->asArray()->one();
            if(!empty($m_price)) {
                $goods[$key]['shop_price'] = $m_price['user_price'];
            }
        endforeach;
        return $this->render('goods_list',[
            'goods' => $goods,
            'pages' => $pages,
        ]);
    }

    public function actionGoods_detail(){
        $id = $_GET['id'];
        $goods = Goods::find()->where("goods_id =".$id)->asArray()->one();
        //查客户等级价钱
        $customer = Ecs_user::find()->where("user_id = ".Yii::$app->session['customer_id'])->asArray()->one();
        $rank = Customer_type::find()->where("rank_id =".$customer['user_rank'])->asArray()->one();
            $data = array();
            $data['rank_name'] = $rank['rank_name'];
            $m_price = Member_price::find()->where("goods_id =".$id)->andWhere("user_rank =".$rank['rank_id'])->asArray()->one();
            if(!empty($m_price)){
                $data['user_price'] = $m_price['user_price'];
            }else{
                $data['user_price'] = $goods['shop_price']*($rank['discount']/100);
            }
            $member_price[] = $data;
        return $this->render("goods_detail",[
            'goods' => $goods,
            'member_price' => $member_price,
        ]);


    }

    public function actionMy_orders(){
        $customer_id = Yii::$app->session['customer_id'];
        $all_data = Order_info::find()->where("user_id =".$customer_id)->orderBy("add_time desc");
        $pages = new Pagination(['totalCount' => $all_data->count(), 'pageSize' => '10']);
        $orders = $all_data->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render("order_list",[
            'orders' => $orders,
            'pages' => $pages,
        ]);
    }

    //订单详情
    public function actionOrder_detail(){
        $id = $_GET['id'];
        $order = Order_info::find()->where("order_id =".$id)->asArray()->one();
        $goods = Order_goods::find()->where("order_id =".$id)->asArray()->all();
        $customer['customer_name'] = Yii::$app->session['user_name'];
        $count = count($goods);
        return $this->render('order_detail',[
            'order' => $order,
            'goods' => $goods,
            'customer' => $customer,
            'count' => $count,
        ]);
    }

    //客户取消订单
    public function actionCancel_order(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $order = Order_info::find()->where("order_id =".$id)->one();
            if($order->user_id == Yii::$app->session['customer_id'] && $order->pay_status == 0 && $order->shipping_status == 0 && $order->order_status !=2){
                $order->order_status = 2;
                if($order->save()){
                    echo 111;
                }else{
                    echo 222;
                }
            }else{
                echo 222;
            }
            exit;
        }
    }

    //客户数据统计
    public function actionStatistic(){
        $customer_id = Yii::$app->session['customer_id'];
        $year = empty($_GET['year']) ? date("Y",time()) : $_GET['year'];
        $mount = empty($_GET['mount']) ? date("m",time()) : $_GET['mount'];
        $start_time = strtotime($year."-".$mount."-01");
        $end_time = strtotime($year."-".($mount+1)."-01");
        //查时间断内已经支付了的订单
        $orders = Order_info::find()->where("user_id =".$customer_id)->andWhere("add_time >=".$start_time)->andWhere("add_time <".$end_time)->andWhere("pay_status = 2")->asArray()->all();
        $total_price = 0;
        $order_ids = array();
        foreach($orders as $order):
            $total_price += $order['order_amount'];
            $order_ids[] = $order['order_id'];
        endforeach;
        if(!empty($order_ids)) {
            $order_ids_str = implode(',', $order_ids);
            //购买的商品
            $order_goods = Order_goods::findBySql("select sum(goods_number) as total,goods_name from ecs_order_goods where order_id in(" . $order_ids_str . ") AND is_gift=0 GROUP BY goods_name;")->asArray()->all();
            //赠送的商品
            $gift_goods = Order_goods::findBySql("select sum(goods_number) as total,goods_name from ecs_order_goods where order_id in(" . $order_ids_str . ") AND is_gift=1 GROUP BY goods_name;")->asArray()->all();
        }
        return $this->render("statistic",[
            'mount' => $mount,
            'year' => $year,
            'total_price' => $total_price,
            'order_goods' => $order_goods,
            'gift_goods' => $gift_goods,
        ]);

    }

    //加入购物车
    public function actionAdd_to_cart(){
        if(Yii::$app->request->post()){
            $goods_id = $_POST['goods_id'];
            $number = $_POST['number'];
            $customer_id = Yii::$app->session['customer_id'];
            $ec_customer = Customer::find()->where("customer_id =".$customer_id)->asArray()->one();
            $cart_check = Customer_cart::find()->where("goods_id =".$goods_id)->andWhere("customer_id =".$ec_customer['id'])->count();
            $goods = Goods::find()->where("goods_id =".$goods_id)->asArray()->one();
            if($cart_check > 0){
                echo 222;
                exit;
            }else{
                $new_cart = new Customer_cart();
                $new_cart->goods_id = $goods_id;
                $new_cart->customer_id = $ec_customer['id'];
                $new_cart->goods_name = $goods['goods_name'];
                $new_cart->nums = $number;
                $new_cart->user_id = $ec_customer['user_id'];
                $customer = Customer::find()->where("customer_id =".Yii::$app->session['customer_id'])->asArray()->one();
                $promotion = $this->get_promotion($goods_id,$customer['type_id']);
                if(!empty($promotion)){
                    foreach($promotion as $val){
                        switch($val['type']){
                            //满送才增加数量，其他涉及价钱的在购物车里面体现
                            case 1 :
                                if($number >= $val['number']){
                                    $coe = (floor($number/$val['number']))*$val['coefficient'];
                                    $gift = new Customer_cart();
                                    $gift->goods_id = $goods_id;
                                    $gift->goods_name = $goods['goods_name']."(赠送)";
                                    $gift->user_id = $customer['user_id'];
                                    $gift->customer_id = $customer['id'];
                                    $gift->nums = $coe;
                                    $gift->is_gift = 1;
                                }
                                break;
                        }
                    }
                }
                if(!empty($gift)){
                    if($gift->save() && $new_cart->save()){
                        echo 111;
                    }else{
                        echo 333;
                    }
                }else{
                    if($new_cart->save()){
                        echo 111;
                    }else{
                        echo 333;
                    }
                }
                exit;
            }
        }
    }

    //购物车
    public function actionMy_cart(){
        $customer_id = Yii::$app->session['customer_id'];
        $customer = Customer::find()->where("customer_id =".$customer_id)->asArray()->one();
        $carts = Customer_cart::find()->where("customer_id =".$customer['id'])->asArray()->all();
        if(empty($carts)){
            Yii::$app->getSession()->setFlash('success','亲，您的车还是空的，看看装点什么吧');
            return $this->redirect("index.php?r=client");
        }
        $rank = Customer_type::find()->where("rank_id =".$customer['type_id'])->asArray()->one();
        $total_price = 0;
        $cart_data = array();
        foreach($carts as $cart):
            $cart_goods = array();
            $cart_goods['goods_name'] = $cart['goods_name'];
            $cart_goods['cart_id'] = $cart['id'];
            $goods = Goods::find()->where("goods_id =".$cart['goods_id'])->asArray()->one();
            $cart_goods['goods_img'] = $goods['goods_img'];
            //查价钱
            $member_price = Member_price::find()->where("goods_id =".$cart['goods_id'])->andWhere("user_rank =".$customer['type_id'])->asArray()->one();
            if($cart['is_gift']){
                $price = 0;
            }else{
                if(!empty($member_price['user_price'])){
                    $price = $member_price['user_price'];
                }else{
                    $price = $goods['shop_price']*($rank['discount']/100);
                }
            }
            $cart_goods['goods_num'] = $goods['goods_number'];
            $cart_goods['price'] = $price;
            $cart_goods['num'] = $cart['nums'];
            $cart_goods['is_gift'] = $cart['is_gift'];
            $cart_data[] = $cart_goods;
            $total_price += $price*$cart['nums'];
        endforeach;
        return $this->render("my_cart",[
            'cart_data' => $cart_data,
            'total_price' => $total_price,
            'customer_id' => $customer['id'],
        ]);
    }

    //修改数量
    public function actionChange_cat_num(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $num = $_POST['new_num'];
            $cart = Customer_cart::find()->where("id =".$id)->one();
            $cart->nums = $num;
            $customer = Customer::find()->where("customer_id =".Yii::$app->session['customer_id'])->asArray()->one();
            $customer_id = $customer['id'];
            $goods_id = $cart['goods_id'];
            $promotion = $this->get_promotion($cart['goods_id'],$customer['type_id']);
            Customer_cart::deleteAll("is_gift = 1 AND $goods_id =".$goods_id." AND customer_id =".$customer_id);
            if(!empty($promotion)){
                foreach($promotion as $val){
                    switch($val['type']){
                        //满送才增加数量，其他涉及价钱的在购物车里面体现
                        case 1 :
                            if($num >= $val['number']){
                                $coe = (floor($num/$val['number']))*$val['coefficient'];
                                $gift = new Customer_cart();
                                $gift->goods_id = $goods_id;
                                $gift->goods_name = $cart->goods_name."(赠送)";
                                $gift->user_id = $customer['user_id'];
                                $gift->customer_id = $customer_id;
                                $gift->nums = $coe;
                                $gift->is_gift = 1;
                            }
                            break;
                    }
                }
            }
            if(!empty($gift)){
                if($cart->save() && $gift->save()){
                    echo 111;
                }else{
                    echo 222;
                }
            }else{
                if($cart->save()){
                    echo 111;
                }else{
                    echo 222;
                }
            }

            exit;
        }
    }

    public function actionDel_cart(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $cart = Customer_cart::find()->where("id =".$id)->asArray()->one();
            if(Customer_cart::deleteAll("goods_id =".$cart['goods_id']." AND customer_id =".$cart['customer_id'])){
                echo 111;
                exit;
            }
        }
    }

    //确认订单
    public function actionAdd_order(){
        $customer_id = Yii::$app->session['customer_id'];
        $customer = Customer::find()->where("customer_id =".$customer_id)->asArray()->one();
        if(Yii::$app->request->post()){
            //先查购物车情况
            $cart_goods = Customer_cart::find()->where("customer_id =".$customer['id'])->asArray()->all();
            if(empty($cart_goods)){
                Yii::$app->getSession()->setFlash('error','您还未购买任何商品！');
                return $this->redirect('index.php?r=client');
            }
            $goods_amount = 0;
            $order_goods = array();  //订单产品信息
            foreach($cart_goods as $val){
                $goods = Goods::find()->where("goods_id =".$val['goods_id'])->asArray()->one();
                $order_good = array();
                $order_good['goods_id'] = $val['goods_id'];
                $order_good['goods_sn'] = $goods['goods_sn'];
//                $order_good['product_id'] = $goods['product_id'];
                $order_good['goods_number'] = $val['nums'];
                $order_good['market_price'] = $goods['market_price'];
                $rank_price = Member_price::find()->where("goods_id =".$val['goods_id'])->andWhere("user_rank =".$customer['type_id'])->asArray()->one();
                //如果是赠送商品，则价格为0，商品标识为赠品，并且赠送标识为1，否则再计算价格
                if($val['is_gift'] == 1){
                    $goods_price = 0;
                    $order_good['goods_name'] = $goods['goods_name']."(赠送)";
                    $order_good['is_gift'] = 1;
                }else{
                    $order_good['is_gift'] = 0;
                    if(!empty($rank_price)){
                        $goods_price = $rank_price['user_price'];
                    }else{
                        $goods_price = $goods['shop_price'];
                    }
                    $order_good['goods_name'] = $goods['goods_name'];
                }
                $order_good['goods_price'] = $goods_price;
                $order_goods[] = $order_good;
                $goods_amount += ($goods_price*$val['nums']);
            }

            $new_order = new Order_info();
            //如果选择自提，直接无视送货信息
            if($_POST['clog'] == 2){
                $new_order->shipping_id = 2;
                $new_order->shipping_name = '客户自提';
            }else{
                $new_order->shipping_id = 1;
                $new_order->shipping_name = '送货';
                //如果地址栏不留空，用输入的地址，如果留空，用地址id获取保存的地址
                if(!empty($_POST['address'])){
                    $new_order->country = 1;
                    $new_order->province = $_POST['province'];
                    $new_order->city = $_POST['city'];
                    $new_order->district = $_POST['district'];
                    $new_order->address = $_POST['address'];
                    $new_order->consignee = $_POST['contacts'];
                    $new_order->tel = $_POST['phone'];
                    //将这个地址保存
                    $new_address = new Address();
                    $new_address->user_id = $customer_id;
                    $new_address->consignee = $_POST['contacts'];
                    $new_address->country = 1;
                    $new_address->province = $_POST['province'];
                    $new_address->city = $_POST['city'];
                    $new_address->district = $_POST['district'];
                    $new_address->address = $_POST['address'];
                    $new_address->tel = $_POST['phone'];
                    $new_address->save();
                }else{
                    $address = Address::find()->where("address_id =".$_POST['address_id'])->asArray()->one();
                    $new_order->country = 1;
                    $new_order->province = $address['province'];
                    $new_order->city = $address['city'];
                    $new_order->district = $address['district'];
                    $new_order->address = $address['address'];
                    $new_order->consignee = $address['consignee'];
                    $new_order->tel = $address['tel'];
                }
            }
            $new_order->order_sn = $this->get_order_sn();
            $new_order->customer_pay = $_POST['pay_type'];
            $new_order->user_id = $customer_id;   //用户id
            $new_order->how_oos = $_POST['remark'];
            $new_order->add_time = time();
            $new_order->get_time = $_POST['get_time'];
            $new_order->customer_id = $customer['id'];  //对应的客户表id
            $new_order->sale_id = $customer['user_id']; //业务员id
            $new_order->goods_amount = $goods_amount ;
            $new_order->order_amount = $goods_amount+$_POST['f_price'];
            $new_order->clog_price = $_POST['f_price'];
            if($new_order->save()){
                //清空购物车
                Customer_cart::deleteAll("customer_id =".$customer['id']);
                foreach($order_goods as $val){
                    $new_order_goods = new Order_goods();
                    $new_order_goods->order_id = $new_order->order_id;
                    $new_order_goods->goods_id = $val['goods_id'];
                    $new_order_goods->goods_name = $val['goods_name'];
                    $new_order_goods->goods_sn = $val['goods_sn'];
                    $new_order_goods->goods_number = $val['goods_number'];
                    $new_order_goods->market_price = $val['market_price'];
                    $new_order_goods->goods_price = $val['goods_price'];
                    $new_order_goods->is_gift = $val['is_gift'];
                    $new_order_goods->save();
                }
                Yii::$app->getSession()->setFlash('success','下单成功！');
                return $this->redirect("index.php?r=client/order_detail&id=".$new_order->order_id);
            }
        }else {
            $provinces = Region::find()->where("parent_id = 1")->asArray()->all();
            $user_address = Address::find()->where("user_id =".$customer_id)->asArray()->all();
            if(!empty($user_address)) {
                $all_address = array();
                foreach ($user_address as $val):
                    $province = Region::find()->where("region_id =" . $val['province'])->asArray()->one();
                    $city = Region::find()->where("region_id =" . $val['city'])->asArray()->one();
                    $district = Region::find()->where("region_id =" . $val['district'])->asArray()->one();
                    $address = array();
                    $address['address_id'] = $val['address_id'];
                    $address['province'] = $province['region_name'];
                    $address['city'] = $city['region_name'];
                    $address['district'] = $district['region_name'];
                    $address['address'] = $val['address'];
                    $address['tel'] = $val['tel'];
                    $address['consignee'] = $val['consignee'];
                    $all_address[] = $address;
                    $address_price = Freight::find()->where("region_id =".$val['city'])->asArray()->one();
                    if(empty($address_price)){
                        $price_default = 0;
                    }else{
                        $price_default = $address_price['price'];
                    }
                endforeach;
            }
            return $this->render('order_add',[
                'provinces' => $provinces,
                'address' => $all_address,
                'price_default' => $price_default,
            ]);
        }
    }

    //获取运费
    public function actionF_price(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $f_price = Freight::find()->where("region_id =".$id)->asArray()->one();
            if(!empty($f_price)) {
                echo $f_price['price'];
            }else{
                echo 0;
            }
            exit;
        }
    }

    //根据地址id查运费
    public function actionGet_f_price(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $address = Address::find()->where("address_id =".$id)->asArray()->one();
            $f_price = Freight::find()->where("region_id =".$address['city'])->asArray()->one();
            if(empty($f_price)){
                echo 0;
            }else{
                echo $f_price['price'];
            }
            exit;
        }
    }

    //根据商品和客户等级获取政策
    public function get_promotion($goods_id,$rank){
        $promotion_goods = Promotion_goods::find()->where("goods_id =".$goods_id)->asArray()->all();
        if(!empty($promotion_goods)){
            $promotion_ids = array();
            foreach($promotion_goods as $val){
                $promotion_ids[] = $val['promotion_id'];
            }
            $promotion_ids = implode(",",$promotion_ids);
            $promotion = Promotion::find()->where("start_time <".time())->andWhere("end_time >".time())->andWhere("rank like '%".$rank."%'")->andWhere("id in (".$promotion_ids.")")->asArray()->all();
            return $promotion;
        }
    }


    //保存地址方法
//    function save_new_address(){
//        $new_address = new Address();
//        $new_address->user_id = Yii::$app->session['user_id'];
//        $new_address->consignee = $_POST['contacts'];
//        $new_address->country = $_POST['country'];
//        $new_address->province = $_POST['province'];
//        $new_address->city = $_POST['city'];
//        $new_address->district = $_POST['district'];
//        $new_address->address = $_POST['address'];
//        $new_address->tel = $_POST['phone'];
//        $new_address->save();
//    }

}