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
use common\models\Ecs_user;
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
        $type = $_GET['type']; //审核类型 1为业务员新增客户 2为自建客户
        if(empty($type)){
            $type = 1;
        }
        if($type == 1){
            $customer = Customer::find()->where("status = 1")->asArray()->all();
            foreach($customer as $key=>$val):
                $customer[$key]['user_id'] = User::find()->where("id =".$val['user_id'])->asArray()->one();
                $customer[$key]['type_id'] = Customer_type::find()->where("rank_id =".$val['type_id'])->asArray()->one();
            endforeach;
        }

        return $this->render("customer_list",[
            'customer' => $customer,
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

}