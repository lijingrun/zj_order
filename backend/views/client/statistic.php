<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/12
 * Time: 11:21
 */
?>
<script>
    function get_data(){
        var year = $("#year").val();
        var mount = $("#mount").val();
        location.href="index.php?r=client/statistic&year="+year+"&mount="+mount;
    }
</script>
<div>
    <div>
        <select id="year" class="form-control" onchange="get_data();">
            <option value="2016" <?php if($year==2016){echo "selected";}?>>2016年</option>
            <option value="2017" <?php if($year==2017){echo "selected";}?>>2017年</option>
            <option value="2018" <?php if($year==2018){echo "selected";}?>>2018年</option>
            <option value="2019" <?php if($year==2019){echo "selected";}?>>2019年</option>
            <option value="2020" <?php if($year==2020){echo "selected";}?>>2020年</option>
            <option value="2021" <?php if($year==2021){echo "selected";}?>>2021年</option>
            <option value="2022" <?php if($year==2022){echo "selected";}?>>2022年</option>
            <option value="2023" <?php if($year==2023){echo "selected";}?>>2023年</option>
            <option value="2024" <?php if($year==2024){echo "selected";}?>>2024年</option>
            <option value="2025" <?php if($year==2025){echo "selected";}?>>2025年</option>
            <option value="2026" <?php if($year==2026){echo "selected";}?>>2026年</option>
        </select>
    </div>
    <div style="padding-top:10px;">
        <select  id="mount" class="form-control" onchange="get_data();">
            <option value="0" <?php if($mount==0){echo "selected";}?>>全年</option>
            <option value="1" <?php if($mount==1){echo "selected";}?>>1月</option>
            <option value="2" <?php if($mount==2){echo "selected";}?>>2月</option>
            <option value="3" <?php if($mount==3){echo "selected";}?>>3月</option>
            <option value="4" <?php if($mount==4){echo "selected";}?>>4月</option>
            <option value="5" <?php if($mount==5){echo "selected";}?>>5月</option>
            <option value="6" <?php if($mount==6){echo "selected";}?>>6月</option>
            <option value="7" <?php if($mount==7){echo "selected";}?>>7月</option>
            <option value="8" <?php if($mount==8){echo "selected";}?>>8月</option>
            <option value="9" <?php if($mount==9){echo "selected";}?>>9月</option>
            <option value="10" <?php if($mount==10){echo "selected";}?>>10月</option>
            <option value="11" <?php if($mount==11){echo "selected";}?>>11月</option>
            <option value="12" <?php if($mount==12){echo "selected";}?>>12月</option>
        </select>
    </div>
    <div style="padding-top: 10px;">
        <?php if($total_price == 0){ ?>
            <p class="bg-warning">您这时间段内没有任何交易产生！</p>
        <?php }else{ ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php
                        if($mount == 0){
                            echo $year."年全年订货清单";
                        }else {
                            echo $year . "年" . $mount . "月订货清单";
                        }
                        ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <h4 style="color:red;">
                        订货总金额：￥<?php echo $total_price?>
                    </h4>
                    <div class="bs-callout bs-callout-warning">
                        <h4>购买商品清单</h4>
                        <?php foreach($order_goods as $goods): ?>
                        <p><?php echo $goods['goods_name']."-----X".$goods['total'] ?></p>
                        <?php endforeach; ?>
                    </div>
                    <div class="bs-callout bs-callout-warning">
                        <h4>赠送商品清单</h4>
                        <?php foreach($gift_goods as $g_goods): ?>
                            <p><?php echo $g_goods['goods_name']."-----X".$g_goods['total'] ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
