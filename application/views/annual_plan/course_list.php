<script type="text/javascript">
    $(document).ready(function () {
        $('#parent_department').change(function(){
            var url='<?php echo site_url('annualplan/courselist/'.$plan['id'].'/'.$course['annual_course_id'])?>';
            window.location=url+'?parent_department='+$('#parent_department').val()+'&department='+$('#department').val();
        });
        $('#department').change(function(){
            var url='<?php echo site_url('annualplan/courselist/'.$plan['id'].'/'.$course['annual_course_id'])?>';
            window.location=url+'?parent_department='+$('#parent_department').val()+'&department='+$('#department').val();
        });
        //选择对象
        $('#addTarget').click(function () {
            $('#conWindow').show();
            resetconWindow();
            return false;
        });
        $('#popConClose,a.okBtn').click(function () {
            $('#conWindow').hide();
            return false;
        });
        $('.deparone').click(function () {
            $(this).addClass('secIpt').siblings().removeClass('secIpt');
            var ischecked = $(this).find('input').is(':checked');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('department/ajaxDepartmentAndStudent') ?>',
                data: {'departmentid': $(this).find('input').val()},
                datatype: 'jsonp',
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    var count = 0;
                    var str = '';
                    $.each(json_obj.departs, function (i, item) {
                        var secIpt = (i == 0) ? 'secIpt' : '';
                        str += '<li class="departwo ' + secIpt + '"><label><input type="checkbox" value="' + item.id + '" />' + item.name + '</label></li>';
                        ++count;
                    });
                    $('ul.twoUl').html(str);
                    var studentcount = 0;
                    var studentstr = '';
                    $.each(json_obj.students, function (i, item) {
                        studentstr += '<li class="students"><label><input type="checkbox" value="' + item.id + '" />' + item.name + '</label></li>';
                        ++studentcount;
                    });
                    $('ul.threeUl').html(studentstr);
                    //判断选中状态
                    if (ischecked) {
                        var targettwo = $('input[name=targettwo]').val();
                        var strarr = targettwo.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.twoUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }

                        var targetstudent = $('input[name=targetstudent]').val();
                        var strarr = targetstudent.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.threeUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }

                        if ($('ul.twoUl').find('input:checked').length === 0) {
                            $('ul.twoUl').find('input').attr('checked', 'checked');
                        }
                        if ($('ul.threeUl').find('input:checked').length != $('ul.threeUl').find('input').length ) {
                            $('ul.threeUl').find('input').attr('checked', 'checked');
                        }
                    } else {
                        $('ul.twoUl').find('input').removeAttr('checked');
                        $('ul.threeUl').find('input').removeAttr('checked');
                    }
                    //审核过的名单不可变更状态
                    var original_target = $('#original_target').val();
                    var originalarr = original_target.split(',');
                    for (var i = 0; i < originalarr.length; i++) {
                        $('ul.threeUl').find('input[value=' + originalarr[i] + ']').attr('checked', 'checked').attr('disabled', 'disabled').die('click');
                    }
                    //遍历赋值隐藏域
                    targetsetval('oneUl', 'targetone');
                    targetsetval('twoUl', 'targettwo');
                    targetsetval('threeUl', 'targetstudent');
                }
            });
        });
        $('.departwo').live('click', function () {
            $(this).addClass('secIpt').siblings().removeClass('secIpt');
            var ischecked = $(this).find('input').is(':checked');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('department/ajaxStudent') ?>',
                data: {'departmentid': $(this).find('input').val()},
                datatype: 'jsonp',
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    var count = 0;
                    var studentstr = '';
                    $.each(json_obj.students, function (i, item) {
                        studentstr += '<li class="students"><label><input type="checkbox" value="' + item.id + '" />' + item.name + '</label></li>';
                        ++count;
                    });
                    $('ul.threeUl').html(studentstr);
                    //判断选中状态
                    if (ischecked) {
                        var targetstudent = $('input[name=targetstudent]').val();
                        var strarr = targetstudent.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.threeUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }
                        if ($('ul.threeUl').find('input:checked').length != $('ul.threeUl').find('input').length) {
                            $('ul.threeUl').find('input').attr('checked', 'checked');
                        }
                    } else {
                        $('ul.threeUl').find('input').removeAttr('checked');
                    }
                    if ($('ul.twoUl').find('input:checked').length === 0) {
                        $('.oneUl .secIpt').find('input').removeAttr('checked', 'checked');
                    } else {
                        $('.oneUl .secIpt').find('input').attr('checked', 'checked');
                    }
                    //审核过的名单不可变更状态
                    var original_target = $('#original_target').val();
                    var originalarr = original_target.split(',');
                    for (var i = 0; i < originalarr.length; i++) {
                        $('ul.threeUl').find('input[value=' + originalarr[i] + ']').attr('checked', 'checked').attr('disabled', 'disabled').die('click');
                    }
                    //遍历赋值隐藏域
                    targetsetval('oneUl', 'targetone');
                    targetsetval('twoUl', 'targettwo');
                    targetsetval('threeUl', 'targetstudent');

                }
            });

        });
        $('.threeUl .students input').live('click', function () {
            if ($('ul.threeUl').find('input:checked').length === 0) {
                $('.twoUl .secIpt').find('input').removeAttr('checked', 'checked');
                if ($('ul.twoUl').find('input:checked').length === 0) {
                    $('.oneUl .secIpt').find('input').removeAttr('checked', 'checked');
                }
            } else {
                $('.twoUl .secIpt').find('input').attr('checked', 'checked');
                $('.oneUl .secIpt').find('input').attr('checked', 'checked');
            }

            //遍历赋值隐藏域
            targetsetval('oneUl', 'targetone');
            targetsetval('twoUl', 'targettwo');
            targetsetval('threeUl', 'targetstudent');

        });
        function targetsetval(ulclass, inputname) {
            //遍历初始化targetcheck值
            var str = $('input[name=' + inputname + ']').val();
            var arr = $.unique(str.split(','));
            $('ul.' + ulclass).find('input').each(function () {
                var v = $(this).val();
                if ($(this).is(':checked')) {
                    if ($.inArray(v, arr) === -1) {
                        arr.push(v);
                    }
                } else if ($.inArray(v, arr) !== -1) {
                    arr.splice($.inArray(v, arr), 1);
                }
            });
            for (var i = 0; i < arr.length; i++) {
                if (arr[i].length == 0) arr.splice(i, 1);
            }
            $('input[name=' + inputname + ']').val(arr.join(','));
            resetconWindow();
        }

        //调整弹窗列数
        function resetconWindow() {
            if ($('#conMessage .twoUl li').length <= 0) {
                $('#conMessage .twoUl').hide();
                $('#conMessage .oneUl,#conMessage .threeUl').width('45%');
            } else {
                $('#conMessage .oneUl,#conMessage .threeUl').width('33%');
                $('#conMessage .twoUl').show();
            }
        }

        $('a.okBtn').click(function () {
            $(this).text('请稍后..');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('annualplan/addstudenttocourselist/'.$plan['id'].'/'.$course['annual_course_id']) ?>',
                data: {
                    'targetstudent': $('input[name=targetstudent]').val()
                },
                async: false,
                success: function (res) {
                    if(res==1){
                        window.location='<?php echo site_url('annualplan/courselist/'.$plan['id'].'/'.$course['annual_course_id']).'?success=success' ?>';
                    }else{
                        alert('添加失败');
                    }
                }
            });
            $(this).text('确定');
            $('#conWindow').hide();
            return false;
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo $course['title']; ?></span>
        <a href="<?php echo site_url('annualplan/course/'.$plan['id']) ?>" class="fRight borBlueH37">返回列表</a>
    </div>
    <?php if(!empty($success)){ ?>
    <p class="alertBox alert-success mb20">
        <span class="alert-msg">操作成功</span>
        <a href="javascript:;" class="alert-remove">X</a>
    </p>
    <?php } ?>
    <div class="comBox">
        <div class="p15 clearfix">
            <p class="clearfix f14 mb20">共有<?php echo $totals ?>个学员,其中<?php echo $cross_num ?>个通过了审核
                <a href="#" id="addTarget" class="fRight borBlueH37">添加学员</a>
                <select id="department" class="iptH37 fRight mr10" <?php if(count($sec_departments)<=0){echo 'style="display:none;"';} ?> >
                    <option value="">全部</option>
                    <?php foreach($sec_departments as $d){ ?>
                        <option value="<?php echo $d['id'] ?>" <?php if($parm['department']==$d['id']){?>selected<?php } ?> ><?php echo $d['name'] ?></option>
                    <?php } ?>
                </select>
                <select id="parent_department" class="iptH37 fRight mr10">
                    <option value="">全部</option>
                    <?php foreach($departments as $d){ ?>
                        <option value="<?php echo $d['id'] ?>" <?php if($parm['parent_department']==$d['id']){?>selected<?php } ?> ><?php echo $d['name'] ?></option>
                    <?php } ?>
                </select>
            </p>

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
                    <th class="aLeft">姓名</th>
                    <th>工号</th>
                    <th>职务</th>
                    <th>部门</th>
                    <th>手机</th>
                    <th>状态</th>
                    <th>操作</th>

                </tr>
                <?php foreach ($aaclist as $a){ ?>
                    <tr>
                        <td class="blue wordBreak"><a href="javascript:;" class="blue mr10"><?php echo $a['name'] ?></a></td>
                        <td class="aCenter"><?php echo $a['job_code'] ?></td>
                        <td class="aCenter"><?php echo $a['job_name'] ?></td>
                        <td class="aCenter"><?php echo ($a['parent_department']==$a['department']||empty($a['parent_department']))?$a['department']:$a['parent_department'].'/'.$a['department'] ?></td>
                        <td class="aCenter"><?php echo $a['mobile'] ?></td>
                        <td class="aCenter"><?php if(empty($a['status'])){echo '未审核';}else{echo ($a['status']=='1')?'已通过':'未通过';} ?></td>
                        <td class="aCenter">
                            <?php if($a['status']==1){ ?>
                                <a href="<?php echo site_url('annualplan/unapproved/'.$plan['id'].'/'.$course['annual_course_id'].'/'.$a['student_id']); ?>" class="blue mr10">取消通过</a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('annualplan/approved/'.$plan['id'].'/'.$course['annual_course_id'].'/'.$a['student_id']); ?>" class="blue">通过</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php foreach ($apclist as $a){ ?>
                    <tr>
                        <td class="blue wordBreak"><a href="javascript:;" class="blue mr10"><?php echo $a['name'] ?></a></td>
                        <td class="aCenter"><?php echo $a['job_code'] ?></td>
                        <td class="aCenter"><?php echo $a['job_name'] ?></td>
                        <td class="aCenter"><?php echo ($a['parent_department']==$a['department']||empty($a['parent_department']))?$a['department']:$a['parent_department'].'/'.$a['department'] ?></td>
                        <td class="aCenter"><?php echo $a['mobile'] ?></td>
                        <td class="aCenter"><?php if(empty($a['status'])){echo '未审核';}else{echo ($a['status']=='1')?'已通过':'未通过';} ?></td>
                        <td class="aCenter">
                            <?php if($a['status']==1){ ?>
                                <a href="<?php echo site_url('annualplan/unapproved/'.$plan['id'].'/'.$course['annual_course_id'].'/'.$a['student_id']); ?>" class="blue mr10">取消通过</a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('annualplan/approved/'.$plan['id'].'/'.$course['annual_course_id'].'/'.$a['student_id']); ?>" class="blue">通过</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type="hidden" name="targetone" value="<?php echo $plan['targetone'] ?>"/>
<input type="hidden" name="targettwo" value="<?php echo $plan['targettwo'] ?>"/>
<input type="hidden" name="targetstudent" value="<?php echo $plan['targetstudent'] ?>" />
<input type="hidden" id="original_target" value="<?php echo $plan['targetstudent'] ?>" />
<div id="conWindow" style="z-index: 99999;display:none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" id="popConClose" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan" class="title_divText">请选择学员</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <div class="secBox">
                <ul class="oneUl">
                    <?php
                    $arr = explode(",", $plan['targetone']);
                    foreach ($deparone as $k => $d) { ?>
                        <li class="deparone <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="deparonecheckbox" <?php if (in_array($d['id'], $arr)) {
                                    echo 'checked';
                                } ?> type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></label>
                        </li>
                    <?php } ?>
                </ul>

                <ul class="twoUl">
                    <?php
                    $arr = explode(",", $plan['targettwo']);
                    foreach ($departwo as $k => $d) { ?>
                        <li class="departwo <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="departwocheckbox" <?php if (in_array($d['id'], $arr)) {
                                    echo 'checked';
                                } ?> type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></label>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="threeUl">
                    <?php
                    $arr = explode(",", $plan['targetstudent']);
                    foreach ($students as $k => $s) { ?>
                        <li class="students"><label><input class="studentscheckbox" <?php if (in_array($s['id'], $arr)) {
                                    echo 'checked disabled="disabled"';
                                } ?> type="checkbox" value="<?php echo $s['id']; ?>"/><?php echo $s['name']; ?></label>
                        </li>
                    <?php } ?>

                </ul>
            </div>
            <ul class="com_btn_list clearfix">
                <li><a class="okBtn" href="javascript:void(0);" jsBtn="okBtn">确定</a></li>
        </div>

    </div>
    <div class="popmap" style="z-index: 100000;"></div>
</div>