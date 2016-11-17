<?php
/**
 *
 * @author lucus
 * 行业
 *
 */
class Industries_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'industries', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'industries', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        $this->db->insert ( 'industries', $obj );
        return $this->db->insert_id();
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'industries', $obj );
    }
    // 删
    public function del($where) {
        $this->db->where ( $where );
        $this->db->delete ( 'industries' );
    }

}