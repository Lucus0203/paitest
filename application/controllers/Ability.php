<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ability extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('user_model','useractionlog_model','company_model','abilityjob_model','companyabilityjob_model','ability_model','department_model','student_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $loginInfo = $this->_logininfo;
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }


    public function index() {
        $company=$this->company_model->get_row(array('code'=>$this->_logininfo['company_code']));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select job.id,job.name,cpjob.target,cpjob.target_one,cpjob.target_two,cpjob.target_student,cpjob.status from " . $this->db->dbprefix('ability_job') . " job "
            . "left join " .$this->db->dbprefix('company_ability_job')." cpjob on cpjob.ability_job_id = job.id and cpjob.company_code='".$this->_logininfo['company_code']."' "
            . "left join " .$this->db->dbprefix('company_order')." company_order on company_order.company_code='".$this->_logininfo['company_code']."' and (company_order.features_id = job.id or company_order.features_id=0 ) and company_order.module='ability' "
            . "where job.status = 1 and (job.type = 1 or job.company_code = " . $this->_logininfo['company_code'] . " or (job.industry_parent_id = '{$company['industry_parent_id']}' and job.industry_id = '{$company['industry_id']}' )) and company_order.checked=1 and (company_order.use_num=0 or company_order.use_num_remain > 0) and (company_order.years=0 or (date_add(company_order.start_time, interval company_order.years year) > NOW() and company_order.start_time < NOW() ) ) or (job.id = 5 and job.status = 1) ";
        $sql.=" group by job.id ";

        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('ability/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();
        if($total_rows<=0){
            redirect(site_url('html/ability'));
            return false;
        }

        $query = $this->db->query($sql . " order by job.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $jobs = $query->result_array();

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
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/index',compact('jobs','deparone','departwo','students','total_rows','links'));
        $this->load->view ( 'footer' );
    }

    /**
     * 岗位能力评估发布并通知
     */
    public function publish($jobid){
        if (!empty($jobid)) {
            $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid));
            if(!empty($compjob)){
                $this->companyabilityjob_model->update(array('status'=>1),$compjob['id']);
            }else{
                $this->companyabilityjob_model->create(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid,'status'=>1));
            }
            $this->updateTargetDataAndNotify($jobid);//更新公司学员评估表并通知
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    /**
     * 岗位能力评估取消发布
     */
    public function unpublish($jobid){
        if (!empty($jobid)) {
            $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid));
            if(!empty($compjob)){
                $this->companyabilityjob_model->update(array('status'=>2),$compjob['id']);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 查看可评估的岗位能力详情
     * @param $jobid
     */
    public function show($jobid){
        $company=$this->company_model->get_row(array('code'=>$this->_logininfo['company_code']));
        $sql = "select job.id,job.name,cpjob.standard_type from " . $this->db->dbprefix('ability_job') . " job "
            . "left join " .$this->db->dbprefix('company_ability_job')." cpjob on cpjob.ability_job_id = job.id and cpjob.company_code='".$this->_logininfo['company_code']."' "
            . "left join " .$this->db->dbprefix('company_order')." company_order on company_order.company_code='".$this->_logininfo['company_code']."' and (company_order.features_id = job.id or company_order.features_id=0 ) and company_order.module='ability' "
            . "where (job.status = 1 and (job.type = 1 or job.company_code = " . $this->_logininfo['company_code'] . " or (job.industry_parent_id = '{$company['industry_parent_id']}' and job.industry_id = '{$company['industry_id']}' )) and company_order.checked=1 and (company_order.use_num=0 or company_order.use_num_remain > 0) and (company_order.years=0 or (date_add(company_order.start_time, interval 1 year) > NOW() and company_order.start_time < NOW() ) )  or (job.id = 5 and job.status = 1) )";
        $sql.=" and job.id=$jobid  ";
        $query = $this->db->query($sql);
        $cpjob = $query->row_array();
        if(empty($cpjob)){//不符合条件则跳转
            redirect(site_url('ability/index'));
            return false;
        }
        //能力模型详细
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$jobid));
        $sql = "select ability.*,if(cajm.model_name!='',cajm.model_name,ability.name) as model_name,if(cajm.level_standard!='',cajm.level_standard,job_model.level_standard) as level_standard from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('ability_model') . " ability on ability.id = job_model.model_id "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.model_id = job_model.model_id and cajm.job_id=$jobid and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where job_model.job_id = $jobid ";
        $query = $this->db->query($sql . " order by ability.type asc,job_model.id asc ");
        $res = $query->result_array();
        $abilities=$levelradar=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
            $levelradar[$a['type']]['level_standard']+=$a['level_standard']*1;
            $levelradar[$a['type']]['level_total']+=$a['level']*1;
        }

        //匹配学员
        $sql="select student.name,cajs.ability_job_id,cajs.student_id,cajs.status from " . $this->db->dbprefix('company_ability_job_student')." cajs left join ".$this->db->dbprefix('student')." student on cajs.student_id = student.id 
            where cajs.ability_job_id=$jobid and cajs.company_code='".$this->_logininfo['company_code']."' and cajs.isdel = 2 ";
        $query = $this->db->query($sql . " order by cajs.id ");
        $students = $query->result_array();
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/show',compact('abilities','abilityjob','levelradar','students','cpjob'));
        $this->load->view ( 'footer' );
    }

    /**
     * 保存新标准
     */
    public function saveStandard(){
        $jobid=$this->input->post('jobid');
        $levels=$this->input->post('levels');
        $types=$this->input->post('types');
        $mids=$this->input->post('mids');
        $mnames=$this->input->post('mnames');
        $levels=explode(',',$levels);
        $types=explode(',',$types);
        $mids=explode(',',$mids);
        $mnames=explode(',',$mnames);
        //判断提交能力模型个数是否和基本岗位模型数量相同
        $sql = "select count(*) as num from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "where job_model.job_id = $jobid ";
        $query = $this->db->query($sql);
        $num = $query->row_array();
        $total_rows = $num['num'];
        if(empty($mids)||count($mids)!=$total_rows){
            echo json_encode(array('success'=>'failure','msg'=>'保存失败,数据不正确!'));
            return false;
        }
        //判断学员是否已提交评估
        $sql = "select count(*) as num from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code='".$this->_logininfo['company_code']."' and assess.ability_job_id = $jobid ";
        $query = $this->db->query($sql);
        $num = $query->row_array();
        $total_rows = $num['num'];
        if($total_rows>0){
            echo json_encode(array('success'=>'failure','msg'=>'保存失败,已有学员提交评估!'));
            return false;
        }
        //清除旧数据
        $clearsql="DELETE FROM `pai_company_ability_job_model` WHERE `company_code` = '".$this->_logininfo['company_code']."' and `job_id` = $jobid ";
        $this->db->query($clearsql);
        //插入新数据
        $insertsql="INSERT INTO `".$this->db->dbprefix('company_ability_job_model')."` (`company_code`, `type`, `job_id`, `model_id`, `model_name`, `level_standard`, `created`) VALUES ";
        $datastr='';
        foreach ($mids as $k=>$mid){
            $d="('{$this->_logininfo['company_code']}', '{$types[$k]}', '$jobid', '{$mid}', '{$mnames[$k]}', '{$levels[$k]}', '".date('Y-m-d H:i:s')."')";
            $datastr.=($datastr=='')?$d:','.$d;
        }
        $insertsql.=$datastr.';';
        $this->db->query($insertsql);
        //更新company_ability_job
        $this->db->where ( array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid ) );
        $this->db->update ( 'company_ability_job', array('standard_type'=>2) );
        echo json_encode(array('success'=>'ok'));

    }

    //重置标准
    public function resetStandard($jobid){
        //判断学员是否已提交评估
        $sql = "select count(*) as num from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code='".$this->_logininfo['company_code']."' and assess.ability_job_id = $jobid ";
        $query = $this->db->query($sql);
        $num = $query->row_array();
        $total_rows = $num['num'];
        if($total_rows>0){
            echo json_encode(array('success'=>'failure','msg'=>'已有学员提交评估,重置失败'));
            return false;
        }
        //清除旧数据
        $clearsql="DELETE FROM `pai_company_ability_job_model` WHERE `company_code` = '".$this->_logininfo['company_code']."' and `job_id` = $jobid ";
        $this->db->query($clearsql);
        //更新company_ability_job
        $this->db->where ( array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$jobid ) );
        $this->db->update ( 'company_ability_job', array('standard_type'=>1) );
        echo json_encode(array('success'=>'ok'));
    }

    /**
     * 匹配评估对象
     */
    public function updateTarget(){
        $data['company_code']=$this->_logininfo['company_code'];
        $data['ability_job_id']=$this->input->post('jobid');
        $data['target_one']=$this->input->post('targetone');
        $data['target_two']=$this->input->post('targettwo');
        $data['target_student']=$this->input->post('targetstudent');
        $data['created']=date('Y-m-d H:i:s');
        $target=$targetstudentids='';
//        if(!empty($data['target_one'])){
//            $targetone=$this->department_model->get_all(' id in ('.$data['target_one'].') ');
//            if(!empty($targetone)){
//                $targetone = array_column($targetone, 'name');
//            }
//        }
//        if(!empty($data['target_two'])) {
//            $targettwo = $this->department_model->get_all(' id in (' . $data['target_two'] . ') ');
//            if (!empty($targettwo)) {
//                $targettwo = array_column($targettwo, 'name');
//            }
//        }
        if(!empty($data['target_student'])) {
            $targetstudent = $this->student_model->get_all(' id in (' . $data['target_student'] . ') and isdel = 2 ');
            if (!empty($targetstudent)) {
                $targetnamearr = array_column($targetstudent, 'name');
                $target .= implode(",", $targetnamearr);
                $targetstudentidsarr = array_column($targetstudent, 'id');
                $targetstudentids .= implode(",", $targetstudentidsarr);
            }
        }
        $data['target']=$target;
        $data['target_student']=$targetstudentids;
        $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$data['company_code'],'ability_job_id'=>$data['ability_job_id']));
        if(!empty($compjob)){
            $this->companyabilityjob_model->update($data,$compjob['id']);
        }else{
            $this->companyabilityjob_model->create($data);
        }
        $this->updateTargetDataAndNotify($data['ability_job_id']);//更新公司岗位评估学员表并通知
        $res = mb_strlen($target, 'utf-8') > 20 ? mb_substr( $target,0,40,"utf-8").'...':$target;
        echo $res;
    }

    /**
     * 更新公司岗位评估学员表并通知
     * @param $ability_job_id
     */
    private function updateTargetDataAndNotify($ability_job_id){
        $compjob=$this->companyabilityjob_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$ability_job_id));
        $studentids=$compjob['target_student'];
        if(!empty($studentids)){
            //已经不存在评估的对象改为删除状态
            $sql = "update ".$this->db->dbprefix('company_ability_job_student')." set isdel = 1 where company_code='".$this->_logininfo['company_code']."' and ability_job_id=$ability_job_id and student_id not in ($studentids) ";
            $this->db->query($sql);
            //循环现有的评估对象,如果已有但是删除状态则更改删除状态,如果无则新增并通知
            $studentids=explode(',',$studentids);
            foreach ($studentids as $sid){
                $where="company_code='".$this->_logininfo['company_code']."' and ability_job_id=$ability_job_id and student_id = $sid ";
                $query = $this->db->get_where ( 'company_ability_job_student', $where );
                $saj=$query->row_array();
                if(!empty($saj)){//已有
                    if($saj['isdel']==1){//但是删除状态则更改正常状态
                        $this->db->where ( $where );
                        $this->db->update ( 'company_ability_job_student', array('isdel'=>2) );//恢复可用
                    }
                }else{//无则新增并通知
                    $obj=array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$ability_job_id,'student_id'=>$sid,'created'=>date("Y-m-d H:i:s"));
                    $this->db->insert ( 'company_ability_job_student', $obj );
                    //评估通知
                    if($compjob['status']==1){//发布中则立即更新并通知
                        $this->load->library(array('notifyclass'));
                        $this->notifyclass->abilitypublish($ability_job_id,$sid);
                    }
                }
            }

        }
    }

    /**
     *
     * 查看评估对象
     * @param $jobid
     */
    public function targets($abilityjobid){
        $companyjob=$this->companyabilityjob_model->get_row(array('ability_job_id'=>$abilityjobid,'company_code'=>$this->_logininfo['company_code']));
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjobid));
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $sql = "select parent_depart.name as parent_department_name ,depart.name as department_name,cajs.point,student.* from " . $this->db->dbprefix('company_ability_job_student') . " cajs "
            . "left join " .$this->db->dbprefix('student')." student on cajs.student_id = student.id "
            . "left join " .$this->db->dbprefix('department')." parent_depart on student.department_parent_id = parent_depart.id "
            . "left join " .$this->db->dbprefix('department')." depart on student.department_id = depart.id and depart.id != student.department_parent_id "
            . "where student.isdel = 2 and cajs.isdel=2 and cajs.ability_job_id=$abilityjobid and cajs.company_code = '".$this->_logininfo['company_code']."'";

        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        $config['base_url'] = site_url('ability/index');
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);
        $links=$this->pagination->create_links();

        $query = $this->db->query($sql . " order by student.id desc limit " . ($page - 1) * $page_size . "," . $page_size);
        $students = $query->result_array();
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/targets',compact('students','total_rows','links','companyjob','abilityjob'));
        $this->load->view ( 'footer' );

    }

    /**
     * 查看评估详情
     */
    public function targetdetail($abilityjobid,$studentid){
        if(empty($studentid)){
            redirect(site_url('ability/targets/'.$abilityjobid));
            return false;
        }
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $sql = "select assess.*,if(cajm.level_standard!='',cajm.level_standard,job_model.level_standard) as level_standard from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "left join " . $this->db->dbprefix('ability_job_model') . " job_model on job_model.id = assess.model_id "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.model_id = assess.model_id and cajm.job_id=$abilityjobid and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjobid and student_id=".$studentid;
        $query = $this->db->query($sql);
        $res = $query->result_array();
        if(count($res)<=0){
            redirect(site_url('ability/targets/'.$abilityjobid));
            return false;
        }
        $abilities=array();
        $studentPoint=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
            $studentPoint[$a['type']]['point']+=$a['point'];
            $studentPoint[$a['type']]['level']+=$a['level'];
        }
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjobid));

        //获取岗位评分标准
        $sql = "select ability.type,if(cajm.level_standard!='',cajm.level_standard,job_model.level_standard) as level_standard from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('ability_model') . " ability on ability.id = job_model.model_id "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.model_id = job_model.model_id and cajm.job_id=$abilityjobid and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where job_model.job_id = $abilityjobid ";
        $sql.=" order by ability.type asc,job_model.id asc";
        $query = $this->db->query("select s.type,sum(level_standard) as point_standard from ($sql) s group by s.type ");
        $res = $query->result_array();
        $standard=array();
        foreach ($res as $s){
            $standard[$s['type']]=$s['point_standard'];
        }

        $this->load->view ( 'header' );
        $this->load->view ( 'ability/targetdetail',compact('student','abilities','abilityjob','studentPoint','standard'));
        $this->load->view ( 'footer' );
    }

}
