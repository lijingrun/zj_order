<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/5/30
 * Time: 8:48
 *
 * 系统数据统计控制器
 */
namespace frontend\controllers;

use common\models\Deposit_log;
use common\models\Orders;
use common\models\Package_log;
use Yii;
use yii\web\Controller;

class StatisticsController extends Controller{

    public function beforeAction($action)
    {
        if(Yii::$app->session['user_role']['store'] != 'on'){
            Yii::$app->getSession()->setFlash('error','你没有权限访问！');
            return $this->goHome();
        }else{
            return $action;
        }
    }

    //主页只显示本店的营业额，充值金额以及套餐金额
    public function actionIndex(){
        $store_id = Yii::$app->session['store_id'];
        $year = $_GET['year'];
        if(empty($year) || $year < 2016) {
            $year = date("Y", time());
        }
        //12个月
        $months = array();
        //循环12个月，计算工单+充值+套餐的价钱
        for($key = 1; $key <= 12; $key++):
            //获取对应月份的第一天和下个月的第一天（计算周期为当月第一天0点到下个月第一天0点）
            $first_day = $year."-".$key."-01";
            if($key != 12){
                $end_day = $year."-".($key+1)."-01";
            }else{
                $end_day = $year."-12-31";
            }
            $start_time = strtotime($first_day);
            $end_time = strtotime($end_day);
            //工单
            $months[$key]['order_price'] = Orders::find()->where("status >= 40")->andWhere(['store_id' => $store_id])->andWhere("finish_time >=".$start_time)->andWhere("finish_time <=".$end_time)->sum('realy_price');
            //套餐
            $months[$key]['package_price'] = Package_log::find()->where(['store_id' => $store_id])->andWhere("create_time >=".$start_time)->andWhere("create_time <=".$end_time)->sum('price');
            //充值
            $months[$key]['balance_price'] = Deposit_log::find()->where(['store_id' => $store_id])->andWhere("create_time >=".$start_time)->andWhere("create_time <=".$end_time)->sum('cash_amount');
            $total_price[$key] =  $months[$key]['order_price'] + $months[$key]['package_price'] + $months[$key]['balance_price'];
        endfor;
        //占比查询
        $first_day = $year."-01-01";
        $last_day = $year."-12-31";
        $first_time = strtotime($first_day);
        $last_time = strtotime($last_day);
        //年订单总额
        $year_order_price = Orders::find()->where("status >= 40")->andWhere(['store_id' => $store_id])->andWhere("finish_time >=".$first_time)->andWhere("finish_time <=".$last_time)->sum('realy_price');
        if(empty($year_order_price)){
            $year_order_price = 0;
        }
        //年套餐总额
        $year_package_price = Package_log::find()->where(['store_id' => $store_id])->andWhere("create_time >=".$first_time)->andWhere("create_time <=".$last_time)->sum('price');
        if(empty($year_package_price)){
            $year_package_price = 0;
        }
        //年充值总额
        $year_balance_price = Deposit_log::find()->where(['store_id' => $store_id])->andWhere("create_time >=".$first_time)->andWhere("create_time <=".$last_time)->sum('cash_amount');
        if(empty($year_balance_price)){
            $year_balance_price = 0;
        }
        //年销售总额
        $year_total_price = $year_order_price + $year_package_price + $year_balance_price;
        if($year_total_price == 0){
            Yii::$app->getSession()->setFlash('error',$year.'年度没有销售情况可统计！');
            return $this->redirect('index.php');
        }
        return $this->render('index',[
            'year' => $year,
            'months' => $months,
            'total_price' => $total_price,
            'year_total_price' => $year_total_price,
            'year_order_price' => $year_order_price,
            'year_package_price' => $year_package_price,
            'year_balance_price' => $year_balance_price,
        ]);
    }

    //一个月的第一天
    function getCurMonthFirstDay($date) {
        return date('Y-m-01', strtotime($date));
    }

    //一个月的最后一天
    function getCurMonthLastDay($date) {
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +1 month -1 day'));
    }


}