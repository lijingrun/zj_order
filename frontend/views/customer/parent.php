<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/9/21
 * Time: 11:54
 */
?>
<script>
    function get_customer(){
        var name = $("#name").val().trim();
        if(name != ''){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/get_customer',
                data : {'name' : name},
                success : function(data){
                    $("#customer_list").html('');
                    $("#customer_list").append(data);
                }
            });
        }
    }
</script>
<div>
    <h4>设置上级经销商<strong><?php echo $customer['customer_name'];?></strong></h4>
    <div style="width: 250px;" >
        <div class="input-group">
            <input type="text" class="form-control" id="name">
          <span class="input-group-btn">
            <button class="btn btn-default" type="button" onclick="get_customer();">搜索客户</button>
          </span>
        </div>
    </div>
    <form method="post">
        <div style="padding-top: 10px;">
            <select name="up_id" id="customer_list" class="form-control" style="width: 180px;">
            </select>
        </div>
        <input type="hidden" value="<?php echo $customer['id']?>" name="customer_id" />
        <br/>
        <input type="submit" class="btn-primary" value="确定" />
    </form>
</div>
