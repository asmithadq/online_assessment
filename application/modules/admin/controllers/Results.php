<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; 
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Results extends MY_Controller
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

        $this->load->model('Mdmaster');
		$this->load->model('Results_model');
		$this->load->model('batch_model');
		$this->load->model('student_model');
		$this->load->model('trades_model');
		 
        $this->require_module_permission('batch_results');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function search_results($tb_id_encode)
    {
        $this->require_permission('view_batch_results');

		$data['title'] = 'Search Results';
		
		$condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
		$batch_id = ($tb_id_encode != 'All') ? id_decode($tb_id_encode) : "";
		$data['batch_id'] = ($batch_id > 0) ? $batch_id : "";
       
        $this->render_page('admin/results/search-results',$data);
    }
    
    public function view_batch_result($tb_id)
    {
        $this->require_permission('view_batch_results');
		
		$data['title'] = 'Search Results';
		
		$condition = "status = 1";
        $data['tb_id'] = $tb_id;
       
        $this->render_page('admin/results/batch-result',$data);
    }
	
	function getLists(){
        $data = $row = array();
		$max_marks_theory = 0;
		$max_marks_practical = 0;
		$total_passed = 0;
		$total_failed = 0;
		$total_absent = 0;
        
        // Fetch member's records
        $resultsData = $this->Results_model->getRows($_POST);	
		
		$trade_id = $resultsData[0]['trade_id'];
		$batch_id = $resultsData[0]['batch_id'];
		$condition = "trade_id = ".$trade_id;
        $arr_trade = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_id','ASC');
        if($arr_trade != false) {
            $pass_percentage = $arr_trade[0]['pass_percentage'];
            $total_max_marks = $arr_trade[0]['total_marks'];

			$condition = "trade_id = ".$trade_id;
			$arr_trade_nos = $this->Mdmaster->getAllRecords('tbl_map_trade_nos',$condition,'trade_id','ASC');
			if($arr_trade_nos != false) {
				foreach($arr_trade_nos as $trade_nos) {
					$theory_marks = $trade_nos['theory_marks'];
					$practical_skill_marks = $trade_nos['practical_skill_marks'];
					$practical_marks = $trade_nos['practical_marks'];
					$viva_marks = $trade_nos['viva_marks'];

					$max_marks_theory += $theory_marks;
					$max_marks_practical += ($practical_skill_marks + $practical_marks + $viva_marks);
				}
				
			}
        }
	      
        $i = $_POST['start'];
        foreach($resultsData as $student){
            $i++;
			
            $action = '<div class="d-flex">';
			if($student['student_attendance'] != "Absent") {				 
    			$action .= '<a href="'.base_url().'view-result-summary/'.id_encode($student['student_id']).'/view" class="badge badge-info"><i class="fa-solid fa-eye"></i> View Summary</a>
    			&nbsp;<a href="'.base_url().'moderate-results/'.id_encode($student['student_id']).'"  class="badge badge-danger"><i class="fa-solid fa-database"></i> Result Moderation</a>';
			}	
			$action .= '</div>';
						
			$res = '';
			if($student['result'] == "Fail")
			{
				$res = '<span class="badge badge-danger">Fail</span>';
				$total_failed++;
			}
			else if($student['result'] == "Pass") {
				$res = '<span class="badge badge-success">Pass</span>';
				$total_passed++;
			}				
        	else if($student['student_attendance'] == "Absent") {
				$res = '<span class="badge badge-secondary">Absent</span>';
				$total_absent++;
			}
			
            $data[] = array($i, $student['enrollment_number'], $student['student_name'],$max_marks_theory,$max_marks_practical,$student['total_theory_marks'],$student['total_practical_marks'],$student['marks_percentage'],$res,$action);
        }
        
        /*echo "<pre>";
        print_r($assessorsData);
        echo "</pre>";
        exit;*/
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Results_model->countAll(),
            "recordsFiltered" => $this->Results_model->countFiltered($_POST),
            "data" => $data,
			"pass_percentage" => $pass_percentage,
			"total_max_marks" => $total_max_marks,
			"total_students" => count($resultsData),
			"total_passed" => $total_passed,
			"total_failed" => $total_failed,
			"total_absent" => $total_absent,
			"tb_id_encode" => id_encode($this->input->post('batch_id')),
			"batch_id" => $batch_id,
        );
        
        // Output to JSON format
        echo json_encode($output);
    }
	
	public function view_result_summary($student_id_encode,$type)
    {
        $student_id = id_decode($student_id_encode);
		$arr_qn_nos_ids = array();
		$arr_qns_details = array();
		$arr_qn_type_details = array();
		$arr_trade_nos_details = array();
		$arr_nos_wise_user_score = array();
		$arr_theory_answers_list = array();
		$arr_practical_activity_answers_list = array();
		$arr_viva_answers_list = array();
		
		$arr_student_details = $this->student_model->getStudentCompleteDetails($student_id);
		if($arr_student_details != false) {
			$tb_id = $arr_student_details[0]['tb_id'];
			$batch_id = $arr_student_details[0]['batch_id'];
			$trade_id = $arr_student_details[0]['trade_id'];
			$enrollment_number = $arr_student_details[0]['enrollment_number'];
			$theory_answers_record_generated = $arr_student_details[0]['viva_answers_record_generated'];
			$str_question_ids = $arr_student_details[0]['theory_questions']; //Qns theory + practical skill
			$show_practical_activity = 0;
			$show_viva = 0;
			
			//Get Mapped Trade Nos Details
			$arr_trade_details = $this->trades_model->getTradeDetails($trade_id);
			if($arr_trade_details != false) {
                foreach($arr_trade_details as $key => $details) {
                    $nos_id = $details['nos_id'];
                    
                    $arr_trade_nos_details[$nos_id]['nos_code'] = $details['nos_code'];
					$arr_trade_nos_details[$nos_id]['nos_title'] = $details['nos_title'];
					$arr_trade_nos_details[$nos_id]['theory_marks'] = $details['theory_marks'];
                    $arr_trade_nos_details[$nos_id]['practical_skill_marks'] = $details['practical_skill_marks'];
                    $arr_trade_nos_details[$nos_id]['practical_marks'] = $details['practical_marks'];
                    $arr_trade_nos_details[$nos_id]['viva_marks'] = $details['viva_marks'];
                    $arr_trade_nos_details[$nos_id]['total_nos_marks'] = $details['total_nos_marks'];

					$arr_nos_wise_user_score[$nos_id]['Theory'] = 0;
					$arr_nos_wise_user_score[$nos_id]['PracticalSkill'] = 0;
					$arr_nos_wise_user_score[$nos_id]['PracticalActivity'] = 0;
					$arr_nos_wise_user_score[$nos_id]['Viva'] = 0;
                }
            }

			/*echo "<pre>";
			print_r($arr_trade_nos_details);
			echo "</pre>";
			exit;*/
			
			$arr_optional_exam_type = explode(",",$arr_student_details[0]['optional_exam_type']);
			if(in_array('practicalActivity',$arr_optional_exam_type)) { //Fetch practicalActivity details
				$str_question_ids .= ",".$arr_student_details[0]['practical_activity_questions'];
				$practicalactivity_answers_record_generated = $arr_student_details[0]['practicalactivity_answers_record_generated'];
				$show_practical_activity = 1;
			}
			if(in_array('viva',$arr_optional_exam_type)) { //Fetch viva details
				$str_question_ids .= ",".$arr_student_details[0]['viva_questions'];
				$viva_answers_record_generated = $arr_student_details[0]['viva_answers_record_generated'];
				$show_viva = 1;
			}

			$arr_question_ids = explode(",",$str_question_ids);
			if(count($arr_question_ids) > 0) { //Get Nos details for the questions
				$arr_question_details = $this->student_model->getQuestionDetails($arr_question_ids);
				//echo "<br> str ".$this->db->last_query();
				if($arr_question_details != false) {
					foreach($arr_question_details as $qn_data) {
						$nos_id = $qn_data['nos_id'];
						$qid = $qn_data['qid'];
						$question_type = $qn_data['question_type'];

						$arr_qn_type_details[$nos_id][$question_type][$qid] = $qid;
						$arr_qn_nos_ids[$qid]['nos_id'] = $nos_id;
						$arr_qn_nos_ids[$qid]['question_type'] = $question_type;

						$arr_qns_details[$nos_id][$qid]['question_type']  	= $qn_data['question_type'];
						$arr_qns_details[$nos_id][$qid]['question'] 		= $qn_data['question'];
						$arr_qns_details[$nos_id][$qid]['option_a'] 		= $qn_data['option_a'];
						$arr_qns_details[$nos_id][$qid]['option_b'] 		= $qn_data['option_b'];
						$arr_qns_details[$nos_id][$qid]['option_c'] 		= $qn_data['option_c'];
						$arr_qns_details[$nos_id][$qid]['option_d'] 		= $qn_data['option_d'];
						$arr_qns_details[$nos_id][$qid]['correct_ans'] 		= $qn_data['correct_ans'];
						$arr_qns_details[$nos_id][$qid]['marks'] 			= $qn_data['marks'];
					}
				}
			}
			
			//Get theory answers details
			$arrGetTheoryAnswerDetails = $this->student_model->getCandidateAnswerDetails($tb_id,$student_id,"theory");
			//echo "<br> str ".$this->db->last_query();exit;

			if($arrGetTheoryAnswerDetails != false) {
				foreach($arrGetTheoryAnswerDetails as $qnData) {
					$qn_id = $qnData['qid'];
					$nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
					$question_type = $arr_qn_nos_ids[$qn_id]['question_type'];
					$correct_ans = strtolower($arr_qns_details[$nos_id][$qn_id]['correct_ans']);
					$user_ans = strtolower($qnData['ans']);
					$marks = $arr_qns_details[$nos_id][$qn_id]['marks'];

					$arr_theory_answers_list[$qn_id]['question_type']  = $question_type;
					$arr_theory_answers_list[$qn_id]['correct_ans']    = $correct_ans;
					$arr_theory_answers_list[$qn_id]['ans']      		= $user_ans;
					$arr_theory_answers_list[$qn_id]['save_type']    	= $qnData['save_type'];
					$arr_theory_answers_list[$qn_id]['marks']      		= $marks;
					

					if($user_ans == $correct_ans) {
						$arr_nos_wise_user_score[$nos_id][$question_type] += $marks;
					}
				}
			}

			if($show_practical_activity == 1) {
				//Get practical activity answers details
				$arrGetPracticalActivityAnswerDetails = $this->student_model->getCandidateAnswerDetails($tb_id,$student_id,"practical_activity");
				//echo "<br> str ".$this->db->last_query();exit;

				if($arrGetPracticalActivityAnswerDetails != false) {
					foreach($arrGetPracticalActivityAnswerDetails as $qnData) {
						$qn_id = $qnData['qid'];
						$nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
						$video_file = $qnData['video_file'];
						$descriptive = $qnData['descriptive'];
						$marks = $qnData['marks'];
						$max_marks = $arr_qns_details[$nos_id][$qn_id]['marks'];

						$arr_practical_activity_answers_list[$qn_id]['video_file']    = $video_file;
						$arr_practical_activity_answers_list[$qn_id]['descriptive']   = $descriptive;
						$arr_practical_activity_answers_list[$qn_id]['marks']         = $marks;
						$arr_practical_activity_answers_list[$qn_id]['max_marks']     = $max_marks;

						$arr_nos_wise_user_score[$nos_id]['PracticalActivity'] += $marks;
					}
				}
			}

			if($show_viva == 1) {
				//Get viva answers details
				$arrGetVivaAnswerDetails = $this->student_model->getCandidateAnswerDetails($tb_id,$student_id,"viva");
				//echo "<br> str ".$this->db->last_query();exit;

				if($arrGetVivaAnswerDetails != false) {
					foreach($arrGetVivaAnswerDetails as $qnData) {
						$qn_id = $qnData['qid'];
						$nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
						$video_file = $qnData['video_file'];
						$descriptive = $qnData['descriptive'];
						$marks = $qnData['marks'];
						$max_marks = $arr_qns_details[$nos_id][$qn_id]['marks'];

						$arr_viva_answers_list[$qn_id]['video_file']    = $video_file;
						$arr_viva_answers_list[$qn_id]['descriptive']   = $descriptive;
						$arr_viva_answers_list[$qn_id]['marks']         = $marks;
						$arr_viva_answers_list[$qn_id]['max_marks']     = $max_marks;

						$arr_nos_wise_user_score[$nos_id]['Viva'] += $marks;
					}
				}
			}

			$arr_snapshot_details = $this->student_model->getStudentSnapshots($student_id);

			//echo "<pre>";
			//print_r($arr_qn_type_details);
			//print_r($arr_qns_details);
			//print_r($arr_theory_answers_list);
			//echo "</pre>";
			//exit;
			
			$data['title'] = 'Result Summary';
			$data['arr_student_details'] = $arr_student_details[0];
			$data['arr_qn_type_details'] = $arr_qn_type_details;
			$data['arr_qns_details'] = $arr_qns_details;
			$data['arr_trade_nos_details'] = $arr_trade_nos_details;
			$data['show_practical_activity'] = $show_practical_activity;
			$data['show_viva'] = $show_viva;
			$data['arr_nos_wise_user_score'] = $arr_nos_wise_user_score;	
			$data['arr_theory_answers_list'] = $arr_theory_answers_list;	
			$data['arr_practical_activity_answers_list'] = $arr_practical_activity_answers_list;	
			$data['arr_viva_answers_list'] = $arr_viva_answers_list;	
			$data['arr_qn_nos_ids'] = $arr_qn_nos_ids;
			$data['arr_snapshot_details'] = $arr_snapshot_details;	
			
			if($type == 'basic_pdf' || $type == 'detailed_pdf') {
				$data['type'] = $type;
				$html = $this->load->view('admin/results/print_candidate_summary',$data,TRUE);
        		//echo $html;exit;

				$file_name = ($type == 'basic_pdf') ? "basic" : "detailed"; 
		
				$mpdf = new \Mpdf\Mpdf(['default_font' => 'serif']);
				$mpdf->simpleTables = true;
				$mpdf->autoScriptToLang = true;
				$mpdf->autoLangToFont = true;
				
				
				$mpdf->WriteHTML($html);
				// Download PDF file
				$mpdf->Output($enrollment_number.'_'.$batch_id.'_'.$file_name.'_summary.pdf', 'D');
			}
			else {
				$this->render_page('admin/results/view-candidate-summary',$data);
			}
		}
		else {
			redirect('search-results');
		}
    }

	public function view_batch_result_summary($tb_id_encode,$type)
    {
		ini_set('memory_limit', '-1');
		$tb_id = id_decode($tb_id_encode);
        $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
        if($arr_batch_details != false) {
			$arr_student_details = array();
			$arr_student_snapshot_details = array();
			$arr_qn_type_details = array();
			$arr_nos_wise_user_score = array();
			$show_practical_activity = 0;
			$show_viva = 0;

            $tb_id = $arr_batch_details[0]['tb_id'];
			$batch_id = $arr_batch_details[0]['batch_id'];
			$trade_id = $arr_batch_details[0]['trade_id'];
			$arr_optional_exam_type = explode(",",$arr_batch_details[0]['optional_exam_type']);
            $qp_shuffling = $arr_batch_details[0]['qp_shuffling'];

			if(in_array('practicalActivity',$arr_optional_exam_type)) { //Fetch practicalActivity details
				$show_practical_activity = 1;
			}
			if(in_array('viva',$arr_optional_exam_type)) { //Fetch viva details
				$show_viva = 1;
			}
			
			//Get Mapped Trade Nos Details
			$arr_trade_details = $this->trades_model->getTradeDetails($trade_id);
			if($arr_trade_details != false) {
                foreach($arr_trade_details as $key => $details) {
                    $nos_id = $details['nos_id'];
                    
                    $arr_trade_nos_details[$nos_id]['nos_code'] = $details['nos_code'];
					$arr_trade_nos_details[$nos_id]['nos_title'] = $details['nos_title'];
					$arr_trade_nos_details[$nos_id]['theory_marks'] = $details['theory_marks'];
                    $arr_trade_nos_details[$nos_id]['practical_skill_marks'] = $details['practical_skill_marks'];
                    $arr_trade_nos_details[$nos_id]['practical_marks'] = $details['practical_marks'];
                    $arr_trade_nos_details[$nos_id]['viva_marks'] = $details['viva_marks'];
                    $arr_trade_nos_details[$nos_id]['total_nos_marks'] = $details['total_nos_marks'];

				}
            }

			if($arr_batch_details[0]['tb_exam_type'] == 'online') {
				$arr_snapshot_details = $this->student_model->getBatchStudentSnapshots($tb_id);
				//echo "<br> str ".$this->db->last_query();
				if($arr_snapshot_details != false) {
					foreach($arr_snapshot_details as $snapshotData) {
						$arr_student_snapshot_details[$snapshotData['student_id']][] = $snapshotData['snapshot_image'];
					}	
				}	
			}

			$arrGetStudentTheoryAnswerDetails = $this->batch_model->getBatchStudentTheoryDetails($tb_id);
			//echo "<br> str ".$this->db->last_query();exit;
			if($arrGetStudentTheoryAnswerDetails != false) {
				foreach($arrGetStudentTheoryAnswerDetails as $qnData) {
					$student_id = $qnData['student_id'];
					$qn_id = $qnData['qid'];
					$nos_id = $qnData['nos_id'];
					$question = $qnData['question'];
					$question_type = $qnData['question_type'];
					$correct_ans = strtolower($qnData['correct_ans']);
					$user_ans = strtolower($qnData['ans']);
					$marks = $qnData['marks'];

					$arr_student_details[$student_id]['enrollment_number'] = $qnData['enrollment_number'];
					$arr_student_details[$student_id]['student_name'] = $qnData['student_name'];
					$arr_student_details[$student_id]['father_name'] = $qnData['father_name'];
					$arr_student_details[$student_id]['dob'] = $qnData['dob'];
					$arr_student_details[$student_id]['student_photo'] = $qnData['student_photo'];
					$arr_student_details[$student_id]['aadhar_number'] = $qnData['aadhar_number'];
					$arr_student_details[$student_id]['aadhar_front_filename'] = $qnData['aadhar_front_filename'];
					$arr_student_details[$student_id]['aadhar_back_filename'] = $qnData['aadhar_back_filename'];
					$arr_student_details[$student_id]['address'] = $qnData['address'];
					$arr_student_details[$student_id]['lat'] = $qnData['lat'];
					$arr_student_details[$student_id]['lng'] = $qnData['lng'];
					$arr_student_details[$student_id]['pass_percentage'] = $qnData['pass_percentage'];
					$arr_student_details[$student_id]['marks_percentage'] = $qnData['marks_percentage'];
					$arr_student_details[$student_id]['result'] = $qnData['result'];
					$arr_student_details[$student_id]['theory_questions'] = $qnData['theory_questions'];
					$arr_student_details[$student_id]['practical_activity_questions'] = $qnData['practical_activity_questions'];
					$arr_student_details[$student_id]['viva_questions'] = $qnData['viva_questions'];
					$arr_student_details[$student_id]['theory_submission_dts'] = $qnData['theory_submission_dts'];
					$arr_student_details[$student_id]['practicalactivity_submission_dts'] = $qnData['practicalactivity_submission_dts'];
					$arr_student_details[$student_id]['viva_submission_dts'] = $qnData['viva_submission_dts'];
					$arr_student_details[$student_id]['practicalactivity_video_file'] = $qnData['practicalactivity_video_file'];
					$arr_student_details[$student_id]['viva_video_file'] = $qnData['viva_video_file'];
					
					$arr_qn_type_details[$student_id][$nos_id][$question_type][$qn_id] = $qn_id;
					
					$arr_theory_answers_list[$student_id][$qn_id]['question']  		= $question;
					$arr_theory_answers_list[$student_id][$qn_id]['question_type']  = $question_type;
					$arr_theory_answers_list[$student_id][$qn_id]['correct_ans']    = $correct_ans;
					$arr_theory_answers_list[$student_id][$qn_id]['ans']      		= $user_ans;
					$arr_theory_answers_list[$student_id][$qn_id]['save_type']    	= $qnData['save_type'];
					$arr_theory_answers_list[$student_id][$qn_id]['marks']      	= $marks;

					if($user_ans == $correct_ans) {
						$arr_nos_wise_user_score[$student_id][$nos_id][$question_type][] = $marks;
					}
				}
			}

			if($show_practical_activity == 1) {
				//Get practical activity answers details
				$arrGetPracticalActivityAnswerDetails = $this->batch_model->getBatchStudentAssessmentDetails($tb_id,"practical_activity");
				//echo "<br> str ".$this->db->last_query();exit;

				if($arrGetPracticalActivityAnswerDetails != false) {
					foreach($arrGetPracticalActivityAnswerDetails as $qnData) {
						$student_id = $qnData['student_id'];
						$qn_id = $qnData['qid'];
						$question = $qnData['question'];
						$question_type = $qnData['question_type'];
						$nos_id = $qnData['nos_id'];
						$video_file = $qnData['video_file'];
						$descriptive = $qnData['descriptive'];
						$marks = $qnData['marks'];
						$max_marks = $qnData['max_marks'];

						$arr_qn_type_details[$student_id][$nos_id][$question_type][$qn_id] = $qn_id;

						$arr_practical_activity_answers_list[$student_id][$qn_id]['question']  	   = $question;
						$arr_practical_activity_answers_list[$student_id][$qn_id]['video_file']    = $video_file;
						$arr_practical_activity_answers_list[$student_id][$qn_id]['descriptive']   = $descriptive;
						$arr_practical_activity_answers_list[$student_id][$qn_id]['marks']         = $marks;
						$arr_practical_activity_answers_list[$student_id][$qn_id]['max_marks']     = $max_marks;

						$arr_nos_wise_user_score[$student_id][$nos_id][$question_type][] = $marks;
					}
				}
			}

			if($show_viva == 1) {
				//Get viva answers details
				$arrGetVivaAnswerDetails = $this->batch_model->getBatchStudentAssessmentDetails($tb_id,"viva");
				//echo "<br> str ".$this->db->last_query();exit;

				if($arrGetVivaAnswerDetails != false) {
					foreach($arrGetVivaAnswerDetails as $qnData) {
						$student_id = $qnData['student_id'];
						$qn_id = $qnData['qid'];
						$question = $qnData['question'];
						$question_type = $qnData['question_type'];
						$nos_id = $qnData['nos_id'];
						$video_file = $qnData['video_file'];
						$descriptive = $qnData['descriptive'];
						$marks = $qnData['marks'];
						$max_marks = $qnData['max_marks'];

						$arr_qn_type_details[$student_id][$nos_id][$question_type][$qn_id] = $qn_id;

						$arr_viva_answers_list[$student_id][$qn_id]['question']  	 = $question;
						$arr_viva_answers_list[$student_id][$qn_id]['video_file']    = $video_file;
						$arr_viva_answers_list[$student_id][$qn_id]['descriptive']   = $descriptive;
						$arr_viva_answers_list[$student_id][$qn_id]['marks']         = $marks;
						$arr_viva_answers_list[$student_id][$qn_id]['max_marks']     = $max_marks;

						$arr_nos_wise_user_score[$student_id][$nos_id][$question_type][] = $marks;
					}
				}
			}
		}

		/*echo "<pre>";
		print_r($arr_student_snapshot_details);
		echo "</pre>";
		exit;*/

		$data["arr_batch_details"] = $arr_batch_details[0];	
        $data["arr_student_details"] = $arr_student_details;	
		$data["arr_student_snapshot_details"] = $arr_student_snapshot_details;
        $data['arr_qn_type_details'] = $arr_qn_type_details;
		$data['show_practical_activity'] = $show_practical_activity;
		$data['show_viva'] = $show_viva;
		$data['arr_nos_wise_user_score'] = $arr_nos_wise_user_score;
		$data['arr_trade_nos_details'] = $arr_trade_nos_details;
		$data['arr_theory_answers_list'] = $arr_theory_answers_list;	
		$data['arr_practical_activity_answers_list'] = $arr_practical_activity_answers_list;	
		$data['arr_viva_answers_list'] = $arr_viva_answers_list;
		$data['type'] = $type;
		$data['student_count'] = 1;
		
        if($type == 'basic_pdf' || $type == 'detailed_pdf') {
			
			$mpdf = new \Mpdf\Mpdf(['default_font' => 'serif']);
			$mpdf->autoScriptToLang = true;
			$mpdf->autoLangToFont = true;
				
			if(count($arr_student_details) > 0) {
				$i = 1;
				$html_content = "";
				foreach($arr_student_details as $student_id => $student_data) {  
					//echo "<br> student id ".$student_id;
					//if($i <= 11) {
						$pdfData = array();
						$pdfData["arr_batch_details"] 									= $arr_batch_details[0];	
						$pdfData["arr_student_details"][$student_id] 					= $arr_student_details[$student_id];	
						//$pdfData["arr_student_snapshot_details"][$student_id] 			= (array_key_exists($student_id,$arr_student_snapshot_details)) ? $arr_student_snapshot_details[$student_id] : "";
						$pdfData['arr_qn_type_details'][$student_id] 					= $arr_qn_type_details[$student_id];
						$pdfData['show_practical_activity'] 							= $show_practical_activity;
						$pdfData['show_viva'] 											= $show_viva;
						$pdfData['arr_nos_wise_user_score'][$student_id] 				= $arr_nos_wise_user_score[$student_id];
						$pdfData['arr_trade_nos_details'] 								= $arr_trade_nos_details;
						$pdfData['arr_theory_answers_list'][$student_id] 				= $arr_theory_answers_list[$student_id];	
						$pdfData['arr_practical_activity_answers_list'][$student_id] 	= $arr_practical_activity_answers_list[$student_id];	
						$pdfData['arr_viva_answers_list'][$student_id] 					= $arr_viva_answers_list[$student_id];
						$pdfData['student_count']					 					= $i;
						$pdfData['type'] 												= $type;
						$pdfData['total_students'] 										= count($arr_student_details);
						$pdfData['student_id'] 											= $student_id;


						//echo "<pre>";
						//print_r($arr_student_details[$student_id]);	
						//print_r($pdfData["arr_student_details"]);	
						//echo "</pre>";

						$html_content = $this->load->view('admin/results/print_batch_summary_pdf',$pdfData,TRUE); 
						//echo $html_content;exit;

						$mpdf->WriteHTML($html_content);
						
						$i++;
					//}	
				}
				//echo $html_content;exit;
			}		
			
			/*$html_content = $this->load->view('admin/results/print_batch_summary',$data,TRUE); 
			//echo $html_content;exit;

			
			
			// Define the chunk size (adjust based on your needs)
            $chunk_size = 100000; // 100,000 characters per chunk
            
            // Initialize mPDF
            $mpdf = new \Mpdf\Mpdf();
    
            $mpdf = new \Mpdf\Mpdf(['default_font' => 'serif']);
			$mpdf->autoScriptToLang = true;
			$mpdf->autoLangToFont = true;
			
			// Split the HTML content into chunks
            $offset = 0;
            $html_length = strlen($html_content);
            
            while ($offset < $html_length) {
                $chunk = substr($html_content, $offset, $chunk_size);
                $mpdf->WriteHTML($chunk);
                $offset += $chunk_size;
            }*/

			$file_name = ($type == 'basic_pdf') ? "basic" : "detailed";
			
			// Download PDF file
			$mpdf->Output($batch_id.'_'.$file_name.'_summary.pdf', 'I');
		}
		else {
			$this->render_page('admin/results/view-batch-summary',$data);
		}
	}

	public function GetTradesBySsc() 
	{
        $ssc_id = $this->input->post('ssc_id');
        
        $condition = "status = 1 ";
        if($ssc_id > 0) {
            $condition .= " AND ssc_id = ".$ssc_id;
        }
        $arr_trades = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_code','ASC');
        
        $output = '<option value="">Choose...</option>';
        if($arr_trades != false) {
            foreach($arr_trades as $trades) {
                $output .= '<option value="'.$trades['trade_id'].'" >'.$trades['trade_name'].' ('.$trades['trade_code'].')</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }
	
	public function GetBatchBySscTrade() 
	{
        $ssc_id = $this->input->post('ssc_id');
		$trade_id = $this->input->post('trade_id');
		$hdn_batch_id = $this->input->post('hdn_batch_id');
        
        $condition = "status = 1 AND result_processing = 'Completed' ";
        if($ssc_id > 0) {
            $condition .= " AND ssc_id = ".$ssc_id;
        }
        if($trade_id > 0) {
            $condition .= " AND $trade_id = ".$trade_id;
        }
        
        $arr_batches = $this->Mdmaster->getAllRecords('tbl_training_batches',$condition,'batch_id','ASC');
        
        $output = '<option value="">Choose...</option>';
        if($arr_batches != false) {
            foreach($arr_batches as $batches) {
				$selected = "";
				if($batches['tb_id'] == $hdn_batch_id) {
					$selected = "selected";
				}
                $output .= '<option value="'.$batches['tb_id'].'" '.$selected.'>'.$batches['batch_id'].'</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }
	
	public function GetCandidateByBatch() 
	{
		$batch_id = $this->input->post('batch_id');
        
        $condition = "status = 1 AND tb_id = ".$batch_id;
        $arr_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'enrollment_number','ASC');
        
        $output = '<option value="">Choose...</option>';
        if($arr_students != false) {
            foreach($arr_students as $students) {
                $output .= '<option value="'.$students['student_id'].'" >'.$students['student_name'].' ('.$students['enrollment_number'].')</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }
	
	public function DownloadPercentageSheet($tb_id=0)
	{		
		$headers = array("SDMSEnrolmentNumber",	"StudentName", "Gender", "Mobile", "Email", "MaxMarksTheory", "MaxMarksPractical", "MarksTheory", "MarksPractical", "Result", "MarksInPercentage");
	
		$data = $this->Results_model->getPercentageSheetData($tb_id);
		//echo "<br> str ".$this->db->last_query();exit;
		if($data != false)
		{
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			//$arr_optional_exam_type = explode(",",$data[0]['optional_exam_type']);

			for ($i = 0, $l = sizeof($headers); $i < $l; $i++) {
				$sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
			}
			
			// Set header styles (bold and black font color)
            $headerStyleArray = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF000000'], // Black font color
                ]
            ];

            // Apply header styles
            $sheet->getStyle('A1:K1')->applyFromArray($headerStyleArray);

			for ($i = 0, $l = sizeof($data); $i < $l; $i++) { // row $i
				$j = 0;
				foreach ($data[$i] as $k => $v) { // column $j
					if($k != 'batch_id') {
						$sheet->setCellValueByColumnAndRow($j + 1, ($i + 1 + 1), $v);
						$j++;
					}
				}
			}
			
			// Set column widths for a range of columns (A to C)
            $columns = range('A', 'K'); // Adjust the range as needed
            
            foreach ($columns as $index => $column) {
                $width = ($index == 0 || $index == 1 || $index == 5 || $index == 6 || $index == 10) ? 25 : 15; // Corresponding widths for columns A, B, and C
                $sheet->getColumnDimension($column)->setWidth($width);
            }
			
			$fileName = $data[0]['batch_id']."_Percentage_Result.xlsx";

			$writer = new Xlsx($spreadsheet);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
			$writer->save('php://output');
		}
	}
	
	
	public function DownloadNOSresultSheet($tb_id=0)
	{		
		$res_batch = $this->Results_model->getBatchCompleteDetails($tb_id);
		//echo "<br> str ".$this->db->last_query();exit;
		$arr_students = $this->student_model->getBatchStudentDetails($tb_id);
		$int_student_count = ($arr_students != false) ? count($arr_students) : 0;
		$arr_optional_exam_type = explode(",",$res_batch['optional_exam_type']);

		$arr_theory_details = $this->Results_model->getNosWiseBatchStudentsTheoryDetails($tb_id);
		$arr_nos_marks_details = array();
		
		if($arr_theory_details != false) {
			foreach($arr_theory_details as $theory_data) {
				$nos_id = $theory_data['nos_id'];
				$student_id = $theory_data['student_id'];
				$correct_ans = strtolower($theory_data['correct_ans']);
				$ans = strtolower($theory_data['ans']);
				$question_type = $theory_data['question_type'];

				$marks = ($correct_ans == $ans) ? $theory_data['marks'] : 0;
				
				if($question_type == 'Theory') {
				    $arr_nos_marks_details[$student_id][$nos_id]['theory'] = (array_key_exists($student_id,$arr_nos_marks_details) && array_key_exists($nos_id,$arr_nos_marks_details[$student_id])
																		  && array_key_exists('theory',$arr_nos_marks_details[$student_id][$nos_id])) 
																		  ? ($arr_nos_marks_details[$student_id][$nos_id]['theory'] + $marks) : $marks;
				}
                else if($question_type == 'PracticalSkill') {
				    $arr_nos_marks_details[$student_id][$nos_id]['practical'] = (array_key_exists($student_id,$arr_nos_marks_details) && array_key_exists($nos_id,$arr_nos_marks_details[$student_id])
																		  && array_key_exists('practical',$arr_nos_marks_details[$student_id][$nos_id])) 
																		  ? ($arr_nos_marks_details[$student_id][$nos_id]['practical'] + $marks) : $marks;
				}
				
			}
		}

		if(in_array('practicalActivity',$arr_optional_exam_type)) {
			$arr_practical_activity_details = $this->Results_model->getNosWiseBatchStudentsPracticalActivityDetails($tb_id);

			if($arr_practical_activity_details != false) {
				foreach($arr_practical_activity_details as $practical_activity_data) {
					$nos_id = $practical_activity_data['nos_id'];
					$student_id = $practical_activity_data['student_id'];
					$marks = $practical_activity_data['marks'];
	
					$arr_nos_marks_details[$student_id][$nos_id]['practical'] = (array_key_exists($student_id,$arr_nos_marks_details) && array_key_exists($nos_id,$arr_nos_marks_details[$student_id])
																			  && array_key_exists('practical',$arr_nos_marks_details[$student_id][$nos_id])) 
																			  ? ($arr_nos_marks_details[$student_id][$nos_id]['practical'] + $marks) : $marks;
				}
			}
		}	
		if(in_array('viva',$arr_optional_exam_type)) {
			$arr_viva_details = $this->Results_model->getNosWiseBatchStudentsVivaDetails($tb_id);
			
			if($arr_viva_details != false) {
				foreach($arr_viva_details as $viva_data) {
					$nos_id = $viva_data['nos_id'];
					$student_id = $viva_data['student_id'];
					$marks = $viva_data['marks'];
	
					$arr_nos_marks_details[$student_id][$nos_id]['practical'] = (array_key_exists($student_id,$arr_nos_marks_details) && array_key_exists($nos_id,$arr_nos_marks_details[$student_id])
																			  && array_key_exists('practical',$arr_nos_marks_details[$student_id][$nos_id])) 
																			  ? ($arr_nos_marks_details[$student_id][$nos_id]['practical'] + $marks) : $marks;
				}
			}
		}	

		/*echo "<pre>";
		print_r($arr_nos_marks_details);
		echo "</pre>";
		exit;*/
		
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet(); 
		
		//Headers
		$headerNos = array();
		$headers1 = array("S.No", "Student Unique ID", "Candidate Name", "Gender", "Aadhar Number");
		$headers2 = array();
		$headers3 = array("Total Marks Theory", "Total Marks Practical", "Gross Total", "Percentage", "Final Result");	
		$headerNosId = array();
		
		$res_nos = $this->Results_model->getAllNOS($tb_id);
		if($res_nos != false)
		{
			foreach($res_nos as $nos)
			{
				$noscode = str_replace(array('/N','/'),'',$nos['nos_code']);

				$headerNos[] = $noscode."-".$nos['nos_title'];
				$headers2[] = "Theory Marks";
				$headerNosId[$nos['nos_id']]['theory'] = 'theory';
				
				if(in_array('practicalActivity',$arr_optional_exam_type) || in_array('viva',$arr_optional_exam_type)) { 
					$headerNos[] = $noscode."-".$nos['nos_title'];
					$headers2[] = "Practical Marks";
					$headerNosId[$nos['nos_id']]['practical'] = 'practical';
				}
			}
		}
		/*echo "<pre>";
		print_r($headerNosId);
		echo "</pre>";
		exit;*/

		//$headers = array_merge($headers1, $headers2);
		$headers = array_merge($headers1,$headers2, $headers3);
		
		$int_total_res_count = $int_pass_count = $int_fail_count = $int_absent_count = 0;
		$int_pass_percent = $int_fail_percent = $int_absent_percent = 0;
		foreach($arr_students as $row)
		{
			if($row['result'] == "Pass")
			{
				$int_pass_count += 1;
			}
			if($row['result'] == "Fail")
			{
				$int_fail_count += 1;
			}
			if($row['result'] == "Absent")
			{
				$int_absent_count += 1;
			}
		}
		$int_total_res_count = $int_pass_count + $int_fail_count + $int_absent_count;
		$int_pass_percent = ($int_pass_count / $int_student_count) *100;
		$int_fail_percent = ($int_fail_count / $int_student_count) *100;
		$int_absent_percent = ($int_absent_count / $int_student_count) *100;
		
		$col=1;
		$row=2;		
		$sheet->setCellValueByColumnAndRow($col++, $row, "Name of Assessing Body:");
		$sheet->setCellValueByColumnAndRow($col+1, $row, $res_batch['ag_name']); //$sheet->setCellValueByColumnAndRow($col+1, $row, $res_batch['ag_name']);
		$col=$col+4;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Name of Training Provider:");
		$sheet->setCellValueByColumnAndRow($col+2, $row, $res_batch['tp_name']);
		$col=$col+6;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Result");
		$sheet->setCellValueByColumnAndRow($col++, $row, "Count");
		$sheet->setCellValueByColumnAndRow($col++, $row, "%");
		
		$col=1;$row++;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Scheme Name:");
		$sheet->setCellValueByColumnAndRow($col+1, $row, $res_batch['scheme_name']);
		$col=$col+4;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Name of Training Center:");
		$sheet->setCellValueByColumnAndRow($col+2, $row, $res_batch['tc_name']);
		$col=$col+6;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Pass");
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_pass_count);
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_pass_percent);
		
		$col=1;$row++;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Assessment Date:");
		$sheet->setCellValueByColumnAndRow($col+1, $row, date('d-m-Y',strtotime($res_batch['tb_assessment_date'])));
		$col=$col+4;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Batch Name:");
		$sheet->setCellValueByColumnAndRow($col+2, $row, $res_batch['batch_id']);
		$col=$col+6;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Fail");
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_fail_count);
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_fail_percent);
		
		$col=1;$row++;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Assessor Details:");
		$sheet->setCellValueByColumnAndRow($col+1, $row, $res_batch['assessor_code']."-".$res_batch['assessor_name']);
		$col=$col+4;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Job Role:");
		$sheet->setCellValueByColumnAndRow($col+2, $row, $res_batch['trade_code']."-".$res_batch['trade_name']);
		$col=$col+6;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Absent");
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_absent_count);
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_absent_percent);

		$col=1;$row++;
		$sheet->setCellValueByColumnAndRow($col++, $row, "No. of Candidates:");
		$sheet->setCellValueByColumnAndRow($col+1, $row, $int_student_count);
		
		$col=13;
		$sheet->setCellValueByColumnAndRow($col++, $row, "Total Count");
		$sheet->setCellValueByColumnAndRow($col++, $row, $int_total_res_count);
		
		// Merge cells 
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('A5:B5');
		$sheet->mergeCells('A6:B6');

		$sheet->mergeCells('C2:D2');
        $sheet->mergeCells('C3:D3');
        $sheet->mergeCells('C4:D4');
        $sheet->mergeCells('C5:D5');
		$sheet->mergeCells('C6:D6');

		$sheet->mergeCells('F2:H2');
        $sheet->mergeCells('F3:H3');
        $sheet->mergeCells('F4:H4');
        $sheet->mergeCells('F5:H5');

		$sheet->mergeCells('I2:K2');
        $sheet->mergeCells('I3:K3');
        $sheet->mergeCells('I4:K4');
        $sheet->mergeCells('I5:K5');
		
		$col = 6;
		$row = $row+3;
		
		for ($i = $col,$m=0, $l = sizeof($headerNos); $m < $l; $i++,$m++) {
			$sheet->setCellValueByColumnAndRow($i, $row, $headerNos[$m]);
		}
		$row++;

		$col = 1;
		for ($i = $col,$m=0, $l = sizeof($headers); $m < $l; $i++,$m++) {
			$sheet->setCellValueByColumnAndRow($i, $row, $headers[$m]);
		}
		$row++;
		
		$data = array();
		$res_students = $this->Results_model->getStudentsByBatchId($tb_id);
		foreach($res_students as $key => $stu)
		{
			$student_id = $stu['student_id'];

			$data[$key][] = ($key+1);
			$data[$key][] = $stu['enrollment_number'];
			$data[$key][] = $stu['student_name'];
			$data[$key][] = $stu['gender'];
			$data[$key][] = $stu['aadhar_number'];
			foreach($headerNosId as $nos_id => $arr_data) {
				foreach($arr_data as $exam_type) {
					//echo "<br> student id ".$student_id." Nos Id ".$nos_id." exam type ".$exam_type;
					$data[$key][] = (array_key_exists($nos_id,$arr_nos_marks_details[$student_id]) && array_key_exists($exam_type,$arr_nos_marks_details[$student_id][$nos_id])) ? $arr_nos_marks_details[$student_id][$nos_id][$exam_type] : "";
				}
			}
			$data[$key][] = $stu['total_theory_marks'];
			$data[$key][] = $stu['total_practical_marks'];
			$data[$key][] = $stu['total_marks'];
			$data[$key][] = $stu['marks_percentage'];
			$data[$key][] = $stu['result'];
		}	
		
		for ($i = $row,$m=0, $l = sizeof($data); $m < $l; $i++,$m++) { // row $i
			$j = 0;			
			foreach ($data[$m] as $k => $v) { // column $j
				$startColumn = ($j+1);
				$rowIndex = $i;
				//$sheet->setCellValueByColumnAndRow($startColumn, $rowIndex, $v);
				
				$sheet->setCellValueExplicit(
					Coordinate::stringFromColumnIndex($startColumn) . $rowIndex, $v, DataType::TYPE_STRING // Explicitly treat the value as a string
				);

				$j++;
			}
		}
		
		$lastRowCnt = (sizeof($data) + 10);

		$desiredWidth = 12; // Set your desired width

		$highestColumn = $sheet->getHighestColumn(); // Get the highest column letter, e.g., 'Z'

		foreach (range('A', $highestColumn) as $columnID) {
			$sheet->getColumnDimension($columnID)->setWidth($desiredWidth);
			$sheet->getStyle($columnID)->getAlignment()->setWrapText(true); 
		}

		/*$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);*/
				
		$headerStyle1 = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
				],
			],
		];

		$sheet->getStyle('A2:D6')->applyFromArray($headerStyle1);
		$sheet->getStyle('F2:K6')->applyFromArray($headerStyle1);
		$sheet->getStyle('M2:O6')->applyFromArray($headerStyle1); 
		$sheet->getStyle('A9:'.$highestColumn.$lastRowCnt)->applyFromArray($headerStyle1);
		
		$fileName = $res_batch['batch_id']."_LSC_Result_Format.xlsx";

		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
		header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
		$writer->save('php://output');
		
	}

}
