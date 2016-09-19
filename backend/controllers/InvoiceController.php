<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/23
 * Time: 17:02
 */

namespace backend\controllers;

use common\models\Customer;
use common\models\Invoice;
use common\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class InvoiceController extends Controller{

    public $enableCsrfValidation = false;

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
        $all_invoices = Invoice::find()->where("seal_id =".$user_id)->orderBy('add_time desc');
        $pages = new Pagination(['totalCount' => $all_invoices->count(), 'pageSize' => '20']);
        $invoices = $all_invoices->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render("index",[
            'invoices' => $invoices,
            'pages' => $pages,
        ]);
    }


    //添加申请
    public function actionAdd(){
        $user_id = Yii::$app->session['user_id'];
        $customers = Customer::find()->where("user_id =".$user_id)->asArray()->all();
        $user = User::find()->where("id =".$user_id)->asArray()->one();
        if(Yii::$app->request->post()){
            $invoice = new Invoice();
            $invoice->invoice_type = $_POST['invoice_type'];
            $invoice->invoice_sn = $_POST['invoice_sn'];
            $invoice->invoice_den = $_POST['invoice_den'];
            $invoice->good_price_type = $_POST['good_price_type'];
            $invoice->money_type = $_POST['money_type'];
            $invoice->company_name = $_POST['company_name'];
            $invoice->goods_name = $_POST['goods_name'];
            $invoice->price = $_POST['price'];
            $invoice->goods_numbers = $_POST['goods_numbers'];
            $invoice->invoice_money = $_POST['invoice_money'];
            $invoice->goods_amount = $_POST['goods_amount'];
            $invoice->invoice_no = $_POST['invoice_no'];
            $invoice->phone = $_POST['phone'];
            $invoice->address = $_POST['address'];
            $invoice->ban_no = $_POST['ban_no'];
            $invoice->seal_id = $user_id;
            $invoice->add_time = time();
            if($invoice->save()){
                Yii::$app->getSession()->setFlash('success','新建成功！');
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试！');
            }
            return $this->redirect("index.php?r=invoice");
        }else{
            return $this->render("add",[
                'customers' => $customers,
                'user' => $user,
            ]);
        }
    }

    //获取客户信息
    public function actionGet_customer(){
        if(Yii::$app->request->post()){
            $customer_id = $_POST['customer_id'];
            $customer = Customer::find()->where("id =".$customer_id)->asArray()->one();
            $return_data = json_encode($customer);
            return $return_data;
        }
    }

}