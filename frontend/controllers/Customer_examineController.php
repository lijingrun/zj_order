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
use common\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class Customer_examineController extends Controller{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $user_id = Yii::$app->session['user_id'];
        $user = User::find()->where("id =".$user_id)->asArray()->one();
        if($user['type_id'] == 2){
            return $action;
        }else{
            Yii::$app->getSession()->setFlash('error','你没有权限操作！');
            return $this->redirect("index.php?r=user");
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
            if($customer->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

}