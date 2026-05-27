<?php
class Nos_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_nos() {
        $this->db->select('*');
        return $this->db->get('tbl_national_occupational_standards')->result();
    }

    
    public function delete_data($nos_id) {
        // Delete data from the table based on nos_id
        $this->db->where('nos_id', $nos_id);
        $this->db->delete('tbl_national_occupational_standards');
    }
    
}
?>
