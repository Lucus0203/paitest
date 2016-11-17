<?php
/**
 * 
 * @author lucus
 * 权限设置
 *
 */
class Purview_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
        //查所有
	public function get_all($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'purview', $where );
		return $query->result_array ();
	}
        
	// 查
	public function get_row($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'purview', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'purview', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'purview', $obj );
	}
	// 删
	public function del($where) {
		$this->db->where ( $where );
		$this->db->delete ( 'purview' );
	}
	
}