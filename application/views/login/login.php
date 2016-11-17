<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>培训派</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/common.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/login.css" />
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.placeholder.min.js"></script>
    <script type="text/javascript">
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?9432a72cc245c2b9cafed658f471d489";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
        $(document).ready(function(){
            $('input, textarea').placeholder();
            $('.logTabUl li').click(function(){
                var i=$(this).index();
                var lf=parseInt($(this).offset().left-$('.logTab').offset().left);
                var w=parseInt($(this).css('width'));
                $('.tabLine').css({'left':lf+'px','width':w+'px'});
                $('.logInner').hide().eq(i).show();
                $('.red').hide();
            })
        })
    </script>
</head>

<body>
<div class="loginReg">
    <div class="header"><img src="<?php echo base_url();?>images/logo_login.png" alt="">
        <p class="aRight">还没有账号？<a href="<?php echo site_url('login/register') ?>">立即注册</a></p>

    </div>
    <div class="logCont">
        <div class="tit">登录培训派</div>
        <div class="logTab">
            <ul class="logTabUl">
                <li>管理员登录</li>
                <li>分级管理员登录</li>
            </ul>
            <p class="tabLine"></p>
        </div>
        <div class="logInner" <?php if($act=='act2'){ ?> style="display:none;" <?php } ?>>
            <form method="post" action="">
                <input type="hidden" name="act" value="act1" />
                <p class="red f14 mb10 aCenter"><?php echo $error_msg; ?></p>
                <div class="iptBox">
                    <input name="user_name" type="text" value="" class="ipt" placeholder="用户名/邮箱地址/手机号" />
                </div>
                <div class="iptBox">
                    <input name="user_pass" type="password" value="" class="ipt" placeholder="密码" />
                    <p class="aRight mt5"><a class="blue" href="<?php echo site_url('login/forgot') ?>">忘记密码？</a></p>
                </div>
                <div class="iptBox">
                    <input type="submit" value="提交" class="blueBtn" />
                </div>
            </form>
        </div>
        <div class="logInner" <?php if($act!='act2'){ ?> style="display:none;" <?php } ?> >
            <form method="post" action="">
                <input type="hidden" name="act" value="act2" />
                <p class="red mb10 aCenter f14"><?php echo $error_msg; ?></p>
                <div class="iptBox">
                    <input name="company_code" type="text" value="" class="ipt" placeholder="公司编号" />
                </div>
                <div class="iptBox">
                    <input name="user_name" type="text" value="" class="ipt" placeholder="手机号" />
                </div>
                <div class="iptBox">
                    <input name="user_pass" type="password" value="" class="ipt" placeholder="密码" />
                    <p class="aRight mt5"><a class="blue" href="<?php echo site_url('login/forgot/sacount') ?>">忘记密码？</a></p>
                </div>
                <div class="iptBox">
                    <input type="submit" value="提交" class="blueBtn"/>
                </div>
            </form>
        </div>
    </div>

</div>