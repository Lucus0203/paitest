<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script>
    $(document).ready(function () {
        $('.canntDelBtn').click(function(){alert('进行中无法删除,请您先暂停发布');return false;});
    });
</script>
<div class="wrap">
    <div class="textureCont w960">
        <div class="texturetip p1524 clearfix">
            <div class="fLeft"><span class="pt5">年度调研</span>
                <?php if(!$isAccessAccount){ ?><p class="clearfix gray9">您正免费体验该功能,有5个体验名额,如需开通请联系<a class="blue" href="tel:021-61723727">021-61723727</a>,辛老师</p><?php } ?>
            </div>
            <div class="fRight">
                <a class="borBlueH37" href="<?php echo site_url('annualsurvey/create') ?>">创建新问卷</a>
            </div>
        </div>
        <div class="topNavi">
            <ul class="topNaviUl">
                <li <?php if(empty($parm['status'])){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index') ?>">全部调研问卷</a></li>
                <li <?php if($parm['status']==2){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index').'?status=2' ?>">未开始</a></li>
                <li <?php if($parm['status']==1){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index').'?status=1' ?>">进行中</a></li>
                <li <?php if($parm['status']==3){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('annualsurvey/index').'?status=3' ?>">已结束</a></li>
            </ul>

        </div>

        <div class="seachBox clearfix borderTop">
            <form method="get" action="">
                <ul>
                    <li class="w250 mr60">
                        <input name="keyword" type="text" value="<?php echo $parm['keyword'] ?>" class="ipt w250" placeholder="关键字">
                    </li>
                    <li class="w496 btn"><span class="mr20">开课时间</span><input name="time_start" type="text" value="<?php echo $parm['time_start'] ?>" class="ipt w156 mr10 DTdate" autocomplete="off">至
                        <input name="time_end" type="text" value="<?php echo $parm['time_end'] ?>" class="ipt w156 ml10 DTdate" autocomplete="off">
                    </li>

                    <li class="btn fRight"><input type="submit" class="borBlueH37 mt3" value="搜索" /></li>
                </ul>
            </form>
        </div>
        <div class="listBox">
            <?php if(count($surveies)>0){?>
                <?php foreach ($surveies as $c){ ?>
                    <div class="listCont">
                        <p class="operaBtn">
                            <a href="<?php echo site_url('annualsurvey/edit/'.$c['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑</a><a href="<?php echo site_url('annualsurvey/copy/'.$c['id']);?>" class="shareBtn"><i class="fa fa-copy fa-lg mr5"></i>复制</a><a href="<?php echo site_url('annualsurvey/del/'.$c['id']); ?>" class="<?php echo ($c['status']==1&&$c['public']==2)?'canntDelBtn':'delBtn' ?>"><i class="fa fa-trash-o fa-lg mr5"></i>删除</a></p>
                        <div class="listText">
                            <p class="titp"><a class="blue" href="<?php echo site_url('annualsurvey/info/'.$c['id']);?>"><?php echo $c['title'] ?></a></p>
                            <p class="titp">
                                <?php if($c['status']==1&&$c['public']==2){ ?>
                                    <span class="greenH25">进行中</span>
                                <?php }elseif($c['status']==2||$c['public']!=2){ ?>
                                    <span class="orangeH25">未发布</span>
                                <?php }elseif($c['status']==3){ ?>
                                    <span class="grayH25">已结束</span>
                                <?php } ?>
                            </p>
                            <p><span class="mr30">开始时间：<?php if(empty($c['time_start'])||empty($c['time_end'])){echo '暂未设置';}else{ ?><?php echo date('Y-m-d H:i',strtotime($c['time_start'])) ?>&nbsp;至&nbsp;<?php echo date('Y-m-d H:i',strtotime($c['time_end'])); }?></span></p>
                            <?php if(!empty($c['info'])){ ?><p>问卷备注：<?php echo mb_strlen($c['info'],'utf-8')>30?mb_substr($c['info'],0,30,'utf-8').'……':mb_substr($c['info'],0,30,'utf-8') ?></p><?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{
                echo '<div class="listCont"><div class="listText"><p>暂无符合条件的调研问卷</p></div></div>';
            } ?>
        </div>
        <div class="pageNavi">
            <?php echo $links ?>
        </div>
    </div>
</div>