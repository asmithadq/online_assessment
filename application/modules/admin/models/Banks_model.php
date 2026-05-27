<?php
class Banks_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_banks() {
        return $this->db->get('tbl_banks')->result();
    }
    
    public function delete_data($bank_id) {
        // Delete data from the table based on bank_id
        $this->db->where('bank_id', $bank_id);
        $this->db->delete('tbl_banks');
    }
 
}
?>
