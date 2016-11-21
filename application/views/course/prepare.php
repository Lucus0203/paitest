<script type="text/javascript">
    $(document).ready(function(){
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        });
        $("#prepareForm").validate({
            rules: {
                note: {
                    required: true
                }
            },
            messages: {
                note: {
                    required: "请输入准备内容"
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
        $('#fileBtn').change(function () {
            // 检查是否为图像类型
            var simpleFile = document.getElementById("fileBtn").files[0];
            if(simpleFile.size>10*1000000){
                $('#fileBtn').after($(this).clone(true)).remove();
                alert('文件大小不能超过10M');
            }else{
                $('#filename').text(simpleFile.name);
            }
        });
        $('#delfile').click(function(){
            return confirm('删除文档不可恢复,确定删除吗?');
        });
    })
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
                    <li class="cur"><a href="<?php echo site_url('course/prepare/'.$course['id']) ?>">课程公告<i class="ml10 fa fa-angle-right fa-lg"></i></a></li>
                </ul>

            </div>
            <div class="contRight">
                <form id="prepareForm" method="post" action="" enctype="multipart/form-data">
                    <input name="act" type="hidden" value="act" />
                    <?php if (!empty($msg)) {?>
                        <p class="alertBox <?php echo $success=='ok'?'alert-success':'alert-danger';?> mb20"><span class="alert-msg"><?php echo $msg ?></span><a href="javascript:;" class="alert-remove">X</a></p>
                    <?php } ?>
                    <p class="f14 mb20 gray6">提前告知学员所需要了解的课程内容及准备事项</p>
                    <table cellspacing="0" class="comTable">
                        <colgroup>
                            <col width="9%">
                            <col width="95%">
                        </colgroup><tbody>
                        <tr>
                            <th style="padding-left: 0;">准备内容</th>
                            <td>
                                <span class="iptInner">
                                <textarea name="note" class="iptare pt10" placeholder="请输入学员课前需要了解的内容及准备事项"><?php echo $prepare['note']; ?></textarea>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th style="padding-left: 0;">附件文档</th>
                            <td>
                                <span class="iptInner">
                                    <input name="file" type="file"
                                           style="display: none;" id="fileBtn"/><a
                                        href="javascript:;" onclick="$('#fileBtn').click()"
                                        class="borBlueH37 mb10 mr10">上传文档</a>
                                    <span id="filename" class="mr10"></span><?php if(!empty($prepare['filename'])){ ?><a href="<?php echo site_url('course/preparefile/'.$course['id']) ?>" class="blue mr10" target="_blank">下载文档(<?php echo $prepare['filename'] ?>)</a><a class="red" id="delfile" href="<?php echo site_url('course/preparedelfile/'.$course['id']) ?>">删除文档</a><?php } ?>
                                </span>
                                <p class="gray9">大小不超过10M</p>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="submit" class="coBtn" value="保存">
                            </td>
                        </tr>
                        </tbody></table>
                </form>
            </div>

        </div>

    </div>
</div>
