<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');

class Omr extends MY_Controller
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
        $this->load->model('attendance_model');
        $this->load->model('batch_model');
        $this->load->model('student_model');

        /*$isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			redirect('admin-login');	
		}*/
    }
	
	public function GenerateOmrPDF($tb_id_encode)
    {
		$tb_id = id_decode($tb_id_encode);
        $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
        $data["arr_batch_details"] = $arr_batch_details[0];	
        $batch_id = $arr_batch_details[0]['batch_id'];
        /*echo "<pre>";
        print_r($data["arr_batch_details"]);
        echo "</pre>";*/
        //exit;
        $arr_batch_student_details = $this->student_model->getBatchStudentDetails($tb_id);
        if($arr_batch_student_details != false) {
            $data["arr_batch_student_details"] = $arr_batch_student_details;	
            
            $html = $this->load->view('admin/batch/print_omr_sheet',$data,TRUE);
            //echo $html;exit;
    
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => '/var/log/temp',
             ]);
            $mpdf->WriteHTML($html);
            // Download PDF file
            $mpdf->Output($batch_id.'_OMRSheet.pdf', 'I');
        }
	}
	
}
