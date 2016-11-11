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
use common\models\Address;
use common\models\Customer;
use common\models\Customer_cart;
use common\models\Customer_order;
use common\models\Customer_order_goods;
use common\models\Customer_type;
use common\models\Ecs_user;
use common\models\Freight;
use common\models\Goods;
use common\models\Member_price;
use common\models\Order_goods;
use common\models\Order_info;
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
        $customer_id = $_GET['customer_id'];
        $customer = Customer::find()->where("id =".$customer_id)->asArray()->one();
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
                $promotions = Promotion::find()->where("id in (".$promotion_id.")")->andWhere("start_time <".time())->andWhere("end_time >".time())->andWhere("rank like '%".$customer['type_id']."%'")->asArray()->all();
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
        $discount = 0;
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
                $promotion = $this->get_promotion($cart['goods_id'],$customer['type_id']);
                if(!empty($promotion)){
                    foreach ($promotion as $val) {
                        switch ($val['type']) {
                            //满减
                            case 2 :
                                if ($cart['nums'] >= $val['number']) {
                                    $coe = (floor($cart['nums'] / $val['number'])) * $val['coefficient'];
                                    $discount += $coe;
                                }
                                break;
                            //满折
                            case 3 :
                                if ($cart['nums'] >= $val['number']) {
                                    $coe = (floor($cart['nums'] / $val['number'])) * $val['coefficient'];
                                    $one_dis = ($price * $cart['nums']) * (1 - $coe);
                                    $discount += $one_dis;
                                }
                                break;
                        }
                    }
                }
            }
            $cart_goods['promotion'] = $promotion;
            $cart_goods['goods_num'] = $goods['goods_number'];
            $cart_goods['price'] = $price;
            $cart_goods['num'] = $cart['nums'];
            $cart_goods['is_gift'] = $cart['is_gift'];
            $cart_data[] = $cart_goods;
            $total_price += $price*$cart['nums'];
        endforeach;
        $total_price -= $discount;
        return $this->render("cart",[
            'cart_data' => $cart_data,
            'total_price' => $total_price,
            'customer_id' => $customer_id,
        ]);
    }

    //确认下单
    public function actionAdd_order(){
        $id = $_GET['customer_id'];
        $user_id = Yii::$app->session['user_id'];
        $customer = Customer::find()->where("id =".$id)->asArray()->one();
        $customer_id = $customer['customer_id'];
        if(!empty($customer['up_customer'])){
            $up_customer = Customer::find()->where("id =".$customer['up_customer'])->asArray()->one();
            $up_rank = Customer_type::find()->where("rank_id =".$up_customer['type_id'])->asArray()->one();
        }
        if(Yii::$app->request->post()){

            //先查购物车情况
            $cart_goods = Customer_cart::find()->where("customer_id =".$customer['id'])->asArray()->all();
            if(empty($cart_goods)){
                Yii::$app->getSession()->setFlash('error','您还未选择任何商品！');
                return $this->redirect('index.php?r=customer');
            }
            $goods_amount = 0;
            $order_goods = array();  //订单产品信息
            $discount = 0;
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
                //查是否有涉及价格的政策，有就改变价钱
                $promotion = $this->get_promotion($val['goods_id'],$customer['type_id']);
                if($val['is_gift'] != 1) {
                    if (!empty($promotion)) {
                        foreach ($promotion as $p) {
                            switch ($p['type']) {
                                //满减
                                case 2 :
                                    if ($val['nums'] >= $p['number']) {
                                        $coe = (floor($val['nums'] / $p['number'])) * $p['coefficient'];
                                        $discount += $coe;
                                    }
                                    break;
                                //满折
                                case 3 :
                                    if ($val['nums'] >= $p['number']) {
//                                        $coe = (floor($val['nums'] / $p['number'])) * $p['coefficient'];
                                        $one_dis = ($goods_price * $val['nums']) * (1 - $p['coefficient']);
                                        if ($one_dis > 0) {
                                            $discount += $one_dis;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
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
                $clog_price = 0;
            }else{
                $clog_price = $_POST['clog_price'];
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
                    if(!empty($_POST['address_id'])) {
                        $address = Address::find()->where("address_id =" . $_POST['address_id'])->asArray()->one();
                        $new_order->country = 1;
                        $new_order->province = $address['province'];
                        $new_order->city = $address['city'];
                        $new_order->district = $address['district'];
                        $new_order->address = $address['address'];
                        $new_order->consignee = $address['consignee'];
                        $new_order->tel = $address['tel'];
                        $new_order->clog_price = $clog_price;
                    }else{
                        Yii::$app->getSession()->setFlash('error','请填写收货内容！');
                        return $this->redirect("index.php?r=client/add_order");
                    }
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
            $new_order->order_amount = $goods_amount+$clog_price-$discount;
            $new_order->discount = $discount;
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
                return $this->redirect("index.php?r=orders");
            }
        }else{
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
//            if(!empty($customer['province_id']))
//                $citys = Region::find()->where("parent_id =".$customer['province_id'])->asArray()->all();
            return $this->render('add_order',[
                'provinces' => $provinces,
                'customer' => $customer,
                'price_default' => $price_default,
                'address' => $all_address,
//                'citys' => $citys,
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

}