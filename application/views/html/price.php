<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>/css/kecheng.css" />
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-scrolltofixed-min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.listBox').delegate('.listCont', 'hover', function() {
            $(this).toggleClass('hover');
        });

        /*精度条*/
        var select = $("#minbeds");
        var slider = $("#slider").slider({
            min: 100,
            max: 1800,
            step: 100,
            range: "min",
            value: 100,
            slide: function(event, ui) {
                select[0].value= (ui.value>1000)?(1000+(ui.value-1000)*5):ui.value;
                calculate();
            }
        });
        $("#minbeds").on("change", function() {
            var v=$(this).val();
            v=(v>1000)?(1000+(v-1000)/5):v;
            slider.slider("value", v );
            calculate();
        });
        /*精度条01*/
        var select01 = $("#minbeds01");
        var slider01 = $("#slider01").slider({
            min: 0,
            max: 6,
            range: "min",
            value: 0,
            slide: function(event, ui) {
                select01[0].selectedIndex = ui.value;
                calculate();
            }
        });
        $("#minbeds01").on("change", function() {
            slider01.slider( "value", this.selectedIndex);
            calculate();
        });

        /*精度条02*/
        var select02 = $("#minbeds02");
        var slider02 = $("#slider02").slider({
            min: 0,
            max: 10,
            range: "min",
            value: 0,
            slide: function(event, ui) {
                select02[0].selectedIndex = ui.value;
                calculate();
            }
        });
        $("#minbeds02").on("change", function() {
            slider02.slider( "value", this.selectedIndex );
            calculate();
        });
        $('.zifeiRight').scrollToFixed({
            marginTop: $('.zifeiBox').offsetTop + 10,
            limit: function() {
                var limit = $('.footer').offset().top - $(this).outerHeight(true) - 30;
                return limit;
            },
            zIndex: 999
        });
        $('#requireSurvey,#trainPlan,#wechat,input[name=discount]').change(function(){calculate();});
        $('#downloadPrice').click(function(){
            var minbeds=$('#minbeds').val()*1;
            var minbeds01=$('#minbeds01').val()*1;
            var minbeds02=$('#minbeds02').val()*1;
            var requireSurvey=($('#requireSurvey').attr('checked'))?1:0;
            var trainPlan=($('#trainPlan').attr('checked'))?1:0;
            var wechat=($('#wechat').attr('checked'))?1:0;
            var discount=$('input[name=discount]:checked').val();
            var str='<?php echo site_url('html/downloadPrice')?>?minbeds='+minbeds+'&minbeds01='+minbeds01+'&minbeds02='+minbeds02+'&requireSurvey='+requireSurvey+'&trainPlan='+trainPlan+'&wechat='+wechat+'&discount='+discount;
            window.open(str);
            return false;
        });
    })
    function calculate(){
        var minbeds=$('#minbeds').val()*1;
        var minbeds01=$('#minbeds01').val()*1;
        var minbeds02=$('#minbeds02').val()*1;
        var requireSurvey=($('#requireSurvey').attr('checked'))?1:0;
        var trainPlan=($('#trainPlan').attr('checked'))?1:0;
        var wechat=($('#wechat').attr('checked'))?1:0;
        var discount=$('input[name=discount]:checked').val();
        var numval=minbeds>1000?minbeds*13:minbeds*15;
        var amount=(200*minbeds01+2000*minbeds02+trainPlan*3000+wechat*800)*discount;
        $('.minbedsnum').text(minbeds+'人');
        $('.minbedsAmount').text(numval);
        $('.minbeds01num').text(minbeds01+'个');
        $('.minbeds01Amount').text(200*minbeds01);
        if(minbeds01>0){$('.minbeds01Tr').show();}else{$('.minbeds01Tr').hide();}
        $('.minbeds02num').text(minbeds02+'个');
        $('.minbeds02Amount').text(2000*minbeds02);
        if(minbeds02>0){$('.minbeds02Tr').show();}else{$('.minbeds02Tr').hide();}
        if(requireSurvey>0){$('.surveyTr').show();}else{$('.surveyTr').hide();}
        if(trainPlan>0){$('.trainPlanTr').show();}else{$('.trainPlanTr').hide();}
        if(wechat>0){$('.wechatTr').show();}else{$('.wechatTr').hide();}
        $('#amount').text(Math.round(amount)+'元');

    }
</script>
<div class="wrap">
    <div class="comBox">
        <div class="ttl01 aCenter">资费标准</div>
        <div class="zifeiBox p20">
            <div class="zifeiLeft">
                <div class="ttl01 pt0">基础功能</div>
                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>培训流程管理</td>
                        <td>培训流程管理，课程发布、报名、签到、课前调研、课前公告、短信通知、课后反馈</td>
                        <td rowspan="4">
                            <p class="mb10">1、学员数1000人以下，按<span class="red">1500元</span>/100人/年。<br> 2、学员数1000~5000人，按<span class="red">6500元</span>/500人/年。 <br>3、超过5000人请咨询客服详谈。
                            </p>
                            <p class="red">2017年1月1日前注册企业长期免费。</p>
                        </td>
                    </tr>
                    <tr>
                        <td>公司信息管理</td>
                        <td>公司基本信息管理，员工信息管理</td>

                    </tr>
                    <tr>
                        <td>权限管理</td>
                        <td>不同级别员工权限管理</td>

                    </tr>
                    <tr>
                        <td>师资管理</td>
                        <td>内训师和外部讲师管理</td>

                    </tr>
                </table>

                <form id="reservation" class="sidebox">
                    <label for="minbeds">学员数</label>
                    <div id='slider' class="sideList">
                        <span class="sliderSpan gray9" style="width:100px; font-size: 12px;">500</span>
                        <span class="sliderSpan gray9" style="width:128px; font-size: 12px;">1千</span>
                        <span class="sliderSpan gray9" style="width:100px; font-size: 12px;">3千</span>
                        <span class="sliderSpan gray9 noborder" style="width: 104px;font-size: 12px;">5千</span>
                    </div>
                    <select name="minbeds" id="minbeds" class="sectside">
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                        <option value="600">600</option>
                        <option value="700">700</option>
                        <option value="800">800</option>
                        <option value="900">900</option>
                        <option value="1000">1000</option>
                        <option value="1500">1500</option>
                        <option value="2000">2000</option>
                        <option value="2500">2500</option>
                        <option value="3000">3000</option>
                        <option value="3500">3500</option>
                        <option value="4000">4000</option>
                        <option value="4500">4500</option>
                        <option value="5000">5000</option>
                    </select>
                </form>
                <div class="ttl01">可选功能</div>
                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>能力模型基础版</td>
                        <td>仅限于可用的基础岗位</td>
                        <td><span class="red">200元</span>/个/年</td>

                    </tr>
                    <tr>
                        <td>能力模型定制版</td>
                        <td>定制按照每个岗位收费</td>
                        <td><span class="red">2000元</span>/个/年</td>
                    </tr>

                </table>

                <form id="reservation01" class="sidebox">
                    <label for="minbeds01">能力模型基础版</label>
                    <div id='slider01' class="sideList" style="width: 400px;">
                        <span class="gray9" style="display: block">
                            <span class="sliderSpan" style="width: 64px;">1</span>
                            <span class="sliderSpan" style="width: 64px;">2</span>
                            <span class="sliderSpan" style="width: 63px;">3</span>
                            <span class="sliderSpan" style="width: 62px;">4</span>
                            <span class="sliderSpan" style="width: 62px;">5</span>
                            <span class="sliderSpan noborder" style="width: 58px;">6</span></span>
                    </div>

                    <select name="minbeds01" id="minbeds01" class="sectside">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>

                    </select>
                </form>

                <form id="reservation02" class="sidebox">
                    <label for="minbeds02">能力模型定制版</label>
                    <div id='slider02' class="sideList" style="width: 400px;">
                        <span class="gray9" style="display: block">
                            <span class="sliderSpan" style="width: 35px;">1</span>
                            <span class="sliderSpan" style="width: 35px;">2</span>
                            <span class="sliderSpan" style="width: 35px;">3</span>
                            <span class="sliderSpan" style="width: 35px;">4</span>
                            <span class="sliderSpan" style="width: 35px;">5</span>
                            <span class="sliderSpan" style="width: 35px;">6</span>
                            <span class="sliderSpan" style="width: 35px;">7</span>
                            <span class="sliderSpan" style="width: 35px;">8</span>
                            <span class="sliderSpan" style="width: 35px;">9</span>
                            <span class="sliderSpan noborder" style="width: 38px;">10</span></span>
                    </div>

                    <select name="minbeds02" id="minbeds02" class="sectside">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>

                    </select>
                </form>

                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>年度培训计划</td>
                        <td>年度需求问卷的制作、调研发放与结果收集。<br>年度培训计划、年度预算评估与培训计划分析，内训师与供应商资源整合。</td>
                        <td>
                            <span class="red">3000</span>元/年。
                        </td>

                    </tr>

                </table>
                <div class="sidebox clearfix">
                    <label>年度培训计划:</label>
                    <label class="switch">
                        <input id="trainPlan" type="checkbox" name="trainPlan" value="1">
                        <div class="slider round"></div>
                    </label>
                </div>

                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="40%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>收费标准</th>
                    </tr>
                    <tr>
                        <td>公众号搭建</td>
                        <td>公众号搭建，绑定微信。</td>
                        <td>
                            <span class="red">800元</span>/年(含微信认证300元)。
                        </td>
                    </tr>

                </table>


                <div class="sidebox clearfix">
                    <label>公众号搭建:</label>
                    <label class="switch">
                        <input id="wechat" name="wechat" type="checkbox">
                        <div class="slider round"></div>
                    </label>
                </div>

                <div class="ttl01">后续功能</div>
                <table class="tableA mb20">
                    <col width="20%" />
                    <col width="62%" />
                    <col width="18%" />
                    <tr>
                        <th>功能</th>
                        <th>内容</th>
                        <th>上线预计</th>
                    </tr>
                    <tr>
                        <td>考试</td>
                        <td>考题设计、题库管理、一键发布、考试统计、报表分析。</td>
                        <td class="aCenter">2017年1月</td>
                    </tr>
                    <tr>
                        <td>学员成长轨迹</td>
                        <td>企业私有数据分析。</td>
                        <td class="aCenter">2017年2月</td>
                    </tr>
                    <tr>
                        <td>课后跟进</td>
                        <td>培训结果跟进，增强培训落地性，员工经理和学员双角色版本。</td>
                        <td class="aCenter">2017年2月</td>
                    </tr>
                    <tr>
                        <td>系统对接</td>
                        <td>与企业现有ERP、OA、SAAS系统的对接。</td>
                        <td class="aCenter">2017年4月</td>
                    </tr>
                    <tr>
                        <td>职业发展通道</td>
                        <td>员工职业发展通道。</td>
                        <td class="aCenter">2017年中旬</td>
                    </tr>
                    <tr>
                        <td>人才发展计划</td>
                        <td>人才发展计划、学习地图的开发和人才梯队建设。</td>
                        <td class="aCenter">2017年中旬</td>
                    </tr>
                    <tr>
                        <td>直播平台</td>
                        <td>E-Learning平台，包括直播、录播、讲师与学员互动、视频资料管理、知识管理等功能。</td>
                        <td class="aCenter">2017年下旬</td>
                    </tr>
                    <tr>
                        <td>微课开发工具</td>
                        <td>一键微课开发工具，高效和轻松的课程制作小能手。</td>
                        <td class="aCenter">2017年下旬</td>
                    </tr>

                </table>
            </div>

            <div class="zifeiRight">
                <div class="ttl01" style="padding-right: 0;">价格清单：<a id="downloadPrice" href="#" class="borBlueBtnH28 f14 fRight">下载清单</a>
                </div>
                <table class="tableB">
                    <col width="40%" />
                    <tr>
                        <td>功能</td>
                        <td class="aCenter">数量</td>
                        <td class="aRight">价格</td>
                    </tr>
                    <tr>
                        <td>基础功能 </td>
                        <td class="aCenter minbedsnum">100人 </td>
                        <td class="red minbedsAmount aRight" style="text-decoration:line-through;">1500</td>
                    </tr>
                    <tr class="minbeds01Tr" style="display: none;">
                        <td>基础能力模型 </td>
                        <td class="aCenter minbeds01num">1个 </td>
                        <td class="red minbeds01Amount aRight">200</td>
                    </tr>
                    <tr class="minbeds02Tr" style="display: none;">
                        <td>定制能力模型 </td>
                        <td class="aCenter minbeds02num">1个 </td>
                        <td class="red minbeds02Amount aRight">2000</td>
                    </tr>
                    <tr class="surveyTr" style="display: none;">
                        <td>年度需求调研</td>
                        <td class="aCenter">1年</td>
                        <td class="red aRight">免费</td>
                    </tr>
                    <tr class="trainPlanTr" style="display: none;">
                        <td>年度培训计划</td>
                        <td class="aCenter">1年</td>
                        <td class="red trainPlanAmount aRight">3000</td>
                    </tr>
                    <tr class="wechatTr" style="display: none;">
                        <td>公众号搭建</td>
                        <td class="aCenter">1个</td>
                        <td class="red wechatAmount aRight">800</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="aRight">
                            <ul class="listRadio fRight">
                                <li><label><input name="discount" type="radio" value="2.55">三年(<span class="red">8.5</span>折)</label></li>
                                <li><label><input name="discount" type="radio" value="1.8">两年(<span class="red">9</span>折)</label></li>
                                <li class="ml0"><label><input name="discount" type="radio" checked value="1">一年</label></li>

                            </ul>

                        </td>

                    </tr>
                </table>
                <p class="f16 aRight p15">总价：<span class="red" id="amount">0元</span></p>
                <p class="p15 f14 aRight gray9" style="border-top:1px solid #dbdbdb;">下单请联系:<a class="ml10 f18 blue" href="tel:021-61723727">021-61723727</a></p>
            </div>

        </div>
    </div>
</div>