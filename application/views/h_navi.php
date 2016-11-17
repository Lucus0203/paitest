<li><a href="<?php echo site_url('index/index') ?>">首页</a></li>
<li id="naviCourse"><a href="<?php echo site_url('course/courselist') ?>">课程管理</a></li>
<li id="naviTeacher"><a href="<?php echo site_url('teacher/teacherlist') ?>">讲师资源</a></li>
<?php if($loginInfo['role']==1||$roleInfo['department']==1||$roleInfo['student']==1){ ?>
<li id="naviDepartmentStudent"><a href="<?php echo site_url('department/index') ?>">组织与学员</a></li>
<?php } ?>
<li id="naviDepartmentStudent"><a href="<?php echo site_url('ability/index') ?>">能力模型</a></li>
<li><a href="javascript:;">年度培训计划</a>
    <ul>
        <li>
            <a href="<?php echo site_url('annualsurvey/index') ?>">年度调研</a>
        </li>
        <li>
            <a href="<?php echo site_url('annualplan/index') ?>">培训计划</a>
        </li>
    </ul>
</li>