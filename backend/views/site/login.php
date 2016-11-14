<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->

<!--    <p>Please fill out the following fields to login:</p>-->

    <div class="row">
        <div class="col-lg-5">
            <h4 style="color:red;">业务员下单登录</h4>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username')->label("业务员名称") ?>

                <?= $form->field($model, 'password')->passwordInput()->label("登录密码") ?>

                <div class="form-group">
                    <?= Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
