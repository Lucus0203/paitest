<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>

<div class="wrap">
    <div class="titCom clearfix">
        <?php $this->load->view ( 'annual_survey/top_tit' ); ?>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_survey/top_navi' ); ?>
    </div>
    <div class="clearfix textureBox p15">
        <p class="clearfix f14 mb20">共有<?php echo $total_rows ?>个人提交的调研问卷</p>
        <?php if (!empty($students)) { ?>
            <table cellspacing="0" class="listTable">
                <colgroup>
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                </colgroup>
                <tbody>
                <tr>
                    <th>姓名</th>
                    <th>工号</th>
                    <th>职务</th>
                    <th>部门</th>
                    <th>手机</th>
                    <th>提交时间</th>
                    <th>操作</th>
                </tr>
                <?php foreach ($students as $s){ ?>
                    <tr>
                        <td class="aCenter blue"><?php echo $s['name'] ?></td>
                        <td class="aCenter"><?php echo $s['job_code'] ?></td>
                        <td class="aCenter wordBreak"><?php echo $s['job_name'] ?></td>
                        <td class="aCenter wordBreak"><?php echo $s['department'] ?></td>
                        <td class="aCenter"><?php echo $s['mobile'] ?></td>
                        <td class="aCenter"><?php echo date("m-d H:i",strtotime($s['created'])) ?></td>
                        <td class="aCenter">
                            <a href="<?php echo site_url('annualsurvey/answerdetail/'.$s['answer_id']) ?>" class="blue" target="_blank">查看问卷</a>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
            <?php echo $links ?>
        <?php } ?>
    </div>
</div>