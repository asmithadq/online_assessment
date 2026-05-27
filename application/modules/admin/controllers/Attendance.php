<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');

class Attendance extends MY_Controller
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

        /*$isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			redirect('admin-login');	
		}*/
    }
	
	public function GenerateAttendancePDF($tb_id_encode)
    {
		$tb_id = id_decode($tb_id_encode);
        $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
        $data["arr_batch_details"] = $arr_batch_details;	
        $batch_id = $arr_batch_details[0]['batch_id'];
		
        // Load your student attendance data from the database
        $arr_students = $this->attendance_model->get_students_details_for_batch($tb_id);
        $data['arr_students'] = $arr_students;
        
        $html = $this->load->view('admin/batch/attendance_header',$data,TRUE);
        $html .= $this->load->view('admin/batch/print_attendance_sheet',$data,TRUE);
        //echo $html;exit;

        $mpdf = new \Mpdf\Mpdf([
            'margin_top' => 5,    // Adjust the value as needed
            'margin_bottom' => 5, // Adjust the value as needed
            'tempDir' => '/var/log/temp',
         ]);
        
        $mpdf->WriteHTML($html);
        // Download PDF file
        $mpdf->Output($batch_id.'_attendance_sheet.pdf', 'I');
    }
	
}
