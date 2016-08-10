<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/5/30
 * Time: 11:39
 */
?>
<script type="text/javascript" src="Chart.js-master/dist/Chart.js"></script>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script>
    function find_statistics(){
        var start_time = $("#start_time").val();
        var end_time = $("#end_time").val();
        location.href="index.php?r=statistics&start_time="+start_time+"&end_time="+end_time;
    }
</script>
<style>
    a:hover{
        text-decoration:none;
        text-decoration:none;
    }
</style>
<div>
    <div class="well" align="center">
        <a href="index.php?r=statistics&year=<?php echo $year-1;?>">《《&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
        <?php echo $year;?>年度销售报表
        <a href="index.php?r=statistics&year=<?php echo $year+1;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;》》</a>
    </div>

    <h4>年度销售情况</h4>
    <div style="width:75%;">
        <canvas id="canvas"></canvas>
    </div>
</div>
<script>
    var MONTHS = ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];

    var randomScalingFactor = function() {
        return Math.round(Math.random() * 50 * (Math.random() > 0.5 ? 1 : 1)) + 50;
    };
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
    };

    var config = {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                label: "月营业额",
                data: <?php echo "[".implode(',',$total_price)."]";?>,
                fill: false,
                borderDash: [5, 5],
            }]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:"Chart.js Line Chart - X-Axis Filter"
            },
            scales: {
                xAxes: [{
                    display: true,
                    ticks: {
                        userCallback: function(dataLabel, index) {
                            return index % 2 === 0 ? dataLabel : '';
                        }
                    }
                }],
                yAxes: [{
                    display: true,
                    beginAtZero: false
                }]
            }
        }
    };

    $.each(config.data.datasets, function(i, dataset) {
        dataset.borderColor = randomColor(0.4);
        dataset.backgroundColor = randomColor(0.5);
        dataset.pointBorderColor = randomColor(0.7);
        dataset.pointBackgroundColor = randomColor(0.5);
        dataset.pointBorderWidth = 1;
    });

</script>
<div style="padding-top: 80px;">
<h4>销售占比</h4>
<!--<div id="canvas-holder" style="width: 50px;">-->
<!--    <canvas id="chart-area1" width="50" height="50" />-->
<!--</div>-->
<div id="canvas-holder" style="width: 300px;margin-left: 10%;">
    <canvas id="chart-area2" width="300" height="300" />
</div>

<div id="chartjs-tooltip"></div>

</div>
<script>
    Chart.defaults.global.tooltips.custom = function(tooltip) {

        // Tooltip Element
        var tooltipEl = $('#chartjs-tooltip');

        if (!tooltipEl[0]) {
            $('body').append('<div id="chartjs-tooltip"></div>');
            tooltipEl = $('#chartjs-tooltip');
        }

        // Hide if no tooltip
        if (!tooltip.opacity) {
            tooltipEl.css({
                opacity: 0
            });
            $('.chartjs-wrap canvas')
                .each(function(index, el) {
                    $(el).css('cursor', 'default');
                });
            return;
        }

        $(this._chart.canvas).css('cursor', 'pointer');

        // Set caret Position
        tooltipEl.removeClass('above below no-transform');
        if (tooltip.yAlign) {
            tooltipEl.addClass(tooltip.yAlign);
        } else {
            tooltipEl.addClass('no-transform');
        }

        // Set Text
        if (tooltip.body) {
            var innerHtml = [
                (tooltip.beforeTitle || []).join('\n'), (tooltip.title || []).join('\n'), (tooltip.afterTitle || []).join('\n'), (tooltip.beforeBody || []).join('\n'), (tooltip.body || []).join('\n'), (tooltip.afterBody || []).join('\n'), (tooltip.beforeFooter || [])
                    .join('\n'), (tooltip.footer || []).join('\n'), (tooltip.afterFooter || []).join('\n')
            ];
            tooltipEl.html(innerHtml.join('\n'));
        }

        // Find Y Location on page
        var top = 0;
        if (tooltip.yAlign) {
            if (tooltip.yAlign == 'above') {
                top = tooltip.y - tooltip.caretHeight - tooltip.caretPadding;
            } else {
                top = tooltip.y + tooltip.caretHeight + tooltip.caretPadding;
            }
        }

        var position = $(this._chart.canvas)[0].getBoundingClientRect();

        // Display, position, and set styles for font
        tooltipEl.css({
            opacity: 1,
            width: tooltip.width ? (tooltip.width + 'px') : 'auto',
            left: position.left + tooltip.x + 'px',
            top: position.top + top + 'px',
            fontFamily: tooltip._fontFamily,
            fontSize: tooltip.fontSize,
            fontStyle: tooltip._fontStyle,
            padding: tooltip.yPadding + 'px ' + tooltip.xPadding + 'px',
        });
    };

    var config2 = {
        type: 'pie',
        data: {
            datasets: [{
                    data: [<?php echo $year_order_price;?>,<?php echo $year_package_price;?>,<?php echo $year_balance_price;?>],
                backgroundColor: [
                    "#F7464A",
                    "#46BFBD",
                    "#FDB45C",
//                    "#949FB1",
//                    "#4D5360",
                ],
            }],
            labels: [
                "工单",
                "套餐",
                "充值",
//                "Grey",
//                "Dark Grey"
            ]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                enabled: false,
            }
        }
    };

    window.onload = function() {
//        var ctx1 = document.getElementById("chart-area1").getContext("2d");
//        window.myPie = new Chart(ctx1, config2);

        var ctx2 = document.getElementById("chart-area2").getContext("2d");
        window.myPie = new Chart(ctx2, config2);

        var ctx = document.getElementById("canvas").getContext("2d");
        window.myLine = new Chart.Bar(ctx, config);
    };
</script>
