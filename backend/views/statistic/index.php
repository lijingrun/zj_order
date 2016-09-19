<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/27
 * Time: 9:28
 */
?>
<script>
    function find(){
        var customer = $("#customer_id").val();
        var month = $("#month").val();
        location.href="index.php?r=statistic&customer="+customer+"&month="+month;
    }
</script>
<div>
    <div>
        <select id="customer_id" class="form-control">
            <option value="0">全部客户</option>
            <?php foreach($customers as $customer): ?>
            <option value="<?php echo $customer['id']?>"><?php echo $customer['customer_name']?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="padding-top:20px;">
        <input type="month" class="form-control" id="month" />
    </div>
    <div style="padding-top:20px;">
        <a class="btn btn-primary" href="#" onclick="find();" role="button">查找</a>
    </div>
</div>
