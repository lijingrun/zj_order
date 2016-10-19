<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/17
 * Time: 10:35
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Promotion extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%promotion}}';
    }
}