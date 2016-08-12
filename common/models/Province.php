<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/10
 * Time: 14:14
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Province extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_province}}';
    }
}