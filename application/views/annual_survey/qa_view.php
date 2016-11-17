<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<script type="text/javascript">
    $(document).ready(function(){
        $('.sideLeft').scrollToFixed({
            marginTop: $('.baoming').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <?php $this->load->view ( 'annual_survey/top_tit' ); ?>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_survey/top_navi' ); ?>
    </div>
    <div class="comBox">
        <p class="yellowTipBox mt20">请注意您的调研时间,调研问卷开始后,内容不可修改</p>
        <div class="baoming">
            <div class="sideLeft">
                <ul class="sideLnavi">
                    <li class="<?php if(strpos(current_url(),'annualsurvey/qa/acceptance/'.$survey['id'])){?>cur<?php } ?>" style="padding-left: 20px;"><a href="<?php echo site_url('annualsurvey/qa/acceptance/'.$survey['id']);?>">培训认同度<?php if(strpos(current_url(),'annualsurvey/qa/acceptance/'.$survey['id'])){?><i class="ml10 fa fa-angle-right fa-lg"></i><?php } ?></a></li>
                    <li class="<?php if(strpos(current_url(),'annualsurvey/qa/organization/'.$survey['id'])){?>cur<?php } ?>" style="padding-left: 20px;"><a href="<?php echo site_url('annualsurvey/qa/organization/'.$survey['id']);?>">培训组织性<?php if(strpos(current_url(),'annualsurvey/qa/organization/'.$survey['id'])){?><i class="ml10 fa fa-angle-right fa-lg"></i><?php } ?></a></li>
                    <li class="<?php if(strpos(current_url(),'annualsurvey/qa/requirement/'.$survey['id'])){?>cur<?php } ?>" style="padding-left: 20px;"><a href="<?php echo site_url('annualsurvey/qa/requirement/'.$survey['id']);?>">需 求 信 息<?php if(strpos(current_url(),'annualsurvey/qa/requirement/'.$survey['id'])){?><i class="ml10 fa fa-angle-right fa-lg"></i><?php } ?></a></li>
                </ul>
            </div>
            <div class="contRight">
                <?php if(count($questions)>0) {
                    foreach ($questions as $kq => $q) { ?>
                        <div class="p20 borderBottom">
                            <ul class="zuoyeList">
                                <li>
                                    <span class="aRight numtype gray9">
                                        <?php echo($kq + 1); ?>.<?php if ($q['type'] == 1) {
                                            echo '单选题';
                                        } elseif ($q['type'] == 2) {
                                            echo '多选题';
                                        } elseif ($q['type'] == 3) {
                                            echo '开放题';
                                        } ?>
                                    </span>
                                    <span class="gray9 f14 ml10"><?php echo $q['title'] ?>
                                        <?php echo ($q['required'] == 2) ? '(选答)' : '(必答)'; ?></span>
                                </li>
                                <?php if ($q['type'] == 1 || $q['type'] == 2) {
                                    foreach ($q['options'] as $ko => $op) {
                                        ?>
                                        <li><span class="w50 gray9 aRight">选项<?php echo $ko + 1 ?></span>
                                            <span class="gray9 f14 ml10"><?php echo $op['content'] ?></span>
                                        </li>
                                    <?php }
                                } ?>
                            </ul>
                        </div>
                    <?php }
                }else{
                    echo '<div class="p20 borderBottom"><span class="gray9 f14 ml10">没有设置问题内容</span></div>';
                }?>
            </div>
        </div>
    </div>
</div>