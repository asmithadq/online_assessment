<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');

class Summary extends MY_Controller
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
        $this->load->model('batch_model');
        $this->load->model('trades_model');

        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			redirect('admin-login');	
		}
    }
	
	public function GenerateBatchBasicSummaryPDF($tb_id_encode)
    {
		$tb_id = id_decode($tb_id_encode);
        $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
        if($arr_batch_details != false) {
            $tb_id = $arr_batch_details[0]['tb_id'];
			$batch_id = $arr_batch_details[0]['batch_id'];
			$trade_id = $arr_batch_details[0]['trade_id'];
			$arr_optional_exam_type = explode(",",$arr_batch_details[0]['optional_exam_type']);
            $qp_shuffling = $arr_batch_details[0]['qp_shuffling'];
			
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

					$arr_nos_wise_user_score[$nos_id]['theory'] = 0;
					$arr_nos_wise_user_score[$nos_id]['practical_activity'] = 0;
					$arr_nos_wise_user_score[$nos_id]['viva'] = 0;
                }
            }

			/*echo "<pre>";
			print_r($arr_trade_nos_details);
			echo "</pre>";
			exit;*/

            $arr_student_details = array();

            if($qp_shuffling == 'Same') {
                $arrGetStudentTheoryAnswerDetails = $this->batch_model->getBatchStudentTheoryDetails($tb_id);
                //echo "<br> str ".$this->db->last_query();exit;
                if($arrGetStudentTheoryAnswerDetails != false) {
                    foreach($arrGetStudentTheoryAnswerDetails as $qnData) {
                        $student_id = $qnData['student_id'];
                        $qn_id = $qnData['qid'];
                        $nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
                        $question_type = $arr_qn_nos_ids[$qn_id]['question_type'];
                        $correct_ans = strtolower($arr_qns_details[$nos_id][$qn_id]['correct_ans']);
                        $user_ans = strtolower($qnData['ans']);
                        $marks = $arr_qns_details[$nos_id][$qn_id]['marks'];

                        $arr_student_details[$student_id]['enrollment_number'] = $qnData['enrollment_number'];
                        $arr_student_details[$student_id]['student_name'] = $qnData['student_name'];
                        $arr_student_details[$student_id]['father_name'] = $qnData['father_name'];
                        $arr_student_details[$student_id]['dob'] = $qnData['dob'];
                        $arr_student_details[$student_id]['student_photo_path'] = $qnData['student_photo_path'];
                        $arr_student_details[$student_id]['aadhar_number'] = $qnData['aadhar_number'];
                        $arr_student_details[$student_id]['aadhar_front_filename'] = $qnData['aadhar_front_filename'];
                        $arr_student_details[$student_id]['aadhar_back_filename'] = $qnData['aadhar_back_filename'];
                        $arr_student_details[$student_id]['address'] = $qnData['address'];
                        $arr_student_details[$student_id]['lat'] = $qnData['lat'];
                        $arr_student_details[$student_id]['lng'] = $qnData['lng'];
                         
    
                        $arr_theory_answers_list[$qn_id]['question_type']  = $question_type;
                        $arr_theory_answers_list[$qn_id]['correct_ans']    = $correct_ans;
                        $arr_theory_answers_list[$qn_id]['ans']      		= $user_ans;
                        $arr_theory_answers_list[$qn_id]['save_type']    	= $qnData['save_type'];
                        $arr_theory_answers_list[$qn_id]['marks']      		= $marks;
                        
    
                        if($user_ans == $correct_ans) {
                            $arr_nos_wise_user_score[$nos_id]['theory'] += $marks;
                        }
                    }
                }
            }
			
			
        }

        $data["arr_batch_details"] = $arr_batch_details;	
        $data["arr_student_details"] = $arr_student_details;	
        
		
       // $html = $this->load->view('admin/summary/summary_header',$data,TRUE);
        /*$html = $this->load->view('admin/summary/print_basic_batch_summary',$data,TRUE);
        echo $html;exit;

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        // Download PDF file
        $mpdf->Output($batch_id.'_basic_summary.pdf', 'D');*/

        $this->render_page('admin/results/view-batch-result-summary',$data);
    }

    
	
}
