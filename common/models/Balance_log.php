<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/5/14
 * Time: 22:57
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Balance_log extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%balance_log}}';
    }
}