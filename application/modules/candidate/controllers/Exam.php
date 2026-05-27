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

class Exam extends MX_Controller
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
       
        $isSessionAlive = $this->session->userdata('is_candidate_logged_in');
		if(!$isSessionAlive){
			redirect('candidate-login');	
		}
        
        $this->load->model('Mdmaster');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    
	public function candidateassessment()
    {
        // Set caching headers
        $this->output->set_header('Cache-Control: no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        
        $isSessionAlive = $this->session->userdata('is_candidate_logged_in');
		if(!$isSessionAlive){
			redirect('candidate-login');	
		}

        $candidate_id = $this->session->userdata('candidate_id');
        $unique_token = $this->session->userdata('unique_token');
        
        // API endpoint URL
        $api_url = base_url().'Api-Questions-List';
    
        // Data to be sent in the POST request
        $post_data = array(
            'candidate_id' => $candidate_id,
            'token' => $unique_token,
        );

        $response = getResponseApi($api_url,$post_data);

        $arrQuestionDetails = array();
        $arrBatchDetails = array();
        $exam_duration_secs = 0;

        if($response->rcode == 200) { //Success
            $arrQuestionDetails = json_decode(json_encode($response->question_list), true);
            $arrBatchDetails = json_decode(json_encode($response->batch_details), true);
            $exam_duration_secs = json_decode(json_encode($response->exam_duration_secs), true);
        }
        else { //Error
            $this->session->set_flashdata('error', $response->message);
        }

        $data['arrQuestionDetails'] = $arrQuestionDetails;
        $data['arrBatchDetails'] = $arrBatchDetails;
        $data['exam_duration_secs'] = $exam_duration_secs;

        /*echo "<pre>";
        print_r($post_data);
	    print_r($arrQuestionDetails);
	    echo "</pre>";
	    exit;*/
        
        $this->load->view('candidate/template/header');
        $this->load->view('candidate/template/sidebar');
        $this->load->view('candidate/assessment-page',$data);
        $this->load->view('candidate/template/footer');
    }

    public function calculateProgressPercentage($answeredQuestions, $totalQuestions) {
        if ($totalQuestions == 0) {
            return 0; // to avoid division by zero
        }
        return ($answeredQuestions / $totalQuestions) * 100;
    }
}
