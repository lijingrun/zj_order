<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 20:48
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Store extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%store}}';
    }
}