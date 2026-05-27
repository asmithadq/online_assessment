<?php

class Attendance_model extends CI_Model {

    public function __construct() {

        parent::__construct();

        $this->load->database();

    }

   public function get_students_details_for_batch($tb_id) {
        $this->db->select("*");
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id', 'left');
        $this->db->where('tbl_students.tb_id', $tb_id);
        $this->db->where('tbl_students.status', 1);
        $query = $this->db->get('tbl_students');
        $result = $query->result_array();
        $count = $query->num_rows();
        //echo "<br> str ".$this->db->last_query();exit;
        $count = $query->num_rows();

        if ($count > 0) {
            return $result;
        } else {
            return 0;
        }
    }

}
?>