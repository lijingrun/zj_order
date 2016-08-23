<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 10:51
 */
namespace frontend\controllers;

use common\models\Customer;
use common\models\Customer_cart;
use common\models\Customer_order;
use common\models\Customer_order_goods;
use common\models\Customer_type;
use common\models\Ecs_user;
use common\models\Goods;
use common\models\Member_price;
use common\models\Region;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class OrdersController extends Controller{

//    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        if(empty(Yii::$app->session['user_id'])){
            return $this->redirect('index.php?r=site/login');
        }else{
            return $action;
        }
    }

    public function actionIndex()
    {
        $all_orders = Customer_order::find()->where("user_id =".Yii::$app->session['user_id']);
//        $customer_name = $_GET['customer_name'];
        $customer_id = $_GET['id'];
        if(!empty($customer_id)){
            $all_orders->andWhere("customer_id =".$customer_id);
        }
        $start = $_GET['start'];
        $end = $_GET['end'];
        if(!empty($start)){
            $start = strtotime($start);
            $all_orders->andWhere("add_time >=".$start);
        }
        if(!empty($end)){
            $end = strtotime($end);
            $all_orders->andWhere("add_time <=".$end);
        }
        $all_orders->orderBy("id desc");
        $pages = new Pagination(['totalCount' => $all_orders->count(), 'pageSize' => '10']);
        $orders = $all_orders->offset($pages->offset)->limit($pages->limit)->all();
        foreach($orders as $key=>$order):
            $orders[$key]['customer_id'] = Customer::find()->where("id =".$order['customer_id'])->asArray()->one();
        endforeach;

        return $this->render("order_list",[
            'pages' => $pages,
            'orders' => $orders,
        ]);
    }


    //新建订单
    public function actionAdd(){
        $user_id = Yii::$app->session['user_id'];
        if(Yii::$app->request->post()){
            $carts = Customer_cart::find()->where("user_id =".$user_id)->asArray()->all();
            if(empty($carts)){
                Yii::$app->getSession()->setFlash("error",'请选择商品！');
                return $this->redirect("index.php?r=orders/add");
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
            $order->user_id = $user_id; //业务员id
            $order->address = $province['region_name'].$city['region_name'].$address;
            $order->contacts = $contacts;
            $order->phone = $phone;
            $order->clog = $_POST['clog'];
            $order->get_time = $_POST['get_time'];
            $order->pay_type = $_POST['pay_type'];
            $order->remarks = $_POST['remarks'];
            $order->add_time = time();
            $order->order_sn = $this->get_order_sn();
            $total_price = 0;
            if($order->save()){
                foreach($carts as $cart):
                    $goods = Goods::find()->where("goods_id =".$cart['goods_id'])->asArray()->one();
                    $order_goods = new Customer_order_goods();
                    $order_goods->order_id = $order['id'];
                    $order_goods->num = $cart['nums'];
                    $order_goods->goods_id = $goods['goods_id'];
                    $order_goods->goods_name = $goods['goods_name'];
                    $order_goods->shop_price = $goods['shop_price'];
                    $rank_price = Member_price::find()->where("goods_id =".$cart['goods_id'])->andWhere("user_rank =".$customer['type_id'])->asArray()->one();
                    if(!empty($rank_price)){
                        $goods_price = $rank_price['user_price'];
                    }else{
                        $goods_price = $goods['shop_price']*($rank['discount']/100);
                    }
                    $order_goods->customer_price = $goods_price;
                    $total_price += $goods_price*$cart['nums'];
                    $order_goods->save();
                endforeach;
                $order->order_amount = $total_price;
                $order->goods_amount = $total_price;
                $order->save();
                Customer_cart::deleteAll("user_id =".$user_id);  //删除临时数据
                Yii::$app->getSession()->setFlash('success','下单成功！');
                return $this->redirect("index.php?r=orders");
            }else{
                Yii::$app->getSession()->setFlash("error","系统繁忙，请稍后重试！");
                return $this->redirect("index.php?r=orders/add");
            }
        }else{
            $provinces = Region::find()->where("parent_id = 1")->asArray()->all();
            $customers = Customer::find()->where('user_id ='.$user_id)->asArray()->all();
            $goods = Goods::find()->asArray()->all();
            return $this->render('add',[
                'provinces' => $provinces,
                'customers' => $customers,
                'goods' => $goods,
            ]);
        }
    }

    //订单详细
    public function actionDetail(){
        $id = $_GET['id'];
        $order = Customer_order::find()->where("id =".$id)->asArray()->one();
        $goods = Customer_order_goods::find()->where("order_id =".$id)->asArray()->all();
        $customer = Customer::find()->where("id =".$order['customer_id'])->asArray()->one();
        $count = count($goods);
        return $this->render('order_detail',[
            'order' => $order,
            'goods' => $goods,
            'customer' => $customer,
            'count' => $count,
        ]);
    }

    //将商品添加到临时购物车
    public function actionAdd_to_cart(){
        if(Yii::$app->request->post()){
            $goods_id = $_POST['goods_id'];
            if(!empty($goods_id)){
                $user_id = Yii::$app->session['user_id'];
                $goods = Goods::find()->where("goods_id =".$goods_id)->asArray()->one();
                $cart = Customer_cart::find()->where("goods_id =".$goods_id)->andWhere("user_id =".$user_id)->one();
                if(!empty($goods) && empty($cart)){
                    $cart_data = new Customer_cart();
                    $cart_data->goods_id = $goods_id;
                    $cart_data->goods_name = $goods['goods_name'];
                    $cart_data->user_id = $user_id;
                    $cart_data->nums = 1;
                    if($cart_data->save()){
                        echo 111;
                    }else{
                        echo 222;
                    }
                }
            }
        }
        exit;
    }

    //获取临时购物车的产品信息啊
    public function actionGet_cart_data(){
        if(Yii::$app->request->post()) {
            $user_id = Yii::$app->session['user_id'];
            $customer_id = $_POST['customer_id'];
            //会员账号
            $customer = Customer::find()->where("id =".$customer_id)->asArray()->one();
            //会员等级
            $rank = Customer_type::find()->where("rank_id =".$customer['type_id'])->asArray()->one();
            if (!empty($user_id)) {
                $data = Customer_cart::find()->where("user_id =" . $user_id)->asArray()->all();
                $cart_data = array();
                $total_price = 0;
                foreach($data as $val):
                    $goods = Goods::find()->where("goods_id =".$val['goods_id'])->asArray()->one();
                    //会员对应价钱
                    $member_price = Member_price::find()->where("goods_id =".$goods['goods_id'])->andWhere("user_rank =".$customer['type_id'])->asArray()->one();
                    if(!empty($member_price)){
                        $price = $member_price['user_price'];
                    }else{
                        $price = $goods['shop_price']*($rank['discount']/100);
                    }
                    $return_data = array();
                    $return_data['goods_name'] = $goods['goods_name'];
                    $return_data['shop_price'] = $goods['shop_price'];  //售卖价钱
                    $return_data['user_price'] = $price;  //会员等级对应的价钱
                    $return_data['nums'] = $val['nums'];  //数量
                    $return_data['cart_id'] = $val['id'];
                    $goods_total_price = $val['nums']*$price;  //产品小计价钱
                    $total_price += $goods_total_price;  //订单总价
                    $return_data['total_price'] = $goods_total_price;
                    $cart_data[] = $return_data;
                endforeach;
                //输出数据
                echo "<tr><th>商品</th><th>客户价格</th><th>数量</th><th></th></tr>";
                foreach($cart_data as $val):
                    echo "<tr>";
                    echo "<td>".$val['goods_name']."</td>";
//                    echo "<td>￥".$val['shop_price']."</td>";
                    echo "<td>￥".$val['user_price']."</td>";
                    echo "<td id='cart".$val['cart_id']."' ondblclick='to_change_nums(".$val['cart_id'].");'>".$val['nums']."</td>";
//                    echo "<td>￥".$val['total_price']."</td>";
                    echo "<td><a href='#' onclick='del_cart(".$val['cart_id'].");'>X</a></td>";
                    echo "</tr>";
                endforeach;
                echo "<tr><td colspan='6'><div align='right'>订单总价：￥".$total_price."</div></td></tr>";
                exit;
            }
        }
    }

    public function actionDel_cart(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            if(Customer_cart::deleteAll("id =".$id)){
                echo 111;
                exit;
            }
        }
    }

    //修改数量
    public function actionChange_cat_num(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $num = $_POST['new_num'];
            $cart = Customer_cart::find()->where("id =".$id)->one();
            $cart->nums = $num;
            if($cart->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

    //作废订单
    public function actionDel(){
        if(Yii::$app->request->post()){
                $id = $_POST['id'];
            $order = Customer_order::find()->where("id =".$id)->one();
            $order->status = 0;
            if($order->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }
}