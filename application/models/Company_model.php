<?php
/**
 * 
 * @author lucus
 * 公司信息
 *
 */
class Company_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }
	
        //查所有
	public function get_all($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'company', $where );
		return $query->result_array ();
	}

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'company', $where );
        return $query->row_array ();
    }
	// 增
	public function create($obj) {
		$this->db->insert ( 'company', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'company', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'company' );
	}
        
        public function get_last_company(){
                $this->db->order_by('code','desc');
                $query = $this->db->get_where ( 'company' );
		return $query->row_array ();
        }
	
}