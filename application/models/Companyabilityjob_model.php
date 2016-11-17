<?php
/**
 *
 * @author lucus
 * 公司职位能力
 *
 */
class Companyabilityjob_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'company_ability_job', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'company_ability_job', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        $this->db->insert ( 'company_ability_job', $obj );
        return $this->db->insert_id();
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'company_ability_job', $obj );
    }
    // 删
    public function del($id) {
        $this->db->where ( 'id', $id );
        $this->db->delete ( 'company_ability_job' );
    }

    //查找数量
    public function get_count($where=FALSE){
        $this->db->where ($where);
        return $this->db->count_all_results('company_ability_job');
    }

}