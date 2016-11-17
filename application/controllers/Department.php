<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Department extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('department_model', 'student_model', 'useractionlog_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $loginInfo = $this->_logininfo;
            $roleInfo = $this->session->userdata('roleInfo');
            if ($loginInfo['role'] != 1) {
                $redirect_flag = true;
                foreach ($roleInfo as $key => $value) {
                    if (strpos(current_url(), $key)) {//包含则不用跳转
                        $redirect_flag = false;
                    }
                }
                if ($redirect_flag) {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }


    public function index($dpid)
    {
        $logininfo = $this->_logininfo;
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $current_department = $this->department_model->get_row(array('id' => $dpid));
        $where = "parent_id is null and company_code = '{$logininfo['company_code']}' ";
        $departments = $this->department_model->get_all($where);
        foreach ($departments as $k => $d) {
            if (($d['id'] == $current_department['parent_id']) || $d['id'] == $dpid) {
                $departments[$k]['departs'] = $this->department_model->get_all(array('parent_id' => $d['id']));
            }
        }
        $this->load->database();
        $sql = "select student.*,department.name as department from " . $this->db->dbprefix('student') . " student left join " . $this->db->dbprefix('department') . " department on student.department_id=department.id where student.user_name <> '' and student.isdel = 2 and student.company_code='{$logininfo['company_code']}' ";
        $sql .= !empty($dpid) ? " and (department_id=".$this->db->escape($dpid)." or department.parent_id = ".$this->db->escape($dpid).") " : '';
        //总人数
        $query = $this->db->query("select count(*) as num from ($sql) s ");
        $num = $query->row_array();
        $total_rows = $num['num'];
        //下级管理员人数
        $query = $this->db->query("select count(*) as num from ($sql and student.role=2 ) s ");
        $num = $query->row_array();
        $admintotal = $num['num'];

        $config['base_url'] = base_url('department/index/' . $dpid);
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $sql .= " order by department_id,student.id desc limit " . ($page - 1) * $page_size . "," . $page_size;
        $students = $this->student_model->get_sql($sql);
        $this->load->view('header');
        $this->load->view('department/edit', array('departments' => $departments, 'current_department' => $current_department, 'students' => $students, 'total' => $total_rows, 'admintotal' => $admintotal, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');

    }


    //部门新增
    public function add()
    {
        $logininfo = $this->_logininfo;
        $parentid = $this->input->post('parentid');
        $departname = $this->input->post('departname');
        if (!empty($departname)) {
            $d = array('company_code' => $logininfo['company_code'], 'name' => $departname);
            if($this->department_model->get_count($d)>0){//已存在
                echo -1;
                return false;
            }
            if (!empty($parentid)) {
                $p = $this->department_model->get_row(array('id' => $parentid));
                $d['parent_id'] = $parentid;
                $d['level'] = $p['level'] * 1 + 1;
            }
            $id = $this->department_model->create($d);
            echo $id;
            return;
        }
        echo 0;

    }

    //部门编辑
    public function save()
    {
        $logininfo = $this->_logininfo;
        $id = $this->input->post('currentid');
        $departname = $this->input->post('currentname');
        if (!empty($departname) && !empty($id)) {
            if($this->department_model->get_count("name = '$departname' and id <> $id ")>0){//已存在
                echo -1;
                return false;
            }
            $d = array('name' => $departname);
            $this->department_model->update($d, $id);
            echo $id;
            return;
        }
        echo 0;
    }

    //部门删除
    public function del()
    {
        $id = $this->input->post('currentid');
        if (empty($id)) {
            echo 1;//无参数
            return;
        } elseif ($this->department_model->get_count(array('parent_id' => $id)) > 0) {
            echo 2;//含有子部门
            return;
        } elseif ($this->student_model->get_count(array('department_id' => $id ,'isdel'=>2)) > 0) {
            echo 3;//部门含有学员
            return;
        }
        $this->department_model->del($id);
        echo 0;

    }

    //Ajax获取二级部门及学员
    public function ajaxDepartmentAndStudent()
    {
        $departmentid = $this->input->post('departmentid');
        $departs = $this->department_model->get_all(array('parent_id' => $departmentid));
        $students = array();
        if (!empty($departs[0])) {//含二级部门
            $students = $this->student_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'department_id' => $departs[0]['id'],'isdel'=>2));
            if($this->student_model->get_count("company_code='".$this->_logininfo['company_code']."' and department_id=$departmentid and department_id=department_parent_id and isdel = 2 ")>0){
                $departs[]=array('id'=>$departmentid,'parent_id'=>$departmentid,'name'=>'未分配','level'=>1);
            }
        } else {
            $students = $this->student_model->get_all(array('department_id' => $departmentid,'isdel'=>2));
        }
        echo json_encode(array('departs' => $departs, 'students' => $students));
    }

    //Ajax获取部门里的学员
    public function ajaxStudent()
    {
        $departmentid = $this->input->post('departmentid');
        $students = $this->student_model->get_all(array('department_id' => $departmentid,'isdel'=>2));
        echo json_encode(array('students' => $students));
    }

}
