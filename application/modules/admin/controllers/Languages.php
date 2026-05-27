<?php defined('BASEPATH') or exit('No direct script access allowed');


class Languages extends MY_Controller
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
        $this->load->model('languages_model');
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

        $data['title'] = 'Languages';
        $data['languages'] = $this->languages_model->get_languages();

        $this->render_page('admin/masters/list-languages',$data);
    }
    
    public function save() {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";*/
        
        $language_id = $this->input->post('language_id');
        
        $data = array(
			'language_name' => $this->input->post('language_name'),
			'status' => $this->input->post('status'),
		);
		
        if($language_id == 0) { //Insert
            $language_id = $this->Mdmaster->addRecord($data,'tbl_languages');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('language_id', $language_id);
            $query = $this->db->update('tbl_languages', $data);
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-languages');
        
    }

    // Delete Scheme record
    public function delete($language_id) {
        $this->require_permission('delete_masters');
        
        //Check whether this language is mapped to language_questions
        $checkIfLanguageQuestionsExists = $this->Mdmaster->checkIfExists('lid',$language_id,'tbl_language_questions');

        if($checkIfLanguageQuestionsExists == false) {
            $this->languages_model->delete_data($language_id);
            
            $this->session->set_flashdata('msg', 'Data deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as Language already mapped to Questions');
        }
        
        redirect('list-languages');
    }

    public function CheckDuplicateLanguageName() {
        $language_name = $this->input->post('language_name');
        $language_id = $this->input->post('language_id');
        
        $condition = ($language_id > 0) ? " language_id != ".$language_id : "";
        $validate = $this->Mdmaster->checkDuplicate('language_name',$language_name,'tbl_languages',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
}
