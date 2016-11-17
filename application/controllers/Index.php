<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Index extends CI_Controller
{
    var $_logininfo;

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model','useractionlog_model', 'course_model', 'teacher_model', 'homework_model','company_model','annualsurvey_model','annualcoursetype_model','annualcourse_model','annualquestion_model','annualoption_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }
    }

    public function main(){
        $logininfo = $this->_logininfo;
        $company = $this->company_model->get_row(array('code'=>$logininfo['company_code']));
        $sql = "select count(*) as num from " . $this->db->dbprefix('course') . " c where c.company_code = '{$logininfo['company_code']}' and c.isdel=2 ";
        $query = $this->db->query($sql)->row_array();
        $courses_num = $query['num'];
        $query = $this->db->query(" select count(*) num from " . $this->db->dbprefix('teacher') . " t where company_code='{$logininfo['company_code']}' and isdel=2 ")->row_array();
        $teachers_num = $query['num'];
        $query = $this->db->query(" select count(*) num from " . $this->db->dbprefix('student') . " s where company_code='{$logininfo['company_code']}' and isdel = 2 ")->row_array();
        $students_num = $query['num'];
        $query = $this->db->query(" select count(*) num from " . $this->db->dbprefix('student') . " s where company_code='{$logininfo['company_code']}' and role=2 ")->row_array();
        $adms_num = $query['num'];

        //最新课程
        $sql = "select c.*,t.name as teacher from " . $this->db->dbprefix('course') . " c "
            . "left join " . $this->db->dbprefix('teacher') . " t on c.teacher_id=t.id "
            . "where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 order by c.id desc limit 5 ";
        $query = $this->db->query($sql);
        $courses = $query->result_array();

        //学员登录二维码
        if(!file_exists(base_url().'uploads/login_qrcode/'.$logininfo['company_code'].'.png')){
            $this->load->library('ciqrcode');
            $params['data'] = $this->config->item('web_url') . 'login/index/' . $logininfo['company_code'] . '.html';
            $params['level'] = 'H';
            $params['size'] = 1025;
            $params['savename'] = './uploads/login_qrcode/' . $logininfo['company_code'].'.png';
            $this->ciqrcode->generate($params);
        }

        //创建测试数据
        $this->createtestdata();

        $this->load->view('header');
        $this->load->view('index', compact('courses_num', 'teachers_num', 'students_num', 'adms_num', 'courses','company'));
        $this->load->view('footer');
    }


    public function index()
    {
        $this->load->view('main');
    }

    public function guidReaded(){
        $userinfo = $this->_logininfo;
        $this->user_model->update(array('guid_step' => 5), $userinfo['id']);
        $userinfo['guid_step'] = 5;
        $this->session->set_userdata('loginInfo', $userinfo);
        $this->load->vars(array('loginInfo' => $userinfo));
        echo 1;
    }

    private function createtestdata(){//创建测试数据;
        //年度调研模板
        if($this->annualsurvey_model->get_count(array('company_code'=>$this->_logininfo['company_code'])) <= 0){
            $surveyid=11;
            $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
            if(!empty($survey['id'])){
                $c = array('company_code' => $this->_logininfo['company_code'],
                    'title' => $survey['title'],
                    'info' => $survey['info'],
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
                $types=$this->annualcoursetype_model->get_all(array('annual_survey_id'=>$surveyid));
                foreach($types as $t){
                    $typdid=$this->annualcoursetype_model->create(array('annual_survey_id'=>$id,'company_code'=>$this->_logininfo['company_code'],'annual_course_library_type_id'=>$t['annual_course_library_type_id'],'name'=>$t['name']));
                    $courses=$this->annualcourse_model->get_all(array('annual_survey_id'=>$surveyid,'annual_course_type_id'=>$t['id']));
                    foreach ($courses as $c){
                        //复制课程
                        $this->annualcourse_model->create(array('annual_survey_id'=>$id,'company_code'=>$this->_logininfo['company_code'],'title'=>$c['title'],'annual_course_type_id'=>$typdid,'annual_course_library_id'=>$c['annual_course_library_id']));
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
            }

        }
    }

    public function loginqrcode(){
        force_download('./uploads/login_qrcode/' . $this->_logininfo['company_code'].'.png', NULL);
    }
}
