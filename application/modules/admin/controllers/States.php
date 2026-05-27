<?php defined('BASEPATH') or exit('No direct script access allowed');


class States extends MY_Controller
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
        $this->load->model('States_model');
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

        $data['title'] = 'States';
        
        $data['states'] = $this->States_model->get_states();
        
        $this->render_page('admin/masters/list-states',$data);
    }
    
    public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $state_id = $this->input->post('state_id');
        
        $data = array(
            'state_code' => $this->input->post('state_code'),
			'state_name' => $this->input->post('state_name'),
			'status' => $this->input->post('status'),
		);
		
        if($state_id == 0) { //Insert
            $state_id = $this->Mdmaster->addRecord($data,'tbl_states');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('state_id', $state_id);
            $query = $this->db->update('tbl_states', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-states');
        
    }

    // Delete States record
    public function delete($state_id) {
        $this->require_permission('delete_masters');
        
        $error = 1;
        //Check whether this state is mapped to District/Partners/Centers/Students/Assessors
        $checkIfStateDistrictExists = $this->Mdmaster->checkIfExists('state_id',$state_id,'tbl_districts');
        if($checkIfStateDistrictExists == false) {
            $checkIfStateCenterExists = $this->Mdmaster->checkIfExists('state',$state_id,'tbl_training_centers');
            if($checkIfStateCenterExists == false) {
                $checkIfStatePartnerExists = $this->Mdmaster->checkIfExists('state',$state_id,'tbl_training_partners');
                if($checkIfStatePartnerExists == false) {
                    $checkIfStateStudentExists = $this->Mdmaster->checkIfExists('state_id',$state_id,'tbl_students');
                    if($checkIfStateStudentExists == false) {
                        $checkIfStateAssessorExists = $this->Mdmaster->checkIfExists('state_id',$state_id,'tbl_assessor');
                        if($checkIfStateAssessorExists == false) {
    
                            $this->States_model->delete_data($state_id);
                            $error = 0;
                        }
                    }
                } 
            }
        }    
        
        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as State already mapped to District/Partners/Centers/Students/Assessors');
        }
        redirect('list-states');
    }
    

}
