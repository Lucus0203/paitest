<script type="text/javascript"  src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).tooltip();
    });
</script>
<span class="titSpan"><?php echo $survey['title'] ?></span>
<?php if(strtotime($survey['time_start'])<time()&&time()<strtotime($survey['time_end'])&&$survey['public']==2){ ?>
    <span class="greenH25 ml20">进行中</span>
<?php }elseif(time()<strtotime($survey['time_start'])||$survey['public']!=2){ ?>
    <span class="orangeH25 ml20">未发布</span>
<?php }elseif(time()>strtotime($survey['time_end'])){ ?>
    <span class="grayH25 ml20">已结束</span>
<?php } ?>
<?php if($anscount>0){ ?>
    <a href="<?php echo site_url('annualplan/create/'.$survey['id']) ?>" class="fRight borBlueH37 mr5">生成年度计划</a>
<?php }else{ ?>
    <a href="javascript:;" class="fRight borBlueH37 aCenter mr5" title="暂未调研数据,无法生成年度计划" style="border: none;background-color: #ccc; color:#fff;">生成年度计划</a>
<?php } ?>