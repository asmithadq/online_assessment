<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . '/vendor/autoload.php';

class QuestionPaper extends MY_Controller
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
        $this->load->model('student_model');
        $this->load->model('trades_model');
        $this->load->model('questions_model');
        $this->load->model('Mdmaster');        

        $isSessionAlive = $this->session->userdata('is_logged_in');
		/*if(!$isSessionAlive){
			redirect('admin-login');	
		}*/
    }
	
	public function GenerateQuestionPaperPDF($tb_id_encode)
    {
		$tb_id = id_decode($tb_id_encode);
        $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);

        $batch_id     = $arr_batch_details[0]['batch_id'];
        $trade_id     = $arr_batch_details[0]['trade_id'];
        $qp_shuffling = $arr_batch_details[0]['qp_shuffling'];
        $qp_generated_status = $arr_batch_details[0]['qp_generated_status'];
        $arr_optional_exam_type = explode(",",$arr_batch_details[0]['optional_exam_type']);
        $lid = ($arr_batch_details[0]['lid'] > 0) ? $arr_batch_details[0]['lid'] : 0;

        if($qp_generated_status == 1) {
            $condition = "tb_id = ".$tb_id;
            $arr_batch_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
            //echo "<br> str ".$this->db->last_query();exit;
    
            $arr_qn_type_details = array();
            $arr_qn_type_nos_details = array();
            $arrLangQuestions = array();
            $question_images_path = base_url().$this->config->item('question_images_path');
            $arr_qn_type_marks = array();
            $arr_qns_details = array();
    
            if($arr_batch_students != false) {
                $student_count = 0;
                foreach($arr_batch_students as $students) {
                    $student_id = $students['student_id'];
                    if($student_count < count($arr_batch_students)) {
                        $str_question_ids = $students['theory_questions']; //Qns theory + practical skill
                    
                        if(in_array('practicalActivity',$arr_optional_exam_type)) { //Fetch practicalActivity details
                            $str_question_ids .= ",".$students['practical_activity_questions'];
                            $show_practical_activity = 1;
                        }
                        if(in_array('viva',$arr_optional_exam_type)) { //Fetch viva details
                            $str_question_ids .= ",".$students['viva_questions'];
                            $show_viva = 1;
                        }
        
                        $arr_question_ids = explode(",",$str_question_ids);
                        if(count($arr_question_ids) > 0) { //Get details for the questions
                            if($lid > 0) {
                                $arrGetQuestionLanguageDetails = $this->questions_model->getCandidateLanguageQuestionDetails($arr_question_ids,$lid);
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
        
                            $arr_question_details = $this->student_model->getQuestionDetails($arr_question_ids);
                            //echo "<br> str ".$this->db->last_query();exit;
                            if($arr_question_details != false) {
                                foreach($arr_question_details as $qn_data) {
                                    $nos_id = $qn_data['nos_id'];
                                    $qid = $qn_data['qid'];
                                    $marks = $qn_data['marks'];
                                    $question_type = ($qn_data['question_type'] == 'PracticalSkill' || $qn_data['question_type'] == 'Theory') ? 'Theory' : $qn_data['question_type'];
                                    
                                    $arr_qn_type_details[$question_type][$qid] = $qid;
                                    $arr_qn_type_marks[$question_type] = (!array_key_exists($question_type,$arr_qn_type_marks)) ? $marks : ($arr_qn_type_marks[$question_type] + $marks);
        
                                    if(strtoupper($qn_data['option_c']) == "NA") {
                                        $qn_data['option_c'] = "";
                                    }
                                    if(strtoupper($qn_data['option_d']) == "NA") {
                                        $qn_data['option_d'] = "";
                                    }
        
                                    $question = $qn_data['question'];
                                    $option_a = $qn_data['option_a'];
                                    $option_b = $qn_data['option_b'];
                                    $option_c = $qn_data['option_c'];
                                    $option_d = $qn_data['option_d'];
        
                                    if($lid > 0) {
                                        if(strtoupper($qn_data['option_c']) == "NA") {
                                           $arrLangOptions[$qn_data['qid']]['option_c'] = "";
                                        }
                                        if(strtoupper($qn_data['option_d']) == "NA") {
                                            $arrLangOptions[$qn_data['qid']]['option_d'] = "";
                                        }
                
                                        $question = (array_key_exists($qn_data['qid'],$arrLangQuestions)) ? $qn_data['question']."|lang|".$arrLangQuestions[$qn_data['qid']]['question'] : $qn_data['question'];
                                        $option_a = (array_key_exists($qn_data['qid'],$arrLangQuestions)) ? $qn_data['option_a']."|lang|".$arrLangQuestions[$qn_data['qid']]['option_a'] : $qn_data['option_a'];
                                        $option_b = (array_key_exists($qn_data['qid'],$arrLangQuestions)) ? $qn_data['option_b']."|lang|".$arrLangQuestions[$qn_data['qid']]['option_b'] : $qn_data['option_b'];
                                        $option_c = (array_key_exists($qn_data['qid'],$arrLangQuestions)) ? $qn_data['option_c']."|lang|".$arrLangQuestions[$qn_data['qid']]['option_c'] : $qn_data['option_c'];
                                        $option_d = (array_key_exists($qn_data['qid'],$arrLangQuestions)) ? $qn_data['option_d']."|lang|".$arrLangQuestions[$qn_data['qid']]['option_d'] : $qn_data['option_d'];
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
                                    
                                    $arr_qns_details[$student_id][$qid]['question'] 		= $question;
                                    $arr_qns_details[$student_id][$qid]['option_a'] 		= $option_a;
                                    $arr_qns_details[$student_id][$qid]['option_b'] 		= $option_b;
                                    $arr_qns_details[$student_id][$qid]['option_c'] 		= $option_c;
                                    $arr_qns_details[$student_id][$qid]['option_d'] 		= $option_d;
                                    $arr_qns_details[$student_id][$qid]['marks'] 		    = $qn_data['marks'];
                                }
                            }
                        }
                        
                        if($qp_shuffling == 'Same') { //Same qns with same order will be saved in to all students of the batch
                            $student_count = count($arr_batch_students); //Break the for loop as the questions sequence will be same for all candidates
                        }
                        else {
                            $student_count++;
                        }
                    }
                }
            }
    
            //echo "<pre>";
            //print_r($arr_optional_exam_type);
            //print_r($arr_qn_type_details);
            //print_r($arr_qns_details);
            //echo "</pre>";
            //exit;
    
            $data["arr_batch_details"] = $arr_batch_details;
            //$data["arr_trade_nos_details"] = $arr_trade_nos_details;	
            $data['arr_qn_type_details'] = $arr_qn_type_details;
            $data['arr_qns_details'] = $arr_qns_details;
            $data['qp_shuffling'] = $qp_shuffling;
            $data['arr_qn_type_marks'] = $arr_qn_type_marks;
            $data['arr_batch_students'] = $arr_batch_students;
            
    		$html = "";
            //$html = $this->load->view('admin/batch/question_paper_header',$data,TRUE);
            $html .= $this->load->view('admin/question_paper/print_question_paper',$data,TRUE);
            //echo $html;exit;
    
            //$mpdf = new \Mpdf\Mpdf();
            $mpdf = new \Mpdf\Mpdf(['default_font' => 'serif','tempDir' => '/var/log/temp']);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
    
            $mpdf->WriteHTML($html);
            // Download PDF file
            $mpdf->Output($batch_id.'_question_paper.pdf', 'I');
        }
        else {
            echo "Questions not yet generated.Please contact Technical Team";
        }
    }

    public function GenerateAnswerKeyPDF($tb_id_encode)
    {
		$tb_id = id_decode($tb_id_encode);
        $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);

        $batch_id     = $arr_batch_details[0]['batch_id'];
        $trade_id     = $arr_batch_details[0]['trade_id'];
        $qp_shuffling = $arr_batch_details[0]['qp_shuffling'];
        $qp_generated_status = $arr_batch_details[0]['qp_generated_status'];
        $arr_optional_exam_type = explode(",",$arr_batch_details[0]['optional_exam_type']);
        $lid = ($arr_batch_details[0]['lid'] > 0) ? $arr_batch_details[0]['lid'] : 0;

        if($qp_generated_status == 1) {
            $condition = "tb_id = ".$tb_id;
            $arr_batch_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
            //echo "<br> str ".$this->db->last_query();exit;
    
            $arr_qn_type_details = array();
            $arr_qn_type_nos_details = array();
            $arrLangQuestions = array();
            $question_images_path = base_url().$this->config->item('question_images_path');
            $arr_qn_type_marks = array();
            $arr_qns_details = array();
    
            if($arr_batch_students != false) {
                $student_count = 0;
                foreach($arr_batch_students as $students) {
                    $student_id = $students['student_id'];
                    if($student_count < count($arr_batch_students)) {
                        $str_question_ids = $students['theory_questions']; //Qns theory + practical skill
                        
                        $arr_question_ids = explode(",",$str_question_ids);
                        if(count($arr_question_ids) > 0) { //Get details for the questions
                            $arr_question_details = $this->student_model->getQuestionDetails($arr_question_ids);
                            //echo "<br> str ".$this->db->last_query();exit;
                            if($arr_question_details != false) {
                                foreach($arr_question_details as $qn_data) {
                                    $nos_id = $qn_data['nos_id'];
                                    $qid = $qn_data['qid'];
                                    $marks = $qn_data['marks'];
                                    $question_type = ($qn_data['question_type'] == 'PracticalSkill' || $qn_data['question_type'] == 'Theory') ? 'Theory' : $qn_data['question_type'];
                                    
                                    $arr_qn_type_details[$question_type][$qid] = $qid;
                                    $arr_qn_type_marks[$question_type] = (!array_key_exists($question_type,$arr_qn_type_marks)) ? $marks : ($arr_qn_type_marks[$question_type] + $marks);
        
                                    $question = $qn_data['question'];
                                    $correct_ans = strtoupper($qn_data['correct_ans']);
                                    
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
                                    $arr_qns_details[$student_id][$qid]['question'] 		= $question;
                                    $arr_qns_details[$student_id][$qid]['correct_ans'] 		= $correct_ans;
                                    $arr_qns_details[$student_id][$qid]['marks'] 		    = $qn_data['marks'];
                                }
                            }
                        }
                        
                        if($qp_shuffling == 'Same') { //Same qns with same order will be saved in to all students of the batch
                            $student_count = count($arr_batch_students); //Break the for loop as the questions sequence will be same for all candidates
                        }
                        else {
                            $student_count++;
                        }
                    }
                }
            }
    
            //echo "<pre>";
            //print_r($arr_optional_exam_type);
            //print_r($arr_qn_type_details);
            //print_r($arr_qns_details); 
            //echo "</pre>";
            //exit;
    
            $data["arr_batch_details"] = $arr_batch_details;
            //$data["arr_trade_nos_details"] = $arr_trade_nos_details;	
            $data['arr_qn_type_details'] = $arr_qn_type_details;
            $data['arr_qns_details'] = $arr_qns_details;
            $data['qp_shuffling'] = $qp_shuffling;
            $data['arr_qn_type_marks'] = $arr_qn_type_marks;
            $data['arr_batch_students'] = $arr_batch_students;
            
    		$html = "";
            //$html = $this->load->view('admin/batch/question_paper_header',$data,TRUE);
            $html .= $this->load->view('admin/question_paper/print_answer_key',$data,TRUE);
            //echo $html;exit;
    
            //$mpdf = new \Mpdf\Mpdf();
            $mpdf = new \Mpdf\Mpdf(['default_font' => 'serif','tempDir' => '/var/log/temp']);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
    
            $mpdf->WriteHTML($html);
            // Download PDF file
            $mpdf->Output($batch_id.'_answer_key.pdf', 'I');
        }
        else {
            echo "Questions not yet generated.Please contact Technical Team";
        }
    }
	
}
