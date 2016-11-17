<script type="text/javascript">
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                title: {
                    required: true
                },
                annual_survey_id: {
                    required: true
                }
            },
            messages: {
                title: {
                    required: "请输入问卷名称"
                },
                annual_survey_id: {
                    required: "请选择调查问卷"
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
    });
</script>
<div class="wrap">
    <div class="titCom clearfix">
        <span class="titSpan"><?php echo empty($survey['id']) ? '创建培训计划' : '编辑培训计划'; ?></span>
        <a href="<?php echo site_url('annualplan/index') ?>" class="fRight borBlueH37">返回列表</a>
    </div>
    <div class="comBox">
        <div class="tableBox">
            <form id="editForm" method="post" action="">
                <input name="act" type="hidden" value="act"/>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>计划名称</th>
                        <td>
                            <span class="iptInner">
                            <input name="title" value="<?php echo $plan['title'] ?>" type="text" class="iptH37 w250" placeholder="请输入问卷名称" autocomplete="off">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>调查问卷</th>
                        <td>
                            <?php if(!empty($survey['id'])){
                                echo $survey['title'] ?>
                                <input type="hidden" name="annual_survey_id" value="<?php echo $survey['id'] ?>">
                            <?php }else{ ?>
                                <span class="iptInner">
                                <select name="annual_survey_id" class="iptH37 w250">
                                    <option value="">请选择</option>
                                    <?php foreach ($surveys as $s){?>
                                        <option value="<?php echo $s['id']; ?>"><?php echo $s['title'];?></option>
                                    <?php } ?>
                                </select><span class="f14 gray9">（请选择相应的调研问卷）</span></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <textarea name="note" class="iptare pt10" placeholder="可输入备注内容"><?php echo $plan['note'] ?></textarea>
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