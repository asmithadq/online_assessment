<?php defined('BASEPATH') or exit('No direct script access allowed');


class Schemes extends MY_Controller
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
        $this->load->model('schemes_model');
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

        $data['title'] = 'Schemes';
        $data['schemes'] = $this->schemes_model->get_schemes();

        $this->render_page('admin/masters/list-schemes',$data);
    }
    
     public function save() {
        $this->require_permission('add_masters');

        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $scheme_id = $this->input->post('scheme_id');
        
        $data = array(
			'scheme_name' => $this->input->post('scheme_name'),
			'status' => $this->input->post('status'),
		);
		
        if($scheme_id == 0) { //Insert
            $scheme_id = $this->Mdmaster->addRecord($data,'tbl_schemes');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('scheme_id', $scheme_id);
            $query = $this->db->update('tbl_schemes', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-schemes');
        
    }

    // Delete Scheme record
    public function delete($scheme_id) {
        $this->require_permission('delete_masters');
        
        //Check whether this scheme is mapped to subscheme
        $checkIfSubSchemeExists = $this->Mdmaster->checkIfExists('scheme_id',$scheme_id,'tbl_subschemes');

        if($checkIfSubSchemeExists == false) {
            $this->schemes_model->delete_data($scheme_id);
            
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as Scheme already mapped to Sub Schemes');
        }

        redirect('list-schemes');
    }

    public function CheckDuplicateScheme() {
        $scheme_name = $this->input->post('scheme_name');
        $scheme_id = $this->input->post('scheme_id');
        
        $condition = ($scheme_id > 0) ? " scheme_id != ".$scheme_id : "";
        $validate = $this->Mdmaster->checkDuplicate('scheme_name',$scheme_name,'tbl_schemes',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
    
}
