<?php
/**
 *
 * @author lucus
 * 课程反馈名单
 *
 */
class Ratingslist_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    //查所有
    public function get_all($where = FALSE){
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'course_ratings_list', $where );
        return $query->result_array ();
    }

    // 查
    public function get_row($where = FALSE) {
        if ($where === FALSE) {
            return array ();
        }
        $query = $this->db->get_where ( 'course_ratings_list', $where );
        return $query->row_array ();
    }
    // 增
    public function create($obj) {
        $this->db->insert ( 'course_ratings_list', $obj );
        return $this->db->insert_id();
    }
    // 改
    public function update($obj, $id) {
        $this->db->where ( 'id', $id );
        $this->db->update ( 'course_ratings_list', $obj );
    }
    // 删
    public function del($where) {
        $this->db->where ( $where );
        $this->db->delete ( 'course_ratings_list' );
    }

    public function count($where){
        $this->db->where ($where);
        $this->db->from('course_ratings_list');
        return $this->db->count_all_results();
    }

}