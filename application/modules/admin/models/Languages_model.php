<?php
class Languages_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_languages() {
        return $this->db->get('tbl_languages')->result();
    }
    
    public function delete_data($language_id) {
        // Delete data from the table based on language_id
        $this->db->where('language_id', $language_id);
        $this->db->delete('tbl_languages');
    }
 
}
?>
