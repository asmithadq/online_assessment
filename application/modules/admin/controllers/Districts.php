<?php defined('BASEPATH') or exit('No direct script access allowed');


class Districts extends MY_Controller
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
        $this->load->model('Districts_model');
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

        $data['title'] = 'Districts';
      
        $data['districts'] = $this->Districts_model->get_districts();
        
        $data['dropdown_data'] = $this->Districts_model->get_dropdown_data();
        
        $this->render_page('admin/masters/list-districts',$data);
    }
    
    
    public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $dist_id = $this->input->post('dist_id');
        
        $data = array(
            'dist_code' => $this->input->post('dist_code'),
			'dist_name' => $this->input->post('dist_name'),
			'state_id' => $this->input->post('state_id'),
			'status' => $this->input->post('status'),
		);
		
        if($dist_id == 0) { //Insert
            $dist_id = $this->Mdmaster->addRecord($data,'tbl_districts');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('dist_id', $dist_id);
            $query = $this->db->update('tbl_districts', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-districts');
        
    }

    // Delete districts record
    public function delete($dist_id) {
        $this->require_permission('delete_masters');
        
        $error = 1;
        //Check whether this state is mapped to Partners/Centers/Students/Assessors
        $checkIfDistrictCenterExists = $this->Mdmaster->checkIfExists('district',$dist_id,'tbl_training_centers');
        if($checkIfDistrictCenterExists == false) {
            $checkIfDistrictPartnerExists = $this->Mdmaster->checkIfExists('district',$dist_id,'tbl_training_partners');
            if($checkIfDistrictPartnerExists == false) {
                $checkIfDistrictStudentExists = $this->Mdmaster->checkIfExists('district_id',$dist_id,'tbl_students');
                if($checkIfDistrictStudentExists == false) {
                    $checkIfDistrictAssessorExists = $this->Mdmaster->checkIfExists('district_id',$dist_id,'tbl_assessor');
                    if($checkIfDistrictAssessorExists == false) {

                        $this->Districts_model->delete_data($dist_id);
                        $error = 0;
                    }
                }
            } 
        }
        
        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as District already mapped to Partners/Centers/Students/Assessors');
        }
        redirect('list-districts');
    }

}
