<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/24
 * Time: 8:56
 */
use yii\widgets\LinkPager;
?>

<div>
    <div align="right" style="padding:10px;">
        <a href="index.php?r=invoice/add">
        <button class="btn-info">新建申请</button>
        </a>
    </div>
    <div>
        <?php foreach($invoices as $val): ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <p class="panel-title">
                        <?php echo $val['company_name']?>
                        <span style="float: right;">
                            <?php
                                switch($val['status']){
                                    case 0 : echo "未审核";
                                        break;
                                    case 1 : echo "已审核";
                                        break;
                                    case 2 : echo "审核不通过";
                                        break;
                                }
                            ?>
                        </span>
                    </p>
                </div>
                <div class="panel-body">
                    <p>开票单位：<?php echo $val['company_name']?></p>
                    <p>物料名：<?php echo $val['goods_name']?></p>
                    <p>总金额：<?php echo $val['goods_amount']?></p>
                    <p>应收税金：<?php echo $val['invoice_money']?>（<?php echo $val['invoice_den']?>%）</p>
                    <p>开户账号：<?php echo $val['ban_no'];?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div>
        <?= LinkPager::widget(['pagination' => $pages]); ?>
    </div>
</div>