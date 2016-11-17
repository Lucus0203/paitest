<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/print.css"/>
<script>
    $(document).ready(function(){
        $('.teacherInfoToggle').click(function(){
            var obj=$(this).parent().parent();
            if(obj.find('.teacherInfo').eq(0).is(':hidden')){
                obj.find('.teacherInfo').eq(0).show().next().hide();
            }else{
                obj.find('.teacherInfo').eq(0).hide().next().show();
            }
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $plan['title'] ?> </span>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_plan/top_navi' ); ?>
        <ul class="fRight proPrint">
            <li>
                <a href="<?php echo site_url('export/exportplan/'.$plan['id']) ?>" target="_blank" class="blue"><i class="fa fa-file-excel-o fa-lg mr5"></i>导出</a>
            </li>
            <li>
                <a href="javascript:window.print();" class="blue"><i class="fa fa-print fa-lg mr5"></i>打印</a>
            </li>
        </ul>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">

            <div class="clearfix">
                <p class="f24 aCenter mb20"><?php echo $plan['title'] ?></p>
                <div class="mb20">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th colspan="5"  class="blueTxt">总览</th>
                        </tr>
                        <tr>
                            <th>课程类型</th>
                            <th>开课数量</th>
                            <th>培训人次</th>
                            <th>培训预算</th>
                        </tr>
                        <?php $count_total=0;$people_total=0;$price_total=0;
                        foreach ($res as $r){ ?>
                            <tr>
                                <td><?php echo $r['name'] ?></td>
                                <td><?php echo round($r['total']['count_num']);$count_total+=$r['total']['count_num']; ?></td>
                                <td><?php echo round($r['total']['people_num']);$people_total+=$r['total']['people_num']; ?></td>
                                <td><?php echo round($r['total']['price_num']);$price_total+=$r['total']['price_num']; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td style="border-bottom: none;">全部</td>
                            <td style="border-bottom: none;"><?php echo round($count_total) ?></td>
                            <td style="border-bottom: none;"><?php echo round($people_total) ?></td>
                            <td style="border-bottom: none;"><?php echo round($price_total) ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <?php foreach ($res as $r){
                    if($r['total']['count_num']>0){ ?>
                <div class="mb20">
                    <table class="tableC">
                        <colgroup>
                            <col width="19%">
                            <col width="28%">
                            <col width="10%">
                            <col width="10%">
                            <col width="7%">
                            <col width="7%">
                            <col width="7%">
                            <col width="7%">
                            <col width="5%">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="9"  class="blueTxt"><?php echo $r['name'] ?></th>
                        </tr>
                        <tr>
                            <th>课程名称</th>
                            <th>课程介绍</th>
                            <th>内训/外训</th>
                            <th>供应商</th>
                            <th>讲师</th>
                            <th>课时</th>
                            <th>人次</th>
                            <th>预算</th>
                            <th>时间</th>
                        </tr>
                        <?php foreach ($r['courses'] as $c){
                            $bbno=($c===end($r['courses']))?'style="border-bottom: none;"':''; ?>
                        <tr>
                            <td class="aLeft" <?php echo $bbno ?> ><?php echo $c['title'] ?></td>
                            <td class="aLeft" <?php echo $bbno ?>><?php echo nl2br($c['info']) ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['external']==1?'外训':'内训' ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['supplier'] ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['teacher'] ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['day'] ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['people'] ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['price'] ?></td>
                            <td <?php echo $bbno ?>><?php echo $c['year'].'.'.$c['month'] ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php }
                } ?>

                <?php if(count($teachers)>0){ ?>
                <div class="mb20">
                    <table class="tableC">
                        <colgroup>
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="70%">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="4" class="blueTxt">讲师介绍</th>
                        </tr>
                        <tr>
                            <th>讲师</th>
                            <th>工作形式</th>
                            <th>工作年限</th>
                            <th>简介</th>

                        </tr>
                        <?php foreach ($teachers as $t){
                            $bbno=($t===end($teachers))?'style="border-bottom: none;"':'';?>
                        <tr>
                            <td <?php echo $bbno ?>><?php echo $t['name'] ?></td>
                            <td <?php echo $bbno ?>><?php echo $t['work_type']==1?'专职':'兼职'; ?></td>
                            <td <?php echo $bbno ?>><?php echo !empty($t['years'])?$t['years'].'年':'' ?></td>
                            <td class="aLeft" <?php echo $bbno ?>><p class="teacherInfo"><?php echo mb_strlen($t['info'],'utf-8')>30?mb_substr($t['info'],0,30,'utf-8').'……'.'<a href="#" class="teacherInfoToggle blue">查看更多</a>':mb_substr($t['info'],0,30,'utf-8') ?></p><p class="teacherInfo" style="display: none;"><?php echo nl2br($t['info']) ?><br/><a href="#" class="teacherInfoToggle blue fRight mr10">收起内容</a></p></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
                <?php if(!empty($plan['note'])){ ?>
                <div class="mb20">
                    <table class="tableC">
                        <tbody>
                        <tr>
                            <th class="blueTxt">备注</th>
                        </tr>
                        <tr>
                            <td class="aLeft p15 f16" style="border-bottom: none;"><?php echo nl2br($plan['note']); ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <?php } ?>

            </div>

        </div>
    </div>
</div>