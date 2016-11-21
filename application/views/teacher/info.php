<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css?112101" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan">讲师介绍</span></div>
        <div class="comBox">
                <p class="opBtn">
<?php if($loginInfo['role']==1||$roleInfo['teacheredit']==1){ ?>
                    <a href="<?php echo site_url('teacher/teacheredit/'.$teacher['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑讲师</a><a href="<?php echo site_url('teacher/teacherdel/'.$teacher['id']);?>" class="delBtn"><i class="fa fa-trash-o fa-lg mr5"></i>删除讲师</a>
<?php } ?>

                <div class="listBox teacherList">
                        <div class="listCont listContGray">
                                <div class="imgBox"><img src="<?php echo !empty($teacher['head_img'])?base_url('uploads/teacher_img/'.$teacher['head_img']):base_url().'images/face_default.png' ?>" alt="" width="160"></div>
                                <div class="listText">
                                        <p class="titp"><?php echo $teacher['name'] ?></p>
                                        <p>师资类型：<?php echo $teacher['type']==2?'外部':'内部' ?></p>
                                        <?php if(!empty($teacher['title'])){?><p>讲师头衔：<?php echo $teacher['title'] ?></p><?php } ?>
            <?php if(!empty($teacher['specialty'])){?><p>擅长类别：<?php echo $teacher['specialty'] ?></p><?php } ?>
            <?php if(!empty($teacher['years'])){?><p>授课年限：<?php echo $teacher['years'] ?></p><?php } ?>
                                        <p>工作形式：<?php echo $teacher['work_type']==2?'兼职':'专职' ?></p>
            <?php if(!empty($teacher['years'])){  if($loginInfo['role']==1||$roleInfo['teacheredit']==1){ ?><p>授课薪酬：<?php echo $teacher['hourly'] ?>元/课时</p><?php } } ?>
                                </div>
                        </div>

                </div>
                <dl class="kecDl">
                        <?php if(!empty($teacher['info'])){ ?>
                        <dt>讲师介绍</dt>
                        <dd class="noborder">
                                <?php echo nl2br($teacher['info']) ?>
                        </dd>
                        <?php } ?>
                </dl>

        </div>
</div>