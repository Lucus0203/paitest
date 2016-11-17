<script type="text/javascript">
        $(document).ready(function(){
                $('[js="addZuoye"]').click(function(){
                    $('.zuoyeList').append('<li><span class="num"></span><input name="homeworks[]" type="text" class="iptH37 w600 ml10" value=""><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a></li>');
                    $('span.num').each(function(i){
                         $(this).text(i+1);
                    });
                })
                $('[js="removeZuoye"]').live('click',function(){
                    $(this).parent().remove();
                    $('span.num').each(function(i){
                         $(this).text(i+1);
                    });
                });
        })
        function checkform(){
            if($('#anstotal').val()*1>0){
                return confirm('已提交的学员需要重新填写,确认保存吗?');
            }else{
                return true;
            }
        }
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/kecheng.css" />
<div class="wrap">
        <div class="titCom clearfix"><span class="titSpan"><?php echo $course['title'] ?>  </span><span class="<?php echo $course['status_class']; ?> ml20"><?php echo $course['status_str']; ?></span></div>
        <div class="topNaviKec">
                <?php $this->load->view ( 'course/top_navi' ); ?>
        </div>
        <div class="comBox clearfix">
                <div class="baoming">

                        <div class="sideLeft">
                                <ul class="sideLnavi">
<?php if($loginInfo['role']==1||$roleInfo['homeworkedit']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/homeworkedit/'.$course['id']) ?>">作业编辑<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['homeworklist']==1){ ?>
                                        <li><a href="<?php echo site_url('course/homeworklist/'.$course['id']) ?>">提交名单<i></i></a></li>
<?php } ?>
                                </ul>

                        </div>
                        <div class="contRight">
                            <form id="editForm" method="post" action="" onsubmit="return checkform()">
                            <input name="act" type="hidden" value="act" />
                            <input id="anstotal" type="hidden" value="<?php echo $anstotal ?>" />
                                <?php if (!empty($msg)) {?>
                                    <p class="alertBox alert-success mb20"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
                                <?php } ?>
                                <?php if(count($homeworks)==0){ ?><p class="f14 mb20 gray6">本课程暂未创建课前作业，请通过以下模板进行创建</p><?php } ?>
                                <p class="yellowTipBox mb20">如果已有学员提交，修改作业后需要学员重新填写</p>
                                <ul class="zuoyeList">
                                    <?php foreach ($homeworks as $k=>$h){ ?>
                                        <li><span class="num"><?php echo ($k+1) ?></span>
                                            <input name="homeworks[]" type="text" class="iptH37 w600 ml10" value="<?php echo $h['title'] ?>"><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                    <?php } ?>
                                    <?php if(count($homeworks)==0){ ?>
                                        <li><span class="num">1</span>
                                                <input name="homeworks[]" type="text" class="iptH37 w600 ml10" value="员工为什么会努力工作？"><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                        <li><span class="num">2</span>
                                                <input name="homeworks[]" type="text" class="iptH37 w600 ml10" value="企业需要什么样的员工，什么样的员工是优秀的员工？"><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                        <li><span class="num">3</span>
                                            <input name="homeworks[]" type="text" class="iptH37 w600 ml10" value=""><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                    <?php }else{ ?>
                                        <li><span class="num"><?php echo ($k>=0)?($k+2):1 ?></span>
                                            <input name="homeworks[]" type="text" class="iptH37 w600 ml10" value=""><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                    <?php } ?>
                                </ul>

                                <div><a href="javascript:;" class="borBlueH37" js="addZuoye">新增作业题</a></div>
                                
                                <div class="aCenter"><input type="submit" class="coBtn" value="保存" /></div>
                        </form>
                        </div>

                </div>

        </div>
    </div>
