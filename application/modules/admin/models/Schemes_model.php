<?php
class Schemes_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_schemes() {
        return $this->db->get('tbl_schemes')->result();
    }
    
    public function delete_data($scheme_id) {
        // Delete data from the table based on scheme_id
        $this->db->where('scheme_id', $scheme_id);
        $this->db->delete('tbl_schemes');
    }
 
}
?>
