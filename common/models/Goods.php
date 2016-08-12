<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 14:57
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Goods extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_goods}}';
    }
}