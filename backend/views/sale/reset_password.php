<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/11
 * Time: 13:53
 */
?>
<script>
    function check_data(){
//        var ps = $("#password").val().trim();
        var new_ps = $("#new_password").val().trim();
        var con_ps = $("#confirm_password").val().trim();
        if(new_ps == '' || con_ps == ''){
            alert("请输入相关内容！");
        }else{
            if(new_ps != con_ps){
                alert("2次输入密码不一致，请重新输入！");
            }else{
                $("#form").submit();
            }
        }
    }
</script>

<div>
    <form id="form" method="post">
<!--        <p>-->
<!--            原来密码：<input type="password" name="password" id="password" />-->
<!--        </p>-->
        <p>
            修改密码：<input type="password" name="new_password" id="new_password"/>
        </p>
        <p>
            确认密码：<input type="password" id="confirm_password" />
        </p>
        <p>
            <input type="button" value="确定" class="btn-success" onclick="check_data();"/>
        </p>
    </form>
</div>
