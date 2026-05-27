<?php
class Documents_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_documents_data() {
        $this->db->select('tbl_assessment_checklist_documents_master.*,tbl_assessment_checklist_documents_category.name as category');
        $this->db->from('tbl_assessment_checklist_documents_master');
        $this->db->join('tbl_assessment_checklist_documents_category','tbl_assessment_checklist_documents_category.checklist_cat_id = tbl_assessment_checklist_documents_master.checklist_cat_id','LEFT');
        $this->db->order_by('status','DESC');
        return $this->db->get()->result();
    }
    
   public function insert_data($data) {
        // Insert data into the table
        $this->db->insert('tbl_assessment_checklist_documents_master', $data);
    }

    public function update_data($acdm_id, $data) {
        // Update data in the table based on acdm_id
        $this->db->where('acdm_id', $acdm_id);
        $this->db->update('tbl_assessment_checklist_documents_master', $data);
    }

    public function delete_data($acdm_id) {
        // Delete data from the table based on acdm_id
        $this->db->where('acdm_id', $acdm_id);
        $this->db->delete('tbl_assessment_checklist_documents_master');
    }
    
    
 
}
?>
