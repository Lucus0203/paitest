<ul class="topNaviUlKec">
    <li class="<?php if(strpos(current_url(),'annualplan/course/'.$plan['id'])){?>cur<?php } ?>">
        <a href="<?php echo site_url('annualplan/course/'.$plan['id']);?>">课程信息</a>
    </li>
    <li class="<?php if(strpos(current_url(),'annualplan/plan/'.$plan['id'])){?>cur<?php } ?>">
        <a href="<?php echo site_url('annualplan/plan/'.$plan['id']);?>">年度培训计划</a>
    </li>
    <li class="<?php if(strpos(current_url(),'annualplan/analysis/'.$plan['id'])){?>cur<?php } ?>">
        <a href="<?php echo site_url('annualplan/analysis/'.$plan['id']);?>">统计图</a>
    </li>
</ul>