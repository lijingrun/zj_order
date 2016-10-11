<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 22:11
 */
namespace common\models;

use Yii;
use yii\base\Model;

class UserForm extends Model{
    public $username;
    public $password;
//    public $user_type;
    public function rules()
    {
        return [
            [['username','password'],'required','message' => '内容不能为空'],
            [['username'], 'unique', 'targetClass' => 'common\models\User', 'message' => '用户已经存在！']
        ];
    }
}