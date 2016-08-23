<?php
namespace backend\controllers;

use common\models\Carema;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\Orders;
use common\models\Cars;
use common\models\User;
//use common\models\Role;

/**
 * Site controller
 */
class SiteController extends \frontend\controllers\SiteController
{
//    /**
//     * @inheritdoc
//     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error', 'register'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout', 'index'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function actions()
//    {
//        return [
//            'error' => [
//                'class' => 'yii\web\ErrorAction',
//            ],
//        ];
//    }
//
//    public function actionIndex()
//    {
//        //先查该登录的工人是否有选择摄像头，没有就提示选择
////        $carema = Carema::find()->where(['store_id' => Yii::$app->session['store_id']])->andWhere(['worker_id' => Yii::$app->session['user_id']])->asArray()->one();
////        if(empty($carema)){
////            return $this->redirect('index.php?r=worker/choose_carema');
////        }
//        //查所有待开工的工单
//        $orders = Orders::find()->where(['status' => 11])->orWhere(['status' => 21])->andWhere(['store_id' => Yii::$app->session['store_id']])->asArray()->orderBy('order_by, status desc,create_time')->all();
//        foreach($orders as $key=>$order):
//            $orders[$key]['car'] = Cars::find()->where(['id' => $order['car_id']])->asArray()->one();
//        endforeach;
//        return $this->render('index',[
//            'orders' => $orders,
//        ]);
//    }
//
//    public function actionLogin()
//    {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            $user = User::find()->where(['username' => $model['username']])->asArray()->one();
////            $user_role = Role::find()->where(['user_id' => $user['id']])->asArray()->one();
//            Yii::$app->session['user_id'] = $user['id'];
//            Yii::$app->session['store_id'] = $user['store_id'];
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    public function actionLogout()
//    {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }

}
