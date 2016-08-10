<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/7
 * Time: 22:35
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Evaluate extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%evaluate}}';
    }
}