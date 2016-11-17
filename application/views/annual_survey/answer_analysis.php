<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/kecheng.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/texture.css"/>
<script>
    $(document).ready(function(){
        $('.sideLeft').scrollToFixed({
            marginTop: $('.baoming').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
        $(".sideLeft a").click(function() {
            var obj=$(this).attr('href');
            $('.moduleBox').hide()
            $(obj).show();
            $(".sideLeft li").removeClass('cur').find('i').remove();
            $(this).append('<i class="ml10 fa fa-angle-right fa-lg"></i>').parent().addClass('cur');
            return false;
        });
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
                    var str='<option value="">所有下级部门</option>';
                    $.each(json_obj.departs,function(i,item){
                        str+='<option value="'+item.id+'">'+item.name+'</option>';
                        ++count;
                    });
                    if(count>0){
                        $('select[name=department_id]').html(str).parent().show();
                    }else{
                        $('select[name=department_id]').html('<option value="" selected >所有下级部门</option>').parent().hide();
                    }
                }
            });
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <?php $this->load->view ( 'annual_survey/top_tit' ); ?>
    </div>

    <div class="topNaviKec01">
        <?php $this->load->view ( 'annual_survey/top_navi' ); ?>
    </div>

    <div class="comBox">
        <div class="seachBox clearfix">
            <form method="get" action="">
                <ul>
                    <li class="w215">
                        <input name="keyword" type="text" value="<?php echo $parm['keyword'] ?>" class="ipt w215" placeholder="题目关键字">
                    </li>
                    <li class="w215">
                        <select name="department_parent_id" class="w215 ipt">
                            <option value="">所有部门</option>
                            <?php foreach($departments as $d){ ?>
                                <option <?php if($d['id']==$parm['department_parent_id']){ ?>selected="selected"<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                            <?php } ?>
                        </select>
                    </li>
                    <li class="w215" <?php if(count($second_departments)<=0){?>style="display: none;"<?php } ?> >
                        <select name="department_id" class="w215 ipt">
                            <option value="" selected >所有下级部门</option>
                            <?php foreach($second_departments as $d){ ?>
                                <option <?php if($d['id']==$parm['department_id']){ ?>selected<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                            <?php } ?>
                        </select>
                    </li>

                    <li class="btn fRight"><input type="submit" class="borBlueH37 mt3" value="搜索" /></li>
                </ul>
            </form>
        </div>
        <div class="baoming">
            <div class="sideLeft">
                <ul class="sideLnavi">
                    <li class="cur" style="padding-left: 20px;"><a href="#acceptance">培训认同度<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
                    <li style="padding-left: 20px;"><a href="#organization">培训组织性</a></li>
                    <li style="padding-left: 20px;"><a href="#requirement">需 求 信 息</a></li>
                    <li style="padding-left: 20px;"><a href="#coursechosen">课 程 选 择</a></li>
                </ul>
            </div>
            <div class="contRight p20">
                <?php foreach ($answer as $ansk=>$a){
                    $qno=1;
                    if($ansk!='courses'){ ?>
                        <div id="<?php echo $ansk ?>" class="moduleBox" <?php if($ansk!='acceptance'){ echo 'style="display: none;"'; }?>>
                        <?php if(count($a)>0){
                            foreach ($a as $ad){?>
                            <p class="ttl01 pt0"><?php echo $qno++.'.'.$ad['title'];?></p>
                            <?php if($ad['type']==1||$ad['type']==2){ ?>
                                <table class="tableA mb20">
                                    <colgroup>
                                        <col width="63%">
                                        <col width="10%">
                                        <col width="27%">
                                    </colgroup>
                                    <tr>
                                        <th>选项</th>
                                        <th>小计</th>
                                        <th>占比</th>
                                    </tr>
                                    <?php foreach ($ad['answer'] as $ansydetail){?>
                                        <tr>
                                            <td><?php echo $ansydetail['option_title']?></td>
                                            <td class="aCenter"><?php echo $ansydetail['num']?></td>
                                            <td><span class="stepList"><em style="width:  <?php echo ($ad['total']==0)?0:round($ansydetail['num']/$ad['total']*100)?>%;"></em></span><?php echo ($ad['total']==0)?0:round($ansydetail['num']/$ad['total']*100)?>%</td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="3">本题有效填写人数 <?php echo $ad['total'] ?></td>
                                    </tr>
                                </table>
                            <?php }elseif($ad['type']==3){?>
                                <table class="tableA mb20">
                                    <colgroup>
                                        <col width="15%">
                                        <col width="85%">
                                    </colgroup>
                                    <tr>
                                        <th>调研对象</th>
                                        <th>答题内容</th>
                                    </tr>
                                    <?php foreach ($ad['answer'] as $ansydetail){?>
                                        <tr>
                                            <td class="aCenter"><?php echo $ansydetail['name']?></td>
                                            <td><?php echo $ansydetail['answer_content']?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="3">本题有效填写人数 <?php echo $ad['total'] ?>
                                            <?php if ($ad['total']>5){ ?><a href="#" class="blue">查看全部答题内容</a><?php } ?></td>
                                    </tr>

                                </table>
                            <?php } ?>
                        <?php }
                        }else{ ?>
                            <p class="ttl01 pt0">暂无符合条件的问题</p>
                        <?php } ?>
                        </div>
                    <?php }else{ ?>
                        <div id="coursechosen" class="moduleBox" style="display: none;">
                            <p class="ttl01 pt0">课程选择</p>
                            <table class="tableA mb20">
                                <colgroup>
                                    <col width="63%">
                                    <col width="10%">
                                    <col width="27%">
                                </colgroup>
                                <tr>
                                    <th>课程</th>
                                    <th>人数</th>
                                    <th>占比</th>
                                </tr>
                                <?php foreach ($a['detail'] as $ad){ ?>
                                    <tr>
                                        <td><?php echo $ad['title']?></td>
                                        <td class="aCenter"><?php echo $ad['num']?></td>
                                        <td><span class="stepList"><em style="width:  <?php echo ($a['total']==0)?0:round($ad['num']/$a['total']*100)?>%;"></em></span><?php echo ($a['total']==0)?0:round($ad['num']/$a['total']*100)?>%</td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="3">有效填写人数 <?php echo $a['total'] ?></td>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>

                <?php } ?>
            </div>
        </div>
    </div>
</div>