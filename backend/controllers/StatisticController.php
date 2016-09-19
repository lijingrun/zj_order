<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/27
 * Time: 9:27
 */
namespace backend\controllers;

use common\models\Customer;
use common\models\Invoice;
use common\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class StatisticController extends Controller{

    public function beforeAction($action)
    {
        if(empty(Yii::$app->session['user_id'])){
            return $this->redirect("index.php?r=site/login");
        }else{
            return $action;
        }
    }


    public function actionIndex(){
        $user_id = Yii::$app->session['user_id'];
        $customers = Customer::find()->where("user_id =".$user_id)->asArray()->all();
        $customer = $_GET['customer'];
        $month = $_GET['month'];
        if(empty($month)){
            $month = date("Y-m",time());
        }
        //计算第一天和最后一天
        $first_day = strtotime($month."-01");
        $last_day = strtotime(date('Y-m-d', strtotime(date($month."-01") . ' +1 month -1 day')));
        //根据时间和选择的客户进行订单搜索
        return $this->render("index",[
            'customers' => $customers,
        ]);
    }
}
