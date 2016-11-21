<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css?112101" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['homeworkedit']==1){ ?>
                                        <li><a href="<?php echo site_url('course/homeworkedit/'.$course['id']) ?>">作业编辑</a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['homeworklist']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/homeworklist/'.$course['id']) ?>">提交名单<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                                <p class="clearfix f14 mb20">共有<?php echo $total ?>人提交了课前作业</p>
                                <table cellspacing="0" class="listTable">
                                        <tbody>
                                                <tr>
                                                        <th class="aLeft">姓名</th>
                                                        <th class="aLeft">工号</th>
                                                        <th class="aLeft">职务</th>
                                                        <th class="aLeft">部门</th>
                                                        <th class="aLeft">手机</th>
                                                        <th>提交时间</th>
                                                        <th>操作</th>
                                                </tr>
                                                <?php foreach ($homeworklist as $h) { ?>
                                                <tr>
                                                        <td class="blue"><?php echo $h['name'] ?></td>
                                                        <td><?php echo $h['job_code'] ?></td>
                                                        <td class="wordBreak"><?php echo $h['job_name'] ?></td>
                                                        <td class="wordBreak"><?php echo $h['department'] ?></td>
                                                        <td><?php echo $h['mobile'] ?></td>
                                                        <td class="aCenter"><?php echo date("m-d H:i",strtotime($h['created'])) ?></td>
                                                        <td class="aCenter"><a href="<?php echo site_url('course/homeworkdetail/'.$h['course_id'].'/'.$h['student_id']) ?>" class="blue" target="_blank">查看作业</a></td>
                                                </tr>
                                                <?php } ?>

                                        </tbody>
                                </table>

                                <div class="pageNavi">
                                        <?php echo $links ?>
                                </div>
                        </div>

                </div>

        </div>
</div>