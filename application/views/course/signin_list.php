<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<script>
    function windowReload(){
        window.location.reload();
        setTimeout("windowReload()",1000*30);
    }
    $(function(){
        setTimeout("windowReload()",1000*30);
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
    <div class="topNaviKec">
        <?php $this->load->view ( 'course/top_navi' ); ?>
    </div>
    <div class="comBox clearfix">
        <div class="baoming">

            <div class="sideLeft">
                <ul class="sideLnavi">
                    <?php if($loginInfo['role']==1||$roleInfo['signinset']==1){ ?>
                        <li><a href="<?php echo site_url('course/signinset/'.$course['id']) ?>">签到设置</a></li>
                    <?php } ?>
                    <?php if($loginInfo['role']==1||$roleInfo['signinlist']==1){ ?>
                        <li class="cur"><a href="<?php echo site_url('course/signinlist/'.$course['id']) ?>">签到名单<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
                    <?php } ?>
                </ul>

            </div>
            <div class="contRight">
                <p class="clearfix f14 mb20">
                    <span class="pt10 fLeft">共有<?php echo $signin_count ?>人签到，<span class="orange"><?php echo $signout_count ?></span> 人签退</span>
                    <?php if($signin_count>0){ ?>
                        <a href="<?php echo site_url('export/signinlist/'.$course['id']) ?>" target="_blank" class="borBlueH37 ml10">导出名单</a>
                    <?php } ?>
                </p>
                <table cellspacing="0" class="listTable">
                    <tbody>
                    <tr>
                        <th class="aLeft">姓名</th>
                        <th class="aLeft">工号</th>
                        <th class="aLeft">职务</th>
                        <th class="aLeft">部门</th>
                        <th class="aLeft">手机</th>
                        <th>签到时间</th>
                        <th>签退时间</th>
                    </tr>
                    <?php foreach ($siginlist as $h) { ?>
                        <tr>
                            <td class="blue"><?php echo $h['name'] ?></td>
                            <td><?php echo $h['job_code'] ?></td>
                            <td class="wordBreak"><?php echo $h['job_name'] ?></td>
                            <td class="wordBreak"><?php echo $h['department'] ?></td>
                            <td><?php echo $h['mobile'] ?></td>
                            <td class="aCenter"><?php echo date("m-d H:i",strtotime($h['signin_time'])) ?></td>
                            <td class="aCenter"><?php echo !empty($h['signout_time'])?date("m-d H:i",strtotime($h['signout_time'])):'' ?></td>
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