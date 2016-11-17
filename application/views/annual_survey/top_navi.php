<ul class="topNaviUlKec">
    <li class="<?php if(strpos(current_url(),'annualsurvey/info')){?>cur<?php } ?>">
        <a href="<?php echo site_url('annualsurvey/info/'.$survey['id']);?>">问卷信息</a>
    </li>
    <li class="<?php if(strpos(current_url(),'annualsurvey/qa')){?>cur<?php } ?>">
        <a href="<?php echo site_url('annualsurvey/qa/acceptance/'.$survey['id']);?>">问题设置</a>
    </li>
    <li class="<?php if(strpos(current_url(),'annualsurvey/course/'.$survey['id'])){?>cur<?php } ?>">
        <a href="<?php echo site_url('annualsurvey/course/'.$survey['id']);?>">课程列表</a>
    </li>
    <li class="<?php if(strpos(current_url(),'annualsurvey/surveylist/'.$survey['id'])){?>cur<?php } ?>">
        <a <?php if($anscount>0){ ?>href="<?php echo site_url('annualsurvey/surveylist/'.$survey['id']);?>"<?php }else{ ?>style="color:#cccccc;cursor: no-drop;" href="javascript:;"<?php } ?> >提交名单</a>
    </li>
    <li class="<?php if(strpos(current_url(),'annualsurvey/answeranalysis/'.$survey['id'])){?>cur<?php } ?>">
        <a <?php if($anscount>0){ ?>href="<?php echo site_url('annualsurvey/answeranalysis/'.$survey['id']);?>"<?php }else{ ?>style="color:#cccccc;cursor: no-drop;" href="javascript:;"<?php } ?>>答案统计</a>
    </li>

</ul>