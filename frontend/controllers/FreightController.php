<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 11:07
 */
namespace frontend\controllers;

use common\models\Customer;
use common\models\Customer_cart;
use common\models\Customer_order;
use common\models\Customer_order_goods;
use common\models\Customer_type;
use common\models\Ecs_user;
use common\models\Freight;
use common\models\Goods;
use common\models\Member_price;
use common\models\Region;
use common\models\User_rule;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class FreightController extends Controller{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $user_id = Yii::$app->session['user_id'];
        if(empty($user_id)){
            return $this->redirect("index.php?r=site/login");
        }else{
            $user_rule = User_rule::find()->where("user_id =".$user_id)->asArray()->one();
            if($user_rule['freight'] != 1){
                Yii::$app->getSession()->setFlash('error','你没有权限访问！');
                return $this->redirect("index.php");
            }else{
                return $action;
            }
        }
    }

    public function actionIndex(){
        $freights = Freight::find()->asArray()->all();
        foreach($freights as $key=>$val){
            $freights[$key]['region'] = Region::find()->where("region_id =".$val['region_id'])->asArray()->one();
        }

        return $this->render('freight',[
            'freights' => $freights,
        ]);
    }

    public function actionSet_up(){
        if(Yii::$app->request->post()){
            $city_ids = $_POST['city_ids'];
            $price = $_POST['price'];
            foreach($city_ids as $id):
                $new_freight = new Freight();
                $new_freight->region_id = $id;
                $new_freight->price = $price;
                $new_freight->save();
            endforeach;
            Yii::$app->getSession()->setFlash('success','保存成功！');
            return $this->redirect("index.php?r=freight");
        }else {
            $province = Region::find()->where("parent_id = 1")->asArray()->all();

            return $this->render('set_up', [
                'provinces' => $province,
            ]);
        }
    }

    public function actionDel(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            if(Freight::deleteAll("id =".$id)){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }


}