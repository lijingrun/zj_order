<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 11:37
 */
?>
<script>
    function del(id){
        if(confirm("是否确定删除改城市的运费设置？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=freight/del',
                data : {'id' : id},
                success : function(data){
                    if(data == 111){
                        location.reload();
                    }else{
                        alert("扑街删唔到啊");
                    }
                }
            });
        }
    }
</script>
<div>
    <div >
        <a href="index.php?r=freight/set_up">
            <input type="button" value="设置运费" />
        </a>
    </div>
    <div style="padding-top:10px;">
        <ul>
        <?php foreach($freights as $val){ ?>
        <li><?php echo $val['region']['region_name']."(￥".$val['price'].")"?>----<span onclick="del(<?php echo $val['id'];?>);">X</span></li>

        <?php } ?>
        </ul>
    </div>
</div>
