<?php
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: lucus
 * Date: 2016/10/14
 * Time: 下午2:49
 */
class Annualplan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session','pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model','useractionlog_model', 'company_model','teacher_model','course_model', 'purview_model', 'industries_model','student_model','teacher_model','department_model','annualsurvey_model','annualplan_model','annualplancourse_model','annualcourse_model','annualplancourselist_model','annualanswercourse_model','annualcoursetype_model','annualcourselibrary_model'));

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

    //年度计划
    public function index(){
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $this->load->database();
        //status 1进行中2未开始3已结束
        $sql = "select p.*,a.title as survey_title from " . $this->db->dbprefix('annual_plan') . " p left join " . $this->db->dbprefix('annual_survey') . " a on p.annual_survey_id = a.id "
            . "where p.isdel != 1 and p.company_code = " . $this->_logininfo['company_code'] ;
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualplan/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $query = $this->db->query($sql . " order by p.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $plans = $query->result_array();

        $isAccessAccount=$this->isAccessAccount();

        $this->load->view('header');
        $this->load->view('annual_plan/list', array('plans' => $plans,'isAccessAccount'=>$isAccessAccount, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');
    }

    //年度计划创建
    public function create($surveyid){
        $act = $this->input->post('act');
        if (!empty($act)) {
            $plan = array('title' => $this->input->post('title'),
                'company_code' => $this->_logininfo['company_code'],
                'annual_survey_id' => $this->input->post('annual_survey_id'),
                'note' => $this->input->post('note'));
            $id=$this->annualplan_model->create($plan);
            redirect(site_url('annualplan/course/'.$id));
        }
        $anscountsql="select count(aa.id) as anscount,aa.annual_survey_id from ".$this->db->dbprefix('annual_answer')." aa where aa.company_code='".$this->_logininfo['company_code']."' and step = 5 group by aa.annual_survey_id ";
        $sql=" select survey.* from ".$this->db->dbprefix('annual_survey')." survey left join ($anscountsql) ans on survey.id=ans.annual_survey_id where survey.company_code='".$this->_logininfo['company_code']."' and isdel=2 and anscount > 0 ";
        $query = $this->db->query($sql);
        $surveys = $query->result_array();
        if(!empty($surveyid)){
            $this->isAllowAnnualid($surveyid);
            $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        }
        $this->load->view('header');
        $this->load->view('annual_plan/edit',compact('surveys','survey'));
        $this->load->view('footer');
    }
    //年度计划编辑
    public function edit($planid){
        $act = $this->input->post('act');
        if (!empty($act)) {
            $plan = array('title' => $this->input->post('title'),
                'note' => $this->input->post('note'));
            $this->annualplan_model->update($plan,$planid);
            redirect(site_url('annualplan/course/'.$planid));
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $survey=$this->annualsurvey_model->get_row(array('id'=>$plan['annual_survey_id']));
        $this->load->view('header');
        $this->load->view('annual_plan/edit',compact('surveys','survey','plan'));
        $this->load->view('footer');
    }


    //课程信息
    public function course($planid){
        $this->isAllowPlanid($planid);
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $openstatus=$this->input->get('openstatus');
        $typeid=$this->input->get('typeid');
        $parm=array();
        $sql = "select ac.id,ac.title as course_title,course.title,course.price,course.day,course.external,act.name as type_name,course.openstatus,aac.num,apcl.list_num from " .
            $this->db->dbprefix('annual_course') . " ac left join ".
            $this->db->dbprefix('annual_plan_course')." course on ac.id=course.annual_course_id and course.annual_plan_id=$planid left join ".
            $this->db->dbprefix('annual_course_type')." act on ac.annual_course_type_id=act.id left join " .
            "( select count(aac.id) as num,aac.annual_course_id from ".$this->db->dbprefix('annual_answer_course') . " aac where aac.company_code ='" . $this->_logininfo['company_code'] ."' and aac.annual_survey_id=".$plan['annual_survey_id']." group by aac.annual_course_id) aac on aac.annual_course_id = ac.id left join ".
            "( select count(apcl.id) as list_num,apcl.annual_course_id from ".$this->db->dbprefix('annual_plan_course_list')." apcl where apcl.company_code ='" . $this->_logininfo['company_code'] ."' and apcl.annual_plan_id=".$plan['id']." and apcl.status = '1' group by apcl.annual_course_id) apcl on apcl.annual_course_id=ac.id  ".
            "where ac.company_code = '" . $this->_logininfo['company_code'] ."' and ac.annual_survey_id = ".$plan['annual_survey_id'] ;
        if(!empty($openstatus)){
            $sql.=$openstatus==1?" and openstatus = 1 ":" and (openstatus != 1 or openstatus is null) ";
            $parm['openstatus']=$openstatus;
        }
        if(!empty($typeid)){
            $sql.=" and ac.annual_course_type_id = ".$this->escapeVal($typeid);
            $parm['typeid']=$typeid;
        }
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('annualplan/course/'.$planid). '?openstatus=' . $parm['openstatus'] . '&typeid=' . $parm['typeid'];
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();
        $query = $this->db->query($sql . " order by ac.id asc limit " . ($page - 1) * $page_size . "," . $page_size);
        $courses = $query->result_array();

        $total=$this->annualcourse_model->get_count("company_code=" . $this->_logininfo['company_code'] ." and annual_survey_id = ".$plan['annual_survey_id']);
        $total_open=$this->annualplancourse_model->get_count("company_code=" . $this->_logininfo['company_code'] ." and annual_plan_id = ".$planid." and openstatus = 1 ");
        $typies=$this->annualcoursetype_model->get_all(array('annual_survey_id'=>$plan['annual_survey_id']));
        $total_syncoursed=$this->annualplancourse_model->get_count("company_code=" . $this->_logininfo['company_code'] ." and annual_plan_id = ".$planid." and course_id is not null and course_id != '' ");

        $total_syncoursed_opened_sql="select count(*) as num from ".$this->db->dbprefix('annual_plan_course')." apc left join ".$this->db->dbprefix('course')." c on apc.course_id=c.id where apc.annual_plan_id = ".$planid." and c.isdel = 2 ";
        $query = $this->db->query($total_syncoursed_opened_sql);
        $total_syncoursed_opened=$query->row_array();
        $total_syncoursed_opened=$total_syncoursed_opened['num'];

        $this->load->view('header');
        $this->load->view('annual_plan/course',compact('plan','courses','links','total_open','total','parm','typies','total_syncoursed','total_syncoursed_opened'));
        $this->load->view('footer');
    }

    //开课课程信息
    public function opencourse($planid,$annualcourseid){
        $this->isAllowPlanid($planid);
        if(empty($annualcourseid)||$this->annualcourse_model->get_count(array('id' => $annualcourseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            redirect(site_url('annualplan/index'));
            return false;
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $annualcourse=$this->annualcourse_model->get_row(array('id'=>$annualcourseid));
        $act=$this->input->post('act');
        if(!empty($act)){
            $c['title']=$this->input->post('title');
            $c['year']=$this->input->post('year');
            $c['month']=$this->input->post('month');
            $c['teacher_id']=!empty($this->input->post('teacher_id'))?$this->input->post('teacher_id'):null;
            $c['price']=!empty($this->input->post('price'))?$this->input->post('price'):null;
            $c['external']=$this->input->post('external');
            $c['day']=!empty($this->input->post('day'))?$this->input->post('day'):null;
            $c['supplier']=$this->input->post('supplier');
            $c['people']=!empty($this->input->post('people'))?$this->input->post('people'):null;
            $c['info']=$this->input->post('info');
            $c['openstatus']=1;

            if($this->annualplancourse_model->get_count(array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid))>0){
                $this->annualplancourse_model->update($c,array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
            }else{
                $c['company_code']=$this->_logininfo['company_code'];
                $c['annual_plan_id']=$planid;
                $c['annual_course_id']=$annualcourseid;
                $c['annual_course_type_id']=$annualcourse['annual_course_type_id'];
                $apc_id=$this->annualplancourse_model->create($c);
            }

            if($plan['syn_status']==1){
                //同步课程内容
                $syncourse = array('user_id'=>$this->_logininfo['id'],
                    'company_code' => $this->_logininfo['company_code'],
                    'title' => $c['title'],
                    'teacher_id' => $c['teacher_id'],
                    'price' => $c['price'],
                    'external' => $c['external'],
                    'supplier' => $c['supplier'],
                    'info' => $c['info'],
                    'isdel' => 2);
                if (empty($c['teacher_id'])) {
                    $syncourse['teacher_id'] = NULL;
                }
                $apcourse=$this->annualplancourse_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
                if(empty($apcourse['course_id'])){
                    $apcourse['course_id']=$this->course_model->create($syncourse);
                    $this->annualplancourse_model->update($apcourse,array('id'=>$apcourse['id']));
                }elseif(!empty($apcourse['course_id'])){
                    $this->course_model->update($syncourse,$apcourse['course_id']);
                }
            }

            redirect($this->input->post('preurl'));
            return;
        }
        $chosennum=$this->annualanswercourse_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$plan['annual_survey_id'],'annual_course_id'=>$annualcourseid));
        $course=$this->annualplancourse_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
        $teachers = $this->teacher_model->get_all(array('company_code' => $this->_logininfo['company_code'], 'isdel' => 2));
        $library=$this->annualcourselibrary_model->get_row(array('id'=>$annualcourse['annual_course_library_id']));
        $preurl=$_SERVER['HTTP_REFERER'];
        $this->load->view('header');
        $this->load->view('annual_plan/course_open',compact('course','annualcourse','teachers','library','preurl','plan','chosennum'));
        $this->load->view('footer');
    }

    //取消课程
    public function closecourse($planid,$annualcourseid){
        $this->isAllowPlanid($planid);
        if(empty($annualcourseid)||$this->annualcourse_model->get_count(array('id' => $annualcourseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            redirect(site_url('annualplan/index'));
            return false;
        }
        $this->annualplancourse_model->update(array('openstatus'=>2),array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));

        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        if($plan['syn_status']==1){
            //同步课程内容
            $apcourse=$this->annualplancourse_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$annualcourseid));
            if(!empty($apcourse['course_id'])){
                $this->course_model->update(array('isdel'=>1),$apcourse['course_id']);
            }
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    //课程同步到课程管理
    public function syncourse($planid){
        $this->isAllowPlanid($planid,false);
        //同步课程
        $acourses=$this->annualplancourse_model->get_all("company_code = '".$this->_logininfo['company_code']."' and annual_plan_id = ".$planid);
        foreach ($acourses as $ac){
            $c = array('user_id'=>$this->_logininfo['id'],
                'company_code' => $this->_logininfo['company_code'],
                'title' => $ac['title'],
                'teacher_id' => $ac['teacher_id'],
                'price' => $ac['price'],
                'external' => $ac['external'],
                'supplier' => $ac['supplier'],
                'info' => $ac['info']);
            if (empty($c['teacher_id'])) {
                $c['teacher_id'] = NULL;
            }
            //同步审核后的学员名单annual_plan_id,annual_course_id
            $stusql="select student.id,student.name,student.department_parent_id,student.department_id from ".$this->db->dbprefix('annual_plan_course_list')." course_list left join ".
                $this->db->dbprefix('student')." student on course_list.student_id=student.id ".
                "where course_list.company_code='".$this->_logininfo['company_code']."' and course_list.annual_plan_id=$planid and annual_course_id=".$ac['annual_course_id']." and course_list.status=1 ";
            $query = $this->db->query($stusql);
            $list = $query->result_array();
            if (!empty($list)) {
                $c['targetone']=$c['targettwo']=$c['target']=$c['targetstudent']='';
                $studentid = array_column($list, 'id');
                $c['targetstudent'] .= implode(",", $studentid);
                $student = array_column($list, 'name');
                $c['target'] .= implode(",", $student);
                $one = array_column($list, 'department_parent_id');
                $c['targetone'] .= implode(",", $one);
                $two = array_column($list, 'department_id');
                $c['targettwo'] .= implode(",", $two);

            }


            $c['isdel']=($ac['openstatus']==1)?2:1;//取消开课则删除之前的课程
            if(empty($ac['course_id'])&&$ac['openstatus']==1){
                $ac['course_id']=$this->course_model->create($c);
                $this->annualplancourse_model->update($ac,array('id'=>$ac['id']));
            }elseif(!empty($ac['course_id'])){
                $this->course_model->update($c,$ac['course_id']);
            }
        }
        //更新计划同步状态
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $plan['syn_status']=1;
        $this->annualplan_model->update($plan,$planid);
        //同步课程报名名单
        $applylistsql="select apc.course_id,apcl.student_id,apcl.status from ".$this->db->dbprefix('annual_plan_course_list')." apcl left join ".$this->db->dbprefix('annual_plan_course')." apc on apcl.annual_plan_id=apc.annual_plan_id and apcl.annual_course_id=apc.annual_course_id where apcl.company_code='".$this->_logininfo['company_code']."' and apc.course_id is not null and apcl.annual_plan_id=$planid group by apc.course_id,apcl.student_id ";
        $query=$this->db->query($applylistsql);
        $list=$query->result_array();
        if(!empty($list)){
            foreach ($list as $s){
                $data = array('course_id' => $s['course_id'], 'student_id' => $s['student_id']);
                $a = $this->db->get_where('course_apply_list', $data)->row_array();
                $data['note'] = '来自年度需求调研的报名申请';
                $data['status'] = $s['status'];
                if (empty($a)) {
                    $this->db->insert('course_apply_list', $data);
                } else {
                    $this->db->where('id', $a['id']);
                    $this->db->update('course_apply_list', $data);
                }
            }
        }
        //正在同步中的课程数量
        $total_syncoursed_opened_sql="select count(*) as num from ".$this->db->dbprefix('annual_plan_course')." apc left join ".$this->db->dbprefix('course')." c on apc.course_id=c.id where apc.annual_plan_id = ".$planid." and c.isdel = 2 ";
        $query = $this->db->query($total_syncoursed_opened_sql);
        $total_syncoursed_opened=$query->row_array();
        echo $total_syncoursed_opened['num'];

    }

    //暂停课程同步
    public function syncoursepause($planid){
        //更新计划同步状态
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $plan['syn_status']=2;
        $this->annualplan_model->update($plan,$planid);
        //正在同步中的课程数量
        $total_syncoursed_opened_sql="select count(*) as num from ".$this->db->dbprefix('annual_plan_course')." apc left join ".$this->db->dbprefix('course')." c on apc.course_id=c.id where apc.annual_plan_id = ".$planid." and c.isdel = 2 ";
        $query = $this->db->query($total_syncoursed_opened_sql);
        $total_syncoursed_opened=$query->row_array();
        echo $total_syncoursed_opened['num'];
    }

    //取消同步的课程
    public function cancelsyncourse($planid){
        $this->isAllowPlanid($planid,false);
        $acourses=$this->annualplancourse_model->get_all("company_code = '".$this->_logininfo['company_code']."' and annual_plan_id = ".$planid." and (course_id is not null or course_id != '') ");
        foreach ($acourses as $ac){
            $c = array('company_code' => $this->_logininfo['company_code'],
                'title' => $ac['title'],
                'teacher_id' => $ac['teacher_id'],
                'price' => $ac['price'],
                'info' => $ac['info'],
                'isdel'=>1);
            if (empty($c['teacher_id'])) {
                $c['teacher_id'] = NULL;
            }
            $this->course_model->update($c,$ac['course_id']);
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $plan['syn_status']=2;
        $this->annualplan_model->update($plan,$planid);
        echo 1;
    }

    //课程名单审核
    public function courselist($planid,$courseid){
        $this->isAllowPlanid($planid);
        $parm['parent_department']=$this->input->get('parent_department');
        $parm['department']=$this->input->get('department');
        $success=$this->input->get('success');
        $departWhere='';
        if(!empty( $parm['parent_department'] )){
            $departWhere.=" and student.department_parent_id = ".$parm['parent_department'];
        }
        if(!empty( $parm['department'] )){
            $departWhere.=" and student.department_id = ".$parm['department'];
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $course=$this->annualplancourse_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid));
        $cross_num=$this->annualplancourselist_model->get_count(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'status'=>1));
        $aacsql = "select student.name,student.job_code,student.job_name,student.mobile,parentdepart.name as parent_department,depart.name as department,aac.student_id,aac.annual_course_id,apcl.status,aac.created from ".
            $this->db->dbprefix('annual_answer_course') . " aac left join ".
            $this->db->dbprefix('annual_plan_course_list') . " apcl on aac.id = apcl.answer_course_id and apcl.annual_plan_id=$planid left join ".
            $this->db->dbprefix('student')." student on aac.student_id=student.id left join ".
            $this->db->dbprefix('department')." parentdepart on student.department_parent_id = parentdepart.id left join ".
            $this->db->dbprefix('department')." depart on student.department_id = depart.id ".
            "where aac.company_code = '" . $this->_logininfo['company_code'] ."' and aac.annual_course_id = $courseid and aac.annual_survey_id = ".$plan['annual_survey_id'];
        $aacsql .= $departWhere;
        $aacsql .= " order by aac.created ";
        $query = $this->db->query($aacsql);
        $aaclist = $query->result_array();

        $apclsql = "select student.name,student.job_code,student.job_name,student.mobile,parentdepart.name as parent_department,depart.name as department,apcl.student_id,apcl.annual_course_id,apcl.status,apcl.created from ".
            $this->db->dbprefix('annual_plan_course_list')." apcl left join ".
            $this->db->dbprefix('student')." student on apcl.student_id=student.id left join ".
            $this->db->dbprefix('department')." parentdepart on student.department_parent_id = parentdepart.id left join ".
            $this->db->dbprefix('department')." depart on student.department_id = depart.id ".
            "where apcl.company_code = '" . $this->_logininfo['company_code'] ."' and apcl.annual_course_id = $courseid and apcl.annual_plan_id = $planid and apcl.annual_course_id=$courseid and (apcl.answer_course_id is null or apcl.answer_course_id = '') ";
        $apclsql .= $departWhere;
        $apclsql .= " order by apcl.created ";
        $query = $this->db->query($apclsql);
        $apclist = $query->result_array();

        $where = "parent_id is null and company_code = '".$this->_logininfo['company_code']."' ";
        $departments = $this->department_model->get_all($where);
        $sec_departments = array();
        if(!empty($parm['parent_department'])){
            $where = "parent_id = ".$parm['parent_department']." and company_code = '".$this->_logininfo['company_code']."' ";
            $sec_departments = $this->department_model->get_all($where);
        }
        $totals=count($apclist)+count($aaclist);

        //已审核的学员对象
        $stusql="select student.id,student.name,student.department_parent_id,student.department_id from ".$this->db->dbprefix('annual_plan_course_list')." course_list left join ".
            $this->db->dbprefix('student')." student on course_list.student_id=student.id ".
            "where course_list.company_code='".$this->_logininfo['company_code']."' and course_list.annual_plan_id=$planid and annual_course_id=".$courseid." and course_list.status=1 ";
        $query = $this->db->query($stusql);
        $list = $query->result_array();
        if (!empty($list)) {
            $studentid = array_column($list, 'id');
            $plan['targetstudent'] = implode(",", $studentid);
            $one = array_column($list, 'department_parent_id');
            $plan['targetone'] = implode(",", $one);
            $two = array_column($list, 'department_id');
            $plan['targettwo'] = implode(",", $two);
        }
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
        $this->load->view('annual_plan/course_list',compact('plan','course','aaclist','apclist','links','cross_num','totals','departments','sec_departments','parm','deparone', 'departwo', 'students','success'));
        $this->load->view('footer');
    }

    //添加课程审核学员名单
    public function addstudenttocourselist($planid,$courseid){
        if(!$this->isAllowPlanid($planid,false)){
            echo 2;
            return false;
        }
        $targetstudent=$this->input->post('targetstudent');
        $studentsarr = explode(',', $targetstudent);
        foreach ($studentsarr as $s){
            $l=array('company_code'=>$this->_logininfo['company_code'],'student_id'=>$s,'annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'status'=>1);
            if($this->annualplancourselist_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'student_id'=>$s,'annual_plan_id'=>$planid,'annual_course_id'=>$courseid))<=0){
                $this->annualplancourselist_model->create($l);
                //同步名单操作
                $this->syncourselist($planid,$courseid,$s,1);
            }
        }
        echo 1;
    }

    //开始审核
    public function approvedstart($planid){
        $this->isAllowPlanid($planid,false);
        //判断是否有其他正在审核开启的年度计划
        if($this->annualplan_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'approval_status'=>1,'isdel'=>2))>0){
            echo json_encode(array('err'=>'approvaling','msg'=>'有正在审核的年度计划,请先暂停其他计划的审核状态'));
            return false;
        }
        //通知并判断部门经理是否缺失
        $studentsql1="select s.department_id from ".$this->db->dbprefix('student')." s left join ".
            $this->db->dbprefix('annual_answer_course')." aac on aac.student_id = s.id left join ".
            $this->db->dbprefix('annual_plan')." plan ON plan.annual_survey_id = aac.annual_survey_id left join ".
            $this->db->dbprefix('annual_plan_course')." apc ON apc.annual_plan_id = plan.id where plan.id = ".$planid."
AND apc.openstatus =1 ";
        $studentsql2="select s.department_id from ".$this->db->dbprefix('student')." s left join ".
            $this->db->dbprefix('annual_plan_course_list')." aplist ON aplist.student_id = s.id where aplist.annual_plan_id = ".$planid;
        $sql="SELECT fsql.department_id,department.name as department,student.id AS student_id, student.name, student.mobile from ".
            "($studentsql1 UNION  ALL $studentsql2 ) fsql LEFT JOIN ".
            $this->db->dbprefix('department')." department ON fsql.department_id=department.id left join ".
            $this->db->dbprefix('student')." student ON student.department_id = fsql.department_id and student.role = 3 ".
            "group by fsql.department_id";//员工经理
        $query = $this->db->query($sql);
        $firstudent = $query->result_array();
        $errdepartment=array();
        $nobifytarget=array();
        foreach ($firstudent as $s){
            if(empty($s['mobile'])){//没指定部门经理
                $errdepartment[]=array('name'=>$s['department'],'department_id'=>$s['department_id']);
            }else{
                $nobifytarget[]=$s['student_id'];
            }
        }

        if(count($errdepartment)<=0){
            $plan=$this->annualplan_model->get_row(array('id'=>$planid));
            $plan['approval_status']=1;
            $this->annualplan_model->update($plan,$planid);
            //通知部门经理审核
            if(count($nobifytarget)>0){
                $nobifytarget=array_unique($nobifytarget);
                $this->load->library(array('notifyclass'));
                $this->notifyclass->planCourseApproved($planid,$nobifytarget);
            }
        }
        echo json_encode(array('err'=>count($errdepartment),'department'=>$errdepartment));
    }

    //暂停审核
    public function approvedpause($planid){
        if(!$this->isAllowPlanid($planid,false)){
            echo 2;
            return false;
        }
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $plan['approval_status']=2;
        $this->annualplan_model->update($plan,$planid);
        echo 1;
    }

    //通过课程审核
    public function approved($planid,$courseid,$studentid){
        $this->isAllowPlanid($planid);
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $pc=$this->annualplancourselist_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'student_id'=>$studentid));
        if(!empty($pc['id'])){
            $pc['status']=1;
            $this->annualplancourselist_model->update($pc,array('id'=>$pc['id']));
        }else{
            $asc=$this->annualanswercourse_model->get_row(array('annual_survey_id'=>$plan['annual_survey_id'],'annual_course_id'=>$courseid,'student_id'=>$studentid));
            $this->annualplancourselist_model->create(array('answer_course_id'=>$asc['id'],'company_code'=>$this->_logininfo['company_code'],'student_id'=>$studentid,'annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'status'=>1));
        }

        //同步操作
        $this->syncourselist($planid,$courseid,$studentid,1);

        redirect($_SERVER['HTTP_REFERER']);
    }
    //取消审核名单
    public function unapproved($planid,$courseid,$studentid){
        $this->isAllowPlanid($planid);
        $pc=$this->annualplancourselist_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'student_id'=>$studentid));
        if(!empty($pc['id'])){
            $pc['status']=2;
            $this->annualplancourselist_model->update($pc,array('id'=>$pc['id']));
            //同步操作
            $this->syncourselist($planid,$courseid,$studentid,2);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    //年度培训计划
    public function plan($planid){
        $this->isAllowPlanid($planid);
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $typies=$this->annualcoursetype_model->get_all(array('annual_survey_id'=>$plan['annual_survey_id']));
        $res=array();
        foreach ($typies as $k=>$t){
            $where=" where pc.annual_plan_id = $planid ".
                " and pc.company_code = '".$this->_logininfo['company_code']."' ".
                " and pc.openstatus=1 ".
                " and pc.annual_course_type_id = ".$t['id'];
            //课程统计
            $tsql="select count(pc.id) as count_num , sum(people) as people_num,sum(price) as price_num from " . $this->db->dbprefix('annual_plan_course') . " pc ".$where;
            $query = $this->db->query($tsql);
            $t['total'] = $query->row_array();
            //课程详细
            $csql="select pc.*,teacher.name as teacher from " . $this->db->dbprefix('annual_plan_course') . " pc left join " . $this->db->dbprefix('teacher') . " teacher on pc.teacher_id=teacher.id ".$where;
            $query = $this->db->query($csql." order by pc.year,pc.month ");
            $t['courses'] = $query->result_array();
            $res[$k]=$t;
        }
        $teachersql="select teacher.* from ".$this->db->dbprefix('annual_plan_course')." plan_course left join ".$this->db->dbprefix('teacher')." teacher on plan_course.teacher_id=teacher.id where plan_course.annual_plan_id=$planid and plan_course.company_code='".$this->_logininfo['company_code']."' and plan_course.openstatus=1 and plan_course.teacher_id is not null group by teacher.id ";
        $query = $this->db->query($teachersql . " order by plan_course.id asc ");
        $teachers = $query->result_array();

        $this->load->view('header');
        $this->load->view('annual_plan/plan',compact('plan','teachers','res'));
        $this->load->view('footer');
    }

    //年度培训计划删除
    public function del($planid){
        $this->isAllowPlanid($planid);
        $this->annualplan_model->update(array('isdel'=>1),$planid);
        $acourses=$this->annualplancourse_model->get_all("company_code = '".$this->_logininfo['company_code']."' and annual_plan_id = ".$planid." and (course_id is not null or course_id != '') ");
        foreach ($acourses as $ac){
            $c = array('company_code' => $this->_logininfo['company_code'],
                'title' => $ac['title'],
                'teacher_id' => $ac['teacher_id'],
                'price' => $ac['price'],
                'info' => $ac['info'],
                'isdel'=>1);
            if (empty($c['teacher_id'])) {
                $c['teacher_id'] = NULL;
            }
            $this->course_model->update($c,$ac['course_id']);
        }
        redirect($_SERVER['HTTP_REFERER']);
        return ;
    }

    //年度培训计划统计
    public function analysis($planid){
        $this->isAllowPlanid($planid);
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        $coursesql="select count(pc.id) as count_num , sum(people) as people_num,sum(price) as price_num,act.name as type_name from " . $this->db->dbprefix('annual_plan_course') . " pc left join " . $this->db->dbprefix('annual_course_type') . " act on pc.annual_course_type_id=act.id ".
            " where pc.annual_plan_id = $planid ".
            " and pc.company_code = '".$this->_logininfo['company_code']."' ".
            " and pc.openstatus=1 ".
            " group by pc.annual_course_type_id ";
        $query = $this->db->query($coursesql);
        $courses = $query->result_array();
        $trendsql = "select pc.id,concat(pc.year,pc.month) as ym from " . $this->db->dbprefix('annual_plan_course') ." pc ".
            " where pc.annual_plan_id = $planid ".
            " and pc.company_code = '".$this->_logininfo['company_code']."' ".
            " and pc.openstatus=1 ";
        $trendsql = "select count(*) as count_num,s.ym from ($trendsql) s group by s.ym order by s.ym asc ";
        $query = $this->db->query($trendsql);
        $data = $query->result_array();
        $datatrend=$trend=array();
        foreach ($data as $d){
            $trend[$d['ym']]=$d['count_num'];
        }
        if(count($data)>0){
            $first=$data[0];
            $last=end($data);
            $firsty=substr($first['ym'],0,4);
            $firstm=substr($first['ym'],-2);
            $lasty=substr($last['ym'],0,4);
            $lastm=substr($last['ym'],-2);
            for($y=$firsty;$y<=$lasty;$y++){
                $fm=($firsty==$y)?$firstm*1:1;
                $lm=($lasty==$y)?$lastm*1:12;
                for($m=$fm;$m<=$lm;$m++){
                    $ym=$m<10?$y.'0'.$m:$y.$m;
                    $datatrend[$ym]=!empty($trend[$ym])?$trend[$ym]:0;
                }
            }
        }
        $this->load->view('header');
        $this->load->view('annual_plan/analysis',compact('plan','courses','datatrend'));
        $this->load->view('footer');
    }

    //同步课程名单
    private function syncourselist($planid,$courseid,$studentid,$status=1){//1通过2取消
        $plan=$this->annualplan_model->get_row(array('id'=>$planid));
        if($plan['syn_status']==1){
            $apcourse=$this->annualplancourse_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid));
            if(!empty($apcourse['course_id'])){
                //课程名单
                $stusql="select student.id,student.name,student.department_parent_id,student.department_id from ".$this->db->dbprefix('annual_plan_course_list')." course_list left join ".
                    $this->db->dbprefix('student')." student on course_list.student_id=student.id ".
                    "where course_list.company_code='".$this->_logininfo['company_code']."' and course_list.annual_plan_id=$planid and annual_course_id=".$courseid." and course_list.status=1 ";
                $query = $this->db->query($stusql);
                $list = $query->result_array();
                if (!empty($list)) {
                    $c=array();
                    $c['targetone']=$c['targettwo']=$c['target']=$c['targetstudent']='';
                    $targetstudentids = array_column($list, 'id');
                    $c['targetstudent'] .= implode(",", $targetstudentids);
                    $student = array_column($list, 'name');
                    $c['target'] .= implode(",", $student);
                    $one = array_column($list, 'department_parent_id');
                    $c['targetone'] .= implode(",", $one);
                    $two = array_column($list, 'department_id');
                    $c['targettwo'] .= implode(",", $two);
                    $this->course_model->update($c,$apcourse['course_id']);
                }
                //报名名单
                $data = array('course_id' => $apcourse['course_id'], 'student_id' => $studentid);
                $a = $this->db->get_where('course_apply_list', $data)->row_array();
                $data['note'] = '年度需求调研报名';
                $data['status'] = $status;
                if (empty($a)) {
                    $this->db->insert('course_apply_list', $data);
                } else {
                    $this->db->where('id', $a['id']);
                    $this->db->update('course_apply_list', $data);
                }
            }
        }
    }


    //是否是自己公司下的问卷
    private function isAllowAnnualid($surveyid,$redirect=true){
        if(empty($surveyid)||$this->annualsurvey_model->get_count(array('id' => $surveyid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualplan/index'));}
            return false;
        }else{
            return true;
        }
    }

    //是否是自己公司下的计划
    private function isAllowPlanid($planid,$redirect=true){
        if(empty($planid)||$this->annualplan_model->get_count(array('id' => $planid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualplan/index'));}
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

}