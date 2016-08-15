<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 8:31
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Ecs_user extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_users}}';
    }
}