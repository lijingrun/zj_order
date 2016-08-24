<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/24
 * Time: 10:49
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Invoice extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%invoice}}';
    }
}