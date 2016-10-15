<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 13:50
 */
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Freight extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%freight}}';
    }
}