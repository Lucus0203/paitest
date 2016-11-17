<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<div class="wrap">
    <div class="textureCont width100">

        <div class="texturetip p2015 clearfix"><span class="fLeft pt5"><?php echo $abilityjob['name'] ?>评估</span>
            <div class="fRight">
                <a class="borBlueH37" href="<?php echo site_url('ability/index') ?>">返回</a>
            </div>
        </div>

        <div class="p15">
            <p class="clearfix f14 mb20">共<?php echo $total_rows ?>评估学员</p>
            <table cellspacing="0" class="listTable">
                <colgroup>
                <col width="20%">
                <col width="30%">
                <col width="20%">
                <col width="10%">
                </colgroup>
                <tbody>
                <tr>
                    <th class="center">学员</th>
                    <th class="center">部门</th>
                    <th class="center">评分</th>
                    <th class="center">操作</th>

                </tr>
                <?php foreach ($students as $s) { ?>
                    <tr>
                        <td class="aCenter"><?php if(!empty($s['point'])){?><a class="blue" href="<?php echo site_url('ability/targetdetail/'.$abilityjob['id'].'/'.$s['id']) ?>" ><?php echo $s['name'] ?></a> <?php }else{?><?php echo $s['name'] ?><?php } ?> </td>
                        <td class="aCenter">
                            <?php echo $s['parent_department_name'] ?> <?php echo $s['department_name'] ?>
                        </td>
                        <td class="aCenter">
                            <?php echo !empty($s['point'])?$s['point']:'未提交' ?>
                        </td>
                        <td class="aCenter">
                            <?php if(!empty($s['point'])){ ?>
                                <a href="<?php echo site_url('ability/targetdetail/'.$abilityjob['id'].'/'.$s['id']) ?>" class="blue addTarget">查看详细</a>
                            <?php }else{ ?>
                                未提交
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