<?php defined('BASEPATH') or exit('No direct script access allowed');


class Settings extends MY_Controller
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
        $this->load->model('settings_model');
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

        $data['title'] = 'Email Templates';
        $data['email_data'] = $this->settings_model->get_email_data();

        $this->render_page('admin/masters/list-email-templates',$data);
    }
    
	public function save() {
        $this->require_permission('add_masters');

        //echo "<pre>";
        //print_r($_POST);
        //print_r($_FILES);
        //echo "</pre>";
        //exit;
        
        $id = $this->input->post('id');
        
        $data = array(
			'email_subject' => $this->input->post('email_subject'),
			'email_content' => $this->input->post('email_content'),
			'status' => $this->input->post('status'),
		);
		
        
        if($id == 0) { //Insert
            $id = $this->Mdmaster->addRecord($data,'tbl_email_templates');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('id', $id);
            $query = $this->db->update('tbl_email_templates', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-email-templates');
        
    }
	
	// Delete Email Template record
    public function delete($id) {
        $this->require_permission('delete_masters');

        $error = 1;

		$this->settings_model->delete_data($id);
		$error = 0;
        
        if($error == 0) {
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete.');
        }

        redirect('list-email-templates');
    }
	
	
	 public function viewMappedEmailContent() {
        $template_id = $this->input->post('template_id');
        $output = "";
        
        $arr_email_templates = $this->settings_model->getEmailTemplatesByID($template_id);
        //echo "<br> str ".$this->db->last_query();exit;
        if($arr_email_templates != false) {
			$output = $arr_email_templates[0]['email_content'];
        }
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();       
        $data['output'] = $output;
        
        echo json_encode($data);
    }
	
	
	public function editEmailTemplate() {
        $template_id = $this->input->post('template_id');
        $data['subject'] = "";
        $data['content'] = "";
		$data['status'] = "";
		
        $arr_email_templates = $this->settings_model->getEmailTemplatesByID($template_id);
        //echo "<br> str ".$this->db->last_query();exit;
        if($arr_email_templates != false) {
			$data['subject'] = $arr_email_templates[0]['email_subject'];
			$data['content'] = $arr_email_templates[0]['email_content'];
			$data['status'] = $arr_email_templates[0]['status'];
        }
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();       
        
        echo json_encode($data);
    }
	
}
