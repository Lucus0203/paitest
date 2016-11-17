<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>培训派</title>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/common.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/login.css"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery1.83.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.placeholder.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.validate.min.1.8.0.1.js"></script>
    <script type="text/javascript">
        var _hmt = _hmt || [];
        (function () {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?9432a72cc245c2b9cafed658f471d489";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
        $(document).ready(function () {
            if (!+[1,]) {
                $('.ieTxt').show();
            } else {
                $('.ieTxt').hide()
            }
            $('input, textarea').placeholder();
            // 手机号码验证
            jQuery.validator.addMethod("isMobile", function (value, element) {
                var length = value.length;
                var mobile = /^((1[0-9][0-9])+\d{8})$/;
                return this.optional(element) || (length == 11 && mobile.test(value));
            }, "请正确填写您的手机号码");
            $("#signupForm").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    user_pass: {
                        required: true,
                        minlength: 6
                    },
                    password_confirm: {
                        required: true,
                        equalTo: "input[name=user_pass]"
                    },
                    company_name: {
                        required: true
                    },
                    industry_parent_id:{
                        required: true
                    },
                    industry_id:{
                        required: true
                    },
                    real_name: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        isMobile: true
                    },
                    mobile_code: {
                        required: true
                    },
                    invitation_code: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: "请输入您的邮箱地址",
                        email: "请输入正确的邮箱地址"
                    },
                    user_pass: {
                        required: "请输入密码",
                        minlength: "密码的长度要大于6个字符"
                    },
                    password_confirm: {
                        required: "请再输入一次密码",
                        equalTo: "两次密码不一致"
                    },
                    company_name: {
                        required: "请输入您的企业名称"
                    },
                    industry_parent_id:{
                        required: "请选择所属行业"
                    },
                    industry_id:{
                        required: "请选择行业领域"
                    },
                    real_name: {
                        required: "请输入您的姓名"
                    },
                    mobile: {
                        required: "请输入您的手机号码",
                        isMobile: "请输入正确的手机号码"
                    },
                    mobile_code: {
                        required: "请输入短信验证码",
                        digits: "请输入正确的短信验证码"
                    },
                    invitation_code: {
                        required: "请输入邀请码"
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

            $('#get_captcha_btn,#get_captcha').click(function(){
                $.ajax({
                    type: "get",
                    url: '<?php echo site_url('login/updateCaptcha') ?>',
                    success: function (res) {
                        $('#get_captcha').html(res);
                    }
                })
            });
            $('#get_mobile_code').click(function () {
                var email = $('#email').val();
                var mobile = $('#mobile').val();
                var captcha = $('#captcha').val();
                if ($.trim(email)=='') {
                    alert('请输入邮箱账号！');
                    $('#email').focus();
                    return false;
                }
                if ($.trim(mobile)=='') {
                    alert('请输入手机号码！');
                    $('#mobile').focus();
                    return false;
                }
                if ($.trim(captcha)=='') {
                    alert('请输入验证码！');
                    $('#captcha').focus();
                    return false;
                }
                if (ismobile(mobile) && $('#get_mobile_code').attr('rel') <= 0) {
                    $.ajax({
                        type: "post",
                        url: '<?php echo site_url('login/getcode') ?>',
                        data: {'mobile': mobile, 'email': email,'captcha':captcha},
                        success: function (res) {
                            if (res == 1) {
                                alert('验证码已发送,请注意查收')
                                $('#get_mobile_code').css('background-color', '#ccc').text('重新获取验证码60').attr('rel', '60');
                                remainsecondes = 60;
                                timing()
                            } else {
                                alert(res);
                            }
                        }
                    })
                }
                return false;

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
        });
        function timing() {
            if (remainsecondes > 0) {
                setTimeout(function () {
                    remainsecondes--;
                    $('#get_mobile_code').text('重新获取验证码' + remainsecondes).attr('rel', remainsecondes);
                    timing();
                }, 1000);
            } else {
                $('#get_mobile_code').css('background-color', '#67d0de').text('获取短信验证码').attr('rel', 0);
                $('#get_captcha_btn').trigger('click');
            }
        }
        function ismobile(mobile) {
            var myreg = /^0?1[0-9][0-9]\d{8}$/;
            if (!myreg.test(mobile)) {
                alert('请输入有效的手机号码！');
                $('input [name=mobile]').focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
<div class="loginReg">
    <div class="header"><img src="<?php echo base_url(); ?>images/logo_login.png" alt="">
        <p class="aRight">已注册会员？<a href="<?php echo site_url('login/index') ?>">请登录 </a></p>

    </div>
    <div class="logCont">
        <div class="tit">企业管理员注册</div>
        <div class="logInner">
            <p class="red f14 mb10 aCenter"><?php echo $msg ?></p>
            <form id="signupForm" action="" method="post">
                <input type="hidden" name="act" value="act"/>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="email" id="email" value="<?php echo $user['email'] ?>" class="ipt"
                               placeholder="您的邮箱地址" autocomplete="off" />
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="password" name="user_pass" value="<?php echo $user['user_pass'] ?>" class="ipt" placeholder="请输入密码" autocomplete="off" />
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="password" name="password_confirm" value="<?php echo $user['user_pass'] ?>" class="ipt" placeholder="请再输入一次密码" autocomplete="off" />
                    </div>
                </div>

                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="company_name" value="<?php echo $user_company_name ?>" class="ipt"
                               placeholder="企业注册名称"/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <select id="industry_parent_id" name="industry_parent_id" class="iptH37 mr5">
                            <option value="">请选择所属行业</option>
                            <?php foreach ($industry_parent as $pindus) { ?>
                                <option value="<?php echo $pindus['id'] ?>" <?php if($pindus['id']==$user_industry_parent['id']){ ?>selected<?php } ?> ><?php echo $pindus['name'] ?></option>
                            <?php } ?>
                        </select>
                        <select id="industry_id" name="industry_id" class="iptH37 w156">
                            <option value="">请选择行业领域</option>
                            <?php if(!empty($user_industry_id)){
                                    foreach ($user_industrys as $ind){
                                ?>
                                <option value="<?php echo $ind['id'] ?>" <?php if($ind['id']==$user_industry_id){ ?>selected<?php } ?> ><?php echo $ind['name'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="real_name" value="<?php echo $user['real_name'] ?>" class="ipt"
                               placeholder="您的姓名"/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" id="mobile" value="<?php echo $user['mobile'] ?>" name="mobile" class="ipt"
                               placeholder="手机号码" autocomplete="off" />
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" id="captcha" value="" class="ipt w157" placeholder="验证码 "/><a id="get_captcha_btn" href="javascript:void(0);" class="blue fRight f14 pt10">换一个?</a><a id="get_captcha" href="javascript:void(0)" class="captchaBtn" rel="0"><?php echo $cap['image'] ?></a>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" name="mobile_code" value="<?php echo $user['mobile_code'] ?>"
                               class="ipt w157" placeholder="短信验证码" autocomplete="off"/>
                        <a id="get_mobile_code" href="javascript:void(0)" class="coBtn fRight" rel="0">获取短信验证码</a>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="text" id="invitation_code" value="<?php echo $user['invitation_code'] ?>" name="invitation_code" class="ipt" placeholder="您的邀请码 "/>
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="submit" value="注册" class="blueBtn"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<p class="ieTxt"><span onclick="$('.ieTxt').hide()">X</span>您目前使用的浏览器无法获得最好的培训管理体验，建议您使用谷歌Chrome浏览器、360浏览器、猎豹浏览器和IE10等
</p>
