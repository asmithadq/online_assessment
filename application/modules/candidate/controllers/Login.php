<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter-HMVC
 *
 * @package    CodeIgniter-HMVC
 * @author     N3Cr0N (N3Cr0N@list.ru)
 * @copyright  2019 N3Cr0N
 * @license    https://opensource.org/licenses/MIT  MIT License
 * @link       <URI> (description)
 * @version    GIT: $Id$
 * @since      Version 0.0.1
 * @filesource
 *
 */

class Login extends MX_Controller
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
       // $this->load->model('Login_model');
        $this->load->model('Mdmaster');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function index()
    {
        $this->load->view('candidate/candidate-login');
    }

    public function validate()
	{
	    $enrollment_number = $this->input->post('enrollment_number');
	    $password = $this->input->post('password');

        if($enrollment_number == "" || $password == "") {
            $this->session->set_flashdata('error', "Please enter your enrollment number and password");
            redirect('candidate-login');
        }
	    
	    //Call Api to validate login
	    // API endpoint URL
        $api_url = base_url().'Api-Candidate-Login';
    
        // Data to be sent in the POST request
        $post_data = array(
            'enrollment_number' => $enrollment_number,
            'password' => $password,
        );

        $response = getResponseApi($api_url,$post_data);
        /*echo "<pre>";
	    print_r($response);
	    echo "</pre>";
	    exit;*/

        if($response->rcode == 200) { //Success
            $this->session->set_userdata('is_candidate_logged_in','1');
    			
            $this->session->set_userdata('enrollment_number',$enrollment_number);
            $this->session->set_userdata('candidate_id',$response->candidate_id);
            $this->session->set_userdata('unique_token',$response->unique_token);
            $this->session->set_userdata('profile_updated',$response->profile_updated);

            if($response->profile_updated == 1) {
                redirect('candidate-dashboard');
            }
            else {
                redirect('candidate-profile');
            }
        }
        else { //Error
            $this->session->set_flashdata('error', $response->message);
            redirect('candidate-login');
        }
    }
    
    public function logout()
	{
		$candidate_id = $this->session->userdata('candidate_id');
        $unique_token = $this->session->userdata('unique_token');
        
        // API endpoint URL
        $api_url = base_url().'Api-Candidate-Logout';
    
        // Data to be sent in the POST request
        $post_data = array(
            'candidate_id' => $candidate_id,
            'token' => $unique_token,
        );

        $response = getResponseApi($api_url,$post_data);

        if($response->rcode == 200) { //Success
            $this->session->sess_destroy();
        }
        else { //Error
            $this->session->set_flashdata('error', $response->message);
        }
        
		redirect('candidate-login');
	}
	
	
	public function dashboard()
    {
        $isSessionAlive = $this->session->userdata('is_candidate_logged_in');
		if(!$isSessionAlive){
			redirect('candidate-login');	
		}

        if($this->session->userdata('profile_updated') == 0) {
            redirect('candidate-profile');
        }

        $candidate_id = $this->session->userdata('candidate_id');
        $unique_token = $this->session->userdata('unique_token');
        
        // API endpoint URL
        $api_url = base_url().'Api-Dashboard';
    
        // Data to be sent in the POST request
        $post_data = array(
            'candidate_id' => $candidate_id,
            'token' => $unique_token,
        );

        $response = getResponseApi($api_url,$post_data);

        $arrCandidateDetails = array();
        $arrBatchDetails = array();

        if($response->rcode == 200) { //Success
            $arrCandidateDetails = json_decode(json_encode($response->candidate_details), true);
            $arrBatchDetails = json_decode(json_encode($response->batch_details), true);
        }
        else { //Error
            $this->session->set_flashdata('error', $response->message);
        }

        $data['arrCandidateDetails'] = $arrCandidateDetails;
        $data['arrBatchDetails'] = $arrBatchDetails;

        /*echo "<pre>";
	    print_r($data);
	    echo "</pre>";
	    exit;*/
        
        $this->load->view('candidate/template/header');
        $this->load->view('candidate/template/sidebar');
        $this->load->view('candidate/candidate-dashboard',$data);
        $this->load->view('candidate/template/footer');
    }
	
	public function updateprofile()
    {
        $isSessionAlive = $this->session->userdata('is_candidate_logged_in');
		if(!$isSessionAlive){
			redirect('candidate-login');	
		}

        $candidate_id = $this->session->userdata('candidate_id');
        $unique_token = $this->session->userdata('unique_token');
        
        // API endpoint URL
        $api_url = base_url().'Api-View-Candidate-Profile';
    
        // Data to be sent in the POST request
        $post_data = array(
            'candidate_id' => $candidate_id,
            'token' => $unique_token,
        );

        $response = getResponseApi($api_url,$post_data);

        $arrCandidateDetails = array();

        if($response->rcode == 200) { //Success
            $arrCandidateDetails = json_decode(json_encode($response->user_details[0]), true);
        }
        else { //Error
            $this->session->set_flashdata('error', $response->message);
        }

        $data['arrCandidateDetails'] = $arrCandidateDetails;

        $condition = "status = 1";
        $data['arr_state'] = $this->Mdmaster->getAllRecords('tbl_states',$condition,'state_name','ASC');
        
        $condition = "status = 1";
        $data['arr_district'] = $this->Mdmaster->getAllRecords('tbl_districts',$condition,'dist_name','ASC');
        /*echo "<pre>";
	    print_r($data);
	    echo "</pre>";
	    exit;*/
        
        $this->load->view('candidate/template/header');
        $this->load->view('candidate/template/sidebar');
        $this->load->view('candidate/candidate-profile',$data);
        $this->load->view('candidate/template/footer');
    }

    public function saveprofile()
    {
        $isSessionAlive = $this->session->userdata('is_candidate_logged_in');
		if(!$isSessionAlive){
			redirect('candidate-login');	
		}

        $candidate_id = $this->session->userdata('candidate_id');
        $unique_token = $this->session->userdata('unique_token');
        $decode_candidate_id = id_decode($candidate_id);
        $lat = $this->input->post('lat');
        $long = $this->input->post('long');
        $photoVal = str_replace('thumbs/','',$this->input->post('hdn_student_photo'));

        if (isset($_FILES['student_photo']) && $_FILES['student_photo']['name'] != '') {
            $file_ext = pathinfo($_FILES["student_photo"]["name"], PATHINFO_EXTENSION);
            if($this->input->post('hdn_student_photo') != "") {
                $file = $this->config->item('student_photo_path').$photoVal;
                $file_old_ext = ".".pathinfo($photoVal, PATHINFO_EXTENSION);
                $watermark_file = str_replace($file_old_ext,"-watermark".$file_old_ext,$file);
                
                // Check if the file exists before attempting to delete it
                if (file_exists($file)) {
                    // Attempt to delete the file
                    unlink($file);
                    unlink($watermark_file);
                } 
            }

            $enrollmentNumber = $this->session->userdata('enrollment_number');
            $candiateName = trim($this->input->post('name'));
            $dateTime = date('d-m-Y h:i A')." GMT +05:30";
            
            //Create WaterMark
            $watermarkValue = $enrollmentNumber."\n".$candiateName."\n".$lat.",".$long."\n".$dateTime.""; 
            
            $updData['student_photo'] = uploadImage('student_photo', 'student_photo', $decode_candidate_id.'-student-photo-'.date('dmYHis') . '.'.$file_ext,$watermarkValue);

            $photoVal = $updData['student_photo'];
        }
        if($this->input->post('student_name') != "" && $this->input->post('student_mobile') != "" && $this->input->post('gender') != "" && $this->input->post('father_name') != ""
                    && $this->input->post('dob') != "" && $this->input->post('aadhar_number') != "" && $this->input->post('address') != "" && $this->input->post('city') != ""
                    && $this->input->post('state_id') != "" && $this->input->post('district_id') != "" && $this->input->post('pincode') != "" 
                    && $photoVal != "" && $this->input->post('lat') != "" && $this->input->post('long') != ""){

            //Update Candidate
            $updData['student_name'] = $this->input->post('student_name');
            $updData['student_email'] = $this->input->post('student_email');
            $updData['student_mobile'] = $this->input->post('student_mobile');
            $updData['gender'] = $this->input->post('gender');
            $updData['father_name'] = $this->input->post('father_name');
            $updData['dob'] = $this->input->post('dob');
            $updData['aadhar_number'] = $this->input->post('aadhar_number');
            $updData['address'] = $this->input->post('address');
            $updData['city'] = $this->input->post('city');
            $updData['state_id'] = $this->input->post('state_id');
            $updData['district_id'] = $this->input->post('district_id');
            $updData['pincode'] = $this->input->post('pincode');
            //$updData['aadhar_front_filename'] = $this->input->post('aadhar_front_filename');
            //$updData['aadhar_back_filename'] = $this->input->post('aadhar_back_filename');
            $updData['lat'] = $this->input->post('lat');
            $updData['lng'] = $this->input->post('long');
            $updData['geo_address'] = $this->input->post('geo_address');
            $updData['profile_updated'] = 1;

            $this->db->where('student_id', $decode_candidate_id); 
            $this->db->update('tbl_students', $updData);
            //echo "<br> str ".$this->db->last_query();exit;
            
            $this->session->set_flashdata('msg', 'Profile updated successfully');
            $this->session->set_userdata('profile_updated',1);
            redirect('candidate-dashboard');
        }
        else {
            $this->session->set_flashdata('error', 'Please enter mandatory fields');
            redirect('candidate-profile');
        }
    }
}
