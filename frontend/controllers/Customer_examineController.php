<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/23
 * Time: 9:14
 */
namespace frontend\controllers;


use common\models\Customer;
use common\models\Customer_type;
use common\models\Ecs_fields;
use common\models\Ecs_user;
use common\models\Region;
use common\models\User;
use common\models\User_rule;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class Customer_examineController extends Controller{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $user_id = Yii::$app->session['user_id'];
        if(empty($user_id)){
            return $this->redirect("index.php?r=site/login");
        }else{
            $user_rule = User_rule::find()->where("user_id =".$user_id)->asArray()->one();
            if($user_rule['customer_examine'] != 1){
                Yii::$app->getSession()->setFlash('error','你没有权限访问！');
                return $this->redirect("index.php");
            }else{
                return $action;
            }
        }
    }

    public function actionIndex(){
//        $type = $_GET['type']; //审核类型 1为业务员新增客户 2为自建客户
//        if(empty($type)){
//            $type = 1;
//        }
//        if($type == 1){
            $customer = Customer::find()->where("status = 1")->asArray()->all();
            foreach($customer as $key=>$val):
                $customer[$key]['user_id'] = User::find()->where("id =".$val['user_id'])->asArray()->one();
                $customer[$key]['type_id'] = Customer_type::find()->where("rank_id =".$val['type_id'])->asArray()->one();
            endforeach;
//        }
        //查找状态 是未认证的客户
        $users = Ecs_user::find()->where("is_validated = 0")->asArray()->all();
        $user_exa = array();
        foreach($users as $key=>$val):
            $one_user = array();
            $one_user['user_name'] = $val['user_name'];
            $one_user['telephone'] = $val['mobile_phone'];
            $one_user['user_id'] = $val['user_id'];
            $other = Ecs_fields::find()->where("user_id = ".$val['user_id'])->asArray()->all();
            foreach($other as $ot):
                switch($ot['reg_field_id']){
                    case 102 : $one_user['address'] = $ot['content']; //地址
                        break;
                    case 101 : $one_user['contacts'] = $ot['content']; //联系人
                        break;
                    case 100 : $one_user['license_id'] = $ot['content'];  //营业执照号
                        break;
                }
            endforeach;
            $user_exa[] = $one_user;
        endforeach;
        $c_count = count($user_exa);
        $s_count = count($customer);
        return $this->render("customer_list",[
            'customer' => $customer,  //业务员登记客户
            'user_exa' => $user_exa, //客户自己登记的
            'c_count' => $c_count,
            's_count' => $s_count,
        ]);
    }

    public function actionPass(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $type = $_POST['type'];
            $customer = Customer::find()->where("id =".$id)->one();
            $customer->status = $type;
//            $customer = Customer::find()->where("id =".$id)->asArray()->one();
            //如果是通过，并且为有关联商城账号id，就新建一个商城账号
            if($type == 2 && empty($customer->customer_id)){
                $user_name = $customer->customer_name;
//                echo $user_name;exit;
                $password = rand(100000,999999);
                if(!empty($user_name)) {
                    //先查用户名是否已经注册商城账号，如果有，就重新写一个
                    $ecs_user = Ecs_user::find()->where("user_name ='" . $user_name."'")->count();
                    if ($ecs_user > 0) {
                        echo 333;
                        exit;
                    } else {
                        $new_ecs_user = new Ecs_user();
                        $new_ecs_user->user_name = $user_name;
                        $new_ecs_user->password = md5($password);
                        $new_ecs_user->email = $customer->email;
                        $new_ecs_user->user_rank = $customer->type_id;
                        $new_ecs_user->office_phone = $customer->phone;
                        $new_ecs_user->mobile_phone = $customer->telephone;
                        $new_ecs_user->is_validated = 1;
                        if ($new_ecs_user->save()) {
                            $customer->customer_id = $new_ecs_user['user_id'];
                            $customer->default_ps = $password;
                        } else {
                            echo 222;exit;
                        }
                    }
                }
            }
            if($customer->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

    public function actionDetail(){
        $id = $_GET['id'];
        $id = $_GET['id'];
        $customer = Customer::find()->where("id =".$id)->asArray()->one();
        $rank = Customer_type::find()->where("rank_id =".$customer['type_id'])->asArray()->one();
        return $this->render("detail",[
            'customer' => $customer,
            'rank' => $rank,
        ]);
    }

    //完善自建客户资料
    public function actionPerfect(){
        $user_id = $_GET['user_id'];
        if(Yii::$app->request->post()){
            if(empty($_POST['customer_name'])){
                Yii::$app->getSession()->setFlash('error','信息不能为空');
                return;
            }
            $province = Region::find()->where("region_id =".$_POST['province'])->asArray()->one();
            $city = Region::find()->where("region_id =".$_POST['city'])->asArray()->one();
            $check_customer = Customer::find()->where("del = 0")->andWhere("customer_name = '".$_POST['customer_name']."'")->one();
            if(!empty($check_customer)){
                Yii::$app->getSession()->setFlash('error','客户已经存在！');
                return $this->redirect("index.php?r=customer/add");
            }
            $customer = new Customer();
            $customer->customer_name = $_POST['customer_name']; //客户名
            $customer->license_id = $_POST['license_id']; //客户名
            $customer->customer_code = $_POST['customer_code']; //客户编码
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
            $customer->user_id = $_POST['sale_id'];  //业务员id
            $customer->customer_id = $user_id;  //业务员id
            //财务信息
            $customer->ban_name = $_POST['ban_name'];
            $customer->status = 2;  //直接通过审核
            $customer->ban = $_POST['ban'];
            $customer->ban_no = $_POST['ban_no'];
            $customer->invoice = $_POST['invoice'];
            $customer->taxes = $_POST['taxes'];
            $ecs_user = Ecs_user::find()->where("user_id =".$user_id)->one();
            $ecs_user->is_validated = 1;
            $ecs_user->user_rank = $_POST['type_id'];
            if($customer->save() && $ecs_user->save()){
                return $this->redirect('index.php?r=customer_examine');
            }else{
                Yii::$app->getSession()->setFlash("error",'服务器繁忙，请稍后重试！');
                return $this->redirect("index.php?r=customer_examine");
            }
        }else{
            $user = Ecs_user::find()->where("user_id = ".$user_id)->asArray()->one();
            if($user['is_validated' != 0]){
                Yii::$app->getSession()->setFlash('error','该客户不是待审客户！');
                return $this->redirect("index.php?r=customer_examine");
            }
            $other = Ecs_fields::find()->where("user_id = ".$user_id)->asArray()->all();
            $user_info = array();
            $user_info['user_id'] = $user_id;
            $user_info['user_name'] = $user['user_name'];
            $user_info['phone'] = $user['mobile_phone'];
            foreach($other as $ot):
                switch($ot['reg_field_id']){
                    case 102 : $user_info['address'] = $ot['content']; //地址
                        break;
                    case 101 : $user_info['contacts'] = $ot['content']; //联系人
                        break;
                    case 100 : $user_info['license_id'] = $ot['content'];  //营业执照号
                        break;
                }
            endforeach;
            $sales = User::find()->where("status = 10")->asArray()->all();
            $provinces = Region::find()->where("parent_id = 1")->asArray()->all();
            $customer_types = Customer_type::find()->asArray()->all();
            return $this->render('perfect',[
                'user_info' => $user_info,
                'sales' => $sales,
                'provinces' => $provinces,
                'customer_types' => $customer_types,
            ]);
        }
    }

    function actionDel_customer(){
        $user_id = $_POST['user_id'];
        if(Ecs_user::deleteAll("user_id =".$user_id)){
            echo 111;
        }else{
            echo 222;
        }
        exit;
    }

}