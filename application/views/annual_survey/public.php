<script type="text/javascript">
    jQuery.validator.addMethod("isexistsurvey", function(value, element,params) {
        var flag = false;
        var time_start = $('#time_start').val();
        var time_end = $('#time_end').val();
        $.ajax({
            type:"POST",
            url:'<?php echo strpos(current_url(),'copy')?site_url('annualsurvey/isExistSurvey'):site_url('annualsurvey/isExistSurvey/'.$survey['id']); ?>',
            async:false,
            data:{'time_start':time_start,'time_end':time_end},
            success: function(res){
                flag = res*1>0?false:true;
            }
        });
        return flag;
    }, "此时段有正在调研的问卷,请更换时段");
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                time_start: {
                    required: true
                },
                time_end: {
                    required: true,
                    compareDate: "input[name=time_start]",
                    isexistsurvey:true
                }
            },
            messages: {
                time_start: {
                    required: "请输入开始时间"
                },
                time_end: {
                    required: "请输入结束时间",
                    compareDate: "结束时间不能早于开始时间",
                    isexistsurvey: "此时段有正在调研的问卷,请更换时段"
                }
            },
            errorPlacement: function (error, element) {
                error.addClass("ui red pointing label transition");
                error.insertAfter(element.parent());
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".row").addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".row").removeClass(errorClass);
            },
            submitHandler: function (form) {
                $('input[type=submit]').val('请稍后..').attr('disabled', 'disabled');
                <?php if($survey['public']==3){ ?>
                form.submit();
                <?php }else{ ?>
                if(confirm('发布后问题及课程无法修改,确认发布吗?')){
                    form.submit();
                }else{
                    $('input[type=submit]').val('确认发布').removeAttr('disabled');
                }
                <?php } ?>
            }
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
                url: '<?php echo site_url('annualsurvey/updateTarget') ?>',
                data: {
                    'targetstudent': $('input[name=targetstudent]').val()
                },
                async: false,
                success: function (res) {
                    $('textarea[name=target]').val(res);
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
        <span class="titSpan"><?php echo $survey['public']=='3'?'继续发布':'发布' ?>调研问卷</span>
        <a href="<?php echo site_url('annualsurvey/info/'.$survey['id']) ?>" class="fRight borBlueH37">返回</a>
    </div>
    <div class="comBox">
        <?php if (!empty($msg)) {?>
            <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <?php if (!empty($errmsg)) {?>
            <p class="alertBox alert-danger"><span class="alert-msg"><?php echo $errmsg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox">
            <form id="editForm" method="post" action="">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th>问卷名称</th>
                        <td>
                            <span class="iptInner">
                            <?php echo $survey['title'] ?>
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>调查时间</th>
                        <td>
                            <span class="iptInner">
                            <input placeholder="开始时间" name="time_start" id="time_start" value="<?php echo empty($survey['time_start'])?'':date("Y-m-d H:i",strtotime($survey['time_start'])) ?>" type="text" class="iptH37 mr10 DTdate w156" autocomplete="off" >至<input placeholder="结束时间" name="time_end" id="time_end" value="<?php echo empty($survey['time_end'])?'':date('Y-m-d H:i',strtotime($survey['time_end'])) ?>" type="text" class="iptH37 ml10 DTdate w156" autocomplete="off" >
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>调研对象</th>
                        <td>
                            <span class="iptInner">
                                <input type="hidden" name="targetone" value="<?php echo $survey['targetone'] ?>"/><input
                                    type="hidden" name="targettwo" value="<?php echo $survey['targettwo'] ?>"/><input
                                    type="hidden" name="targetstudent" value="<?php echo $survey['targetstudent'] ?>"/>
                            <textarea readonly="true" placeholder="请选择学员" name="target" class="iptare pt10 w345"><?php echo $survey['target'] ?></textarea><a id="addTarget" class="borBlueH37 ml20" href="javascript:void(0)" style="vertical-align:text-bottom;">选择学员</a>
                            </span>
                            <p class="gray9 mt15">仅选中学员将收到调研通知，并可完成需求调研</p>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="iptInner">
                            <input type="submit" value="<?php echo $survey['public']=='3'?'确认继续':'确认' ?>发布" class="coBtn mr30">
                            </span>
                        </td>
                    </tr>
                </table>
        </div>
    </div>
</div>

<div id="conWindow" style="z-index: 99999;display:none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" id="popConClose" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan"
                                                                                                                                   class="title_divText">请选择学员</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <div class="secBox">
                <ul class="oneUl">
                    <?php
                    $arr = explode(",", $survey['targetone']);
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
                    $arr = explode(",", $survey['targettwo']);
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
                    $arr = explode(",", $survey['targetstudent']);
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