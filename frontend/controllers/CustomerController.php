<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 14:39
 */
namespace frontend\controllers;

use common\models\City;
use common\models\Customer;
use common\models\Customer_type;
use common\models\Ecs_user;
use common\models\Province;
use common\models\Region;
use common\models\User_address;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class CustomerController extends Controller{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        if(empty(Yii::$app->session['user_id'])){
            return $this->redirect('index.php?r=site/login');
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
            $user_name = $_POST['user_name'];
            $password = $_POST['password'];
            if(!empty($user_name)) {
                //先查用户名是否已经注册商城账号，如果有，就重新写一个
                $ecs_user = Ecs_user::find()->where("user_name ='" . $user_name."'")->count();
                if ($ecs_user > 0) {
                    Yii::$app->getSession()->setFlash('error', '商城账号已经存在！');
                    return $this->redirect("index.php?r=customer/add");
                } else {
                    $new_ecs_user = new Ecs_user();
                    $new_ecs_user->user_name = $user_name;
                    $new_ecs_user->password = md5($password);
                    $new_ecs_user->email = $_POST['email'];
                    $new_ecs_user->user_rank = $_POST['type_id'];
                    $new_ecs_user->office_phone = $_POST['phone'];
                    $new_ecs_user->mobile_phone = $_POST['tel_phone'];
                    if ($new_ecs_user->save()) {
                        $customer->customer_id = $new_ecs_user['user_id'];
                    } else {
                        Yii::$app->getSession()->setFlash('error', "服务器繁忙，请稍后重试！");
                        return $this->redirect("index.php?r=customer/add");
                    }
                }
            }

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
            //商城账号
            if(!empty($_POST['user_name'])) {
                $customer->user_name = $_POST['user_name'];
                $customer->password = $_POST['password'];
            }
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

}