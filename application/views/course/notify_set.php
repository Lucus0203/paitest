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
                                        <?php if($loginInfo['role']==1||$roleInfo['applyset']==1){ ?>
                                                <li><a href="<?php echo site_url('course/applyset/'.$course['id']) ?>">报名设置<i></i></a></li>
                                        <?php } ?>
                                        <?php if($loginInfo['role']==1||$roleInfo['applylist']==1){ ?>
                                                <li><a href="<?php echo site_url('course/applylist/'.$course['id']) ?>">报名名单<i></i></a></li>
                                        <?php } ?>
                                        <?php if($loginInfo['role']==1||$roleInfo['notifyset']==1){ ?>
                                                <li class="cur"><a href="<?php echo site_url('course/notifyset/'.$course['id']) ?>">通知设置<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
                                        <?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                        <form id="editForm" method="post" action="">
                            <input name="act" type="hidden" value="act" />
                        <table cellspacing="0" class="comTable">
                                <colgroup><col width="100">
                                </colgroup><tbody><tr>
                                        <th>自动通知</th>
                                        <td>
                                                <ul class="lineUl">
                                                        <li>
                                                            <label><input name="isnotice_open" checked="" value="1" type="radio">开启</label></li>
                                                        <li>
                                                            <label><input name="isnotice_open" <?php if($course['isnotice_open']==2){echo 'checked="checked"';} ?> value="2" type="radio">关闭</label></li>
                                                </ul>

                                        </td>
                                </tr>
                                <tr>
                                        <th>通知渠道</th>
                                        <td>
                                        <ul class="lineUl">
                                                        <li>
                                                            <label><input name="notice_type_msg" <?php if($course['notice_type_msg']==1){echo 'checked="checked"';} ?>  value="1" type="checkbox">短信通知</label></li>
                                                        <li>
                                                            <label><input name="notice_type_email" <?php if($course['notice_type_email']==1){echo 'checked="checked"';} ?> value="1" type="checkbox">邮件通知</label></li>                                                                        <li><label><input name="notice_type_wx" <?php if($course['notice_type_wx']==1){echo 'checked="checked"';} ?> value="1" type="checkbox">微信通知(需要学员授权微信服务号)</label></li>
                                                </ul>
                                        </td>
                                </tr>
                                <tr>
                                        <th></th>
                                        <td>
                                                <input type="submit" class="coBtn" value="保存设置">
                                        </td>
                                </tr>
                        </tbody></table>
                        </form>
                </div>
                </div>

        </div>
</div>