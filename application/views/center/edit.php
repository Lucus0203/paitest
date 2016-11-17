<script type="text/javascript">
    $(document).ready(function () {
        $('.logTabUl li').click(function () {
            var i = $(this).index();
            var lf = parseInt($(this).offset().left - $('.logTabUl').offset().left);
            var w = parseInt($(this).css('width'));
            $('.tabLine').css({'left': lf+'px', 'width': w+'px'});
            $('.tableBox').hide().eq(i).show();
            $('.alertBox').hide().eq(i).show();
        });
        $('#fileBtn').change(function () {
            // 检查是否为图像类型
            var simpleFile = document.getElementById("fileBtn").files[0];
            if (!/image\/\w+/.test(simpleFile.type)) {
                alert("请确保文件类型为图像类型");
                return false;
            }
            var reader = new FileReader();
            // 将文件以Data URL形式进行读入页面
            reader.readAsDataURL(simpleFile);
            reader.onload = function (e) {
                if ($('#fileBtn').prev().length) {
                    $('#fileBtn').prev().attr('src', this.result);
                } else {
                    $('#fileBtn').before('<img src="' + this.result + '" width="200" />')
                }
            }
        });
        $('#industry_parent_id').change(function () {
            var parent_id = $(this).val();
            $.ajax({
                type: "post",
                url: '<?php echo site_url('ajax/getIndustries') ?>',
                data: {'parent_id': parent_id},
                success: function (res) {
                    var json_obj = $.parseJSON(res);
                    var str = '<option value="">请选择行业领域</option>';
                    $.each(json_obj, function (i, item) {
                        str += '<option value="' + item.id + '">' + item.name + '</option>';
                    });
                    $('#industry_id').html(str);

                }
            })
        });
        // 手机号码验证
        jQuery.validator.addMethod("isMobile", function (value, element) {
            var length = value.length;
            var mobile = /^((1[0-9][0-9])+\d{8})$/;
            return this.optional(element) || (length == 11 && mobile.test(value));
        }, "请正确填写您的手机号码");
        $("#companyeditForm").validate({
            rules: {
                name: {
                    required: true
                },
                industry_parent_id: {
                    required: true
                },
                industry_id: {
                    required: true
                },
                logo: {
                    accept: "image/*",
                    filesize: 5 * 1048576
                },
                contact: {
                    required: true
                },
                mobile: {
                    required: true,
                    isMobile: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {

                name: {
                    required: "请输入公司名称"
                },
                industry_parent_id: {
                    required: "请选择所属行业"
                },
                industry_id: {
                    required: "请选择行业领域"
                },
                logo: {
                    accept: "图片格式不正确",
                    filesize: "图片大小不能超过5M"
                }, contact: {
                    required: "请输入联系人"
                },
                mobile: {
                    required: "请输入手机号",
                    ismobile: "手机号不正确"
                },
                email: {
                    required: "请输入邮箱",
                    email: "邮箱格式不正确"
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
            }
        });

        $("#editForm").validate({
            rules: {
                cur_pass: {
                    required: true
                },
                new_pass: {
                    required: true,
                    minlength: 6
                },
                repeat_pass: {
                    required: true,
                    equalTo: "input[name=new_pass]"
                }
            },
            messages: {
                cur_pass: {
                    required: "请输入当前密码"
                },
                new_pass: {
                    required: "请输入新密码",
                    minlength: "密码的长度要大于6个字符"
                },
                repeat_pass: {
                    required: "请再输入一次密码",
                    equalTo: "两次密码不一致"
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
            }
        });
        <?php if($loginInfo['role']==1&&$tab==2){?>$('.tabLine').css('left', '123px');
        <?php }elseif($loginInfo['role']==1&&$tab==3){ ?>$('.tabLine').css('left', '229px');<?php } ?>
    })
</script>
<div class="wrap">
    <div class="comBox p15">

        <div class="logTab">
            <ul class="logTabUl">
                <li>公司信息</li>
                <li>密码修改</li>
                <?php if ($loginInfo['role'] == 1) { ?>
                    <li>权限设置</li><?php } ?>
            </ul>
            <p class="tabLine" style="left: 17px; width: 72px;"></p>
        </div>
        <?php if (!empty($msg)) {?>
            <p class="alertBox <?php echo $success=='ok'?'alert-success':'alert-danger' ?> "><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox tableBox01" <?php if ($tab != 1) {
            echo 'style="display:none"';
        } ?> >
            <form id="companyeditForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="info"/>
                <table cellspacing="0" class="comTable">
                    <col width="15%"/>
                    <tr>
                        <th>公司编号</th>
                        <td>
                            <?php echo $user['company_code'] ?><span class="gray9">(系统分配)</span>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>公司名称</th>
                        <td><?php if ($loginInfo['role'] == 1) { ?>
                            <span class="iptInner">
                                <input name="name" value="<?php echo $company['name'] ?>" type="text"
                                       class="iptH37 w345"></span>
                            <?php } else { ?>
                                <?php echo $company['name'] ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>所属行业</th>
                        <td>
                            <span class="iptInner">
                                <select id="industry_parent_id" name="industry_parent_id" class="iptH37">
                                <option value="">请选择所属行业</option>
                                <?php foreach ($industry_parent as $pindus) { ?>
                                    <option
                                        value="<?php echo $pindus['id'] ?>" <?php if ($company['industry_parent_id'] == $pindus['id']) {
                                        echo 'selected="selected"';
                                    } ?> ><?php echo $pindus['name'] ?></option>
                                <?php } ?>
                            </select>
                            <select id="industry_id" name="industry_id" class="iptH37">
                                <option value="">请选择行业领域</option>
                                <?php foreach ($industry as $indus) { ?>
                                    <option
                                        value="<?php echo $indus['id'] ?>" <?php if ($company['industry_id'] == $indus['id']) {
                                        echo 'selected="selected"';
                                    } ?> ><?php echo $indus['name'] ?></option>
                                <?php } ?>
                            </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>公司Logo</th>
                        <td>
                                            <span>
                                                    <?php if (!empty($company['logo'])) { ?><img width="200"
                                                                                                 src="<?php echo base_url() . 'uploads/company_logo/' . $company['logo'] ?>" /><?php } ?>
                                                <?php if ($loginInfo['role'] == 1){ ?>
                                                <input name="logo" type="file"
                                                       style="<?php if (empty($company['logo'])) { ?>visibility: hidden;<?php } else { ?>display:none<?php } ?>"
                                                       id="fileBtn"/><br>
                                        <a href="javascript:;" onclick="$('#fileBtn').click()" class="borBlueH37 mb10">上传logo</a>
                                            </span>
                            <p class="gray9">JPG、PNG、GIF格式图片，大小不超过5M</p>

                            <?php } ?>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>联系人</th>
                        <td>
                            <span class="iptInner">
                            <input name="contact" value="<?php echo $user['real_name'] ?>" type="text"
                                   class="iptH37 w345"></span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>手机号码</th>
                        <td>
                            <span class="iptInner">
                            <input name="mobile" value="<?php echo $user['mobile'] ?>" type="text" class="iptH37 w345">
                                </span>
                        </td>
                    </tr>

                    <tr>
                        <th>电话号码</th>
                        <td>
                            <span class="iptInner">
                            <input name="tel" value="<?php echo $logininfo['tel'] ?>" type="text" class="iptH37 w345">
                                </span>
                        </td>
                    </tr>

                    <tr>
                        <th><span class="red">*</span>电子邮件</th>
                        <td>
                            <span class="iptInner">
                            <input name="email" value="<?php echo $user['email'] ?>" type="text" class="iptH37 w345">
                                </span>
                        </td>
                    </tr>


                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="保存" class="coBtn">

                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="tableBox tableBox01" <?php if ($tab != 2) {
            echo 'style="display:none"';
        } ?>>
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="pass"/>
                <table cellspacing="0" class="comTable">
                    <col width="15%"/>
                    <tr>
                        <th>登录账号</th>
                        <td>
                            <?php echo $user['user_name'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>当前密码</th>
                        <td>
                            <span class="iptInner">
                            <input name="cur_pass" type="password" class="iptH37 w345">
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>新密码</th>
                        <td><span class="iptInner">
                            <input name="new_pass" type="password" class="iptH37 w345">
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>重复新密码</th>
                        <td>
                            <span class="iptInner">
                            <input name="repeat_pass" type="password" class="iptH37 w345">
                                </span>
                        </td>
                    </tr>


                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="保存" class="coBtn">

                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php if ($loginInfo['role'] == 1) { ?>
            <div class="tableBox tableBox01" <?php if ($tab != 3) {
                echo 'style="display:none"';
            } ?>>
                <form id="editForm" method="post" action="">
                    <input name="act" type="hidden" value="purview"/>
                    <table cellspacing="0" class="listTable">
                        <tbody>
                        <tr>
                            <th class="aLeft">一级模块</th>
                            <th class="aLeft">二级模块</th>
                            <th class="aLeft">三级模块</th>
                            <th>助理管理员</th>
                            <th>员工经理</th>
                        </tr>
                        <tr>
                            <td rowspan="14">课程管理</td>
                            <td rowspan="5">课程管理</td>
                            <td>列表</td>
                            <td class="aCenter"><input name="role2[courselist]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['courselist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[courselist]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['courselist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>查看</td>
                            <td class="aCenter"><input name="role2[courseinfo]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['courseinfo'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[courseinfo]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['courseinfo'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>新增</td>
                            <td class="aCenter"><input name="role2[coursecreate]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['coursecreate'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[coursecreate]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['coursecreate'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>编辑</td>
                            <td class="aCenter"><input name="role2[courseedit]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['courseedit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[courseedit]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['courseedit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>删除</td>
                            <td class="aCenter"><input name="role2[coursedel]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['coursedel'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[coursedel]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['coursedel'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td rowspan="3">报名管理</td>
                            <td>报名设置</td>
                            <td class="aCenter"><input name="role2[applyset]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['applyset'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[applyset]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['applyset'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>名单管理</td>
                            <td class="aCenter"><input name="role2[applylist]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['applylist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[applylist]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['applylist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>通知设置</td>
                            <td class="aCenter"><input name="role2[notifyset]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['notifyset'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[notifyset]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['notifyset'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td rowspan="2">签到管理</td>
                            <td>签到设置</td>
                            <td class="aCenter"><input name="role2[signinset]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['signinset'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[signinset]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['signinset'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>签到名单</td>
                            <td class="aCenter"><input name="role2[signinlist]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['signinlist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[signinlist]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['signinlist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td rowspan="2">课前调研</td>
                            <td>调研编辑</td>
                            <td class="aCenter"><input name="role2[surveyedit]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['surveyedit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[surveyedit]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['surveyedit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>提交名单</td>
                            <td class="aCenter"><input name="role2[surveylist]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['surveylist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[surveylist]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['surveylist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td rowspan="2">课程反馈</td>
                            <td>问题设置</td>
                            <td class="aCenter"><input name="role2[ratingsedit]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['ratingsedit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[ratingsedit]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['ratingsedit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>反馈结果</td>
                            <td class="aCenter"><input name="role2[ratingslist]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['ratingslist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[ratingslist]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['ratingslist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <th colspan="5"></th>
                        </tr>
                        <tr>
                            <td rowspan="5">讲师资源</td>
                            <td rowspan="5">讲师管理</td>
                            <td>列表</td>
                            <td class="aCenter"><input name="role2[teacherlist]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['teacherlist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[teacherlist]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['teacherlist'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>信息</td>
                            <td class="aCenter"><input name="role2[teacherinfo]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['teacherinfo'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[teacherinfo]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['teacherinfo'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>新增</td>
                            <td class="aCenter"><input name="role2[teachercreate]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['teachercreate'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[teachercreate]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['teachercreate'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>编辑</td>
                            <td class="aCenter"><input name="role2[teacheredit]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['teacheredit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[teacheredit]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['teacheredit'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>删除</td>
                            <td class="aCenter"><input name="role2[teacherdel]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['teacherdel'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[teacherdel]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['teacherdel'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <th colspan="5"></th>
                        </tr>
                        <tr>
                            <td rowspan="2">组织与学员</td>
                            <td>组织管理</td>
                            <td>部门编辑/新增</td>
                            <td class="aCenter"><input name="role2[department]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['department'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[department]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['department'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>
                        <tr>
                            <td>员工管理</td>
                            <td>员工编辑/新增</td>
                            <td class="aCenter"><input name="role2[student]" value="1"
                                                       type="checkbox" <?php if ($role['role2']['student'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                            <td class="aCenter"><input name="role3[student]" value="1"
                                                       type="checkbox" <?php if ($role['role3']['student'] == 1) {
                                    echo 'checked';
                                } ?> /></td>
                        </tr>

                        </tbody>
                    </table>
                    <p class="aCenter p40"><input type="submit" value="保存" class="coBtn"></p>
                </form>
            </div>
        <?php } ?>


    </div>
</div>