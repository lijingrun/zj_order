<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/11
 * Time: 18:54
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;


class User_type extends ActiveRecord{
    public static function tableName()
    {
        return '{{%user_type}}';
    }
}