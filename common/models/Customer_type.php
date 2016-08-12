<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 16:10
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Customer_type extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_user_rank}}';
    }
}