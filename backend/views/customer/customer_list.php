<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 15:05
 */
use yii\widgets\LinkPager;
?>
<script>
    function del_customer(id){
        if(confirm("是否确定删除该客户？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/del_customer',
                data : {'id' : id},
                success : function(data){
                    return_data = JSON.parse(data);
                    if(return_data.error_code == 0){
                        alert(return_data.error_data);
                        location.reload();
                    }else{
                        alert(return_data.error_data);
                    }
                }
            });
        }
    }
    function find(){
        var name = $("#name").val().trim();
        if(name != ''){
            location.href="index.php?r=customer&key_word="+name;
        }
    }
</script>

<div align="right">
    <a href="index.php?r=customer/add">
        <button class="btn-primary">添加新客户</button>
    </a>
</div>
<div class="input-group" style="padding-top: 10px;">
    <input type="text" id="name" class="form-control" value="<?php echo $key_word;?>" placeholder="搜索客户名称" aria-describedby="basic-addon2">
    <span class="input-group-addon" id="basic-addon2" onclick="find();">搜索</span>
</div>
<div style="padding-top: 10px;">
    <?php foreach($customers as $customer): ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <span class="panel-title"><?php echo $customer['customer_name'];?></span>
            <span style="float: right;font-size: 13px;">
                <?php
                    switch($customer['status']){
                        case 1 : echo "待审客户";
                            break;
                        case 2 : echo "正常客户";
                            break;
                        case 3 : echo "审核不通过";
                            break;
                    }
                ?>
            </span>
        </div>
        <div class="panel-body">
            <p><?php echo $customer['province'].$customer['city']?></p>
            <p>客户等级：<?php echo $customer['type_id']['rank_name'];?></p>
            <div style="font-size: 18px;" align="right">
                <?php if($customer['status'] == 2){ ?>
                <a href="index.php?r=customer/add_cart&customer_id=<?php echo $customer['id']?>">
                    <input type="button" class="btn-success" value="代下单" />
                </a>
                <?php } ?>
                <a href="index.php?r=customer/detail&id=<?php echo $customer['id'];?>">
                    <input type="button" class="btn-info" value="查看详细" />
                </a>
            </div>
        </div>

    </div>
    <?php endforeach; ?>
    <div align="center">
        <?= LinkPager::widget(['pagination' => $pages]); ?>
    </div>
</div>
