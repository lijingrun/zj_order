<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 20:54
 */
namespace common\models;

use Yii;
use yii\base\Model;

class Store_addForm extends Model{
    public $store_name;


    public function rules()
    {
        return [
            [['store_name'],'required','message' => '请输入店铺名称'],
            [['store_name'], 'unique', 'targetClass' => 'common\models\Store', 'message' => '这个车牌号码已经被登记了！']
        ];
    }
}