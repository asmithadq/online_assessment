<?php defined('BASEPATH') or exit('No direct script access allowed');


class Documents extends MY_Controller
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
        $this->load->model('Documents_model');
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

        $data['title'] = 'Assessment Documents Master';
        $condition = "status = 1";
        $data['arr_assessment_checklist_documents_category'] = $this->Mdmaster->getAllRecords('tbl_assessment_checklist_documents_category',$condition,'checklist_cat_id','ASC');
        $data['documents_data'] = $this->Documents_model->get_documents_data();

        $this->render_page('admin/masters/list-assessment-documents-master',$data);
    }

    public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $acdm_id = $this->input->post('acdm_id');
        
        $data = array(
			'document_title' => $this->input->post('document_title'),
			'document_type' => $this->input->post('document_type'),
            'document_requirement' => $this->input->post('document_requirement'),
            'checklist_cat_id' => $this->input->post('checklist_cat_id'), 
			'status' => $this->input->post('status'),
		);
		
		if($acdm_id == 0) { //Insert
            $acdm_id = $this->Mdmaster->addRecord($data,'tbl_assessment_checklist_documents_master');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('acdm_id', $acdm_id);
            $query = $this->db->update('tbl_assessment_checklist_documents_master', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-assessment-documents-master');
        
    }

    // Delete document record
    public function delete($acdm_id) {
        $this->require_permission('delete_masters');
        
        $error = 1;
        //Check whether this ssc_id is mapped to partner/centers/trades/Assessor
        /*$checkIfSscCenterExists = $this->Mdmaster->checkIfExists('ssc_id',$ssc_id,'tbl_map_center_sector_skill_councils');
        
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
        }*/

        $this->Documents_model->delete_data($acdm_id);
        $error = 0;
        
        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as Document already mapped to checklist');
        }

        redirect('list-assessment-documents-master');
    }
    
    public function CheckDuplicateDocumentTitle() {
        $document_title = $this->input->post('document_title');
        $acdm_id = $this->input->post('acdm_id');
        
        $condition = ($acdm_id > 0) ? " acdm_id != ".$acdm_id : "";
        $validate = $this->Mdmaster->checkDuplicate('document_title',$document_title,'tbl_assessment_checklist_documents_master',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
  
}
