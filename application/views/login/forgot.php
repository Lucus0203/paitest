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
                    company_code: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        isMobile: true
                    },
                    mobile_code: {
                        required: true
                    },
                    user_pass: {
                        required: true
                    }
                },
                messages: {
                    company_code: {
                        required: "请输入您的公司编号",
                    },
                    mobile: {
                        required: "请输入您的手机号码",
                        isMobile: "请输入正确的手机号码"
                    },
                    mobile_code: {
                        required: "请输入短信验证码"
                    },
                    user_pass: {
                        required: "请输入新密码"
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
                var company_code = $('#company_code').val();
                var mobile = $('#mobile').val();
                var captcha = $('#captcha').val();
                if ($.trim(company_code)=='') {
                    alert('请输入公司编号！');
                    $('#company_code').focus();
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
                        url: '<?php echo site_url('login/getcode/forgot') ?>',
                        data: {'mobile': mobile, 'company_code': company_code,'captcha':captcha},
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
        <p class="aRight">想起密码？<a href="<?php echo site_url('login/index') ?>">请登录 </a></p>
    </div>
    <div class="logCont">
        <div class="tit">忘记密码</div>
        <div class="logInner">
            <p class="red f14 mb10 aCenter"><?php echo $msg ?></p>
            <?php if($success=='ok'){?>
            <div class="iptBox aCenter p40">
                <a class="borBlueH37" href="<?php echo site_url('login/index') ?>">返回登录</a>
            </div>
            <?php }else{ ?>
            <form id="signupForm" action="" method="post">
                <div style="visibility: hidden;height: 0;"><input type="text"> <input type="password"></div>
                <input type="hidden" name="act" value="act"/>
                <input type="hidden" id="sacount" value="<?php echo $sacount ?>" />
                <div class="iptBox" <?php if(empty($sacount)){echo 'style="display:none;"';} ?> >
                    <div class="iptInner">
                        <input type="text" name="company_code" id="company_code" value="<?php echo empty($sacount)?'sacount':$user['company_code'] ?>" class="ipt" placeholder="公司编号" autocomplete="off" />
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
                        <input type="password" name="user_pass" value="<?php echo $user['user_pass'] ?>" class="ipt" placeholder="请输入新密码" autocomplete="off" />
                    </div>
                </div>
                <div class="iptBox">
                    <div class="iptInner">
                        <input type="submit" value="修改密码" class="blueBtn"/>
                    </div>
                </div>
            </form>
            <?php } ?>
        </div>
    </div>

</div>

<p class="ieTxt"><span onclick="$('.ieTxt').hide()">X</span>您目前使用的浏览器无法获得最好的培训管理体验，建议您使用谷歌Chrome浏览器、360浏览器、猎豹浏览器和IE10等
</p>
