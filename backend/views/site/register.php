<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/2/10
 * Time: 22:18
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div>
    <?php $form = ActiveForm::begin([
        'options' => ['style' => 'width:250px;'],
    ]); ?>
    <?= $form->field($model,'user_name')->label('账号名称'); ?>
    <?= $form->field($model,'password')->passwordInput()->label('账号密码'); ?>
    <?= $form->field($model,'phone')->label('电话号码'); ?>
    <?= $form->field($model,'rec_numbers')->label('推荐码'); ?>
    <div class="form-group">
        <?= Html::submitButton('注册', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


