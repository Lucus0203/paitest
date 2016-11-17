<?php
/**
 *
 * @author lucus
 * 用户
 *
 */
class Useractionlog_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'user_action_log', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'user_action_log', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        if(!strpos($obj['url'],'favicon')){
            $this->db->insert ( 'user_action_log', $obj );
            return $this->db->insert_id();
        }
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'user_action_log', $obj );
    }
    // 删
    public function del($where) {
        $this->db->where ( $where );
        $this->db->delete ( 'user_action_log' );
    }

    // 改条件
    public function update_where($obj, $where) {
        $this->db->where ( $where );
        $this->db->update ( 'user_action_log', $obj );
    }

    //查找数量
    public function get_count($where=FALSE){
        $this->db->where ($where);
        return $this->db->count_all_results('user_action_log');
    }

}