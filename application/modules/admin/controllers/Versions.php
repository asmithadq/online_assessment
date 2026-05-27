<?php defined('BASEPATH') or exit('No direct script access allowed');


class Versions extends MY_Controller
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
        $this->load->model('versions_model');
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

        $data['title'] = 'Trade/QP Versions';
       
		$data['version_data'] = $this->versions_model->get_version_data();
		
        $this->render_page('admin/masters/list-versions',$data);
    }
	
	 public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $trade_version_id = $this->input->post('trade_version_id');
        
        $data = array(
			'trade_version' => $this->input->post('trade_version'),
		);
		      
        if($trade_version_id == 0) { //Insert
            $trade_version_id = $this->Mdmaster->addRecord($data,'tbl_trade_version');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('trade_version_id', $trade_version_id);
            $query = $this->db->update('tbl_trade_version', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-versions');
        
    }
	
	
	 // Delete Trade/QP Version record
    public function delete($trade_version_id) {
        $this->require_permission('delete_masters');
        
        $error = 1;
        //Check whether this trade_version_id is mapped to Trade/QP
        $checkIfVersionExists = $this->Mdmaster->checkIfExists('trade_version_id',$trade_version_id,'tbl_trades');
        
                    if($checkIfVersionExists == false) {
                        $this->versions_model->delete_data($trade_version_id);
                        $error = 0;
                    }
     
        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as Trade/QP Versions already mapped to Trade/QP');
        }

        redirect('list-versions');
    }
	
}
