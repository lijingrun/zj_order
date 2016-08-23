<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/12
 * Time: 21:24
 */
namespace backend\controllers;
use common\models\Additional_goods;
use common\models\Additional_orders;
use common\models\Car_add2Form;
use common\models\Car_model;
use common\models\Car_reason;
use common\models\Car_style;
use common\models\Car_type;
use common\models\Carema;
use common\models\Cars;
use common\models\Coupon;
use common\models\Evaluate;
use common\models\Index;
use common\models\Member_cons_point;
use common\models\Member_coupon;
use common\models\Member_discount;
use common\models\Members;
use common\models\Order_back;
use common\models\Orders;
use common\models\Package;
use common\models\Package_goods;
use common\models\Package_member;
use common\models\Point_transfer;
use common\models\Service;
use common\models\Service_goods;
use common\models\Goods;
use common\models\Goods_type;
use common\models\Order_goods;
use common\models\Service_price;
use common\models\Car_brand;
use common\models\MemberRegisterForm;
use common\models\MemberLoginForm;
use common\models\Service_type;
use common\models\Store;
use common\models\Weixin;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class MemberController extends Controller{

    public $layout = 'mobile';

    public function actionIndex(){
        if(empty(Yii::$app->session['member_id'])) {
            //判断是否微信登录
            $weixin_code = Yii::$app->request->get('code');
            $weixin_appid = Yii::$app->request->get('appid');
            if (!empty($weixin_code)) {
                Yii::$app->session['wechat_code'] = $weixin_code;
                Yii::$app->session['wechat_appid'] = Yii::$app->request->get('appid');
                // testing
                $appid = $_GET['id'];
                $weixin = Weixin::find()->where(['appid' => $appid])->one();
                $secret = $weixin->app_secret;
                Yii::$app->session['wechat_appid'] = $appid;
                $code = Yii::$app->request->get('code');

                $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='
                    . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $get_token_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $res = curl_exec($ch);
                curl_close($ch);
                $json_obj = json_decode($res, true);
                $access_token = $json_obj['access_token'];
                $openid = $json_obj['openid'];

//                $weixin['access_token'] = $access_token;
//                $weixin->save();

                Yii::$app->session['wechat_openid'] = $openid;

                if (!empty($openid)) {

                    $member_record = Members::find()->where(['open_id' => $openid, 'weixin_id' => $appid])->asArray()->one();
                    if (empty($member_record)) {
                        return $this->redirect(array('/member/register'));
                    } else {
                        Yii::$app->session['member_id'] = $member_record['id'];
                        return $this->render('index.php?r=member/core');
                    }
                } else {
                    return $this->redirect(array('/member/register'));
                }
            } else {
                return $this->redirect('index.php?r=member_login/login');
            }
        }else {
            //不为空的时候，查openid并保存
            $weixin_code = Yii::$app->request->get('code');
            if (!empty($weixin_code)) {
                $member = Members::find()->where(['id' => Yii::$app->session['member_id']])->asArray()->one();
                if(empty($member['open_id'])){
                    Yii::$app->session['wechat_code'] = $weixin_code;
                    Yii::$app->session['wechat_appid'] = Yii::$app->request->get('appid');
                    // testing
                    $appid = $_GET['id'];
                    $weixin = Weixin::find()->where(['appid' => $appid])->one();
                    $secret = $weixin->app_secret;
                    Yii::$app->session['wechat_appid'] = $appid;
                    $code = Yii::$app->request->get('code');

                    $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='
                        . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $get_token_url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    $res = curl_exec($ch);
                    curl_close($ch);
                    $json_obj = json_decode($res, true);
                    $access_token = $json_obj['access_token'];
                    $openid = $json_obj['openid'];

                    $weixin['access_token'] = $access_token;
//                    $weixin->save();
                    Yii::$app->session['wechat_openid'] = $openid;
                    $member = Members::find()->where(['id' => Yii::$app->session['member_id']])->one();
                    $member->open_id = $openid;
                    $member->weixin_id = $appid;
                    $member->save();
                }
            }
            $my_cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
            $index_images = Index::find()->asArray()->orderBy('order')->all();
            $need = false;
            //如果有任何一辆车无完善信息，就需要完善
            foreach($my_cars as $car):
                if(empty($car['style_id']) || empty($car['car_code'])){
                    $need = true;
                    break;
                }
            endforeach;
			return $this->render('index',[
                'need' => $need,
                'cars' => $my_cars,
                'index_images' => $index_images,
            ]);
		}
    }

    //销售具体页面
    public function actionInterface(){
        $id = $_GET['id'];
        $index = Index::find()->where(['id' => $id])->asArray()->one();
        return $this->render('interface',[
            'index' => $index,
        ]);
    }

    //订单页面
    public function actionMy_order(){
        $cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        if(empty($cars)){
//            Yii::$app->getSession()->setFlash('error','您还未登记任何车辆，请先登记车辆信息！');
            return $this->redirect('index.php?r=member/car_detail');
        }
        //先查所有车辆，再查车辆下面的所有工单
        $my_cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        $car_ids = array();
        foreach($my_cars as $val):
            $car_ids[] = $val['id'];
        endforeach;
        //查是否有还未确认的加单提醒之类的
        $resaons = Car_reason::find()->where(['status' => 1])->andWhere(['in','car_id',$car_ids])->asArray()->all();
        $all_orders = Orders::find()->where(['in','car_id',$car_ids])->orderBy('create_time desc');
        $pages = new Pagination([
            'totalCount' => $all_orders->count(),
            'pageSize' => 5,
        ]);
        $back = Order_back::find()->where(['member_id' => Yii::$app->session['member_id']])->count();
        $orders = $all_orders->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        foreach($orders as $key=>$val):
            $orders[$key]['car'] = Cars::find()->where(['id' => $val['car_id']])->asArray()->one();
        endforeach;
        return $this->render('my_orders',[
            'orders' => $orders,
            'pages' => $pages,
            'back' => $back,
            'resaons' => $resaons,
        ]);
    }

    //评价
    public function actionEvaluate(){
        $order_id = $_GET['order_id'];
        $order = Orders::find()->where(['id' => $order_id])->andWhere(['status' => 40])->asArray()->one();
        if(Yii::$app->request->post()){
            $evluate = new Evaluate();
            $evluate->use_time = $_POST['use_time'];
            $evluate->service = $_POST['service'];
            $evluate->com = $_POST['com'];
            $evluate->craft = $_POST['craft'];
            $evluate->content = $_POST['content'];
            $evluate->order_id = $order_id;
            $evluate->created_time = time();
            $order = Orders::find()->where(['id' => $order_id])->andWhere(['status' => 40])->one();
            $order->status = 50;
            if($evluate->save() && $order->save()){
                return $this->redirect('index.php?r=member/my_order');
            }
        }else{
            return $this->render('evaluate',[
                'order' => $order,
            ]);
        }
    }

    //查看评价详情
    public function actionEvaluate_detail(){
        $order_id = $_GET['order_id'];
        $evaluate = Evaluate::find()->where(['order_id' => $order_id])->asArray()->one();
        return $this->render('evaluate_detail',[
            'evaluate' => $evaluate,
        ]);
    }

    //返工申请列表
    public function actionOrder_back_list(){
        $backs = Order_back::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        return $this->render('back_list',[
            'backs' => $backs,
        ]);
    }

    //客户要求返工
    public function actionBack_work(){
        if(Yii::$app->request->post()){
            $why = $_POST['why'];
            $order_no = $_POST['order_no'];
            $order = Orders::find()->where(['order_no' => $order_no])->asArray()->one();
            $car = Cars::find()->where(['id' => $order['car_id']])->asArray()->one();
            //增加返工申请
            $order_back = new Order_back();
            $order_back->member_id = Yii::$app->session['member_id'];
            $order_back->order_no = $order_no;
            $order_back->why = $why;
            $order_back->worker_id = $order['worker_id'];
            $order_back->store_id = $order['store_id'];
            $order_back->created_time = time();
            $order_back->car_no = $car['car_no'];
            $order_back->car_id = $car['id'];
            if($order_back->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

    //微信支付返回
    public function actionWeixin_notify(){
        return "SUCCESS";
    }

    //到支付页面
    public function actionGoto_pay(){
        $order_id = $_GET['order_id'];
        $order = Orders::find()->where(['id' => $order_id])->asArray()->one();
        if(empty($order['id'])){
            echo  '该订单不存在！';
            exit;
        }
        if($order['status'] != 30){
            echo "工单不是付款环节，不能付款！";
            exit;
        }
        if(Yii::$app->request->post()){
            print_r($_POST);exit;
        }else {
            return $this->render('choose_pay_type', [
                'order_id' => $order_id,
            ]);
        }
    }

    //我的卡包
    public function actionMy_coupon(){
        $member_id = Yii::$app->session['member_id'];
        //查名下所有的优惠卷
        $coupons = Member_coupon::find()->where(['member_id' => $member_id])->andWhere(['>','end_time',time()])->andWhere(['status' => 2])->asArray()->all();
        foreach($coupons as $key=>$coupon):
            $coupons[$key]['coupon_id'] = Coupon::find()->where(['coupon_id' => $coupon['coupon_id']])->asArray()->one();
        endforeach;
        return $this->render('my_coupons',[
            'coupons' => $coupons,
        ]);
    }

    //新增订单页面
    public function actionOrder_add(){
        $my_cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        if(count($my_cars) == 1){
            $car = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->one();
        }
//        $services = Service::find()->asArray()->all();
        $service_types = Service_type::find()->all();
        if (Yii::$app->request->post()) {
            $car_id = $_POST['car_id'];
            $service_id = $_POST['service_id'];
            $service = Service::find()->where(['id' => $service_id])->asArray()->one();
            //检查工单池里面有无未开工的新工单，有就用同一个order_no，无就新建一个
            $order = Orders::find()->where(['car_id' => $_POST['car_id']])->andWhere(['status' => 10])->asArray()->one();
            if (!empty($order)) {
                $order_no = $order['order_no'];
            } else {
                $order_no = $this->_get_order_no();
            }
            $new_order = new Orders();
            $new_order->order_no = $order_no;
            $new_order->service_id = $service['id'];
            $new_order->service_name = $service['name'];
            $new_order->status = 10; //客户新建，未有店铺接收
            $new_order->create_time = time();
            $new_order->price = $service['price'];
            $new_order->car_id = $car_id;
            $goods_ids = $_POST['goods_ids'];
            if($new_order->save()){
                if(!empty($goods_ids)) {
                    foreach ($goods_ids as $goods_id):
                        if ($goods_id != 0) {
                            $goods = Goods::find()->where(['goods_id' => $goods_id])->asArray()->one();
                            $service_goods = Service_goods::find()->where(['service_id' => $service_id])->andWhere(['goods_type' => $goods['goods_type']])->asArray()->one();
                            $order_goods = new Order_goods();
                            $order_goods->order_no = $order_no;
                            $order_goods->goods_id = $goods_id;
                            $order_goods->goods_name = $goods['goods_name'];
                            $order_goods->price = $goods['price'];
                            $order_goods->nums = $service_goods['nums'];
                            $order_goods->save();
                        }
                    endforeach;
                }
//                Yii::$app->getSession()->setFlash('success','下单成功！');
            }else{
//                Yii::$app->getSession()->setFlash('error','系统繁忙，请稍后重试');
            }
            return $this->redirect("index.php?r=member/order&order_id=".$new_order['id']);
        }else{

            return $this->render('add_order',[
                'car' => $car,
                'my_cars' => $my_cars,
                'my_cars' => $my_cars,
                'service_types' => $service_types,
            ]);
        }




    }

    //客户预约工单选择店铺
    public function actionChoose_store(){
        $order_no = $_GET['order_no'];
        $stores = Store::find()->asArray()->all();
        if(Yii::$app->request->post()){
            $store_id = $_POST['store_id'];
            $orders = Orders::find()->where(['order_no' => $order_no])->all();
            foreach($orders as $order):
                $order->store_id = $store_id;
                $order->save();
            endforeach;
            return $this->redirect('index.php?r=member/my_order');
        }else {
            return $this->render('choose_store', [
                'stores' => $stores,
            ]);
        }
    }

    //订单详细页面
    public function actionOrder(){
        $order_id = $_GET['order_id'];
        $order= Orders::find()->where(['id' => $order_id])->asArray()->one();
        $order['car'] = Cars::find()->where(['id' => $order['car_id']])->asArray()->one();
        $order_goods = Order_goods::find()->where(['order_id' => $order['id']])->asArray()->all();
        foreach($order_goods as $key=>$goods):
            $order_goods[$key]['goods_id'] = Goods::find()->where(['goods_id' => $goods['goods_id']])->asArray()->one();
        endforeach;
        //查同一个工单号下面所有的工单
        $other_orders = Orders::find()->where(['order_no' => $order['order_no']])->asArray()->all();
        $store = Store::find()->where(['store_id' => $order['store_id']])->asArray()->one();
        return $this->render('order_detail',[
            'order' => $order,
            'goods' => $order_goods,
            'other_orders' => $other_orders,
            'store' => $store,
        ]);
    }

    //查看现场
    public function actionCheck(){
        $order_id = $_GET['order_id'];
        $order = Orders::find()->where(['id' => $order_id])->asArray()->one();
        $carema = Carema::find()->where(['worker_id' => $order['worker_id']])->asArray()->one();
//        print_r($carema);exit;
        return $this->render('check',[
            'carema' => $carema,
        ]);
    }

    //车辆信息
    public function actionMy_cars(){
        $cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        foreach($cars as $key=>$car):
            $cars[$key]['brand'] = Car_model::find()->where(['id' => $car['brand']])->asArray()->one();
            $cars[$key]['type'] = Car_type::find()->where(['type_id' => $car['car_type']])->asArray()->one();
        endforeach;
        if(empty($cars)){
//            Yii::$app->getSession()->setFlash('error','您还未登记任何车辆，请先登记车辆信息！');
            return $this->redirect('index.php?r=member/car_detail');
        }
        return $this->render('my_cars',[
            'cars' => $cars,
        ]);
    }

    //修改车辆信息
    public function actionCar_detail(){
        $car_id = $_GET['car_id'];
        $message = $_GET['message'];
        $car = Cars::find()->where(['id' => $car_id])->andWhere(['member_id' => Yii::$app->session['member_id']])->asArray()->one();
        $car['brand'] = Car_model::find()->where(['id' => $car['brand']])->asArray()->one();
        $types = Car_type::find()->asArray()->all();
        if(Yii::$app->request->post()){
            if(!empty($car_id)){
                $car = Cars::find()->where(['id' => $car_id])->andWhere(['member_id' => Yii::$app->session['member_id']])->one();
            }else{
                $check_cars = Cars::find()->where(['car_no' => $_POST['car_no']])->count();
                if($check_cars == 0) {
                    $car = new Cars();
                    $car->car_no = $_POST['car_no'];
                    $car->member_id = Yii::$app->session['member_id'];
                }else{
                    echo "车牌号码已经被注册了，请<a href='index.php?r=member/car_detail'>重新填写</a>";
                    exit;
                }
            }
            if(!empty($_POST['brand_id'])) {
                $brand = Car_brand::find()->where(['brand_id' => $_POST['brand_id']])->asArray()->one();
                $car->brand = $brand['brand_id'];
                $car->brand_name = $brand['brand_name'];
            }
            if(!empty($_POST['model'])){
                $model = Car_model::find()->where(['id' => $_POST['model']])->asArray()->one();
                $car->model_id = $model['id'];
                $car->model_name = $model['model_name'];
            }
            if(!empty($_POST['style_id'])) {
                $style = Car_style::find()->where(['id' => $_POST['style_id']])->asArray()->one();
                $car->style_id = $style['id'];
                $car->style_name = $style['style_name'];
            }
            $car->car_code = $_POST['car_code'];
            $car->buy_year = $_POST['buy_year'];
            $car->engine_type = $_POST['engine_type'];
            if($car->save()){
                return $this->redirect('index.php?r=member/my_cars');
            }else{
                return $this->redirect('index.php?r=member/my_cars');
            }
        }else{
            return $this->render('car_detail',[
                'car' => $car,
                'types' => $types,
                'message' => $message,
            ]);
        }
    }

    //获取车辆品牌
    public function actionGet_car_brand(){
        $car_brands = Car_brand::find()->orderBy('CONVERT(brand_name USING GBK) ASC')->asArray()->all();
        echo "<select onchange='get_models();' id='brand_id' name='brand_id'>";
        echo "<option value='0'>请选择品牌</option>";
        foreach($car_brands as $brand):
            echo "<option value='".$brand['brand_id']."' >". 
				$this->getFirstCharter($brand['brand_name']). ' - ' .$brand['brand_name']."</option>";
        endforeach;
        echo "</selece>";
        exit;
    }

    //获取车辆型号
    public function actionGet_models(){
        $brand_id = $_POST['brand_id'];
        $models = Car_model::find()->where(['brand_id' => $brand_id])->asArray()->all();
        echo "<div><select id='model' name='model' onchange='get_style();'>";
        echo "<option value='0'>请选择款式</option>";
        foreach($models as $model):
            echo "<option value='".$model['id']."'>".$model['model_name']."(".$model['year'].")"."</option>";
        endforeach;
        echo "</select></div>";
        exit;
    }

    //工单获取车辆型号
    public function actionGet_style(){
        if(Yii::$app->request->post()){
            $model_id = $_POST['model_id'];
            $styles = Car_style::find()->where(['model_id' => $model_id])->asArray()->all();
            echo "<div id='style_list'><select  id='style' name='style_id'>";
            foreach($styles as $style):
                echo "<option value='".$style['id']."' id='style".$style['id']."'>".$style['style_name']."</option>";
            endforeach;
            echo "</select>";
            echo "</div>";
            exit;
        }
    }

    //车辆订单信息
    public function actionCar_orders(){
        $car_id = $_GET['car_id'];
        $car = Cars::find()->where(['id' => $car_id])->asArray()->one();
        if($car['member_id'] != Yii::$app->session['member_id']){
            return $this->redirect('index.php?r=member');
        }
        $all_orders = Orders::find()->where(['car_id' => $car_id])->orderBy('create_time desc');
        $pages = new Pagination([
            'totalCount' => $all_orders->count(),
            'pageSize' => 10,
        ]);
        $orders = $all_orders->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $reason = Car_reason::find()->where(['car_id' => $car_id])->andWhere(['status' => 1])->asArray()->one();
        return $this->render('car_orders',[
            'orders' => $orders,
            'pages' => $pages,
            'car' => $car,
            'reason' => $reason,
        ]);
    }

    //追加订单提醒,2016-5-27,这个方法是客户自己手动加单，后来改成前台加单
    public function actionReason(){
        $car_id = $_GET['car_id'];
        //无车id就查当前账号所有，有车id就只查该车
        if(empty($car_id)) {
            $member_id = Yii::$app->session['member_id'];
            $cars = Cars::find()->where(['member_id' => $member_id])->asArray()->all();
            $car_ids = array();
            foreach ($cars as $car):
                $car_ids[] = $car['id'];
            endforeach;
            $reasons = Car_reason::find()->where(['in', 'car_id', $car_ids])->andWhere(['status' => 1])->asArray()->all();
        }else{
            $reasons = Car_reason::find()->where([ 'car_id'=> $car_id])->andWhere(['status' => 1])->asArray()->all();
        }
        foreach($reasons as $key=>$reason):
            $reason_goods = Additional_goods::find()->where(['reason_id' => $reason['id']])->asArray()->all();
            $r_goods_ids = array();
            foreach($reason_goods as $val):
                $r_goods_ids[] = $val['goods_id'];
            endforeach;
            if(!empty($r_goods_ids)){
                $r_goods_ids = implode(',',$r_goods_ids);
                $reasons[$key]['reason_goods'] = Goods::find()->where("goods_id in (".$r_goods_ids.")")->asArray()->all();
            }
        endforeach;
        return $this->render('reason',[
            'reasons' => $reasons,
        ]);
    }

    //客户同意加单
    public function actionSure_to_add(){
        if(Yii::$app->request->post()){
            $reason_id = $_POST['id'];
            $reason = Car_reason::find()->where(['id' => $reason_id])->one();
            $order_no = $reason->order_no;
            $reason->status = 2; //改变加单提醒的状态为同意
            //新增工单
//            $reason_order = Additional_orders::find()->where(['reason_id' => $reason_id])->asArray()->one();
            $order = Orders::find()->where(['order_no' => $order_no])->asArray()->one();
            $new_order = new Orders();
            $new_order->order_no = $order_no;
            $new_order->service_id = $reason['service_id'];
            $new_order->service_name = $reason['service_name'];
            $new_order->create_time = time();
            $new_order->begin_time = $order['begin_time'];
            $new_order->checked_time = $order['checked_time'];
            $new_order->store_id = $order['store_id'];
            $new_order->car_id = $order['car_id'];
            $new_order->worker_id = $order['worker_id'];
            $new_order->status = $order['status'];
            $new_order->mileage = $order['mileage'];
            $new_order->mileage = $order['mileage'];
            $new_order->mileage = $order['mileage'];
            $new_order->appointment_id = Yii::$app->session['member_id'];
            $new_order->reason = $reason->reason;
            $new_order->take_sp = $order['take_sp'];
            //转换成真正工单后，原来的删除
//            if($new_order->save()){
//                Additional_orders::deleteAll(['reason_id' => $reason_id]);
//            }
            //保存商品
//            $reason_goods = Additional_goods::find()->where(['reason_id' => $reason_id])->asArray()->all();
//            foreach($reason_goods as $goods):
//                $new_order_goods = new Order_goods();
//                $new_order_goods->order_no = $goods['order_no'];
//                $new_order_goods->goods_id = $goods['goods_id'];
//                $new_order_goods->goods_name = $goods['goods_name'];
//                $new_order_goods->price = $goods['price'];
//                $new_order_goods->nums = $goods['nums'];
//                $new_order_goods->order_id = $new_order['id'];
//                $new_order_goods->save();
//            endforeach;
            if($new_order->save() && $reason->save()){
                echo 111;
            }
            exit;
        }
    }

    //添加订单
    public function actionAdd_order()
    {
        $car_id = $_GET['car_id'];
        $my_cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        $car = Cars::find()->where(['id' => $car_id])->asArray()->one();
//        if(empty($car['style_id']) && empty($car['car_code'])){
//            $message =  '请完善车辆资料，以便我们能更好未您服务！';
//            return $this->redirect('index.php?r=member/car_detail&car_id='.$car_id."&message=".$message);
//        }
        $need_code = false;
        if(empty($car['car_code'])){
            $need_code = true;
        }
        if ($car['member_id'] != Yii::$app->session['member_id']) {
            return $this->redirect('index.php?r=member');
        }
//        $services = Service::find()->asArray()->all();
        $service_types = Service_type::find()->asArray()->all();
        if (Yii::$app->request->post()) {
            $service_id = $_POST['service_id'];
            $mileage = $_POST['mileage'];
            $car_code = $_POST['car_code'];
            //如果输入了车架码，就直接保存到车辆信息里面
            if(!empty($car_code)) {
                $car = Cars::find()->where(['id' => $car_id])->one();
                $car->car_code = $car_code;
                $car->save();
            }
            $service = Service::find()->where(['id' => $service_id])->asArray()->one();
            //检查工单池里面有无未开工的新工单，有就用同一个order_no，无就新建一个
            $order = Orders::find()->where(['car_id' => $_POST['car_id']])->andWhere(['status' => 10])->asArray()->one();
            if (!empty($order)) {
                $order_no = $order['order_no'];
            } else {
                $order_no = $this->_get_order_no();
            }
            $new_order = new Orders();
            $new_order->order_no = $order_no;
            $new_order->mileage = $mileage;
            $new_order->service_id = $service['id'];
            $new_order->take_sp = $_POST['take_sp'];
            $new_order->service_name = $service['name'];
            $new_order->status = 10; //客户新建，未有店铺接收
            $new_order->create_time = time();
            $new_order->appointment = 1; //是预约单
            $new_order->appointment_id = Yii::$app->session['member_id']; //预约客户账号
//            $new_order->price = $service['price'];  工时费结算时候先插入
            $new_order->car_id = $car_id;
            $goods_ids = $_POST['goods_ids'];
            $goods_nums = $_POST['goods_nums'];
            if($new_order->save()){
                if(!empty($goods_ids)) {
                    foreach ($goods_ids as $key=>$goods_id):
                        if ($goods_id != 0) {
                            $goods = Goods::find()->where(['goods_id' => $goods_id])->asArray()->one();
                            $order_goods = new Order_goods();
                            $order_goods->order_no = $order_no;
                            $order_goods->order_id = $new_order['id'];
                            $order_goods->goods_id = $goods_id;
                            $order_goods->goods_name = $goods['goods_name'];
                            $order_goods->price = $goods['price'];
                            $order_goods->nums = $goods_nums[$key];
                            $order_goods->save();
                        }
                    endforeach;
                }
//                Yii::$app->getSession()->setFlash('success','下单成功！');
            }else{
//                Yii::$app->getSession()->setFlash('error','系统繁忙，请稍后重试');
            }
            return $this->redirect("index.php?r=member/order&order_id=".$new_order['id']);
        }else{

            //查是否有未开工的订单，是的话直接获取这些订单的公里数
            $order = Orders::find()->where(['status' => 10])->orWhere(['status' => 11])->andWhere(['car_id' => $car_id])->orderBy('create_time')->asArray()->one();
            $mileage = $order['mileage'];
            return $this->render('add_order',[
                'car' => $car,
                'my_cars' => $my_cars,
//                'services' => $services,
                'mileage' => $mileage,
                'need_code' => $need_code,
                'service_types' => $service_types,
            ]);
        }
    }

    //根据类型获取服务
    public function actionFind_service(){
        if(Yii::$app->request->post()){
            $type_id = $_POST['type_id'];
            $services = Service::find()->where(['type_id' => $type_id])->asArray()->all();
            echo "<select name=\"service_id\" onchange=\"find_goods();\" id=\"service_id\">";
            echo "<option value=\"0\">请选择需要的服务</option>";
            foreach($services as $service):
                echo "<option value='".$service['id']."'>".$service['name']."</option>";
            endforeach;
            echo "</select>";
            exit;
        }
    }

    //获取工时介绍
    public function actionWorker_content(){
        $service_id = $_POST['service_id'];
        $service = Service::find()->where(['id' => $service_id])->asArray()->one();
        echo "<div id='worker_content_list'>";
        echo $service['worker_content'];
        echo "</div>";
        exit;
    }

    //客户主动加单
    public function actionAdditional(){
        $reason_id = $_GET['reason_id'];
        $reason = Car_reason::find()->where(['id' => $reason_id])->asArray()->one();
        $car_id = $reason['car_id'];
        $my_cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        $car = Cars::find()->where(['id' => $reason['car_id']])->asArray()->one();
        //查对应的服务内容
        $service = Service::find()->where(['id' => $reason['service_id']])->asArray()->one();       //需要增加的服务
        $service_type = Service_type::find()->where(['type_id' => $service['type_id']])->asArray()->one();  //对应的类型
        $service_types = Service_type::find()->asArray()->all();
        //查对应类型的服务
        //查对应订单，是的话直接获取这些订单的公里数
        $order = Orders::find()->where(['order_no' => $reason['order_no']])->asArray()->one();
        $mileage = $order['mileage'];
        if (Yii::$app->request->post()) {
            $service_id = $_POST['service_id'];
            $mileage = $_POST['mileage'];
            $service = Service::find()->where(['id' => $service_id])->asArray()->one();
            if (!empty($order)) {
                $order_no = $order['order_no'];
            } else {
                $order_no = $this->_get_order_no();
            }
            $new_order = new Orders();
            $new_order->order_no = $order_no;
            $new_order->mileage = $mileage;
            $new_order->service_id = $service['id'];
            $new_order->service_name = $service['name'];
            $new_order->status = 20; //如果下单，直接合并到之前的工单,如果工人完工，就会将所有的加单提醒取消
            $new_order->create_time = time();
            $new_order->appointment = 0;
            $new_order->take_sp = $order['take_sp'];
            //$new_order->appointment_id = Yii::$app->session['member_id']; //预约客户账号
            $new_order->car_id = $car_id;
            $new_order->worker_id = $order['worker_id'];
            $new_order->store_id = $order['store_id'];
            $new_order->checked_time = $order['checked_time'];
            $new_order->reason = $reason['reason'];
            $new_order->begin_time = $order['begin_time'];
            $goods_ids = $_POST['goods_ids'];
            $goods_nums = $_POST['goods_nums'];
            if($new_order->save()){
                if(!empty($goods_ids)) {
                    foreach ($goods_ids as $key=>$goods_id):
                        if ($goods_id != 0) {
                            $goods = Goods::find()->where(['goods_id' => $goods_id])->asArray()->one();
                            $order_goods = new Order_goods();
                            $order_goods->order_no = $order_no;
                            $order_goods->order_id = $new_order['id'];
                            $order_goods->goods_id = $goods_id;
                            $order_goods->goods_name = $goods['goods_name'];
                            $order_goods->price = $goods['price'];
                            $order_goods->nums = $goods_nums[$key];
                            $order_goods->save();
                        }
                    endforeach;
                }
                $reason_edit = Car_reason::find()->where(['id' => $reason_id])->one();
                $reason_edit->status = 2;
                $reason_edit->save();
                return $this->redirect("index.php?r=member/order&order_id=".$new_order['id']);
            }else{
                echo "服务器繁忙，请稍后重试！";
                return;
            }

        }else{
            return $this->render('add_order',[
                'car' => $car,
                'my_cars' => $my_cars,
                'mileage' => $mileage,
                'service_types' => $service_types,
                'service' => $service,
                'service_type' => $service_type,
                'order' => $order,
            ]);
        }
    }

    //客户套餐
    public function actionMy_packages(){
        $packages = Package_member::find()->where(['member_id' => Yii::$app->session['member_id']])->andWhere("nums > 0")->asArray()->all();
        $my_cars = Cars::find()->where(['member_id' => Yii::$app->session['member_id']])->asArray()->all();
        $p_id = 0;
        foreach($packages as $key=>$package):
            $packages[$key]['goods'] = Package_goods::find()->where(['package_id' => $package['package_id']])->asArray()->all();
            $p_id = $package['package_id'];
        endforeach;
        $pack = Package::find()->where(['id' => $p_id])->asArray()->one();
        return $this->render('my_packages',[
            'packages' => $packages,
            'pack' => $pack,
            'my_cars' => $my_cars,
        ]);
    }

    //根据p_id生成工单
    public function actionCreate_package_order(){
        $p_id = $_POST['p_id'];
        $package = Package::find()->where(['id' => $p_id])->asArray()->one();
        $service = Service::find()->where(['id' => $package['service_id']])->asArray()->one();
        $p_member = Package_member::find()->where(['package_id' => $p_id])->where(['member_id' => Yii::$app->session['member_id']])->one();
        $p_member->nums -= 1;
        $p_member->save();
        $p_goods = Package_goods::find()->where(['package_id' => $p_id])->asArray()->all();
        $order = new Orders();
        $old_order = Orders::find()->where(['car_id' => $_POST['car_id']])->andWhere("status < 21")->asArray()->one();
        if(!empty($old_order)){
            $order_no = $old_order['order_no'];
            $worker_id = $old_order['worker_id'];
            $status = $old_order['status'];
            $store_id = $old_order['store_id'];
            $begin_time = $old_order['begin_time'];
            $take_sp = $old_order['take_sp'];
            $checked_time = $old_order['checked_time'];
            $mileage = $old_order['mileage'];
        }else{
            $order_no = $this->_get_order_no();
            $worker_id = 0;
            $status = 10;
            $store_id = 0;
            $begin_time = 0;
            $take_sp = 0;
            $mileage = 0;
            $checked_time = 0;
        }

        $order->order_no = $order_no;
        $order->checked_time = $checked_time;
        $order->service_id = $package['service_id'];
        $order->service_name = $service['name'];
        $order->create_time = time();
        $order->worker_id = $worker_id;
        $order->status = $status; //需要客户自己选择店铺
        $order->car_id = $_POST['car_id'];
        $order->appointment = '套餐预约';
        $order->begin_time = $begin_time;
        $order->store_id = $store_id;
        $order->take_sp = $take_sp;
        $order->mileage = $mileage;
        $order->appointment_id = Yii::$app->session['member_id'];
        $order->package_id = $p_id;

        $order->save();
        //保存订单商品
        foreach($p_goods as $val):
            $goods = Goods::find()->where(['goods_id' => $val['goods_id']])->asArray()->one();
            $order_goods = new Order_goods();
            $order_goods->order_no = $order_no;
            $order_goods->goods_id = $goods['goods_id'];
            $order_goods->goods_name = $goods['goods_name'];
            $order_goods->price = $goods['price'];
            $order_goods->package_id = $p_id;
            $order_goods->nums = $val['nums'];
            $order_goods->order_id = $order['id'];
            $order_goods->save();
        endforeach;
        echo 111;
        exit;
    }

    //客户放弃加单
    public function actionGive_up_reason(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $reason = Car_reason::find()->where(['id' => $id])->one();
            $reason->status = 0;
            $reason->give_up_time = time();
            if($reason->save() && Additional_goods::deleteAll(['reason_id' => $id]) && Additional_orders::deleteAll(['reason_id' => $id])){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

    //为订单添加产品
    public function actionOrder_goods_add()
    {
        $id = $_GET['order_id'];
        $order = Orders::find()->where(['id' => $id])->asArray()->one();
        $car = Cars::find()->where(['id' => $order['car_id']])->asArray()->one();
        //先查服务对应的产品的类型，再根据类型查找所有的对应车辆适用的产品给客户选择
        $service_goods = Service_goods::find()->where(['service_id' => $order['service_id']])->asArray()->all();
        $goods_type = array();
        foreach ($service_goods as $service_good):
            $goods_type[] = $service_good['goods_type'];
        endforeach;
        $goods_type = implode(',', $goods_type);
        if (!empty($goods_type)) {
            $goods = Goods::find()->where("style_ids like '%" . $car['style_id'] . "%'")->orWhere(['style_ids' => 'all'])->andWhere('goods_type in (' . $goods_type . ')')->asArray()->all();
        }
        //查找已经添加了的所有商品
        $has_add_goods = Order_goods::find()->where(['order_id' => $id])->asArray()->all();
        foreach($has_add_goods as $key=>$val):
            $has_add_goods[$key]['goods_id'] = Goods::find()->where(['goods_id' => $val['goods_id']])->asArray()->one();
        endforeach;
        if(Yii::$app->request->post()){
            $goods_ids = $_POST['goods_ids'];
            $goods_nums = $_POST['nums'];
            foreach($goods_ids as $key=>$goods_id){
                $the_goods = Goods::find()->where(['goods_id' => $goods_id])->asArray()->one();
                $new_order_goods = new Order_goods();
                $new_order_goods->order_no = $order['order_no'];
                $new_order_goods->order_id = $id;
                $new_order_goods->goods_id = $the_goods['goods_id'];
                $new_order_goods->goods_name = $the_goods['goods_name'];
                $new_order_goods->price = $the_goods['price'];
                $new_order_goods->nums = $goods_nums[$key];
                $new_order_goods->save();
            }
            Yii::$app->getSession()->setFlash('success','添加成功！');
            return $this->redirect('index.php?r=member/order_goods_add&order_id='.$id);
        }else{
            return $this->render('order_goods_add',[
                'goods' => $goods,
                'has_add_goods' => $has_add_goods,
            ]);
        }
    }

    //删除商品
    public function actionDel_goods(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            if(Order_goods::deleteAll('id ='.$id)){
                echo 111;
            }else{
                echo 222;
            }

        }
    }

    //根据service_id获取产品列表
    public function actionGet_goods(){
        if(Yii::$app->request->post()){
            echo "<p style='font-size: 20px;'><input type='button' class='btn-success' value='查看工时费详细' id='worker_content' onclick='find_worder_content();'></p>";
            $car_id = $_POST['car_id'];
            $car = Cars::find()->where(['id' => $car_id])->asArray()->one();
            $service_id = $_POST['service_id'];
            $service = Service::find()->where(['id' => $service_id])->asArray()->one();
            //查服务下面需要的商品类型
            $service_goods = Service_goods::find()->where(['service_id' => $service_id])->asArray()->all();
            //查对应的商品信息，并入数组
            foreach($service_goods as $key=>$service_good):
                //类型下面所有商品，如果那个类型的商品需要匹配车型的，就显示匹配车型的，如果不是，就显示全部的
                $goods_type = Goods_type::find()->where(['type_id' => $service_good['goods_type']])->asArray()->one();
                if($goods_type['need_style'] == 1){
                    $service_goods[$key]['goods'] = Goods::find()->where("style_ids like '%,".$car['style_id'].",%'")->andWhere(['goods_type' => $service_good['goods_type']])->asArray()->all();
                }else{
                    $service_goods[$key]['goods'] = Goods::find()->where(['goods_type' => $service_good['goods_type']])->asArray()->all();
                }
                //对应的类型
                $service_goods[$key]['goods_type'] = $goods_type;
            endforeach;
            //查服务对应的工时费
//            $service_price = Service_price::find()->where(['service_id' => $service_id])->asArray()->all();
//            //查汽车类型，并入工时费数组
//            if(!empty($service_price)){
//                foreach($service_price as $key=>$price):
//                        $service_price[$key]['car_type'] = Car_type::find()->where(['type_id' => $price['car_type']])->asArray()->one();
//                endforeach;
//            }

            //循环输入需要的数组
            foreach($service_goods as $service_good):
                echo "<div class=\"panel-heading\">";
                echo "<h4>".$service_good['goods_type']['name']."</h4></div><div class='panel-body'>";
                if(empty($service_good['goods'])){ echo "<p style='color:red'>本类型商品需要匹配车型，系统稍后会回复你这个配件的价格</p>";}
                foreach($service_good['goods'] as $good):
                        echo "<div><input type='checkbox' name='goods_ids[]' value='" . $good['goods_id'] . "' id='check_box" . $good['goods_id'] . "' onclick='input_nums(" . $good['goods_id'] . ");' />";
                        echo "<span style='font-size: 20px;'>" . $good['goods_name'] . $good['style'] . "(" . $good['spec'] . ")" . "--￥" . $good['price'] . "</span></div>";
                        echo "<p id='input" . $good['goods_id'] . "'></p>";

                endforeach;
                    echo "<input type='checkbox' ><span style='font-size: 20px;'>自带</span>";
                echo "</div>";
            endforeach;
            exit;
        }
    }

    //添加车辆信息
    public function actionCar_add(){
        $car_brands = Car_brand::find()->asArray()->all();
        if(Yii::$app->request->post()){
            $check_car = Cars::find()->where(['car_no' => $_POST['car_no']])->count();
            if($check_car != 0) {
                $car = new Cars();
                $car->member_id = Yii::$app->session['member_id'];
                $car->car_no = $_POST['car_no'];
                if ($car->save()) {
                } else {
                }
                return $this->redirect("index.php?r=member/my_cars");
            }else{
                echo "车牌已经被注册了！请<a href='index.php?r=member/car_add'>重新填写</a>";
            }
        }else{
            return $this->render('car_add',[
//                'model' => $model,
                'car_brands' => $car_brands,
            ]);
        }
    }

    public function beforeAction($action)
    {
        if(empty(Yii::$app->session['member_id'])){
            $weixin_code = Yii::$app->request->get('code');
            $weixin_appid = Yii::$app->request->get('appid');
            if (!empty($weixin_code)) {
                Yii::$app->session['wechat_code'] = $weixin_code;
                Yii::$app->session['wechat_appid'] = Yii::$app->request->get('appid');
                // testing
                $appid = $_GET['id'];
                $weixin = Weixin::find()->where(['appid' => $appid])->one();
                $secret = $weixin->app_secret;
                Yii::$app->session['wechat_appid'] = $appid;
                $code = Yii::$app->request->get('code');

                $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='
                    . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $get_token_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $res = curl_exec($ch);
                curl_close($ch);
                $json_obj = json_decode($res, true);
                $access_token = $json_obj['access_token'];
                $openid = $json_obj['openid'];

                $weixin['access_token'] = $access_token;
//                $weixin->save();

                Yii::$app->session['wechat_openid'] = $openid;

                if (!empty($openid)) {

                    $member_record = Members::find()->where(['open_id' => $openid, 'weixin_id' => $appid])->asArray()->one();
                    if (empty($member_record)) {
                        return $this->redirect(array('index.php?r=member_login/login'));
                    } else {
                        Yii::$app->session['member_id'] = $member_record['id'];
                        return $this->redirect('index.php?r=member/core');
                    }
                } else {
                    return $this->redirect(array('index.php?r=member_login/login'));
                }
            } else {
                return $this->redirect('index.php?r=member_login/login');
            }
//            return $this->redirect('index.php?r=member_login/login');

        }else{
//            Yii::$app->session['member_id'] = null;
            return $action;
        }
    }

    //个人中心
    public function actionCore(){
        $member = Members::find()->where(['id' => Yii::$app->session['member_id']])->asArray()->one();
        $member_cons_point = Member_cons_point::find()->where(['member_id' => $member['id']])->andWhere(['>','ev_time',time()])->andWhere(['>','surplus',0])->asArray()->all();
        $cons_point = 0;
        foreach($member_cons_point as $point):
            $cons_point += $point['surplus'];
        endforeach;
        $member['type'] = Member_discount::find()->where(['member_type' => $member['type']])->asArray()->one();
        return $this->render('core',[
            'member' => $member,
            'cons_point' => $cons_point,
        ]);
    }

    //客户自检
    public function actionCheck_myself(){
        if(Yii::$app->request->post()){
            $car_id = $_POST['car_id'];
            $mileage = $_POST['mileage'];
            //查最上一次的订单检测里程，mileage必须大于这个
            $last_service = Orders::find()->where(['car_id' => $car_id])->orderBy('mileage desc')->asArray()->one();
            if(!empty($last_service)) {
                if ($last_service['mileage'] > $mileage) {
                    echo "<div style='font-size: 18px;'>";
                    echo "你输入的里程小于上次维修里程!<br />";
                    echo "<div style='padding-top:20px;'><a href='index.php?r=member'><input type='button' value='重新输入' class='btn-success' /></a></div>";
                    echo "</div>";
                    exit;
                }
            }
            //先查客户是否有任何维修记录
            $has_service = Orders::find()->where(['car_id' => $car_id])->andWhere(['>','status','30'])->one();
            if(!empty($has_service)) {
                //获取所有有下次修理里程的服务类型
                $need_check_services = Service::find()->where('check_km > 0')->asArray()->all();
                //获取到当前的里程后，查询该车辆在系统中的各种服务，对比相对于最上一次服务的里程，看距离下次进行该服务需要多少里程
                foreach ($need_check_services as $service):
                    $last_order = Orders::find()->where(['car_id' => $car_id])->andWhere(['service_id' => $service['id']])->andWhere(['>','status','30'])->orderBy('create_time desc')->asArray()->one();
                    if(!empty($last_order)) {
                        $next_mileage = $mileage - $last_order['mileage']; //上次服务到现在的里程
                        //如果上次服务到现在的里程大于或者等于维修里程，就提示需要进店检测，否则，提示还有多少里程就需要到店检测
                        if ($next_mileage >= $service['check_km']) {
                            echo '<div class="alert alert-warning" role="alert"><strong>注意！</strong>' . $service['name'] . '已经到了下次维护里程，请尽快到店检测</div>';
                        } else {
                            echo '<div class="alert alert-success" role="alert">还有' . ($service['check_km'] - $next_mileage) . "km就需要" . $service['name'] . ",请及时到店进行检测</div>";
                        }
                    }
                endforeach;
            }else{
                echo "<p>您的车在本店暂时还没有任何保养记录，请到店进行检测</p>";
            }
            echo "<a href='index.php?r=member/add_order&car_id=".$car_id."'><input type='button' value='马上下单' class='btn-info' /></a>";
            exit;
        }
    }

    //消费积分卷详细内容页
    public function actionCons_point(){
        //查找名下所有有效并且不为0的积分卷
        $cons_points = Member_cons_point::find()->where(['member_id' => Yii::$app->session['member_id']])->andWhere(['>','ev_time',time()])->andWhere(['>','surplus',0])->asArray()->all();
        return $this->render('cons_point',[
            'cons_point' => $cons_points,
        ]);
    }

    //转让积分
    public function actionTransfer(){
        if(Yii::$app->request->post()){
            $phone = $_POST['phone'];
            $id = $_POST['id'];
            $point = Member_cons_point::find()->where(['id' => $id])->andWhere(['member_id' => Yii::$app->session['member_id']])->one();
            $to_member = Members::find()->where(['phone' => $phone])->asArray()->one();
            if(empty($to_member['id'])){
                echo 333;
                exit;
            }else{
                $point->member_id = $to_member['id'];
                //插入转让记录
                $new_log = new Point_transfer();
                $new_log->by_member = Yii::$app->session['member_id'];
                $new_log->to_member = $to_member['id'];
                $new_log->point_id = $id;
                if($point->save() && $new_log->save()){
                    echo 111;
                }else{
                    echo 222;
                }
            }
            exit;
        }
    }

    //取消订单
    public function actionDel_order(){
        if(Yii::$app->request->post()) {
            $id = $_POST['id'];
            $order = Orders::find()->where(['id' => $id])->one();
            //如果是套餐卷生成的订单，返还套餐
            $p = true;
            if($order['package_id'] != 0){
                $member_package = Package_member::find()->where(['package_id' => $order['package_id']])->andWhere(['member_id' => Yii::$app->session['member_id']])->one();
                $member_package->nums += 1;
                if(!$member_package->save()){
                    $p = false;
                }
            }
            if(Orders::deleteAll(['id' => $order['id']]) && Order_goods::deleteAll(['order_id' =>$order['id'] ]) && $p){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

		
	// member register.
    public function actionRegister() {
        $model = new MemberRegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = new Members();
            $user->user_name = $model['user_name'];
            $user->password = md5($model['password']);
            $user->phone = $model['phone'];
			$user->rec_numbers = $this->getRandChar(6);
            $user->create_time = time();
			$user->open_id = Yii::$app->session['wechat_openid'];
			$user->weixin_id = Yii::$app->session['wechat_appid'];
			
            if($user->save()){
				Yii::$app->session['member_id'] = $user->id;
                Yii::$app->getSession()->setFlash('success', '注册成功！');
            }else{
                Yii::$app->getSession()->setFlash('error', '注册失败！');
			}

            return $this->redirect(array('/member/'));
        } else {
            return $this->render('register', [
				'model' => $model
            ]);
        }
    }

    //复制订单
    public function actionCopy_order(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $mg = $_POST['mg'];
            //根据id查订单以及订单包含的产品
            $old_order = Orders::find()->where(['id' => $id])->asArray()->one();
            $old_order_goods = Order_goods::find()->where(['order_id' => $old_order['id']])->asArray()->all();
            $order = new Orders();
            //查是否有还未开工的工单，有就用这些工单的工单号，无就新建一个
            $has_order = Orders::find()->where(['status' => 11])->andWhere(['car_id' => $old_order['car_id']])->asArray()->one();
            if(!empty($has_order)){
                $order_no = $has_order['order_no'];
            }else {
                $order_no = $order_no = $this->_get_order_no();
            }
            $order->order_no = $order_no;
            $order->service_id = $old_order['service_id'];
            $order->service_name = $old_order['service_name'];
            $order->create_time = time();
            //$order->store_id = $old_order['store_id'];
            $order->car_id = $old_order['car_id'];
            $order->appointment_id = Yii::$app->session['member_id'];
            $order->appointment = 1;
            $order->mileage = $mg;
            $order->status = 10;
            if($order->save() && !empty($old_order_goods)){
                foreach($old_order_goods as $goods):
                    $the_goods = Goods::find()->where(['goods_id' => $goods['goods_id']])->asArray()->one();
                    $order_goods = new Order_goods();
                    $order_goods->order_no = $order_no;
                    $order_goods->goods_id = $goods['goods_id'];
                    $order_goods->goods_name = $goods['goods_name'];
                    $order_goods->price = $the_goods['price'];
                    $order_goods->nums = $goods['nums'];
                    $order_goods->has_taked = 0;
                    $order_goods->order_id = $order['id'];
                    $order_goods->save();
                endforeach;
            }
            echo $order['id'];
            exit;
        }
    }

    //查等待时间
    public function actionCheck_time(){
        $order_no = $_POST['order_no'];
        $order = Orders::find()->where(['order_no' => $order_no])->orderBy('create_time')->asArray()->one();
        //查店铺下面排在本工单前，还未开工的所有工单
        $pev_orders = Orders::find()->where('status < 20')->andWhere(['store_id' => $order['store_id']])->andWhere("create_time <".$order['create_time'])->asArray()->all();
        //循环所有工单，计算所有服务的标准时间
        $need_time = 0;
        $need_car = 0;
        foreach($pev_orders as $val):
            $service = Service::find()->where(['id' => $val['service_id']])->asArray()->one();
            $need_time += $service['use_time']+5;
            $need_car++;
        endforeach;
        //计算正在开工的订单还剩下多少时间
        $begin_order = Orders::find()->where(['status' => 20])->andWhere(['store_id' => $order['store_id']])->asArray()->one();
        if(!empty($begin_order)) {
            $begin_orders = Orders::find()->where(['order_no' => $begin_order['order_no']])->asArray()->all();
            $has_time = 0;
            foreach($begin_orders as $val):
                $service = Service::find()->where(['id' => $val['service_id']])->asArray()->one();
                $has_time += $service['use_time']+5;
            endforeach;
            $has_time_2 = time() - $begin_order['begin_time'];  //已经开始了多少时间
            $has_time_2 = $has_time_2/60;
            $has_time = $has_time - $has_time_2;
        }
        if($has_time < 0){
            $has_time = 0;
            $need_car++;
        }
//        echo $begin_has_use_time;exit;
        $need_time += $has_time+5;
        echo "您前面还有".$need_car."辆车在排队，";
        echo "您的排队时间大约是".ceil($need_time)."分钟";
        exit;
    }

	function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        $member = Members::find()->where(['rec_numbers' => $str])->count();
        if($member != 0){
            $this->getRandChar($length);
        }else{
            return $str;
        }

    }

	function getFirstCharter($str) {
		if(empty($str)){return '';}
		$fchar=ord($str{0});
		if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
		$s1=iconv('UTF-8','gb2312',$str);
		$s2=iconv('gb2312','UTF-8',$s1);
		$s=$s2==$str?$s1:$str;
		$asc=ord($s{0})*256+ord($s{1})-65536;
		if($asc>=-20319&&$asc<=-20284) return 'A';
		if($asc>=-20283&&$asc<=-19776) return 'B';
		if($asc>=-19775&&$asc<=-19219) return 'C';
		if($asc>=-19218&&$asc<=-18711) return 'D';
		if($asc>=-18710&&$asc<=-18527) return 'E';
		if($asc>=-18526&&$asc<=-18240) return 'F';
		if($asc>=-18239&&$asc<=-17923) return 'G';
		if($asc>=-17922&&$asc<=-17418) return 'H';
		if($asc>=-17417&&$asc<=-16475) return 'J';
		if($asc>=-16474&&$asc<=-16213) return 'K';
		if($asc>=-16212&&$asc<=-15641) return 'L';
		if($asc>=-15640&&$asc<=-15166) return 'M';
		if($asc>=-15165&&$asc<=-14923) return 'N';
		if($asc>=-14922&&$asc<=-14915) return 'O';
		if($asc>=-14914&&$asc<=-14631) return 'P';
		if($asc>=-14630&&$asc<=-14150) return 'Q';
		if($asc>=-14149&&$asc<=-14091) return 'R';
		if($asc>=-14090&&$asc<=-13319) return 'S';
		if($asc>=-13318&&$asc<=-12839) return 'T';
		if($asc>=-12838&&$asc<=-12557) return 'W';
		if($asc>=-12556&&$asc<=-11848) return 'X';
		if($asc>=-11847&&$asc<=-11056) return 'Y';
		if($asc>=-11055&&$asc<=-10247) return 'Z';
		return '?';
	}	

}
