<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 16:52
 */
?>
<script>
    function find_goods(){
        var goods_name = $("#goods_name").val().trim();
        if(goods_name == ''){
            alert("请输入商品名称！");
        }else{
            $("#goods_list").html("");
            $.ajax({
                type : 'post',
                url : 'index.php?r=marketing/find_goods',
                data : {'goods_name' : goods_name},
                success : function(data){
//                    alert(data);
                    $("#goods_list").html(data);
                }
            });
        }
    }
    function choose_goods(){
        var goods_id = $("#goods_list").val();
        var goods_name = $("#goods_detail"+goods_id).text();
        var goods_htm = "<li id='has_choose"+goods_id+"'>"+goods_name+"-----<a onclick='del_goods("+goods_id+");'>X</a><input type='hidden' value='"+goods_id+"' name='goods_id[];' /></li>";
        $("#choose_goods").append(goods_htm);
    }
    function del_goods(id){
        $("#has_choose"+id).remove();
    }
    function check_data(){
        var s_time = $("#start_time").val();
        var e_time = $("#end_time").val();
        var coefficient = $("#coefficient").val();
        var number = $("#number").val();
        var title = $("#title").val();
        if(s_time == '' || e_time == '' || coefficient == '' || number == '' || title == ''){
            alert("请输入相关内容！");
        }else{
            $("#form").submit();
        }
    }

</script>
<div>
    <form method="post" id="form">
        <div>
            <h4>活动名称</h4>
            <input type="text" name="title" id="title" value="<?php echo $promotion['title'];?>" />
        </div>
        <div>
            <h4>活动开始时间：</h4>
            <input type="datetime-local" name="start_time" id="start_time" value="<?php echo date("Y-m-d H:i:s",$promotion['start_time']);?>" />
        </div>
        <div>
            <h4>活动结束时间：</h4>
            <input type="datetime-local" name="end_time" id="end_time" value="<?php echo date("Y-m-d H:i:s",$promotion['end_time']);?>" />
        </div>
        <div>
            <h4>促销形式：</h4>
            <div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" id="optionsRadios1" value="1"  <?php echo $promotion['type'] == 1 ? 'checked' : '';?>>
                        满送（客户每买满数量赠送一定同类型商品）
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" id="optionsRadios2" value="2" <?php echo $promotion['type'] == 2 ? 'checked' : '';?>>
                        满减（客户满买数量直接减除金额）
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" id="optionsRadios3" value="3" <?php echo $promotion['type'] == 3 ? 'checked' : '';?>>
                        满折（客户买满数量直接打折）
                    </label>
                </div>
            </div>
        </div>
        <div>
            <h4>促销参数：</h4>
            <p>数量：<input type="text" name="number" id="number" value="<?php echo $promotion['number']?>" /></p>
            <p>系数：<input type="text" name="coefficient" id="coefficient" value="<?php echo $promotion['coefficient']?>" />（按照选择方案填写系数）</p>
            <p>满送的系数是赠送数量，满减的系数为减的金额（直减），满折的系数为折扣（小数）</p>
        </div>
        <div>
            <h4>客户级别:</h4>
            <?php foreach($rank as $val): ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="rank[]" value="<?php echo $val['rank_id'];?>"  <?php if(in_array($val['rank_id'],$choose_rank)){echo "checked";}?> >
                        <?php echo $val['rank_name']; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div>
            <h4>活动商品</h4>
            <div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="goods_name">
                      <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <span class="glyphicon glyphicon-search" onclick="find_goods();"></span>
                        </button>
                      </span>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div style="width:50%; padding:15px;">
            <div>
                <select class="form-control" id="goods_list">

                </select>
            </div>
            <div>
                <input type="button" value="添加商品" class="btn-info" onclick="choose_goods();" />
            </div>
            <div >
                <ul id="choose_goods">
                    <?php foreach($goods as $good): ?>
                        <li id='has_choose<?php echo $good['goods_id'];?>'>
                            <?php echo $good['goods_name'];?>-----
                            <a onclick='del_goods(<?php echo $good['goods_id'];?>);'>
                            X
                            </a>
                            <input type='hidden' value='<?php echo $good['goods_id'];?>' name='goods_id[];' />
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div>
            <input type="button" value="确定提交" onclick="check_data();" class="btn-success"  />
        </div>
    </form>
</div>
