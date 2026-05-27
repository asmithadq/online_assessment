<?php defined('BASEPATH') or exit('No direct script access allowed');


class Banks extends MY_Controller
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
        $this->load->model('banks_model');
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

        $data['title'] = 'Banks';
        $data['banks'] = $this->banks_model->get_banks();

        $this->render_page('admin/masters/list-banks',$data);
    }
    
    
    public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $bank_id = $this->input->post('bank_id');
        
        $data = array(
            'bank_name' => $this->input->post('bank_name'),
			'address' => $this->input->post('address'),
			'branch' => $this->input->post('branch'),
			'status' => $this->input->post('status'),
		);
		
        if($bank_id == 0) { //Insert
            $bank_id = $this->Mdmaster->addRecord($data,'tbl_banks');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('bank_id', $bank_id);
            $query = $this->db->update('tbl_banks', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-banks');
        
    }

    // Delete Banks record
    public function delete($bank_id) {
        $this->require_permission('delete_masters');
        
        $error = 0;
        //Check whether this bank is mapped to partner/centers
        $checkIfBankCenterExists = $this->Mdmaster->checkIfExists('bank_name',$bank_id,'tbl_training_centers');
        
        if($checkIfBankCenterExists == false) {
            $checkIfBankPartnerExists = $this->Mdmaster->checkIfExists('bank_name',$bank_id,'tbl_training_partners');
            if($checkIfBankPartnerExists == false) {

                $this->banks_model->delete_data($bank_id);
            } 
            else {
                $error = 1;
            }   
        }
        else {
            $error = 1;
        }

        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as Bank already mapped to Partners/Centers');
        }
        
        redirect('list-banks');
    }
}
