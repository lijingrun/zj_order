<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 14:52
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Customer_cart extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%customer_cart}}';
    }
}