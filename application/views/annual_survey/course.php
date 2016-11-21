<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css?112101"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript">
    $(document).ready(function () {
        $('.closeBtn').click(function () {
            $('#conWindow').hide();
        });
        $('#addType').click(function () {
            $('#conWindow').show();
            $('#conWindow .comTable').hide().eq(0).show();
            $('#conWindow .title_divText').text('添加课程类型');
        });
        $('input[name=coursetypeselect]').change(function(){
            if($('input[name=coursetypeselect]:checked').val()==1){
                $('#coursetypes').removeAttr('disabled').css({'color':'#666','background-color':'#f8f8f8'});
                $('#coursetypeval').attr('disabled','disabled').css({'color':'#ccc','background-color':'#fff'});
            }else{
                $('#coursetypes').attr('disabled','disabled').css({'color':'#ccc','background-color':'#fff'});
                $('#coursetypeval').removeAttr('disabled').css({'color':'#666','background-color':'#f8f8f8'});
            }
        });
        $('a.okBtn').click(function () {
            $(this).text('请稍后..');
            if($('#conWindow .comTable').eq(0).is(':visible')){
                var coursetypeselect=$('input[name=coursetypeselect]:checked').val();
                if(coursetypeselect==1){
                    var library_type_id=$('#coursetypes').val();
                    if(library_type_id==''){
                        alert('请选择课程类型')
                    }else{
                        $.ajax({
                            type: "post",
                            url: '<?php echo site_url('annualsurvey/courseSelect/'.$survey['id']) ?>',
                            data: {'annual_course_library_type_id': library_type_id},
                            async: false,
                            success: function (res) {
                                if(res==0){
                                    alert('添加失败')
                                }else{
                                    window.location=res;
                                }
                            }
                        });
                    }
                }else{
                    var name=$('#coursetypeval').val();
                    if(name==''){
                        alert('请输入课程类型')
                    }else{
                        $.ajax({
                            type: "post",
                            url: '<?php echo site_url('annualsurvey/courseTypeAdd/'.$survey['id']) ?>',
                            data: {'name': name},
                            async: false,
                            success: function (res) {
                                if (res == 0) {
                                    alert('添加失败');
                                } else {
                                    window.location=res;
                                }
                            }
                        });
                    }
                }
            }else{
                var actiontype=$('#actiontype').val();
                var objid=$('#objid').val();
                var objval=$('#objval').val();
                if($.trim(objval)==''){
                    if(actiontype=='edittype'){
                        alert('请输入类型名称')
                    }else{
                        alert('请输入课程标题')
                    }
                }else{
                    if(actiontype=='edittype'){
                        $.ajax({
                            type: "post",
                            url: '<?php echo site_url('annualsurvey/courseTypeEdit/'.$survey['id']) ?>',
                            data: {'coursetypeid': objid,'name':objval},
                            async: false,
                            success: function (res) {
                                if (res == 0) {
                                    alert('操作失败');
                                } else {
                                    window.location=res;
                                }
                            }
                        });
                    }else{
                        $.ajax({
                            type: "post",
                            url: '<?php echo site_url('annualsurvey/courseSave/'.$survey['id'].'/'.$currentcoursetype['id']) ?>',
                            data: {'courseid': objid,'title':objval},
                            async: false,
                            success: function (res) {
                                if (res == 0) {
                                    alert('操作失败');
                                } else {
                                    window.location=res;
                                }
                            }
                        });
                    }
                }
            }
            return false;
        });
        $('#delType').click(function () {
            return confirm('确认删除并清除该类型下所有课程吗?');
        });
        $('.delCourse').click(function(){
            var title=$(this).parent().prev().prev().text();
            return confirm('确认删除《'+title+'》吗?');
        });
        $('#editType').click(function(){
            $('#conWindow').show();
            $('#conWindow .comTable').hide().eq(1).show();
            $('#actiontype').val('edittype');
            $('#objid').val($(this).attr('rel'));
            $('#objval').val($(this).parent().prev().text());
            $('#conWindow .title_divText').text('编辑类型');
            $('#conWindow .comTable:eq(1) label:eq(0)').text('类型名称');
        });
        $('#addCourse').click(function(){
            $('#conWindow').show();
            $('#conWindow .comTable').hide().eq(1).show();
            $('#actiontype').val('addcourse');
            $('#objid').val('');
            $('#objval').val('');
            $('#conWindow .title_divText').text('添加课程');
            $('#conWindow .comTable:eq(1) label:eq(0)').text('课程标题');
        });
        $('.editCourse').click(function(){
            $('#conWindow').show();
            $('#conWindow .comTable').hide().eq(1).show();
            $('#actiontype').val('editcourse');
            $('#objid').val($(this).attr('rel'));
            $('#objval').val($(this).parent().prev().prev().text());
            $('#conWindow .title_divText').text('编辑课程');
            $('#conWindow .comTable:eq(1) label:eq(0)').text('课程标题');
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <?php $this->load->view ( 'annual_survey/top_tit' ); ?>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_survey/top_navi' ); ?>
    </div>
    <div class="clearfix textureBox">
        <p class="yellowTipBox mt20">请注意您的调研时间,调研问卷开始后,内容不可修改</p>
        <div class="textureSide">
            <?php if(!$isStarted){?><a id="addType" href="javascript:void(0)" class="topbtn">添加课程类型</a><?php } ?>
            <div class="fnavi mb10">
                <a href="<?php echo site_url('annualsurvey/course/' .$survey['id']) ?>"
                   class="flink <?php echo empty($currentcoursetype['id'])?'on':'' ?>">所有课程</a>
            </div>
            <?php foreach ($coursetypes as $t) { ?>
                <div class="fnavi mb10">
                    <a href="<?php echo site_url('annualsurvey/course/' .$survey['id'].'/'.$t['id']) ?>"
                       class="flink <?php echo $currentcoursetype['id'] == $t['id'] ? 'on' : '' ?>"><?php echo $t['name'] ?></a>
                </div>
            <?php } ?>
        </div>
        <div class="textureCont">
            <?php if (!empty($res=='success')) {?>
                <p class="alertBox alert-success"><span class="alert-msg">操作成功</span><a href="javascript:;" class="alert-remove">X</a></p>
            <?php } ?>
            <?php if (!empty($res=='fail')) {?>
                <p class="alertBox alert-danger"><span class="alert-msg">操作失败</span><a href="javascript:;" class="alert-remove">X</a></p>
            <?php } ?>
            <div class="texturetip textureWite clearfix mb10 mr20"><span class="fLeft"><?php if(empty($currentcoursetype)){echo '所有课程';}else{echo $currentcoursetype['name'];}?></span>
                <?php if(!empty($currentcoursetype) && !$isStarted){ ?>
                <div class="fRight">
                    <a id="addCourse" href="#" class="borBlueBtnH28" style="margin-right:0;">添加课程</a>
                    <a id="editType" rel="<?php echo $currentcoursetype['id']; ?> " href="#" class="borBlueBtnH28">编辑类型</a>
                    <a id="delType" href="<?php echo site_url('annualsurvey/delType/' .$survey['id'].'/'.$currentcoursetype['id']) ?>" class="borBlueBtnH28">删除类型</a>
                </div>
                <?php } ?>
            </div>
            <div class="clearfix mr20">
                <p class="clearfix f14 mb20">共有<?php echo $total_rows ?>个课程</p>
                <?php if (!empty($courses)) { ?>
                    <table cellspacing="0" class="listTable">
                        <colgroup>
                            <col width="70%">
                            <col width="15%">
                            <?php if(!$isStarted){?><col width="15%"><?php } ?>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th class="aLeft">课程名称</th>
                            <th class="aLeft">课程类型</th>
                            <?php if(!$isStarted){?><th>操作</th><?php } ?>
                        </tr>
                        <?php foreach ($courses as $c) { ?>
                            <tr>
                                <td><a class="blue"><?php echo $c['title'] ?></a></td>
                                <td><?php echo $c['typename'] ?></td>
                                <?php if(!$isStarted){?><td class="aCenter"><a class="blue editCourse" rel="<?php echo $c['id'] ?>" href="#">编辑</a>
                                        <a class="blue delCourse" href="<?php echo site_url('annualsurvey/delCourse/' .$survey['id'].'/' . $c['id']) ?>">删除</a>
                                </td><?php } ?>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                    <?php echo $links ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!--tankuang de yangshi -->
<div id="conWindow" style="z-index: 99999; display: none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan" class="title_divText">添加课程类型</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <table class="comTable">
                <col width="150"/>
                <tr>
                    <th><label><input type="radio" name="coursetypeselect" value="1" checked class="mr5">从库中导入</label></th>
                    <td class="aLeft">
                        <select id="coursetypes" class="ipt w250">
                            <option value="">请选择</option>
                            <?php foreach ($courselibrarytypes as $ct) { ?>
                                <option value="<?php echo $ct['id']; ?>"><?php echo $ct['name']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label><input type="radio" name="coursetypeselect" value="2" class="mr5">自定义类型</label></th>
                    <td class="aLeft">
                        <input id="coursetypeval" type="text" class="ipt w250" disabled style="color:#ccc;background-color:#fff;"></td>
                </tr>
                <tr>
                    <th></th>
                    <td class="aLeft"><a jsbtn="okBtn" href="javascript:;" class="okBtn">保存</a></td>
                </tr>
            </table>
            <table class="comTable" style="display: none;">
                <col width="150"/>
                <tr>
                    <th><label>添加课程</label></th>
                    <td class="aLeft">
                        <input type="hidden" id="actiontype" value="addcourse" />
                        <input type="hidden" id="objid" value="" />
                        <input type="text" id="objval" class="ipt w345"></td>
                </tr>
                <tr>
                    <th></th>
                    <td class="aLeft"><a jsbtn="okBtn" href="javascript:;" class="okBtn">保存</a></td>
                </tr>
            </table>
        </div>

    </div>
    <div class="popmap" style="z-index: 100000;"></div>
</div>