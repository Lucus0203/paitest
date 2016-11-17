<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Annualsurvey extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session','pagination'));
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model','useractionlog_model', 'company_model', 'purview_model', 'industries_model','student_model','department_model','annualsurvey_model','annualquestion_model','annualoption_model','annualanswer_model','annualanswerdetail_model','annualcoursetype_model','annualcourse_model','annualcourselibrary_model','annualcourselibrarytype_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id'=>$this->_logininfo['id'],'url'=>uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }

    private function escapeVal($val){
        return !empty($val)?$this->db->escape($val):'';
    }

    public function index()
    {
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $parm['status'] = $this->input->get('status');
        $parm['keyword'] = $this->input->get('keyword');
        $parm['time_start'] = $this->input->get('time_start');
        $parm['time_end'] = $this->input->get('time_end');
        $pvalue=array_map(array($this,'escapeVal'),$parm);//防sql注入
        $this->load->database();
        //status 1进行中2未开始3已结束
        $sql = "select a.*,if( unix_timestamp(now()) < unix_timestamp(a.time_start),2,if( unix_timestamp(now()) > unix_timestamp(a.time_end),3,1) ) as status from " . $this->db->dbprefix('annual_survey') . " a "
            . "where a.company_code = " . $this->_logininfo['company_code'] . " and a.isdel=2 ";
        if ($parm['status'] == 2) {//未开始
            $sql .= " and unix_timestamp(now()) < unix_timestamp(a.time_start) ";
        } elseif ($parm['status'] == 3) {//已结束
            $sql .= " and unix_timestamp(now()) > unix_timestamp(a.time_end) ";
        } elseif ($parm['status'] == 1) {//进行中
            $sql .= " and unix_timestamp(now()) >= unix_timestamp(a.time_start) and unix_timestamp(now()) <= unix_timestamp(a.time_end) ";
        }
        if (!empty($parm['keyword'])) {
            $sql .= " and (a.title like '%" .  $this->db->escape_like_str($parm['keyword']) . "%' )";
        }
        if (!empty($parm['time_start'])) {
            $sql .= " and unix_timestamp(time_start) >= unix_timestamp(" . $pvalue['time_start']  . ") ";
        }
        if (!empty($parm['time_end'])) {
            $sql .= " and unix_timestamp(time_start) <= unix_timestamp(" . $pvalue['time_end'] . ") ";
        }
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualsurvey/index') . '?keyword=' . $parm['keyword'] . '&time_start=' . $parm['time_start'] . '&time_end=' . $parm['time_end'] . '&status=' . $parm['status'];
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $query = $this->db->query($sql . " order by a.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $surveies = $query->result_array();

        $isAccessAccount=$this->isAccessAccount();

        $this->load->view('header');
        $this->load->view('annual_survey/list', array('surveies' => $surveies,'parm' => $parm,'isAccessAccount'=>$isAccessAccount, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }

    public function create(){
        $act = $this->input->post('act');
        $errmsg = '';
        $survey = array();
        if (!empty($act)) {
            $logininfo = $this->_logininfo;
            $c = array('company_code' => $logininfo['company_code'],
                'title' => $this->input->post('title'),
                'info' => $this->input->post('info'),
                'created'=>date("Y-m-d H:i:s"));
            $id = $this->annualsurvey_model->create($c);
            //二维码
            $survey = array('qrcode'=>$id . rand(1000, 9999));
            $this->load->library('ciqrcode');
            $params['data'] = $this->config->item('web_url') . 'annual/answer/'.$id.'.html';
            $params['level'] = 'H';
            $params['size'] = 1024;
            $params['savename'] = './uploads/annualqrcode/' . $survey['qrcode'] . '.png';
            $this->ciqrcode->generate($params);
            $this->annualsurvey_model->update($survey, $id);
            redirect(site_url('annualsurvey/info/'.$id));
            return;
        }
        $this->load->view('header');
        $this->load->view('annual_survey/edit',compact('errmsg','survey'));
        $this->load->view('footer');
    }

    //编辑
    public function edit($surveyid){
        $this->isAllowAnnualid($surveyid);
        $act = $this->input->post('act');
        $msg = $errmsg = '';
        if (!empty($act)) {
            $survey = array('title' => $this->input->post('title'),
                'info' => $this->input->post('info'));
            $this->annualsurvey_model->update($survey, $surveyid);
            $msg = '保存成功';
            redirect(site_url('annualsurvey/info/'.$surveyid));
        }
        $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        $this->load->view('header');
        $this->load->view('annual_survey/edit',compact('survey','msg'));
        $this->load->view('footer');
    }

    public function copy($surveyid){
        $this->isAllowAnnualid($surveyid);
        $act = $this->input->post('act');
        $errmsg = '';
        $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        $survey['title'].='(副本)';
        unset($survey['time_start']);
        unset($survey['time_end']);
        if (!empty($act)) {
            $logininfo = $this->_logininfo;
            $c = array('company_code' => $logininfo['company_code'],
                'title' => $this->input->post('title'),
                'info' => $this->input->post('info'),
                'created'=>date("Y-m-d H:i:s"));
            $id = $this->annualsurvey_model->create($c);
            //二维码
            $survey = array('qrcode'=>$id . rand(1000, 9999));
            $this->load->library('ciqrcode');
            $params['data'] = $this->config->item('web_url') . 'annual/answer/'.$id.'.html';
            $params['level'] = 'H';
            $params['size'] = 1024;
            $params['savename'] = './uploads/annualqrcode/' . $survey['qrcode'] . '.png';
            $this->ciqrcode->generate($params);
            $this->annualsurvey_model->update($survey, $id);
            //复制课程类型
            $types=$this->annualcoursetype_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid));
            foreach($types as $t){
                $typdid=$this->annualcoursetype_model->create(array('annual_survey_id'=>$id,'company_code'=>$t['company_code'],'annual_course_library_type_id'=>$t['annual_course_library_type_id'],'name'=>$t['name']));
                $courses=$this->annualcourse_model->get_all(array('annual_survey_id'=>$surveyid,'annual_course_type_id'=>$t['id']));
                foreach ($courses as $c){
                    //复制课程
                    $this->annualcourse_model->create(array('annual_survey_id'=>$id,'company_code'=>$c['company_code'],'title'=>$c['title'],'annual_course_type_id'=>$typdid,'annual_course_library_id'=>$c['annual_course_library_id']));
                }
            }
            //复制问题
            $questions=$this->annualquestion_model->get_all(array('annual_survey_id'=>$surveyid));
            foreach ($questions as $q){
                $questionid=$this->annualquestion_model->create(array('annual_survey_id'=>$id,'type'=>$q['type'],'module'=>$q['module'],'title'=>$q['title'],'required'=>$q['required']));
                $options=$this->annualoption_model->get_all(array('annual_survey_id'=>$surveyid,'annual_question_id'=>$q['id']));
                foreach ($options as $o){
                    //复制选项
                    $this->annualoption_model->create(array('annual_survey_id'=>$id,'annual_question_id'=>$questionid,'content'=>$o['content']));
                }
            }
            redirect(site_url('annualsurvey/info/'.$id));
            return;
        }
        $this->load->view('header');
        $this->load->view('annual_survey/edit',compact('errmsg','survey'));
        $this->load->view('footer');
    }

    //发布
    public function starting($surveyid){
        $this->isAllowAnnualid($surveyid);
        $act = $this->input->post('act');
        $msg = $errmsg = '';
        if (!empty($act)) {
            $survey = array('time_start'=>$this->input->post('time_start'),
                'time_end'=>$this->input->post('time_end'),
                'target' => $this->input->post('target'),
                'targetone' => $this->input->post('targetone'),
                'targettwo' => $this->input->post('targettwo'),
                'targetstudent' => $this->input->post('targetstudent'),
                'public' => 2);
            $isexit=$this->exitSurveyCount($surveyid);
            if($isexit>0){
                $errmsg='同一时间仅可发布一份年度需求调研,请修改您的调查时间';
            }else {
                $this->annualsurvey_model->update($survey, $surveyid);
                //学员名单同步
                if(!empty($survey['targetstudent'])){
                    $target=$targetid='';
                    $targetstudent = $this->student_model->get_all(" id in (" . $survey['targetstudent'] . ") and company_code='".$this->_logininfo['company_code']."' and isdel=2 ");
                    if (!empty($targetstudent)) {
                        $targetstudentid = array_column($targetstudent, 'id');
                        $targetid .= implode(",", $targetstudentid);
                        $targetstudent = array_column($targetstudent, 'name');
                        $target .= implode(",", $targetstudent);
                    }
                    $survey['targetstudent']=$targetid;
                    $survey['target']=$target;
                }
                $this->annualsurvey_model->update($survey, $surveyid);
                //创建回答日志并发送通知
                $studentsarr = explode(',', $survey['targetstudent']);
                $notify_target=array();
                foreach ($studentsarr as $s) {
                    $annual=$this->annualanswer_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'student_id'=>$s,'annual_survey_id'=>$surveyid));
                    if(empty($annual['id'])){
                        $this->annualanswer_model->create(array('company_code'=>$this->_logininfo['company_code'],'student_id'=>$s,'annual_survey_id'=>$surveyid));
                    }
                    if(empty($annual['id'])||$annual['step']!='5'){
                        $notify_target[]=$s;
                    }
                }
                //通知对象
                if(count($notify_target)>0){
                    $this->load->library(array('notifyclass'));
                    $this->notifyclass->annualsurveystart($surveyid,$notify_target);
                }

                $msg = '保存成功';
                redirect(site_url('annualsurvey/info/'.$surveyid));
            }

        }
        $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        //培训对象数据
        $deparone = $this->department_model->get_all(array('company_code' => $this->_logininfo['company_code'], 'level' => 0));
        if (!empty($deparone[0]['id'])) {
            $departwo = $this->department_model->get_all(array('parent_id' => $deparone[0]['id']));
            if($this->student_model->get_count("company_code='".$this->_logininfo['company_code']."' and department_id=".$deparone[0]['id']." and department_id=department_parent_id and isdel = 2 ")>0){
                $departwo[]=array('id'=>$deparone[0]['id'],'parent_id'=>$deparone[0]['id'],'name'=>'未分配','level'=>1);
            }
        }
        $student_departmentid=$departwo[0]['id']??$deparone[0]['id'];
        if (!empty($student_departmentid)) {
            $students = $this->student_model->get_all(array('department_id' => $student_departmentid,'isdel'=>2));
        }
        $this->load->view('header');
        $this->load->view('annual_survey/public',compact('survey','msg','errmsg','deparone', 'departwo', 'students'));
        $this->load->view('footer');
    }

    //暂停发布
    public function stoping($surveyid){
        $this->annualsurvey_model->update(array('public'=>3), $surveyid);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function isExistSurvey($surveyid=null){
        echo $this->exitSurveyCount($surveyid);
    }

    private function exitSurveyCount($surveyid=null){
        $time_start=$this->input->post('time_start');
        $time_end=$this->input->post('time_end');
        $countSql = "select count(*) as total from " . $this->db->dbprefix('annual_survey') . " a where a.isdel=2 and a.public=2 and a.company_code='".$this->_logininfo['company_code']."' and ( (unix_timestamp('".$time_start.":00') < unix_timestamp(a.time_start) and unix_timestamp('".$time_end.":00') > unix_timestamp(a.time_start)) or 
            (unix_timestamp('".$time_start.":00') < unix_timestamp(a.time_end) and unix_timestamp('".$time_end.":00') > unix_timestamp(a.time_end)) or 
            (unix_timestamp('".$time_start.":00') > unix_timestamp(a.time_start) and unix_timestamp('".$time_end.":00') < unix_timestamp(a.time_end)) ) ";
        $countSql .= !empty($surveyid)?" and id <> $surveyid ":'';
        //判断时间是否有重复的问卷
        $query=$this->db->query($countSql);
        $count=$query->row_array();
        return $count['total'];
    }

    public function info($surveyid){
        $this->isAllowAnnualid($surveyid);
        $anscount=$this->annualanswer_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid,'step'=>5));
        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));
        $this->load->view('header');
        $this->load->view('annual_survey/info', compact('survey','anscount'));
        $this->load->view('footer');
    }

    //问卷删除
    public function del($surveyid)
    {
        $this->isAllowAnnualid($surveyid);
        if (!empty($surveyid)) {
            $a = $this->annualsurvey_model->get_row(array('id' => $surveyid));
            if(strtotime($a['time_start'])<time()&&strtotime($a['time_end'])>time()&&$a['public']=='2'){
                echo '问卷进行中不可删除';
                return false;
            }
            if ($a['company_code'] == $this->_logininfo['company_code']) {
                $this->annualsurvey_model->update(array('isdel' => 1), $surveyid);
            }
        }
        redirect(site_url('annualsurvey/index'));
    }

    //QA设置
    public function qa($qatype,$surveyid){
        $this->isAllowAnnualid($surveyid);
        $module=1;
        switch ($qatype){
            case 'acceptance':
                $module=1;
                break;
            case 'organization':
                $module=2;
                break;
            case 'requirement':
                $module=3;
                break;
            default :
                break;
        }
        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));
        $questions=$this->annualquestion_model->get_all(array('annual_survey_id'=>$surveyid,'module'=>$module));
        foreach ($questions as $k=>$q){
            $questions[$k]['options']=$this->annualoption_model->get_all(array('annual_question_id'=>$q['id']));
        }
        $anscount=$this->annualanswer_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid,'step'=>5));
        $isStarted=$this->isStarted($surveyid);//问卷是否已开始
        $view=!$isStarted?'annual_survey/qa':'annual_survey/qa_view';
        $this->load->view('header');
        $this->load->view($view, compact('survey','qatype','questions','isStarted','anscount'));
        $this->load->view('footer');
    }

    //QA保存
    public function saveQa($qatype,$surveyid){
        $this->isAllowAnnualid($surveyid,false);
        $question=$this->input->post('question');
        $required=$this->input->post('required');
        $type=$this->input->post('type');
        $no=$this->input->post('no');

        $module=1;
        switch ($qatype){
            case 'acceptance':
                $module=1;
                break;
            case 'organization':
                $module=2;
                break;
            case 'requirement':
                $module=3;
                break;
            default :
                break;
        }
        //已开始过的问卷不可修改问题
        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));
        $isStarted=$this->isStarted($surveyid);//问卷是否已开始
        if($isStarted){
            echo 0;
        }else{
            $deldatasql="delete question,opt from ". $this->db->dbprefix('annual_question') ." question left join ". $this->db->dbprefix('annual_option') ." opt on question.id=opt.annual_question_id where question.annual_survey_id=".$this->db->escape($surveyid)." and module=$module ";
            $this->db->query($deldatasql);

            foreach ($question as $k=>$title){
                $q=array('annual_survey_id'=>$surveyid,'title'=>$title,'type'=>$type[$k],'module'=>$module,'required'=>$required[$k]);
                $qid=$this->annualquestion_model->create($q);
                $options=$this->input->post('option'.$no[$k]);
                foreach ($options as $op){
                    $o=array('annual_survey_id'=>$surveyid,'annual_question_id'=>$qid,'content'=>$op);
                    $this->annualoption_model->create($o);
                }
            }
            echo 1;
        }
    }

    //课程选择
    public function course($surveyid,$coursetypeid=null){
        $this->isAllowAnnualid($surveyid);
        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));
        $courselibrarytypes=$this->annualcourselibrarytype_model->get_all(array('ispublic'=>1));
        $coursetypes=$this->annualcoursetype_model->get_all(array('annual_survey_id'=>$surveyid,'company_code'=>$this->_logininfo['company_code']));
        if(!empty($coursetypeid)){
            $currentcoursetype=$this->annualcoursetype_model->get_row(array('id'=>$coursetypeid));
        }
        //查找课程
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $this->load->database();
        $sql = "select ac.*,act.name as typename from " . $this->db->dbprefix('annual_course') . " ac left join 
            " . $this->db->dbprefix('annual_course_type') . " act on ac.annual_course_type_id = act.id "
            . " where ac.company_code = " . $this->_logininfo['company_code'] . " and ac.annual_survey_id=$surveyid ";
        if(!empty($coursetypeid)){
            $sql.=" and ac.annual_course_type_id = ".$this->db->escape($coursetypeid);
        }
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualsurvey/course/'.$surveyid.'/'.$coursetypeid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $query = $this->db->query($sql . " order by ac.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $courses = $query->result_array();
        $links = $this->pagination->create_links();

        $anscount=$this->annualanswer_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid,'step'=>5));
        $isStarted=$this->isStarted($surveyid);//问卷是否已开始
        $res=$this->session->userdata('res_status');//返回状态
        $this->session->unset_userdata('res_status');
        $this->load->view('header');
        $this->load->view('annual_survey/course', compact('survey','courselibrarytypes','coursetypes','currentcoursetype','courses','links','total_rows','res','isStarted','anscount'));
        $this->load->view('footer');
    }

    //ajax课程选择
    public function courseSelect($surveyid){
        $library_type_id=$this->input->post('annual_course_library_type_id');
        $library_type=$this->annualcourselibrarytype_model->get_row(array('id'=>$library_type_id));
        if(!empty($library_type_id)&&!empty($surveyid)&&$this->isAllowAnnualid($surveyid,false)){
            $count=$this->annualcoursetype_model->get_count(array('annual_survey_id'=>$surveyid,'annual_course_library_type_id'=>$library_type_id));
            $type_name=$count>0?$library_type['name'].($count+1):$library_type['name'];
            $coursetypeid=$this->annualcoursetype_model->create(array('annual_survey_id'=>$surveyid,'company_code'=>$this->_logininfo['company_code'],'annual_course_library_type_id'=>$library_type_id,'name'=>$type_name));
            $insertSql="insert into ".$this->db->dbprefix('annual_course')." (`annual_survey_id`, `company_code`, `title`, `annual_course_library_id`,`annual_course_type_id`, `created`) select $surveyid,'".$this->_logininfo['company_code']."' , title,id, $coursetypeid, CURRENT_TIMESTAMP from ".$this->db->dbprefix('annual_course_library')." cl where cl.type_id=$library_type_id ;";
            $this->db->query($insertSql);
            echo site_url('annualsurvey/course/'.$surveyid.'/'.$coursetypeid);
        }else{
            echo 0;
        }
    }

    //ajax课程添加
    public function courseTypeAdd($surveyid){
        $name=$this->input->post('name');
        if(!empty($name)&&!empty($surveyid)&&$this->isAllowAnnualid($surveyid,false)){
            $coursetypeid=$this->annualcoursetype_model->create(array('annual_survey_id'=>$surveyid,'company_code'=>$this->_logininfo['company_code'],'name'=>$name));
            echo site_url('annualsurvey/course/'.$surveyid.'/'.$coursetypeid);
        }else{
            echo 0;
        }
    }

    //删除课程类型
    public function delType($surveyid,$coursetypeid){
        if(!empty($coursetypeid)&&!empty($surveyid)&&$this->isAllowAnnualid($surveyid,false)){
            $this->annualcoursetype_model->del($coursetypeid);
            $this->annualcourse_model->del(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid,'	annual_course_type_id'=>$coursetypeid));
            redirect(site_url('annualsurvey/course/'.$surveyid).'?res=success');
        }else{
            redirect(site_url('annualsurvey/course/'.$surveyid).'?res=fail');
        }
    }
    //删除课程
    public function delCourse($surveyid,$courseid){
        if(!empty($courseid)&&!empty($surveyid)&&$this->isAllowAnnualid($surveyid,false)){
            $this->annualcourse_model->del(array('company_code'=>$this->_logininfo['company_code'],'id'=>$courseid));
            $this->session->set_userdata('res_status','success');
        }else{
            $this->session->set_userdata('res_status','fail');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    //ajax课程类型编辑
    public function courseTypeEdit($surveyid){
        $name=$this->input->post('name');
        $coursetypeid=$this->input->post('coursetypeid');
        if(!empty($name)&&!empty($coursetypeid)&&!empty($surveyid)&&$this->isAllowAnnualid($surveyid,false)){
            $this->annualcoursetype_model->update(array('name'=>$name),$coursetypeid);
            $this->session->set_userdata('res_status','success');
        }else{
            $this->session->set_userdata('res_status','fail');
        }
        echo $_SERVER['HTTP_REFERER'];
    }
    //ajax保存课程
    public function courseSave($surveyid,$coursetypeid){
        $title=$this->input->post('title');
        $courseid=$this->input->post('courseid');
        if(!empty($title)&&!empty($surveyid)&&$this->isAllowAnnualid($surveyid,false)){
            if(empty($courseid)){
                $this->annualcourse_model->create(array('annual_survey_id'=>$surveyid,'company_code'=>$this->_logininfo['company_code'],'title'=>$title,'annual_course_type_id'=>$coursetypeid));
            }else{
                $this->annualcourse_model->update(array('title'=>$title),$courseid);
            }
            $this->session->set_userdata('res_status','success');
        }else{
            $this->session->set_userdata('res_status','fail');
        }
        echo $_SERVER['HTTP_REFERER'];

    }

    //提交名单
    public function surveylist($surveyid){
        $this->isAllowAnnualid($surveyid);
        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));

        //提交学员
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $this->load->database();
        $sql = "select s.name,s.job_code,s.job_name,s.mobile,d.name as department,a.id as answer_id,a.created "
            . "from " . $this->db->dbprefix('annual_answer') . " a left join " . $this->db->dbprefix('student') . " s on a.student_id=s.id "
            . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id "
            . " where a.company_code = " . $this->_logininfo['company_code'] . " and a.annual_survey_id=$surveyid and a.step=5 ";
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualsurvey/surveylist/'.$surveyid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $query = $this->db->query($sql . " order by a.created desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $students = $query->result_array();
        $links = $this->pagination->create_links();
        $anscount=$this->annualanswer_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid,'step'=>5));
        $this->load->view('header');
        $this->load->view('annual_survey/surveylist', compact('survey','students','links','total_rows','anscount'));
        $this->load->view('footer');
    }

    //问卷详情
    public function answerdetail($answerid){
        $answer=$this->annualanswer_model->get_row(array('id'=>$answerid));
        $survey=$this->annualsurvey_model->get_row(array('id'=>$answer['annual_survey_id']));
        $student=$this->student_model->get_row(array('id'=>$answer['student_id']));
        $depart = $this->department_model->get_row(array('id'=>$student['department_id']));
        $student['department'] = $depart['name'];
        $step=array('1'=>'acceptance','2'=>'organization','3'=>'requirement');
        foreach ($step as $sk=>$s){
            $questions=$this->annualquestion_model->get_all(array('annual_survey_id'=>$survey['id'],'module'=>$sk));
            foreach ($questions as $k=>$q){
                $answersql="select d.answer_content,o.content as option_title from " . $this->db->dbprefix('annual_answer_detail') . " d left join " . $this->db->dbprefix('annual_option') . " o on d.annual_option_id=o.id "
                    . " where d.annual_answer_id = " . $answerid . " and d.annual_question_id = ".$q['id'];
                $query = $this->db->query($answersql);
                $questions[$k]['answer']=($q['type']==2)?$query->result_array():$query->row_array();
            }
            $answer[$s]=$questions;
        }
        $sql = "select c.title "
            . "from " . $this->db->dbprefix('annual_answer_course') . " a left join " . $this->db->dbprefix('annual_course') . " c on a.annual_course_id=c.id "
            . " where a.company_code = " . $this->_logininfo['company_code'] . " and a.annual_answer_id=$answerid ";
        $query = $this->db->query($sql . " order by a.created asc ");
        $answer['courses'] = $query->result_array();
        $this->load->view('annual_survey/answer_detail', compact('survey','answer','student','step'));
    }

    //答案统计
    public function answeranalysis($surveyid){
        $this->isAllowAnnualid($surveyid);
        $parm['keyword'] = $this->input->get('keyword');
        $parm['department_parent_id'] = $this->input->get('department_parent_id');
        $parm['department_id'] = $this->input->get('department_id');
        $pvalue=array_map(array($this,'escapeVal'),$parm);//防sql注入
        $where = "parent_id is null and company_code = '{$this->_logininfo['company_code']}' ";
        $departments = $this->department_model->get_all($where);
        $second_departments = !empty($parm['department_id'])?$this->department_model->get_all(array('parent_id' => $pvalue['department_id'])):array();

        $keyword = !empty($parm['keyword'])?" and (q.title like '%" .  $this->db->escape_like_str($parm['keyword']) . "%' )":'';
        $departWhere = !empty($parm['department_parent_id'])?" and s.department_parent_id = ".$pvalue['department_parent_id']:'';
        $departWhere .= !empty($parm['department_id'])?" and s.department_id = ".$pvalue['department_id']:'';


        $survey = $this->annualsurvey_model->get_row(array('id' => $surveyid,'company_code' => $this->_logininfo['company_code']));
        $step=array('1'=>'acceptance','2'=>'organization','3'=>'requirement');
        foreach ($step as $sk=>$s){
            $questionsql="select * from ".$this->db->dbprefix('annual_question')." q "
                ." where annual_survey_id=$surveyid and module = $sk";
            $questionsql .= $keyword;
            $questionsql.=" order by id asc ";
            $query=$this->db->query($questionsql);
            $questions=$query->result_array();
            foreach ($questions as $k=>$q){
                if($q['type']==1||$q['type']==2){
                    $answersql="select o.content as option_title,count(d.id) as num from " . $this->db->dbprefix('annual_option') . " o "
                        . "left join " . $this->db->dbprefix('annual_answer_detail') . " d on d.annual_option_id=o.id "
                        . "left join " . $this->db->dbprefix('student') . " s on d.student_id = s.id ".$departWhere
                        . " where o.annual_question_id = " . $q['id'];
                    $answersql.= " group by o.id ";
                    $query = $this->db->query($answersql);
                    $questions[$k]['answer']=$query->result_array();
                }elseif($q['type']==3){
                    $answersql="select s.name,d.answer_content from " . $this->db->dbprefix('annual_answer_detail') . " d  "
                        . "left join " . $this->db->dbprefix('student') . " s on d.student_id = s.id ".$departWhere
                        . " where d.annual_question_id = ".$q['id'];
                    $query = $this->db->query($answersql);
                    $questions[$k]['answer']=$query->result_array();
                }
                $answersql="select d.* from " . $this->db->dbprefix('annual_answer_detail') . " d  "
                    . "left join " . $this->db->dbprefix('student') . " s on d.student_id = s.id "
                    . " where d.annual_question_id = ".$q['id'];
                $answersql.=$departWhere;
                $answersql.=" group by d.student_id ";
                $query = $this->db->query("select count(a.id) as total from ($answersql) a ");
                $total=$query->row_array();
                $questions[$k]['total']=$total['total'];
            }
            $answer[$s]=$questions;
        }
        //课程统计
        $sql = "select c.title,count(a.id) as num "
            . "from " . $this->db->dbprefix('annual_course') . " c "
            . "left join " . $this->db->dbprefix('annual_answer_course') . " a on a.annual_course_id=c.id "
            . "left join " . $this->db->dbprefix('student') . " s on a.student_id = s.id "
            . " where c.company_code = " . $this->_logininfo['company_code'] . " and c.annual_survey_id=$surveyid ";
        $sql.=$departWhere;
        $query = $this->db->query($sql . " group by c.id order by c.created asc ");
        $answer['courses']['detail'] = $query->result_array();

        $answersql="select c.id from " . $this->db->dbprefix('annual_answer_course') . " c  "
            . "left join " . $this->db->dbprefix('student') . " s on c.student_id = s.id "
            . " where c.annual_survey_id = ".$surveyid;
        $answersql.=$departWhere;
        $answersql.=" group by c.student_id ";
        $query = $this->db->query("select count(a.id) as total from ($answersql) a ");
        $total=$query->row_array();
        $answer['courses']['total']=$total['total'];
        $anscount=$this->annualanswer_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$surveyid,'step'=>5));

        $this->load->view('header');
        $this->load->view('annual_survey/answer_analysis', compact('parm','survey','departments','second_departments','answer','anscount'));
        $this->load->view('footer');

    }

    //下载签到二维码
    public function downloadqrcode($surveyid)
    {
        $this->isAllowAnnualid($surveyid);
        $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        force_download($survey['title'].'.png',file_get_contents('uploads/annualqrcode/'.$survey['qrcode'].'.png'));

    }


    /**
     * 匹配通知学员
     */
    public function updateTarget(){
        $data['company_code']=$this->_logininfo['company_code'];
        $data['target_student']=$this->input->post('targetstudent');
        $target='';
        if(!empty($data['target_student'])) {
            $targetstudent = $this->student_model->get_all(" id in (" . $data['target_student'] . ") and company_code='".$this->_logininfo['company_code']."' and isdel=2 ");
            if (!empty($targetstudent)) {
                $targetstudent = array_column($targetstudent, 'name');
                $target .= implode(",", $targetstudent);
            }
        }
        $data['target']=$target;
        $res = $target;//mb_strlen($target, 'utf-8') > 20 ? mb_substr( $target,0,40,"utf-8").'...':$target;
        echo $res;
    }

    //是否是自己公司下的问卷
    private function isAllowAnnualid($surveyid,$redirect=true){
        if(empty($surveyid)||$this->annualsurvey_model->get_count(array('id' => $surveyid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualsurvey/index'));}
            return false;
        }else{
            return true;
        }
    }

    //是否是正式账号
    private function isAccessAccount(){
        $ordersql="select count(*) as num FROM pai_company_order company_order where company_order.module='annualplan' and company_order.company_code=".$this->_logininfo['company_code']." and company_order.checked=1 and (company_order.use_num=0 or company_order.use_num_remain > 0) and (company_order.years=0 or (date_add(company_order.start_time, interval company_order.years year) > NOW() and company_order.start_time < NOW() ) )";
        $query = $this->db->query($ordersql);
        $res = $query->row_array();
        if($res['num']>0){
            return true;
        }else{
            return false;
        }
    }

    private function isStarted($surveyid){
        $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        return ($survey['public']!=1);//问卷是否已开始
    }

}
