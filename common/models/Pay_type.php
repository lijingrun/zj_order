<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/5/9
 * Time: 14:57
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Pay_type extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%pay_type}}';
    }
}