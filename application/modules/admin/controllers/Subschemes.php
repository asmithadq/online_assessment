<?php defined('BASEPATH') or exit('No direct script access allowed');


class Subschemes extends MY_Controller
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
        $this->load->model('Subschemes_model');
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

        $data['title'] = 'Subschemes';
        $data['subschemes'] = $this->Subschemes_model->get_subschemes();
        $data['schemes_dropdown_data'] = $this->Subschemes_model->get_schemes_dropdown_data();

        $this->render_page('admin/masters/list-subschemes',$data);
    }
    
     public function save() {
        $this->require_permission('add_masters');

        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $subscheme_id = $this->input->post('subscheme_id');
        
        $data = array(
			'subscheme_name' => $this->input->post('subscheme_name'),
			'scheme_id' => $this->input->post('scheme_id'),
			'status' => $this->input->post('status'),
		);
		
        if($subscheme_id == 0) { //Insert
            $subscheme_id = $this->Mdmaster->addRecord($data,'tbl_subschemes');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('subscheme_id', $subscheme_id);
            $query = $this->db->update('tbl_subschemes', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-subschemes');
        
    }

    // Delete Scheme record
    public function delete($subscheme_id) {
        $this->require_permission('delete_masters');
        
        //Check whether this sub scheme is mapped to batches
        $checkIfBatchExists = $this->Mdmaster->checkIfExists('subscheme_id',$subscheme_id,'tbl_training_batches');

        if($checkIfBatchExists == false) {
            $this->Subschemes_model->delete_data($subscheme_id);
            
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as Sub Scheme already mapped to Batches');
        }
        redirect('list-subschemes');
    }

    public function CheckDuplicateSubScheme() {
        $subscheme_name = $this->input->post('subscheme_name');
        $subscheme_id = $this->input->post('subscheme_id');
        
        $condition = ($subscheme_id > 0) ? " subscheme_id != ".$subscheme_id : "";
        $validate = $this->Mdmaster->checkDuplicate('subscheme_name',$subscheme_name,'tbl_subschemes',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
    
}
