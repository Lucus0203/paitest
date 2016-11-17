<?php
/**
 *
 * @author lucus
 * 课前调研
 *
 */
class Annualsurvey_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'annual_survey', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'annual_survey', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        $this->db->insert ( 'annual_survey', $obj );
        return $this->db->insert_id();
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'annual_survey', $obj );
    }
    // 删
    public function del($id) {
        $this->db->where ( 'id', $id );
        $this->db->delete ( 'annual_survey' );
    }
    //查找数量
    public function get_count($where=FALSE){
        $this->db->where ($where);
        return $this->db->count_all_results('annual_survey');
    }

}