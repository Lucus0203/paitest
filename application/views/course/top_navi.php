<ul class="topNaviUlKec">
        <li <?php if(strpos(current_url(),'course/courseinfo')){?> class="cur"<?php } ?>><a href="<?php echo site_url('course/courseinfo/'.$course['id']) ?>">课程信息</a></li>
<?php if($loginInfo['role']==1||$roleInfo['applylist']==1||$roleInfo['applylist']==1){ ?>
        <li <?php if(strpos(current_url(),'course/applyset')||strpos(current_url(),'course/applylist')||strpos(current_url(),'course/notifyset')){?> class="cur"<?php } ?>><a href="<?php $uriarg=$roleInfo['applylist']==1?'applylist':'applyset'; echo site_url('course/'.$uriarg.'/'.$course['id']) ?>">报名管理</a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['signinset']==1||$roleInfo['signinlist']==1){ ?>
        <li <?php if(strpos(current_url(),'course/signinset')||strpos(current_url(),'course/signinlist')){?> class="cur"<?php } ?>><a href="<?php $uriarg=$roleInfo['signinlist']==1?'signinlist':'signinset'; echo site_url('course/'.$uriarg.'/'.$course['id']) ?>">签到管理</a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['surveyedit']==1||$roleInfo['surveylist']==1){ ?>
        <li <?php if(strpos(current_url(),'course/surveyedit')||strpos(current_url(),'course/surveylist')){?> class="cur"<?php } ?>><a href="<?php $uriarg=$roleInfo['surveylist']==1?'surveylist':'surveyedit'; echo site_url('course/'.$uriarg.'/'.$course['id']) ?>">课前调研</a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['homeworkedit']==1||$roleInfo['homeworklist']==1){ ?>
        <!--<li <?php if(strpos(current_url(),'course/homeworkedit')||strpos(current_url(),'course/homeworklist')){?> class="cur"<?php } ?>><a href="<?php $uriarg=$roleInfo['homeworklist']==1?'homeworklist':'homeworkedit'; echo site_url('course/'.$uriarg.'/'.$course['id']) ?>">课前作业</a></li>-->
<?php } ?>
        <li <?php if(strpos(current_url(),'course/prepare')){?> class="cur"<?php } ?>><a href="<?php echo site_url('course/prepare/'.$course['id']) ?>">课程公告</a></li>
<?php if($loginInfo['role']==1||$roleInfo['ratingsedit']==1||$roleInfo['ratingslist']==1){ ?>
        <li <?php if(strpos(current_url(),'course/ratingsedit')||strpos(current_url(),'course/ratingslist')){?> class="cur"<?php } ?>><a href="<?php $uriarg=$roleInfo['ratingslist']==1?'ratingslist':'ratingsedit'; echo site_url('course/'.$uriarg.'/'.$course['id']) ?>">课程反馈</a></li>
<?php } ?>
</ul>