<?php defined('BASEPATH') or exit('No direct script access allowed');


class Nos extends MY_Controller
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
        $this->load->model('Nos_model');
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

        $data['title'] = 'NOS';
        $data['nos'] = $this->Nos_model->get_nos();
        
        $this->render_page('admin/masters/list-nos',$data);
    }
    
     public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $nos_id = $this->input->post('nos_id');
        
        $data = array(
			'nos_code' => $this->input->post('nos_code'),
			'nos_title' => $this->input->post('nos_title'),
			'status' => $this->input->post('status'),
		);
		
        if($nos_id == 0) { //Insert
            $nos_id = $this->Mdmaster->addRecord($data,'tbl_national_occupational_standards');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('nos_id', $nos_id);
            $query = $this->db->update('tbl_national_occupational_standards', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-nos');
        
    }

    // Delete Nos record
    public function delete($nos_id) {
        $this->require_permission('delete_masters');
        
        //Check whether this nos is mapped to trade
        $checkIfNosExists = $this->Mdmaster->checkIfExists('nos_id',$nos_id,'tbl_map_trade_nos');

        if($checkIfNosExists == false) {
            $this->Nos_model->delete_data($nos_id);
            
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as NOS already mapped to Trades');
        }
        
        redirect('list-nos');
    }

    public function CheckDuplicateNOSCode() {
        $nos_code = $this->input->post('nos_code');
        $nos_id = $this->input->post('nos_id');
        
        $condition = ($nos_id > 0) ? " nos_id != ".$nos_id : "";
        $validate = $this->Mdmaster->checkDuplicate('nos_code',$nos_code,'tbl_national_occupational_standards',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
    
}
