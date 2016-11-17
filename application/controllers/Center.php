<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Center extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model','useractionlog_model', 'company_model', 'purview_model', 'industries_model','student_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id'=>$this->_logininfo['id'],'url'=>uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }


    public function index($tab)
    {
        $tab = empty($tab) ? 1 : $tab;
        $logininfo = $this->_logininfo;
        $act = $this->input->post('act');
        $success = $msg='';
        if (!empty($act)) {
            if ($act == 'info') {//公司信息
                $res=$this->updateInfo();
                $success=$res['success'];
                $msg=$res['msg'];
            } elseif ($act == 'pass') {//修改密码
                $res=$this->updatePass();
                $tab = 2;
                $success=$res['success'];
                $msg=$res['msg'];
            } elseif ($act == 'purview') {//权限设置
                $res=$this->updatePurview();
                $tab = 3;
                $success=$res['success'];
                $msg=$res['msg'];
            }
        }
        $user = $this->user_model->get_row(array('id' => $logininfo['id']));
        $company = $this->company_model->get_row(array('code' => $logininfo['company_code']));
        $purviews = $this->purview_model->get_all(array('company_code' => $logininfo['company_code']));
        $industry_parent=$this->industries_model->get_all("parent_id = '' or parent_id is null ");
        $industry=array();
        if(!empty($company['industry_parent_id'])){
            $industry=$this->industries_model->get_all(array('parent_id'=>$company['industry_parent_id']));
        }

        $roledata = array();
        foreach ($purviews as $p) {
            $roledata['role' . $p['role']][$p['key']] = $p['value'];
        }
        $this->load->view('header');
        $this->load->view('center/edit', array('user' => $user, 'tab' => $tab, 'company' => $company, 'role' => $roledata,'industry_parent'=>$industry_parent,'industry'=>$industry, 'msg' => $msg, 'success' => $success));
        $this->load->view('footer');

    }

    private function updateInfo(){
        $logininfo = $this->_logininfo;
        $success='ok';
        $msg='';
        $c = array('name' => $this->input->post('name'),
            'industry_parent_id' => $this->input->post('industry_parent_id'),
            'industry_id' => $this->input->post('industry_id'),
            'contact' => $this->input->post('contact'),
            'mobile' => $this->input->post('mobile'),
            'tel' => $this->input->post('tel'),
            'email' => $this->input->post('email'));
        $repeatmobile = $this->user_model->get_row("role=1 and mobile = ".$this->db->escape($c['mobile'])." and id != ".$logininfo['id']);
        $repeatemail = $this->user_model->get_row("role=1 and email = ".$this->db->escape($c['email'])." and id != ".$logininfo['id']);
        if(!empty($repeatmobile)){
            $success='false';
            $msg='手机号已被其他用户使用,请确认是否输入正确';
        }elseif (!empty($repeatemail)){
            $success='false';
            $msg='邮箱地址已被其他用户使用,请确认是否输入正确';
        }else{
            $config['max_size'] = 5*1024;
            $config['upload_path'] = './uploads/company_logo';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name'] = $file_name = $logininfo['id'] . date("YmdHis");
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $img = $this->upload->data();
                $c['logo'] = $file_name . $img['file_ext'];
                //缩略
                $config['image_library'] = 'gd2';
                $config['source_image'] = './uploads/company_logo/' . $c['logo'];
                $config['create_thumb'] = FALSE;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 320;
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
            }
            $lastc = $this->company_model->get_row(array('code' => $logininfo['company_code']));
            if ($logininfo['role'] == 1) {
                $this->company_model->update($c, $lastc['id']);
                //更新公司手机信息则更新学员管理员
                $sacount=$this->student_model->get_row(array('company_code'=>$logininfo['company_code'],'role'=>9));
                if(!empty($sacount)){
                    $this->student_model->update(array('mobile'=>$c['mobile'],'user_name'=>$c['mobile']),$sacount['id']);
                }
                $logininfo['company_name'] = $this->input->post('name');
                $logininfo['logo'] = !empty($c['logo']) ? $c['logo'] : $logininfo['logo'];
            }
            $user = array('real_name' => $c['contact'], 'mobile' => $c['mobile'], 'tel' => $c['tel'], 'email' => $c['email']);
            $this->user_model->update($user, $logininfo['id']);
            $logininfo = $this->user_model->get_row(array('id' => $logininfo['id']));
            $this->session->set_userdata('loginInfo', $logininfo);
            $this->load->vars(array('loginInfo' => $logininfo));
            $msg='信息保存成功';
        }
        return array('success'=>$success,'msg'=>$msg);
    }

    private function updatePass(){
        $cur_pass = $this->input->post('cur_pass');
        $new_pass = $this->input->post('new_pass');
        $success='ok';
        $logininfo = $this->user_model->get_row(array('id' => $this->_logininfo['id']));
        if (md5($cur_pass) != $logininfo['user_pass']) {
            $msg = '当前密码错误';
            $success='false';
        } else {
            $this->user_model->update(array('user_pass' => md5($new_pass)), $this->_logininfo['id']);
            $msg = '密码更新成功';
        }
        return array('success'=>$success,'msg'=>$msg);
    }

    private function updatePurview(){
        $rolemax = 3;
        $this->purview_model->del(array('company_code' => $this->_logininfo['company_code']));
        for ($roleindex = 2; $roleindex <= $rolemax; $roleindex++) {
            $role = $this->input->post('role' . $roleindex);
            foreach ($role as $key => $v) {
                $r = array('company_code' => $this->_logininfo['company_code'],
                    'key' => $key,
                    'value' => $v,
                    'role' => $roleindex);
                $this->purview_model->create($r);
            }
        }
        return array('success'=>'ok','msg'=>'权限设置保存成功');
    }

}
