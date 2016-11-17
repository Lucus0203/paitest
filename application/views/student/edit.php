<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/texture.css" />
<script type="text/javascript">
$(document).ready(function(){
    $('.closeBtn').click(function(){$('#conWindow').hide();});
    $('select[name=department_parent_id]').change(function(){
        var departmentid=$(this).val();
        $.ajax({
                type:"post",
                url:'<?php echo site_url('department/ajaxDepartmentAndStudent') ?>',
                data:{'departmentid':departmentid},
                datatype:'jsonp',
                success:function(res){
                        var json_obj = $.parseJSON(res);
                        var count=0;
                        var str='<option value="'+departmentid+'">请选择</option>';
                        $.each(json_obj.departs,function(i,item){
                            str+='<option value="'+item.id+'">'+item.name+'</option>';
                            ++count;
                        });
                        if(count>0){
                            $('select[name=department_id]').show().html(str)
                        }else{
                            $('select[name=department_id]').hide().html('<option value="'+departmentid+'" selected >请选择</option>');
                        }
                }
        });
    });
    $('#addDeart').click(function(){
        $('#conMessage input[name=departid]').val('');
        $('#conMessage input[name=departname]').val('');
        $('#title_divSpan').text('增加一级部门');
        $('#conWindow').show();});
    $('a.okBtn').click(function(){
        act=$('#conMessage input[name=act]').val();
        departid=$('#conMessage input[name=departid]').val();
        departname=$('#conMessage input[name=departname]').val();
        $.ajax({
                type:"post",
                url:'<?php echo site_url('department/add') ?>',
                data:{'parentid':departid,'departname':departname},
                success:function(res){
                        if(res==0){
                            alert('添加失败');
                        }else{
                            id=res;
                            $('.textureSide').append('<div class="fnavi"><a href="<?php echo base_url() ?>department/index/'+res+'.html" class="flink"><i class="iup"></i>'+departname+'</a><ul class="clink departChildren'+res+'"></ul></div>');
                            $('select[name=department_parent_id]').append('<option value="'+res+'">'+departname+'</option>');
                            $('#conWindow').hide();
                        }
                }
        })
        return false;
    });
    jQuery.validator.addMethod("isMobile", function(value, element) { 
        var length = value.length; 
        var mobile = /^((1[0-9]{2})+\d{8})$/; 
        return this.optional(element) || (length == 11 && mobile.test(value)); 
    }, "请正确填写您的手机号码");
    $('input[name=mobile]').blur(function(){
        var mobile=$(this).val();
        $('input[name=user_name]').val(mobile);
        if($.trim($('input[name=student_pass]').val())==''){
            $('input[name=student_pass]').val(mobile.slice(-6));
            $('#student_pass').val(mobile.slice(-6));
        }
    });
    $('#student_pass').keyup(function(){
       $('input[name=student_pass]').val($(this).val());
        return false;
    });
    $( "#editForm" ).validate( {
        ignore: ".ignore",
        rules: {
            name: {
                required: true
            },
            department_parent_id:{
                required: true
            },
            mobile: {
                required: true,
                isMobile: true
            },
            email: {
                email: true
            },
            student_pass:{
                required: true,
                minlength:6
            }
        },
        messages: {
            name: {
                required: "请输入学员姓名"
            },
            department_parent_id:{
                required: "请选择您的部门"
            },
            mobile: {
                required: "请输入您的电话号码",
                isMobile: "请输入正确的手机号码"
            },
            email: {
                email: "请输入正确的邮箱地址"
            },
            student_pass:{
                required: "请输入密码",
                minlength:"最少{0}位密码"
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
            form.submit();
        }
    });
});
</script>
<div class="wrap clearfix">
    <div class="textureSide">
        <a id="addDeart" href="javascript:void(0)" class="topbtn">新增部门</a>
        <div class="fnavi">
            <a class="flink mb10 <?php echo empty($current_department['id'])?'on':'' ?>" href="<?php echo site_url('department/index') ?>">所有学员</a>
        </div>
            <?php foreach ($departments as $d){ ?>
                <div class="fnavi">
                    <a href="<?php echo site_url('department/index/'.$d['id']) ?>" class="flink <?php echo $current_department['id']==$d['id']?'on':'' ?>"><i class="iup"></i><?php echo $d['name'] ?></a>
                    <ul class="clink departChildren<?php echo $d['id'] ?>">
                        <?php if(!empty($d['departs'])){
                            foreach ($d['departs'] as $dp){ ?>
                                <li class="<?php echo $current_department['id']==$dp['id']?'on':'' ?>"><a href="<?php echo site_url('department/index/'.$dp['id']) ?>"><?php echo $dp['name'] ?></a></li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            <?php } ?>

    </div>
    <div class="textureCont">
            <input type="hidden" id="current_department_id" value="<?php echo $current_department['id'] ?>" />
            <input type="hidden" id="current_department_name" value="<?php echo $current_department['name'] ?>" />
            <div class="texturetip clearfix"><span class="fLeft"><?php echo empty($student)?'增加':'编辑' ?>学员<?php echo !empty($current_department['name'])?'('.$current_department['name'].')':'' ?></span>
                    <div class="fRight"><a href="<?php echo site_url('department/index/'.$current_department['id']) ?>" class="borBlueBtnH28">返回<?php echo $current_department['name'] ?></a></div>
            </div>
<?php if($msg=='保存成功'){?>
    <p class="alertBox alert-success "><span class="alert-msg">保存成功!</span><a href="javascript:;" class="alert-remove">X</a></p>
<?php } ?>
<?php if($msg!='保存成功'&&!empty($msg)){?>
        <p class="alertBox alert-danger"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
<?php } ?>
            <div class="p15">
                    <form id="editForm" method="post" action=""  enctype="multipart/form-data" autocomplete="off">
                    <input name="act" type="hidden" value="act" />
                    <table cellspacing="0" class="comTable mb20">
                            <colgroup><col width="100">
                            </colgroup><tbody>
                            <tr>
                                    <th><span class="red">*</span>学员姓名</th>
                                    <td>
                                        <span class="iptInner">
                                            <input name="name" value="<?php echo $student['name']?>" type="text" class="iptH37 w250">
                                        </span>
                                    </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>性别</th>
                                <td>
                                    <ul class="lineUl">
                                        <li>
                                            <label><input name="sex" value="1" type="radio" checked="">男</label></li>
                                        <li>
                                            <label><input name="sex" value="2" type="radio" <?php if($student['sex']==2){ echo 'checked'; } ?>>女</label></li>
                                    </ul>

                                </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>所在部门</th>
                                <td><span class="iptInner">
                                    <select name="department_parent_id" class="iptH37 w250">
                                        <option value="">请选择</option>
                                        <?php foreach($departments as $d){ ?>
                                            <option <?php if((!empty($student['department_parent_id'])&&$d['id']==$student['department_parent_id'])||empty($student['department_parent_id'])&&($d['id']==$current_department['id']||$d['id']==$current_department['parent_id'])){ ?>selected="selected"<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                                        <?php } ?>
                                    </select>&nbsp;
                                    <select <?php if(count($second_departments)<=0){?>style="display: none;"<?php } ?> name="department_id" class="iptH37 w250">
                                        <option value="<?php echo !empty($current_department['parent_id'])?$current_department['parent_id']:$current_department['id'] ?>" selected >请选择</option>
                                        <?php foreach($second_departments as $d){ ?>
                                            <option <?php if($d['id']==$student['department_id']||$d['id']==$current_department['id']){ ?>selected<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                    <th>学员工号</th>
                                    <td>
                                        <input name="job_code" value="<?php echo $student['job_code'] ?>" type="text" class="iptH37 w250">

                                    </td>
                            </tr>
                            <tr>
                                    <th>职位名称</th>
                                    <td>
                                        <input name="job_name" value="<?php echo $student['job_name'] ?>" type="text" class="iptH37 w250">


                                    </td>
                            </tr>
                            <tr>
                                <th>电子邮件</th>

                                <td><span class="iptInner">
                                    <input name="email" value="<?php echo $student['email'] ?>" type="text" class="iptH37 w250">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                    <th><span class="red">*</span>手机号码</th>

                                    <td>
                                        <span class="iptInner">
                                        <input name="mobile" value="<?php echo $student['mobile'] ?>" type="text" class="iptH37 w250" <?php if($student['role']==9){ echo ' readonly'; }?> autocomplete="off" >
                                        <input name="user_name" value="<?php echo $student['user_name'] ?>" type="hidden" class="iptH37 w250" autocomplete="off" >
                                        </span>

                                    </td>
                            </tr>
                            <tr>
                                <th><span class="red">*</span>登录密码</th>
                                <td><span class="iptInner">
                                        <input name="student_pass" value="<?php echo $student['user_pass'] ?>" type="hidden" >
                                        <input id="student_pass" value="<?php echo $student['user_pass'] ?>" type="password" class="iptH37 w250" autocomplete="off" ><br><span style="color:#ccc">默认手机号码后6位</span>
                                        </span>

                                </td>
                            </tr>
                            <tr>
                                <th>角色</th>
                                <td>
                                    <?php if($student['role']==9){?>
                                        <label><input name="role" value="9" type="hidden" />系统管理员</label>
                                    <?php }else{?>
                                        <label><input name="role" value="1" type="radio" checked class="mr10" />普通学员</label>
                                        <label><input name="role" value="2" type="radio" <?php if($student['role']==2){echo 'checked';} ?> class="mr10" />助理管理员<span class="gray9 f14">(公司培训负责人)</span> </label>
                                        <label><input name="role" value="3" type="radio" <?php if($student['role']==3){echo 'checked';} ?> class="mr10" />员工经理<span class="gray9 f14">(部门负责人、部门经理)</span> </label>
                                    <?php } ?>
                                </td>
                            </tr>

                            <tr>
                                <th></th>
                                <td>

                                    <input type="submit" value="保存" class="coBtn">
                                </td>
                            </tr>

                    </tbody></table>
                    </form>
            </div>

    </div>
</div>

<!--tankuang de yangshi -->
<div id="conWindow" style="z-index: 99999; display: none;" class="popWinBox">
        <div class="pop_div" style="z-index: 100001;">
                <div class="title_div"><a class="closeBtn" href="javascript:;"><i class="fa fa-close fa-lg"></i></a><span id="title_divSpan" class="title_divText">增加一级部门</span> </div>
                <div id="conMessage" class="pop_txt01">
                        <table class="comTable">
                                <col width="150" />
                                <tr>
                                        <th>部门名称</th>
                                        <td class="aLeft">
                                            <input name="act" value="add" type="hidden" >
                                            <input name="departid" type="hidden" >
                                            <input name="departname" type="text" class="ipt w250"></td>
                                </tr><tr>
                                        <th></th>
                                        <td class="aLeft"><a jsbtn="okBtn" href="javascript:;" class="okBtn">保存设置</a></td>
                                </tr>
                        </table>


                </div>

        </div>
        <div class="popmap" style="z-index: 100000;"></div>
</div>
