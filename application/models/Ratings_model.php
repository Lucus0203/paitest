<?php
/**
 * 
 * @author lucus
 * 课程反馈
 *
 */
class Ratings_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
        //查所有
	public function get_all($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course_ratings', $where );
		return $query->result_array ();
	}
        
	// 查
	public function get_row($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course_ratings', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'course_ratings', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'course_ratings', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'course_ratings' );
	}
	
}