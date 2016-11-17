<script type="text/javascript">
    $(document).ready(function(){
        $('.listBox .listCont .imgBox').each(function(){
            $(this).height($(this).next().find('.listText').height());
        })
    });
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<div class="wrap">
			<div class="comBox">
				<div class="texturetip clearfix p1524"><span class="fLeft pt5">讲师列表</span>
					<div class="fRight">
						<a class="borBlueH37 aCenter" href="<?php echo site_url('teacher/teachercreate') ?>">创建讲师</a>
					</div>
				</div>
				<div class="seachBox clearfix">
                    <form method="get" action="">
					<ul>
						<li>
                            <input name="keyword" value="<?php echo $parm['keyword'] ?>" type="text" class="ipt" placeholder="关键字">
						</li>
						<li>
                                                        <select name="type" class="ipt">
								<option value="">师资类型</option>
                                                                <option value="1" <?php if($parm['type']==1){ ?>selected=""<?php } ?>>内部</option>
								<option value="2" <?php if($parm['type']==2){ ?>selected=""<?php } ?>>外部</option>
							</select>
						</li>
						<li>
                                                    <input name="specialty" value="<?php echo $parm['specialty'] ?>" type="text" class="ipt" placeholder="擅长类型">
						</li>
						<li>
                                                        <select name="work_type" class="ipt">
								<option value="">工作形式</option>
								<option value="1" <?php if($parm['work_type']==1){ ?>selected=""<?php } ?>>专职</option>
								<option value="2" <?php if($parm['work_type']==2){ ?>selected=""<?php } ?>>兼职</option>
							</select>
						</li>
                                                <li class="btn fRight"><input type="submit" value="搜索" class="borBlueH37 mt3" /></li>
					</ul>
                                        </form>
				</div>
				<div class="listBox teacherList">
                                    <?php foreach ($teachers as $t) { ?>
					<div class="listCont">
                                                <?php if($loginInfo['role']==1||$roleInfo['teacheredit']==1){ ?>
						<p class="operaBtn">
                                                    <a href="<?php echo site_url('teacher/teacheredit/'.$t['id']); ?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑</a><a href="<?php echo site_url('teacher/teacherdel/'.$t['id']);?>" class="delBtn"><i class="fa fa-trash-o fa-lg mr5"></i>删除</a></p>
                                                <?php } ?>

                                                <div class="imgBox">
                                                    <span class="helper"></span>
														<a href="<?php echo site_url('teacher/teacherinfo/'.$t['id']); ?>"><img src="<?php echo !empty($t['head_img'])?base_url('uploads/teacher_img/'.$t['head_img']):base_url().'images/face_default.png' ?>" alt="" width="110"></a>
												</div>
						<a class="blue" href="<?php echo site_url('teacher/teacherinfo/'.$t['id']); ?>">
							<div class="listText"><p class="titp blue"><?php echo $t['name'] ?></p>
							<p><span class="mr30">师资类型：<?php echo $t['type']==1?'内部':'外部' ?></span><?php if(!empty($t['specialty'])){ ?><span class="mr30">擅长类别：<?php echo $t['specialty'] ?></span><?php } ?><?php if(!empty($t['years'])){ ?>授课年限：<?php echo $t['years'] ?>年<?php } ?></p>
							<?php if(!empty($t['info'])){ ?><p>讲师介绍：<?php echo mb_strlen($t['info'],'utf-8')>30?mb_substr($t['info'],0,30,'utf-8').'……':mb_substr($t['info'],0,30,'utf-8') ?></p><?php } ?>
							</div>
						</a>
					</div>
                                    <?php } ?>
				</div>
                                <div class="pageNavi">
					<?php echo $links ?>
				</div>
			</div>
		</div>