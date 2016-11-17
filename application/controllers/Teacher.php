<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Teacher extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session', 'pagination'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('teacher_model', 'useractionlog_model'));

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

    private function escapeVal($val){
        return !empty($val)?$this->db->escape($val):'';
    }

    public function teacherlist()
    {
        $logininfo = $this->_logininfo;
        $page = $this->input->get('per_page', true);
        $page = $page * 1 < 1 ? 1 : $page;
        $page_size = 10;
        $parm['keyword'] = $this->input->get('keyword');
        $parm['type'] = $this->input->get('type');
        $parm['specialty'] = $this->input->get('specialty');
        $parm['work_type'] = $this->input->get('work_type');
        $pvalue=array_map(array($this,'escapeVal'),$parm);//防sql注入
        $where = " company_code = '" . $logininfo['company_code'] . "' and isdel=2 ";
        if (!empty($parm['keyword'])) {
            $where .= " and (name like '%" . $this->db->escape_like_str($parm['keyword']) . "%' or title like '%" . $this->db->escape_like_str($parm['keyword']) . "%' or info like '%" . $this->db->escape_like_str($parm['keyword']) . "%' or specialty like '%" . $this->db->escape_like_str($parm['keyword']) . "%') ";
        }
        if (!empty($parm['type'])) {
            $where .= " and type = " . $pvalue['type'] ;
        }
        if (!empty($parm['specialty'])) {
            $where .= " and specialty like '%" . $this->db->escape_like_str($parm['specialty'])."%' ";
        }
        if (!empty($parm['work_type'])) {
            $where .= " and work_type = " . $pvalue['work_type'] ;
        }
        $this->load->database();
        $this->db->where($where);
        $this->db->from('teacher');
        $this->db->order_by("id desc");
        $this->db->limit($page_size, ($page - 1) * $page_size);
        $teachers = $this->db->get()->result_array();
        //分页
        $this->db->where($where);
        $this->db->from('teacher');
        $total_rows = $this->db->count_all_results();
        $config['base_url'] = site_url('teacher/teacherlist') . '?keyword=' . $parm['keyword'] . '&type=' . $parm['type'] . '&specialty=' . $parm['specialty'] . '&work_type=' . $parm['work_type'];
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

        $this->load->view('header');
        $this->load->view('teacher/list', array('teachers' => $teachers, 'parm' => $parm, 'links' => $this->pagination->create_links()));
        $this->load->view('footer');

    }

    //创建课程
    public function teachercreate()
    {
        $res = array();
        $act = $this->input->post('act');
        if (!empty($act)) {
            $refere_url=$this->input->post('refere_url');
            $logininfo = $this->_logininfo;
            $t = array('company_code' => $logininfo['company_code'],
                'type' => $this->input->post('type'),
                'name' => $this->input->post('name'),
                'title' => $this->input->post('title'),
                'specialty' => $this->input->post('specialty'),
                'years' => $this->input->post('years'),
                'work_type' => $this->input->post('work_type'),
                'hourly' => $this->input->post('hourly'),
                'info' => $this->input->post('info'));

            $config['max_size'] = 5*1024;
            $config['upload_path'] = './uploads/teacher_img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name'] = $file_name = $logininfo['id'] . date("YmdHis");

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('head_img')) {
                $img = $this->upload->data();
                $t['head_img'] = $file_name . $img['file_ext'];
                //缩略
                $config['image_library'] = 'gd2';
                $config['source_image'] = './uploads/teacher_img/' . $t['head_img'];
                $config['create_thumb'] = FALSE;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 320;
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
            }
            $id = $this->teacher_model->create($t);
            echo '<script>history.go(-2);</script>';
            return;
        }

        $this->load->view('header');
        $this->load->view('teacher/edit', $res);
        $this->load->view('footer');
    }


    //讲师编辑
    public function teacheredit($id)
    {
        $logininfo = $this->_logininfo;
        $act = $this->input->post('act');
        if (!empty($act)) {
            $logininfo = $this->_logininfo;
            $t = array('company_code' => $logininfo['company_code'],
                'type' => $this->input->post('type'),
                'name' => $this->input->post('name'),
                'title' => $this->input->post('title'),
                'specialty' => $this->input->post('specialty'),
                'years' => $this->input->post('years'),
                'work_type' => $this->input->post('work_type'),
                'hourly' => $this->input->post('hourly'),
                'info' => $this->input->post('info'));
            $config['upload_path'] = './uploads/teacher_img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name'] = $file_name = $logininfo['id'] . date("YmdHis");

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('head_img')) {
                $img = $this->upload->data();
                $t['head_img'] = $file_name . $img['file_ext'];
                //缩略
                $config['image_library'] = 'gd2';
                $config['source_image'] = './uploads/teacher_img/' . $t['head_img'];
                $config['create_thumb'] = FALSE;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 320;
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
            }
            $this->teacher_model->update($t, $id);
            $msg = '讲师保存成功';
        }
        $teacher = $this->teacher_model->get_row(array('id' => $id,'company_code'=>$logininfo['company_code']));
        $this->load->view('header');
        $this->load->view('teacher/edit', array('teacher' => $teacher, 'msg' => $msg));
        $this->load->view('footer');
    }

    //讲师信息
    public function teacherinfo($id)
    {
        $teacher = $this->teacher_model->get_row(array('id' => $id,'company_code'=>$this->_logininfo['company_code']));
        $this->load->view('header');
        $this->load->view('teacher/info', array('teacher' => $teacher));
        $this->load->view('footer');
    }

    //讲师编辑
    public function teacherdel($id)
    {
        $logininfo = $this->_logininfo;
        if (!empty($id)) {
            $logininfo = $this->_logininfo;
            $t = $this->teacher_model->get_row(array('id' => $id));
            if ($t['company_code'] == $logininfo['company_code']) {
                $this->teacher_model->update(array('isdel' => 1), $id);
            }
        }
        redirect(site_url('teacher/teacherlist'));
    }

}
