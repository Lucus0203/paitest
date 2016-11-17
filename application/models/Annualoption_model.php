<?php
/**
 *
 * @author lucus
 * 课前调研问题选项
 *
 */
class Annualoption_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE,$orderby = 'id',$direction='asc'){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->order_by($orderby,$direction)->get_where ( 'annual_option', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'annual_option', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        $this->db->insert ( 'annual_option', $obj );
        return $this->db->insert_id();
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'annual_option', $obj );
    }
    // 删
    public function del($id) {
        $this->db->where ( 'id', $id );
        $this->db->delete ( 'annual_option' );
    }
    //查找数量
    public function get_count($where=FALSE){
        $this->db->where ($where);
        return $this->db->count_all_results('annual_option');
    }

}