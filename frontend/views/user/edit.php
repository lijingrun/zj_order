<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/10
 * Time: 9:51
 */
?>

<div>
    <form method="post">
        <p>
            用户名称：<input type="text" value="<?php echo $user['username']?>" name="username" />
        </p>
        <p>
            登录密码：<input type="password" name="password" /><span style="color:red">不填写则不修改密码</span>
        </p>
        <p>
            <input type="submit" value="确定" class="btn-success" />
            <input type="button" value="取消" class="btn-danger" />
        </p>
    </form>
</div>
