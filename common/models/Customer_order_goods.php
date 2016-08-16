<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/16
 * Time: 10:59
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Customer_order_goods extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%customer_order_goods}}';
    }
}