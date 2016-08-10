<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/14
 * Time: 10:31
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Point_transfer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%point_transfer}}';
    }
}