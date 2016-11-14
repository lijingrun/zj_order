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
    function find_by_cat(){
        var obj=document.getElementsByName('category');  //选择所有name="'test'"的对象，返回数组
        //取到对象数组后，我们来循环检测它是不是被选中
        var s='';
        for(var i=0; i<obj.length; i++){
            if(obj[i].checked) s+=obj[i].value+',';  //如果选中，将value添加到变量s中
        }
        if(s == ''){
            alert("您还未选择任何分类！");
        }else{
            location.href="index.php?r=goods&category="+s;
        }
    }
</script>
<body style="background-color: #f5f5f5;">

    <div class="panel panel-info">
        <a data-toggle="modal" data-target="#myModal">
            <div class="panel-body">
                按类型筛选
            </div>
        </a>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">商品类型</h4>
                </div>
                <div class="modal-body">
                    <?php foreach($category as $cat){ ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="category" <?php if(!empty($category_arr)){if(in_array($cat['cat_id'],$category_arr)){echo "checked='checked'";} }?> value="<?php echo $cat['cat_id']?>"> <?php echo $cat['cat_name'];?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="find_by_cat();">确定</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>

<?php if($goods){ ?>
<?php foreach($goods as $good): ?>
    <a href="index.php?r=goods/detail&id=<?php echo $good['goods_id']?>">
        <div class="row" style="padding-top: 10px;padding-bottom:10px;background-color: white;margin:10px;">
            <div class="col-xs-5 col-sm-4">
                <img src="<?php echo Yii::$app->params['url'].$good['goods_img']?>" style="width:100%;" />
            </div>
            <div class="col-xs-7 col-sm-4">
                <p style="padding-top:20px;">
                    <?php echo $good['goods_name']; ?>
                </p>
                <p style="color:#00a2d4">
                    ￥<?php echo $good['shop_price']?>
                </p>

            </div>
            <?php if(!empty($good['seller_note'])){ ?>
            <div style="padding-left: 10px;color:red">
                <?php foreach($good['seller_note'] as $promotion): ?>
                    <p><?php echo $promotion['title'];?></p>
                <?php endforeach; ?>
            </div>
            <?php } ?>
        </div>
    </a>
<?php endforeach; ?>
    <div align="center">
        <?= LinkPager::widget(['pagination' => $pages]); ?>
    </div>
<?php }else{ ?>
    <div style="padding:10px;padding-top: 20px;">
        <a href="index.php?r=goods">
            <div class="alert alert-danger" role="alert">
                <strong>查不到相关的商品信息!</strong> 点我试试
            </div>
        </a>
    </div>
<?php } ?>

</body>
