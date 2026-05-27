<?php defined('BASEPATH') or exit('No direct script access allowed');


class Partners extends MY_Controller
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
        $this->load->model('partner_model');
        $this->load->model('Mdmaster');
        
        $this->require_module_permission('training_partners');
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
        $this->require_permission('view_partner');
        
        $data = array();   
        
        $this->render_page('admin/partner/list-training-partners',$data);
    }
    
      function getLists(){
        $data = $row = array();
        
        // Fetch member's records
        $partnersData = $this->partner_model->getRows($_POST);

        $arrSscMappedDetails = array();
        
        $arr_tp_details = $this->partner_model->getPartnerDetails();
        //echo "<br> str ".$this->db->last_query();
        if($arr_tp_details != false) {
            foreach($arr_tp_details as $details) {
                if(array_key_exists($details['tp_id'],$arrSscMappedDetails)) {
                    $arrSscMappedDetails[$details['tp_id']] .= '<li>'.$details['ssc_title'].' ('.$details['ssc_code'].')</li>';
                }
                else {
                    $arrSscMappedDetails[$details['tp_id']] = '<li>'.$details['ssc_title'].' ('.$details['ssc_code'].')</li>';
                }
            }
        } 
        
        $i = $_POST['start'];
        foreach($partnersData as $partner){
            $i++;

            $address = "";
            if($partner['address_1'] != "") {
                $address = $partner['address_1'];
            }
            if($partner['address_2'] != "") {
                $address .= ($address != "") ? ",".$partner['address_2'] : $partner['address_2'];
            }
            if($partner['city'] != "") {
                $address .= ($address != "") ? ",".$partner['city'] : $partner['city'];
            }
            
            $state = ($partner['state'] != "") ? $partner['state_code'].'-'.$partner['state_name'] : '';
			
            $district = ($partner['district'] != "") ? $partner['dist_code'].'-'.$partner['dist_name'] : '';
			
            $logo = ($partner['logo'] != "") ? '<img class="rounded-circle" width="35" src="'.base_url().$this->config->item('training_partner_images_path').$partner['logo'].'" alt="">' : '';
			
            $status = ($partner['status'] == 1) ? '<span class="badge light badge-success border-0">Active</span>' : '<span class="badge light badge-danger border-0">Inactive</span>';
			
            $contact_photo = ($partner['contact_photo'] != "") ? '<img class="rounded-circle" width="35" src="'.base_url().$this->config->item('training_partner_images_path').$partner['contact_photo'].'" alt="">' : '';
			
			$status = "Active";
			if($partner['status'] == 0)
			{
				$status = "Inactive";
			}

            $mapped_ssc = (array_key_exists($partner['tp_id'],$arrSscMappedDetails)) ? $arrSscMappedDetails[$partner['tp_id']] : "";

            $action = '<div class="d-flex">';

            $action .= '<a href="#" class="btn btn-dark shadow btn-xs sharp me-1" id="btn-'.$partner['tp_id'].'" onclick="getPartnerDetails('.$partner['tp_id'].');" data-logo="'.$partner['logo'].'" 
                                    data-tp_code="'.$partner['tp_code'].'" data-name="'.$partner['name'].'" data-address_1="'.$partner['address_1'].'" data-address_2="'.$partner['address_2'].'" 
                                    data-address="'.$address.'"  data-city="'.$partner['city'].'" data-state="'.$partner['state_name'].'" data-district="'.$partner['dist_name'].'" 
                                    data-pincode="'.$partner['pincode'].'" data-email="'.$partner['email'].'" data-phone="'.$partner['phone'].'" data-mobile="'.$partner['mobile'].'" 
                                    data-website="'.$partner['website'].'" data-bank_name="'.$partner['bk_name'].'" data-bank_branch="'.$partner['bank_branch'].'" 
                                    data-bank_account_no="'.$partner['bank_account_no'].'" data-contact_first_name="'.$partner['contact_first_name'].'" 
                                    data-contact_last_name="'.$partner['contact_last_name'].'" data-contact_middle_name="'.$partner['contact_middle_name'].'" 
                                    data-contact_gender="'.$partner['contact_gender'].'" data-contact_photo="'.$partner['contact_photo'].'" data-contact_phone="'.$partner['contact_phone'].'" 
                                    data-contact_mobile="'.$partner['contact_mobile'].'" data-status="'.$status.'" data-mapped_ssc="'.$mapped_ssc.'">
                                    <i class="fas fa-eye"></i></a><span id="spin_'.$partner['tp_id'].'" style="display:none;" class="fa-stack fa-lg"><i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span>';							 
						
			$action .= '<a href="'.site_url('edit-training-partner/'. $partner['tp_id']).'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
						<a href="'.site_url('delete-training-partner/'. $partner['tp_id']).'" onclick="return confirm(\'Are you sure you want to delete this record?\');" class="btn btn-danger shadow btn-xs sharp">
							<i class="fa fa-trash"></i>
						</a>
					</div>';
            
            $data[] = array($i, $logo." ".$partner['name']."(".$partner['tp_code'].")",$state,$district,$partner['email'],$partner['mobile'],$status,$action);
        }
        
        /*echo "<pre>";
        print_r($partnersData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->partner_model->countAll(),
            "recordsFiltered" => $this->partner_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
    
    public function viewAddEditForm($tp_id = 0)
    {
        $this->require_permission('add_partner');

        $condition = "status = 1";
        $data['arr_state'] = $this->Mdmaster->getAllRecords('tbl_states',$condition,'state_name','ASC');
        
        $condition = "status = 1";
        $data['arr_district'] = $this->Mdmaster->getAllRecords('tbl_districts',$condition,'dist_name','ASC');
        
        $condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
        
        $condition = "status = 1";
        $data['arr_banks'] = $this->Mdmaster->getAllRecords('tbl_banks',$condition,'bank_name','ASC');

        $arrSscMappedDetails = array();
        
        if($tp_id > 0) {
            $data['arr_tp_details'] = $this->partner_model->getPartnerDetails($tp_id);
            //echo "<br> str ".$this->db->last_query();
            if($data['arr_tp_details'] != false) {
                foreach($data['arr_tp_details'] as $details) {
                    $arrSscMappedDetails[$details['ssc_id']] = $details['ssc_id'];
                }
            }
        }
        
        $data['tp_id'] = $tp_id;
        $data['arrSscMappedDetails'] = $arrSscMappedDetails;
        
        $this->render_page('admin/partner/add-edit-training-partners',$data);
    }
    
    public function save()
    {
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";
        exit;*/
        $tp_id = $this->input->post('tp_id');
        
        $data = array(
			'name' => $this->input->post('name'),
            'tp_code' => $this->input->post('tp_code'),
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
                
                $data['logo'] = uploadImage('logo', 'training_partner', seo_friendly_url($this->input->post('tp_code')).'-logo-' . mt_rand(11, 99) . '.'.$file_ext);
            }
            if (isset($_FILES['contact_photo']) && $_FILES['contact_photo']['name'] != '') {
                $file_ext = pathinfo($_FILES["contact_photo"]["name"], PATHINFO_EXTENSION);
                
                $data['contact_photo'] = uploadImage('contact_photo', 'training_partner', seo_friendly_url($this->input->post('tp_code')).'-contact_photo-' . mt_rand(11, 99) . '.'.$file_ext);
            }
        }
        
        if($tp_id == 0) { //Insert
            $tp_id = $this->Mdmaster->addRecord($data,'tbl_training_partners');
            
            $arr_ssc_id = $this->input->post('ssc_id');
            if(count($arr_ssc_id) > 0) {
                foreach($arr_ssc_id as $scc_id) {
                    //Map partner to scc_id
                    $insData = array(
            			'tp_id' => $tp_id,
                        'ssc_id' => $scc_id,
                    );
                    
                    $this->Mdmaster->addRecord($insData,'tbl_map_partner_sector_skill_councils');
                }
            }
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('tp_id', $tp_id);
            $query = $this->db->update('tbl_training_partners', $data);
            
            $arr_ssc_id = $this->input->post('ssc_id');
            if(count($arr_ssc_id) > 0) {
                $this->db->where('tp_id', $tp_id);
	            $result=$this->db->delete('tbl_map_partner_sector_skill_councils');
                
                foreach($arr_ssc_id as $scc_id) {
                    //Map partner to scc_id
                    $insData = array(
            			'tp_id' => $tp_id,
                        'ssc_id' => $scc_id,
                    );
                    
                    $this->Mdmaster->addRecord($insData,'tbl_map_partner_sector_skill_councils');
                }
            }
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-training-partners');
    }
    
    public function delete($tp_id) {
        $this->require_permission('delete_partner');

        //Check whether this tp is mapped to tc
        $checkIfPartnerExists = $this->Mdmaster->checkIfExists('tp_id',$tp_id,'tbl_training_centers');

        if($checkIfPartnerExists == false) {
            $this->db->where('tp_id', $tp_id);
            $result=$this->db->delete('tbl_training_partners');
            
            $this->session->set_flashdata('msg', 'Training Partner deleted successfully');
        }
        else {
            $this->session->set_flashdata('error', 'Could not delete as training partner already mapped to centers');
        }
        redirect('list-training-partners');
    }
    
    public function CheckDuplicateTpCode() {
        $tp_code = $this->input->post('tp_code');
        $tp_id = $this->input->post('tp_id');
        
        $condition = ($tp_id > 0) ? " tp_id != ".$tp_id : "";
        $validate = $this->Mdmaster->checkDuplicate('tp_code',$tp_code,'tbl_training_partners',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }
    
    
   
}
