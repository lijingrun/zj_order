<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/8
 * Time: 13:04
 */
namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class Upload_imageForm extends Model{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}