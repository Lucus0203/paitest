<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Upload extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','download'));
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

    //上传学员
    public function uploadstudent()
    {
        $this->load->database ();
        $config['max_size'] =80*1024;
        $config['upload_path'] = './uploads/studentdata/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['file_name'] = $file_name = $this->_logininfo['id'] . date("YmdHis");
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('excelFile')) {
            $file = $this->upload->data();
            $excelfile = $file_name . $file['file_ext'];
            $this->load->library('PHPExcel');
            $objPHPExcel = PHPExcel_IOFactory::load($config['upload_path'] . $excelfile);
            $sheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestCol = $sheet->getHighestColumn(); // 取得总列数
            $first_depart_array=$second_depart_array=array();//初始化部门临时数组
            if($highestCol=='I') {
                for ($row = 2; $row <= $highestRow; $row++) {
                    $name = $objPHPExcel->getActiveSheet()->getCell('A' . $row)->getValue();//姓名
                    $mobile = $objPHPExcel->getActiveSheet()->getCell('B' . $row)->getValue();//手机
                    $mobile = substr($mobile, 0,11);
                    $email = $objPHPExcel->getActiveSheet()->getCell('C' . $row)->getValue();//邮箱
                    $pass = $objPHPExcel->getActiveSheet()->getCell('D' . $row)->getValue();//密码
                    $sex = $objPHPExcel->getActiveSheet()->getCell('E' . $row)->getValue();//性别
                    $jobcode = $objPHPExcel->getActiveSheet()->getCell('F' . $row)->getValue();//工号
                    $jobname = $objPHPExcel->getActiveSheet()->getCell('G' . $row)->getValue();//职位
                    $first_department = trim($objPHPExcel->getActiveSheet()->getCell('H' . $row)->getValue());//一级部门
                    $second_department = trim($objPHPExcel->getActiveSheet()->getCell('I' . $row)->getValue());//二级部门


                    $student = array('company_code' => $this->_logininfo['company_code'],
                        'sex' => trim($sex) == '男' ? 1 : 2,
                        'name' => trim($name),
                        'job_code' => trim($jobcode),
                        'job_name' => trim($jobname),
                        'mobile' => trim($mobile),
                        'email' => trim($email),
                        'user_name' => trim($mobile),
                        'user_pass' => !empty($pass) ? md5($pass) : md5(substr(trim($mobile),-6)),
                        'role' => 1,
                        'isdel' => 2);
                    //数据验证
                    $flag = $this->validateStudent($student, $row, $first_department, $second_department);
                    if (!$flag) {//验证不通过则跳出程序
                        return false;
                    }

                    //获取部门id
                    if (!empty($first_department)) {
                        $student['department_parent_id'] = $this->searchDepartId($first_department, $first_depart_array);
                    }
                    if (!empty($second_department)) {
                        $student['department_id'] = $this->searchDepartId($second_department, $second_depart_array, $student['department_parent_id']);
                    } else {
                        $student['department_id'] = $student['department_parent_id'];
                    }

                    //判断学员是否存在
                    $s = $this->student_model->get_row(array('company_code' => $this->_logininfo['company_code'], 'mobile' => $student['mobile'], 'isdel' => 2));
                    if (empty($s['id'])) {
                        $this->student_model->create($student);
                    } else {
                        unset($student['user_pass']);
                        $this->student_model->update($student, $s['id']);
                    }
                }
            }
            unlink($config['upload_path'] . $excelfile);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    //学员数据验证
    private  function validateStudent($data,$row,$first_department,$second_department){
        $flag=true;
        $msg='';
        if(empty($data['mobile'])){
            $msg .= '上传失败!第'.$row.'行的手机号不能是空的<br/>';
            $flag=false;
        }
        if(!preg_match("/^1[0-9]{10}$/",$data['mobile'])){
            $msg .= '上传失败!第'.$row.'行的手机号格式不正确<br/>';
            $flag=false;
        }
        if(empty($first_department)&&!empty($second_department)){
            $msg .= '上传失败!第'.$row.'行的二级部门是['.$second_department.'], 一级部门的内容不能是空的<br/>';
            $flag=false;
        }
        echo $msg;
        return $flag;
    }

    //查找部门id,没有则创建
    private function searchDepartId($name,&$arr,$fdepart_id=null){
        $key=array_search($name,$arr);
        if(empty($key)){
            $depart=$this->department_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'name'=>$name));
            if(empty($depart['id'])){
                $depart=array('company_code'=>$this->_logininfo['company_code'],'name'=>$name);
                if(!empty($fdepart_id)){
                    $p = $this->department_model->get_row(array('id' => $fdepart_id));
                    $depart['parent_id'] = $fdepart_id;
                    $depart['level'] = $p['level'] * 1 + 1;
                }
                $key=$this->department_model->create($depart);
            }else{
                $key=$depart['id'];
            }
            $arr[$key]=$depart['name'];
        }
        return $key;
    }

    //下载学员模板
    public function downloadstudentexample(){
        $data = file_get_contents('./uploads/studentdata/uploadExample.xlsx'); // Read the file's contents
        force_download('学员导入模板.xlsx', $data);
    }

    //上传课程反馈
    public function uploadratings()
    {
        $this->load->database ();
        $config['max_size'] =80*1024;
        $config['upload_path'] = './uploads/ratingsdata/';
        $config['allowed_types'] = '*';
        $config['file_name'] = $file_name = $this->_logininfo['id'] . date("YmdHis");
        $this->load->library('upload', $config);
        $res=array('err_code'=>0);
        if ($this->upload->do_upload('excelFile')) {
            $file = $this->upload->data();
            $excelfile = $file_name . $file['file_ext'];
            $this->load->library('PHPExcel');
            $objPHPExcel = PHPExcel_IOFactory::load($config['upload_path'] . $excelfile);
            $sheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestCol = $sheet->getHighestColumn(); // 取得总列数
            if($highestCol=='B') {
                for ($row = 2; $row <= $highestRow; $row++) {
                    $title = $objPHPExcel->getActiveSheet()->getCell('A' . $row)->getValue();//标题
                    $type = $objPHPExcel->getActiveSheet()->getCell('B' . $row)->getValue();//类型
                    $data=array('title'=>trim($title),'type'=>trim($type));
                    //数据验证
                    if(!empty($title)||!empty($type)){
                        $vres = $this->validateRatings($row, $data);
                        if (!$vres['flag']) {//验证不通过则跳出程序
                            $res=array('err_code'=>3,'msg'=>$vres['msg']);
                            echo json_encode($res);
                            return false;
                        }
                    }
                    $res['data'][]=$data;
                }
                unlink($config['upload_path'] . $excelfile);
                echo json_encode($res);
                return false;
            }else{
                $res=array('err_code'=>2,'msg'=>'文件上传失败,数据内容不正确');
                echo json_encode($res);
                return false;
            }
        }else{
            $res=array('err_code'=>1,'msg'=>'文件上传失败');
            echo json_encode($res);
            return false;
        }
    }

    //课程反馈数据验证
    private  function validateRatings($row, $data){
        $flag=true;
        $msg='';
        if(empty($data['title'])){
            $msg .= '上传失败!第'.$row.'行的标题不能是空的
';
            $flag=false;
        }
        if(empty($data['type'])){
            $msg .= '上传失败!第'.$row.'行的题目类型不能是空的
';
            $flag=false;
        }
        if(strpos($data['type'],'评分')===false&&strpos($data['type'],'开放')===false){
            $msg .= '上传失败!第'.$row.'行的题目类型不正确
';
            $flag=false;
        }
        $res=array('flag'=>$flag,'msg'=>$msg);
        return $res;
    }

    //下载课程反馈模板
    public function downloadratingsexample(){
        $data = file_get_contents('./uploads/ratingsdata/uploadExample.xlsx'); // Read the file's contents
        force_download('课程反馈导入模板.xlsx', $data);
    }


}
