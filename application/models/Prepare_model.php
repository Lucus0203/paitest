<?php
/**
 *
 * @author lucus
 * 课前准备
 *
 */
class Prepare_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'course_prepare', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'course_prepare', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        $this->db->insert ( 'course_prepare', $obj );
        return $this->db->insert_id();
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'course_prepare', $obj );
    }
    // 删
    public function del($id) {
        $this->db->where ( 'id', $id );
        $this->db->delete ( 'course_prepare' );
    }

}