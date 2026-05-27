<?php

class Nsfq_model extends CI_Model {

    public function __construct() {

        parent::__construct();

        $this->load->database();

    }



    public function get_nsfq_level() {

        return $this->db->get('tbl_nsfq_levels')->result();

    }

    

    public function delete_data($nsfq_id) {

        // Delete data from the table based on nsfq_id	

        $this->db->where('nsfq_id', $nsfq_id);

        $this->db->delete('tbl_nsfq_levels');

    }

 

}

?>