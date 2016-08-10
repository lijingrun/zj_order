<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 21:07
 */
namespace frontend\controllers;

use common\models\Role;
use common\models\User;
use common\models\UserForm;
use Yii;
use yii\web\Controller;
use common\models\Store;
use common\models\User_type;

class UserController extends Controller{

    public function beforeAction($action)
    {
        if(Yii::$app->session['user_role']['user'] != 'on'){
            Yii::$app->getSession()->setFlash('error','你没有权限访问！');
            return $this->goHome();
        }else{
            return $action;
        }
    }

    public function actionIndex(){
        $users = User::find()->asArray()->all();
        foreach($users as $key=>$user):
            $users[$key]['store'] = Store::find()->where(['store_id' => $user['store_id']])->asArray()->one();

        endforeach;
        return $this->render('user_list',[
            'users' => $users,
        ]);
    }
    public $enableCsrfValidation = false;
    //权限设置
    public function actionRole(){

        $user_id = $_GET['id'];
        $role = Role::find()->where(['user_id' => $user_id])->asArray()->one();
        if(Yii::$app->request->post()){
            if(empty($role['id'])){
                $role = new Role();
                $role->user_id = $user_id;
                $role->goods = $_POST['goods'];
                $role->car = $_POST['car'];
                $role->store = $_POST['store'];
                $role->worker = $_POST['worker'];
                $role->orders = $_POST['orders'];
                $role->member = $_POST['member'];
                $role->service = $_POST['service'];
                $role->user = $_POST['user'];
                $role->statistics = $_POST['statistics'];
            }else{
                $role = Role::find()->where(['user_id' => $user_id])->one();
                $role->goods = $_POST['goods'];
                $role->car = $_POST['car'];
                $role->store = $_POST['store'];
                $role->worker = $_POST['worker'];
                $role->orders = $_POST['orders'];
                $role->member = $_POST['member'];
                $role->service = $_POST['service'];
                $role->user = $_POST['user'];
                $role->statistics = $_POST['statistics'];
            }
            if($role->save()){
                Yii::$app->getSession()->setFlash('success','权限修改成功，重新登录后生效！');
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试！');
            }
            return $this->redirect('index.php?r=user/role&id='.$user_id);
        }else{
            return $this->render('role',[
                'role' => $role,
            ]);
        }
    }

    public function actionChange_password(){
        $user_id = Yii::$app->session['user_id'];
        if(Yii::$app->request->post()){
            $user = User::find()->where(['id' => $user_id])->asArray()->one();
            $is_true = Yii::$app->getSecurity()->validatePassword($_POST['old_password'],$user['password_hash']);
            if($is_true){
                $user = User::find()->where(['id' => $user_id])->one();
                $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($_POST['new_password']);
                if($user->save()){
                    Yii::$app->getSession()->setFlash('success','修改成功！');
                    return $this->redirect('index.php?r=user');
                }
            }else{
                Yii::$app->getSession()->setFlash('error','原密码错误');
                return $this->redirect('index.php?r=user/change_password');
            }
        }else{
            return $this->render('change_password');
        }
    }

    //添加账号
    public function actionAdd(){
        $model = new UserForm();
        $all_store = Store::find()->asArray()->all();
        $stores = array();
        foreach($all_store as $store):
            $stores[$store['store_id']] = $store['store_name'];
        endforeach;
        $all_type = User_type::find()->asArray()->all();
        $user_types = array();
        foreach($all_type as $type):
            $user_types[$type['type_id']] = $type['type_name'];
        endforeach;
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $user = new User();
            $user->username = $model['username'];
            $user->store_id = $model['store_id'];
            $user->type_id = $model['user_type'];
            $user->setPassword($model['password']);
            $user->generateAuthKey();
            $user->created_at = time();
            if($user->save()){
                Yii::$app->getSession()->setFlash('success','添加成功！');
            }else{
                Yii::$app->getSession()->setFlash('error','添加失败！');
            }
            return $this->redirect("index.php?r=user");
        }else{
            return $this->render('register',[
                'stores' => $stores,
                'model' => $model,
                'user_types' => $user_types,
            ]);
        }
    }
}