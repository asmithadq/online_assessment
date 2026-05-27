<?php defined('BASEPATH') or exit('No direct script access allowed');


class Centers extends MY_Controller
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
        $this->load->model('center_model');
        $this->load->model('Mdmaster');
        
        $this->require_module_permission('training_centers');
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
        $this->require_permission('view_training_centers');
        
        $data = array();   
        
        $this->render_page('admin/center/list-training-centers',$data);
    }
    
      function getLists(){
        $data = $row = array();
        
        // Fetch member's records
        $centersData = $this->center_model->getRows($_POST);

        $arrSscMappedDetails = array();
        
        $arr_tc_details = $this->center_model->getcenterDetails();
        //echo "<br> str ".$this->db->last_query();
        if($arr_tc_details != false) {
            foreach($arr_tc_details as $details) {
                if(array_key_exists($details['tc_id'],$arrSscMappedDetails)) {
                    $arrSscMappedDetails[$details['tc_id']] .= '<li>'.$details['ssc_title'].' ('.$details['ssc_code'].')</li>';
                }
                else {
                    $arrSscMappedDetails[$details['tc_id']] = '<li>'.$details['ssc_title'].' ('.$details['ssc_code'].')</li>';
                }
            }
        }
        
        $i = $_POST['start'];
        foreach($centersData as $center){
            $i++;

            $state = ($center['state'] != "") ? $center['state_code'].'-'.$center['state_name'] : '';
            $district = ($center['district'] != "") ? $center['dist_code'].'-'.$center['dist_name'] : '';
			
            $logo = ($center['logo'] != "") ? '<img class="rounded-circle" width="35" src="'.base_url().$this->config->item('training_center_images_path').$center['logo'].'" alt="">' : '';
            $status = ($center['status'] == 1) ? '<span class="badge light badge-success border-0">Active</span>' : '<span class="badge light badge-danger border-0">Inactive</span>';
			
			$status = "Active";
			if($center['status'] == 0)
			{
				$status = "Inactive";
			}

            $mapped_ssc = (array_key_exists($center['tc_id'],$arrSscMappedDetails)) ? $arrSscMappedDetails[$center['tc_id']] : "";

            $state = ($center['city'] != "") ? $center['state_name'].','.$center['city'] : $center['state_name'];
			
			$action = '<div class="d-flex">';
			
			$action .= '<a href="#" class="btn btn-dark shadow btn-xs sharp me-1" id="btn-'.$center['tc_id'].'" onclick="getCenterDetails('.$center['tc_id'].');" data-logo="'.$center['logo'].'" 
                                    data-tc_code="'.$center['tc_code'].'" data-name="'.$center['name'].'" data-address_1="'.$center['address_1'].'" data-address_2="'.$center['address_2'].'" 
                                    data-state="'.$state.'" data-district="'.$center['dist_name'].'" data-pincode="'.$center['pincode'].'" 
                                    data-email="'.$center['email'].'" data-phone="'.$center['phone'].'" data-mobile="'.$center['mobile'].'" data-website="'.$center['website'].'" 
                                    data-bank_name="'.$center['bk_name'].'" data-bank_branch="'.$center['bank_branch'].'" data-bank_account_no="'.$center['bank_account_no'].'" 
                                    data-contact_first_name="'.$center['contact_first_name'].'" data-contact_last_name="'.$center['contact_last_name'].'" data-contact_middle_name="'.$center['contact_middle_name'].'" 
                                    data-contact_gender="'.$center['contact_gender'].'" data-contact_photo="'.$center['contact_photo'].'" data-contact_phone="'.$center['contact_phone'].'" 
                                    data-contact_mobile="'.$center['contact_mobile'].'" data-status="'.$status.'" data-mapped_ssc="'.$mapped_ssc.'">
                                    <i class="fas fa-eye"></i></a><span id="spin_'.$center['tc_id'].'" style="display:none;" class="fa-stack fa-lg"><i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span>';
			
            $action .= '<a href="'.site_url('edit-training-center/'. $center['tc_id']).'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
							<a href="'.site_url('delete-training-center/'. $center['tc_id']).'" onclick="return confirm(\'Are you sure you want to delete this record?\');" class="btn btn-danger shadow btn-xs sharp">
							    <i class="fa fa-trash"></i> 
							</a>
						</div>';
            
            $data[] = array($i, $logo." ".$center['name']."(".$center['tc_code'].")",$center['tp_name']."(".$center['tp_code'].")",$state,$district,$center['email'],$center['mobile'],$status,$action);
        }
        
        /*echo "<pre>";
        print_r($centersData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->center_model->countAll(),
            "recordsFiltered" => $this->center_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
    
    public function viewAddEditForm($tc_id = 0)
    {
        $this->require_permission('add_training_centers');

        $condition = "status = 1";
        $data['arr_state'] = $this->Mdmaster->getAllRecords('tbl_states',$condition,'state_name','ASC');
        
        $condition = "status = 1";
        $data['arr_district'] = $this->Mdmaster->getAllRecords('tbl_districts',$condition,'dist_name','ASC');
        
        $condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
        
        $condition = "status = 1";
        $data['arr_banks'] = $this->Mdmaster->getAllRecords('tbl_banks',$condition,'bank_name','ASC');
        
        $condition = "status = 1";
        $data['arr_training_partners'] = $this->Mdmaster->getAllRecords('tbl_training_partners',$condition,'name','ASC');

        $arrSscMappedDetails = array();
        
        if($tc_id > 0) {
            $data['arr_tc_details'] = $this->center_model->getcenterDetails($tc_id);
            //echo "<br> str ".$this->db->last_query();exit;
            if($data['arr_tc_details'] != false) {
                foreach($data['arr_tc_details'] as $details) {
                    $arrSscMappedDetails[$details['ssc_id']] = $details['ssc_id'];
                }
            }
        }
        
        $data['tc_id'] = $tc_id;
        $data['arrSscMappedDetails'] = $arrSscMappedDetails;

        /*echo "<pre>";
        print_r($arrSscMappedDetails);
        echo "</pre>";
        exit;*/
        
        $this->render_page('admin/center/add-edit-training-centers',$data);
    }
    
    public function save()
    {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";
        exit;*/
        $tc_id = $this->input->post('tc_id');
        
        $data = array(
            'tp_id' => $this->input->post('tp_id'),
			'name' => $this->input->post('name'),
            'tc_code' => $this->input->post('tc_code'),
            'address_1' => $this->input->post('address_1'),
            'address_2' => $this->input->post('address_2'),
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'district' => $this->input->post('district'),
            'pincode' => $this->input->post('pincode'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'mobile' => $this->input->post('mobile'),
            'website' => $this->input->post('website'),
            'bank_name' => $this->input->post('bank_name'),
            'bank_branch' => $this->input->post('bank_branch'),
            'bank_account_no' => $this->input->post('bank_account_no'),
            'contact_first_name' => $this->input->post('contact_first_name'),
            'contact_middle_name' => $this->input->post('contact_middle_name'),
            'contact_last_name' => $this->input->post('contact_last_name'),
            'contact_gender' => $this->input->post('contact_gender'),
            'contact_phone' => $this->input->post('contact_phone'),
            'contact_mobile' => $this->input->post('contact_mobile'),
            'status' => $this->input->post('status'),
        );
		
		if (!empty($_FILES)) {
            if (isset($_FILES['logo']) && $_FILES['logo']['name'] != '') {
                $file_ext = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
                
                $data['logo'] = uploadImage('logo', 'training_center', seo_friendly_url($this->input->post('tp_code')).'-logo-' . mt_rand(11, 99) . '.'.$file_ext);
            }
            if (isset($_FILES['contact_photo']) && $_FILES['contact_photo']['name'] != '') {
                $file_ext = pathinfo($_FILES["contact_photo"]["name"], PATHINFO_EXTENSION);
                
                $data['contact_photo'] = uploadImage('contact_photo', 'training_center', seo_friendly_url($this->input->post('tp_code')).'-contact_photo-' . mt_rand(11, 99) . '.'.$file_ext);
            }
        }
        
        if($tc_id == 0) { //Insert
            $tc_id = $this->Mdmaster->addRecord($data,'tbl_training_centers');
            
            $arr_ssc_id = $this->input->post('ssc_id');
            if(count($arr_ssc_id) > 0) {
                foreach($arr_ssc_id as $scc_id) {
                    //Map center to scc_id
                    $insData = array(
            			'tc_id' => $tc_id,
                        'ssc_id' => $scc_id,
                    );
                    
                    $this->Mdmaster->addRecord($insData,'tbl_map_center_sector_skill_councils');
                }
            }
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('tc_id', $tc_id);
            $query = $this->db->update('tbl_training_centers', $data);
            
            $arr_ssc_id = $this->input->post('ssc_id');
            if(count($arr_ssc_id) > 0) {
                $this->db->where('tc_id', $tc_id);
	            $result=$this->db->delete('tbl_map_center_sector_skill_councils');
                
                foreach($arr_ssc_id as $scc_id) {
                    //Map center to scc_id
                    $insData = array(
            			'tc_id' => $tc_id,
                        'ssc_id' => $scc_id,
                    );
                    
                    $this->Mdmaster->addRecord($insData,'tbl_map_center_sector_skill_councils');
                }
            }
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-training-centers');
    }
    
    public function delete($tc_id) {
        $this->require_permission('delete_training_centers');

        //Check whether this tc is mapped to batches
        $checkIfBatchExists = $this->Mdmaster->checkIfExists('tc_id',$tc_id,'tbl_training_batches');

        if($checkIfBatchExists == false) {
            $this->db->where('tc_id', $tc_id);
            $result=$this->db->delete('tbl_training_centers');
            
            $this->session->set_flashdata('msg', 'Center deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as training center already mapped to batches');
        }
        redirect('list-training-centers');
    }
    
    public function CheckDuplicateTcCode() {
        $tc_code = $this->input->post('tc_code');
        $tc_id = $this->input->post('tc_id');
        
        $condition = ($tc_id > 0) ? " tc_id != ".$tc_id : "";
        $validate = $this->Mdmaster->checkDuplicate('tc_code',$tc_code,'tbl_training_centers',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
    
    
   
}
