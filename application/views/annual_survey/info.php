<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<script>
    $(document).ready(function(){
        $('#stoping').click(function () {
            return confirm('确定暂停发布吗?');
        });
        $('.canntDelBtn').click(function(){alert('进行中无法删除,请您先暂停发布');return false;});
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <?php $this->load->view ( 'annual_survey/top_tit' ); ?>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_survey/top_navi' ); ?>
        <?php if($survey['public']=='2'){ ?>
        <a id="stoping" href="<?php echo site_url('annualsurvey/stoping/'.$survey['id']) ?>" class="fRight borBlueH37 mt5 mr5">暂停发布</a>
        <?php }else{ ?>
            <a href="<?php echo site_url('annualsurvey/starting/'.$survey['id']) ?>" class="fRight borBlueH37 mt5 mr5"><?php echo $survey['public']=='3'?'继续发布':'发布' ?></a>
        <?php } ?>
    </div>

    <div class="comBox">
        <p class="opBtn pb0">
            <a href="<?php echo site_url('annualsurvey/edit/'.$survey['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑信息</a><a href="<?php echo site_url('annualsurvey/copy/'.$survey['id']);?>" class="editBtn"><i class="fa fa-copy fa-lg mr5"></i>复制问卷</a><a href="<?php echo site_url('annualsurvey/del/'.$survey['id']);?>" class="<?php echo (strtotime($survey['time_start'])<time()&&time()<strtotime($survey['time_end'])&&$survey['public']==2)?'canntDelBtn':'delBtn' ?>"><i class="fa fa-trash-o fa-lg mr5"></i>删除问卷</a>
        <div class="ewmBox">
            <div class="boxl">
                <p class="blue f18"><?php echo $survey['title'] ?></p>
                <p class="f14 gray6 mb10">开始时间：<?php if(empty($survey['time_start'])||empty($survey['time_end'])){echo '暂未设置';}else{ ?><?php echo $survey['time_start'] ?> 至 <?php echo $survey['time_end']; }?></p>
                <p class="borderTop f14 gray6 pt10">问卷备注：<?php echo nl2br($survey['info']) ?></p>
            </div>
            <div class="fRight"><img src="<?php echo base_url('uploads/annualqrcode/'.$survey['qrcode'].'.png') ?>" alt="" width="160"><p class="aCenter gray9">扫一扫预览问卷</p><p class="aCenter gray9"><a href="<?php echo site_url('annualsurvey/downloadqrcode/'.$survey['id'])?>" class="blue">下载二维码</a>,邮箱发送给学员</p></div>

        </div>
        <dl class="kecDl">
        </dl>

    </div>
</div>