<?php

class Versions_model extends CI_Model {

    public function __construct() {

        parent::__construct();

        $this->load->database();

    }



    public function get_version_data() {

        $this->db->select('*');

        $this->db->from('tbl_trade_version');

        $this->db->order_by('trade_version_id','ASC');

        return $this->db->get()->result();

    }

    

   public function insert_data($data) {

        // Insert data into the table

        $this->db->insert('tbl_trade_version', $data);

    }



    public function update_data($trade_version_id, $data) {

        // Update data in the table based on trade_version_id

        $this->db->where('trade_version_id', $trade_version_id);

        $this->db->update('tbl_trade_version', $data);

    }



    public function delete_data($trade_version_id) {

        // Delete data from the table based on trade_version_id

        $this->db->where('trade_version_id', $trade_version_id);

        $this->db->delete('tbl_trade_version');

    }

}

?>