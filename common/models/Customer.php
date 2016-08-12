<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/10
 * Time: 16:33
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_customer}}';
    }
}