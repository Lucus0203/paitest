<script type="text/javascript">
        $(document).ready(function(){
                $('[js="addZuoye"]').click(function(){
                    $('.zuoyeList').append('<li><span class="num"></span><input name="surveys[]" type="text" class="iptH37 w600 ml10" value=""><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a></li>');
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
<?php if($loginInfo['role']==1||$roleInfo['surveyedit']==1){ ?>
                                        <li class="cur"><a href="<?php echo site_url('course/surveyedit/'.$course['id']) ?>">调研编辑<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
<?php } ?>
<?php if($loginInfo['role']==1||$roleInfo['surveylist']==1){ ?>
                                        <li><a href="<?php echo site_url('course/surveylist/'.$course['id']) ?>">提交名单</a></li>
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
                                <?php if(count($surveys)==0){ ?><p class="f14 mb20 gray6">本课程暂未创建课前调研，请通过以下模板进行创建</p><?php } ?>
                                <?php if($anstotal>0){?><p class="yellowTipBox mb20">已有学员提交，修改问题后需要学员重新填写</p><?php } ?>
                                <ul class="zuoyeList">
                                    <?php foreach ($surveys as $k=>$h){ ?>
                                        <li><span class="num"><?php echo ($k+1) ?></span><input name="surveys[]" type="text" class="iptH37 w600 ml10" value="<?php echo $h['title'] ?>"><a href="javascript:;" js='removeZuoye' class="blue ml10">删除</a>
                                        </li>
                                    <?php } ?>
                                    <?php if(count($surveys)==0){ ?>
                                        <li><span class="num">1</span><input name="surveys[]" type="text" class="iptH37 w600 ml10" value="请描述培训要解决的主要问题？"><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a>
                                        </li>
                                        <li><span class="num">2</span><input name="surveys[]" type="text" class="iptH37 w600 ml10" value="您期望的培训效果？"><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a>
                                        </li>
                                        <li><span class="num">3</span><input name="surveys[]" type="text" class="iptH37 w600 ml10" value=""><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a>
                                        </li>
                                    <?php }else{ ?>
                                        <li><span class="num"><?php echo ($k>=0)?($k+2):1 ?></span><input name="surveys[]" type="text" class="iptH37 w600 ml10" value=""><a href="javascript:;" js="removeZuoye" class="blue ml10">删除</a>
                                        </li>
                                    <?php } ?>
                                </ul>

                                <div><a href="javascript:;" class="borBlueH37" js="addZuoye">添加问题</a></div>
                                
                                <div class="aCenter"><input type="submit" class="coBtn" value="保存" /></div>
                        </form>
                        </div>

                </div>

        </div>
    </div>
