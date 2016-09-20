<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/9/20
 * Time: 8:57
 */
?>

<div>
    <h4 class="h4">用户权限操作--(<?php echo $user['username']?>)</h4>
    <form method="post">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="is_seal" value="1" <?php if($user_rule['is_seal'] == 1){echo "checked='checked'";}?>>
                        业务员
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="invoice_examine" <?php if($user_rule['invoice_examine'] == 1){echo "checked='checked'";}?> value="1">
                        发票审核
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_examine" <?php if($user_rule['customer_examine'] == 1){echo "checked='checked'";}?>  value="1"> 客户审核
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="user"  value="1" <?php if($user_rule['user'] == 1){echo "checked='checked'";}?>> 操作账号
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer"  value="1" <?php if($user_rule['customer'] == 1){echo "checked='checked'";}?>> 操作客户
                    </label>
                </div>
            </div>
        </div>
        <div >
            <input type="submit" value="确定" class="btn-success" />
        </div>
    </form>
</div>
