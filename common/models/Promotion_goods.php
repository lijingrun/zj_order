<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/17
 * Time: 10:37
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Promotion_goods extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%promotion_goods}}';
    }
}