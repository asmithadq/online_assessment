<?php
class States_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

      public function get_states() {
        $this->db->order_by('state_name', 'asc');
        return $this->db->get('tbl_states')->result();
    }
    
     public function delete_data($state_id) {
        // Delete data from the table based on state_id
        $this->db->where('state_id', $state_id);
        $this->db->delete('tbl_states');
    }
 
     
}
?>
