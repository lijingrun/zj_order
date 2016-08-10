<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/9
 * Time: 20:11
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Point_log extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%point_log}}';
    }
}