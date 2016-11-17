<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Student extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model', 'useractionlog_model', 'department_model', 'student_model'));

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
        $res = array();
        $act = $this->input->post('act');
        $current_department = $this->department_model->get_row(array('id' => $dpid));
        if (!empty($act)) {
            $s = array('company_code' => $logininfo['company_code'],
                'sex' => $this->input->post('sex'),
                'name' => $this->input->post('name'),
                'job_code' => $this->input->post('job_code'),
                'job_name' => $this->input->post('job_name'),
                'department_parent_id' => $this->input->post('department_parent_id'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'user_name' => $this->input->post('mobile'),
                'user_pass' => $this->input->post('student_pass'),
                'role' => $this->input->post('role'));
            $s['department_id']=$this->input->post('department_id')??$this->input->post('department_parent_id');

            //验证账号
            if ($this->student_model->get_count(array('company_code' => $s['company_code'],'mobile' => $s['mobile'],'isdel'=>2)) > 0) {
                $res['msg'] = '手机号已被使用';
                $res['student'] = $s;
            } else {
                $s['user_pass'] = md5($s['user_pass']);
                $id = $this->student_model->create($s);
                //创建下级管理员账号
                if ($s['role'] != 1) {
                    $useracount = array('student_id' => $id,
                        'user_name' => $s['mobile'],
                        'user_pass' => $s['user_pass'],
                        'company_code' => $logininfo['company_code'],
                        'real_name' => $s['name'],
                        'email' => $s['email'],
                        'mobile' => $s['mobile'],
                        'role' => $s['role'],
                        'register_flag' => 2,
                        'status' => 2);
                    $uid = $this->user_model->create($useracount);
                }
                redirect(site_url("department/index/" . $s['department_id']));
                return;
            }
        }

        $where = "parent_id is null and company_code = '{$logininfo['company_code']}' ";
        $departments = $this->department_model->get_all($where);
        $second_departments = empty($current_department['parent_id'])?$this->department_model->get_all(array('parent_id' => $current_department['id'])):$this->department_model->get_all(array('parent_id' => $current_department['parent_id']));
        foreach ($departments as $key => $d) {
            if (($d['id'] == $current_department['parent_id']) || $d['id'] == $dpid) {
                $departments[$key]['departs'] = $this->department_model->get_all(array('parent_id' => $d['id']));
            }
        }
        $res['departments'] = $departments;
        $res['second_departments'] = $second_departments;
        $res['current_department'] = $current_department;
        $res['current_parent_department']=!empty($current_department['parent_id'])?$this->department_model->get_row(array('id'=>$current_department['parent_id'])):array();
        $this->load->view('header');
        $this->load->view('student/edit', $res);
        $this->load->view('footer');

    }

    //学员编辑
    public function edit($id)
    {
        $logininfo = $this->_logininfo;
        $res = array();
        $success = $this->input->get('success');
        if (!empty($success)) {
            $res['msg'] = '保存成功';
        }
        $act = $this->input->post('act');
        $res['student'] = $this->student_model->get_row(array('id' => $id,'company_code' => $logininfo['company_code']));
        $current_department = $this->department_model->get_row(array('id' => $res['student']['department_id']));
        if (!empty($act)) {
            $s = array('company_code' => $logininfo['company_code'],
                'sex' => $this->input->post('sex'),
                'name' => $this->input->post('name'),
                'job_code' => $this->input->post('job_code'),
                'job_name' => $this->input->post('job_name'),
                'department_parent_id' => $this->input->post('department_parent_id'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'user_name' => $this->input->post('user_name'),
                'user_pass' => $this->input->post('student_pass'),
                'role' => $this->input->post('role'));
            $s['department_id']=$this->input->post('department_id')??$this->input->post('department_parent_id');
            if ($res['student']['user_pass'] != $s['user_pass']) {
                $s['user_pass'] = md5($s['user_pass']);
            }
            //验证账号
            if ($this->student_model->get_count(" company_code = '{$s['company_code']}' and mobile='".$s['mobile']."' and id <> {$id} and isdel = 2 ") > 0) {
                $res['msg'] = '手机号已被使用';
                $res['student'] = $s;
            } else {
                $this->student_model->update($s, $id);
                //创建/更新下级管理员账号
                if ($s['role'] != 1 && $s['role'] != 9) {
                    $u = $this->user_model->get_row(" student_id = $id ");
                    $useracount = array(
                        'user_name' => $s['mobile'],
                        'user_pass' => $s['user_pass'],
                        'real_name' => $s['name'],
                        'email' => $s['email'],
                        'mobile' => $s['mobile'],
                        'role' => $s['role'],
                        'register_flag' => 2,
                        'status' => 2);
                    if (!empty($u['id'])) {//已存在则更新
                        $this->user_model->update($useracount, $u['id']);
                    } else {//不存在则新增
                        $useracount['student_id'] = $id;
                        $useracount['company_code'] = $logininfo['company_code'];
                        $uid = $this->user_model->create($useracount);
                    }
                } else {//清除角色为1的管理员账号
                    $this->user_model->del(array('student_id' => $id));
                }
                redirect(site_url("student/edit/" . $id) . '?success=success');
                return;
            }
        }

        $where = "parent_id is null and company_code = '{$logininfo['company_code']}' ";
        $departments = $this->department_model->get_all($where);
        $second_departments = !empty($res['student']['department_parent_id'])?$this->department_model->get_all(array('parent_id' => $res['student']['department_parent_id'],'company_code'=>$logininfo['company_code'])):array();
        foreach ($departments as $key => $d) {
            if (($d['id'] == $current_department['parent_id']) || $d['id'] == $current_department['id']) {
                $departments[$key]['departs'] = $this->department_model->get_all(array('parent_id' => $d['id']));
            }
        }
        $res['departments'] = $departments;
        $res['second_departments'] = $second_departments;
        $res['current_department'] = $current_department;
        $this->load->view('header');
        $this->load->view('student/edit', $res);
        $this->load->view('footer');
    }

    //删除学员
    public function del($id)
    {
        $s = $this->student_model->get_row(array('id' => $id));
        if ($s['company_code'] == $this->_logininfo['company_code']) {
            $this->student_model->update(array('isdel' => 1), $id);
            $this->user_model->del(array('student_id' => $id));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }


}
