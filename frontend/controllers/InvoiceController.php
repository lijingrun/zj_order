<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/24
 * Time: 11:18
 */
namespace frontend\controllers;

use common\models\Category;
use common\models\Customer_type;
use common\models\Goods;
use common\models\Invoice;
use common\models\Member_price;
use common\models\User;
use common\models\User_rule;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class InvoiceController extends Controller{

    public function beforeAction($action)
    {
        $user_id = Yii::$app->session['user_id'];
        if(empty($user_id)){
            return $this->redirect("index.php?r=site/login");
        }else{
            $user_rule = User_rule::find()->where("user_id =".$user_id)->asArray()->one();
            if($user_rule['invoice_examine'] != 1){
                Yii::$app->getSession()->setFlash('error','你没有权限访问！');
                return $this->redirect("index.php");
            }else{
                return $action;
            }
        }
    }

    public function actionIndex(){
        $all_invoices = Invoice::find()->orderBy("add_time desc");
        $type = $_GET['type'];
        if($type != 0){
            switch($type){
                case 1 : $all_invoices->andWhere("status = 0");
                    break;
                case 2 : $all_invoices->andWhere("status = 1");
                    break;
                case 3 : $all_invoices->andWhere("status = 2");
                    break;
            }
        }

        $pages = new Pagination(['totalCount' => $all_invoices->count(), 'pageSize' => '20']);
        $invoices = $all_invoices->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index',[
            'invoices' => $invoices,
            'pages' => $pages,
            'type' => $type,
        ]);
    }

    public function actionPass(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $type = $_POST['type'];
            $invoice = Invoice::find()->where("id =".$id)->one();
            $invoice->status = $type;
            $invoice->examine_id = Yii::$app->session['user_id'];
            $invoice->examine_time = time();
            if($invoice->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

    public function actionDetail(){
        $id = $_GET['id'];
        $invoice = Invoice::find()->where("id =".$id)->asArray()->one();
        $invoice['seal_id'] = User::find()->where("id =".$invoice['seal_id'])->asArray()->one();
        if(!empty($invoice['examine_id'])) {
            $invoice['examine_id'] = User::find()->where("id =" . $invoice['examine_id'])->asArray()->one();
        }
        return $this->render("detail",[
            'invoice' => $invoice,
        ]);
    }

    public function actionChange_invoice_sn(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            $new_sn = $_POST['new_sn'];
            $invoice = Invoice::find()->where("id =".$id)->one();
            $invoice['invoice_sn'] = $new_sn;
            if($invoice->save()){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

}