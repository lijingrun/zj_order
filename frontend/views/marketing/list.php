<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 16:50
 */
use yii\widgets\LinkPager;
?>
<script>
    function del(id){
        if(confirm("是否确定删除该政策？")){
            $.ajax({
                type : 'post',
                url : 'index.php?r=marketing/del',
                data : {'id' : id},
                success : function(data){
                    if(data == 111){
                        alert("操作成功！");
                        location.reload();
                    }
                }
            });
        }
    }
</script>
<div>
    <a href="index.php?r=marketing/add">
        <input type="button" value="新建活动政策" />
    </a>
    <div>
        <table class="table table-striped">
            <tr>
               <th>活动名称</th>
               <th>活动时间</th>
                <th>活动产品</th>
               <th>针对等级</th>
               <th>操作</th>
            </tr>
            <?php foreach($promotion as $val): ?>
            <tr>
                <td><?php echo $val['promotion']['title'];?></td>
                <td><?php echo "开始时间：".date("Y-m-d H:i:s",$val['promotion']['start_time'])."<br />结束时间：".date("Y-m-d H:i:s",$val['promotion']['end_time']);?></td>
                <td>
                    <?php foreach($val['goods'] as $good):
                        echo $good['goods_name']."<br />";
                    endforeach;
                    ?>
                </td>
                <td><?php
                    foreach($val['rank'] as $v):
                        echo $v['rank_name']."<br />";
                    endforeach;
                    ?>
                </td>
                <td>
                    <a href="index.php?r=marketing/edit&id=<?php echo $val['promotion']['id'];?>">
                        <button class="btn-success">修改</button>
                    </a>
                    <button class="btn-danger" onclick="del(<?php echo $val['promotion']['id'];?>);">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="12">
                    <?= LinkPager::widget(['pagination' => $pages]); ?>
                </td>
            </tr>
        </table>
    </div>
</div>
