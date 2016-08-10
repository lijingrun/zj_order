<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/5/27
 * Time: 10:12
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Additional_goods extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%additional_goods}}';
    }
}