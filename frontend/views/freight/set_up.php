<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 11:41
 */

?>
<script>
    function get_city(){
        var id = $("#province").val();
        $("#city").html("");
        $("#district").html("");
        if(id != 0){
            $.ajax({
                type : 'post',
                url : "index.php?r=customer/get_city",
                data : {'id' : id},
                success : function(data){
                    $("#city").html(data);
                }
            });
        }
    }
    function get_district(){
        var id = $("#city").val();
        $("#district").html("");
        if(id != 0){
            $.ajax({
                type : 'post',
                url : "index.php?r=customer/get_city",
                data : {'id' : id},
                success : function(data){
                    $("#district").html(data);
                }
            });
        }
    }
    function push_city(){
        var city_id = $("#city").val();
        var city = $("option[value = "+city_id+"]").html();
        var html = "<input type='hidden' name='city_ids[]' value='"+city_id+"' id='city_id"+city_id+"' />";
        var html2 = "<p id='city"+city_id+"'><span>"+city+"</span>-----<a href='#' onclick='del_city("+city_id+");'>X</a></p>";
        $("#choose_list").append(html);
        $("#choose_list").append(html2);
    }
    function del_city(id){
        $("#city_id"+id).remove();
        $("#city"+id).remove();
    }
</script>
<div>
    <form method="post">
    <p>
        运费：
        <input type="text" id="price" name="price" />元
    </p>
    <p>
        省份：
        <select id="province" onchange="get_city();">
            <option value="0" >选择省份</option>
            <?php foreach($provinces as $province): ?>
            <option value="<?php echo $province['region_id']?>"><?php echo $province['region_name'];?></option>
            <?php endforeach; ?>
        </select>
        <select id="city" onchange="get_district();">
            <option value="0">选择城市</option>
        </select>
        <input type="button" value="添加" onclick="push_city();" />
<!--        <select id="district">-->
<!--            <option value="0">选择地区</option>-->
<!--        </select>-->
    </p>
    <div id="choose_list">

    </div>
    <p>
        <input type="submit" value="提交" />
    </p>
    </form>
</div>
