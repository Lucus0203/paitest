<?php
/**
 * 
 * @author lucus
 * 课程
 *
 */
class Course_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
        //查所有
	public function get_all($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course', $where );
		return $query->result_array ();
	}
        
	// 查
	public function get_row($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course', $where );
                $course = $query->row_array ();
                $course['status_str']='';
                $course['status_class']='';
                if($course['ispublic']==1){
                    if($course['isapply_open']==1 && strtotime($course['apply_start'])<time()&&strtotime($course['apply_end'])>time()){
                        $course['status_str']='报名中';
                        $course['status_class']='greenH25';
                    }
                    if(strtotime($course['time_start'])<time()&&strtotime($course['time_end'])>time()){
                        $course['status_str']='进行中';
                        $course['status_class']='orangeH25';
                    }
                    if(strtotime($course['time_end'])<time()){
                        $course['status_str']='已结束';
                        $course['status_class']='grayH25';
                    }
                }else{
                    $course['status_str']='待发布';
                    $course['status_class']='orangeH25';
                }
		return $course;
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'course', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
                unset($obj['status_str']);
                unset($obj['status_class']);
		$this->db->where ( 'id', $id );
		$this->db->update ( 'course', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'course' );
	}

    //查找数量
    public function get_count($where=FALSE){
        $this->db->where ($where);
        return $this->db->count_all_results('course');
    }

    //SQL查询
    public function get_sql($sql){
        $query = $this->db->query($sql);
        return $query->result_array();
    }
	
}