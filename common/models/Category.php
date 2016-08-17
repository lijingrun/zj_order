<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/17
 * Time: 14:04
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ecs_category}}';
    }
}