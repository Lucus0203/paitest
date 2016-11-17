<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>js/Chart.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script>
    $(function(){
        var config = {
            type: 'radar',
            data: {
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"经验",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'<?php echo $student['name'] ?>',
                    backgroundColor: "rgba(255, 206, 73,0.5)",
                    pointBackgroundColor: "rgba(255, 206, 73,1)",
                    data: [<?php if(array_key_exists(1,$studentPoint)){ echo round($studentPoint[1]['point']/$studentPoint[1]['level']*5,1) ?>,<?php } ?>
                        <?php if(array_key_exists(3,$studentPoint)){ echo round($studentPoint[3]['point']/$studentPoint[3]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(5,$studentPoint)){ echo round($studentPoint[5]['point']/$studentPoint[5]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(4,$studentPoint)){ echo round($studentPoint[4]['point']/$studentPoint[4]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(2,$studentPoint)){ echo round($studentPoint[2]['point']/$studentPoint[2]['level']*5,1) ?>,<?php } ?>]
                },{
                    label:'<?php echo $abilityjob['name'] ?>',
                    backgroundColor: "rgba(156,224,234,0.5)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [<?php if(array_key_exists(1,$studentPoint)){ echo round($standard[1]/$studentPoint[1]['level']*5,1) ?>,<?php } ?>
                        <?php if(array_key_exists(3,$studentPoint)){ echo round($standard[3]/$studentPoint[3]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(5,$studentPoint)){ echo round($standard[5]/$studentPoint[5]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(4,$studentPoint)){ echo round($standard[4]/$studentPoint[4]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(2,$studentPoint)){ echo round($standard[2]/$studentPoint[2]['level']*5,1) ?>,<?php } ?>]
                }]
            },
            options: {
                legend: {
                    //display:false
                    labels:{boxWidth: 20}
                },
                scale: {
                    gridLines: {
                        color: ['#d8d8d8']
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        };
        window.myRadar = new Chart(document.getElementById("canvas"), config);

        $('.nengliRight').scrollToFixed({
            marginTop: $('.nengli').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
        $('ul.star li').click(function() {
            var i = $(this).parent().find('li').index($(this));
            $(this).addClass('cur').siblings().removeClass('cur');
            var starBox = $(this).parent().parent();
            starBox.find('.starTxt').hide().eq(i).show();
            starBox.prev().val(i + 1);
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="textureCont w960">

        <div class="texturetip clearfix"><span class="fLeft pt5"><?php echo $student['name'].'《'.$abilityjob['name'] ?>》能力评估</span>
            <div class="fRight">
                <a class="borBlueH37" href="<?php echo $_SERVER['HTTP_REFERER'] ?>">返回</a>
            </div>
        </div>

        <div class="nengli">
            <div class="nengliRight">
                <div style="width: 500px;height:500px; margin:40px 0 0 -100px;">
                    <canvas id="canvas"></canvas>
                </div>
            </div>
            <div class="nengliLeft">
                <?php foreach ($abilities as $key=>$abilies) {?>
                    <?php if($key==1){
                        echo '<p class="blueline"><span>专业/技能</span></p>';
                    }elseif($key==2){
                        echo '<p class="blueline"><span>通用能力</span></p>';
                    }elseif($key==3){
                        echo '<p class="blueline"><span>领导力</span></p>';
                    }elseif($key==4){
                        echo '<p class="blueline"><span>个性</span></p>';
                    }elseif($key==5){
                        echo '<p class="blueline"><span>经验</span></p>';
                    } ?>
                        <?php foreach ($abilies as $k=>$a){ ?>
                            <p class="txt"><span><?php echo ($k+1).'、'.$a['name'] ?></span><?php echo $a['info'] ?></p>

                            <div class="starBox">
                                <ul class="star">
                                    <?php for($i=1;$i<=$a['level'];$i++){?>
                                    <li class="<?php if($a['point']==$i){ echo 'cur yellow'; }elseif($a['level_standard']==$i){echo 'blue';} ?>" >
                                        <a href="#"><i class="fa fa-star fa-3x"></i><span class="num"><?php echo $i ?></span></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php for($i=1;$i<=$a['level'];$i++){ ?>
                                <p <?php if($a['point']!=$i){?>style="display:none;"<?php } ?> class="starTxt"><?php echo nl2br($a['level_info'.$i]) ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>
                <?php } ?>
            </div>
        </div>

    </div>
</div>