<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript">
    currentTargetIndex=0;
    $(function(){
        $('.publish').click(function () {
            return confirm('确定发布并短信提醒匹配人员吗?');
        });
        $('.unpublish').click(function(){
            return confirm('此操作将停止收集学员评估,是否继续?')
        });
        //选择对象
        $('.addTarget').click(function () {
            currentTargetIndex=$('.addTarget').index($(this));
            //初始化弹窗
            var targetone = $('input[name=targetone]').eq(currentTargetIndex).val();
            var strarr = targetone.split(',');
            $('ul.oneUl').find('input').removeAttr('checked');
            for (var i = 0; i < strarr.length; i++) {
                $('ul.oneUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
            }

            var targettwo = $('input[name=targettwo]').eq(currentTargetIndex).val();
            var strarr = targettwo.split(',');
            $('ul.twoUl').find('input').removeAttr('checked');
            for (var i = 0; i < strarr.length; i++) {
                $('ul.twoUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
            }

            var targetstudent = $('input[name=targetstudent]').eq(currentTargetIndex).val();
            var strarr = targetstudent.split(',');
            $('ul.threeUl').find('input').removeAttr('checked');
            for (var i = 0; i < strarr.length; i++) {
                $('ul.threeUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
            }
            $('#conWindow').show();
            resetconWindow();
            return false;
        });
        $('#popConClose').click(function () {
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
                        var targettwo = $('input[name=targettwo]').eq(currentTargetIndex).val();
                        var strarr = targettwo.split(',');
                        for (var i = 0; i < strarr.length; i++) {
                            $('ul.twoUl').find('input[value=' + strarr[i] + ']').attr('checked', 'checked');
                        }

                        var targetstudent = $('input[name=targetstudent]').eq(currentTargetIndex).val();
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
                        var targetstudent = $('input[name=targetstudent]').eq(currentTargetIndex).val();
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
            var str = $('input[name=' + inputname + ']').eq(currentTargetIndex).val();
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
            $('input[name=' + inputname + ']').eq(currentTargetIndex).val(arr.join(','));
            resetconWindow();
        }
        //调整弹窗列数
        function resetconWindow(){
            if($('#conMessage .twoUl li').length<=0){
                $('#conMessage .twoUl').hide();
                $('#conMessage .oneUl,#conMessage .threeUl').width('45%');
            }else{
                $('#conMessage .oneUl,#conMessage .threeUl').width('33%');
                $('#conMessage .twoUl').show();
            }
        }
        $('a.okBtn').click(function () {
            $(this).text('请稍后..');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('ability/updateTarget') ?>',
                data: {
                    'jobid': $('input[name=jobid]').eq(currentTargetIndex).val(),
                    'targetone': $('input[name=targetone]').eq(currentTargetIndex).val(),
                    'targettwo': $('input[name=targettwo]').eq(currentTargetIndex).val(),
                    'targetstudent': $('input[name=targetstudent]').eq(currentTargetIndex).val()
                },
                async: false,
                success: function (res) {
                    $('.target').eq(currentTargetIndex).text(res);
                }
            });
            $(this).text('确定');
            $('#conWindow').hide();
            return false;
        });

    });
</script>
<div class="wrap">
    <div class="textureCont w960">

        <div class="texturetip p2015 clearfix"><span class="fLeft pt5">所有能力模型</span>
            <div class="fRight">
                <a class="borBlueH37" href="<?php echo site_url('html/ability') ?>">更多模型</a>
            </div>
        </div>

        <div class="p15">
            <p class="clearfix f14 mb20">共<?php echo $total_rows ?>个岗位</p>
            <table cellspacing="0" class="listTable">
                <colgroup>
                    <col width="20%">
                    <col width="50%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>
                <tbody>
                <tr>
                    <th class="aLeft">岗位</th>
                    <th class="center">匹配学员</th>
                    <th class="center">状态</th>
                    <th class="center">操作</th>

                </tr>
                <?php foreach ($jobs as $job) { ?>
                    <tr>
                        <td class="aLeft"><a class="blue" href="<?php echo site_url('ability/show/'.$job['id']) ?>"><?php echo $job['name'] ?></a></td>
                        <td class="aCenter">
                            <input type="hidden" name="jobid" value="<?php echo $job['id'] ?>"/>
                            <input type="hidden" name="targetone" value="<?php echo $job['target_one'] ?>"/>
                            <input type="hidden" name="targettwo" value="<?php echo $job['target_two'] ?>"/>
                            <input type="hidden" name="targetstudent" value="<?php echo $job['target_student'] ?>"/>
                            <a class="blue target" href="<?php echo site_url('ability/targets/'.$job['id']) ?>"><?php echo $res = mb_strlen($job['target'], 'utf-8') > 20 ? mb_substr( $job['target'],0,40,"utf-8").'...':$job['target']; ?></a>
                        </td>
                        <td class="aCenter">
                            <?php echo $job['status']==1?'发布中':'未发布' ?>
                        </td>
                        <td class="aCenter">
                            <a href="#" class="blue addTarget">匹配</a>&nbsp;&nbsp;
                            <?php if($job['status']==1){ ?>
                                <a class="blue unpublish" href="<?php echo site_url('ability/unpublish/'.$job['id']) ?>" >取消</a>
                            <?php }else{ ?>
                                <a class="blue publish" href="<?php echo site_url('ability/publish/'.$job['id']) ?>">发布</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="4"><a class="blue" href="<?php echo site_url('html/ability') ?>"><i class="fa fa-plus fa-lg mr5"></i>开通更多</a></td>
                </tr>

                </tbody>
            </table>
            <div class="pageNavi">
                <?php echo $links ?>
            </div>

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
                    foreach ($deparone as $k => $d) { ?>
                        <li class="deparone <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="deparonecheckbox" type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></label>
                        </li>
                    <?php } ?>
                </ul>

                <ul class="twoUl">
                    <?php
                    foreach ($departwo as $k => $d) { ?>
                        <li class="departwo <?php if ($k == 0) {
                            echo 'secIpt';
                        } ?>"><label><input class="departwocheckbox" type="checkbox" value="<?php echo $d['id']; ?>"/><?php echo $d['name']; ?></label>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="threeUl">
                    <?php
                    foreach ($students as $k => $s) { ?>
                        <li class="students"><label><input class="studentscheckbox" type="checkbox" value="<?php echo $s['id']; ?>"/><?php echo $s['name']; ?></label>
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