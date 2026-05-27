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

class Dashboard extends MY_Controller
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
        $this->load->model('Dashboard_model');
        $this->load->model('Mdmaster');

        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			redirect('admin-login');	
		}
    }

    public function index()
    {
        $this->require_permission('view_dashboard');
        
        $this->load->view('admin/template/header');
        $this->load->view('admin/template/sidebar');
        $this->load->view('admin/admin-dashboard');
        $this->load->view('admin/template/footer');
    }
	
	function getUpcomingAssessments()
	{
        $data = $row = array();
        
        // Fetch member's records
        $assessmentsData = $this->Dashboard_model->getRows($_POST);
	      
        $i = $_POST['start'];
        foreach($assessmentsData as $assessments)
		{
            $i++;
			
			$ssc = $assessments['trade_code'] .' ' . $assessments['trade_name']."<br>".$assessments['ssc_code'] .' ' . $assessments['ssc_title'];
            
			if($assessments['tb_assessment_status'] == "Pending"){
				$status = '<span class="badge badge-primary border-0">InProcess</span>';
			}else{
				$status = '<span class="badge badge-success border-0">Completed</span>';
    		}
 	   	
            $data[] = array($i, $assessments['batch_id']."<br>".$assessments['tc_name'], $ssc, $assessments['student_count'], date('d-m-Y H:i:s',strtotime($assessments['tb_start_date_time'])), 
                                date('d-m-Y H:i:s',strtotime($assessments['tb_end_date_time'])), $assessments['assessor_name'], $status);
        }
     
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Dashboard_model->countAll(),
            "recordsFiltered" => $this->Dashboard_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
	
	// Controller method to get count of Partners via AJAX
    public function get_partners_count() {
        $count = $this->Dashboard_model->get_partners_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to get count of Partners via AJAX
    public function get_centers_count() {
        $count = $this->Dashboard_model->get_centers_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
    
	 // Controller method to get count of batches via AJAX
    public function get_batch_count() {
        $count = $this->Dashboard_model->get_batch_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to get count of Assessors via AJAX
    public function get_assessors_count() {
        $count = $this->Dashboard_model->get_assessors_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to get count of Students Assesment Pending via AJAX
    public function get_students_assessment_pending_count() {
        $count = $this->Dashboard_model->get_students_assessment_pending_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to get count of Students Assesment Completed via AJAX
    public function get_students_assessment_completed_count() {
        $count = $this->Dashboard_model->get_students_assessment_completed_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to get count of batches inprocess via AJAX
    public function get_batch_inprocess_count() {
        $count = $this->Dashboard_model->get_batch_inprocess_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to get count of batches completed via AJAX
    public function get_batch_completed_count() {
        $count = $this->Dashboard_model->get_batch_completed_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	//  Controller method to retrieve the count of batch results pending via AJAX
    public function get_batch_results_pending_count() {
        $count = $this->Dashboard_model->get_batch_results_pending_count();
        $data['count'] = $count;
        echo json_encode($data);
    }
	
	// Controller method to retrieve the count of batch results completed via AJAX
    public function get_batch_results_completed_count() {
        $count = $this->Dashboard_model->get_batch_results_completed_count();
        $data['count'] = $count;
        echo json_encode($data);
    }

    public function get_batch_review_count() {
        $start_date = ($this->input->post('start_date') != "") ? $this->input->post('start_date') : date('Y-m-01');
        $end_date = ($this->input->post('end_date') != "") ? $this->input->post('end_date') : date("Y-m-t");

        $batch_count            = $this->Dashboard_model->get_batch_count($start_date,$end_date);
        $batch_inprocess_count  = $this->Dashboard_model->get_batch_inprocess_count($start_date,$end_date);
        $batch_completed_count  = $this->Dashboard_model->get_batch_completed_count($start_date,$end_date);
        $batch_results_pending_count = $this->Dashboard_model->get_batch_results_pending_count($start_date,$end_date);
        $batch_results_completed_count = $this->Dashboard_model->get_batch_results_completed_count($start_date,$end_date);

        $data['batch_count']                    = $batch_count;
        $data['batch_inprocess_count']          = $batch_inprocess_count;
        $data['batch_completed_count']          = $batch_completed_count;
        $data['batch_results_pending_count']    = $batch_results_pending_count;
        $data['batch_results_completed_count']  = $batch_results_completed_count;

        echo json_encode($data);
    }

    
}
