<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?112101"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript"  src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#open_status').change(function(){
            var url='<?php echo site_url('annualplan/course/'.$plan['id'])?>';
            window.location=url+'?typeid='+$('#typeid').val()+'&openstatus='+$('#open_status').val();
        });
        $('#typeid').change(function(){
            var url='<?php echo site_url('annualplan/course/'.$plan['id'])?>';
            window.location=url+'?typeid='+$('#typeid').val()+'&openstatus='+$('#open_status').val();
        });
        $('a.cancel').click(function(){
            return confirm('确定取消开课?');
        });
        $('#syncourse').click(function(){
            var flag=true;
            <?php if($total_syncoursed>0){?>
            flag=confirm('此操作将更新课程管理中的课程信息,确认同步吗?');
            <?php } ?>
            if(flag){
                $(this).text('同步中,请稍后..');
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/syncourse/'.$plan['id']) ?>',
                    async: false,
                    success: function (res) {
                        $('#syncoursepause,.alert-success').show();
                        setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                        $('#syncourse').text('开启课程同步').hide();
                        $('#total_syncoursed_opened').val(res);
                        resetclearsyncoursebtn();
                    }
                });
            }
            return false;
        });
        $('#syncoursepause').click(function(){
            $(this).text('暂停同步中,请稍后..');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('annualplan/syncoursepause/'.$plan['id']) ?>',
                async: false,
                success: function (res) {
                    $('#syncourse,.alert-success').show();
                    setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                    $('#syncoursepause').text('暂停课程同步').hide();
                    $('#total_syncoursed_opened').val(res);
                    resetclearsyncoursebtn();
                }
            });
        });
        $('a.approvedstart').click(function(){
            var msg='确认开启审核并通知吗?';
            if(confirm(msg)){
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/approvedstart/'.$plan['id']) ?>',
                    async: false,
                    dataType : 'json',
                    success: function (res) {
                        if (res.err=='approvaling'){
                            $('.alert-danger').html('<span class="alert-msg">'+res.msg+'</span><a href="javascript:;" class="alert-remove">X</a>').show();
                        }else if (res.err*1 > 0) {
                            var department='';
                            $.each(res.department,function(i,item){
                                department+=item.name+'、';
                            });
                            var href='<?php echo base_url() ?>department/index/'+res.department[0].department_id+'.html';
                            $('.alert-danger').html('<span class="alert-msg">以下部门尚未指定部门经理<a href="'+href+'" class="departmentUrl blue ml20">去完善</a></span><a href="javascript:;" class="alert-remove">X</a><br><br><span class="alert-msg black department">'+department+'</span>').show();
                        }else{
                            $('.alert-danger,a.approvedstart').hide();
                            $('a.approvedpause,.alert-success').show();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                        }
                    }
                });
            }
            return false;
        });
        $('.approvedpause').click(function(){
            if(confirm('确定要暂停审核吗?')){
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/approvedpause/'.$plan['id']) ?>',
                    async: false,
                    dataType : 'json',
                    success: function (res) {
                        if (res==1) {
                            $('a.approvedpause').hide();
                            $('a.approvedstart,.alert-success').show();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                        }
                    }
                });
            }
            return false;
        });
        $('#clearsyncourse').click(function(){
            if($('#total_syncoursed_opened').val()*1 <= 0){
                return false;
            }
            if(confirm('确定清除同步课程吗?')){
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('annualplan/cancelsyncourse/'.$plan['id']) ?>',
                    async: false,
                    dataType : 'json',
                    success: function (res) {
                        if (res==1) {
                            $('#syncourse,.alert-success').show();
                            $('#syncoursepause').hide();
                            setTimeout(function(){$('.alert-success').fadeOut(500);},2000);
                            $('#clearsyncourse').css({'border':'none','background-color':'#ccc','color':'#fff'});
                        }
                    }
                });
            }
            return false;
        });
        $(document).tooltip();
        clearTimeout(alertBoxTimeSet);
        function resetclearsyncoursebtn(){
            if($('#total_syncoursed_opened').val()*1 > 0){
                $('#clearsyncourse').removeAttr('style');
            }else{
                $('#clearsyncourse').css({'border':'none','background-color':'#ccc','color':'#fff'});
            }
        }
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $plan['title'] ?></span>
    </div>
    <p style="display: none;" class="alertBox alert-danger mb20">
        <span class="alert-msg">以下部门尚未指定部门经理<a href="#" class="departmentUrl blue ml20">去完善</a></span>
        <a href="javascript:;" class="alert-remove">X</a>
        <br><br><span class="alert-msg black department"></span>
    </p>
    <p style="display: none;" class="alertBox alert-success mb20">
        <span class="alert-msg">操作成功</span>
        <a href="javascript:;" class="alert-remove">X</a>
    </p>
    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_plan/top_navi' ); ?>
        <ul class="fRight proPrint">
            <li>
                <a href="#" <?php if($plan['approval_status']!=1){ ?>style="display: none;" <?php } ?> class="approvedpause borBlueH37 f13" title="暂停员工经理将无法审核学员选课" >暂停审核</a>
                <a href="#" <?php if($plan['approval_status']==1){ ?>style="display: none;" <?php } ?> class="approvedstart borBlueH37 f13" title="开启员工经理将收到学员选课通知" >开启审核并通知</a>
            </li>
            <li>
                <a id="syncoursepause" <?php if($plan['syn_status']!=1){ ?>style="display: none" <?php } ?> href="#" class="borBlueH37 f13" title="暂停同步课程将不会自动添加到课程管理中" >暂停课程同步</a>
                <a id="syncourse" <?php if($plan['syn_status']==1){ ?>style="display: none" <?php } ?> href="#" class="borBlueH37 f13" title="开启课程同步将自动添加到课程管理中" >开启课程同步</a>
            </li>
            <li>
                <a id="clearsyncourse" <?php if($total_syncoursed_opened<=0){ ?>style="border: none;background-color: #ccc; color:#fff;"<?php } ?> href="#" class="borBlueH37 f13" title="将清除课程管理中该计划被同步的课程" >清除同步课程</a>
                <input type="hidden" id="total_syncoursed_opened" value="<?php echo $total_syncoursed_opened ?>" />
            </li>
        </ul>
    </div>

    <div class="clearfix textureBox">
        <div class="p15">

            <div class="clearfix">
                <p class="clearfix f14 mb20">共有<?php echo $total ?>个课程,其中<?php echo $total_open ?>个开课
                    <select id="open_status" class="iptH37 fRight">
                        <option value="">全部状态</option>
                        <option value="1" <?php if($parm['openstatus']==1){?>selected<?php } ?> >已开设</option>
                        <option value="2" <?php if($parm['openstatus']==2){?>selected<?php } ?> >未开设</option>
                    </select>
                    <select id="typeid" class="iptH37 fRight mr10">
                        <option value="">全部类型</option>
                        <?php foreach ($typies as $t){?>
                            <option value="<?php echo $t['id']?>" <?php if($parm['typeid']==$t['id']){?>selected<?php } ?> ><?php echo $t['name']?></option>
                        <?php } ?>
                    </select>
                </p>

                <table cellspacing="0" class="listTable">
                    <colgroup>
                        <col width="40%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="5%">
                        <col width="5%">
                        <col width="15%">

                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="aLeft">课程名称</th>
                        <th>课程类型</th>
                        <th>课程预算</th>
                        <th>课时</th>
                        <th>选课人数</th>
                        <th>通过名单</th>
                        <th>操作</th>

                    </tr>
                    <?php foreach ($courses as $c){ ?>
                    <tr>
                        <td class="wordBreak"><?php if($c['openstatus']==1){?><a href="<?php echo site_url('annualplan/opencourse/'.$plan['id'].'/'.$c['id'])?>" class="blue mr10"><?php echo !empty($c['title'])?$c['title']:$c['course_title'] ?><i class="fa fa-edit fa-lg ml10"></i></a><?php }else{ echo !empty($c['title'])?$c['title']:$c['course_title']; } ?></td>
                        <td class="aCenter"><?php echo $c['type_name'] ?></td>
                        <td class="aCenter"><?php echo !empty($c['price'])?$c['price']:'未填写' ?></td>
                        <td class="aCenter"><?php echo !empty($c['day'])?$c['day']:'未填写' ?></td>
                        <td class="aCenter"><?php echo round($c['num']) ?></td>
                        <td class="aCenter"><?php echo round($c['list_num']) ?></td>
                        <td class="aCenter">
                            <?php if($c['openstatus']==1){?>
                                <a href="<?php echo site_url('annualplan/courselist/'.$plan['id'].'/'.$c['id']); ?>" class="blue mr10">审核</a><a href="<?php echo site_url('annualplan/closecourse/'.$plan['id'].'/'.$c['id'])?>" class="blue cancel">取消</a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('annualplan/opencourse/'.$plan['id'].'/'.$c['id'])?>" class="blue">开课</a>
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