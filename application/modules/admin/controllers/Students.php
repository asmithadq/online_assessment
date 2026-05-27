<?php defined('BASEPATH') or exit('No direct script access allowed');

class Students extends MY_Controller
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

        $isSessionAlive=$this->session->userdata('is_logged_in');
		if(!$isSessionAlive || $isSessionAlive==NULL){
			redirect('admin-login');
		}

        $this->load->model('Mdmaster');
        $this->load->model('student_model');
        $this->load->model('mainModel');
        $this->load->model('batch_model');
    }
    
    public function view_batch_students($tb_id)
    {
        $condition = "tb_id = ".$tb_id;
        $arr_batch_details = $this->Mdmaster->getAllRecords('tbl_training_batches',$condition,'tb_id','ASC');

        $data['title'] = 'Students Listing for Batch Id - '.$arr_batch_details[0]['batch_id'];  // Set the title here
        $data['tb_id'] = $tb_id;  // Set the title here
        $data['batch_id'] = $arr_batch_details[0]['batch_id'];
      
        $this->render_page('admin/batch/view-batch-students',$data);   
    }

    function getLists(){
        $data = $row = array();
        $tb_id = $this->input->post('tb_id');
        // Fetch student's records
        $studentsData = $this->student_model->getRows($_POST);

        //Get Candidate Snapshots
        $arr_candidate_snapshot_details = array();
        $arr_candidate_snapshot_list = $this->student_model->getStudentSnapshotsCount($tb_id);
        if($arr_candidate_snapshot_list != false) {
            foreach($arr_candidate_snapshot_list as $snapshot_data) {
                $arr_candidate_snapshot_details[$snapshot_data['student_id']] = $snapshot_data['total_snapshots'];
            }
        }
        /*echo "<pre>";
        print_r($arr_candidate_snapshot_details);
        echo "</pre>";
        exit;*/
        
        $i = $_POST['start'];
        foreach($studentsData as $student){
            $i++;
            
            $checkbox = '<div class="form-check custom-checkbox ms-2"><input type="checkbox" class="form-check-input chk_students" id="customCheckBox2" name="chk_student_id[]" value="'.$student['student_id'].'" required=""><label class="form-check-label" for="customCheckBox2"></label></div>';
            $student_photo = ($student['student_photo'] != "") ? base_url().$this->config->item('student_photo_path').$student['student_photo'] : base_url().'assets/admin/images/profile/small/pic8.jpg';
            $student_photo_image = '<img class="avatar avatar-lg" width="35" src="'.$student_photo.'" alt="'.$student['student_name'].'">';

            $watermark_file = str_replace(".jpeg","-watermark.jpeg",$student['student_photo']);
            $student_watermark_photo = ($student['student_photo'] != "") ? base_url().$this->config->item('student_photo_path').$watermark_file : base_url().'assets/admin/images/profile/small/pic8.jpg';
            
            $profile_rejection_comment =  ($student['profile_verification_status'] == 'Rejected') ? $student['profile_rejection_comment'] : "NA";

            $student_enrollment_number = $student['enrollment_number'];
            $dob = (!empty($student['dob']) && strtotime($student['dob']) !== false) ? date('d-m-Y', strtotime($student['dob'])) : "";

            if($student['profile_updated'] == 1 || $student['aadhar_front_filename'] != "" || $student['aadhar_back_filename'] != "") {
            $student_enrollment_number = '<button type="button" class="badge badge-rounded badge-info" id="btn-'.$student['student_id'].'" onclick="getAadharDetails('.$student['student_id'].');" 
                                        data-enrollment_number="'.$student['enrollment_number'].'" data-student_name="'.$student['student_name'].'" data-father_name="'.$student['father_name'].'" 
                                        data-gender="'.$student['gender'].'" data-dob="'.$dob.'" data-student_photo="'.$student_photo.'" data-address="'.$student['address'].'"  
                                        data-city="'.$student['city'].'" data-pincode="'.$student['pincode'].'" data-dist_name="'.$student['dist_name'].'" data-state_name="'.$student['state_name'].'"  
                                        data-profile_verification_status="'.$student['profile_verification_status'].'" data-profile_rejection_comment="'.$profile_rejection_comment.'" data-aadhar_number="'.$student['aadhar_number'].'" 
                                        data-aadhar_front_filename="'.$student['aadhar_front_filename'].'" data-aadhar_back_filename="'.$student['aadhar_back_filename'].'" data-student_photo_with_aadhar="'.$student['student_photo_with_aadhar'].'">
                                        <i class="fa-solid fa-eye me-2"></i>'.$student['enrollment_number'].'</button>
                                        <span id="spin_'.$student['student_id'].'" style="display:none;" class="fa-stack fa-lg"><i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span>';
            }
                
            $viewAadhaarDetails = $student['aadhar_number'];

            if($student['aadhar_front_filename'] != "" || $student['aadhar_back_filename'] != "") { 
                $viewAadhaarDetails = '<button type="button" class="badge badge-rounded badge-info" id="btn-'.$student['student_id'].'" onclick="getAadharDetails('.$student['student_id'].');" 
                                        data-enrollment_number="'.$student['enrollment_number'].'" data-student_name="'.$student['student_name'].'" data-father_name="'.$student['father_name'].'" 
                                        data-gender="'.$student['gender'].'" data-dob="'.$dob.'" data-student_photo="'.$student_photo.'" data-address="'.$student['address'].'"  
                                        data-city="'.$student['city'].'" data-pincode="'.$student['pincode'].'" data-dist_name="'.$student['dist_name'].'" data-state_name="'.$student['state_name'].'"  
                                        data-profile_verification_status="'.$student['profile_verification_status'].'" data-profile_rejection_comment="'.$profile_rejection_comment.'" data-aadhar_number="'.$student['aadhar_number'].'" 
                                        data-aadhar_front_filename="'.$student['aadhar_front_filename'].'" data-aadhar_back_filename="'.$student['aadhar_back_filename'].'">
                                        <i class="fa-solid fa-eye me-2"></i>'.$student['aadhar_number'].'</button>
                                        <span id="spin_'.$student['student_id'].'" style="display:none;" class="fa-stack fa-lg"><i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span>';
            }

            if($student['profile_verification_status'] == 'Pending') {
                $profile_verification_status_css = 'warning';
            }
            if($student['profile_verification_status'] == 'Verified') {
                $profile_verification_status_css = 'success';
            }
            if($student['profile_verification_status'] == 'Rejected') {
                $profile_verification_status_css = 'danger';
            }
            $profile_verification_status = '<span class="badge light badge-'.$profile_verification_status_css.'"><i class="fa fa-circle text-'.$profile_verification_status_css.' me-1"></i>'.$student['profile_verification_status'].'</span>';

            $student_attendance_css = ($student['student_attendance'] == 'Present') ? 'success' : 'danger';
            $student_attendance = '<button type="button" class="badge light badge-'.$student_attendance_css.'"><i class="fa fa-circle text-'.$student_attendance_css.' me-1"></i>'.$student['student_attendance'].'</button>';
			
            if($student['student_assessment_status'] == 'Pending') {
                $student_assessment_status_css = 'warning';
            }
            if($student['student_assessment_status'] == 'Practical Activity') {
                $student_assessment_status_css = 'info';
            }
            if($student['student_assessment_status'] == 'Viva') {
                $student_assessment_status_css = 'primary';
            }
            if($student['student_assessment_status'] == 'Completed') {
                $student_assessment_status_css = 'success';
            }

            $snapshots_count = (array_key_exists($student['student_id'],$arr_candidate_snapshot_details)) ? $arr_candidate_snapshot_details[$student['student_id']] : 0;
            $snapshot_url = ($snapshots_count > 0) ? base_url().'view-candidate-snapshots/'.id_encode($student['student_id']) : 'javascript:void(0)';
            
            //$student_assessment_status = 'Assessment Status:<span class="badge light badge-'.$student_assessment_status_css.'"><i class="fa fa-circle text-'.$student_assessment_status_css.' me-1"></i>'.$student['student_assessment_status'].'</span>';

            $theory_questions = ($student['theory_questions'] != '') ? explode(",",$student['theory_questions']) : array();
            $practical_activity_questions = ($student['practical_activity_questions'] != '') ? explode(",",$student['practical_activity_questions']) : array();
            $viva_questions = ($student['viva_questions'] != '') ? explode(",",$student['viva_questions']) : array();
            
            $student_assessment_status = '<div class="bootstrap-badge">';
                                        if(count($theory_questions) > 0) {
                                            $student_assessment_status .= '<a href="'.base_url().'view-candidate-assessment-page/'.id_encode($student['student_id']).'/theory" class="badge badge-rounded badge-primary badge-sm">Theory('.count($theory_questions).')</a>';    
                                        }
                                        if(count($practical_activity_questions) > 0) {        
                                            $student_assessment_status .= '&nbsp;<a href="'.base_url().'view-candidate-assessment-page/'.id_encode($student['student_id']).'/practical_activity" class="badge badge-rounded badge-secondary badge-sm"">PracticalActivity('.count($practical_activity_questions).')</a>';
                                        }
                                        if(count($viva_questions) > 0) {          
                                            $student_assessment_status .= '&nbsp;<a href="'.base_url().'view-candidate-assessment-page/'.id_encode($student['student_id']).'/viva" class="badge badge-rounded badge-light" badge-sm">Viva('.count($viva_questions).')</a>';
                                        }
                                        $student_assessment_status .= '&nbsp;<a href="'.$snapshot_url.'" class="badge badge-rounded badge-info" badge-sm">Snapshots('.$snapshots_count.')</a>
                                          </div>';
            /*if($i == 1) {                           
            $student_assessment_status .= '<div class="bootstrap-badge">
                                                <div id="lightgallery" class="row">';
                                                    if($student['student_photo_with_aadhar'] != "") {
                                                        $student_photo_with_aadhar = ($student['student_photo_with_aadhar'] != "") ? base_url().$this->config->item('aadhaar_filename_path').$student['student_photo_with_aadhar'] : base_url().'assets/admin/images/profile/small/pic8.jpg';
                                                        //$student_assessment_status .='<img class="avatar avatar-lg" width="35" src="'.$student_photo_with_aadhar.'" alt="'.$student['student_name'].'">';
                                                            
                                                        $student_assessment_status .='<a href="'.$student_photo_with_aadhar.'" data-exthumbimage="'.$student_photo_with_aadhar.'" data-src="'.$student_photo_with_aadhar.'" class="lg-item">
                                                                                        <img class="avatar avatar-lg" width="35" src="'.$student_photo_with_aadhar.'" alt="'.$student['student_name'].'">
                                                                                    </a>';
                                                        $student_assessment_status .='<a href="'.$student_photo_with_aadhar.'" data-exthumbimage="'.$student_photo_with_aadhar.'" data-src="'.$student_photo_with_aadhar.'" class="lg-item">
                                                                                    <img class="avatar avatar-lg" width="35" src="'.$student_photo_with_aadhar.'" alt="'.$student['student_name'].'">
                                                                                </a>'; 
                                                        $student_assessment_status .='<a href="'.$student_photo_with_aadhar.'" data-exthumbimage="'.$student_photo_with_aadhar.'" data-src="'.$student_photo_with_aadhar.'" class="lg-item">
                                                                                <img class="avatar avatar-lg" width="35" src="'.$student_photo_with_aadhar.'" alt="'.$student['student_name'].'">
                                                                            </a>';                                                    
                                                    }
            $student_assessment_status .= '     </div>
                                            </div>'; 
            }   */                                                        
            
            $assessment_status = ($student['student_attendance'] != 'Absent') ? $student['student_assessment_status'] : 'Absent';                            

            $student_assessment_status .= '<ul class="list-group list-group-flush">
                                            <li class="list-group-item assessment-info">';
            
            $student_assessment_status .= '<span class="me-3 activity">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 17 17">
                                                    <circle cx="8.5" cy="8.5" r="8.5" fill="#f93a0b"></circle>
                                                </svg>
                                            </span>
                                            Assessment Status: '.$assessment_status.'<br>';
            
            if($student['logged_in_dts'] != '') {
                $student_assessment_status .= '<span class="me-3 activity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 17 17">
                                                        <circle cx="8.5" cy="8.5" r="8.5" fill="#22bc32"></circle>
                                                    </svg>
                                                </span> 
                                                Logged in at: '.date('d-m-Y H:i:s',strtotime($student['logged_in_dts'])).'<br>';
            }
            if($student['theory_start_dts'] != '') {
                $student_assessment_status .= '<span class="me-3 activity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 17 17">
                                                        <circle cx="8.5" cy="8.5" r="8.5" fill="#ffe70c"></circle>
                                                    </svg>
                                                </span>
                                                Theory Started: '.date('d-m-Y H:i:s',strtotime($student['theory_start_dts'])).'<br>';
            }
            if($student['theory_submission_dts'] != '') {
                $student_assessment_status .= '<span class="me-3 activity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 17 17">
                                                        <circle cx="8.5" cy="8.5" r="8.5" fill="#9933cb"></circle>
                                                    </svg>
                                                </span>
                                                Theory Submitted: '.date('d-m-Y H:i:s',strtotime($student['theory_submission_dts'])).'<br>';
            }
            if($student['practicalactivity_submission_dts'] != '') {
                $student_assessment_status .= '<span class="me-3 activity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 17 17">
                                                        <circle cx="8.5" cy="8.5" r="8.5" fill="#9933cb"></circle>
                                                    </svg>
                                                </span>
                                                Practical Activity Submitted: '.date('d-m-Y H:i:s',strtotime($student['practicalactivity_submission_dts'])).'<br>';
            }
            if($student['viva_submission_dts'] != '') {
                $student_assessment_status .= '<span class="me-3 activity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 17 17">
                                                        <circle cx="8.5" cy="8.5" r="8.5" fill="#9933cb"></circle>
                                                    </svg>
                                                </span>
                                                Viva Submitted: '.date('d-m-Y H:i:s',strtotime($student['viva_submission_dts']));
            }
                        
            $data[] = array($checkbox,$student_photo_image,$student_enrollment_number,$student['student_name'],$student['password'],$profile_verification_status,$student_attendance,$student_assessment_status);
        }
        
        /*echo "<pre>";
        print_r($tradesData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->student_model->countAll(),
            "recordsFiltered" => $this->student_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }

    public function updateStudentProfileVerificationStatus() {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $updStatus['profile_verification_status'] = $this->input->post('profile_verification_status');
        $updStatus['profile_rejection_comment'] = ($this->input->post('profile_verification_status') == 'Rejected') ? $this->input->post('profile_rejection_comment') : "";

        if(array_key_exists('chk_student_id',$this->input->post())) { //Mass Update
            $arrStudentIds = $this->input->post('chk_student_id');

            $this->db->where_in('student_id', array_values($arrStudentIds));
            $query = $this->db->update('tbl_students', $updStatus);
            //echo "<br> str ".$this->db->last_query();exit;

            $type = 'success';
        }
        else {
            if($this->input->post('student_id') > 0 && $this->input->post('profile_verification_status') != "") {
                $this->db->where('student_id', $this->input->post('student_id'));
                $query = $this->db->update('tbl_students', $updStatus);

                $type = 'success';
            }
            else {
                $type = 'error';
            }
        }
            
        $data['type'] = $type;
        
        echo json_encode($data);
    }

    public function updateStudentDeleteStatus() {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        //$updStatus['status'] = 0;
        
        if(array_key_exists('chk_student_id',$this->input->post())) { //Mass Update
            $arrStudentIds = $this->input->post('chk_student_id');
            $tb_id = $this->input->post('tb_id');

            $this->db->where_in('student_id', array_values($arrStudentIds));
            $this->db->delete('tbl_students');
            //$query = $this->db->update('tbl_students', $updStatus);
            //echo "<br> str ".$this->db->last_query();exit;
            
            //Delete from tbl_theory_answers,tbl_viva_answers,tbl_practical_activity_answers
            $this->db->where_in('student_id', array_values($arrStudentIds));
            $this->db->delete('tbl_theory_answers');
            
            $this->db->where_in('student_id', array_values($arrStudentIds));
            $this->db->delete('tbl_practical_activity_answers');
            
            $this->db->where_in('student_id', array_values($arrStudentIds));
            $this->db->delete('tbl_viva_answers');
            
            //Check whether all students are deleted, if yes then make the batch qp_generated_status = 0
            $totalBatchStudents = $this->batch_model->getBatchTotalStudentsCount($tb_id);
            if($totalBatchStudents == 0) {
                $updBatchStatus['qp_generated_status'] = 0;
                $this->db->where('tb_id', $tb_id);
                $query = $this->db->update('tbl_training_batches', $updBatchStatus);
            }
            
            $type = 'success';
        }
        else {
            $type = 'error';
        }
            
        $data['type'] = $type;
        
        echo json_encode($data);
    }

    public function updateStudentAttendanceStatus() {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $updStatus['student_attendance'] = $this->input->post('student_attendance');
        if($this->input->post('student_attendance') == 'Absent') {
            $updStatus['theory_marks'] = 0;
            $updStatus['practical_skill_marks'] = 0;
            $updStatus['total_theory_marks'] = 0;
            $updStatus['practical_activity_marks'] = 0;
            $updStatus['viva_marks'] = 0;
            $updStatus['total_practical_marks'] = 0;
            $updStatus['total_marks'] = 0;
            $updStatus['marks_percentage'] = 0;
            $updStatus['pass_percentage'] = 0;
            $updStatus['result'] = 'Absent';
        }
        
        if(array_key_exists('chk_student_id',$this->input->post())) { //Mass Update
            $arrStudentIds = $this->input->post('chk_student_id');

            $this->db->where_in('student_id', array_values($arrStudentIds));
            $query = $this->db->update('tbl_students', $updStatus);
            //echo "<br> str ".$this->db->last_query();exit;

            $type = 'success';
        }
        else {
            $type = 'error';
        }
            
        $data['type'] = $type;
        
        echo json_encode($data);
    }

    public function viewCandidateAssessmentPage($student_id_encode,$exam_type)
    {
        $student_id = id_decode($student_id_encode);
        
        $condition = "student_id = ".$student_id;
        $arr_student_details = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
        $arr_student_list = $arr_student_details[0];
        /*echo "<pre>";
        print_r($arr_student_list);
        echo "</pre>";
        exit;*/

        $tb_id =  $arr_student_list['tb_id'];
        $qp_generated_status =  $arr_student_list['qp_generated_status'];
        $theory_questions =  $arr_student_list['theory_questions'];
        $practical_activity_questions =  $arr_student_list['practical_activity_questions'];
        $viva_questions =  $arr_student_list['viva_questions'];
        
        $arr_batch_details = array();
        $exam_duration_secs = 0;
        $arr_batch_list = $this->mainModel->getBatchDetails($tb_id);
        $lid = 0;

        if($arr_batch_list != 'failure') {
            $currentDateTime = strtotime(date('Y-m-d H:i:s'));
            $assessmentStartDateTime = strtotime($arr_batch_list[0]['tb_start_date_time']);
            $assessmentEndDateTime = strtotime($arr_batch_list[0]['tb_end_date_time']);

            $arr_batch_details['trade_name'] = $arr_batch_list[0]['trade_code']." - ".$arr_batch_list[0]['trade_name'];
            $arr_batch_details['batch_id'] = $arr_batch_list[0]['batch_id'];
            $arr_batch_details['scheme'] = $arr_batch_list[0]['scheme_name'].'('.$arr_batch_list[0]['subscheme_name'].')';
            $arr_batch_details['assessment_date'] = date('d-m-Y H:i:s',$assessmentStartDateTime)." To ".date('d-m-Y H:i:s',$assessmentEndDateTime);
            $arr_batch_details['exam_duration_mins'] = convertMinutesToHoursAndMinutes($arr_batch_list[0]['exam_duration_mins']);
            $arr_batch_details['lid'] = $arr_batch_list[0]['lid'];
            $lid = ($arr_batch_list[0]['lid'] > 0) ? $arr_batch_list[0]['lid'] : 0;
        }    

        $question_list = array();
        $question_images_path = base_url().$this->config->item('question_images_path');
        $arrLangQuestions = array();
                
        if($qp_generated_status == 1) {
            if($exam_type == 'theory' && $theory_questions != "") {
                $arrQuestionIds = explode(",",$theory_questions);
            }
            else if($exam_type == 'practical_activity' && $practical_activity_questions != "") {
                $arrQuestionIds = explode(",",$practical_activity_questions);
            }
            else if($exam_type == 'viva' && $viva_questions != "") {
                $arrQuestionIds = explode(",",$viva_questions);
            }

            if($lid > 0) {
                $arrGetQuestionLanguageDetails = $this->mainModel->getCandidateLanguageQuestionDetails($arrQuestionIds,$lid);
                //echo "<br> str ".$this->db->last_query();exit;
                if($arrGetQuestionLanguageDetails != false) {
                    foreach($arrGetQuestionLanguageDetails as $qnLangData) {
                        $arrLangQuestions[$qnLangData['qid']]['question'] = $qnLangData['lang_question'];
                        $arrLangQuestions[$qnLangData['qid']]['option_a'] = $qnLangData['lang_option_a'];
                        $arrLangQuestions[$qnLangData['qid']]['option_b'] = $qnLangData['lang_option_b'];
                        $arrLangQuestions[$qnLangData['qid']]['option_c'] = $qnLangData['lang_option_c'];
                        $arrLangQuestions[$qnLangData['qid']]['option_d'] = $qnLangData['lang_option_d'];
                    }    
                }   
            }
            
            //Get the questions list and details
            $arrGetQuestionDetails = $this->mainModel->getCandidateQuestionDetails($arrQuestionIds,$student_id,0,$exam_type);
            //echo "<br> str ".$this->db->last_query();exit;

            if($arrGetQuestionDetails != false) {
                foreach($arrGetQuestionDetails as $qnData) {
                    if(strtoupper($qnData['option_c']) == "NA") {
                        $qnData['option_c'] = "";
                    }
                    if(strtoupper($qnData['option_d']) == "NA") {
                        $qnData['option_d'] = "";
                    }

                    $question = $qnData['question'];
                    $option_a = $qnData['option_a'];
                    $option_b = $qnData['option_b'];
                    $option_c = $qnData['option_c'];
                    $option_d = $qnData['option_d'];

                    if($lid > 0) {
                        if(strtoupper($qnData['option_c']) == "NA") {
                           $arrLangOptions[$qnData['qid']]['option_c'] = "";
                        }
                        if(strtoupper($qnData['option_d']) == "NA") {
                            $arrLangOptions[$qnData['qid']]['option_d'] = "";
                        }

                        $question = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['question']."|lang|".$arrLangQuestions[$qnData['qid']]['question'] : $qnData['question'];
                        $option_a = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_a']."|lang|".$arrLangQuestions[$qnData['qid']]['option_a'] : $qnData['option_a'];
                        $option_b = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_b']."|lang|".$arrLangQuestions[$qnData['qid']]['option_b'] : $qnData['option_b'];
                        $option_c = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_c']."|lang|".$arrLangQuestions[$qnData['qid']]['option_c'] : $qnData['option_c'];
                        $option_d = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_d']."|lang|".$arrLangQuestions[$qnData['qid']]['option_d'] : $qnData['option_d'];
                    }
                    
                    // Use regular expression to capture text between |% and %|
                    if (preg_match_all('/\|%([^%]+)%\|/', $question, $qmatches)) {
                        // Extract captured text
                        $arrImgNameQn = $qmatches[1];
                        if(count($arrImgNameQn) > 0) {
                            foreach($arrImgNameQn as $imgNameQn) {
                                $imgUrlQn = "<img src='".$question_images_path.$imgNameQn."'>";
                                $question = str_replace('|%'.$imgNameQn.'%|',$imgUrlQn,$question);
                            }
                        }
                    } 

                    if (preg_match_all('/\|%([^%]+)%\|/', $option_a, $matches)) {
                        // Extract captured text
                        $arrImgName = $matches[1];
                        /*echo "<pre>";
                        print_r($arrImgName);
                        echo "</pre>";*/
                        
                        if(count($arrImgName) > 0) {
                            foreach($arrImgName as $imgName) {
                                $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                $option_a = str_replace('|%'.$imgName.'%|',$imgUrl,$option_a);
                            }
                        }
                    }

                    if (preg_match_all('/\|%([^%]+)%\|/', $option_b, $matches)) {
                        // Extract captured text
                        $arrImgName = $matches[1];
                        /*echo "<pre>";
                        print_r($arrImgName);
                        echo "</pre>";*/
                        
                        if(count($arrImgName) > 0) {
                            foreach($arrImgName as $imgName) {
                                $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                $option_b = str_replace('|%'.$imgName.'%|',$imgUrl,$option_b);
                            }
                        }
                    }

                    if (preg_match_all('/\|%([^%]+)%\|/', $option_c, $matches)) {
                        // Extract captured text
                        $arrImgName = $matches[1];
                        /*echo "<pre>";
                        print_r($arrImgName);
                        echo "</pre>";*/
                        
                        if(count($arrImgName) > 0) {
                            foreach($arrImgName as $imgName) {
                                $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                $option_c = str_replace('|%'.$imgName.'%|',$imgUrl,$option_c);
                            }
                        }
                    }

                    if (preg_match_all('/\|%([^%]+)%\|/', $option_d, $matches)) {
                        // Extract captured text
                        $arrImgName = $matches[1];
                        /*echo "<pre>";
                        print_r($arrImgName);
                        echo "</pre>";*/
                        
                        if(count($arrImgName) > 0) {
                            foreach($arrImgName as $imgName) {
                                $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                $option_d = str_replace('|%'.$imgName.'%|',$imgUrl,$option_d);
                            }
                        }
                    }

                    $arr_question_list['qid'] = $qnData['qid'];
                    $arr_question_list['nos_title'] = $qnData['nos_title'];
                    $arr_question_list['exam_name'] = $qnData['exam_name'];
                    $arr_question_list['question'] = $question;
                    $arr_question_list['option_a'] = $option_a;
                    $arr_question_list['option_b'] = $option_b;
                    $arr_question_list['option_c'] = $option_c;
                    $arr_question_list['option_d'] = $option_d;
                    
                    array_push($question_list,$arr_question_list);
                }  
            }          
        }    
        $data['arrQuestionDetails'] = $question_list;
        $data['arr_batch_details'] = $arr_batch_details;
        $data['tb_id'] = $tb_id;
        $data['exam_type'] = $exam_type;
        $data['arr_student_list'] = $arr_student_list;

        //echo "<pre>";
        //print_r($post_data);
	    //print_r($question_list);
	    //echo "</pre>";
	    //exit;
      
        $this->render_page('admin/batch/view-candidate-assessment-page',$data);
    }

    public function viewCandidateSnapshots($student_id_encode)
    {
        $student_id = id_decode($student_id_encode);
        
        $condition = "student_id = ".$student_id;
        $arr_student_details = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
        $arr_student_list = $arr_student_details[0];

        $condition = "student_id = ".$student_id;
        $arr_snapshot_details = $this->Mdmaster->getAllRecords('tbl_student_snapshots',$condition,'ss_id','ASC');

        /*echo "<pre>";
        print_r($arr_student_list);
        echo "</pre>";
        exit;*/

        $data['arr_student_list'] = $arr_student_list;
        $data['arr_snapshot_details'] = $arr_snapshot_details;
      
        $this->render_page('admin/batch/view-candidate-snapshots',$data);
    }

    public function resetExam() {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $exam_type = $this->input->post('exam_type');

        if(array_key_exists('chk_student_id',$this->input->post())) { //Mass Update
            $arrStudentIds = $this->input->post('chk_student_id');

            if($exam_type == "all") {
                # Theory
                $updTheoryData['ans'] = ""; 
                $updTheoryData['save_type'] = "NV"; 
                $updTheoryData['time_left_secs'] = 0;
                $updTheoryData['modified_dts'] = ""; 

                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_theory_answers', $updTheoryData);

                # Practical Activity
                $updPracticalActivityData['marks'] = ""; 
                $updPracticalActivityData['video_file'] = ""; 
                $updPracticalActivityData['descriptive'] = "";
                
                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_practical_activity_answers', $updPracticalActivityData);

                # Viva
                $updVivaData['marks'] = ""; 
                $updVivaData['video_file'] = ""; 
                $updVivaData['descriptive'] = "";
                
                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_viva_answers', $updVivaData);

                # Students
                $updData['student_assessment_status'] = 'Pending';
                $updData['theory_start_dts'] = NULL;
                $updData['theory_submission_dts'] = NULL;
                $updData['practicalactivity_submission_dts'] = NULL;
                $updData['viva_submission_dts'] = NULL;
                $updData['practicalactivity_video_file'] = ''; 
                $updData['viva_video_file'] = ''; 
                $updData['time_left_secs'] = 0; 

                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_students', $updData);

            }
            else if($exam_type == "theory") {
                # Theory
                $updTheoryData['ans'] = ""; 
                $updTheoryData['save_type'] = "NV"; 
                $updTheoryData['time_left_secs'] = 0;
                $updTheoryData['modified_dts'] = ""; 

                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_theory_answers', $updTheoryData);

                # Students
                $updData['student_assessment_status'] = 'Pending';
                $updData['theory_start_dts'] = NULL;
                $updData['theory_submission_dts'] = NULL;
                $updData['time_left_secs'] = 0; 

                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_students', $updData);

            }
            else if($exam_type == "practical_activity") {
                $updPracticalActivityData['marks'] = ""; 
                $updPracticalActivityData['video_file'] = ""; 
                $updPracticalActivityData['descriptive'] = "";
                
                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_practical_activity_answers', $updPracticalActivityData);

                # Students
                $updData['student_assessment_status'] = "Practical Activity";
                $updData['practicalactivity_submission_dts'] = NULL;

                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_students', $updData);

            }
            else if($exam_type == "viva") {
                $updVivaData['marks'] = ""; 
                $updVivaData['video_file'] = ""; 
                $updVivaData['descriptive'] = "";
                
                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_viva_answers', $updVivaData);

                # Students
                $updData['student_assessment_status'] = "Viva";
                $updData['viva_submission_dts'] = NULL;

                $this->db->where_in('student_id', array_values($arrStudentIds));
                $query = $this->db->update('tbl_students', $updData);
                //echo "<br> str ".$this->db->last_query();exit;
            }
            
            $type = 'success';
        }
        else {
            $type = 'error';
        }
            
        $data['type'] = $type;
        
        echo json_encode($data);
    }
    public function updateStudentDeviceLoginStatus() {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $updStatus['device_id'] = "";
        $updStatus['logged_in_status'] = 0;
        
        if(array_key_exists('chk_student_id',$this->input->post())) { //Mass Update
            $arrStudentIds = $this->input->post('chk_student_id');

            $this->db->where_in('student_id', array_values($arrStudentIds));
            $query = $this->db->update('tbl_students', $updStatus);
            //echo "<br> str ".$this->db->last_query();exit;

            $type = 'success';
        }
        else {
            $type = 'error';
        }
            
        $data['type'] = $type;
        
        echo json_encode($data);
    }
}