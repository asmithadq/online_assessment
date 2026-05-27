<?php
class Subschemes_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_subschemes() {
        $this->db->select('*');
        $this->db->join('tbl_schemes', 'tbl_schemes.scheme_id = tbl_subschemes.scheme_id');
        return $this->db->get('tbl_subschemes')->result();
    }

    
    public function delete_data($subscheme_id) {
        // Delete data from the table based on subscheme_id
        $this->db->where('subscheme_id', $subscheme_id);
        $this->db->delete('tbl_subschemes');
    }
    
    public function get_schemes_dropdown_data() {
        $this->db->select('scheme_id,scheme_name');
        $this->db->where('status', 1); // Add the WHERE condition
        $this->db->order_by('scheme_name', 'ASC'); // Add the ORDER BY clause
        $query = $this->db->get('tbl_schemes');
        $result = $query->result();

        $dropdown_data = array();

        foreach ($result as $row) {
            // Customize the display value as needed
            $display_value = $row->scheme_name ;
            
            $dropdown_data[$row->scheme_id] = $display_value;
        }

        return $dropdown_data;
    }
    
}
?>
