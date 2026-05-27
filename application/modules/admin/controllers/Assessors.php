<?php defined('BASEPATH') or exit('No direct script access allowed');


class Assessors extends MY_Controller
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
        $this->load->model('Assessors_model');
        $this->load->model('Mdmaster');
        
        $this->require_module_permission('assessors');
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
        $this->require_permission('view_assessors');
        
        $data['title'] = 'Assessors';  // Set the title here
        
        $this->render_page('admin/assessor/list-assessors', $data);
        
    }
    
  /*  public function add()
    {
        $data['title'] = 'Assessor Profile';  // Set the title here
        
        $this->render_page('admin/add-assessors', $data);
        
    }*/
	
	function getLists(){
        $data = $row = array();
        
        // Fetch member's records
        $assessorsData = $this->Assessors_model->getRows($_POST);

        $arrAssociatedAgencies = array();
        //Get assessor associated agencies
        $arr_assessor_associated_agencies_data = $this->Mdmaster->getAllRecords('tbl_assessor_associated_agencies');
        if($arr_assessor_associated_agencies_data != false) {
            foreach($arr_assessor_associated_agencies_data as $details) {
                if(array_key_exists($details['assessor_id'],$arrAssociatedAgencies)) {
                    $arrAssociatedAgencies[$details['assessor_id']] .= '<li>'.$details['agency_name'].'</li>';
                }
                else {
                    $arrAssociatedAgencies[$details['assessor_id']] = '<li>'.$details['agency_name'].'</li>';
                }
            }
        }
		  
        $i = $_POST['start'];
        foreach($assessorsData as $assessor){
            $i++;
            
            $state = ($assessor['state_id'] != "") ? $assessor['state_name'] : '';

            $district = ($assessor['district_id'] != "") ? $assessor['dist_name'] : '';

            $ssc_codes = ($assessor['ssc_codes'] != "") ? $assessor['ssc_codes'] : '';
       
            $status = ($assessor['assessor_status'] == "Active") ? '<span class="badge light badge-success border-0">Active</span>' : '<span class="badge light badge-danger border-0">Inactive</span>';
			
            $assessor_photo = ($assessor['assessor_photo'] != "") ? '<img class="rounded-circle" width="35" src="'.base_url().$this->config->item('assessors_images_path').$assessor['assessor_photo'].'" alt="">' : "";;
			
			$arr_ssc = $this->Assessors_model->getAssessorSectorSkillCouncil($assessor['assessor_id']);
			$mapssc = "";
			
			if(count($arr_ssc) > 0)
			{
				$mapssc .= "<ul class='list-unstyled'>";
				foreach($arr_ssc as $row)
				{
					$mapssc .= "<li>".$row['ssc_title']."</li>";
				}		
				$mapssc .= "</ul>";
			}

            $mapAssociatedAgencies = (array_key_exists($assessor['assessor_id'],$arrAssociatedAgencies)) ? $arrAssociatedAgencies[$assessor['assessor_id']] : "";
			
            $action = '<div class="d-flex">';
			
			//$action .= '<a href="#" class="btn btn-dark shadow btn-xs sharp me-1" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"><i class="fas fa-eye"></i></a>';
			
			$action .= '<a href="#" class="btn btn-dark shadow btn-xs sharp me-1" id="btn-'.$assessor['assessor_id'].'" onclick="getAssessorDetails('.$assessor['assessor_id'].');" 
                                    data-assessor_code="'.$assessor['assessor_code'].'" data-assessor_name="'.$assessor['assessor_name'].'" data-assessor_gender="'.$assessor['assessor_gender'].'" 
                                    data-assessor_mobile="'.$assessor['assessor_mobile'].'" data-assessor_email="'.$assessor['assessor_email'].'" data-assessor_photo="'.$assessor['assessor_photo'].'" 
                                    data-address="'.$assessor['address'].'" data-state_name="'.$assessor['state_name'].'" data-dist_name="'.$assessor['dist_name'].'" 
                                    data-assessor_resume="'.$assessor['assessor_resume'].'" data-assessor_status="'.$assessor['assessor_status'].'" data-mapssc="'.$mapssc.'" data-mapAssociatedAgencies="'.$mapAssociatedAgencies.'">
                                    <i class="fas fa-eye"></i></a><span id="spin_'.$assessor['assessor_id'].'" style="display:none;" class="fa-stack fa-lg">
                                    <i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span>';							 
							 
			$action .= '<a href="'.site_url('edit-assessor/'. $assessor['assessor_id']).'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
						<a href="'.site_url('delete-assessor/'. $assessor['assessor_id']).'" onclick="return confirm(\'Are you sure you want to delete this record?\');" class="btn btn-danger shadow btn-xs sharp me-1">
						    <i class="fa fa-trash"></i>
						</a>
						<a href="'.site_url('reset-assessor-device/'. $assessor['assessor_id']).'" class="btn btn-light shadow btn-xs sharp" title="Reset Device"><i class="fas fa-refresh"></i></a>
						</div>';
        	
            $data[] = array($i, $assessor_photo." ".$assessor['assessor_name']."(".$assessor['assessor_code'].")",$assessor['assessor_email'],$assessor['assessor_mobile'],$assessor['assessor_password'],$ssc_codes,$state,$district,$status,$action); 
        }
        
        /*echo "<pre>";
        print_r($assessorsData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Assessors_model->countAll(),
            "recordsFiltered" => $this->Assessors_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
    
    public function viewAddEditForm($assessor_id = 0)
    {
		$this->require_permission('add_assessors');
        
        $data['title'] = 'Assessor Profile';
		
        $condition = "status = 1";
        $data['arr_state'] = $this->Mdmaster->getAllRecords('tbl_states',$condition,'state_name','ASC');
        
        $condition = "status = 1";
        $data['arr_district'] = $this->Mdmaster->getAllRecords('tbl_districts',$condition,'dist_name','ASC');   
        
        $condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
        
        $arr_mapped_ssc = array();
       
        if($assessor_id > 0) {
            $data['arr_assessor_details'] = $this->Assessors_model->getAssessorDetails($assessor_id);
            if($data['arr_assessor_details'] != false) {
                foreach($data['arr_assessor_details'] as $val) {
                    $arr_mapped_ssc[$val['ssc_id']] = $val['ssc_id'];
                }
            }
        }

        $condition = "assessor_id = ".$assessor_id;
        $data['arr_assessor_associated_agencies_data'] = $this->Mdmaster->getAllRecords('tbl_assessor_associated_agencies',$condition);

        $data['assessor_id'] = $assessor_id;
        $data['arr_mapped_ssc'] = $arr_mapped_ssc;
        
        $this->render_page('admin/assessor/add-edit-assessors',$data);
    }
    
    public function save()
    {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";
        exit;*/
        $assessor_id = $this->input->post('assessor_id');

        $data = array(
            'assessor_code' => $this->input->post('assessor_code'),
			'assessor_name' => $this->input->post('assessor_name'),
            'assessor_gender' => $this->input->post('assessor_gender'),
            'assessor_mobile' => $this->input->post('assessor_mobile'),
            'assessor_email' => $this->input->post('assessor_email'),
            'address' => $this->input->post('address'),
            'state_id' => $this->input->post('state_id'),
            'district_id' => $this->input->post('district_id'),
            'assessor_status' => $this->input->post('assessor_status'),
        );
		
		if (!empty($_FILES)) {
            if (isset($_FILES['assessor_photo']) && $_FILES['assessor_photo']['name'] != '') {
                $file_ext = pathinfo($_FILES["assessor_photo"]["name"], PATHINFO_EXTENSION);
                
                $data['assessor_photo'] = uploadImage('assessor_photo', 'assessors', 'assessor_photo-' . seo_friendly_url($this->input->post('assessor_code')).'-' . mt_rand(11, 99) . '.'.$file_ext);
                
            }
            if (isset($_FILES['assessor_resume']) && $_FILES['assessor_resume']['name'] != '') {
                $file_ext = pathinfo($_FILES["assessor_resume"]["name"], PATHINFO_EXTENSION);
                
                $data['assessor_resume'] = uploadImage('assessor_resume', 'assessors', 'assessor_resume-' . seo_friendly_url($this->input->post('assessor_code')).'-' . mt_rand(11, 99) . '.'.$file_ext);
            }
        }
        
        if($assessor_id == 0) { //Insert
            $str_token = $this->input->post('assessor_code').date('Ymdhis');
            $unique_token =  md5($str_token);

            $data['assessor_password'] = random_numeric(4);
            $data['unique_token'] = $unique_token;
        
            $assessor_id = $this->Mdmaster->addRecord($data,'tbl_assessor'); 
            
            $arr_ssc_id = $this->input->post('ssc_id');
            if(count($arr_ssc_id) > 0) {
                foreach($arr_ssc_id as $scc_id) {
                    //Map partner to scc_id
                    $insData = array(
            			'assessor_id' => $assessor_id,
                        'ssc_id' => $scc_id,
                    );
                    
                    $this->Mdmaster->addRecord($insData,'tbl_map_assessor_sector_skill_councils');
                }
            }
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('assessor_id', $assessor_id);
            $query = $this->db->update('tbl_assessor', $data);  
            
            $arr_ssc_id = $this->input->post('ssc_id');
            if(count($arr_ssc_id) > 0) {
                $this->db->where('assessor_id', $assessor_id);
	            $result=$this->db->delete('tbl_map_assessor_sector_skill_councils');
                
                foreach($arr_ssc_id as $scc_id) {
                    //Map partner to scc_id
                    $insData = array(
            			'assessor_id' => $assessor_id,
                        'ssc_id' => $scc_id,
                    );
                    
                    $this->Mdmaster->addRecord($insData,'tbl_map_assessor_sector_skill_councils');
                }
            }
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-assessors');
    }
    
    public function delete($assessor_id) {
        $this->require_permission('delete_assessors');
        
        //Check whether this tc is mapped to batches
        $checkIfBatchExists = $this->Mdmaster->checkIfExists('assessor_id',$assessor_id,'tbl_training_batches');

        if($checkIfBatchExists == false) {
            $this->db->where('assessor_id', $assessor_id);
	        $result=$this->db->delete('tbl_assessor');
            
            $this->session->set_flashdata('msg', 'Assessor deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as assessor already mapped to batches');
        }
        redirect('list-assessors');
    }
    
    public function CheckDuplicateAssessorCode() {
        $assessor_code = $this->input->post('assessor_code');
        $assessor_id = $this->input->post('assessor_id');
        
        $condition = ($assessor_id > 0) ? " assessor_id != ".$assessor_id : "";
        $validate = $this->Mdmaster->checkDuplicate('assessor_code',$assessor_code,'tbl_assessor',$condition);
        //echo "<br> str ".$this->db->last_query();
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }
    
    public function CheckDuplicateAssessorMobile() {
        $assessor_mobile = $this->input->post('assessor_mobile');
        $assessor_id = $this->input->post('assessor_id');
        
        $condition = ($assessor_id > 0) ? " assessor_id != ".$assessor_id : "";
        $validate = $this->Mdmaster->checkDuplicate('assessor_mobile',$assessor_mobile,'tbl_assessor',$condition);
        //echo "<br> str ".$this->db->last_query();
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }
    
    public function CheckDuplicateAssessorEmail() {
        $assessor_email = $this->input->post('assessor_email');
        $assessor_id = $this->input->post('assessor_id');
        
        $condition = ($assessor_id > 0) ? " assessor_id != ".$assessor_id : "";
        $validate = $this->Mdmaster->checkDuplicate('assessor_email',$assessor_email,'tbl_assessor',$condition);
        //echo "<br> str ".$this->db->last_query();
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }
    
    public function resetDevice($assessor_id) {
        $this->require_permission('reset_assessor_device');

        $updStatus['device_id'] = "";
        $updStatus['logged_in_status'] = 0;
        
        $this->db->where('assessor_id', $assessor_id);
	    $query = $this->db->update('tbl_assessor', $updStatus);
            
        $this->session->set_flashdata('msg', 'Device has been reset successfully');
        redirect('list-assessors');
    }
}
