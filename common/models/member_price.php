<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 15:59
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Member_price extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_member_price}}';
    }
}