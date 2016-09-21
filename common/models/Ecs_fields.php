<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/9/21
 * Time: 8:59
 *
 * ecshop里面对应的其他注册信息
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Ecs_fields extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_reg_extend_info}}';
    }
}