<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 8:25
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Order_info extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_order_info}}';
    }
}