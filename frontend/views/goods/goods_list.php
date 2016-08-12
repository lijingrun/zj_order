<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 15:01
 */
use yii\widgets\LinkPager;
?>
<script>
    function find_goods(){
        var key_word = $("#key_word").val().trim();
        if(key_word != ''){
            location.href="index.php?r=goods&key_word="+key_word;
        }
    }
</script>
<div >
    <div align="center">
        商品名称/编号
        <input type="text" id="key_word" placeholder="输入商品名称或者编号查找">
        <input type="button" value="查找" onclick="find_goods();" class="btn-primary" />
        <a href="index.php?r=goods" >
            <input type="button" value="重置" class="btn-primary">
        </a>
    </div>
    <h4 class="h4">商品列表</h4>
    <table class="table table-hover" >
        <tr>
            <th>商品名称</th>
            <th>商品编号</th>
            <th>商品价格</th>
            <th>商品库存</th>
        </tr>
        <?php foreach($goods as $good): ?>
        <tr>
            <td><?php echo $good['goods_name']?></td>
            <td><?php echo $good['goods_sn']?></td>
            <td>
                零售价：￥<?php echo $good['market_price']."<br />";?>
                本店价：￥<?php echo $good['shop_price']."<br />";?>
                <?php if(!empty($good['price'])){ ?>
                <?php foreach($good['price'] as $val): ?>
                <?php echo $val['rank_name']."：￥".$val['price']."<br />";?>
                <?php endforeach; }?>
            </td>
            <td><?php echo $good['goods_number']?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" align="right">
                <?= LinkPager::widget(['pagination' => $pages]); ?>
            </td>
        </tr>
    </table>
</div>
