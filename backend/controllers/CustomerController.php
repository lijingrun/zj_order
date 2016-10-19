<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 14:39
 */
namespace backend\controllers;

use common\models\Category;
use common\models\City;
use common\models\Customer;
use common\models\Customer_cart;
use common\models\Customer_order;
use common\models\Customer_order_goods;
use common\models\Customer_type;
use common\models\Ecs_user;
use common\models\Goods;
use common\models\Member_price;
use common\models\Promotion;
use common\models\Promotion_goods;
use common\models\Province;
use common\models\Region;
use common\models\User_address;
use common\models\User_rule;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\User;


class CustomerController extends Controller{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $user_id = Yii::$app->session['user_id'];
        if(empty($user_id)){
            return $this->redirect("index.php?r=site/login");
        }else{
                return $action;
        }
    }

    public function actionIndex(){
        $all_customers = Customer::find()->where("user_id =".Yii::$app->session['user_id'])->andWhere("del = 0");
        $key_word = $_GET['key_word'];
        if(!empty($key_word)){
            $all_customers->andWhere("customer_name like '%".$key_word."%'");
        }
        $page = new Pagination(['totalCount' => $all_customers->count(),'pageSize' => '20']);
        $customers = $all_customers->offset($page->offset)->limit($page->limit)->all();
        foreach($customers as $key=>$customer){
            $customers[$key]['type_id'] = Customer_type::find()->where("rank_id = ".$customer['type_id'])->asArray()->one();
            if(!empty($customer['customer_id'])) {
                $customers[$key]['customer_id'] = Ecs_user::find()->where("user_id =" . $customer['customer_id'])->asArray()->one();
            }
        }
        return $this->render("customer_list",[
            'customers' => $customers,
            'pages' => $page,
            'key_word' => $key_word,
        ]);
    }

    //添加客户资料
    public function actionAdd(){
        if(Yii::$app->request->post()){
            if(empty($_POST['customer_name'])){
                Yii::$app->getSession()->setFlash('error','信息不能为空');
                return;
            }
            $user_id = Yii::$app->session['user_id'];
            $province = Region::find()->where("region_id =".$_POST['province'])->asArray()->one();
            $city = Region::find()->where("region_id =".$_POST['city'])->asArray()->one();
            $check_customer = Customer::find()->where("del = 0")->andWhere("customer_name = '".$_POST['customer_name']."'")->one();
            if(!empty($check_customer)){
                Yii::$app->getSession()->setFlash('error','客户已经存在！');
                return $this->redirect("index.php?r=customer/add");
            }
            $customer = new Customer();
            $customer->customer_name = $_POST['customer_name']; //客户名
            $customer->customer_code = $_POST['customer_code']; //客户编码
            $customer->license_id = $_POST['license_id'];  //营业执照号
            $customer->province_id = $_POST['province']; //所在省份
            $customer->province = $province['region_name']; //所在省份
            $customer->city_id = $_POST['city']; //所在城市
            $customer->city = $city['region_name']; //所在城市
            $customer->address = $_POST['address']; //详细地址
            $customer->zip_code = $_POST['zip_code']; //邮编
            $customer->phone = $_POST['phone']; //电话
            $customer->fax = $_POST['fax']; //传真
            $customer->type_id = $_POST['type_id']; //客户类型
            $customer->start_time = strtotime($_POST['start_time']); //签约开始时间
            $customer->end_time = strtotime($_POST['end_time']);  //签约结束时间
            $customer->name = $_POST['name']; //姓名
            $customer->position = $_POST['position']; //职位
            $customer->telephone = $_POST['telephone'];
            $customer->email = $_POST['email'];
            $customer->qq = $_POST['qq'];
            $customer->log_code = $_POST['log_code']; //物流编码
            $customer->spare = $_POST['spare'];
            $customer->user_id = $user_id;
            //财务信息
            $customer->ban_name = $_POST['ban_name'];
            $customer->ban = $_POST['ban'];
            $customer->ban_no = $_POST['ban_no'];
            $customer->invoice = $_POST['invoice'];
            $customer->taxes = $_POST['taxes'];

            //商城账号信息
//            $user_name = $_POST['user_name'];
//            $password = $_POST['password'];
//            if(!empty($user_name)) {
//                //先查用户名是否已经注册商城账号，如果有，就重新写一个
//                $ecs_user = Ecs_user::find()->where("user_name ='" . $user_name."'")->count();
//                if ($ecs_user > 0) {
//                    Yii::$app->getSession()->setFlash('error', '商城账号已经存在！');
//                    return $this->redirect("index.php?r=customer/add");
//                } else {
//                    $new_ecs_user = new Ecs_user();
//                    $new_ecs_user->user_name = $user_name;
//                    $new_ecs_user->password = md5($password);
//                    $new_ecs_user->email = $_POST['email'];
//                    $new_ecs_user->user_rank = $_POST['type_id'];
//                    $new_ecs_user->office_phone = $_POST['phone'];
//                    $new_ecs_user->mobile_phone = $_POST['tel_phone'];
//                    if ($new_ecs_user->save()) {
//                        $customer->customer_id = $new_ecs_user['user_id'];
//                    } else {
//                        Yii::$app->getSession()->setFlash('error', "服务器繁忙，请稍后重试！");
//                        return $this->redirect("index.php?r=customer/add");
//                    }
//                }
//            }

            if($customer->save()){
                return $this->redirect('index.php?r=customer');
            }else{
                Yii::$app->getSession()->setFlash("error",'服务器繁忙，请稍后重试！');
                return $this->redirect("index.php?r=customer/add");
            }
        }else {
            $provinces = Region::find()->where("parent_id = 1")->asArray()->all();
            $customer_types = Customer_type::find()->asArray()->all();
            return $this->render("add", [
                'provinces' => $provinces,
                'customer_types' => $customer_types,
            ]);
        }
    }

    //客户详细资料
    public function actionDetail(){
        $id = $_GET['id'];
        $customer = Customer::find()->where("id =".$id)->asArray()->one();
        $rank = Customer_type::find()->where("rank_id =".$customer['type_id'])->asArray()->one();
        if(!empty($customer['user_id'])){
            $user = Ecs_user::find()->where("user_id =".$customer['user_id'])->asArray()->one();
            $user_name = $user['user_name'];
        }else{
            $user_name = "未开通";
        }
        return $this->render("detail",[
            'customer' => $customer,
            'rank' => $rank,
            'user_name' => $user_name,
        ]);
    }

    public function actionEdit(){
        $id = $_GET['id'];
        $customer = Customer::find()->where("id =".$id)->asArray()->one();
        if(!empty($customer['customer_id'])) {
            $ecs_user = Ecs_user::find()->where("user_id =" . $customer['customer_id'])->asArray()->one();
        }
        if(empty($customer)){
            Yii::$app->getSession()->setFlash('error','客户不存在!');
            return $this->redirect("index.php?r=customer");
        }
        if(Yii::$app->request->post()){
            $customer = Customer::find()->where("id =".$id)->one();
            $province = Region::find()->where("region_id =".$_POST['province'])->asArray()->one();
            $city = Region::find()->where("region_id =".$_POST['city'])->asArray()->one();
            $customer->customer_name = $_POST['customer_name']; //客户名
            $customer->license_id = $_POST['license_id']; //营业执照号
            $customer->customer_code = $_POST['customer_code']; //客户编码
            $customer->province_id = $_POST['province']; //所在省份
            $customer->province = $province['region_name']; //所在省份
            $customer->city_id = $_POST['city']; //所在城市
            $customer->city = $city['region_name']; //所在城市
            $customer->address = $_POST['address']; //详细地址
            $customer->zip_code = $_POST['zip_code']; //邮编
            $customer->phone = $_POST['phone']; //电话
            //审核通过和不通过的会员都需要重新审核一次
            if($customer->status == 3 || $customer->status == 2){
                $customer->status = 1;
            }
            $customer->fax = $_POST['fax']; //传真
            $customer->type_id = $_POST['type_id']; //客户类型
            $customer->start_time = strtotime($_POST['start_time']); //签约开始时间
            $customer->end_time = strtotime($_POST['end_time']);  //签约结束时间
            $customer->name = $_POST['name']; //姓名
            $customer->position = $_POST['position']; //职位
            $customer->telephone = $_POST['telephone'];
            $customer->email = $_POST['email'];
            $customer->qq = $_POST['qq'];
            $customer->log_code = $_POST['log_code']; //物流编码
            $customer->spare = $_POST['spare'];
            //商城账号
//            if(!empty($_POST['user_name'])) {
//                $customer->user_name = $_POST['user_name'];
//                $customer->password = $_POST['password'];
//            }
            //财务信息
            $customer->ban_name = $_POST['ban_name'];
            $customer->ban = $_POST['ban'];
            $customer->ban_no = $_POST['ban_no'];
            $customer->invoice = $_POST['invoice'];
            $customer->taxes = $_POST['taxes'];
            if($customer->save()){
                Yii::$app->getSession()->setFlash('success','修改成功！');
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试！');
            }
            return $this->redirect('index.php?r=customer');
        }else{
            $provinces = Region::find()->where("parent_id = 1")->asArray()->all();
            $citys = Region::find()->where("parent_id =".$customer['province_id'])->asArray()->all();
            $types = Customer_type::find()->asArray()->all();
            return $this->render("edit",[
                'provinces' => $provinces,
                'citys' => $citys,
                'customer_types' => $types,
                'customer' => $customer,
                'ecs_user' => $ecs_user,
            ]);
        }
    }

    //删除客户
    public function actionDel_customer(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $customer = Customer::find()->where("id =".$id)->andWhere("user_id =".Yii::$app->session['user_id'])->one();
            $customer->del = 1;
            if($customer->save()){
                $data = $this->return_json(0,'操作成功！');
            }else{
                $data = $this->return_json(1,'服务器繁忙，请稍后重试！');
            }
            return $data;
        }
    }

    public function actionType(){
        $types = Customer_type::find()->all();

        return $this->render("type_list",[
            'types' => $types,
        ]);
    }

    //重置密码
    public function actionReset_password(){
        if(Yii::$app->request->post()){
            $user_id = $_POST['user_id'];
            if(!empty($user_id)){
                $ecs_user = Ecs_user::find()->where("user_id =".$user_id)->one();
                $ecs_user->password ="e10adc3949ba59abbe56e057f20f883e";
                if($ecs_user->save()){
                    echo 111;
                }else{
                    echo 222;
                }
                exit;
            }
        }
    }

    //增加客户类型
    public function actionType_add(){
        if(Yii::$app->request->post()){
            $name = $_POST['name'];
            $discount = $_POST['discount'];
            if($discount > 100){
                $data['error_code'] = 2;
                $data['error_data'] = "折扣格式错误,请重新填写！";
                $data = json_encode($data);
                return $data;
            }
            $type = Customer_type::find()->where("name = '".$name."'")->asArray()->one();
            if(!empty($type)){
                $data['error_code'] = 3;
                $data['error_data'] = "等级已存在！";
                $data = json_encode($data);
                return $data;
            }
            $type = new Customer_type();
            $type->name = $name;
            $type->add_time = time();
            $type->discount = $discount;
            if($type->save()){
                $data['error_code'] = 0;
            }else{
                $data['error_code'] = 1;
                $data['error_data'] = "服务器繁忙，请稍后重试！";
            }
            $data = json_encode($data);
            return $data;
        }
    }

    //修改等级名称
    public function actionChange_type_name(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $name = $_POST['name'];
            if(empty($id) || empty($name)){
                $data['error_code'] = 1;
                $data['error_data'] = "需要信息不全！";
                $return_data = json_encode($data);
                return $return_data;
            }
            $type = Customer_type::find()->where("id =".$id)->one();
            if(empty($type)){
                $data['error_code'] = 2;
                $data['error_data'] = "类型不存在！";
                $return_data = json_encode($data);
                return $return_data;
            }
            $type->name = $name;
            if($type->save()){
                $data['error_code'] = 0;
                $return_data = json_encode($data);
                return $return_data;
            }else{
                $data['error_code'] = 3;
                $data['error_data'] = "服务器繁忙，请稍后重试";
                $return_data = json_encode($data);
                return $return_data;
            }
        }
    }

    //修改类型折扣
    public function actionChange_type_discount(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $discount = $_POST['discount'];
            if(empty($id) || empty($discount)){
                $data['error_code'] = 1;
                $data['error_data'] = "需要信息不全";
                $return_data = json_encode($data);
                return $return_data;
            }
            if($discount<0 || $discount > 100){
                $data['error_code'] = 2;
                $data['error_data'] = "折扣格式错误，需要在0-100之间";
                $return_data = json_encode($data);
                return $return_data;
            }
            $type = Customer_type::find()->where("id =".$id)->one();
            if(empty($type)){
                $data['error_code'] = 3;
                $data['error_data'] = "类型不存在！";
                $return_data = json_encode($data);
                return $return_data;
            }
            $type->discount = $discount;
            if($type->save()){
                $data['error_code'] = 0;
                $return_data = json_encode($data);
                return $return_data;
            }else{
                $data['error_code'] = 4;
                $data['error_data'] = "服务器繁忙，请稍后重试！";
                $return_data = json_encode($data);
                return $return_data;
            }
        }
    }

    //删除
    public function actionDel(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            if(empty($id)){
                $data = $this->return_json(1,'需求信息为空！');
            }
            if(Customer_type::deleteAll("id =".$id)){
                $data = $this->return_json(0,'');
            }else{
                $data = $this->return_json(2 , '服务器繁忙，请稍后重试');
            }
            return $data;
        }
    }

    public function actionGet_city(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            if(!empty($id)){
                $data = $this->get_city($id);
            }
            echo $data;
            exit;
        }
    }

    //添加购物车
    public function actionAdd_cart(){
        $all_goods = Goods::find()->where("is_delete = 0");
        $key_word = $_GET['key_word'];
        $cat_id = $_GET['cat_id'];
        if(!empty($cat_id)){
            $all_goods->andWhere("cat_id =".$cat_id);
        }
        if(!empty($key_word)){
            $all_goods->andWhere("goods_name like '%".$key_word."%'")->orWhere("goods_sn like '%".$key_word."%'");
        }
        $category_data = Category::find()->asArray()->all();
        $child = array();
        $category = $this->cate($category_data,$child,0);
        $pages = new Pagination(['totalCount' => $all_goods->count(),'pageSize' => 10]);
        $goods = $all_goods->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        foreach($goods as $key=>$good){
            //查有无政策
            $promotion_goods = Promotion_goods::find()->where("goods_id =".$good['goods_id'])->asArray()->all();
            if(!empty($promotion_goods)){
                $promotion_id = array();
                foreach($promotion_goods as $val){
                    $promotion_id[] = $val['promotion_id'];
                }
                $promotion_id = implode(',',$promotion_id);
                $promotions = Promotion::find()->where("id in (".$promotion_id.")")->andWhere("start_time <".time())->andWhere("end_time >".time())->asArray()->all();
                if(!empty($promotions)) {
                    $goods[$key]['seller_note'] = $promotions;
                }
            }
        }
        $customer_id = $_GET['customer_id'];
        return $this->render("add_cart",[
            'goods' => $goods,
            'pages' => $pages,
            'customer_id' => $customer_id,
            'key_word' => $key_word,
            'category' => $category,
        ]);
    }

    //将商品添加到购物车
    public function actionAdd_to_cart(){
        if(Yii::$app->request->post()){
            $goods_id = $_POST['goods_id'];
            $customer_id = $_POST['customer_id'];
            $user_id = Yii::$app->session['user_id'];
            $number = $_POST['number'];
            $cart = Customer_cart::find()->where("goods_id =".$goods_id)->andWhere("user_id =".$user_id)->andWhere("customer_id =".$customer_id)->count();
            if($cart > 0){
                echo 222;
                exit;
            }
            $goods = Goods::find()->where("goods_id =".$goods_id)->asArray()->one();
            $new_cart = new Customer_cart();
            $new_cart->goods_id = $goods_id;
            $new_cart->goods_name = $goods['goods_name'];
            $new_cart->user_id = Yii::$app->session['user_id'];
            $new_cart->customer_id = $customer_id;
            $new_cart->nums = $number;
            $customer = Customer::find()->where("id =".$customer_id)->asArray()->one();
            //查是否有优惠政策，如果有，就查客户是否满足，满足的话按照政策来处理
            $promotion_goods = Promotion_goods::find()->where("goods_id =".$goods_id)->asArray()->all();
            if(!empty($promotion_goods)){
                $promotion_id = array();
                foreach($promotion_goods as $good){
                    $promotion_id[] = $good['promotion_id'];
                }
                $promotion_id = implode(",",$promotion_id);
                $promotion = Promotion::find()->where("id in (".$promotion_id.")")->andWhere("start_time <".time())->andWhere("end_time >".time())->andWhere("rank like '%".$customer['type_id']."%'")->asArray()->all();
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
                                    $gift->user_id = Yii::$app->session['user_id'];
                                    $gift->customer_id = $customer_id;
                                    $gift->nums = $coe;
                                    $gift->is_gift = 1;
                                }
                                break;
                        }
                    }
                }
            }
            if(!empty($gift)){
                if ($new_cart->save() && $gift->save()) {
                    echo 111;
                } else {
                    echo 333;
                }
            }else {
                if ($new_cart->save()) {
                    echo 111;
                } else {
                    echo 333;
                }
            }
            exit;
        }
    }

    //查看购物车
    public function actionCart(){
        $customer_id = $_GET['customer_id'];
        $carts = Customer_cart::find()->where("customer_id =".$customer_id)->asArray()->all();
        $customer = Customer::find()->where("id = ".$customer_id)->asArray()->one();
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
            if($cart['is_gift'] == 1){
                $price = 0;
            }else {
                if (!empty($member_price['user_price'])) {
                    $price = $member_price['user_price'];
                } else {
                    $price = $goods['shop_price'] * ($rank['discount'] / 100);
                }
            }
            $cart_goods['goods_num'] = $goods['goods_number'];
            $cart_goods['price'] = $price;
            $cart_goods['num'] = $cart['nums'];
            $cart_goods['is_gift'] = $cart['is_gift'];
            $cart_data[] = $cart_goods;
            $total_price += $price*$cart['nums'];
        endforeach;
        return $this->render("cart",[
            'cart_data' => $cart_data,
            'total_price' => $total_price,
            'customer_id' => $customer_id,
        ]);
    }

    //确认下单
    public function actionAdd_order(){
        $customer_id = $_GET['customer_id'];
        $user_id = Yii::$app->session['user_id'];
        $customer = Customer::find()->where("id =".$customer_id)->asArray()->one();
        if(!empty($customer['up_customer'])){
            $up_customer = Customer::find()->where("id =".$customer['up_customer'])->asArray()->one();
            $up_rank = Customer_type::find()->where("rank_id =".$up_customer['type_id'])->asArray()->one();
        }
        if(Yii::$app->request->post()){
            $carts = Customer_cart::find()->where("customer_id =".$customer_id)->andWhere("user_id =".$user_id)->asArray()->all();
            if(empty($carts)){
                Yii::$app->getSession()->setFlash("error",'你未选择商品！');
                return $this->redirect("index.php?r=customer/cart&customer_id=".$customer_id);
            }
            $province_id = $_POST['province']; //省份id
            $city_id = $_POST['city'];      //城市id
            $address = $_POST['address'];  //地址
            $contacts = $_POST['contacts']; //联系人
            $phone = $_POST['phone']; //联系电话
            $customer_id = $_POST['customer_id']; //客户id
            $customer = Customer::find()->where("id =".$customer_id)->asArray()->one();
            $rank = Customer_type::find()->where("rank_id =".$customer['type_id'])->asArray()->one(); //会员级别
            $province = Region::find()->where("region_id =".$province_id)->asArray()->one();
            $city = Region::find()->where("region_id =".$city_id)->asArray()->one();
            //保存订单信息
            $order = new Customer_order();
            $order->customer_id = $customer_id; //客户id
            $order->sale_id = $user_id; //业务员id
            $order->user_id = $customer['customer_id'];
            $order->address = $province['region_name'].$city['region_id'].$address;
            $order->consignee = $contacts;
            $order->tel = $phone;
            $order->shipping_id = $_POST['clog'];
            if($_POST['clog'] == 1){
                $colg_name = '送货';
            }else{
                $colg_name = "自提";
            }
            $order->country = 1;
            $order->province = $province_id;
            $order->city = $city_id;
            $order->shipping_name = $colg_name;
            $order->get_time = $_POST['get_time'];
            $order->customer_pay = $_POST['pay_type'];
            $order->best_time = $_POST['remarks'];
            $order->add_time = time();
            $order->order_sn = $this->get_order_sn();
            $total_price = 0;
            $up_total_price = 0;
            if($order->save()){
                foreach($carts as $cart):
                    $goods = Goods::find()->where("goods_id =".$cart['goods_id'])->asArray()->one();
                    $order_goods = new Customer_order_goods();
                    $order_goods->order_id = $order['order_id'];
                    $order_goods->goods_number = $cart['nums'];
                    $order_goods->goods_id = $goods['goods_id'];
                    $order_goods->goods_sn = $goods['goods_sn'];
                    $order_goods->market_price = $goods['shop_price'];
                    $rank_price = Member_price::find()->where("goods_id =".$cart['goods_id'])->andWhere("user_rank =".$customer['type_id'])->asArray()->one();
                    if($cart['is_gift'] == 1){
                        $order_goods->goods_name = $goods['goods_name']."(赠送)";
                        $goods_price = 0;
                    }else {
                        $order_goods->goods_name = $goods['goods_name'];
                        if (!empty($rank_price)) {
                            $goods_price = $rank_price['user_price'];
                        } else {
                            $goods_price = $goods['shop_price'] * ($rank['discount'] / 100);
                        }
                    }
                    if(!empty($up_customer)){
                        $up_rank_price = Member_price::find()->where("goods_id =".$cart['goods_id'])->andWhere("user_rank =".$up_customer['type_id'])->asArray()->one();
                        if(!empty($up_rank_price)){
                            $up_price = $up_rank_price['user_price'];
                        }else{
                            $up_price = $goods['shop_price']*($up_rank['discount']/100);
                        }
                        $order_goods->up_price = $up_price;
                        $up_total_price += $up_price*$cart['nums'];
                    }
                    $order_goods->goods_price = $goods_price;
                    $total_price += $goods_price*$cart['nums'];
                    $order_goods->save();
                endforeach;
                $order->order_amount = $total_price;
                $order->goods_amount = $total_price;
                $order->up_amount = $up_total_price;
                $order->save();
                Customer_cart::deleteAll("user_id =".$user_id);  //删除临时数据
                Yii::$app->getSession()->setFlash('success','下单成功！');
                return $this->redirect("index.php?r=orders");
            }else{
                Yii::$app->getSession()->setFlash("error","系统繁忙，请稍后重试！");
                return $this->redirect("index.php?r=orders");
            }
        }else{
            $provinces = Region::find()->where("parent_id = 1")->asArray()->all();
            if(!empty($customer['province_id']))
                $citys = Region::find()->where("parent_id =".$customer['province_id'])->asArray()->all();
            return $this->render('add_order',[
                'provinces' => $provinces,
                'customer' => $customer,
                'citys' => $citys,
            ]);

        }
    }

    //根据省份城市，获取客户编码内容
    public function actionGet_code(){
        $province_id = $_POST['province_id'];
        $city_id = $_POST['city_id'];
        $code = $this->get_code($province_id,$city_id);
        echo $code;
        exit;
    }

}