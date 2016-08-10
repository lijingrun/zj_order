<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 20:46
 */
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Store;
use common\models\Store_addForm;


class StoreController extends Controller{

    public function beforeAction($action)
    {
        if(Yii::$app->session['user_role']['store'] != 'on'){
            Yii::$app->getSession()->setFlash('error','你没有权限访问！');
            return $this->goHome();
        }else{
            return $action;
        }
    }

    public function actionIndex(){
        $stores = Store::find()->asArray()->all();
        return $this->render('store_list',[
            'stores' => $stores,
        ]);
    }

    public function actionAdd(){
        $model = new Store_addForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $store = new Store();
            $store->store_name = $model['store_name'];
            $store->create_time = time();
            if($store->save()){
                Yii::$app->getSession()->setFlash('success','添加成功！');
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试');
            }
            return $this->redirect('index.php?r=store');
        }else{

            return $this->render('store_add',[
               'model' => $model,
            ]);
        }
    }
}
