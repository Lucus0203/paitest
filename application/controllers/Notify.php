<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Notify extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('wechat','zhidingsms'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model','company_model','course_model','teacher_model','homework_model','survey_model','ratings_model','student_model','department_model'));
                
                $config['protocol']     = 'smtp';  
                $config['smtp_host']    = '127.0.0.1';  
                $config['smtp_user']    = 'mailservice';
                $config['smtp_pass']    = 'service';  
                $config['smtp_port']    = '25';  
                $config['charset']      = 'utf-8';  
                $config['mailtype']     = 'html';
                $config['smtp_timeout'] = '5';  
                $config['newline'] = "\r\n";
                $this->load->library ('email', $config);
		
	}
	
	
        //开课前一天通知  //时间触发
	public function coursestart() {
                $courses=$this->course_model->get_all(" notice_trigger_one=1 and time_start >= '".date('Y-m-d',strtotime('+1 day'))." 00:00:00' and time_start <= '".date('Y-m-d',strtotime('+1 day'))." 23:59:59' ");
                foreach ($courses as $c) {
                    if($c['isnotice_open']!=1){//自动通知关闭
                        continue;
                    }
                    $subject="《{$c['title']}》即将开课";
                    $company = $this->CI->company_model->get_row(array('code' => $c['company_code']));
                    $t=date('m月d日H时', strtotime($c['time_start']));
                    $sign=$company['name'];
                    $sign.=($company['code']=='100276')?' 人力资源部':'';
                    $this->load->database ();
                    $sql="select s.* from ".$this->db->dbprefix('student')." s left join ".$this->db->dbprefix('course_apply_list')." a on s.id=a.student_id where a.course_id=".$c['id']." and a.status=1 ";
                    $query = $this->db->query ($sql);
                    $students=$query->result_array();
                    foreach ($students as $s) {
                        //短信通知
                        if (!empty($s['mobile'])) {
                            /*$msg = "亲爱的{$s['name']}:
你已成功报名参加《{$subject}》课程即将开课。该课程将于{$t}在{$c['address']}举行，请安排好工作，或做好出差计划，准时参加课程。
上课前，请做好课前作业，提交给我们。
签到在开课前2小时生效，别忘了签到哦，谢谢！

" . $company['name'];
                            if($company['code']=='100276'){
                                $msg.="
人力资源部";
                            }
                            $msg.="
". date("Y年m月d日");
                            $this->CI->zhidingsms->sendSMS($s['mobile'], $msg);*/
                            $content='@1@='.$s['name'].',@2@='.$subject.',@3@='.$t.',@4@='.$c['address'].',@5@='.$sign.',@6@='.date("Y年m月d日");
                            $this->CI->zhidingsms->sendTPSMS($s['mobile'], $content,'ZD30018-0006');
                        }

                        //mail
                        if (!empty($s['email'])) {
                            $tomail = $s['email'];
                            $message = "亲爱的{$s['name']}:
<p style=\"text-indent:20px\">《{$c['title']}》课程将于{$t}在{$c['address']}举行，请安排好工作，或做好出差计划，准时参加课程。</p>
<p style=\"text-indent:20px\">上课前，请做好课前作业，提交给我们。</p>
<p style=\"text-indent:20px\">签到在开课前2小时生效，别忘了签到哦，谢谢！</p>

<p>" . $company['name'].'</p>';
                            if($company['code']=='100276'){
                                $message.='<p>人力资源部</p>';
                            }
                            $message.='<p>'. date("Y年m月d日").'</p>';
                            $this->email->from('service@trainingpie.com', '培训派');
                            $this->email->to($tomail);//
                            $this->email->subject($subject);
                            $this->email->message($message);
                            $this->email->send();
                            $this->email->clear();
                        }
                        //微信通知
                        if (!empty($s['openid'])) {
                            $wxdata = array(
                                'userName' => array(
                                    'value' => $s['name'],
                                    'color' => "#173177"
                                ),
                                'courseName' => array(
                                    'value' => $c['title'],
                                    'color' => "#173177"
                                ),
                                'date' => array(
                                    'value' => date('m月d日H点', strtotime($c['time_start'])) . "在" . $c['address'],
                                    'color' => "#173177"
                                ),
                                'remark' => array(
                                    'value' => "请安排好工作，或做好出差计划，准时参加课程。
上课前，请做好课前作业，提交给我们。
签到在开课前2小时生效，别忘了签到哦，谢谢！",
                                    'color' => "#173177"
                                )
                            );
                            $res = $this->wechat->templateSend($s['openid'], '5yxj6pEwlEw9xB0fFy-xUp6ec0azoAvPYA-tE-uBwDU', $this->config->item('web_url') . 'course/courseinfo/' . $c['id'] . '.html', $wxdata);
                        }
                    }
                }
	}
        
        //课程跟进 //时间触发
        public function coursefollow($courseid){
            
        }
	
}
