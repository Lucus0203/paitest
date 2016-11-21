<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css?112101" />
<script type="text/javascript">
    $(function(){$('.shareBtn').click(function(){return confirm('确定发布吗?');});});
</script>
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><?php if(empty($course['time_start'])||empty($course['time_end'])||empty($course['address'])){echo '<span class="orange ml20">未完善</span>';}?><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>

        </div>
        <div class="comBox">
                <p class="opBtn">
<?php if($loginInfo['role']==1||$roleInfo['courseedit']==1){ ?>
                    <a href="<?php echo site_url('course/courseedit/'.$course['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑课程</a><a href="<?php echo site_url('course/coursedel/'.$course['id']);?>" class="delBtn"><i class="fa fa-trash-o fa-lg mr5"></i>删除课程</a>
<?php } ?><?php if($course['ispublic']!=1){ ?><?php if(empty($course['time_start'])||empty($course['time_end'])||empty($course['address'])){?><a href="<?php echo site_url('course/courseedit/'.$course['id']); ?>"><i class="fa fa-link fa-lg mr5"></i>发布</a><?php }else{ ?><a href="<?php echo site_url('course/coursepublic/'.$course['id']); ?>" class="shareBtn"><i class="fa fa-link fa-lg mr5"></i>发布</a><?php }} ?>
                </p>

                <div class="listBox">
                        <div class="listCont listContGray">
                            <div class="imgBox"><img src="<?php echo empty($course['page_img'])?base_url().'images/course_default_img.jpg':base_url('uploads/course_img/'.$course['page_img']) ?>" alt="" width="160"></div>
                                <div class="listText">
                                        <p class="titp"><?php echo $course['title'] ?></p>
                                        <p>开课时间：<?php echo $course['time_start'] ?> 至 <?php echo $course['time_end'] ?></p>
                                        <p>课程地点：<?php echo $course['address'] ?></p>
                                        <p>课程讲师：<a href="<?php echo site_url('teacher/teacherinfo/'.$teacher['id']) ?>" class="blue"><?php echo $teacher['name'] ?></a></span></p>

                                        <p>培训学员：<?php echo $course['target'] ?></p>
                                </div>
                        </div>

                </div>
                <dl class="kecDl">
                        <?php if(!empty($course['info'])){ ?>
                        <dt>课程介绍</dt>
                        <dd>
                                <?php echo nl2br($course['info']) ?>
                        </dd>
                        <?php } ?>
                        <?php if(!empty($course['income'])){ ?>
                        <dt>课程收益</dt>
                        <dd><?php echo nl2br($course['income']) ?></dd>
                        <?php } ?>
                        <dt>课程大纲</dt>
                        <dd class="noborder">
                                <?php echo nl2br($course['outline']) ?>
                        </dd>
                </dl>

        </div>
</div>