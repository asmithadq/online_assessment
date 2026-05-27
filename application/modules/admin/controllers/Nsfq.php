<?php defined('BASEPATH') or exit('No direct script access allowed');


class Nsfq extends MY_Controller
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
        $this->load->model('nsfq_model');
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

        $data['title'] = 'NSFQ Levels';
        $data['nsfq_level'] = $this->nsfq_model->get_nsfq_level();

        $this->render_page('admin/masters/list-nsfq-levels',$data);
    }
	
	
	 public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $nsfq_id = $this->input->post('nsfq_id');
        
        $data = array(
			'nsfq_level' => $this->input->post('nsfq_level')
		);
		
        if($nsfq_id == 0) { //Insert
            $nsfq_id = $this->Mdmaster->addRecord($data,'tbl_nsfq_levels');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('nsfq_id', $nsfq_id);
            $query = $this->db->update('tbl_nsfq_levels', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-nsfq-levels');
        
    }
	
	 // Delete NSFQ record
    public function delete($nsfq_id) {
        $this->require_permission('delete_masters');
        
        //Check whether this language is mapped to language_questions
        $checkIfNsfqLevelExists = $this->Mdmaster->checkIfExists('nsfq_id',$nsfq_id,'tbl_trades');

        if($checkIfNsfqLevelExists == false) {
            $this->nsfq_model->delete_data($nsfq_id);
            
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as NSFQ already mapped to Questions');
        }
        
        redirect('list-nsfq-levels');
    }
	
}
