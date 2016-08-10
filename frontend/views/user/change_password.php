<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/5/8
 * Time: 18:03
 */
?>
<script>
    function check_data(){
        var p1 = $("#password1").val().trim();
        var p2 = $("#password2").val().trim();
        var o_p = $("#old_password").val().trim();
        if(p1 == '' || p2 == '' || o_p == '' ){
            alert("请输入需要的内容");
        }else if( p1 != p2){
            alert("2次密码输入不正确！");
        }else{
            $("#form").submit();
        }
    }
</script>
<div>
    <form method="post" id="form">
        <p>
            旧密码：<input type="password" name="old_password" id="old_password" />
        </p>
        <p>
            新密码：<input type="password" name="new_password" id="password1" />
        </p>
        <p>
            确认密码：<input type="password" id="password2" />
        </p>
        <p>
            <input type="button" value="确定" class="btn-success" onclick="check_data();" />
        </p>
    </form>
</div>
