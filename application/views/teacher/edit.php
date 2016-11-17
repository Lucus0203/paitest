<script type="text/javascript">
    $(document).ready(function () {
        $("#editForm").validate({
            rules: {
                name: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "请输入讲师姓名"
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
            submitHandler:function(form){
                $('input[type=submit]').val('请稍后..').attr('disabled','disabled');
                form.submit();
            }
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
                $('#fileBtn').prev().attr('src', this.result);
            }
        });
    });
</script>
<div class="wrap">
    <div class="titCom clearfix"><span class="titSpan"><?php echo empty($teacher) ? '创建讲师' : '编辑讲师' ?></span></div>
    <div class="comBox">
        <?php if (!empty($msg)) {?>
            <p class="alertBox alert-success"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
        <?php } ?>
        <div class="tableBox">
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input name="act" type="hidden" value="act"/>
                <input name="refere_url" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'];?>" />
                <div class="upPhoto">
                    <span><?php if (!empty($teacher['head_img'])) { ?><img src="<?php echo base_url() ?>/uploads/teacher_img/<?php echo $teacher['head_img'] ?>" alt="" width="122"><?php } else { ?><img src="<?php echo base_url() ?>images/face_default.png" width="122"><?php } ?><input name="head_img" type="file" style="<?php if (empty($teacher['head_img'])) { ?>visibility: hidden;<?php } else { ?>display:none<?php } ?>" id="fileBtn"/><a class="blue" href="javascript:;" onclick="$('#fileBtn').click()">上传头像</a>
                    </span>
                </div>
                <table cellspacing="0" class="comTable">
                    <col width="20%"/>
                    <tr>
                        <th><span class="red">*</span>讲师姓名</th>
                        <td>
                            <span class="iptInner">
                            <input name="name" placeholder="请输入讲师姓名" value="<?php echo $teacher['name'] ?>"
                                   type="text" class="iptH37 w237">
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">*</span>师资类型</th>
                        <td>
                            <ul class="lineUl">
                                <li>
                                    <label><input name="type" checked="checked" value="1" type="radio">内部</label></li>
                                <li>
                                    <label><input
                                            name="type" <?php echo $teacher['type'] == 2 ? 'checked="checked"' : '' ?> value="2" type="radio">外部</label></li>
                            </ul>

                        </td>
                    </tr>

                    <tr>
                        <th><span class="red">*</span>工作形式</th>
                        <td>
                            <ul class="lineUl">
                                <li>
                                    <input checked="checked" name="work_type" value="1" type="radio">专职
                                </li>
                                <li>
                                    <input name="work_type" <?php echo $teacher['work_type'] == 2 ? 'checked="checked"' : '' ?> value="2" type="radio">兼职
                                </li>
                            </ul>

                        </td>
                    </tr>
                    <tr>
                        <th>擅长类别</th>
                        <td>
                            <span class="iptInner">
                            <input name="specialty" placeholder="请输入擅长类型" value="<?php echo $teacher['specialty'] ?>" type="text" class="iptH37 w237">

                        </td>
                    </tr>
                    <tr>
                        <th>授课年限</th>
                        <td>
                            <span class="iptInner">
                            <select name="years" class="iptH37 w237">
                                <option value="">请选择</option>
                                <?php for ($i = 1; $i <= 30; $i++) {
                                    if ($teacher['years'] == $i) {
                                        echo '<option selected="selected" value="' . $i . '">' . $i . '年</option>';
                                    } else {
                                        echo '<option value="' . $i . '">' . $i . '年</option>';
                                    }
                                } ?>
                            </select>
                            </span>

                        </td>
                    </tr>

                    <tr>
                        <th>授课薪酬</th>
                        <td>
                            <span class="iptInner">
                            <input name="hourly" placeholder="请输入授课薪酬" value="<?php echo $teacher['hourly'] ?>" type="text" class="iptH37 w157 mr20">元/课时
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <th>讲师简介</th>
                        <td>
                            <span class="iptInner">
                            <textarea name="info" placeholder="请输入讲师简介和头衔" class="iptare pt10"><?php echo $teacher['info'] ?></textarea>
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

    </div>
</div>