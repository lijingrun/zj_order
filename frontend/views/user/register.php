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
    <?= $form->field($model,'username')->label('账号名称'); ?>
    <?= $form->field($model,'password')->label('账号密码'); ?>
    <?= $form->field($model,'user_type')->dropDownList($user_types,['prompt'=>'请选择账号类型'])->label('账号类型') ?>
    <?= $form->field($model,'store_id')->dropDownList($stores,['prompt'=>'请选择所属店铺'])->label('所属店铺') ?>
    <div class="form-group">
        <?= Html::submitButton('添加', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


