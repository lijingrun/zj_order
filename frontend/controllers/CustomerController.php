<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 14:39
 */
namespace frontend\controllers;

use common\models\Customer;
use common\models\Customer_type;
use common\models\Province;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class CustomerController extends Controller{

    public $enableCsrfValidation = false;

    public function actionIndex(){
        $all_customers = Customer::find();
        $page = new Pagination(['totalCount' => $all_customers->count(),'pageSize' => '20']);
        $customers = $all_customers->offset($page->offset)->limit($page->limit)->all();

        return $this->render("customer_list",[
            'customers' => $customers,
            'pages' => $page,
        ]);
    }

    //添加客户资料
    public function actionAdd(){
        if(Yii::$app->request->post()){
            if(empty($_POST['customer_name'])){
                Yii::$app->getSession()->setFlash('error','信息不能为空');
                return;
            }
            $customer = new Customer();
            $customer->customer_name = $_POST['customer_name']; //客户名
            $customer->customer_code = $_POST['customer_code']; //客户编码
            $customer->province = $_POST['province']; //所在省份
            $customer->city = $_POST['city']; //所在城市
            $customer->address = $_POST['address']; //详细地址
            $customer->zip_code = $_POST['zip_code']; //邮编
            $customer->phone = $_POST['phone']; //电话
            $customer->fax = $_POST['fax']; //传真
            $customer->type_id = $_POST['type_id']; //客户类型
            $customer->start_time = $_POST['start_time']; //签约开始时间
            $customer->end_time = $_POST['end_time'];  //签约结束时间
            $customer->name = $_POST['name']; //姓名
            $customer->position = $_POST['position']; //职位
            $customer->telephone = $_POST['telephone'];
            $customer->email = $_POST['email'];
            $customer->qq = $_POST['qq'];
            $customer->log_code = $_POST['log_code']; //物流编码
            $customer->spare = $_POST['spare'];
            //商城账号
            $customer->user_name = $_POST['user_name'];
            $customer->password = $_POST['password'];
            //财务信息
            $customer->ban_name = $_POST['ban_name'];
            $customer->ban = $_POST['ban'];
            $customer->ban_no = $_POST['ban_no'];
            $customer->invoice = $_POST['invoice'];
            $customer->taxes = $_POST['taxes'];
            if($customer->save()){
                return $this->redirect('index.php?r=customer');
            }else{
                Yii::$app->getSession()->setFlash("error",'服务器繁忙，请稍后重试！');
                return $this->redirect("index.php?r=customer/add");
            }
        }else {
            $provinces = Province::find()->asArray()->all();
            $customer_types = Customer_type::find()->asArray()->all();
            return $this->render("add", [
                'provinces' => $provinces,
                'customer_types' => $customer_types,
            ]);
        }
    }

    public function actionType(){
        $types = Customer_type::find()->all();

        return $this->render("type_list",[
            'types' => $types,
        ]);
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