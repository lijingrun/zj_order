<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/9
 * Time: 15:33
 */
?>
<style>
    .input-div{
        font-size: 20px;
    }

    .span1{
        font-weight : 900;
    }
</style>
<script>
    function save_data(){
        var name = $("#name").val().trim();
        var discount = $("#discount").val().trim();
        if(name == '' || discount == ''){
            alert("请填写相关内容！");
        }else{
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/type_add',
                data : {'name' : name, 'discount' : discount},
                success : function(data){
                    var return_data = JSON.parse(data);
                    if(return_data.error_code == 0){
                        location.reload();
                    }else{
                        alert(return_data.error_data);
                    }
                }
            });
        }
    }
    function to_change_name(id){
        var input_html = "<input type='text' id='new_name' onblur='change_name("+id+");' />";
        $("#name"+id).html(input_html);
    }
    function change_name(id){
        var new_name = $("#new_name").val().trim();
        if(new_name == ''){
            alert("请输入内容！");
        }else{
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/change_type_name',
                data : {'id' : id, 'name' : new_name},
                success : function(data){
                    var return_data = JSON.parse(data);
                    if(return_data.error_code == 0){
                        location.reload();
                    }else{
                        alert(return_data.error_data);
                    }
                }
            });
        }
    }
    function to_change_discount(id){
        var input_html = "<input type='text' id='new_discount' onblur='change_discount("+id+");' />";
        $("#discount"+id).html(input_html);
    }
    function change_discount(id){
        var new_discount = $("#new_discount").val().trim();
        if(new_discount == ''){
            alert("请输入内容！");
        }else{
            $.ajax({
                type : 'post',
                url : 'index.php?r=customer/change_type_discount',
                data : {'id' : id, 'discount' : new_discount},
                success : function(data){
                    var return_data = JSON.parse(data);
                    if(return_data.error_code == 0){
                        location.reload();
                    }else{
                        alert(return_data.error_data);
                    }
                }
            });
        }
    }
    function del(id){
        if(confirm("是否确定删除客户类型？")){
            $.ajax({
                type : "post",
                url : "index.php?r=customer/del",
                data : {"id" : id},
                success : function(data){
                    var return_data = JSON.parse(data);
                    if(return_data.error_code == 0){
                        location.reload();
                    }else{
                        alert(return_data.error_data);
                    }
                }
            });
        }
    }
</script>
<div align="right">
<!--    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">-->
<!--        新增-->
<!--    </button>-->
</div>


<!-- 添加类型div -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">设置新客户等级</h4>
            </div>
            <div class="modal-body">
                <div class="input-div">
                   <div class="row" style="padding-bottom: 10px;">
                       <div class="col-md-3" align="right">客户名称:</div>
                       <div class="col-md-6">
                           <input type="text" id="name" />
                       </div>
                   </div>
                    <div class="row">
                        <div class="col-md-3" align="right">折扣:</div>
                        <div class="col-md-6">
                            <input type="text" style="width:80px;" id="discount" />%
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="save_data();">保存</button>
            </div>
        </div>
    </div>
</div>
<!-- 增加类型 end -->
<div style="padding-bottom: 30px;">

</div>
<table class="table" style="width:50%;" align="center">
    <tr>
        <th>级别名称</th>
        <th>订货折扣</th>
<!--        <th>操作</th>-->
    </tr>
    <?php foreach($types as $type): ?>
    <tr>
        <td >
            <?php echo $type['rank_name'];?>
        </td>
        <td>
            折扣：<?php echo $type['discount'];?>%
        </td>
    </tr>
    <?php endforeach; ?>
</table>
