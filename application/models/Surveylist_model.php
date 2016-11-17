<?php
/**
 * 
 * @author lucus
 * 课前调研答题序列
 *
 */
class Surveylist_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
        //查所有
	public function get_all($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course_survey_list', $where );
		return $query->result_array ();
	}
        
	// 查
	public function get_row($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course_survey_list', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'course_survey_list', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'course_survey_list', $obj );
	}
	// 删
	public function del($where) {
		$this->db->where ( $where );
		$this->db->delete ( 'course_survey_list' );
	}
        
        public function count($where){
                $this->db->where ($where);
                $this->db->from('course_survey_list');
                return $this->db->count_all_results();
        }
	
}