<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/11
 * Time: 13:47
 */

namespace backend\controllers;
use common\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class SaleController extends Controller{

    public $enableCsrfValidation = false;


    public function beforeAction($action)
    {
        if(!empty(Yii::$app->session['user_id'])){
            return $action;
        }else{
            return $this->redirect("index.php?r=site/login");
        }
    }

    public function actionIndex(){
    }

    public function actionReset_password(){
        if(Yii::$app->request->post()){
            $user_id = Yii::$app->session['user_id'];
            $new_password = $_POST['new_password'];
            $user = User::find()->where("id =".$user_id)->one();
            $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($new_password);
            if($user->save()){
                Yii::$app->getSession()->setFlash('success','操作成功！');
                unset(Yii::$app->session['user_id']);
                return $this->redirect("index.php");
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试！');
                return;
            }
        }else{
            return $this->render("reset_password");
        }


    }
}