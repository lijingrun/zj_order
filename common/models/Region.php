<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/15
 * Time: 10:27
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Region extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_region}}';
    }
}