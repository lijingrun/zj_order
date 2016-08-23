<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/3/28
 * Time: 9:13
 */
namespace backend\controllers;


use common\models\Members;
use common\models\Weixin;
use Yii;
use yii\web\Controller;

class Member_loginController extends Controller{

    public $enableCsrfValidation = false;
    public $layout = 'mobile';
    //会员登录
    public function actionLogin(){
        if(Yii::$app->request->post()){
            $phone = $_POST['phone'];
            $password = md5($_POST['password']);
            $member = Members::find()->where(['phone' => $phone])->andWhere(['password' => $password])->asArray()->one();
            if(!empty($member)){
                Yii::$app->session['member_id'] = $member['id'];
                $weixin_code = Yii::$app->request->get('code');
                $weixin_appid = Yii::$app->request->get('appid');
                if (!empty($weixin_code)) {
                    Yii::$app->session['wechat_code'] = $weixin_code;
                    Yii::$app->session['wechat_appid'] = Yii::$app->request->get('appid');
                    // testing
                    $appid = $_GET['id'];
                    $weixin = Weixin::find()->where(['appid' => $appid])->one();
                    $secret = $weixin->app_secret;
                    Yii::$app->session['wechat_appid'] = $appid;
                    $code = Yii::$app->request->get('code');
                    echo $code;exit;
                    $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='
                        . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $get_token_url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    $res = curl_exec($ch);
                    curl_close($ch);
                    $json_obj = json_decode($res, true);
                    $access_token = $json_obj['access_token'];
                    $openid = $json_obj['openid'];

                    $weixin['access_token'] = $access_token;
                    $weixin->save();

                    Yii::$app->session['wechat_openid'] = $openid;
                    //登录之后保存信息
                    if (!empty($openid)) {
                        $member_record = Members::find()->where(['id' => $member['id']])->one();
                        $member_record->open_id = $openid;
                        $member_record->weixin_id = $appid;
                        $member_record->save();
                        return $this->redirect('index.php?r=member/core');
                    } else {
                        return $this->redirect('index.php?r=member/core');
                    }
                } else {
                    return $this->redirect('index.php?r=member/core');
                }
            }else{
                return $this->render('member_login',[
                    'error_message' => '账号/密码错误',
                    'phone' => $_POST['phone'],
                ]);
            }
        }else {
            return $this->render('member_login');
        }
    }

    //会员登录
    public function actionLogin_in(){
        $phone = $_POST['phone'];
        $password = md5($_POST['password']);
        $member = Members::find()->where(['phone' => $phone])->andWhere(['password' => $password])->asArray()->one();
        if(!empty($member)){
            Yii::$app->session['member_id'] = $member['id'];
            echo 111;
        }else{
            echo 222;
        }
        exit;
    }

    //会员注册
    public function actionRegister(){

        if(Yii::$app->request->post()){
            $phone = $_POST['phone'];
            $member = Members::find()->where(['phone' => $phone])->asArray()->one();
            if(!empty($member['phone'])){
                echo "该号码已经被注册！<a href='index.php?r=member_login/register'>重新填写</a>";exit;
            }
            $con_phone = $_POST['con_phone'];
            $con_member = Members::find()->where(['phone' => $con_phone])->asArray()->one();
            $new_member = new Members();
            $new_member->cons_point = 0;
            $new_member->user_name = $_POST['user_name'];
            $new_member->phone = $_POST['phone'];
            $new_member->password = md5($_POST['password']);
            $new_member->create_time = time();
            $new_member->from_member = $con_member['id'];
            if($new_member->save()){
                Yii::$app->session['member_id'] = $new_member['id'];
                if(!empty($con_member)){

                }
                return $this->redirect('index.php?r=member/car_detail');
            }else{
                echo "服务器繁忙，请稍后重试！";
                exit;
            }
        }else{
            return $this->render('register');
        }
    }

    //注销账号
    public function actionLogout()
    {
        Yii::$app->session['member_id'] = null;
        return $this->redirect('index.php?r=member_login/login');
    }

}