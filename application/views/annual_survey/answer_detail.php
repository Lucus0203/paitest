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
            <a href="javascript:void(0);"><img src="<?php echo base_url();?>images/logo01.png" alt="培训派"></a>
            <span class="logoTip"><?php echo $survey['title']?></span>
        </div>
    </div>
</div>
<div class="wrap">
    <p class="askbox">答卷人:<span class="fbold">&nbsp;<?php echo $student['name'] ?>&nbsp;</span>
        <?php echo $student['job_name'].'/'.$student['department'].'/'.$student['mobile']; ?><span class="ml20">提交时间：<?php echo date("Y.m.d H:i:s",strtotime($answer['created'])) ?></span></p>
    <div class="comBox p40">
        <?php foreach ($step as $sk=>$sp){?>
        <div class="titCom clearfix"><span class="titSpan">
                <?php
                switch ($sp){
                    case 'acceptance':
                        echo '认同度';
                        break;
                    case 'organization':
                        echo '组织性';
                        break;
                    case 'requirement':
                        echo '需求信息';
                        break;
                    default :
                        break;
                }
                ?>
            </span></div>
        <dl class="askDl">
            <?php foreach ($answer[$sp] as $k=>$q){ ?>
                <dt><?php echo $q['title'] ?></dt>
                <dd class="f18 mb10">
                    <?php if($q['type']==1){
                        echo $q['answer']['option_title'];
                    }elseif($q['type']==2){?>
                        <div class="starBox mb0">
                            <ul>
                                <?php foreach ($q['answer'] as $ans){?>
                                    <li>
                                        <?php echo $ans['option_title'] ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php }elseif($q['type']==3){
                        echo nl2br($q['answer']['answer_content']);
                    }?>
                </dd>
            <?php } ?>
        </dl>
        <?php } ?>
        <div class="titCom clearfix"><span class="titSpan">选择课程</span></div>
        <dl class="askDl">
            <dd class="f18 mb10">
            <div class="starBox mb0">
                <ul>
                    <?php foreach ($answer['courses'] as $c){?>
                        <li>
                            <?php echo '《'.$c['title'].'》'; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            </dd>
        </dl>
    </div>
</div>
<div class="footer">
    <p><a href="http://www.trainingpie.com/about_us.html" target="_blank">关于培训派</a></p>
    <p>Copyright &copy;2016 trainingpie.com 上海齐训网络科技有限公司 All Rights Reserved  <a href="http://www.miitbeian.gov.cn" target="_blank">沪ICP备13006997号-2</a></p>
</div>
</body>

</html>