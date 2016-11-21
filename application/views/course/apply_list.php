<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css?112101" />
<script type="text/javascript">
    $(document).ready(function(){
        $('#filter_status').change(function(){
            var url='<?php echo site_url('course/applylist/'.$course['id'])?>';
            window.location=($(this).val()=='')?url:url+'?applystatus='+$(this).val();
        });
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

                    <?php if($loginInfo['role']==1||$roleInfo['applyset']==1){ ?>
                        <li><a href="<?php echo site_url('course/applyset/'.$course['id']) ?>">报名设置</a></li>
                    <?php } ?>
                    <?php if($loginInfo['role']==1||$roleInfo['applylist']==1){ ?>
                        <li class="cur"><a href="<?php echo site_url('course/applylist/'.$course['id']) ?>">报名名单<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
                    <?php } ?>
                    <?php if($loginInfo['role']==1||$roleInfo['notifyset']==1){ ?>
                        <li ><a href="<?php echo site_url('course/notifyset/'.$course['id']) ?>">通知设置</a></li>
                    <?php } ?>
                </ul>

            </div>
            <div class="contRight">
                <p class="clearfix f14 mb20">
                    <select id="filter_status" class="iptH37 fRight">
                        <option value="">全部</option>
                        <option value="1" <?php if($pargram['applystatus']==1){echo 'selected'; }?>>审核通过</option>
                        <option value="2" <?php if($pargram['applystatus']==2){echo 'selected'; }?>>审核不通过</option>
                    </select>
                    <span class="pt10 fLeft">共有<?php echo $total ?>人报名，<span class="orange"><?php echo $refusetotal ?></span> 人未通过审核</span>
                    <?php if($total>0){ ?>
                        <a href="<?php echo site_url('export/applylist/'.$course['id']) ?>" target="_blank" class="borBlueH37 ml10">导出名单</a>
                    <?php } ?>
                </p>
                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="10%">
                        <col width="10%">
                        <col width="20%">
                        <col width="25%">
                        <col width="10%">
                        <col width="20%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">姓名</th>
                        <th class="aLeft">工号</th>
                        <th class="aLeft">职务</th>
                        <th class="aLeft">部门</th>
                        <th class="aLeft">手机</th>
                        <th class="aLeft">申请原因</th>
                        <th>报名时间</th>
                        <th>状态</th>
                        <th>审核</th>
                    </tr>
                    <?php foreach ($applys as $a){ ?>
                        <tr>
                            <td class="blue"><?php echo $a['name'] ?></td>
                            <td><?php echo $a['job_code'] ?></td>
                            <td class="wordBreak"><?php echo $a['job_name'] ?></td>
                            <td class="wordBreak"><?php echo $a['department'] ?></td>
                            <td><?php echo $a['mobile'] ?></td>
                            <td class="wordBreak"><?php echo $a['note'] ?></td>
                            <td><?php echo date("m-d H:i",strtotime($a['created'])) ?></td>
                            <td class="aCenter"><?php if($a['apply_status']==1){ ?>
                                    <span class="green">审核通过</span>
                                <?php }elseif($a['apply_status']==2){ ?>
                                    <span class="red">审核不通过</span>
                                <?php }else{ ?>
                                    <span class="orange">待审核</span>
                                <?php } ?>
                            </td>
                            <td class="aCenter">
                                <?php if($a['apply_status']!=1){ ?>
                                    <a href="<?php echo site_url('course/applycheck/'.$a['apply_id']).'?status=1' ?>" class="blue">通过</a>
                                <?php }elseif($a['apply_status']==1){ ?>
                                    <a href="<?php echo site_url('course/applycheck/'.$a['apply_id']).'?status=2' ?>" class="blue">不通过</a>
                                <?php } ?>
                            </td>
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