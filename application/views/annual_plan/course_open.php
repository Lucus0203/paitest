<script type="text/javascript">
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                title: {
                    required: true
                },
                people: {
                    required: true,
                    digits: true
                },
                price: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                title: {
                    required: "请输入课程名称"
                },
                people: {
                    required: "请输入课程人次",
                    digits: '请输入数字'
                },
                price: {
                    required: "请输入课程预算",
                    digits: '请输入数字'
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
            submitHandler: function (form) {
                $('input[type=submit]').val('请稍后..').attr('disabled', 'disabled');
                form.submit();
            }
        });
        $('#time_end').focus(function(){
            $(this).val($.trim($(this).val())==''?$('#time_start').val():$(this).val());
        });
        $('input[name=price],input[name=people]').blur(function(){
            var price=$('input[name=price]').val();
            var people=$('input[name=people]').val();
            var avg=(price*1>0&&people*1>0)?price/people:0;
            $('#avgprice').text(Math.round(avg)+'元/人');
        });
        $('input[name=external]').change(function(){
            if($('input[name=external]:checked').val()=='1'){
                $('.supplierBox').show();
            }else{
                $('.supplierBox').hide();
            }
        });

    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan">课程信息</span>
        <a href="<?php echo $preurl ?>" class="fRight borBlueH37">返回列表</a>
    </div>
    <div class="comBox">
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <input name="preurl" type="hidden" value="<?php echo $preurl ?>">
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>课程名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="title" value="<?php echo !empty($course['title'])?$course['title']:$annualcourse['title'] ?>"
                                   type="text" class="iptH37 w345" placeholder="请输入课程名称">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>课程时间</th>
                        <td><select name="year" class="iptH37 w75 mr10">
                                <?php for ($i=0;$i<5;$i++){ ?>
                                    <option value="<?php echo date("Y")+$i;?>" <?php if($course['year']==date("Y")+$i){?>selected<?php } ?> ><?php echo date("Y")+$i;?></option>
                                <?php } ?>
                            </select><select name="month" class="iptH37 w75">
                                <?php $m='01';
                                for ($i=1;$i<=12;$i++){
                                    $m=$i<10?'0'.$i:$i; ?>
                                    <option value="<?php echo $m;?>" <?php if($course['month']==$m){?>selected<?php } ?>><?php echo $m;?>月</option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <th>课程讲师</th>
                        <td>
                            <span class="iptInner">
                                <select name="teacher_id" class="iptH37 w156">
                                    <option value="">请选择</option>
                                    <?php foreach ($teachers as $t) {
                                        echo $course['teacher_id'] == $t['id'] ? '<option selected value="' . $t['id'] . '">' . $t['name'] . '</option>' : '<option value="' . $t['id'] . '">' . $t['name'] . '</option>';
                                    } ?>
                                </select><a class="borBlueH37 ml20" href="<?php echo site_url('teacher/teachercreate') ?>">创建讲师</a>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>课程人次</th>
                        <td><span class="iptInner">
                                <input name="people" value="<?php echo !empty($course['people'])?$course['people']:$chosennum; ?>" placeholder="请输入课程人次" class="iptH37 w157 mr20 w237">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>课程预算</th>
                        <td>
                            <span class="iptInner">
                            <input name="price" placeholder="请输入课程预算" value="<?php echo $course['price'] ?>" type="text" class="iptH37 w157 mr5">元<span id="avgprice" class="ml20"></span>
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>内训/公开</th>
                        <td>
                            <label><input type="radio" name="external" value="2" checked />&nbsp;内训</label><label class="ml20"><input type="radio" name="external" value="1" <?php if($course['external']==1){?>checked<?php } ?> />&nbsp;公开</label>

                        </td>
                    </tr>
                    <tr class="supplierBox" <?php if($course['external']!=1){?>style="display: none;" <?php } ?>>
                        <th>供应商</th>
                        <td><input name="supplier" value="<?php echo $course['supplier'] ?>" placeholder="请输入供应商" class="iptH37 w157 mr20 w237"></td>
                    </tr>
                    <tr>
                        <th>课程时长</th>
                        <td><input name="day" value="<?php echo $course['day'] ?>" placeholder="请输入课程时长" class="iptH37 w157 mr20 w237"></td>
                    </tr>
                    <tr>
                        <th>课程介绍</th>
                        <td>
                            <textarea name="info" class="iptare pt10" placeholder="请输入课程介绍和收益"><?php echo !empty($course['info'])?$course['info']:$library['info'] ?></textarea>

                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="iptInner">
                            <input type="submit" value="保存" class="coBtn mr30">
                            </span>
                        </td>
                    </tr>
                </table>
        </div>

    </div>
</div>