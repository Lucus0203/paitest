<script type="text/javascript">
    $(document).ready(function(){
        $( "#editForm" ).validate( {
            rules: {
                apply_start: {
                    required: true
                },
                apply_end: {
                    required: true,
                    compareDate: "input[name=apply_start]"
                },
                apply_num: {
                    required: true
                }
            },
            messages: {
                apply_start: {
                    required: "请输入报名开始时间"
                },
                apply_end: {
                    required: "请输入报名结束时间",
                    compareDate: "结束时间不能早于开始时间"

                },
                apply_num: {
                    required: "请输入报名人数"

                }
            },
            errorPlacement: function ( error, element ) {
                error.addClass( "ui red pointing label transition" );
                error.insertAfter( element.parent() );
            },
            highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".row" ).addClass( errorClass );
            },
            unhighlight: function (element, errorClass, validClass) {
                $( element ).parents( ".row" ).removeClass( errorClass );
            },
            submitHandler:function(form){
                $('input[type=submit]').val('请稍后..').attr('disabled','disabled');
                if($('#ispublic').val()!=1&&$('input[name=isapply_open]:checked').val()==1){
                    if(confirm('课程暂未发布,是否发布课程并开启报名')){
                        form.submit();
                    }else{
                        $('input[type=submit]').val('保存').removeAttr('disabled');
                    }
                }else{
                    form.submit();
                }
            }
        });

        $('#apply_end').focus(function(){
            $(this).val($.trim($(this).val())==''?$('#apply_start').val():$(this).val());
        });

        $('#selectSomeone').click(function(){
            $('#conWindow').show();
        });
        $('a.calBtn,div.popmap,a.closeBtn').click(function(){
            $('#conWindow').hide();
        });
        $('input[name=isapply_open]').change(function(){
            if($(this).val()==2){
                $('#notifysubmitBtn').css({'background-color':'#ccc'}).attr('disabled','disabled');
            }else{
                $('#notifysubmitBtn').css({'background-color':'#67d0de'}).removeAttr('disabled');
            }
        });
        $('#notifysubmitBtn').click(function(){
            $('#notify_check').val('1');
        });
        $('#submit').click(function(){
            $('#notify_check').val('');
        });

        //学员选择器
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
                url: '<?php echo site_url('ajax/ApplyTargetDepartmentAndStudent/'.$course['id']) ?>',
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
                        if ($('ul.threeUl').find('input:checked').length === 0) {
                            $('ul.threeUl').find('input').attr('checked', 'checked');
                        }
                    } else {
                        $('ul.twoUl').find('input').removeAttr('checked');
                        $('ul.threeUl').find('input').removeAttr('checked');
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
                url: '<?php echo site_url('ajax/ApplyTargetAjaxStudent/'.$course['id']) ?>',
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
                        if ($('ul.threeUl').find('input:checked').length === 0) {
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
            var tar=$('input[name=targetstudent]').val();
            tar=(tar!='')?tar.split(','):'';
            $('#addTarget').next().text('通知人数('+tar.length+')');
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
            $('#conWindow').hide();
            return false;
        });

        //全选
        $('#checkAll').change(function(){
            if($(this).attr('checked')){
                $('.oneUl input,.twoUl input,.threeUl input').attr('checked','checked');
                $('input[name=targetone]').val($('#targetone_original').val());
                $('input[name=targettwo]').val($('#targettwo_original').val());
                $('input[name=targetstudent]').val($('#targetstudent_original').val());
                var tar=$('#targetstudent_original').val();
                tar=(tar!='')?tar.split(','):'';
                $('#addTarget').next().text('通知人数('+tar.length+')');
            }else{
                $('.deparone input,.departwo input,.students input').removeAttr('checked');
                $('input[name=targetone]').val('');
                $('input[name=targettwo]').val('');
                $('input[name=targetstudent]').val('');
                $('#addTarget').next().text('通知人数(0)');
            }
        });
        var tar=$('input[name=targetstudent]').val();
        tar=(tar!='')?tar.split(','):'';
        $('#addTarget').next().text('通知人数('+tar.length+')');
    });
</script>
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
                                <li class="cur"><a href="<?php echo site_url('course/applyset/'.$course['id']) ?>">报名设置<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['applylist']==1){ ?>
                                <li><a href="<?php echo site_url('course/applylist/'.$course['id']) ?>">报名名单</a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['notifyset']==1){ ?>
                                <li ><a href="<?php echo site_url('course/notifyset/'.$course['id']) ?>">通知设置</a></li>
<?php } ?>
                        </ul>

                </div>
                <div class="contRight">
                    <?php if (!empty($msg)) {?>
                        <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
                    <?php } ?>
                    <form id="editForm" method="post" action="">
                        <input name="act" type="hidden" value="act" />
                        <input id="ispublic" type="hidden" value="<?php echo $course['ispublic'] ?>" />
                        <table cellspacing="0" class="comTable">
                            <colgroup><col width="100">
                            </colgroup><tbody><tr>
                                <th><span class="red">*</span>开启报名</th>
                                <td>
                                    <ul class="lineUl">
                                        <li>
                                            <label><input name="isapply_open" value="1" checked="checked" type="radio">开启</label></li>
                                        <li>
                                            <label><input name="isapply_open" value="2" <?php if($course['isapply_open']==2){echo 'checked="checked"';} ?> type="radio">关闭</label></li>
                                    </ul>

                                </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>报名时间</th>
                                <td><span class="iptInner">
                                            <input type="text" name="apply_start" id="apply_star" value="<?php echo !empty($course['apply_start'])?date("Y-m-d H:i",strtotime($course['apply_start'])):'' ?>" class="iptH37 DTdate" autocomplete="off"> 至 <input name="apply_end" id="apply_end" value="<?php echo !empty($course['apply_start'])?date("Y-m-d H:i",strtotime($course['apply_end'])):'' ?>" type="text" class="iptH37 DTdate" autocomplete="off">
                                                </span>

                                </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>报名人数</th>
                                <td>
                                    <input type="text" name="apply_num" value="<?php echo $course['apply_num']>0?$course['apply_num']:0 ?>" class="iptH37 w157">人时，停止报名 <span class="gray9">(0表示不限人数)</span>


                                </td>
                            </tr>
                            <tr>
                                <th>其他设置</th>
                                <td>
                                    <label><input name="apply_check" value="1" <?php if($course['apply_check']==1){echo 'checked="checked"';} ?> type="checkbox" class="mr10" />报名需审核</label>
                                    <!--<ul class="lineUl">
                                                        <li>
                                                            <input name="apply_check_type" <?php if($course['apply_check_type']==1){echo 'checked="checked"';} ?> value="1" type="radio">管理员审核</li>
                                                        <li>
                                                            <input name="apply_check_type" <?php if($course['apply_check_type']==2){echo 'checked="checked"';} ?> value="2" type="radio">部门经理审核(分级管理员)</li>
                                                </ul>-->

                                </td>
                            </tr>
                            <tr>
                                <th>报名提示</th>

                                <td>
                                    <input name="apply_tip" class="iptH37 w345" value="<?php echo $course['apply_tip'] ?>" />

                                </td>
                            </tr>
                            <tr>
                                <th>本次通知</th>
                                <td><a id="addTarget" class="borBlueH37" href="javascript:void(0)">选择学员</a><span class="ml10">通知人数(0)</span>
                                    <input type="hidden" name="targetone" value="<?php echo $course['targetone'] ?>"/>
                                    <input type="hidden" name="targettwo" value="<?php echo $course['targettwo'] ?>"/>
                                    <input type="hidden" name="targetstudent" value="<?php echo $course['targetstudent'] ?>"/>
                                    <input type="hidden" id="targetone_original" value="<?php echo $course['targetone'] ?>"/>
                                    <input type="hidden" id="targettwo_original" value="<?php echo $course['targettwo'] ?>"/>
                                    <input type="hidden" id="targetstudent_original" value="<?php echo $course['targetstudent'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <input id="notify_check" type="hidden" name="notify_check" value="" />
                                    <input id="notifysubmitBtn" type="submit" class="coBtn mr20" <?php if($course['isapply_open']!=1){echo 'style="background-color:#ccc;color:#fff;" disabled="disabled;"';} ?> value="保存并发送通知">
                                    <input id="submit" type="submit" class="coBtn" value="仅保存">
                                </td>
                            </tr>
                        </tbody></table>
                    </form>
                </div>

                </div>

        </div>
</div>
<div id="conWindow" style="z-index: 99999;display:none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" id="popConClose" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan" class="title_divText">请选择学员</span> <label>
        </div>
        <div id="conMessage" class="pop_txt01">
            <ul class="secList">
                <li><label class="ml10"><input id="checkAll" type="checkbox" checked >全选</label></li>
            </ul>
            <div class="secBox">
                <ul class="oneUl">
                    <?php
                    $arr = explode(",", $course['targetone']);
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
                    $arr = explode(",", $course['targettwo']);
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
                    $arr = explode(",", $course['targetstudent']);
                    foreach ($students as $k => $s) { ?>
                        <li class="students"><label><input class="studentscheckbox" <?php if (in_array($s['id'], $arr)) {
                                    echo 'checked';
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