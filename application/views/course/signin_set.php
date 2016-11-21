<script type="text/javascript">
    $(document).ready(function(){
        $( "#editForm" ).validate( {
            rules: {
                signin_start: {
                    required: true
                },
                signin_end: {
                    required: true,
                    compareDate: "input[name=signin_start]"
                }
            },
            messages: {
                signin_start: {
                    required: "请输入签到开始时间"
                },
                signin_end: {
                    required: "请输入签到结束时间",
                    compareDate:"结束时间不能早于开始时间"
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
            }
        });

        $('#signin_end').focus(function(){
            $(this).val($.trim($(this).val())==''?$('#signin_start').val():$(this).val());
        });
        $('#signout_end').focus(function(){
            $(this).val($.trim($(this).val())==''?$('#signout_start').val():$(this).val());
        });
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
<?php if($loginInfo['role']==1||$roleInfo['signinset']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/signinset/'.$course['id']) ?>">签到设置<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['signinlist']==1){ ?>
                                        <li><a href="<?php echo site_url('course/signinlist/'.$course['id']) ?>">签到名单</a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                            <?php if (!empty($msg)) {?>
                                <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
                            <?php } ?>
                        <form id="editForm" method="post" action="">
                            <input name="act" type="hidden" value="act" />
                                <table cellspacing="0" class="comTable">
                                        <colgroup>
                                                <col width="100">
                                        </colgroup>
                                        <tbody>
                                                <tr>
                                                        <th><span class="red">*</span>开启签到</th>
                                                        <td>
                                                                <ul class="lineUl">
                                                                        <li>
                                                                            <input name="issignin_open" checked="" value="1" type="radio">开启</li>
                                                                        <li>
                                                                            <input name="issignin_open" <?php if($course['issignin_open']==2){echo 'checked="checked"';} ?> value="2" type="radio">关闭</li>
                                                                </ul>

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th><span class="red">*</span>签到时段</th>
                                                        <td><span class="iptInner">
                                                                <input type="text" id="signin_start" name="signin_start" value="<?php echo !empty($course['signin_start'])?date("Y-m-d H:i",strtotime($course['signin_start'])):'' ?>" class="iptH37 DTdate" autocomplete="off"> 至 <input id="signin_end" name="signin_end" value="<?php echo !empty($course['signin_end'])?date("Y-m-d H:i",strtotime($course['signin_end'])):'' ?>" type="text" class="iptH37 DTdate" autocomplete="off">
                                                            </span>

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>签到二维码</th>
                                                        <td><img src="<?php echo base_url().'uploads/course_qrcode/'.$course['signin_qrcode'].'.png' ?>" height="120" /><p class="aCenter blue" style="width:120px"><a class="blue" href="<?php echo site_url('course/downloadqrcode/'.$course['id']) ?>?type=signin" target="_blank" >下载</a></p>
                                                        </td>
                                                </tr>
                                                <tr><td colspan="2"><p class="red">签退无需求可不设置</p></td></tr>
                                                <tr>
                                                        <th>签退时段</th>
                                                        <td><span class="iptInner">
                                                            <input type="text" id="signout_start" name="signout_start" value="<?php echo !empty($course['signout_start'])?date("Y-m-d H:i",strtotime($course['signout_start'])):'' ?>" class="iptH37 DTdate" autocomplete="off"> 至 <input id="signout_end" name="signout_end" value="<?php echo !empty($course['signout_end'])?date("Y-m-d H:i",strtotime($course['signout_end'])):'' ?>" type="text" class="iptH37 DTdate" autocomplete="off">
                                                            </span>
                                                            

                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>签退二维码</th>
                                                        <td><img src="<?php echo base_url().'uploads/course_qrcode/'.$course['signout_qrcode'].'.png' ?>" height="120" /><p class="aCenter" style="width:120px"><a class="blue" href="<?php echo site_url('course/downloadqrcode/'.$course['id']) ?>?type=signout" target="_blank" >下载</a></p>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th></th>
                                                        <td>
                                                                <input type="submit" class="coBtn" value="保存">
                                                        </td>
                                                </tr>
                                        </tbody>
                                </table>
                            </form>
                        </div>

                </div>

        </div>
</div>