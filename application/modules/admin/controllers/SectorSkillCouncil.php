<?php defined('BASEPATH') or exit('No direct script access allowed');


class SectorSkillCouncil extends MY_Controller
{
    //
    public $CI;

    /**
     * An array of variables to be passed through to the
     * view, layout,....
     */
    protected $data = array();

    /**
     * [__construct description]
     *
     * @method __construct
     */
    public function __construct()
    {
        // To inherit directly the attributes of the parent class.
        parent::__construct();
        $this->load->model('SectorSkillCouncil_model');
        $this->load->model('Mdmaster');

        $this->require_module_permission('masters');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function list()
    {
        $this->require_permission('view_masters');

        $data['title'] = 'Sector Skill Councils';  // Set the title here
        $data['ssc_data'] = $this->SectorSkillCouncil_model->get_ssc_data();
        //echo "<br> str ".$this->db->last_query();exit;
        
        $this->render_page('admin/masters/list-sectorskillcouncils', $data);
        
    }
    
    public function save() {
        $this->require_permission('add_masters');

        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $ssc_id = $this->input->post('ssc_id');
        
        $data = array(
			'ssc_code' => $this->input->post('ssc_code'),
			'ssc_title' => $this->input->post('ssc_title'),
			'status' => $this->input->post('status'),
		);
		
		if (!empty($_FILES)) {
            if (isset($_FILES['ssc_logo']) && $_FILES['ssc_logo']['name'] != '') {
                $file_ext = pathinfo($_FILES["ssc_logo"]["name"], PATHINFO_EXTENSION);
                
                $data['ssc_logo'] = uploadImage('ssc_logo', 'ssc_logo', seo_friendly_url($this->input->post('ssc_code')).'-' . mt_rand(11, 99) . '.'.$file_ext);
            }
        }
        
        if($ssc_id == 0) { //Insert
            $ssc_id = $this->Mdmaster->addRecord($data,'tbl_sector_skill_council');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('ssc_id', $ssc_id);
            $query = $this->db->update('tbl_sector_skill_council', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-sectorskillcouncils');
        
    }

    // Delete SSC record
    public function delete($ssc_id) {
        $this->require_permission('delete_masters');

        $error = 1;
        //Check whether this ssc_id is mapped to partner/centers/trades/Assessor
        $checkIfSscCenterExists = $this->Mdmaster->checkIfExists('ssc_id',$ssc_id,'tbl_map_center_sector_skill_councils');
        
        if($checkIfSscCenterExists == false) {
            $checkIfSscPartnerExists = $this->Mdmaster->checkIfExists('ssc_id',$ssc_id,'tbl_map_partner_sector_skill_councils');
            if($checkIfSscPartnerExists == false) {
                $checkIfSscTradeExists = $this->Mdmaster->checkIfExists('ssc_id',$ssc_id,'tbl_trades');
                if($checkIfSscTradeExists == false) {
                    $checkIfSscAssessorExists = $this->Mdmaster->checkIfExists('ssc_id',$ssc_id,'tbl_map_assessor_sector_skill_councils');
                    if($checkIfSscAssessorExists == false) {

                        $this->SectorSkillCouncil_model->delete_data($ssc_id);
                        $error = 0;
                    }
                }
            } 
        }
        
        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as SSC already mapped to Partners/Centers/Trades/Assessors');
        }

        redirect('list-sectorskillcouncils');
    }
    
}
