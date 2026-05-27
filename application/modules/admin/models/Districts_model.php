<?php
class Districts_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
 
    /*public function get_districts() {
        $this->db->order_by('dist_name', 'asc');
        return $this->db->get('tbl_districts')->result();
    }*/
 
    public function get_districts() {
        $this->db->select('tbl_districts.dist_id, tbl_districts.state_id, tbl_districts.dist_code, tbl_districts.dist_name, 
        tbl_districts.status as dist_status, tbl_districts.created_dts as dist_created_dts, tbl_states.state_code, tbl_states.state_name, tbl_states.status as state_status');
        $this->db->from('tbl_districts');
        $this->db->join('tbl_states', 'tbl_districts.state_id = tbl_states.state_id', 'left');
        //$this->db->limit(1);
        $this->db->order_by('tbl_districts.dist_name', 'ASC'); // Assuming you want to order by the state status in ascending order

        $query = $this->db->get();

        return $query->result();
    }
    
     public function get_dropdown_data() {
        $this->db->select('state_id,state_code,state_name');
        $this->db->where('status', 1); // Add the WHERE condition
        $this->db->order_by('state_name', 'ASC'); // Add the ORDER BY clause
        $query = $this->db->get('tbl_states');
        $result = $query->result();

        $dropdown_data = array();

        foreach ($result as $row) {
            // Customize the display value as needed
            $display_value = $row->state_name . ' (' . $row->state_code. ')';
            
            $dropdown_data[$row->state_id] = $display_value;
        }

        return $dropdown_data;
    }
    
      public function delete_data($dist_id) {
        // Delete data from the table based on dist_id
        $this->db->where('dist_id', $dist_id);
        $this->db->delete('tbl_districts');
    }
 
}
?>
