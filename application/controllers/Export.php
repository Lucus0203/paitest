<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Export extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model', 'useractionlog_model', 'department_model','student_model','course_model','annualplan_model','annualcoursetype_model'));

        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            redirect('login', 'index');
        } else {
            $roleInfo = $this->session->userdata('roleInfo');
            $this->useractionlog_model->create(array('user_id' => $this->_logininfo['id'], 'url' => uri_string()));
            $this->load->vars(array('loginInfo' => $this->_logininfo, 'roleInfo' => $roleInfo));
        }

    }

    //导出全部学员
    public function studentdata(){
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '姓名')
            ->setCellValue('B1', '手机号')
            ->setCellValue('C1', '邮箱')
            ->setCellValue('D1', '密码(保密安全需要,不被导出)')
            ->setCellValue('E1', '性别')
            ->setCellValue('F1', '工号')
            ->setCellValue('G1', '职位')
            ->setCellValue('H1', '一级部门')
            ->setCellValue('I1', '二级部门');
        $sql = "select student.*,department_parent.name as department_parent,department.name as department from " . $this->db->dbprefix('student') . " student left join " . $this->db->dbprefix('department') . " department_parent on student.department_parent_id=department_parent.id left join " . $this->db->dbprefix('department') . " department on student.department_id=department.id where student.user_name <> '' and student.isdel = 2 and student.company_code='{$this->_logininfo['company_code']}' ";
        $query = $this->db->query($sql . " order by student.department_parent_id,student.department_id,student.id ");
        $students = $query->result_array();
        foreach($students as $k => $s){
            $num=$k+2;
            $sex=!empty($s['sex'])?$s['sex']==1?'男':'女':'';
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$num, $s['name'])
                ->setCellValue('B'.$num, $s['mobile'])
                ->setCellValue('C'.$num, $s['email'])
                ->setCellValue('D'.$num, '')
                ->setCellValue('E'.$num, $sex)
                ->setCellValue('F'.$num, $s['job_code'])
                ->setCellValue('G'.$num, $s['job_name'])
                ->setCellValue('H'.$num, $s['department'])
                ->setCellValue('I'.$num, $s['department']);
        }

        $objPHPExcel->getActiveSheet()->setTitle('学员名单');
        $objPHPExcel->setActiveSheetIndex(0);
        $name='所有学员名单';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //报名名单
    public function applylist($courseid)
    {
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '申请原因')
                ->setCellValue('G1', '报名时间')
                ->setCellValue('H1', '状态');
            $sql = "select s.name,s.job_code,s.job_name,s.mobile,d.name as department,a.id as apply_id,a.status as apply_status,a.note,a.created "
                . "from " . $this->db->dbprefix('course_apply_list') . " a left join " . $this->db->dbprefix('student') . " s on a.student_id=s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id "
                . "where a.course_id=$courseid ";
            $query = $this->db->query($sql . " order by a.id desc ");
            $applys = $query->result_array();
            foreach($applys as $k => $a){
                $num=$k+2;
                if($a['apply_status']==1){
                    $status='审核通过';
                }elseif($a['apply_status']==2){
                    $status='审核不通过';
                }else{
                    $status='待审核';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $a['name'])
                    ->setCellValue('B'.$num, $a['job_code'])
                    ->setCellValue('C'.$num, $a['job_name'])
                    ->setCellValue('D'.$num, $a['department'])
                    ->setCellValue('E'.$num, $a['mobile'])
                    ->setCellValue('F'.$num, $a['note'])
                    ->setCellValue('G'.$num, date("m-d H:i",strtotime($a['created'])))
                    ->setCellValue('H'.$num, $status);
            }

            $objPHPExcel->getActiveSheet()->setTitle('报名名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》报名名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }

    }

    //签到名单
    public function signinlist($courseid){
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '签到时间')
                ->setCellValue('G1', '签退时间');
            $sql = "select s.*,d.name as department,siginlist.id as siginlist_id,siginlist.signin_time,siginlist.signout_time "
                . "from " . $this->db->dbprefix('course_signin_list') . " siginlist left join " . $this->db->dbprefix('student') . " s on siginlist.student_id=s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id "
                . "where siginlist.course_id=$courseid ";
            $query = $this->db->query($sql . " order by siginlist.id desc ");
            $siginlist = $query->result_array();
            foreach($siginlist as $k => $s){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $s['name'])
                    ->setCellValue('B'.$num, $s['job_code'])
                    ->setCellValue('C'.$num, $s['job_name'])
                    ->setCellValue('D'.$num, $s['department'])
                    ->setCellValue('E'.$num, $s['mobile'])
                    ->setCellValue('F'.$num, date("m-d H:i",strtotime($s['signin_time'])))
                    ->setCellValue('G'.$num, !empty($h['signout_time'])?date("m-d H:i",strtotime($s['signout_time'])):'');
            }

            $objPHPExcel->getActiveSheet()->setTitle('签到名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》签到名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //课前调研名单
    public function surveylist($courseid){
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '提交时间');
            $listsql = "select * from " . $this->db->dbprefix('course_survey_list') . " h where course_id=$courseid group by student_id order by created desc ";
            $sql = "select h.*,s.name,s.job_code,s.job_name,d.name as department,s.mobile from ($listsql) h left join " . $this->db->dbprefix('student') . " s on h.student_id = s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id ";
            $query = $this->db->query($sql . " order by h.created desc ");
            $surveylist = $query->result_array();
            foreach($surveylist as $k => $s){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $s['name'])
                    ->setCellValue('B'.$num, $s['job_code'])
                    ->setCellValue('C'.$num, $s['job_name'])
                    ->setCellValue('D'.$num, $s['department'])
                    ->setCellValue('E'.$num, $s['mobile'])
                    ->setCellValue('F'.$num, date("m-d H:i",strtotime($s['created'])));
            }

            $objPHPExcel->getActiveSheet()->setTitle('调研名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》调研名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //课程反馈名单
    public function ratingslist($courseid){
        if($this->isAllowCourseid($courseid)){
            $course=$this->course_model->get_row(array('id'=>$courseid));
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, 'Some value');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '工号')
                ->setCellValue('C1', '职务')
                ->setCellValue('D1', '部门')
                ->setCellValue('E1', '手机')
                ->setCellValue('F1', '提交时间');
            $listsql = "select h.* from " . $this->db->dbprefix('course_ratings_list') . " h "
                . "left join " . $this->db->dbprefix('course_ratings') . " rats on h.ratings_id=rats.id where h.course_id=$courseid and rats.num=1 ";
            $sql = "select h.*,s.name,s.job_code,s.job_name,d.name as department,s.mobile from ($listsql) h left join " . $this->db->dbprefix('student') . " s on h.student_id = s.id "
                . "left join " . $this->db->dbprefix('department') . " d on s.department_id = d.id ";
            $query = $this->db->query($sql . " order by h.created desc ");
            $ratingslist = $query->result_array();
            //反馈问题
            $ratsql="select * from ". $this->db->dbprefix('course_ratings')." rat where course_id=$courseid order by num asc ";
            $query = $this->db->query($ratsql);
            $ratques = $query->result_array();
            foreach ($ratques as $k=>$q){
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(($k+6), 1, $q['title']);
            }
            foreach($ratingslist as $k => $r){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $r['name'])
                    ->setCellValue('B'.$num, $r['job_code'])
                    ->setCellValue('C'.$num, $r['job_name'])
                    ->setCellValue('D'.$num, $r['department'])
                    ->setCellValue('E'.$num, $r['mobile'])
                    ->setCellValue('F'.$num, date("m-d H:i",strtotime($r['created'])));

                    $ratanswersql="select ratans.*,ratque.type from ". $this->db->dbprefix('course_ratings_list')." ratans left join ". $this->db->dbprefix('course_ratings') ." ratque on ratans.ratings_id=ratque.id where ratans.course_id=$courseid and ratans.student_id='".$r['student_id']."' order by num asc ";
                    $query = $this->db->query($ratanswersql);
                    $ratanswer = $query->result_array();
                    foreach ($ratanswer as $k=>$ans){
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow(($k+6), $num, ($ans['type']==2)?$ans['content']:$ans['star']);
                    }
            }

            $objPHPExcel->getActiveSheet()->setTitle('课程反馈名单');
            $objPHPExcel->setActiveSheetIndex(0);
            $name='《'.$course['title'].'》课程反馈名单';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //导出年度计划excel
    public function exportplan($planid){
        if($this->isAllowPlanid($planid)){
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
            //讲师

            $teachersql="select teacher.* from ".$this->db->dbprefix('annual_plan_course')." plan_course left join ".$this->db->dbprefix('teacher')." teacher on plan_course.teacher_id=teacher.id where plan_course.annual_plan_id=$planid and plan_course.company_code='".$this->_logininfo['company_code']."' and plan_course.openstatus=1 and plan_course.teacher_id is not null group by teacher.id ";
            $query = $this->db->query($teachersql . " order by plan_course.id asc ");
            $teachers = $query->result_array();

            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objActSheet = $objPHPExcel->getActiveSheet()->setTitle('年度计划课程');
            //excel style
            $styleTitle = array(
                'font' => array('color' => array('argb' => 'FFffffff')),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FF00bbd3')
                ),
            );
            $styleTh = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FFf5f5f5')
                ),
            );
            $styleTd = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
            );
            $styleTdLeft = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER),
            );

            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, 'Some value');
            $objActSheet->mergeCells('A1:I1')->setCellValue('A1', '总览')
                ->mergeCells('A2:C2')->setCellValue('A2', '课程类型')
                ->mergeCells('D2:E2')->setCellValue('D2', '开课数量')
                ->mergeCells('F2:G2')->setCellValue('F2', '培训人次')
                ->mergeCells('H2:I2')->setCellValue('H2', '培训预算');
            $objActSheet->getStyle('A1')->applyFromArray($styleTitle);
            $objActSheet->getStyle('A2')->applyFromArray($styleTh);
            $objActSheet->getStyle('D2')->applyFromArray($styleTh);
            $objActSheet->getStyle('F2')->applyFromArray($styleTh);
            $objActSheet->getStyle('H2')->applyFromArray($styleTh);
            $count_total=0;$people_total=0;$price_total=0;
            $i=3;
            foreach ($res as $r){
                $count_total+=$r['total']['count_num'];
                $people_total+=$r['total']['people_num'];
                $price_total+=$r['total']['price_num'];
                $objActSheet->mergeCells('A'.$i.':C'.$i)->setCellValue('A'.$i,$r['name']);
                $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,round($r['total']['count_num']));
                $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,round($r['total']['people_num']));
                $objActSheet->mergeCells('H'.$i.':I'.$i)->setCellValue('H'.$i,round($r['total']['price_num']));
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);
                ++$i;
            }
            $objActSheet->mergeCells('A'.$i.':C'.$i)->setCellValue('A'.$i,'全部');
            $objActSheet->mergeCells('D'.$i.':E'.$i)->setCellValue('D'.$i,"=SUM(D3:D".($i-1).")");
            $objActSheet->mergeCells('F'.$i.':G'.$i)->setCellValue('F'.$i,"=SUM(F3:F".($i-1).")");
            $objActSheet->mergeCells('H'.$i.':I'.$i)->setCellValue('H'.$i,"=SUM(H3:H".($i-1).")");
            $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
            $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
            $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
            $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);

            ++$i;
            foreach ($res as $r){
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
                if($r['total']['count_num']>0){
                    ++$i;
                    $objActSheet->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, $r['name']);
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTitle);
                    ++$i;
                    $objActSheet->setCellValue('A'.$i,'课程名称');
                    $objActSheet->setCellValue('B'.$i,'课程介绍');
                    $objActSheet->setCellValue('C'.$i,'内训/外训');
                    $objActSheet->setCellValue('D'.$i,'供应商');
                    $objActSheet->setCellValue('E'.$i,'讲师');
                    $objActSheet->setCellValue('F'.$i,'天数');
                    $objActSheet->setCellValue('G'.$i,'人次');
                    $objActSheet->setCellValue('H'.$i,'预算');
                    $objActSheet->setCellValue('I'.$i,'时间');
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('B'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('C'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('D'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('E'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('F'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('G'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('H'.$i)->applyFromArray($styleTh);
                    $objActSheet->getStyle('I'.$i)->applyFromArray($styleTh);
                    foreach ($r['courses'] as $c){
                        ++$i;
                        $objActSheet->setCellValue('A'.$i,$c['title']);
                        $objActSheet->setCellValue('B'.$i,$c['info']);
                        $objActSheet->setCellValue('C'.$i,$c['external']==1?'外训':'内训');
                        $objActSheet->setCellValue('D'.$i,$c['supplier']);
                        $objActSheet->setCellValue('E'.$i,$c['teacher']);
                        $objActSheet->setCellValue('F'.$i,$c['day']);
                        $objActSheet->setCellValue('G'.$i,$c['people']);
                        $objActSheet->setCellValue('H'.$i,$c['price']);
                        $objActSheet->setCellValue('I'.$i,$c['year'].'.'.$c['month']);
                        $objActSheet->getStyle('A'.$i)->applyFromArray($styleTdLeft);
                        $objActSheet->getStyle('B'.$i)->applyFromArray($styleTdLeft);
                        $objActSheet->getStyle('C'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('D'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('E'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('F'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('G'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('H'.$i)->applyFromArray($styleTd);
                        $objActSheet->getStyle('I'.$i)->applyFromArray($styleTd);
                    }
                    ++$i;
                }
            }
            if(count($teachers)>0) {
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
                ++$i;
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '讲师介绍');
                $objActSheet->getStyle('A' . $i)->applyFromArray($styleTitle);
                ++$i;
                $objActSheet->setCellValue('A'.$i,'讲师');
                $objActSheet->setCellValue('B'.$i,'工作形式');
                $objActSheet->setCellValue('C'.$i,'工作年限');
                $objActSheet->mergeCells('D'.$i.':I'.$i)->setCellValue('D'.$i,'简介');
                $objActSheet->getStyle('A'.$i)->applyFromArray($styleTh);
                $objActSheet->getStyle('B'.$i)->applyFromArray($styleTh);
                $objActSheet->getStyle('C'.$i)->applyFromArray($styleTh);
                $objActSheet->getStyle('D'.$i)->applyFromArray($styleTh);
                foreach ($teachers as $t){
                    ++$i;
                    $objActSheet->setCellValue('A'.$i,$t['name']);
                    $objActSheet->setCellValue('B'.$i,$t['work_type']==1?'专职':'兼职');
                    $objActSheet->setCellValue('C'.$i,!empty($t['years'])?$t['years'].'年':'');
                    $objActSheet->mergeCells('D'.$i.':I'.$i)->setCellValue('D'.$i,$t['info']);
                    $objActSheet->getStyle('A'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('B'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('C'.$i)->applyFromArray($styleTd);
                    $objActSheet->getStyle('D'.$i)->applyFromArray($styleTdLeft);
                }
                ++$i;
            }
            if(!empty($plan['note'])){
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '');
                ++$i;
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, '备注');
                $objActSheet->getStyle('A' . $i)->applyFromArray($styleTitle);
                ++$i;
                $objActSheet->mergeCells('A' . $i . ':I' . $i)->setCellValue('A' . $i, $plan['note']);
                $objActSheet->getStyle('A' . $i)->applyFromArray($styleTdLeft);
            }


            $name=$plan['title'];
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }else{
            echo '数据错误,导出失败';
        }
    }

    //是否是自己公司下的课程
    private function isAllowCourseid($courseid){
        if(empty($courseid)||$this->course_model->get_count(array('id' => $courseid,'company_code'=>$this->_logininfo['company_code']))<=0){
            return false;
        }else{
            return true;
        }
    }

    //是否是自己公司下的计划
    private function isAllowPlanid($planid){
        if(empty($planid)||$this->annualplan_model->get_count(array('id' => $planid,'company_code'=>$this->_logininfo['company_code']))<=0){
            return false;
        }else{
            return true;
        }
    }


}
