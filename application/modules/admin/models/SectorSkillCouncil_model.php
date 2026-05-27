<?php
class SectorSkillCouncil_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_ssc_data() {
        $this->db->select('*');
        $this->db->from('tbl_sector_skill_council');
        $this->db->order_by('status','DESC');
        return $this->db->get()->result();
    }
    
   public function insert_data($data) {
        // Insert data into the table
        $this->db->insert('tbl_sector_skill_council', $data);
    }

    public function update_data($ssc_id, $data) {
        // Update data in the table based on ssc_id
        $this->db->where('ssc_id', $ssc_id);
        $this->db->update('tbl_sector_skill_council', $data);
    }

    public function delete_data($ssc_id) {
        // Delete data from the table based on ssc_id
        $this->db->where('ssc_id', $ssc_id);
        $this->db->delete('tbl_sector_skill_council');
    }
    
    
 
}
?>
