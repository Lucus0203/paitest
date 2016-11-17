<?php

/**
 * 培训派通知类
 *
 * @version        1.0
 */
class Notifyclass
{
    protected $CI;

    function __construct($config = array())
    {

        $this->CI =& get_instance();
        $this->CI->load->library(array('wechat','zhidingsms'));
        $this->CI->load->helper(array('form', 'url'));
        $this->CI->load->model(array('user_model', 'company_model', 'course_model', 'teacher_model', 'homework_model', 'survey_model', 'ratings_model', 'student_model', 'department_model','abilityjob_model','annualsurvey_model','annualplan_model'));

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = '127.0.0.1';
        $config['smtp_user'] = 'mailservice';
        $config['smtp_pass'] = 'service';
        $config['smtp_port'] = '25';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['smtp_timeout'] = '5';
        $config['newline'] = "\r\n";
        $this->CI->load->library('email', $config);
    }


    //开启报名 //人工触发
    public function applyopen($courseid,$notifyTarget="")
    {
        $course = $this->CI->course_model->get_row(array('id' => $courseid));
        if($course['isnotice_open']!=1){//自动通知关闭
            return false;
        }
        $user = $this->CI->user_model->get_row(array('id' => $course['user_id']));
        $company = $this->CI->company_model->get_row(array('code'=>$user['company_code']));
        $ischeckmsg=($course['apply_check']==1)?'报名需经您的上级领导审核，通过后另行通知。':'报名成功后我们将另行通知。';
        $t1 = date('m月d日H:i', strtotime($course['time_start']));//举行时间
        $t2 = date('m月d日H:i', strtotime($course['apply_start']));//报名开始时间
        $t3 = date('m月d日H:i', strtotime($course['apply_end']));//报名截止时间
        $link = $this->CI->config->item('web_url') . 'course/info/'.$course['id'].'.html';//链接
        $link_short='course/info/'.$course['id'].'.html';
        $sign=$company['name'];
        $sign.=($company['code']=='100276')?' 人力资源部':'';
        //短信通知
        if($course['notice_type_msg']==1){
            $studentsarr = explode(',', $notifyTarget);//explode(',', $course['targetstudent']);
            $students=array();
            foreach ($studentsarr as $s) {
                $student = $this->CI->student_model->get_row(array('id' => $s));
                if(!empty($student['email'])){
                    $students[]=$student;
                }
                if($student['register_flag']==1){
                    $pass=substr($student['mobile'],-6);
                    $accountmsg='账号：'.$student['mobile'].'
密码：'.$pass.'
记得修改密码。';
                    $this->CI->student_model->update(array('user_pass'=>md5($pass)),$student['id']);
                }else{
                    $accountmsg='';
                }
                /*$msg="亲爱的{$student['name']}：
依据公司安排，《{$course['title']}》将于{$t1}举行。报名已启动，将在{$t2}截止，点击报名：
{$link}
{$accountmsg}
{$ischeckmsg}
为了大家的共同进步，请积极参与！

".$company['name'];
                if($company['code']=='100276'){
                    $msg.="
人力资源部";
                }
                $msg.="
". date("Y年m月d日");
                $this->CI->zhidingsms->sendSMS($student['mobile'], $msg);*/
                $content='@1@='.$student['name'].',@2@='.$course['title'].',@3@='.$t1.',@4@='.$t2.',@5@='.$t3.',@6@='.$link_short.',@7@='.$accountmsg.',@8@='.$ischeckmsg.',@9@='.$sign.',@10@='.date("Y年m月d日");
                $this->CI->zhidingsms->sendTPSMS($student['mobile'], $content,'ZD30018-0007');
            }
        }

        //mail
        if($course['notice_type_email']==1){
            $tomail = $user['email'];
            $subject = "《{$course['title']}》开启报名";
            $studentname="亲爱的{$user['real_name']}：";
            $message = <<< EOF
<p style="text-indent:40px">依据公司培训计划安排，《{$course['title']}》将于{$t1}举行。现已启动报名工作，报名将在{$t2}截止，点击下面的链接报名吧。
<br><a href='{$link}' target='_blank'>{$link}</a></p>
<p style="text-indent:40px">{$ischeckmsg}</p>
<p style="text-indent:40px">为了大家的共同进步，请积极参与！</p>

<br><p style="text-align: right;margin-right: 40px;">{$company['name']}</p>
EOF;
            if($company['code']=='100276'){
                $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
            }
            $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($studentname.$message);
            $this->CI->email->send();//发送给创建者
            $this->CI->email->clear();
            //发送给学员
            foreach ($students as $student) {
                if(!empty($student['email'])){
                    $studentname="亲爱的{$student['name']}：";
                    $this->CI->email->from('service@trainingpie.com', '培训派');
                    $this->CI->email->to($student['email']);
                    $this->CI->email->subject($subject);
                    $this->CI->email->message($studentname.$message);
                    $this->CI->email->send();
                    $this->CI->email->clear();
                }
            }
        }

        //微信通知
        if ($course['notice_type_wx']==1) {
            foreach ($studentsarr as $s) {
                $student = $this->CI->student_model->get_row(array('id' => $s));
                if(!empty($student['openid'])){
                    $wxdata = array(
                        'first' => array(
                            'value' => '亲爱的' . $student['name'] . '
依据公司培训计划安排，《' . $course['title'] . '》将于'.$t1.'举行。现已启动报名工作，报名将在'.$t2.'截止',
                            'color' => "#173177"
                        ),
                        'keyword1' => array(
                            'value' => $course['title'],
                            'color' => "#173177"
                        ),
                        'keyword2' => array(
                            'value' => $t2 . '~' . $t3,
                            'color' => "#173177"
                        ),
                        'remark' => array(
                            'value' => "请提前安排好工作或出差行程，准时参加培训。上课前请先完成课前调研表和课前作业并提交给我们。
预祝学习愉快，收获满满！",
                            'color' => "#173177"
                        )
                    );
                    $res = $this->CI->wechat->templateSend($student['openid'], 'iYvemgywJfyHxJu_lm_kS3-txaBnfR9gQCO93YCf0EA', $link, $wxdata);
                }
            }
        }


    }


    //报名成功 人员触发 人工审核
    public function applysuccess($courseid, $studentid)
    {
        $course = $this->CI->course_model->get_row(array('id' => $courseid));
        if($course['isnotice_open']!=1){//自动通知关闭
            return false;
        }
        $student = $this->CI->student_model->get_row(array('id' => $studentid));
        $company = $this->CI->company_model->get_row(array('code' => $student['company_code']));

        $t = date('Y年m月d日H:i', strtotime($course['time_start']));//举行时间
        $link = $this->CI->config->item('web_url') .'course/survey/' . $course['id'] . '.html';//链接
        $link_short='course/survey/' . $course['id'].'.html';
        $sign=$company['name'];
        $sign.=($company['code']=='100276')?' 人力资源部':'';
        //短信通知
        if (!empty($student['mobile'])&&$course['notice_type_msg']==1) {
            /*$msg = "亲爱的{$student['name']}：
你已成功报名参加《{$course['title']}》，该课程将于{$t}在{$course['address']}举行，请提前安排好工作或出差行程，准时参加培训。
上课前请先完成课前调研表（{$link}）和课前作业并提交给我们。
预祝学习愉快，收获满满！

" . $company['name'];
            if($company['code']=='100276'){
                $msg.="
人力资源部";
            }
            $msg.="
". date("Y年m月d日");
            $this->CI->zhidingsms->sendSMS($student['mobile'], $msg);*/
            $content='@1@='.$student['name'].',@2@='.$course['title'].',@3@='.$t.',@4@='.$course['address'].',@5@='.$link_short.',@6@='.$sign.',@7@='.date("Y年m月d日");
            $this->CI->zhidingsms->sendTPSMS($student['mobile'], $content,'ZD30018-0003');
        }

        //mail
        if (!empty($student['email'])&&$course['notice_type_email']==1) {

            $tomail = $student['email'];
            $subject = "《{$course['title']}》报名成功";
            $message = "亲爱的{$student['name']}：
<p style='text-indent:40px'>你已成功报名参加《{$course['title']}》，该课程将于{$t}在{$course['address']}举行，请提前安排好工作或出差行程，准时参加培训。</p>
<p style='text-indent:40px'>上课前请先完成课前调研表（<a href='{$link}' target='_blank'>{$link}</a>）和课前作业并提交给我们。</p>
<p style='text-indent:40px'>预祝学习愉快，收获满满！</p>

<p style=\"text-align: right;margin-right: 40px;\">".$company['name'].'</p>';
            if($company['code']=='100276'){
                $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
            }
            $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($message);
            $this->CI->email->send();
            $this->CI->email->clear();

        }
        //微信通知
        if (!empty($student['openid'])&&$course['notice_type_wx']==1) {
            $wxdata = array(
                'first' => array(
                    'value' => '您好,' . $student['name'] . '
您已成功报名参加' . $course['title'],
                    'color' => "#173177"
                ),
                'class' => array(
                    'value' => $course['title'],
                    'color' => "#173177"
                ),
                'time' => array(
                    'value' => date('m月d日', strtotime($course['time_start'])),
                    'color' => "#173177"
                ),
                'add' => array(
                    'value' => $course['address'],
                    'color' => "#173177"
                ),
                'remark' => array(
                    'value' => "请提前安排好工作或出差行程，准时参加培训。上课前请先完成课前调研表和课前作业并提交给我们。
预祝学习愉快，收获满满！",
                    'color' => "#173177"
                )
            );
            $res = $this->CI->wechat->templateSend($student['openid'], 'yFfIfh1EPvvpyeNplv5n6xBEyn5Em4r5ZYAHoLFnM9E', $this->CI->config->item('web_url') . 'course/survey/' . $course['id'] . '.html', $wxdata);
        }
    }

    //能力评估通知
    public function abilitypublish($ability_job_id,$student_id){
        $abilityjob = $this->CI->abilityjob_model->get_row(array('id' => $ability_job_id));
        $student = $this->CI->student_model->get_row(array('id' => $student_id));
        $company = $this->CI->company_model->get_row(array('code' => $student['company_code']));

        $link = $this->CI->config->item('web_url') .'ability/assess/' . $ability_job_id . '/'.$student['company_code'].'.html';//链接
        $link_short='ability/assess/' . $ability_job_id . '/'.$student['company_code'].'.html';
        $sign=$company['name'];
        $sign.=($company['code']=='100276')?' 人力资源部':'';
        //短信通知
        if (!empty($student['mobile'])) {
            /*$msg = "亲爱的{$student['name']}：
请完成《{$abilityjob['name']}》能力评估（{$link}）并提交给我们。
预祝学习愉快，收获满满！

" . $company['name'];
            if($company['code']=='100276'){
                $msg.="
人力资源部";
            }
            $msg.="
". date("Y年m月d日");
            $this->CI->zhidingsms->sendSMS($student['mobile'], $msg);*/
            $content='@1@='.$student['name'].',@2@='.$abilityjob['name'].',@3@='.$link_short.',@4@='.$sign.',@5@='.date("Y年m月d日");
            $this->CI->zhidingsms->sendTPSMS($student['mobile'], $content,'ZD30018-0004');
        }

        //mail
        if (!empty($student['email'])) {

            $tomail = $student['email'];
            $subject = "《{$abilityjob['name']}》能力评估";
            $message = "亲爱的{$student['name']}：
<p style='text-indent:40px'>请完成《{$abilityjob['name']}》能力评估（<a href='{$link}' target='_blank'>{$link}</a>）并提交给我们。</p>
<p style='text-indent:40px'>预祝学习愉快，收获满满！</p>

<p style='text-align: right;margin-right: 40px;'>".$company['name'].'</p>';
            if($company['code']=='100276'){
                $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
            }
            $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($message);
            $this->CI->email->send();
            $this->CI->email->clear();

        }
    }

    public function annualsurveystart($surveyid,$notifyTarget){
        $survey = $this->CI->annualsurvey_model->get_row(array('id' => $surveyid));
        $qrcode = base_url('uploads/annualqrcode/'.$survey['qrcode'].'.png');
        $company = $this->CI->company_model->get_row(array('code'=>$survey['company_code']));
        $t1 = date('m月d日H:i', strtotime($survey['time_start']));//调研开始时间
        $t2 = date('m月d日H:i', strtotime($survey['time_end']));//调研截止日期
        $link = $this->CI->config->item('web_url') . 'annual/answer/'.$surveyid.'.html';//链接
        $link_short='annual/answer/'.$surveyid.'.html';
        $sign=$company['name'];
        $sign.=($company['code']=='100276')?' 人力资源部':'';
        foreach ($notifyTarget as $s) {
            $student = $this->CI->student_model->get_row(array('id' => $s));
            if(!empty($student['email'])){
                $students[]=$student;
            }
            if($student['register_flag']==1){
                $pass=substr($student['mobile'],-6);
                $accountmsg='账号：'.$student['mobile'].'
密码：'.$pass.'
记得修改密码。';
                $this->CI->student_model->update(array('user_pass'=>md5($pass)),$student['id']);
            }else{
                $accountmsg='';
            }
            /*$msg="亲爱的{$student['name']}：
为了合理安排公司培训,提高您的工作能力,正在进行培训需求调研,点击链接完成《{$survey['title']}》,调研截止日期{$t2}
{$link}
{$accountmsg}

".$company['name'];
            if($company['code']=='100276'){
                $msg.="
人力资源部";
            }
            $msg.="
". date("Y年m月d日");
            $this->CI->zhidingsms->sendSMS($student['mobile'], $msg);*/
            $content='@1@='.$student['name'].',@2@='.$survey['title'].',@3@='.$t2.',@4@='.$link_short.',@5@='.$accountmsg.',@6@='.$sign.',@7@='.date("Y年m月d日");
            $this->CI->zhidingsms->sendTPSMS($student['mobile'], $content,'ZD30018-0008');
        }

        //mail
        $tomail = $company['email'];
        $subject = "《{$survey['title']}》";
        $studentname="亲爱的{$company['contact']}：";
        $message = <<< EOF
<p style="text-indent:40px">为了合理安排公司培训,提高您的工作能力,正在进行培训需求调研,点击链接完成《{$survey['title']}》,调研截止日期{$t2}。
<br>如果是手机端,直接点击链接<a href='{$link}' target='_blank'>{$link}</a>
<br>如果是电脑端,请扫描下面二维码:</p>
<p style="text-align: center;"><img src="{$qrcode}" alt="" width="200"></p>

<br><p style="text-align: right;margin-right: 40px;">{$company['name']}</p>
EOF;
        if($company['code']=='100276'){
            $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
        }
        $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
        $this->CI->email->from('service@trainingpie.com', '培训派');
        $this->CI->email->to($tomail);//
        $this->CI->email->subject($subject);
        $this->CI->email->message($studentname.$message);
        $this->CI->email->send();//发送给创建者
        $this->CI->email->clear();
        //发送给学员
        foreach ($students as $student) {
            if(!empty($student['email'])){
                $studentname="亲爱的{$student['name']}：";
                $this->CI->email->from('service@trainingpie.com', '培训派');
                $this->CI->email->to($student['email']);
                $this->CI->email->subject($subject);
                $this->CI->email->message($studentname.$message);
                $this->CI->email->send();
                $this->CI->email->clear();
            }
        }

        //微信通知
        foreach ($notifyTarget as $s) {
            $student = $this->CI->student_model->get_row(array('id' => $s));
            if(!empty($student['openid'])){
                $wxdata = array(
                    'first' => array(
                        'value' => '亲爱的' . $student['name'] . "
为了合理安排公司培训,提高您的工作能力,正在进行培训需求调研,点击详情完成《{$survey['title']}》,调研截止日期{$t2}",
                        'color' => "#173177"
                    ),
                    'keyword1' => array(
                        'value' => $survey['title'],
                        'color' => "#173177"
                    ),
                    'keyword2' => array(
                        'value' => $t1 . '~' . $t2,
                        'color' => "#173177"
                    ),
                    'remark' => array(
                        'value' => "请点击详情进行处理",
                        'color' => "#173177"
                    )
                );
                $res = $this->CI->wechat->templateSend($student['openid'], 'iYvemgywJfyHxJu_lm_kS3-txaBnfR9gQCO93YCf0EA', $link, $wxdata);
            }
        }

    }

    //年度计划开启审核
    public function planCourseApproved($planid,$nobifytarget){
        $plan=$this->CI->annualplan_model->get_row(array('id'=>$planid));
        $link_pc = $this->CI->config->item('base_url') . 'annualmanage/approved/'.$planid.'.html';//链接
        $link_web = $this->CI->config->item('web_url') . 'annualmanage/approved/'.$planid.'.html';//链接
        $link_short='annualmanage/approved/'.$planid.'.html';
        $company = $this->CI->company_model->get_row(array('code'=>$plan['company_code']));
        $sign=$company['name'];
        $sign.=($company['code']=='100276')?' 人力资源部':'';
        foreach ($nobifytarget as $s) {
            $student = $this->CI->student_model->get_row(array('id' => $s));
            if(!empty($student['email'])){
                $students[]=$student;
            }
            if($student['register_flag']==1){
                $pass=substr($student['mobile'],-6);
                $accountmsg='账号：'.$student['mobile'].'
密码：'.$pass.'
记得修改密码。';
                $this->CI->student_model->update(array('user_pass'=>md5($pass)),$student['id']);
            }else{
                $accountmsg='';
            }
            /*$msg="亲爱的{$student['name']}：
公司正在制定未来的培训计划,您可以进行部门内员工的课程报名审核,点击链接审核,感谢配合
{$link}
{$accountmsg}

".$company['name'];
            if($company['code']=='100276'){
                $msg.="
人力资源部";
            }
            $msg.="
". date("Y年m月d日");
            $this->CI->zhidingsms->sendSMS($student['mobile'], $msg);*/
            $content='@1@='.$student['name'].',@2@='.$link_short.',@3@='.$accountmsg.',@4@='.$sign.',@5@='.date("Y年m月d日");
            $this->CI->zhidingsms->sendTPSMS($student['mobile'], $content,'ZD30018-0009');
        }

        //mail
        $tomail = $company['email'];
        $subject = "《年度计划待审核》";
        $studentname="亲爱的{$company['contact']}：";
        $message = <<< EOF
<p style="text-indent:40px">公司正在制定未来的培训计划,您可以进行部门内员工的课程报名审核,点击链接审核,感谢配合。
<br>如果是手机端,直接点击链接<a href='{$link_web}' target='_blank'>{$link_web}</a>
<br>如果是电脑端,直接点击链接<a href='{$link_pc}' target='_blank'>{$link_pc}</a></p>

<br><p style="text-align: right;margin-right: 40px;">{$company['name']}</p>
EOF;
        if($company['code']=='100276'){
            $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
        }
        $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
        $this->CI->email->from('service@trainingpie.com', '培训派');
        $this->CI->email->to($tomail);//
        $this->CI->email->subject($subject);
        $this->CI->email->message($studentname.$message);
        $this->CI->email->send();//发送给创建者
        $this->CI->email->clear();
        //发送给学员
        foreach ($students as $student) {
            if(!empty($student['email'])){
                $studentname="亲爱的{$student['name']}：";
                $this->CI->email->from('service@trainingpie.com', '培训派');
                $this->CI->email->to($student['email']);
                $this->CI->email->subject($subject);
                $this->CI->email->message($studentname.$message);
                $this->CI->email->send();
                $this->CI->email->clear();
            }
        }

        //微信通知
        foreach ($nobifytarget as $s) {
            $student = $this->CI->student_model->get_row(array('id' => $s));
            if(!empty($student['openid'])){
                $wxdata = array(
                    'first' => array(
                        'value' => '亲爱的' . $student['name'] . "
公司正在制定未来的培训计划,您可以进行部门内员工的课程报名审核,点击链接审核,感谢配合。",
                        'color' => "#173177"
                    ),
                    'keyword1' => array(
                        'value' => '年度计划待审核',
                        'color' => "#173177"
                    ),
                    'remark' => array(
                        'value' => "请点击详情进行处理",
                        'color' => "#173177"
                    )
                );
                $res = $this->CI->wechat->templateSend($student['openid'], 'iYvemgywJfyHxJu_lm_kS3-txaBnfR9gQCO93YCf0EA', $link_web, $wxdata);
            }
        }
    }


}

/* end of file */
