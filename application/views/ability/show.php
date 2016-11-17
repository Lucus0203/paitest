<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/font-awesome.min.css"/>
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
    var editflag=false;
    $(function(){
        var config = {
            type: 'radar',
            data: {
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"经验",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'<?php echo $abilityjob['name'] ?>',
                    backgroundColor: "rgba(156,224,234,0.7)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [<?php if(array_key_exists(1,$levelradar)){ echo round($levelradar[1]['level_standard']/$levelradar[1]['level_total']*5,1); ?>,<?php } ?> <?php if(array_key_exists(3,$levelradar)){ echo round($levelradar[3]['level_standard']/$levelradar[3]['level_total']*5,1); ?>,<?php } ?><?php if(array_key_exists(5,$levelradar)){ echo round($levelradar[5]['level_standard']/$levelradar[5]['level_total']*5,1); ?>,<?php } ?><?php if(array_key_exists(4,$levelradar)){ echo round($levelradar[4]['level_standard']/$levelradar[4]['level_total']*5,1); ?>,<?php } ?><?php if(array_key_exists(2,$levelradar)){ echo round($levelradar[2]['level_standard']/$levelradar[2]['level_total']*5,1); } ?>]
                },]
            },
            options: {
                legend: {
                    //display:false
                    labels:{boxWidth: 20}
                },
                scale: {
                    ticks: {
                        beginAtZero: true,
                        backdropColor:'rgba(255, 255, 255, 0)'
                    }
                }
            }
        };
        window.myRadar = new Chart(document.getElementById("canvas"), config);

        $('ul.star li').click(function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur yellow').siblings().removeClass('cur yellow');
            var starBox=$(this).parent().parent();
            starBox.find('.starTxt').hide().eq(i).show();
            starBox.prev().val(i+1);
            resetRadarData();
            if($('#newAbilityStandard').hasClass('borGaryBtnH28')){
                $('#newAbilityStandard').removeClass('borGaryBtnH28').addClass('borBlueBtnH28');
            }
            editflag=true;
            return false;
        });
        $('.editBtn').live('click',function(){
            var span=$(this).parent();
            var txt=span.text();
            var v=txt.slice(txt.indexOf('、')+1);
            var inp=span.find('input').eq(0).val(v).show();
            span.text(txt.slice(0,txt.indexOf('、')+1)).append(inp);
            var strLength = inp.val().length * 2;
            inp.attr('data-prev',inp.val()).focus();
            inp[0].setSelectionRange(strLength, strLength);
            inp.blur(function(){
                var v=$(this).val()==''?$(this).attr('data-prev'):$(this).val();
                var inp=$(this).hide();
                var txt=$(this).parent().text();
                $(this).parent().text(txt+v).append(inp).append('<a href="#" class="editBtn blue"><i class="f18 ml10 fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></a>');
                editflag=true;
                if($('#newAbilityStandard').hasClass('borGaryBtnH28')){
                    $('#newAbilityStandard').removeClass('borGaryBtnH28').addClass('borBlueBtnH28');
                }
            });
            return false;
        });
        function resetRadarData(){
            var data=[0,0,0,0,0,0];
            $('.company_model_type').each(function(i){
                var type=$(this).val()*1;
                data[type]+=$('.company_model_level').eq(i).val()*1;
            });
            for(i in data){
                data[i]=data[i]/$('.starType'+i+' li').length*5;
            }
            var radardata=new Array();
            radardata[0]=data[1];
            radardata[1]=data[3];
            radardata[2]=data[5];
            radardata[3]=data[4];
            radardata[4]=data[2];
            radardata=radardata.filter(function(n){return n});
            config.data.datasets[0].data=radardata;
            window.myRadar.update();

        }
        //保存新标准
        $('#newAbilityStandard').click(function(){
            if($('#newAbilityStandard').hasClass('borBlueBtnH28')){
                if(confirm('确定保存新标准?')){
                    var levels=types=mids=mnames='';
                    var jobid=$('#jobid').val();
                    $(".company_model_level").each(function(){
                        levels+=levels==''?$(this).val():','+$(this).val();
                    });
                    $(".company_model_type").each(function(){
                        types+=types==''?$(this).val():','+$(this).val();
                    });
                    $(".company_model_id").each(function(){
                        mids+=mids==''?$(this).val():','+$(this).val();
                    });
                    $(".company_model_name").each(function(){
                        mnames+=mnames==''?$(this).val():','+$(this).val();
                    });
                    $.ajax({
                        url:'<?php echo site_url('ability/saveStandard') ?>',
                        type:'post',
                        data:{'jobid':jobid,'levels':levels,'types':types,'mids':mids,'mnames':mnames},
                        dataType:"json",
                        success:function(res){
                            if(res.success!='ok'){
                                $('.alert-danger .alert-msg').text(res.msg).parent().show();
                            }else{
                                $('ul.star li.yellow').addClass('blue').siblings().removeClass('blue');
                                $('.alert-success .alert-msg').text('保存成功!').parent().show();
                                $('#resetAbilityStandard').removeClass('borGaryBtnH28').addClass('borBlueBtnH28');
                                editflag=false;
                            }
                        }
                    });
                }
            }
            return false;
        });
        //重置新标准
        $('#resetAbilityStandard').click(function(){
            if($('#resetAbilityStandard').hasClass('borBlueBtnH28')){
                if(confirm('重置不可恢复,确定重置吗?')){
                    $.ajax({
                        url:'<?php echo site_url('ability/resetStandard/'.$abilityjob['id']) ?>',
                        dataType:"json",
                        success:function(res){
                            if(res.success!='ok'){
                                alert(res.msg);
                            }else{
                                window.location.reload();
                            }
                        }
                    });
                }
            }
            return false;
        });
        $('.nengliRight').scrollToFixed({
            marginTop: $('.nengli').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
    });
//    window.onbeforeunload = function() {
//        if(editflag) {
//            return confirm('您修改的岗位内容未保存,是否保存?');
//        }
//    };
</script>
<div class="wrap">
    <div class="textureCont width100">
        <input id="jobid" type="hidden" value="<?php echo $abilityjob['id'] ?>" />
        <div class="texturetip clearfix"><span class="fLeft mr10"><?php echo $abilityjob['name'] ?>评估标准</span>
            <div class="fRight">
                <a class="borBlueH37" href="<?php echo site_url('html/ability') ?>">更多模型</a>
            </div>
        </div>
        <p class="alertBox alert-success " style="display: none;"><span class="alert-msg">保存成功!</span><a href="javascript:;" class="alert-remove">X</a></p>
        <p class="alertBox alert-danger" style="display: none;"><span class="alert-msg">保存失败!</span><a href="javascript:;" class="alert-remove">X</a></p>
        <div class="nengli">
            <div class="nengliRight pt10">
                <div class="fRight">
                    <a id="newAbilityStandard" class="borGaryBtnH28" href="#">保存新标准</a>
                    <a id="resetAbilityStandard" <?php echo $cpjob['standard_type']==2?'class="borBlueBtnH28"':'class="borGaryBtnH28"' ?> href="<?php echo site_url('ability/resetStandard/'.$abilityjob['id']) ?>">重置默认值</a>
                    <a class="borBlueBtnH28" href="<?php echo site_url('ability/index') ?>">返回岗位列表</a>
                </div>
                <div style="width: 500px;height:300px; margin:40px 0 0 -100px;">
                    <canvas id="canvas"></canvas>
                </div>
                <p class="txt">匹配学员:</p>
                <p class="txt ml10"><?php if(count($students)>0){?>
                        <?php foreach ($students as $stu){?>
                            <?php if($stu['status']==2){//完成评估 ?>
                                <a class="blue" href="<?php echo site_url('ability/targetdetail/'.$abilityjob['id'].'/'.$stu['student_id']) ?>" ><?php echo $stu['name'] ?></a>
                            <?php }else{ echo $stu['name']; } ?>
                        <?php }?>
                    <?php }else{ echo '暂无匹配对象';} ?></p>
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
                            <p class="txt">
                                <span><?php echo ($k+1).'、'.$a['model_name'] ?><input class="company_model_name" style="display: none;" type="text" value="<?php echo $a['model_name'] ?>" maxlength="50"><a href="#" class="editBtn blue"><i class="f18 ml10 fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></a></span>
                                <?php echo $a['info'] ?>
                            </p>
                            <input class="company_model_id" type="hidden" value="<?php echo $a['id'] ?>">
                            <input class="company_model_type" type="hidden" value="<?php echo $a['type'] ?>">
                            <input class="company_model_level" type="hidden" value="<?php echo $a['level_standard'] ?>">
                            <div class="starBox">
                                <ul class="star starType<?php echo $a['type'] ?>">
                                    <?php for($i=1;$i<=$a['level'];$i++){?>
                                    <li class="<?php if($i==$a['level_standard']){ echo 'cur blue'; } ?>" >
                                        <a href="#"><i class="fa fa-star fa-3x"></i><span class="num"><?php echo $i ?></span></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php for($i=1;$i<=$a['level'];$i++){?>
                                <p class="starTxt" <?php if($i!=$a['level_standard']){ ?>style="display: none;"<?php } ?> ><?php echo nl2br($a['level_info'.$i]) ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>
                <?php } ?>
            </div>
        </div>

    </div>
</div>