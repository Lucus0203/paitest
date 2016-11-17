<script type="text/javascript">
$(document).ready(function(){
    $(function(){$('.shareBtn').click(function(){return confirm('确定发布吗?');});});
    $('.listBox .listCont .imgBox').each(function(){
        $(this).height($(this).next().height());
    })
});
</script>
<div class="wrap">
			<div class="comBox">
				<div class="topNavi">
<?php if($loginInfo['role']==1||$roleInfo['coursecreate']==1){ ?>
                                    <a href="<?php echo site_url('course/coursecreate') ?>" class="fRight borBlueH37">创建新课程</a>
<?php } ?>
					<ul class="topNaviUl">
                                                <li <?php if(empty($parm['status'])){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist') ?>">全部课程</a></li>
                                                <li <?php if($parm['status']==4){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=4' ?>">待发布</a></li>
						<li <?php if($parm['status']==5){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=5' ?>">报名未开始</a></li>
						<li <?php if($parm['status']==1){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=1' ?>">报名中</a></li>
						<li <?php if($parm['status']==2){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=2' ?>">进行中</a></li>
						<li <?php if($parm['status']==3){ ?>class="cur"<?php } ?>><a href="<?php echo site_url('course/courselist').'?status=3' ?>">已结束</a></li>
					</ul>

				</div>

				<div class="seachBox clearfix borderTop">
                    <form method="get" action="">
                        <ul>
                            <li class="w250 mr60">
                                <input name="keyword" type="text" value="<?php echo $parm['keyword'] ?>" class="ipt w250" placeholder="关键字">
                            </li>
                            <li class="w496 btn"><span class="mr20">开课时间</span><input name="time_start" type="text" value="<?php echo $parm['time_start'] ?>" class="ipt w156 mr10 DTdate" autocomplete="off">至
                                <input name="time_end" type="text" value="<?php echo $parm['time_end'] ?>" class="ipt w156 ml10 DTdate" autocomplete="off">
                            </li>

                            <li class="btn fRight"><input type="submit" class="borBlueH37 mt3" value="搜索" /></li>
                        </ul>
                    </form>
				</div>
				<div class="listBox">
					<?php if(count($courses)>0){?>
						<?php foreach ($courses as $c){ ?>
							<div class="listCont">
								<p class="operaBtn">
								<?php if($loginInfo['role']==1||$roleInfo['courseedit']==1){ ?><a href="<?php echo site_url('course/courseedit/'.$c['id']);?>" class="editBtn"><i class="fa fa-edit fa-lg mr5"></i>编辑</a><a href="<?php echo site_url('course/coursedel/'.$c['id']);?>" class="delBtn"><i class="fa fa-trash-o fa-lg mr5"></i>删除</a><?php } ?><?php if($c['status']==4){ ?><?php if(empty($c['time_start'])||empty($c['time_end'])||empty($c['address'])){?><a href="<?php echo site_url('course/courseedit/'.$c['id']); ?>"><i class="fa fa-link fa-lg mr5"></i>发布</a><?php }else{ ?><a href="<?php echo site_url('course/coursepublic/'.$c['id']); ?>" class="shareBtn"><i class="fa fa-link fa-lg mr5"></i>发布</a><?php } ?>

                                    <?php } ?></p>
								<div class="imgBox"><span class="helper"></span><a href="<?php echo site_url('course/courseinfo/'.$c['id']);?>"><img src="<?php echo empty($c['page_img'])?base_url().'images/course_default_img.jpg':base_url('uploads/course_img/'.$c['page_img']) ?>" alt="" width="160"></a></div>
								<div class="listText">
									<p class="titp"><a class="blue" href="<?php echo site_url('course/courseinfo/'.$c['id']);?>"><?php echo $c['title'] ?></a><?php if(empty($c['time_start'])||empty($c['time_end'])||empty($c['address'])){echo '<span class="orange">(未完善)</span>';} ?></p>
									<p class="titp">
										<?php if($c['status']==1){//1报名中2进行中3结束4待发布5待开启报名9其他 ?>
											<span class="greenH25">报名中</span>
										<?php }elseif($c['status']==2){ ?>
											<span class="greenH25">进行中</span>
										<?php }elseif($c['status']==3){ ?>
											<span class="grayH25">已结束</span>
										<?php }elseif($c['status']==4){ ?>
											<span class="orangeH25">待发布</span>
										<?php }elseif($c['status']==5){ ?>
											<span class="orangeH25">报名未开始</span>
										<?php } ?>
									</p>
									<?php if(!empty($c['teacher'])){ ?>
									<p>课程讲师：<a class="blue" href="<?php echo site_url('teacher/teacherinfo/'.$c['teacher_id']); ?>"><?php echo $c['teacher'] ?></a> </p><?php } ?>
                                <?php if(!empty($c['time_start'])&&!empty($c['time_end'])){ ?>
									<p><span class="mr30">开课时间：<?php echo date('Y-m-d H:i',strtotime($c['time_start'])) ?>&nbsp;至&nbsp;<?php echo date('Y-m-d H:i',strtotime($c['time_end'])) ?></span><?php echo !empty($c['time_endregister_end'])?'报名截止：'.$c['time_endregister_end']:'' ?></p>
                                <?php } ?>
                                <?php if(!empty($c['address'])){ ?>
									<p>开课地点：<?php echo mb_strlen($c['address'],'utf-8')>30?mb_substr($c['address'],0,30,'utf-8').'……':mb_substr($c['address'],0,30,'utf-8') ?></p>
                                <?php } ?>
									<?php if(!empty($c['info'])){ ?><p>课程介绍：<?php echo mb_strlen($c['info'],'utf-8')>30?mb_substr($c['info'],0,30,'utf-8').'……':mb_substr($c['info'],0,30,'utf-8') ?></p><?php } ?>
								</div>
							</div>
						<?php } ?>
					<?php }else{
						echo '<div class="listCont"><div class="listText"><p>暂无符合条件的课程</p></div></div>';
					} ?>
				</div>
                <div class="pageNavi">
					<?php echo $links ?>
				</div>
			</div>
		</div>