<!DOCTYPE html>
<html>

    <head>
            <meta charset="UTF-8">
            <title></title>
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/font-awesome.min.css" />
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/common.css" />
            <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css?112101" />
            <script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
    </head>

    <body class="nobg">




    <div class="headerCom">
                    <div class="inner">
                            <div class="log">
                                    <a href="javascript:void(0);"><img src="<?php echo base_url();?>images/logo01.png" alt="培训派"></a><span class="logoTip"><?php echo $course['title']?></span>
                            </div>


                    </div>
            </div>
            <div class="wrap">
                <p class="askbox">反馈人:<span class="fbold">&nbsp;<?php echo $student['name'] ?>&nbsp;</span>
                    <?php echo $student['job_name'].'/'.$student['department'].'/'.$student['mobile']; ?><span class="ml20">提交时间：<?php echo date("Y.m.d H:i:s",strtotime($ratings[0]['created'])) ?></span></p>
                    <div class="comBox p40">
                        <?php $firstrat=array_shift($ratings);?>
                        <p class="aCenter f24 mb20">总体评分</p>
                        <div class="starBox aCenter f18">
                            <ul class="star">
                                <?php for($i=1;$i<6;$i++){?>
                                    <li class="blue" >
                                        <a href="javascript:void(0)" class="<?php echo ($i>round($firstrat['star']))?'starGray7':''?>"><i class="fa fa-star fa-2x"></i><span class="num"><?php echo $i ?></span></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <dl class="askDl">
                            <?php foreach ($ratings as $k=>$h){ ?>
                                <dt><?php echo ($h['type']==1)?'评分题':'开放题' ?>&nbsp;<?php echo $h['title'] ?></dt>
                                <dd class="f18 mb10">
                                    <?php if($h['type']==1){ ?>
                                        <div class="starBox mb0">
                                            <ul class="star">
                                                <?php for($i=1;$i<6;$i++){?>
                                                    <li class="blue" >
                                                        <a href="javascript:void(0)" class="<?php echo ($i>round($h['star']))?'starGray7':''?> pb0"><i class="fa fa-star fa-2x"></i><span class="num"><?php echo $i ?></span></a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php }elseif($h['type']==2){
                                        echo nl2br($h['content']);
                                    }?>
                                </dd>
                            <?php } ?>
                        </dl>
                    </div>
            </div>
            <div class="footer">
                    <p><a href="http://www.trainingpie.com/about_us.html" target="_blank">关于培训派</a></p>
                    <p>Copyright &copy;2016 peixunpai.com All Rights Reserved</p>
            </div>
    </body>

</html>