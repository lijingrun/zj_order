<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 10:40
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class User_address extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_user_address}}';
    }
}