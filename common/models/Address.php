<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 8:54
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Address extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_user_address}}';
    }
}