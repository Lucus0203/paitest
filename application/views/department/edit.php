<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script type="text/javascript">
    $(document).ready(function () {
        $('.closeBtn').click(function () {
            $('#conWindow').hide();
        });
        $('#addDeart,#addDeart2').click(function () {
            $('#conMessage input[name=act]').val('add');
            $('#conMessage input[name=departid]').val('');
            $('#conMessage input[name=departname]').val('');
            $('#title_divSpan').text('增加一级部门');
            $('#conWindow').show();
        });
        $('#addChildDepart').click(function () {
            $('#conMessage input[name=act]').val('add');
            current_department_id = $('#current_department_id').val();
            current_department_name = $('#current_department_name').val();
            $('#title_divSpan').text('增加下级部门(' + current_department_name + ')');
            $('#conMessage input[name=departid]').val(current_department_id);
            $('#conMessage input[name=departname]').val('');
            $('#conWindow').show();
            return false;
        });
        $('a.okBtn').click(function () {
            act = $('#conMessage input[name=act]').val();
            departid = $('#conMessage input[name=departid]').val();
            departname = $('#conMessage input[name=departname]').val();
            if (act == 'save') {//编辑
                current_id = $('#current_department_id').val();
                current_name = $('#conMessage input[name=departname]').val();
                ;
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('department/save') ?>',
                    data: {'currentid': current_id, 'currentname': current_name},
                    success: function (res) {
                        if (res == 0) {
                            alert('修改失败');
                        }else if(res==-1){
                            alert('部门已存在');
                        } else {
                            id = res;
                            $('#current_department_name').val(departname);
                            $('.textureSide a.on,.textureSide li.on a').text(departname);
                            $('.texturetip span').eq(0).text(departname);
                            $('#conWindow').hide();
                        }
                    }
                })
            } else {//新增
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('department/add') ?>',
                    data: {'parentid': departid, 'departname': departname},
                    success: function (res) {
                        if (res == 0) {
                            alert('添加失败');
                        }else if(res==-1){
                            alert('部门已存在');
                        }else {
                            id = res;
                            if (departid == '') {
                                $('.textureSide').append('<div class="fnavi"><a href="<?php echo base_url() ?>department/index/' + res + '.html" class="flink"><i class="iup"></i>' + departname + '</a><ul class="clink departChildren' + res + '"></ul></div>');
                                $('#conWindow').hide();
                            } else {
                                $('ul.departChildren' + departid).append('<li><a target="_blank" href="<?php echo base_url() ?>department/index/' + res + '.html">' + departname + '</a></li>');
                                $('#conWindow').hide();
                            }
                        }
                    }
                })
            }
            return false;
        });
        $('#editDepart').click(function () {
            $('#conMessage input[name=act]').val('save');
            $('#conMessage input[name=departname]').val($('#current_department_name').val());
            $('#title_divSpan').text('编辑部门');
            $('#conWindow').show();
        });
        $('#delDepart').click(function () {
            current_id = $('#current_department_id').val();
            if (confirm('确定删除当前部门吗?')) {
                $.ajax({
                    type: "post",
                    url: '<?php echo site_url('department/del') ?>',
                    data: {'currentid': current_id},
                    success: function (res) {
                        if (res == 0) {
                            id = res;
                            $('.textureSide a.on,.textureSide li.on a').remove();
                            $('.textureCont').html('');
                            $('#conWindow').hide();
                        } else if (res == 1) {
                            alert('删除失败');
                        } else if (res == 2) {
                            alert('含有下级部门无法删除');
                        } else if (res == 3) {
                            alert('部门含有学员数据,请修改学员所在部门');
                        }
                    }
                });
            }
        });
        $('.delStudent').click(function () {
            return confirm('确认删除吗?');
        });

        $('#excelFileBtn').change(function(){
            // 检查是否为图像类型
            var simpleFile = document.getElementById("excelFileBtn").files[0];
            var name=simpleFile.name;
            var ext=name.slice(name.indexOf('.'));
            if(!/xls/.test(name)) {
                alert("请确保文件类型为excel");
                return false;
            }else{
                $('#excelFileBtn').next().html(name);
                $('#importBtn').show();
            }
        });
        $('#uploadForm').validate({
            rules: {
                excelFile: {
                    required: true,
                    extension: "xls|xlsx",
                    filesize: 50 * 1048576
                }
            },
            messages: {
                excelFile: {
                    required: "请上传要导入数据",
                    accept: "上传文件必须是excel",
                    filesize: "图片大小不能超过50M"
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
            submitHandler:function(form){
                $('input[type=submit]').val('请稍后..').attr('disabled','disabled');
                form.submit();
            }
        });
    });
    function checkFile(){
        if($('#excelFileBtn').val()==''){
            alert('请选择学员数据文件');
            return false;
        }else{
            return true;
        }
    }
</script>
<div class="wrap">
    <div class="textureSide">
        <a id="addDeart" href="javascript:void(0)" class="topbtn">新增部门</a>
        <div class="fnavi">
            <a class="flink mb10 <?php echo empty($current_department['id'])?'on':'' ?>" href="<?php echo site_url('department/index') ?>">所有学员</a>
        </div>
        <?php foreach ($departments as $d) { ?>
            <div class="fnavi">
                <a href="<?php echo site_url('department/index/' . $d['id']) ?>"
                   class="flink <?php echo $current_department['id'] == $d['id'] ? 'on' : '' ?>"><i
                        class="iup fa fa-angle-down"></i><?php echo $d['name'] ?></a>
                <ul class="clink departChildren<?php echo $d['id'] ?>">
                    <?php if (!empty($d['departs'])) {
                        foreach ($d['departs'] as $dp) { ?>
                            <li class="<?php echo $current_department['id'] == $dp['id'] ? 'on' : '' ?>"><a
                                    href="<?php echo site_url('department/index/' . $dp['id']) ?>"><?php echo $dp['name'] ?></a>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        <?php } ?>
<!--        <a href="--><?php //echo site_url('ability/index') ?><!--" class="toporangebtn mt20">能力模型管理</a>-->
    </div>
    <div class="textureCont">
        <?php if (!empty($current_department)) { ?>
            <input type="hidden" id="current_department_id" value="<?php echo $current_department['id'] ?>"/>
            <input type="hidden" id="current_department_name" value="<?php echo $current_department['name'] ?>"/>
            <div class="texturetip p2014 clearfix"><span class="fLeft"><?php echo $current_department['name'] ?></span>
                <div class="fRight"><?php if ($current_department['level'] == 0) { ?>
                        <a id="addChildDepart" href="javascript:;" class="borBlueBtnH28 w72 aCenter">添加下级部门</a><?php } ?>
                    <a id="editDepart" href="javascript:;" class="borBlueBtnH28 w72 aCenter">编辑部门</a>
                    <a id="delDepart" href="javascript:;" class="borBlueBtnH28 w72 aCenter">删除部门</a>
                    <a href="<?php echo site_url('student/index/' . $current_department['id']) ?>" class="borBlueBtnH28 w72 aCenter">增加学员</a></div>
            </div>
        <?php } else { ?>
            <div class="texturetip p2014 clearfix"><span class="fLeft">所有学员</span>
                <div class="fRight">
                    <?php if(count($students)>0){ ?><a href="<?php echo site_url('export/studentdata') ?>" target="_blank" class="borBlueBtnH28 w72 aCenter">导出全部学员</a><?php } ?>
                    <a id="addDeart2" href="javascript:;" class="borBlueBtnH28 w72 aCenter">添加一级部门</a>
                    <a href="<?php echo site_url('student/index/' . $current_department['id']) ?>" class="borBlueBtnH28 w72 aCenter">增加学员</a>
                </div>
            </div>
        <?php } ?>
        <div class="p15">
            <p class="clearfix f14 mb20">
                <?php if (empty($students)) { ?>
                    当前部门没有学员
                <?php } else { ?>
                    当前部门共有<?php echo $total ?>人，<?php echo $admintotal ?>名分级管理员
                <?php } ?>
            </p>
            <?php if (!empty($students)) { ?>
                <table cellspacing="0" class="listTable">
                    <tbody>
                    <tr>
                        <th class="aLeft">姓名</th>
                        <th class="aLeft">工号</th>
                        <th class="aLeft">职务</th>
                        <th class="aLeft">部门</th>
                        <th class="aLeft">账号状态</th>
                        <th class="aLeft">账号类别</th>
                        <th>操作</th>
                    </tr>
                    <?php foreach ($students as $s) { ?>
                        <tr>
                            <td class="blue"><a class="blue" href="<?php echo site_url('student/edit/' . $s['id']) ?>"><?php echo !empty($s['name'])?$s['name']:'<span class="orange">未填写</span>' ?></a></td>
                            <td><?php echo $s['job_code'] ?></td>
                            <td><?php echo $s['job_name'] ?></td>
                            <td><?php echo $s['department'] ?></td>
                            <td><?php echo $s['status'] == 1 ? '未激活' : '已激活' ?></td>
                            <td><span class="yellow"><?php
                                    if ($s['role'] == 9) {
                                        echo '系统管理员';
                                    } elseif ($s['role'] == 1) {
                                        echo '普通学员';
                                    } elseif ($s['role'] == 2) {
                                        echo '助理管理员';
                                    } else {
                                        echo '员工经理';
                                    } ?></span></td>
                            <td class="aCenter"><a class="blue" href="<?php echo site_url('student/edit/' . $s['id']) ?>">编辑</a>
                                <?php if($s['role'] != 9){ ?>
                                <a class="blue delStudent" href="<?php echo site_url('student/del/' . $s['id']) ?>">删除</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo $links ?>
            <?php } ?>
        </div>

        <?php if(empty($current_department['id'])){ ?>
        <div class="texturetip clearfix">
            <form id="uploadForm" method="post" action="<?php echo site_url('upload/uploadstudent') ?>"  enctype="multipart/form-data" onsubmit="return checkFile();">
                <div>
                    <a href="javascript:;" onclick="$('#excelFileBtn').click();" class="borBlueH37 mb10">导入学员数据</a>
                    <input id="excelFileBtn" type="file" name="excelFile" style="display: none;" autocomplete="off" />
                    <span></span>
                    <p class="gray9 mb20">导入时如果学员数据已存在(手机号不能重复)则会更新数据<a href="<?php echo site_url('upload/downloadstudentexample') ?>" class="blue">下载模板</a> </p>
                    <p class="aCenter"><input id="importBtn" style="display: none;" class="coBtn" type="submit" value="确定导入"></p>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</div>

<!--tankuang de yangshi -->
<div id="conWindow" style="z-index: 99999; display: none;" class="popWinBox">
    <div class="pop_div" style="z-index: 100001;">
        <div class="title_div"><a class="closeBtn" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan"
                                                                                  class="title_divText">增加一级部门</span>
        </div>
        <div id="conMessage" class="pop_txt01">
            <table class="comTable">
                <col width="150"/>
                <tr>
                    <th>部门名称</th>
                    <td class="aLeft">
                        <input name="act" value="add" type="hidden">
                        <input name="departid" type="hidden">
                        <input name="departname" type="text" class="ipt w250"></td>
                </tr>
                <tr>
                    <th></th>
                    <td class="aLeft"><a jsbtn="okBtn" href="javascript:;" class="okBtn">保存设置</a></td>
                </tr>
            </table>


        </div>

    </div>
    <div class="popmap" style="z-index: 100000;"></div>
</div>