<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');
use Phppot\DataSource;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Batches extends MY_Controller
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
        $this->load->model('Mdmaster');
        $this->load->model('mainModel');
        $this->load->model('partner_model');
        $this->load->model('assessors_model');
        $this->load->model('students_skipped_model');
        $this->load->model('student_model');
        
        $this->require_module_permission(array('batches_inprocess','batches_completed','batch_results'));
    }
    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function inprocess()
    {
        $this->require_permission('view_batches_inprocess');

        $data['title'] = 'Batches Inprocess';  // Set the title here
        $data['type'] = 'Pending';  

        $condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
        
        $this->render_page('admin/batch/list-batches-inprocess',$data);
    }

    public function completed()
    {
        $this->require_permission('view_batches_completed');

        $data['title'] = 'Batches Completed';  // Set the title here
        $data['type'] = 'Completed';  
        
        $this->render_page('admin/batch/list-batches-completed',$data);
    }
    
    function getLists(){
        $data = $row = array();
        $type = $this->input->post('type');
        // Fetch member's records
        $batchesData = $this->batch_model->getRows($_POST);
        
        $arrBatchStudentsCountData = array();
        
        $arrBatchStudentsCount = $this->batch_model->getBatchStudentsCount($this->input->post('type'));
        if($arrBatchStudentsCount != false) {
            foreach($arrBatchStudentsCount as $batchData) {
                $arrBatchStudentsCountData[$batchData['tb_id']] = $batchData['total_students'];
            }
        }
        
        $i = $_POST['start'];
        foreach($batchesData as $batch){
            $batch_id = seo_friendly_url($batch['batch_id']);

            $i++;

            $studentsImportedCount[$batch['tb_id']] = (array_key_exists($batch['tb_id'],$arrBatchStudentsCountData)) ? $arrBatchStudentsCountData[$batch['tb_id']] : 0;
            
            $totalStudents = (array_key_exists($batch['tb_id'],$arrBatchStudentsCountData)) ? '<a href="'.site_url('view-batch-students/'. $batch['tb_id']).'" class="btn btn-success shadow btn-sm sharp me-1">'.$batch['tb_target'].'&nbsp;('.$studentsImportedCount[$batch['tb_id']].')</a>' : '<button type="button" class="btn btn-warning btn-icon-sm">'.$batch['tb_target'].'&nbsp;(0)</button>';
            
            $status = ($batch['status'] == 1) ? '<span class="badge light badge-success border-0">Active</span>' : '<span class="badge light badge-danger border-0">Inactive</span>';

            if($batch['tb_target'] != $studentsImportedCount[$batch['tb_id']]) {
                $qp_generated_status = '<span class="badge badge-secondary">Target Not Met</span>';
            }
            else {
                $qp_generated_status = ($batch['qp_generated_status'] == 0) ? '<button type="button" class="btn btn-danger btn-md" id="btn_'.$batch['tb_id'].'" onclick="generateQuestionBank('.$batch['tb_id'].');"><span class="btn-icon-start text-danger"><i class="fa fa-spinner color-danger"></i>
                </span><span id="spn_'.$batch['tb_id'].'">Generate</span></button>' : '<button type="button" class="btn btn-success btn-md"><span class="btn-icon-start text-success"><i class="fa fa-check color-success"></i>
                </span>Generated</button>';
            }

            $trade_details = '<div class="details">
                                <div>
                                    <h6>'.$batch['trade_name'].'</h6>
                                    <span>'.$batch['trade_code'].'</span>
                                </div>	
                            </div>';

            $assessor_details = '<div class="details">
                                <div>
                                    <h6>'.$batch['assessor_name'].'</h6>
                                    <span>'.$batch['assessor_code'].'</span>
                                </div>	
                            </div>';                

                            
            
			if ($type == "Pending") {
                $action = '<div class="dropdown ms-auto text-end c-pointer">
                                <div class="btn-link" data-bs-toggle="dropdown">
                                    <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                                </div>';
                $action .= '<div class="dropdown-menu dropdown-menu-end">
									<a class="dropdown-item" href="#" id="btn-'.$batch['tb_id'].'" onclick="getBatchDetails('.$batch['tb_id'].');" data-ag_id="'.$batch['ag_name'].'" data-scheme_id="'.$batch['scheme_name'].'" data-subscheme_id="'.$batch['subscheme_name'].'" 
                                                                                                                                data-ssc_id="'.$batch['ssc_code'].'" data-trade_id="'.$batch['trade_name'].'" data-tp_id="'.$batch['tp_name'].'" 
                                                                                                                                data-tc_id="'.$batch['tc_name'].'" data-batch_id="'.$batch['batch_id'].'" data-tb_target="'.$batch['tb_target'].'" 
                                                                                                                                data-spoc_name="'.$batch['spoc_name'].'" data-spoc_mobile="'.$batch['spoc_mobile'].'" data-assessor_id="'.$batch['assessor_name'].'" 
                                                                                                                                data-tb_assessment_date="'.date('d-m-Y',strtotime($batch['tb_assessment_date'])).'" data-tb_start_date_time="'.date('d-m-Y H:i:s',strtotime($batch['tb_start_date_time'])).'" data-tb_end_date_time="'.date('d-m-Y H:i:s',strtotime($batch['tb_end_date_time'])).'"  
                                                                                                                                data-lid="'.$batch['language_name'].'" data-exam_duration_mins="'.$batch['exam_duration_mins'].'" data-tb_exam_type="'.$batch['tb_exam_type'].'" 
                                                                                                                                data-qp_shuffling="'.$batch['qp_shuffling'].'" data-take_snapshots="'.$batch['take_snapshots'].'" data-aadhar_verification="'.$batch['aadhar_verification'].'" 
                                                                                                                                data-practical_answer_type="'.$batch['practical_answer_type'].'" data-practicalactivity_duration_mins="'.$batch['practicalactivity_duration_mins'].'" 
                                                                                                                                data-viva_answer_type="'.$batch['viva_answer_type'].'" data-viva_duration_mins="'.$batch['viva_duration_mins'].'" data-tb_assessment_status="'.$batch['tb_assessment_status'].'" 
                                                                                                                                data-theory_instructions="'.$batch['theory_instructions'].'" data-practical_activity_instructions="'.$batch['practical_activity_instructions'].'" 
                                                                                                                                data-viva_instructions="'.$batch['viva_instructions'].'"><i class="fas fa-eye"></i>&nbsp;View Details</a>
                                    <a class="dropdown-item" href="'.site_url('import-students/'. $batch['tb_id']).'"><i class="fas fa-users"></i>&nbsp;Import Students</a>
									<a class="dropdown-item" href="'.site_url('download-attendance-sheet/'. id_encode($batch['tb_id'])).'" target="_blank"><i class="fas fa-print"></i>&nbsp;Download Attendance PDF</a>
									<a class="dropdown-item" href="'.site_url('download-omr-sheet/'. id_encode($batch['tb_id'])).'" target="_blank"><i class="fas fa-print"></i>&nbsp;Download OMR PDF</a>';
                                    if($batch['qp_generated_status'] == 1 && $studentsImportedCount[$batch['tb_id']] > 0) {
                                        $action .= '<a class="dropdown-item" href="'.site_url('download-batch-question-paper/'. id_encode($batch['tb_id'])).'" target="_blank"><i class="fas fa-print"></i>&nbsp;Download Question Paper PDF</a>';
                                        $action .= '<a class="dropdown-item" href="'.site_url('download-batch-answer-key/'. id_encode($batch['tb_id'])).'" target="_blank"><i class="fas fa-print"></i>&nbsp;Download Answer Key PDF</a>';
                                    }
                                    if($batch['center_building_photo'] != "") {
                                        $action .= '<a class="dropdown-item" href="'.site_url('download-batch-center-photos/'. $batch['center_building_photo']).'" target="_blank"><i class="fas fa-download"></i>&nbsp;Download Center Building Photo</a>';
                                    }
                                    if($batch['selfie_with_center_board'] != "") {
                                        $action .= '<a class="dropdown-item" href="'.site_url('download-batch-center-photos/'. $batch['selfie_with_center_board']).'" target="_blank"><i class="fas fa-download"></i>&nbsp;Download Selfie with Center Board Photo</a>';
                                    }
                                    $action .= '<a class="dropdown-item" href="'.site_url('edit-batch/'. $batch['tb_id']).'"><i class="fas fa-pencil-alt"></i>&nbsp;Edit Batch</a>';
                                    //if($totalStudents == 0) {
                                    $action .= '<a class="dropdown-item" href="'.site_url('delete-batch/'. $batch['tb_id']).'" onclick="return confirm(\'Are you sure you want to delete this record?\');"><i class="fas fa-trash"></i>&nbsp;Delete Batch</a>';      
                                    $action .= '<a class="dropdown-item" href="'.site_url('view-batch-assessment-documents/'. $batch['tb_id']).'/'.$batch_id.'"><i class="fas fa-eye"></i>&nbsp;View Batch Assessment Documents</a>';  
                                    //}
                     $action .= '</div>
                            </div>';


                $data[] = array($i, $batch['batch_id'],$trade_details,
                date('d-m-Y',strtotime($batch['tb_start_date_time']))."<br>".date('H:i',strtotime($batch['tb_start_date_time'])), 
                date('d-m-Y',strtotime($batch['tb_end_date_time']))."<br>".date('H:i',strtotime($batch['tb_end_date_time'])), 
                $totalStudents,$assessor_details,$batch['tb_exam_type'],$qp_generated_status,$status,$action);            
            } else {
                $result_processing_col = '<button type="button" class="btn btn-danger btn-md" id="btn_'.$batch['tb_id'].'" onclick="processResult('.$batch['tb_id'].','.$batch['trade_id'].');"><span class="btn-icon-start text-danger"><i class="fa fa-spinner color-danger"></i>
                                        </span><span id="spn_'.$batch['tb_id'].'">Process</span></button>';

                $result_processing = ($batch['result_processing'] == 'Pending') ? '<span class="badge badge-lg light badge-danger">'.$batch['result_processing'].'</span>' :  '<span class="badge badge-lg light badge-success">'.$batch['result_processing'].'</span>';

                $action = '<div class="dropdown ms-auto text-end c-pointer">
                            <div class="btn-link" data-bs-toggle="dropdown">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="'.site_url('download-batch-assessment-documents/'. $batch['tb_id']).'/'.$batch_id.'"><i class="fas fa-download"></i>&nbsp;Download Batch Assessment Documents</a>
                                <a class="dropdown-item" href="'.site_url('view-batch-assessment-documents/'. $batch['tb_id']).'/'.$batch_id.'"><i class="fas fa-eye"></i>&nbsp;View Batch Assessment Documents</a>
                                <a class="dropdown-item" href="'.site_url('view-batch-result/'. $batch['tb_id']).'"><i class="fas fa-eye""></i>&nbsp;View Batch Result</a>
                                <a class="dropdown-item" href="'.site_url('download-batch-assessment-photos/'. $batch['tb_id']).'/'.$batch_id.'"><i class="fas fa-download"></i>&nbsp;Download Batch Assessment Photos</a>
                                <a class="dropdown-item" href="'.site_url('download-batch-candidates-aadhaar-photos/'. $batch['tb_id']).'/'.$batch_id.'" target="_blank"><i class="fas fa-download"></i>&nbsp;Download Batch Candidates Aadhar Photos PDF</a>
                                <a class="dropdown-item" href="'.site_url('download-batch-candidates-with-aadhaar-photos/'. $batch['tb_id']).'/'.$batch_id.'" target="_blank"><i class="fas fa-download"></i>&nbsp;Download Batch Candidates with Aadhar Photos PDF</a>
                                <a class="dropdown-item" href="'.site_url('download-batch-group-photos/'. $batch['tb_id']).'/'.$batch_id.'" target="_blank"><i class="fas fa-download"></i>&nbsp;Download Batch Group Photos PDF</a>
                                <a class="dropdown-item" href="#" id="btn-'.$batch['tb_id'].'" onclick="getBatchDetails('.$batch['tb_id'].');" data-ag_id="'.$batch['ag_name'].'" data-scheme_id="'.$batch['scheme_name'].'" data-subscheme_id="'.$batch['subscheme_name'].'" 
                                                                                                                                data-ssc_id="'.$batch['ssc_code'].'" data-trade_id="'.$batch['trade_name'].'" data-tp_id="'.$batch['tp_name'].'" 
                                                                                                                                data-tc_id="'.$batch['tc_name'].'" data-batch_id="'.$batch['batch_id'].'" data-tb_target="'.$batch['tb_target'].'" 
                                                                                                                                data-spoc_name="'.$batch['spoc_name'].'" data-spoc_mobile="'.$batch['spoc_mobile'].'" data-assessor_id="'.$batch['assessor_name'].'" 
                                                                                                                                data-tb_assessment_date="'.date('d-m-Y',strtotime($batch['tb_assessment_date'])).'" data-tb_start_date_time="'.date('d-m-Y H:i:s',strtotime($batch['tb_start_date_time'])).'" data-tb_end_date_time="'.date('d-m-Y H:i:s',strtotime($batch['tb_end_date_time'])).'"  
                                                                                                                                data-lid="'.$batch['language_name'].'" data-exam_duration_mins="'.$batch['exam_duration_mins'].'" data-tb_exam_type="'.$batch['tb_exam_type'].'" 
                                                                                                                                data-qp_shuffling="'.$batch['qp_shuffling'].'" data-take_snapshots="'.$batch['take_snapshots'].'" data-aadhar_verification="'.$batch['aadhar_verification'].'" 
                                                                                                                                data-practical_answer_type="'.$batch['practical_answer_type'].'" data-practicalactivity_duration_mins="'.$batch['practicalactivity_duration_mins'].'" 
                                                                                                                                data-viva_answer_type="'.$batch['viva_answer_type'].'" data-viva_duration_mins="'.$batch['viva_duration_mins'].'" data-tb_assessment_status="'.$batch['tb_assessment_status'].'" 
                                                                                                                                data-theory_instructions="'.$batch['theory_instructions'].'" data-practical_activity_instructions="'.$batch['practical_activity_instructions'].'" 
                                                                                                                                data-viva_instructions="'.$batch['viva_instructions'].'"><i class="fas fa-eye"></i>&nbsp;View Details</a>
                                <a class="dropdown-item" href="'.site_url('edit-batch/'. $batch['tb_id']).'"><i class="fas fa-pencil-alt"></i>&nbsp;Edit Batch</a>
                                <a class="dropdown-item" href="'.site_url('delete-batch/'. $batch['tb_id']).'" onclick="return confirm(\'Are you sure you want to delete this record?\');"><i class="fas fa-trash"></i>&nbsp;Delete Batch</a>
                            </div>
                        </div>';

                $data[] = array($i, $batch['batch_id'],$trade_details, 
                date('d-m-Y',strtotime($batch['tb_start_date_time']))."<br>".date('H:i',strtotime($batch['tb_start_date_time'])), 
                date('d-m-Y',strtotime($batch['tb_end_date_time']))."<br>".date('H:i',strtotime($batch['tb_end_date_time'])), 
                $totalStudents,$assessor_details,$batch['tb_exam_type'],$result_processing,$result_processing_col,$action);        
            }
        }
        
        /*echo "<pre>";
        print_r($tradesData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->batch_model->countAll(),
            "recordsFiltered" => $this->batch_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
	
	public function viewAddEditForm($tb_id = 0)
    {
        $this->require_permission('add_batches_inprocess');

        $data['title'] = 'Add Batch';  // Set the title here
        
        $condition = "status = 1";
        $data['arr_assessment_agency'] = $this->Mdmaster->getAllRecords('tbl_assessment_agency',$condition,'ag_name','ASC');
        
        $condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
        
        $condition = "status = 1";
        $data['arr_schemes'] = $this->Mdmaster->getAllRecords('tbl_schemes',$condition,'scheme_name','ASC');
        
        $condition = "status = 1";
        $data['arr_sub_schemes'] = $this->Mdmaster->getAllRecords('tbl_subschemes',$condition,'subscheme_name','ASC');
        
        $condition = "status = 1 and language_id > 1";
        $data['arr_languages'] = $this->Mdmaster->getAllRecords('tbl_languages',$condition,'language_name','ASC');
        
        if($tb_id > 0) {
            $data['title'] = 'Edit Batch';  // Set the title here
            
            $arr_batch_details = $this->batch_model->getBatchDetails($tb_id);
            //echo "<br> str ".$this->db->last_query();
            //exit;
            
            $data['arr_batch_details'] = $arr_batch_details;
        }
        
        $data['tb_id'] = $tb_id;
        
        $this->render_page('admin/batch/add-edit-assessment-batch',$data);
    }
    
    public function save()
    {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/
        $tb_id = $this->input->post('tb_id');
        $action = "";
        
        $tb_start_date_time_timestamp = strtotime($this->input->post('tb_start_date_time'));
        $tb_end_date_time_timestamp = strtotime($this->input->post('tb_end_date_time'));

        // Regular expression to match the first <p> tag and last </p> tag -- For CKEditor
        $pattern = '/^<p[^>]*>|<\/p>$/';

        // Replace the first <p> tag and last </p> tag with an empty string
        $theory_instructions = preg_replace($pattern, '', $this->input->post('theory_instructions'));
        $practical_activity_instructions = preg_replace($pattern, '', $this->input->post('practical_activity_instructions'));
        $viva_instructions = preg_replace($pattern, '', $this->input->post('viva_instructions'));
        
        $data = array(
            'ag_id' => $this->input->post('ag_id'),
			'scheme_id' => $this->input->post('scheme_id'),
			'subscheme_id' => $this->input->post('subscheme_id'),
			'ssc_id' => $this->input->post('ssc_id'),
			'trade_id' => $this->input->post('trade_id'),
            'tp_id' => $this->input->post('tp_id'),
            'tc_id' => $this->input->post('tc_id'),
            'batch_id' => $this->input->post('batch_id'),
            'tb_target' => $this->input->post('tb_target'),
            'spoc_name' => $this->input->post('spoc_name'),
            'spoc_mobile' => $this->input->post('spoc_mobile'),
            'assessor_id' => $this->input->post('assessor_id'),
            'tb_assessment_date' => $this->input->post('tb_assessment_date'),
            'tb_start_date_time' => date('Y-m-d H:i:s', $tb_start_date_time_timestamp),
            'tb_end_date_time' => date('Y-m-d H:i:s', $tb_end_date_time_timestamp),
            'exam_duration_mins' => $this->input->post('exam_duration_mins'),
            'practicalactivity_duration_mins' => $this->input->post('practicalactivity_duration_mins'),
            'viva_duration_mins' => $this->input->post('viva_duration_mins'),
            'lid' => $this->input->post('lid'),
            'tb_exam_type' => $this->input->post('tb_exam_type'),
            'qp_shuffling' => $this->input->post('qp_shuffling'),
            'take_snapshots' => $this->input->post('take_snapshots'),
            'tb_assessment_status' => $this->input->post('tb_assessment_status'),
            'theory_instructions' => $theory_instructions,
            'practical_activity_instructions' => $practical_activity_instructions,
            'viva_instructions' => $viva_instructions,
            'practical_answer_type' => $this->input->post('practical_answer_type'),
            'viva_answer_type' => $this->input->post('viva_answer_type'),
            'aadhar_verification' => $this->input->post('aadhar_verification'),
            'profile_updation' => $this->input->post('profile_updation'),
		);

        /*echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;*/
		
        if($tb_id == 0) { //Insert
            $action = "add";
            $tb_id = $this->Mdmaster->addRecord($data,'tbl_training_batches');
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $action = "edit";

            $this->db->where('tb_id', $tb_id);
            $query = $this->db->update('tbl_training_batches', $data);

            //Check whether the trade is changed, then regenerate the candidates qp
            $hdn_trade_id = $this->input->post('hdn_trade_id');
            if($this->input->post('trade_id') != $hdn_trade_id) { 
                //check whether qp is generated
                $arr_batch_details = $this->batch_model->getBatchDetails($tb_id);
                if($arr_batch_details[0]['qp_generated_status'] == 1) {
                    //Regenrate QP :: generateQuestionBank 
                }
            }
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }

        //If profile_updation is optional, update candidate profile_updated = 1
        if($this->input->post('profile_updation') == 'Optional') {
            $stdProfileUpdData['profile_updated'] = 1;

            $this->db->where('tb_id', $tb_id);
            $query = $this->db->update('tbl_students', $stdProfileUpdData); 
        }

        //Mark candidate assessment status as completed if tb_assessment_status = completed
        if($this->input->post('tb_assessment_status') == 'Completed') {
            $stdUpdData['student_assessment_status'] = 'Completed';

            $this->db->where('tb_id', $tb_id);
            $query = $this->db->update('tbl_students', $stdUpdData); 
        }
        
        //Send Login Details via Email
        $getMailTemplateDetails = get_email_template_details(1);
        foreach($getMailTemplateDetails as $details) {
            $subject = $details['email_subject'];
            $email_content = $details['email_content'];

            //Get assessor details
            $condition = "assessor_id = ".$this->input->post('assessor_id');
            $assessorDetails = $this->Mdmaster->getAllRecords('tbl_assessor',$condition,'assessor_name','ASC');
            $arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id); 
            //echo "<br> str ".$this->db->last_query();exit;

            if($assessorDetails != false && $arr_batch_details != false) {
                $assessor_name = $assessorDetails[0]['assessor_name'];
                $assessor_email = $assessorDetails[0]['assessor_email'];
                $assessor_code = $assessorDetails[0]['assessor_code'];
                $assessor_password = $assessorDetails[0]['assessor_password'];
                $batch_id = $arr_batch_details[0]['batch_id'];
                $trade_name = $arr_batch_details[0]['trade_name'];
                $tb_assessment_date = date('d-m-Y',strtotime($arr_batch_details[0]['tb_assessment_date']));
                $batch_id = $arr_batch_details[0]['batch_id'];
                $scheme_name = $arr_batch_details[0]['scheme_name'];
                $spoc_name = $arr_batch_details[0]['spoc_name'];
                $spoc_mobile = $arr_batch_details[0]['spoc_mobile'];
                $tc_code = $arr_batch_details[0]['tc_code'];
                $tc_name = $arr_batch_details[0]['tc_name'];
                $center_address = $arr_batch_details[0]['center_address'];

                $batchDetails = '<tr>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$batch_id.'</td>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$scheme_name.'</td>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$trade_name.'</td>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$tb_assessment_date.'</td>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$tc_code.' - '.$tc_name.'<br>'.$center_address.'</td>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$spoc_name.'</td>
                                <td style="border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; border-right: 1px solid #c6c6c6; border-top: 1px solid #c6c6c6">'.$spoc_mobile.'</td>
                            </tr>';

                $message = str_replace(array('$assessor_name','$assessor_code','$assessor_password','$batchDetails'),array($assessor_name,$assessor_code,$assessor_password,$batchDetails),$email_content);

                //echo "<br> Message <br>".$message;
                //exit;

                send_email($subject,$message,$assessor_name,$assessor_email); 
            }
        }  
        
        if($action == "add") {
            redirect('import-students/'.$tb_id);
        }
        else {
            redirect('list-batches-inprocess');
        }    
    }
    
    public function delete($tb_id) {
        $this->require_permission('delete_batches_inprocess');

        $updStatus['status'] = 0;
        $this->db->where('tb_id', $tb_id);
        $query = $this->db->update('tbl_training_batches', $updStatus);
	    
	    //Delete related students and their other records
	    $this->db->where('tb_id', $tb_id);
        $query = $this->db->update('tbl_students', $updStatus);
        //echo "<br> str ".$this->db->last_query();exit;
        
        //Delete from tbl_theory_answers,tbl_viva_answers,tbl_practical_activity_answers
        $this->db->where('tb_id', $tb_id);
        $this->db->delete('tbl_theory_answers');
        
        $this->db->where('tb_id', $tb_id);
        $this->db->delete('tbl_practical_activity_answers');
        
        $this->db->where('tb_id', $tb_id);
        $this->db->delete('tbl_viva_answers');
	    
        $this->session->set_flashdata('msg', 'Data deleted successfully');
        redirect('list-batches-inprocess');
    }
    
    public function GetTradesBySsc() {
        $ssc_id = $this->input->post('ssc_id');
        $trade_id = $this->input->post('trade_id');
        
        $condition = "status = 1 AND ssc_id = ".$ssc_id;
        $arr_trades = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_code','ASC');
        
        $output = '<option value="">Choose...</option>';
        if($arr_trades != false) {
            foreach($arr_trades as $trades) {
                $selected = ($trades['trade_id'] == $trade_id) ? 'selected' : '';
                $output .= '<option value="'.$trades['trade_id'].'" '.$selected.'>'.$trades['trade_name'].' ('.$trades['trade_code'].')</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }
    
    public function GetPartnersBySsc() {
        $ssc_id = $this->input->post('ssc_id');
        $tp_id = $this->input->post('tp_id');
        
        $arr_partners = $this->partner_model->getPartnersDetailsBySsc($ssc_id);
        
        $output = '<option value="">Choose...</option>';
        if($arr_partners != false) {
            foreach($arr_partners as $partners) {
                $selected = ($partners['tp_id'] == $tp_id) ? 'selected' : '';
                $output .= '<option value="'.$partners['tp_id'].'" '.$selected.'>'.$partners['name'].'</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }
    
    public function GetAssessorsBySsc() {
        $ssc_id = $this->input->post('ssc_id');
        $assessor_id = $this->input->post('assessor_id');
        
        $arr_assessors = $this->assessors_model->getAssessorDetailsBySsc($ssc_id);
        //echo "<br> str ".$this->db->last_query();
        
        $output = '<option value="">Choose...</option>';
        if($arr_assessors != false) {
            foreach($arr_assessors as $assessors) {
                $selected = ($assessors['assessor_id'] == $assessor_id) ? 'selected' : '';
                $output .= '<option value="'.$assessors['assessor_id'].'" '.$selected.'>'.$assessors['assessor_code'].'-'.$assessors['assessor_name'].'</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }
    
    public function GetCentersByPartner() {
        $tp_id = $this->input->post('tp_id');
        $ssc_id = $this->input->post('ssc_id');
        $tc_id = $this->input->post('tc_id');
        
        $arr_centers = $this->partner_model->getCenterDetailsByPartnerSsc($tp_id,$ssc_id);
        
        $output = '<option value="">Choose...</option>';
        if($arr_centers != false) {
            foreach($arr_centers as $centers) {
                $selected = ($centers['tc_id'] == $tc_id) ? 'selected' : '';
                $output .= '<option value="'.$centers['tc_id'].'" data-spoc_name="'.$centers['contact_first_name'].' '.$centers['contact_last_name'].'" data-spoc_mobile="'.$centers['contact_mobile'].'" '.$selected.'>'.$centers['name'].' ('.$centers['tc_code'].')</option>';
            }
        }    
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output'] = $output;
        
        echo json_encode($data);
    }

    public function GetAssessorTradesBySsc() {
        $ssc_id = $this->input->post('ssc_id');
        
        $condition = "status = 1 AND ssc_id = ".$ssc_id;
        $arr_trades = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_code','ASC');
        
        $output_trades = '<option value="">Choose...</option>';
        if($arr_trades != false) {
            foreach($arr_trades as $trades) {
                $selected = '';
                $output_trades .= '<option value="'.$trades['trade_id'].'" '.$selected.'>'.$trades['trade_name'].' ('.$trades['trade_code'].')</option>';
            }
        } 
        
        $arr_assessors = $this->assessors_model->getAssessorDetailsBySsc($ssc_id);
        //echo "<br> str ".$this->db->last_query();
        
        $output_assessors = '<option value="">Choose...</option>';
        if($arr_assessors != false) {
            foreach($arr_assessors as $assessors) {
                $selected = ''; 
                $output_assessors .= '<option value="'.$assessors['assessor_id'].'" '.$selected.'>'.$assessors['assessor_code'].'-'.$assessors['assessor_name'].'</option>';
            }
        }
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['output_trades'] = $output_trades;
        $data['output_assessors'] = $output_assessors;
        
        echo json_encode($data);
    }
    
    public function CheckDuplicateBatchId() {
        $batch_id = $this->input->post('batch_id');
        $tb_id = $this->input->post('tb_id');
        
        $condition = ($tb_id > 0) ? " tb_id != ".$tb_id." AND status = 1" : "status = 1";
        $validate = $this->Mdmaster->checkDuplicate('batch_id',$batch_id,'tbl_training_batches',$condition);
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }
	
	public function importStudents($tb_id)
    {
        $condition = "tb_id = ".$tb_id;
        $arr_batch_details = $this->Mdmaster->getAllRecords('tbl_training_batches',$condition,'tb_id','ASC');
        $batch_id = ($arr_batch_details != false) ? $arr_batch_details[0]['batch_id'] : "";
        $tb_target = ($arr_batch_details != false) ? $arr_batch_details[0]['tb_target'] : "";
        $qp_generated_status = ($arr_batch_details != false) ? $arr_batch_details[0]['qp_generated_status'] : 0;
        $profile_updation = ($arr_batch_details != false) ? $arr_batch_details[0]['profile_updation'] : 'Mandatory';

        $totalBatchStudents   = $this->batch_model->getBatchTotalStudentsCount($tb_id);
        $totalPendingStudents = $this->batch_model->getBatchTotalPendingStudentsCount($tb_id); //Get assessment not started students count
        
        $data['title'] = 'Import Students - '.$batch_id;  // Set the title here
      
        $data['tb_id'] = $tb_id;
		$data['batch_id'] = $batch_id;
        $data['tb_target'] = $tb_target;
        $data['totalBatchStudents'] = $totalBatchStudents;
        $data['totalPendingStudents'] = $totalPendingStudents;
        $data['qp_generated_status'] = $qp_generated_status; 
        $data['profile_updation'] = $profile_updation; 
		
        $this->render_page('admin/batch/import_students',$data);
    }
	
	
	public function importStudentsSave()
    {		
		/*echo "<pre>";
		print_r($_POST);
		print_r($_FILES);
		echo "</pre>";
		exit;*/
		
		$tb_id = $this->input->post('tb_id');
		$batch_id = $this->input->post('batch_id');
        $tb_target = $this->input->post('tb_target');
        $profile_updation = $this->input->post('profile_updation');
 
		$message = "";
		$uniqueId = "";
		//Get Total count of students in the batch
		$total_students = $this->batch_model->getBatchTotalStudentsCount($tb_id);
		$ct_uploaded_students = ($total_students > 0) ? $total_students : 0;
		$ct_skipped_students = 0;
		$type = "";
		
		$allowedFileType = [
			'application/vnd.ms-excel',
			'text/xls',
			'text/xlsx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		];
		if (in_array($_FILES["file"]["type"], $allowedFileType)) 
		{
			//$targetPath = 'uploads/students/' . $_FILES['file']['name'];
			
			$file_name = "Students_".$tb_id . '_' . time() . '_' . str_replace(" ", "", $_FILES['file']['name']);
					
			$targetPath = 'uploads/students/' . $file_name;
			
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
			$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadSheet = $Reader->load($targetPath);
			$excelSheet = $spreadSheet->getActiveSheet();
			$spreadSheetAry = $excelSheet->toArray();

            $nonNullRowCount = 0;

            foreach ($spreadSheetAry as $row) {
                $isNonNullRow = false;
                
                foreach ($row as $cell) {
                    if ($cell !== null) {
                        $isNonNullRow = true;
                        break;
                    }
                }
                
                if ($isNonNullRow) {
                    $nonNullRowCount++;
                }
            }


			$sheetCount = $nonNullRowCount;
			$arrEnrollmentNo = array();
			$arrExistingEnrollmentNo = array(); 
			$arrAadharNo = array();
			$arrExistingAadharNo = array();
			
			/*echo "<pre>";
			print_r($spreadSheetAry);
            echo "</pre>";
			echo "<br> sheet count ".$sheetCount;exit; */
			
			//Fetch all the enrollment no's and store in array
			for ($i = 1; $i <= $sheetCount; $i++) 
			{
			    if (isset($spreadSheetAry[$i][0])) {
					$enrollment_number = $spreadSheetAry[$i][0];
					$arrEnrollmentNo[$enrollment_number] = $enrollment_number;
				}
				if (isset($spreadSheetAry[$i][2])) {
					$aadhar_number = $spreadSheetAry[$i][2];
					$arrAadharNo[$aadhar_number] = $aadhar_number;
				}
			} 
			
			/*echo "<pre>";
			print_r($arrEnrollmentNo);
            print_r($arrAadharNo);
			echo "</pre>";
			exit;*/
			
			if(count($arrEnrollmentNo) > 0) {
                $arrExistingEnrollmentNoDetails = $this->batch_model->get_student_by_enrollment_number($arrEnrollmentNo);
                //echo "<br> str ".$this->db->last_query();exit;
                if($arrExistingEnrollmentNoDetails != false) {
                    foreach($arrExistingEnrollmentNoDetails as $enrollmentData) {
                        $arrExistingEnrollmentNo[$enrollmentData['enrollment_number']] = $enrollmentData['batch_id'];
                    }
                }
            }    
			
            if(count($arrAadharNo) > 0) {
                $arrExistingAadharNoDetails = $this->batch_model->get_student_by_aadhar_number($arrAadharNo);
                //echo "<br> str ".$this->db->last_query();exit;
                if($arrExistingAadharNoDetails != false) {
                    foreach($arrExistingAadharNoDetails as $aadharData) {
                        $arrExistingAadharNo[$aadharData['aadhar_number']] = $aadharData['batch_id'];
                    }
                }
            }

            $uniqueId = $batch_id.'-'.random_strings(6);
			for ($i = 1; $i < $sheetCount; $i++) 
			{
				$arr_students = array();
				$skipped_reason = "";
				$arr_students['tb_id'] = $tb_id;
				$arr_students['password'] = $this->generateRandomStudentPassword();
				
				if (isset($spreadSheetAry[$i][1])) {
					$arr_students['student_name'] = $spreadSheetAry[$i][1];
				}
				
				$errorEnrollmentNumber = 0;
				$errorAadhaarNumber = 0;
                $errorBatchTarget = 0;
				
				if (isset($spreadSheetAry[$i][0])) {
					$arr_students['enrollment_number'] = $spreadSheetAry[$i][0];
					if(array_key_exists($arr_students['enrollment_number'],$arrExistingEnrollmentNo))
					{
						$errorEnrollmentNumber++;
					}
				}
								
				if (isset($spreadSheetAry[$i][2])) {
					$arr_students['aadhar_number'] = $spreadSheetAry[$i][2];
					$count_chars = mb_strlen($arr_students['aadhar_number']);
					if($count_chars < 12)
					{
						$errorAadhaarNumber++;
					}
					else if(array_key_exists($arr_students['aadhar_number'],$arrExistingAadharNo))
					{
						$errorAadhaarNumber++;
					}
				}

                if($ct_uploaded_students == $tb_target) {
                    $errorBatchTarget++;
                }    
				//echo "<br> errorEnrollmentNumber ".$errorEnrollmentNumber." errorAadhaarNumber ".$errorAadhaarNumber." errorBatchTarget ".$errorBatchTarget;
				if($errorEnrollmentNumber > 0 || $errorAadhaarNumber > 0 || $errorBatchTarget > 0) {
				    if($errorEnrollmentNumber > 0) {
				        $skipped_reason .= "Enrolment Number already exists in the ". $arrExistingEnrollmentNo[$arr_students['enrollment_number']];
				    }
				    if($errorAadhaarNumber > 0) {
				        if(array_key_exists($arr_students['aadhar_number'],$arrExistingAadharNo))
    					{
    						$skipped_reason .= "Aadhar Number already exists in the ". $arrExistingAadharNo[$arr_students['aadhar_number']];
    					}
				        else {
				            $skipped_reason .= "Aadhar Number has less than 12 characters.";
				        }
				    }
                    if($errorBatchTarget > 0) {
				        $skipped_reason .= "Batch Target already Met. Target:". $tb_target;
				    }
				    
				    /*echo "<pre>";
        			print_r($arr_students);
                    echo "</pre>";*/
				    
				    $data_skipped = array(
						'enrollment_number' => $arr_students['enrollment_number'],
						'aadhar_number' => (array_key_exists('aadhar_number',$arr_students)) ? $arr_students['aadhar_number'] : "",
						'student_name' => $arr_students['student_name'],
						'reason_skipped' => $skipped_reason,
						'unique_id' => $uniqueId
					);
					$this->batch_model->addStudentsSkipped($data_skipped);
				    
				    $ct_skipped_students++;
				}
				
								
				if (isset($spreadSheetAry[$i][3])) {
					$arr_students['student_email'] = $spreadSheetAry[$i][3];
				}
				if (isset($spreadSheetAry[$i][4])) {
					$arr_students['student_mobile'] = $spreadSheetAry[$i][4];
				}
						
				if ($skipped_reason == "" && isset($arr_students['enrollment_number']) && isset($arr_students['student_name']))
				{
					$str_token = $arr_students['enrollment_number'].date('Ymdhis');
                    $unique_token =  md5($str_token);
                    $arr_students['unique_token'] = $unique_token;
                    $arr_students['profile_updated'] = ($profile_updation == 'Optional') ? 1 : 0;
                    
                    $insertId = $this->batch_model->addStudents($arr_students);
					
					$ct_uploaded_students++;
					
					$arrExistingEnrollmentNo[$arr_students['enrollment_number']] = $batch_id;
					if(array_key_exists('aadhar_number',$arr_students)){
					    $arrExistingAadharNo[$arr_students['aadhar_number']] = $batch_id;
					}
					
					if (! empty($insertId)) {
						$type = "success";
					} else {
						$type = "error";
						$message = "Problem in Importing Excel Data";
					}
				}
			}
		} else {
			$type = "error";
			$message = "Invalid File Type. Upload Excel File.";
		}
		if($ct_skipped_students == 0 && $type != "error")
		{
			$type = "success";
			$message = "Excel Data Imported with Uploaded Students: " . ($ct_uploaded_students - $total_students) ." and Skipped Students: " . $ct_skipped_students;			
		}
		else if($ct_skipped_students > 0 && $type != "error") {
		    $type = "error";
			$message = "Excel Data Imported with Uploaded Students: " . ($ct_uploaded_students - $total_students) ." and Skipped Students: " . $ct_skipped_students;
		}
		unlink($targetPath);
		$response = array('type' => $type,'message' => $message, 'unique_id' => $uniqueId, 'skipped' => $ct_skipped_students);
        echo json_encode($response);
		
		//$this->session->set_flashdata('msg', $message);
		//redirect('import-students/'.$tb_id.'/');
	}
	
	public function generateRandomStudentPassword()
    {
		$password = '';
        $digits = '123456789'; // Exclude 0 from the possible digits

        // Generate a 4-digit password
        for ($i = 0; $i < 4; $i++) {
            // Choose a random digit from the allowed digits
            $index = rand(0, strlen($digits) - 1);
            // Append the selected digit to the password
            $password .= $digits[$index];
        }

        return $password; 
	}
	
	function getSkippedStudentsLists(){
        $data = $row = array();
        
        // Fetch member's records
        $skippedStudentsData = $this->students_skipped_model->getRows($_POST);
        
        $i = $_POST['start'];
        foreach($skippedStudentsData as $std){
            $i++;
            
            $data[] = array($i, $std['enrollment_number'],$std['aadhar_number'],$std['student_name'],$std['reason_skipped']);
        }
        
        /*echo "<pre>";
        print_r($tradesData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->students_skipped_model->countAll(),
            "recordsFiltered" => $this->students_skipped_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }

    function generateQuestionBank() {
        $generate_qb_message_error = "";

        $tb_id = $this->input->post('tb_id');
        $arrErrorMessage = array();
        $type = "";
        $message = "";

        //Get the Nos Mapped to Trade for the Batch
        $arrBatchTradeNosDetails = $this->batch_model->getBatchTradeNosDetails($tb_id);
        if($arrBatchTradeNosDetails != false) {
            $trade_id     = $arrBatchTradeNosDetails[0]['trade_id'];
            $qp_shuffling = $arrBatchTradeNosDetails[0]['qp_shuffling'];

            $arrNosList = array();
            $arrNosTotalMarksList = array();
            $arrNosQuestionType = array();
            $arrNosIds = array();
            $arrNosDetails = array();
            $arrNosQnsMarks = array();
            $arrNosQnTypeMarks = array();
            $arrNosQnIdMarks = array();
            $arrRandomNosQnTypeMarks = array();
            
            foreach($arrBatchTradeNosDetails as $batchNosData) {
                if($batchNosData['theory_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['Theory'] = $batchNosData['theory_marks'];
                }
                if($batchNosData['practical_skill_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['PracticalSkill'] = $batchNosData['practical_skill_marks'];
                }
                if($batchNosData['practical_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['PracticalActivity'] = $batchNosData['practical_marks'];
                }
                if($batchNosData['viva_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['Viva'] = $batchNosData['viva_marks'];
                }
                
                $arrNosTotalMarksList[$batchNosData['nos_id']]['TotalNosMarks'] = $batchNosData['total_nos_marks'];

                $arrNosIds[$batchNosData['nos_id']] = $batchNosData['nos_id'];
                $arrNosDetails[$batchNosData['nos_id']] = $batchNosData['nos_code']; //."-".$batchNosData['nos_title']

                if($batchNosData['theory_marks'] > 0 && !in_array('Theory',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'Theory');
                }
                if($batchNosData['practical_skill_marks'] > 0 && !in_array('PracticalSkill',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'PracticalSkill');
                }
                if($batchNosData['practical_marks'] > 0 && !in_array('PracticalActivity',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'PracticalActivity');
                }
                if($batchNosData['viva_marks'] > 0 && !in_array('Viva',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'Viva');
                }
            }

            //Get Questions based on trade_id and Nos array
            $arrQuestionsList = $this->batch_model->getQuestionsByNosTrade($trade_id,$arrNosIds,$arrNosQuestionType);
            //echo "<br> str ".$this->db->last_query();exit;
            if($arrQuestionsList != false) {
                foreach($arrQuestionsList as $qnData) {
                    $arrNosQnsMarks[$qnData['nos_id']][$qnData['question_type']][] = $qnData['marks'];
                    $arrNosQnTypeMarks[$qnData['nos_id']][$qnData['question_type']][$qnData['marks']][] =  $qnData['qid'];
                    $arrNosQnIdMarks[$qnData['nos_id']][$qnData['question_type']][$qnData['qid']] =  $qnData['marks'];

                    //For Random qp_shuffling
                    $arrRandomNosQnTypeMarks[$qnData['nos_id']][$qnData['question_type']][$qnData['marks']][] =  $qnData['qid'];
                }
            }
            
            //echo "<pre>";
            //print_r($arrNosList); 
            //print_r($arrNosQuestionType); 
            //print_r($arrNosQnsMarks); 
            //echo "</pre>";
            //exit;

            $arrFinalNosWiseQnTypeMarks = array();
            $arrFinalNosWiseMarks = array();
            $arrFinalNosWiseQns = array();
            $arrQnIds = array();
            $arrRandomQnMarks = array();
            
            if(count($arrNosList) > 0) {
                foreach($arrNosList as $nosId => $arrQnData) {
                    //echo "<br><br> Nos Id ".$nosId;
                    foreach($arrQnData as $questionType => $target_sum) {
                        //echo "<br><br> questionType ".$questionType." target_sum ".$target_sum;

                        $arrFinalNosWiseQnTypeMarks[$nosId][$questionType] = 0;
                        $arrFinalNosWiseMarks[$nosId] = 0;
                        
                        if(array_key_exists($nosId,$arrNosQnsMarks) && array_key_exists($questionType,$arrNosQnsMarks[$nosId])) {
                            $main_array = $arrNosQnsMarks[$nosId][$questionType];

                            /*echo "<br>Main Array: ";
                            echo "<pre>";
                            print_r($main_array);
                            echo "</pre>";*/

                            // Find all combinations of values with the given sum
                            $all_combinations = array();
                            sort($main_array);
                            $this->findUniqueCombinations($main_array, $target_sum, 0, 0, array(), $all_combinations);

                            // Display the combinations
                            //echo "<br>".count($all_combinations)." Combinations of values whose sum totals to $target_sum:\n";
                            if (!empty($all_combinations)) {
                                /*echo "<pre>";
                                print_r($all_combinations);
                                echo "</pre>";*/

                                $randomCombinationIndex = array_rand($all_combinations);
                                $combination = $all_combinations[$randomCombinationIndex];

                                foreach ($combination as $combinationMarks) {
                                    //echo "<br><br>Marks: ".$combinationMarks;
                                    if(array_key_exists($nosId,$arrNosQnTypeMarks) && array_key_exists($questionType,$arrNosQnTypeMarks[$nosId]) && array_key_exists($combinationMarks,$arrNosQnTypeMarks[$nosId][$questionType])) {
                                        /*echo "<pre>";
                                        print_r($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);
                                        echo "</pre>";*/

                                        // Shuffle the array
                                        shuffle($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);

                                        /*echo "<pre>";
                                        print_r($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);
                                        echo "</pre>";*/

                                        // Pick one random value
                                        $randomQnIndex = array_rand($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);
                                        $randomQnId = $arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks][$randomQnIndex];

                                        //Unset that array key 
                                        unset($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks][$randomQnIndex]);
                                        //echo "<br>randomQnIndex: ".$randomQnIndex." random Qid ".$randomQnId;
                                        
                                        $arrQnIds[$nosId][$questionType][$randomQnId] = $combinationMarks;
                                        $arrRandomQnMarks[$nosId][$questionType][$combinationMarks][] = $combinationMarks;  //This is used for Random qp_shuffling
                                    }  
                                }
                            } else {
                                $type = "error";
                                $arrErrorMessage[$nosId][] = "Total Qn marks not satisfying the target sum marks. Required total marks for ".$questionType." questions: ".$target_sum;
                                //echo "<br> type ".$type." message ".$message;
                            }
                        }
                        else {
                            $type = "error";
                            $arrErrorMessage[$nosId][] = $questionType." questions are missing";
                        }
                    }
                }
                //echo "<pre>";
                //print_r($arrQnIds);
                //print_r($arrRandomQnMarks);
                //echo "</pre>";
                //exit;

                if($type == "") {
                    if(count($arrQnIds) > 0) {
                        foreach($arrQnIds as $nosId => $arrMainData)  {
                            //echo "<br> Nos ID ".$nosId;
                            foreach($arrMainData as $questionType => $arrMainData1)  {
                                //echo "<br> questionType ".$questionType;
                                foreach($arrMainData1 as $qId => $marks)  {
                                    //echo "<br> qId ".$qId." marks ".$marks;
                                    $arrFinalNosWiseQns[$questionType][$qId] = $qId;
                                    $arrFinalNosWiseQnTypeMarks[$nosId][$questionType] += $marks;
                                    $arrFinalNosWiseMarks[$nosId] += $marks;
                                }
                            }
                        }

                        //echo "<pre>";
                        //print_r($arrFinalNosWiseQns);
                        //print_r($arrFinalNosWiseQnTypeMarks);
                        //print_r($arrFinalNosWiseMarks);
                        //echo "</pre>";
                        //exit;

                        $finalError = 0;
                        $finalQns = array();
                        //Validate the marks from the questions picked with the Question Pattern matrix
                        foreach($arrFinalNosWiseQnTypeMarks as $nosId => $arrFinalMainData) {
                            foreach($arrFinalMainData as $questionType => $marksForSelectedQids) {
                                $marksQnTypeQBMatrix = $arrNosList[$nosId][$questionType];
                                //echo "<br> Nosid ".$nosId."questionType ".$questionType." main marks ".$marksQnTypeQBMatrix." qn marks ".$marksForSelectedQids;
                                if($marksQnTypeQBMatrix != $marksForSelectedQids) {
                                    $finalError++;
                                    $type = "error";
                                    $arrErrorMessage[$nosId][] = " Marks for selected questions are not matching with the matrix for question type ".$questionType.".<br>Matrix marks($marksQnTypeQBMatrix) : ".$questionType." marks($marksForSelectedQids)";
                                }
                                else {
                                    //$finalQns[$questionType] = 
                                }
                            }
                            if($finalError == 0) {
                                $marksQBMatrix = $arrNosTotalMarksList[$nosId]['TotalNosMarks'];
                                $marksForSelectedQids = $arrFinalNosWiseMarks[$nosId];
                                //echo "<br> Nosid ".$nosId." main total marks ".$marksQBMatrix." qn total marks ".$marksForSelectedQids;
                                if($marksQBMatrix != $marksForSelectedQids) {
                                    $finalError++;
                                    $type = "error";
                                    $arrErrorMessage[$nosId][] = "Total marks for selected questions are not matching with the matrix.<br>Matrix marks($marksQBMatrix) : selected questions marks($marksForSelectedQids)";
                                }
                            }
                        }

                        if($finalError == 0) {
                            $cntUpdate = 0;
                            $arrTheoryQns = array();
                            //Get Student id's for the batch
                            $arrStudentIds = $this->batch_model->getBatchStudentsIds($tb_id);
                            //echo "<br> str ".$this->db->last_query();exit;
                            $totalStudents = ($arrStudentIds != false) ? count($arrStudentIds) : 0;

                            //echo "<br> No Errors so save in table";
                            // Merge theory and practical skill qns
                            if(array_key_exists('Theory',$arrFinalNosWiseQns) && array_key_exists('PracticalSkill',$arrFinalNosWiseQns)) {
                                $arrTheoryQns = array_merge($arrFinalNosWiseQns['Theory'], $arrFinalNosWiseQns['PracticalSkill']);
                            }
                            else {
                                if(array_key_exists('Theory',$arrFinalNosWiseQns) && !array_key_exists('PracticalSkill',$arrFinalNosWiseQns)) {
                                    $arrTheoryQns = $arrFinalNosWiseQns['Theory'];
                                }
                                else if(!array_key_exists('Theory',$arrFinalNosWiseQns) && array_key_exists('PracticalSkill',$arrFinalNosWiseQns)) {
                                    $arrTheoryQns = $arrFinalNosWiseQns['PracticalSkill'];
                                }
                            }
                           
                            /*echo "<pre>";
                            print_r($arrTheoryQns);
                            echo "</pre>";*/

                            // Shuffle the merged array
                            shuffle($arrTheoryQns);                        

                            //Save qns in students table
                            if($qp_shuffling == 'Same') { //Same qns with same order will be saved in to all students of the batch
                                if($arrStudentIds != false) {
                                    $arrUpd['theory_questions'] = implode(',',$arrTheoryQns);    
                                    $arrUpd['theory_qns'] =  array_key_exists('Theory',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['Theory']) : "";     
                                    $arrUpd['practical_skill_questions'] =  array_key_exists('PracticalSkill',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['PracticalSkill']) : "";    
                                    $arrUpd['practical_activity_questions'] =  array_key_exists('PracticalActivity',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['PracticalActivity']) : "";   
                                    $arrUpd['viva_questions'] =  array_key_exists('Viva',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['Viva']) : "";
                                    $arrUpd['qp_generated_status'] = 1;

                                    $this->db->where('tb_id', $tb_id);
                                    $this->db->where('status',1);
                                    $query = $this->db->update('tbl_students', $arrUpd);
                                    //echo "<br> str ".$this->db->last_query();exit;

                                    // Get the number of affected rows
                                    $cntUpdate = $this->db->affected_rows();
                                    //echo "<br> affected rows ".$cntUpdate." total students ".$totalStudents;exit;
                                    
                                    foreach($arrStudentIds as $studData) {
                                        $student_id = $studData['student_id'];
                                        
                                        //Save the questions in tbl_theory_answers table
                                        foreach($arrTheoryQns as $theory_qid) {
                                            $arrTheoryAnswerInsert['tb_id'] = $tb_id;
                                            $arrTheoryAnswerInsert['student_id'] = $student_id;
                                            $arrTheoryAnswerInsert['qid'] = $theory_qid;
                                            $arrTheoryAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                            
                                            $this->db->insert('tbl_theory_answers', $arrTheoryAnswerInsert);
                                        }

                                        $updData = array();
                                        $updData['theory_answers_record_generated'] = 1;
                                        $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                        
                                        //Generate PracticalActivity Answers records
                                        if(array_key_exists('PracticalActivity',$arrFinalNosWiseQns) && count($arrFinalNosWiseQns['PracticalActivity']) > 0) {
                                            foreach($arrFinalNosWiseQns['PracticalActivity'] as $practical_qid) {
                                                //Save the questions in tbl_practical_activity_answers table
                                                $arrPracticalAnswerInsert['tb_id'] = $tb_id;
                                                $arrPracticalAnswerInsert['student_id'] = $student_id;
                                                $arrPracticalAnswerInsert['qid'] = $practical_qid;
                                                $arrPracticalAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_practical_activity_answers', $arrPracticalAnswerInsert);
                                            }

                                            $updData = array();
                                            $updData['practicalactivity_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                        }
                                        //Generate Viva Answers records
                                        if(array_key_exists('Viva',$arrFinalNosWiseQns) && count($arrFinalNosWiseQns['Viva']) > 0) {
                                            foreach($arrFinalNosWiseQns['Viva'] as $viva_qid) {
                                                $arrVivaInsert['tb_id'] = $tb_id;
                                                $arrVivaInsert['student_id'] = $student_id;
                                                $arrVivaInsert['qid'] = $viva_qid;
                                                $arrVivaInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_viva_answers', $arrVivaInsert);
                                            } 
                                            $updData = array();
                                            $updData['viva_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                        }
                                    }    

                                    if($cntUpdate == $totalStudents) {
                                        $arrUpdBatch['qp_generated_status'] = 1;
                                        $this->db->where('tb_id', $tb_id);
                                        $query = $this->db->update('tbl_training_batches', $arrUpdBatch);
                                        //echo "<br> str ".$this->db->last_query();

                                        $type = 'success'; // Data was inserted successfully
                                        $message = 'Questions Generated Successfully';
                                    }
                                }    
                            } 
                            else if($qp_shuffling == 'Shuffled') { //Same qns with shuffled order will be saved in to all students of the batch
                                if($arrStudentIds != false) {
                                    foreach($arrStudentIds as $studData) {
                                        $arrUpd = array();
                                        $student_id = $studData['student_id'];

                                        shuffle($arrTheoryQns);
                                        shuffle($arrFinalNosWiseQns['PracticalActivity']);
                                        shuffle($arrFinalNosWiseQns['Viva']);

                                        $arrUpd['theory_questions'] = implode(',',$arrTheoryQns);    
                                        $arrUpd['theory_qns'] =  implode(',',$arrFinalNosWiseQns['Theory']);     
                                        $arrUpd['practical_skill_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalSkill']);    
                                        $arrUpd['practical_activity_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalActivity']);   
                                        $arrUpd['viva_questions'] =  implode(',',$arrFinalNosWiseQns['Viva']);
                                        $arrUpd['qp_generated_status'] = 1;

                                        $this->db->where('tb_id', $tb_id);
                                        $this->db->where('student_id', $student_id);
                                        $query = $this->db->update('tbl_students', $arrUpd);

                                        if ($this->db->affected_rows() > 0) {
                                            $cntUpdate++;
                                            
                                            //Save the questions in tbl_theory_answers table
                                            foreach($arrTheoryQns as $theory_qid) {
                                                $arrTheoryAnswerInsert['tb_id'] = $tb_id;
                                                $arrTheoryAnswerInsert['student_id'] = $student_id;
                                                $arrTheoryAnswerInsert['qid'] = $theory_qid;
                                                $arrTheoryAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_theory_answers', $arrTheoryAnswerInsert);
                                            }
    
                                            $updData = array();
                                            $updData['theory_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            
                                            //Generate PracticalActivity Answers records
                                            if(count($arrFinalNosWiseQns['PracticalActivity']) > 0) {
                                                foreach($arrFinalNosWiseQns['PracticalActivity'] as $practical_qid) {
                                                    //Save the questions in tbl_practical_activity_answers table
                                                    $arrPracticalAnswerInsert['tb_id'] = $tb_id;
                                                    $arrPracticalAnswerInsert['student_id'] = $student_id;
                                                    $arrPracticalAnswerInsert['qid'] = $practical_qid;
                                                    $arrPracticalAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_practical_activity_answers', $arrPracticalAnswerInsert);
                                                }

                                                $updData = array();
                                                $updData['practicalactivity_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                            //Generate Viva Answers records
                                            if(count($arrFinalNosWiseQns['Viva']) > 0) {
                                                foreach($arrFinalNosWiseQns['Viva'] as $viva_qid) {
                                                    $arrVivaInsert['tb_id'] = $tb_id;
                                                    $arrVivaInsert['student_id'] = $student_id;
                                                    $arrVivaInsert['qid'] = $viva_qid;
                                                    $arrVivaInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_viva_answers', $arrVivaInsert);
                                                } 
                                                $updData = array();
                                                $updData['viva_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                        }
                                    }
                                    if($cntUpdate == $totalStudents) {
                                        $arrUpdBatch['qp_generated_status'] = 1;
                                        $this->db->where('tb_id', $tb_id);
                                        $query = $this->db->update('tbl_training_batches', $arrUpdBatch);

                                        $type = 'success'; // Data was inserted successfully
                                        $message = 'Questions Generated Successfully';
                                    }
                                }
                            }
                            else if($qp_shuffling == 'Random') { //Random qns will be saved for each of the students of the batch
                                /*echo "<pre>";
                                print_r($arrRandomQnMarks); 
                                echo "</pre>";*/

                                $arrRandomMarksQuestions = $arrRandomNosQnTypeMarks;

                                /*echo "<pre>";
                                print_r($arrRandomNosQnTypeMarks);
                                echo "</pre>";
                                exit;*/

                                if($arrStudentIds != false) {
                                    foreach($arrStudentIds as $studData) {
                                        $arrUpd = array();
                                        $student_id = $studData['student_id'];

                                        //echo "<br><br> student_id ".$student_id;

                                        $arrFinalNosWiseQns = array();

                                        foreach($arrRandomQnMarks as $nosId => $arrRandomData) {
                                            //echo "<br><br> Nosid ".$nosId;
                                            foreach($arrRandomData as $questionType => $arrRandomData1) {
                                                foreach($arrRandomData1 as $questionMarks => $arrReqQn) {
                                                    $totalQnsPick = count($arrReqQn);
                                                    //echo "<br> questionType ".$questionType." marks ".$questionMarks." Req Qns ".$totalQnsPick;

                                                    if(count($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks]) < $totalQnsPick) {
                                                        // Assign values from main array to sub array if not already set in sub array
                                                        foreach ($arrRandomNosQnTypeMarks[$nosId][$questionType][$questionMarks] as $mainQid) {
                                                            if (!in_array($mainQid, $arrRandomMarksQuestions[$nosId][$questionType][$questionMarks])) {
                                                                $arrRandomMarksQuestions[$nosId][$questionType][$questionMarks][] = $mainQid;
                                                            }
                                                        }
                                                    }

                                                    /*echo "<pre>";
                                                    print_r($arrRandomMarksQuestions);
                                                    echo "</pre>";*/

                                                    for($i=0;$i<$totalQnsPick;$i++) {
                                                        shuffle($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks]);
            
                                                        // Pick one random value
                                                        $randomQnIndex = array_rand($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks]);
                                                        $randomQnId = $arrRandomMarksQuestions[$nosId][$questionType][$questionMarks][$randomQnIndex];
                
                                                        //Unset that array key
                                                        unset($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks][$randomQnIndex]);
                
                                                        //echo "<br>randomQnIndex: ".$randomQnIndex." random Qid ".$randomQnId;
        
                                                        $arrFinalNosWiseQns[$questionType][$randomQnId] = $randomQnId;
                                                    }
                                                }
                                            }
                                        }

                                        $arrTheoryQns = array_merge($arrFinalNosWiseQns['Theory'], $arrFinalNosWiseQns['PracticalSkill']);

                                        shuffle($arrTheoryQns);
                                        shuffle($arrFinalNosWiseQns['PracticalActivity']);
                                        shuffle($arrFinalNosWiseQns['Viva']);

                                        $arrUpd['theory_questions'] = implode(',',$arrTheoryQns);    
                                        $arrUpd['theory_qns'] =  implode(',',$arrFinalNosWiseQns['Theory']);     
                                        $arrUpd['practical_skill_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalSkill']);    
                                        $arrUpd['practical_activity_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalActivity']);   
                                        $arrUpd['viva_questions'] =  implode(',',$arrFinalNosWiseQns['Viva']);
                                        $arrUpd['qp_generated_status'] = 1;

                                        /*echo "<pre>";
                                        print_r($arrFinalNosWiseQns);
                                        echo "</pre>";*/

                                        $this->db->where('tb_id', $tb_id);
                                        $this->db->where('student_id', $student_id);
                                        $query = $this->db->update('tbl_students', $arrUpd);

                                        if ($this->db->affected_rows() > 0) {
                                            $cntUpdate++;
                                            
                                            //Save the questions in tbl_theory_answers table
                                            foreach($arrTheoryQns as $theory_qid) {
                                                $arrTheoryAnswerInsert['tb_id'] = $tb_id;
                                                $arrTheoryAnswerInsert['student_id'] = $student_id;
                                                $arrTheoryAnswerInsert['qid'] = $theory_qid;
                                                $arrTheoryAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_theory_answers', $arrTheoryAnswerInsert);
                                            }
    
                                            $updData = array();
                                            $updData['theory_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);

                                            //Generate PracticalActivity Answers records
                                            if(count($arrFinalNosWiseQns['PracticalActivity']) > 0) {
                                                foreach($arrFinalNosWiseQns['PracticalActivity'] as $practical_qid) {
                                                    //Save the questions in tbl_practical_activity_answers table
                                                    $arrPracticalAnswerInsert['tb_id'] = $tb_id;
                                                    $arrPracticalAnswerInsert['student_id'] = $student_id;
                                                    $arrPracticalAnswerInsert['qid'] = $practical_qid;
                                                    $arrPracticalAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_practical_activity_answers', $arrPracticalAnswerInsert);
                                                }

                                                $updData = array();
                                                $updData['practicalactivity_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                            //Generate Viva Answers records
                                            if(count($arrFinalNosWiseQns['Viva']) > 0) {
                                                foreach($arrFinalNosWiseQns['Viva'] as $viva_qid) {
                                                    $arrVivaInsert['tb_id'] = $tb_id;
                                                    $arrVivaInsert['student_id'] = $student_id;
                                                    $arrVivaInsert['qid'] = $viva_qid;
                                                    $arrVivaInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_viva_answers', $arrVivaInsert);
                                                } 
                                                $updData = array();
                                                $updData['viva_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                        }
                                    }
                                    if($cntUpdate == $totalStudents) {
                                        $arrUpdBatch['qp_generated_status'] = 1;
                                        $this->db->where('tb_id', $tb_id);
                                        $query = $this->db->update('tbl_training_batches', $arrUpdBatch);

                                        $type = 'success'; // Data was inserted successfully
                                        $message = 'Questions Generated Successfully';
                                    }
                                }
                            }
                        }
                        else {
                            $type = "error";
                            $message = "Couldn't generate Question Bank mismatch with matrix.";
                        }
                    } //End count($arrQnIds)
                    else {
                        $type = "error";
                        $message = "Couldn't generate Question Bank.No sufficient questions.";
                    }
                }    
            } //End Nos list
        }
        else {
            $type = "error";
            $message = "Nos not mapped to the trade selected for the batch.";
        }

        if(count($arrErrorMessage) > 0) {
            foreach($arrErrorMessage as $nosId => $arrQnError) {
                foreach($arrQnError as $qnError) {
                    $generate_qb_message_error  .= "<tr class='showError'><td><b>".strtoupper($arrNosDetails[$nosId])."</b></td><td>".$qnError."</td></tr>";
                }
            }
        }

        //echo "<br> type ".$type." error ".$message;
        //exit;
        $response = array('type' => $type,'message' => $message,'generate_qb_message_error' => $generate_qb_message_error);
        echo json_encode($response);
    }

    function generateCandidateQuestionBank() {
        $arrCandidateIds = $this->input->post('chk_student_id');
        $tb_id = $this->input->post('tb_id');

        $this->db->where('tb_id', $tb_id);
        $this->db->where_in('student_id', $arrCandidateIds);
        $this->db->delete('tbl_theory_answers');
        
        $this->db->where('tb_id', $tb_id);
        $this->db->where_in('student_id', $arrCandidateIds);
        $this->db->delete('tbl_practical_activity_answers');
        
        $this->db->where('tb_id', $tb_id);
        $this->db->where_in('student_id', $arrCandidateIds);
        $this->db->delete('tbl_viva_answers');

        $generate_qb_message_error = "";

        $tb_id = $this->input->post('tb_id');
        $arrErrorMessage = array();
        $type = "";
        $message = "";

        //Get the Nos Mapped to Trade for the Batch
        $arrBatchTradeNosDetails = $this->batch_model->getBatchTradeNosDetails($tb_id);
        if($arrBatchTradeNosDetails != false) {
            $trade_id     = $arrBatchTradeNosDetails[0]['trade_id'];
            $qp_shuffling = $arrBatchTradeNosDetails[0]['qp_shuffling'];

            $arrNosList = array();
            $arrNosTotalMarksList = array();
            $arrNosQuestionType = array();
            $arrNosIds = array();
            $arrNosDetails = array();
            $arrNosQnsMarks = array();
            $arrNosQnTypeMarks = array();
            $arrNosQnIdMarks = array();
            $arrRandomNosQnTypeMarks = array();
            
            foreach($arrBatchTradeNosDetails as $batchNosData) {
                if($batchNosData['theory_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['Theory'] = $batchNosData['theory_marks'];
                }
                if($batchNosData['practical_skill_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['PracticalSkill'] = $batchNosData['practical_skill_marks'];
                }
                if($batchNosData['practical_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['PracticalActivity'] = $batchNosData['practical_marks'];
                }
                if($batchNosData['viva_marks'] > 0) {
                    $arrNosList[$batchNosData['nos_id']]['Viva'] = $batchNosData['viva_marks'];
                }
                
                $arrNosTotalMarksList[$batchNosData['nos_id']]['TotalNosMarks'] = $batchNosData['total_nos_marks'];

                $arrNosIds[$batchNosData['nos_id']] = $batchNosData['nos_id'];
                $arrNosDetails[$batchNosData['nos_id']] = $batchNosData['nos_code']; //."-".$batchNosData['nos_title']

                if($batchNosData['theory_marks'] > 0 && !in_array('Theory',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'Theory');
                }
                if($batchNosData['practical_skill_marks'] > 0 && !in_array('PracticalSkill',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'PracticalSkill');
                }
                if($batchNosData['practical_marks'] > 0 && !in_array('PracticalActivity',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'PracticalActivity');
                }
                if($batchNosData['viva_marks'] > 0 && !in_array('Viva',$arrNosQuestionType)) {
                    array_push($arrNosQuestionType,'Viva');
                }
            }

            //Get Questions based on trade_id and Nos array
            $arrQuestionsList = $this->batch_model->getQuestionsByNosTrade($trade_id,$arrNosIds,$arrNosQuestionType);
            //echo "<br> str ".$this->db->last_query();exit;
            if($arrQuestionsList != false) {
                foreach($arrQuestionsList as $qnData) {
                    $arrNosQnsMarks[$qnData['nos_id']][$qnData['question_type']][] = $qnData['marks'];
                    $arrNosQnTypeMarks[$qnData['nos_id']][$qnData['question_type']][$qnData['marks']][] =  $qnData['qid'];
                    $arrNosQnIdMarks[$qnData['nos_id']][$qnData['question_type']][$qnData['qid']] =  $qnData['marks'];

                    //For Random qp_shuffling
                    $arrRandomNosQnTypeMarks[$qnData['nos_id']][$qnData['question_type']][$qnData['marks']][] =  $qnData['qid'];
                }
            }
            
            //echo "<pre>";
            //print_r($arrNosList); 
            //print_r($arrNosQuestionType); 
            //print_r($arrNosQnsMarks); 
            //echo "</pre>";
            //exit;

            $arrFinalNosWiseQnTypeMarks = array();
            $arrFinalNosWiseMarks = array();
            $arrFinalNosWiseQns = array();
            $arrQnIds = array();
            $arrRandomQnMarks = array();
            
            if(count($arrNosList) > 0) {
                foreach($arrNosList as $nosId => $arrQnData) {
                    //echo "<br><br> Nos Id ".$nosId;
                    foreach($arrQnData as $questionType => $target_sum) {
                        //echo "<br><br> questionType ".$questionType." target_sum ".$target_sum;

                        $arrFinalNosWiseQnTypeMarks[$nosId][$questionType] = 0;
                        $arrFinalNosWiseMarks[$nosId] = 0;
                        
                        if(array_key_exists($nosId,$arrNosQnsMarks) && array_key_exists($questionType,$arrNosQnsMarks[$nosId])) {
                            $main_array = $arrNosQnsMarks[$nosId][$questionType];

                            /*echo "<br>Main Array: ";
                            echo "<pre>";
                            print_r($main_array);
                            echo "</pre>";*/

                            // Find all combinations of values with the given sum
                            $all_combinations = array();
                            sort($main_array);
                            $this->findUniqueCombinations($main_array, $target_sum, 0, 0, array(), $all_combinations);

                            // Display the combinations
                            //echo "<br>".count($all_combinations)." Combinations of values whose sum totals to $target_sum:\n";
                            if (!empty($all_combinations)) {
                                /*echo "<pre>";
                                print_r($all_combinations);
                                echo "</pre>";*/

                                $randomCombinationIndex = array_rand($all_combinations);
                                $combination = $all_combinations[$randomCombinationIndex];

                                foreach ($combination as $combinationMarks) {
                                    //echo "<br><br>Marks: ".$combinationMarks;
                                    if(array_key_exists($nosId,$arrNosQnTypeMarks) && array_key_exists($questionType,$arrNosQnTypeMarks[$nosId]) && array_key_exists($combinationMarks,$arrNosQnTypeMarks[$nosId][$questionType])) {
                                        /*echo "<pre>";
                                        print_r($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);
                                        echo "</pre>";*/

                                        // Shuffle the array
                                        shuffle($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);

                                        /*echo "<pre>";
                                        print_r($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);
                                        echo "</pre>";*/

                                        // Pick one random value
                                        $randomQnIndex = array_rand($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks]);
                                        $randomQnId = $arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks][$randomQnIndex];

                                        //Unset that array key 
                                        unset($arrNosQnTypeMarks[$nosId][$questionType][$combinationMarks][$randomQnIndex]);
                                        //echo "<br>randomQnIndex: ".$randomQnIndex." random Qid ".$randomQnId;
                                        
                                        $arrQnIds[$nosId][$questionType][$randomQnId] = $combinationMarks;
                                        $arrRandomQnMarks[$nosId][$questionType][$combinationMarks][] = $combinationMarks;  //This is used for Random qp_shuffling
                                    }  
                                }
                            } else {
                                $type = "error";
                                $arrErrorMessage[$nosId][] = "Total Qn marks not satisfying the target sum marks. Required total marks for ".$questionType." questions: ".$target_sum;
                                //echo "<br> type ".$type." message ".$message;
                            }
                        }
                        else {
                            $type = "error";
                            $arrErrorMessage[$nosId][] = $questionType." questions are missing";
                        }
                    }
                }
                //echo "<pre>";
                //print_r($arrQnIds);
                //print_r($arrRandomQnMarks);
                //echo "</pre>";
                //exit;

                if($type == "") {
                    if(count($arrQnIds) > 0) {
                        foreach($arrQnIds as $nosId => $arrMainData)  {
                            //echo "<br> Nos ID ".$nosId;
                            foreach($arrMainData as $questionType => $arrMainData1)  {
                                //echo "<br> questionType ".$questionType;
                                foreach($arrMainData1 as $qId => $marks)  {
                                    //echo "<br> qId ".$qId." marks ".$marks;
                                    $arrFinalNosWiseQns[$questionType][$qId] = $qId;
                                    $arrFinalNosWiseQnTypeMarks[$nosId][$questionType] += $marks;
                                    $arrFinalNosWiseMarks[$nosId] += $marks;
                                }
                            }
                        }

                        //echo "<pre>";
                        //print_r($arrFinalNosWiseQns);
                        //print_r($arrFinalNosWiseQnTypeMarks);
                        //print_r($arrFinalNosWiseMarks);
                        //echo "</pre>";
                        //exit;

                        $finalError = 0;
                        $finalQns = array();
                        //Validate the marks from the questions picked with the Question Pattern matrix
                        foreach($arrFinalNosWiseQnTypeMarks as $nosId => $arrFinalMainData) {
                            foreach($arrFinalMainData as $questionType => $marksForSelectedQids) {
                                $marksQnTypeQBMatrix = $arrNosList[$nosId][$questionType];
                                //echo "<br> Nosid ".$nosId."questionType ".$questionType." main marks ".$marksQnTypeQBMatrix." qn marks ".$marksForSelectedQids;
                                if($marksQnTypeQBMatrix != $marksForSelectedQids) {
                                    $finalError++;
                                    $type = "error";
                                    $arrErrorMessage[$nosId][] = " Marks for selected questions are not matching with the matrix for question type ".$questionType.".<br>Matrix marks($marksQnTypeQBMatrix) : ".$questionType." marks($marksForSelectedQids)";
                                }
                                else {
                                    //$finalQns[$questionType] = 
                                }
                            }
                            if($finalError == 0) {
                                $marksQBMatrix = $arrNosTotalMarksList[$nosId]['TotalNosMarks'];
                                $marksForSelectedQids = $arrFinalNosWiseMarks[$nosId];
                                //echo "<br> Nosid ".$nosId." main total marks ".$marksQBMatrix." qn total marks ".$marksForSelectedQids;
                                if($marksQBMatrix != $marksForSelectedQids) {
                                    $finalError++;
                                    $type = "error";
                                    $arrErrorMessage[$nosId][] = "Total marks for selected questions are not matching with the matrix.<br>Matrix marks($marksQBMatrix) : selected questions marks($marksForSelectedQids)";
                                }
                            }
                        }

                        if($finalError == 0) {
                            $cntUpdate = 0;
                            $arrTheoryQns = array();
                            //Get Student id's for the batch
                            $arrStudentIds = $this->batch_model->getBatchStudentsByIds($tb_id,$arrCandidateIds);
                            //echo "<br> str ".$this->db->last_query();exit;
                            $totalStudents = ($arrStudentIds != false) ? count($arrStudentIds) : 0;

                            //echo "<br> No Errors so save in table";
                            // Merge theory and practical skill qns
                            if(array_key_exists('Theory',$arrFinalNosWiseQns) && array_key_exists('PracticalSkill',$arrFinalNosWiseQns)) {
                                $arrTheoryQns = array_merge($arrFinalNosWiseQns['Theory'], $arrFinalNosWiseQns['PracticalSkill']);
                            }
                            else {
                                if(array_key_exists('Theory',$arrFinalNosWiseQns) && !array_key_exists('PracticalSkill',$arrFinalNosWiseQns)) {
                                    $arrTheoryQns = $arrFinalNosWiseQns['Theory'];
                                }
                                else if(!array_key_exists('Theory',$arrFinalNosWiseQns) && array_key_exists('PracticalSkill',$arrFinalNosWiseQns)) {
                                    $arrTheoryQns = $arrFinalNosWiseQns['PracticalSkill'];
                                }
                            }
                           
                            /*echo "<pre>";
                            print_r($arrTheoryQns);
                            echo "</pre>";*/

                            // Shuffle the merged array
                            shuffle($arrTheoryQns);                        

                            //Save qns in students table
                            if($qp_shuffling == 'Same') { //Same qns with same order will be saved in to all students of the batch
                                if($arrStudentIds != false) {
                                    $arrUpd['theory_questions'] = implode(',',$arrTheoryQns);    
                                    $arrUpd['theory_qns'] =  array_key_exists('Theory',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['Theory']) : "";     
                                    $arrUpd['practical_skill_questions'] =  array_key_exists('PracticalSkill',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['PracticalSkill']) : "";    
                                    $arrUpd['practical_activity_questions'] =  array_key_exists('PracticalActivity',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['PracticalActivity']) : "";   
                                    $arrUpd['viva_questions'] =  array_key_exists('Viva',$arrFinalNosWiseQns) ? implode(',',$arrFinalNosWiseQns['Viva']) : "";
                                    $arrUpd['qp_generated_status'] = 1;
                                    $arrUpd['qp_regenerated_count'] = 1;      
                                    
                                    $this->db->where('tb_id', $tb_id);
                                    $this->db->where('status',1);
                                    $query = $this->db->update('tbl_students', $arrUpd);
                                    //echo "<br> str ".$this->db->last_query();exit;

                                    // Get the number of affected rows
                                    $cntUpdate = $this->db->affected_rows();
                                    //echo "<br> affected rows ".$cntUpdate." total students ".$totalStudents;exit;
                                    
                                    foreach($arrStudentIds as $studData) {
                                        $student_id = $studData['student_id'];
                                        
                                        //Save the questions in tbl_theory_answers table
                                        foreach($arrTheoryQns as $theory_qid) {
                                            $arrTheoryAnswerInsert['tb_id'] = $tb_id;
                                            $arrTheoryAnswerInsert['student_id'] = $student_id;
                                            $arrTheoryAnswerInsert['qid'] = $theory_qid;
                                            $arrTheoryAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                            
                                            $this->db->insert('tbl_theory_answers', $arrTheoryAnswerInsert);
                                        }

                                        $updData = array();
                                        $updData['theory_answers_record_generated'] = 1;
                                        $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                        
                                        //Generate PracticalActivity Answers records
                                        if(array_key_exists('PracticalActivity',$arrFinalNosWiseQns) && count($arrFinalNosWiseQns['PracticalActivity']) > 0) {
                                            foreach($arrFinalNosWiseQns['PracticalActivity'] as $practical_qid) {
                                                //Save the questions in tbl_practical_activity_answers table
                                                $arrPracticalAnswerInsert['tb_id'] = $tb_id;
                                                $arrPracticalAnswerInsert['student_id'] = $student_id;
                                                $arrPracticalAnswerInsert['qid'] = $practical_qid;
                                                $arrPracticalAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_practical_activity_answers', $arrPracticalAnswerInsert);
                                            }

                                            $updData = array();
                                            $updData['practicalactivity_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                        }
                                        //Generate Viva Answers records
                                        if(array_key_exists('Viva',$arrFinalNosWiseQns) && count($arrFinalNosWiseQns['Viva']) > 0) {
                                            foreach($arrFinalNosWiseQns['Viva'] as $viva_qid) {
                                                $arrVivaInsert['tb_id'] = $tb_id;
                                                $arrVivaInsert['student_id'] = $student_id;
                                                $arrVivaInsert['qid'] = $viva_qid;
                                                $arrVivaInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_viva_answers', $arrVivaInsert);
                                            } 
                                            $updData = array();
                                            $updData['viva_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                        }
                                    }    

                                    if($cntUpdate == $totalStudents) {
                                        $arrUpdBatch['qp_generated_status'] = 1;
                                        $this->db->where('tb_id', $tb_id);
                                        $query = $this->db->update('tbl_training_batches', $arrUpdBatch);
                                        //echo "<br> str ".$this->db->last_query();

                                        $type = 'success'; // Data was inserted successfully
                                        $message = 'Questions Generated Successfully';
                                    }
                                }    
                            } 
                            else if($qp_shuffling == 'Shuffled') { //Same qns with shuffled order will be saved in to all students of the batch
                                if($arrStudentIds != false) {
                                    foreach($arrStudentIds as $studData) {
                                        $arrUpd = array();
                                        $student_id = $studData['student_id'];

                                        shuffle($arrTheoryQns);
                                        shuffle($arrFinalNosWiseQns['PracticalActivity']);
                                        shuffle($arrFinalNosWiseQns['Viva']);

                                        $arrUpd['theory_questions'] = implode(',',$arrTheoryQns);    
                                        $arrUpd['theory_qns'] =  implode(',',$arrFinalNosWiseQns['Theory']);     
                                        $arrUpd['practical_skill_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalSkill']);    
                                        $arrUpd['practical_activity_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalActivity']);   
                                        $arrUpd['viva_questions'] =  implode(',',$arrFinalNosWiseQns['Viva']);
                                        $arrUpd['qp_generated_status'] = 1;

                                        $this->db->where('tb_id', $tb_id);
                                        $this->db->where('student_id', $student_id);
                                        $query = $this->db->update('tbl_students', $arrUpd);

                                        if ($this->db->affected_rows() > 0) {
                                            $cntUpdate++;
                                            
                                            //Save the questions in tbl_theory_answers table
                                            foreach($arrTheoryQns as $theory_qid) {
                                                $arrTheoryAnswerInsert['tb_id'] = $tb_id;
                                                $arrTheoryAnswerInsert['student_id'] = $student_id;
                                                $arrTheoryAnswerInsert['qid'] = $theory_qid;
                                                $arrTheoryAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_theory_answers', $arrTheoryAnswerInsert);
                                            }
    
                                            $updData = array();
                                            $updData['theory_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            
                                            //Generate PracticalActivity Answers records
                                            if(count($arrFinalNosWiseQns['PracticalActivity']) > 0) {
                                                foreach($arrFinalNosWiseQns['PracticalActivity'] as $practical_qid) {
                                                    //Save the questions in tbl_practical_activity_answers table
                                                    $arrPracticalAnswerInsert['tb_id'] = $tb_id;
                                                    $arrPracticalAnswerInsert['student_id'] = $student_id;
                                                    $arrPracticalAnswerInsert['qid'] = $practical_qid;
                                                    $arrPracticalAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_practical_activity_answers', $arrPracticalAnswerInsert);
                                                }

                                                $updData = array();
                                                $updData['practicalactivity_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                            //Generate Viva Answers records
                                            if(count($arrFinalNosWiseQns['Viva']) > 0) {
                                                foreach($arrFinalNosWiseQns['Viva'] as $viva_qid) {
                                                    $arrVivaInsert['tb_id'] = $tb_id;
                                                    $arrVivaInsert['student_id'] = $student_id;
                                                    $arrVivaInsert['qid'] = $viva_qid;
                                                    $arrVivaInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_viva_answers', $arrVivaInsert);
                                                } 
                                                $updData = array();
                                                $updData['viva_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                        }
                                    }
                                    if($cntUpdate == $totalStudents) {
                                        $arrUpdBatch['qp_generated_status'] = 1;
                                        $this->db->where('tb_id', $tb_id);
                                        $query = $this->db->update('tbl_training_batches', $arrUpdBatch);

                                        $type = 'success'; // Data was inserted successfully
                                        $message = 'Questions Generated Successfully';
                                    }
                                }
                            }
                            else if($qp_shuffling == 'Random') { //Random qns will be saved for each of the students of the batch
                                /*echo "<pre>";
                                print_r($arrRandomQnMarks); 
                                echo "</pre>";*/

                                $arrRandomMarksQuestions = $arrRandomNosQnTypeMarks;

                                /*echo "<pre>";
                                print_r($arrRandomNosQnTypeMarks);
                                echo "</pre>";
                                exit;*/

                                if($arrStudentIds != false) {
                                    foreach($arrStudentIds as $studData) {
                                        $arrUpd = array();
                                        $student_id = $studData['student_id'];

                                        //echo "<br><br> student_id ".$student_id;

                                        $arrFinalNosWiseQns = array();

                                        foreach($arrRandomQnMarks as $nosId => $arrRandomData) {
                                            //echo "<br><br> Nosid ".$nosId;
                                            foreach($arrRandomData as $questionType => $arrRandomData1) {
                                                foreach($arrRandomData1 as $questionMarks => $arrReqQn) {
                                                    $totalQnsPick = count($arrReqQn);
                                                    //echo "<br> questionType ".$questionType." marks ".$questionMarks." Req Qns ".$totalQnsPick;

                                                    if(count($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks]) < $totalQnsPick) {
                                                        // Assign values from main array to sub array if not already set in sub array
                                                        foreach ($arrRandomNosQnTypeMarks[$nosId][$questionType][$questionMarks] as $mainQid) {
                                                            if (!in_array($mainQid, $arrRandomMarksQuestions[$nosId][$questionType][$questionMarks])) {
                                                                $arrRandomMarksQuestions[$nosId][$questionType][$questionMarks][] = $mainQid;
                                                            }
                                                        }
                                                    }

                                                    /*echo "<pre>";
                                                    print_r($arrRandomMarksQuestions);
                                                    echo "</pre>";*/

                                                    for($i=0;$i<$totalQnsPick;$i++) {
                                                        shuffle($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks]);
            
                                                        // Pick one random value
                                                        $randomQnIndex = array_rand($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks]);
                                                        $randomQnId = $arrRandomMarksQuestions[$nosId][$questionType][$questionMarks][$randomQnIndex];
                
                                                        //Unset that array key
                                                        unset($arrRandomMarksQuestions[$nosId][$questionType][$questionMarks][$randomQnIndex]);
                
                                                        //echo "<br>randomQnIndex: ".$randomQnIndex." random Qid ".$randomQnId;
        
                                                        $arrFinalNosWiseQns[$questionType][$randomQnId] = $randomQnId;
                                                    }
                                                }
                                            }
                                        }

                                        $arrTheoryQns = array_merge($arrFinalNosWiseQns['Theory'], $arrFinalNosWiseQns['PracticalSkill']);

                                        shuffle($arrTheoryQns);
                                        shuffle($arrFinalNosWiseQns['PracticalActivity']);
                                        shuffle($arrFinalNosWiseQns['Viva']);

                                        $arrUpd['theory_questions'] = implode(',',$arrTheoryQns);    
                                        $arrUpd['theory_qns'] =  implode(',',$arrFinalNosWiseQns['Theory']);     
                                        $arrUpd['practical_skill_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalSkill']);    
                                        $arrUpd['practical_activity_questions'] =  implode(',',$arrFinalNosWiseQns['PracticalActivity']);   
                                        $arrUpd['viva_questions'] =  implode(',',$arrFinalNosWiseQns['Viva']);
                                        $arrUpd['qp_generated_status'] = 1;

                                        /*echo "<pre>";
                                        print_r($arrFinalNosWiseQns);
                                        echo "</pre>";*/

                                        $this->db->where('tb_id', $tb_id);
                                        $this->db->where('student_id', $student_id);
                                        $query = $this->db->update('tbl_students', $arrUpd);

                                        if ($this->db->affected_rows() > 0) {
                                            $cntUpdate++;
                                            
                                            //Save the questions in tbl_theory_answers table
                                            foreach($arrTheoryQns as $theory_qid) {
                                                $arrTheoryAnswerInsert['tb_id'] = $tb_id;
                                                $arrTheoryAnswerInsert['student_id'] = $student_id;
                                                $arrTheoryAnswerInsert['qid'] = $theory_qid;
                                                $arrTheoryAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                
                                                $this->db->insert('tbl_theory_answers', $arrTheoryAnswerInsert);
                                            }
    
                                            $updData = array();
                                            $updData['theory_answers_record_generated'] = 1;
                                            $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);

                                            //Generate PracticalActivity Answers records
                                            if(count($arrFinalNosWiseQns['PracticalActivity']) > 0) {
                                                foreach($arrFinalNosWiseQns['PracticalActivity'] as $practical_qid) {
                                                    //Save the questions in tbl_practical_activity_answers table
                                                    $arrPracticalAnswerInsert['tb_id'] = $tb_id;
                                                    $arrPracticalAnswerInsert['student_id'] = $student_id;
                                                    $arrPracticalAnswerInsert['qid'] = $practical_qid;
                                                    $arrPracticalAnswerInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_practical_activity_answers', $arrPracticalAnswerInsert);
                                                }

                                                $updData = array();
                                                $updData['practicalactivity_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                            //Generate Viva Answers records
                                            if(count($arrFinalNosWiseQns['Viva']) > 0) {
                                                foreach($arrFinalNosWiseQns['Viva'] as $viva_qid) {
                                                    $arrVivaInsert['tb_id'] = $tb_id;
                                                    $arrVivaInsert['student_id'] = $student_id;
                                                    $arrVivaInsert['qid'] = $viva_qid;
                                                    $arrVivaInsert['created_dts'] = date('Y-m-d H:i:s');
                                                    
                                                    $this->db->insert('tbl_viva_answers', $arrVivaInsert);
                                                } 
                                                $updData = array();
                                                $updData['viva_answers_record_generated'] = 1;
                                                $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);
                                            }
                                        }
                                    }
                                    if($cntUpdate == $totalStudents) {
                                        $arrUpdBatch['qp_generated_status'] = 1;
                                        $this->db->where('tb_id', $tb_id);
                                        $query = $this->db->update('tbl_training_batches', $arrUpdBatch);

                                        $type = 'success'; // Data was inserted successfully
                                        $message = 'Questions Generated Successfully';
                                    }
                                }
                            }
                        }
                        else {
                            $type = "error";
                            $message = "Couldn't generate Question Bank mismatch with matrix.";
                        }
                    } //End count($arrQnIds)
                    else {
                        $type = "error";
                        $message = "Couldn't generate Question Bank.No sufficient questions.";
                    }
                }    
            } //End Nos list
        }
        else {
            $type = "error";
            $message = "Nos not mapped to the trade selected for the batch.";
        }

        if(count($arrErrorMessage) > 0) {
            foreach($arrErrorMessage as $nosId => $arrQnError) {
                foreach($arrQnError as $qnError) {
                    $generate_qb_message_error  .= "<tr class='showError'><td><b>".strtoupper($arrNosDetails[$nosId])."</b></td><td>".$qnError."</td></tr>";
                }
            }
        }

        //echo "<br> type ".$type." error ".$message;
        //exit;
        $response = array('type' => $type,'message' => $message,'generate_qb_message_error' => $generate_qb_message_error);
        echo json_encode($response);
    }

    // Function to find unique combinations of values with the given sum
    private function findUniqueCombinations($arr, $target_sum, $current_sum, $start_index, $path, &$combinations) {
        if ($current_sum == $target_sum) {
            $combinations[] = $path;
            return;
        }

        for ($i = $start_index; $i < count($arr); $i++) {
            // Check if the current element is already included in the path
            if ($i > $start_index && $arr[$i] == $arr[$i - 1]) {
                continue; // Skip duplicates
            }

            // Include the current element in the path
            $new_path = $path;
            $new_path[] = $arr[$i];

            // Recursively check unique combinations with the current element
            $this->findUniqueCombinations($arr, $target_sum, $current_sum + $arr[$i], $i + 1, $new_path, $combinations);
        }
    }

    public function processBatchResult() {
        $tb_id = $this->input->post('tb_id');
        $trade_id = $this->input->post('trade_id');
        $total_max_marks = 0;
        $pass_percentage = 0;
        $type = 'error';

        //Check whether any candidate student_attendance is Pending, If Yes mark Result as Pending
        $condition = "student_attendance = 'Pending' AND tb_id = ".$tb_id;
        $arr_pending_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
        //echo "<br> str ".$this->db->last_query();exit;
        if($arr_pending_students != false) {
            $type = 'error';
            $message = 'Attendance is not marked for '.count($arr_pending_students).' students';
        }
        else {
            $condition = "trade_id = ".$trade_id;
            $arr_trade = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_id','ASC');
            if($arr_trade != false) {
                $pass_percentage = $arr_trade[0]['pass_percentage'];
                $total_max_marks = $arr_trade[0]['total_marks'];
                $arr_optional_exam_type = explode(",",$arr_trade[0]['optional_exam_type']);
            }    

            $arrStudentAttendance = array();
            $arrStudentTheoryResults = array();
            $arrStudentPracticalActivityResults = array();
            $arrStudentVivaResults = array();
            $arrStudentResults = array();

            //Get All students who are Present for the assessment
            $condition = "student_attendance = 'Present' AND tb_id = ".$tb_id;
            $arr_present_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
            if($arr_present_students != false) {
                foreach($arr_present_students as $stdRow) {
                    $studentId = $stdRow['student_id'];
                    $student_attendance = $stdRow['student_attendance'];

                    $arrStudentAttendance[$studentId] =  $student_attendance;
                    $arrStudentTheoryResults[$studentId]['theory_marks'] = 0;
                    $arrStudentTheoryResults[$studentId]['total_theory_marks'] =  0;
                    $arrStudentTheoryResults[$studentId]['practical_skill_marks'] = 0;
                }
            }

            //Get Result
            $arrCandidatesTheoryAnswersList = $this->batch_model->getCandidatesTheoryAnswersList($tb_id);
            //echo "<br> str ".$this->db->last_query();exit;
            if($arrCandidatesTheoryAnswersList != false) {
                foreach($arrCandidatesTheoryAnswersList as $details) {
                    $student_id = $details['student_id'];
                    $question_type = $details['question_type'];
                    $marks = $details['marks'];
                    //$student_attendance = $details['student_attendance'];

                    //$arrStudentAttendance[$student_id] =  $student_attendance;

                    if($question_type == 'Theory') {
                        $arrStudentTheoryResults[$student_id]['theory_marks'] = $marks;
                        
                        if(!array_key_exists('total_theory_marks',$arrStudentTheoryResults[$student_id])) {
                            $arrStudentTheoryResults[$student_id]['total_theory_marks'] =  $marks;
                        }
                        else {
                            $arrStudentTheoryResults[$student_id]['total_theory_marks'] +=  $marks;
                        }
                    }
                    if($question_type == 'PracticalSkill') {
                        $arrStudentTheoryResults[$student_id]['practical_skill_marks'] = $marks;
                    }
                }
            }

            if(in_array('practicalActivity',$arr_optional_exam_type)) {
                $arrCandidatesPracticalActivityAnswersList = $this->batch_model->getCandidatesPracticalActivityAnswersList($tb_id);
                if($arrCandidatesPracticalActivityAnswersList != false) {
                    foreach($arrCandidatesPracticalActivityAnswersList as $pa_details) {
                        $student_id = $pa_details['student_id'];
                        $marks = $pa_details['marks'];
                        
                        if(!array_key_exists($student_id,$arrStudentPracticalActivityResults)) {
                            $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'] =  $marks;
                        }
                        else {
                            $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'] +=  $marks;
                        }
                    }
                }
            }
            
            if(in_array('viva',$arr_optional_exam_type)) {
                $arrCandidatesVivaAnswersList = $this->batch_model->getCandidatesVivaAnswersList($tb_id);
                if($arrCandidatesVivaAnswersList != false) {
                    foreach($arrCandidatesVivaAnswersList as $viva_details) {
                        $student_id = $viva_details['student_id'];
                        $marks = $viva_details['marks'];
                        
                        if(!array_key_exists($student_id,$arrStudentVivaResults)) {
                            $arrStudentVivaResults[$student_id]['viva_marks'] =  $marks;
                        }
                        else {
                            $arrStudentVivaResults[$student_id]['viva_marks'] +=  $marks;
                        }
                    }
                }
            }    
            
            /*echo "<pre>";
            print_r($arrStudentTheoryResults);
            print_r($arrStudentPracticalActivityResults);
            print_r($arrStudentVivaResults);
            echo "</pre>";
            exit;*/

            if(count($arrStudentTheoryResults) > 0) {
                foreach($arrStudentTheoryResults as $student_id => $arr_result) {
                    $total_theory_marks = $arr_result['total_theory_marks'];

                    $total_practical_marks = 0;
                    
                    if(array_key_exists('practical_skill_marks',$arr_result)) {
                        $total_practical_marks += $arr_result['practical_skill_marks'];
                    }
                    if(in_array('practicalActivity',$arr_optional_exam_type) && array_key_exists($student_id,$arrStudentPracticalActivityResults)) {
                        $total_practical_marks += $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'];
                    }
                    if(in_array('viva',$arr_optional_exam_type) && array_key_exists($student_id,$arrStudentVivaResults)) {
                        $total_practical_marks += $arrStudentVivaResults[$student_id]['viva_marks'];
                    }  

                    $total_marks = $total_theory_marks + $total_practical_marks;

                    //Calculate percentage if the candidate is present
                    if($arrStudentAttendance[$student_id] != 'Absent') {
                        $marks_percentage = ($total_max_marks > 0) ? (($total_marks / $total_max_marks) * 100) : 0;
                        $result = ($marks_percentage >= $pass_percentage) ? 'Pass' : 'Fail';
                    }
                    else {
                        $marks_percentage = 0;
                        $result = 'Absent';
                    }
                    
                    
                    $arr_result['practical_activity_marks'] = array_key_exists($student_id,$arrStudentPracticalActivityResults) ? $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'] : 0;
                    $arr_result['viva_marks'] = array_key_exists($student_id,$arrStudentVivaResults) ? $arrStudentVivaResults[$student_id]['viva_marks'] : 0;
                    $arr_result['total_practical_marks'] = $total_practical_marks;
                    $arr_result['total_marks'] = $total_marks;
                    $arr_result['marks_percentage'] = $marks_percentage;
                    $arr_result['pass_percentage'] = $pass_percentage;
                    $arr_result['result'] = $result;
                    
                    $this->db->where('student_id', $student_id);
                    $query = $this->db->update('tbl_students', $arr_result);
                }

                //update batches table to mark the processing as completed
                $arr_update['result_processing'] = 'Completed';
                $this->db->where('tb_id', $tb_id);
                $query = $this->db->update('tbl_training_batches', $arr_update);
            }
            
            $type = 'success';
            $message = 'Results processed successfully';
        }    

        $response = array('type' => $type,'message' => $message);
        echo json_encode($response);
    }

    public function downloadBatchAssessmentDocuments($tb_id = 0,$batch_id = "")
    {
		$file_path = "./".$this->config->item('assessors_checklist_documents_path');
		
		$arrChecklistDocumentsDetails = $this->batch_model->getChecklistDocumentsDetailsFileUploaded($tb_id);
		
		if($arrChecklistDocumentsDetails != false)
		{
			$zip = new ZipArchive();

			$tmp_file = tempnam('.','');
			$zip->open($tmp_file, ZipArchive::CREATE);

			foreach($arrChecklistDocumentsDetails as $file)
			{
				$file_path_full = $file_path . $file['document_file_uploaded'];

                if (file_exists($file_path_full)) {
                    $download_file = file_get_contents($file_path_full);
                    $zip->addFromString(basename($file_path_full),$download_file);
                }
                
                //$download_file = file_get_contents($file_path.$file['document_file_uploaded']);
				//$zip->addFromString(basename($file_path.$file['document_file_uploaded']),$download_file);
			}

			$zip->close();

			$file_name = $batch_id."-assessment-documents-".date('YmdHis').".zip";
			header('Content-disposition: attachment; filename='.$file_name);
			header('Content-type: application/zip');
			readfile($tmp_file);
		}
	}

    public function viewBatchAssessmentDocuments($tb_id = 0,$batch_id = "")
    {
		$arr_checklist_uploaded_details = array();

        $arrChecklistDocumentsDetails = $this->batch_model->getBatchChecklistDocumentsDetails($tb_id);
		
		if($arrChecklistDocumentsDetails != false)
		{
			foreach($arrChecklistDocumentsDetails as $checklist_data) {
                $arr_checklist_uploaded_details[$checklist_data['acdm_id']]['document_file_uploaded'] =  $checklist_data['document_file_uploaded'];
                $arr_checklist_uploaded_details[$checklist_data['acdm_id']]['document_description'] =  $checklist_data['document_description'];
                $arr_checklist_uploaded_details[$checklist_data['acdm_id']]['watermarking_error'] =  $checklist_data['watermarking_error'];
            }
		}

        $condition = "status = 1";
        $data['arr_checklist_documents_master'] = $this->Mdmaster->getAllRecords('tbl_assessment_checklist_documents_master',$condition,'acdm_id','ASC');
        $data['arr_checklist_uploaded_details'] = $arr_checklist_uploaded_details;
        $data['tb_id'] = $tb_id;
        $data['batch_id'] = $batch_id;

        $this->render_page('admin/batch/list-assessment-documents',$data);
	}
	
	public function downloadBatchAssessmentPhotos($tb_id = 0,$batch_id = "")
    {
		$condition = "student_attendance = 'Present' AND tb_id = ".$tb_id;
        $arr_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
        
        $condition = "tb_id = ".$tb_id;
        $arr_batch_details = $this->Mdmaster->getAllRecords('tbl_training_batches',$condition,'tb_id','ASC');
        if($arr_batch_details != false) {
            $zip = new ZipArchive();

    		$tmp_file = tempnam('.','');
    		$zip->open($tmp_file, ZipArchive::CREATE);
    		
    		$file_path = "./".$this->config->item('assessors_assements_path');
    		
    		$center_building_photo = $arr_batch_details[0]['center_building_photo'];
            $selfie_with_center_board = $arr_batch_details[0]['selfie_with_center_board'];
            
            if($center_building_photo != "") {
                $center_building_photo_download = $this->sanitizeFileName($center_building_photo);

                $download_file = file_get_contents($file_path.$center_building_photo);
			    $zip->addFromString(basename($file_path.$center_building_photo_download),$download_file);
            }
    		if($selfie_with_center_board != "") {
                $selfie_with_center_board_download = $this->sanitizeFileName($selfie_with_center_board);

                $download_file = file_get_contents($file_path.$selfie_with_center_board);
			    $zip->addFromString(basename($file_path.$selfie_with_center_board_download),$download_file);
            }
            
            if($arr_students != false) {
                $file_path = "./".$this->config->item('aadhaar_filename_path');
                $student_photo_file_path = "./".$this->config->item('student_photo_path');
                foreach($arr_students as $details) {
                    $student_photo = $details['student_photo'];
                    $aadhar_front_filename = $details['aadhar_front_filename'];
                    $aadhar_back_filename = $details['aadhar_back_filename'];
                    $student_photo_with_aadhar = $details['student_photo_with_aadhar'];
                    
                    if($aadhar_back_filename != "") {
                        $aadhar_back_filename_download = $this->sanitizeFileName($aadhar_back_filename);

                        $download_file = file_get_contents($file_path.$aadhar_back_filename);
        			    $zip->addFromString(basename($file_path.$aadhar_back_filename_download),$download_file);
                    }
                    if($aadhar_front_filename != "") {
                        $aadhar_front_filename_download = $this->sanitizeFileName($aadhar_front_filename);

                        $download_file = file_get_contents($file_path.$aadhar_front_filename);
        			    $zip->addFromString(basename($file_path.$aadhar_front_filename_download),$download_file);
                    }
                    if($student_photo_with_aadhar != "") {
                        $student_photo_with_aadhar_download = $this->sanitizeFileName($student_photo_with_aadhar);

                        $download_file = file_get_contents($file_path.$student_photo_with_aadhar);
        			    $zip->addFromString(basename($file_path.$student_photo_with_aadhar_download),$download_file);
                    }
                    if($student_photo != "") {
                        $student_photo_download = $this->sanitizeFileName($student_photo);

                        $download_file = file_get_contents($student_photo_file_path.$student_photo);
        			    $zip->addFromString(basename($student_photo_file_path.$student_photo_download),$download_file); 
                    }
                }
            }
            
            $zip->close();

    		$file_name = $batch_id."-assessment-photos-".date('YmdHis').".zip";
    		header('Content-disposition: attachment; filename='.$file_name);
    		header('Content-type: application/zip');
    		readfile($tmp_file);
        }
    }
    
    public function downloadBatchCandidatesAadhaarPhotos($tb_id = 0,$batch_id = "") 
    {
		$arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
        
		$arr_batch_student_details = $this->student_model->getBatchPresentStudentDetails($tb_id);
        if($arr_batch_student_details != false) {
            $data["arr_batch_student_details"] = $arr_batch_student_details;	
            $data["arr_batch_details"] = $arr_batch_details[0];	
            
            $html = $this->load->view('admin/batch/print_candidate_aadhaar_front_back_pdf',$data,TRUE);
            //echo $html;exit;
    
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            // Download PDF file                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
            $mpdf->Output($batch_id.'_Candidate_aadhaar_front_back_photos.pdf', 'I');
        }
	}
	
	public function downloadBatchCandidatesWithAadhaarPhotos($tb_id = 0,$batch_id = "")
    {
		$arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
		
		$arr_batch_student_details = $this->student_model->getBatchPresentStudentDetails($tb_id);
        if($arr_batch_student_details != false) {
            $data["arr_batch_student_details"] = $arr_batch_student_details;
            $data["arr_batch_details"] = $arr_batch_details[0];	
            
            $html = $this->load->view('admin/batch/print_candidate_photos_with_aadhaar_pdf',$data,TRUE);
            //echo $html;exit;
    
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            // Download PDF file
            $mpdf->Output($batch_id.'_Candidate_photos_with_aadhaar.pdf', 'I');
        }
	}

    public function downloadBatchGroupPhotos($tb_id = 0,$batch_id = "")
    {
		$arr_batch_details = $this->batch_model->getBatchCompleteDetails($tb_id);
        $arr_batch_checklist_group_details = $this->batch_model->getBatchChecklistDocumentsGroupPhotos($tb_id);
        //echo "<br> str ".$this->db->last_query();exit;

        if($arr_batch_checklist_group_details != false) {
            $arrCategoryMaster = array();
            $arrDocumentMaster = array();
            $arrGroupDocDetails = array();

            foreach($arr_batch_checklist_group_details as $row) {
                $categoryName = $row['category'];
                $documentTitle = $row['document_title'];
                $documentFileUploaded = $row['document_file_uploaded'];

                $arrCategoryMaster[$row['checklist_cat_id']]                    = $categoryName;
                $arrDocumentMaster[$row['acdm_id']]                             = $documentTitle;
                $arrGroupDocDetails[$row['checklist_cat_id']][$row['acdm_id']]  = $documentFileUploaded;
            }

            /*echo "<pre>";
            print_r($arrCategoryMaster);
            print_r($arrDocumentMaster);
            print_r($arrGroupDocDetails);
            echo "</pre>";
            exit;*/
            
            if(count($arrCategoryMaster) > 0) {
                $data["arrDocumentMaster"] = $arrDocumentMaster;
                $data["arr_batch_details"] = $arr_batch_details[0];	
                // Array to hold generated PDF file paths
                $pdfFiles = [];

                foreach($arrCategoryMaster as $checklist_cat_id => $category) {
                    $data["category"]           = $category;	
                    $data["arrGroupDocDetails"] = $arrGroupDocDetails[$checklist_cat_id];	
                    $html = $this->load->view('admin/batch/print_group_photos_pdf',$data,TRUE);
                    //echo $html;

                    $pdfFilePath = './uploads/group_photos/' . $batch_id."_".$category.".pdf"; // Save in the root folder

                    // Create PDF using mPDF
                    $mpdf = new \Mpdf\Mpdf();
                    $mpdf->WriteHTML($html);
                    $mpdf->Output($pdfFilePath, 'F'); // Save to file

                    // Store the file path
                    $pdfFiles[] = $pdfFilePath;
                }
            }
            
            // Create a zip file
            $zip = new ZipArchive();
            $zipfilename = $batch_id . '_group_photos.zip';
            $zipFilePath = './uploads/group_photos/' . $zipfilename;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
                exit("Cannot open <$zipFilePath>\n");
            }

            // Add PDF files to zip
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();

            // Delete the generated PDF files
            foreach ($pdfFiles as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Serve the zip file for download using a more reliable method
            if (file_exists($zipFilePath)) {
                // Set headers for download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="'.$zipfilename.'"');
                header('Content-Length: ' . filesize($zipFilePath));

                // Flush output buffer to avoid header issues
                ob_clean();
                flush();

                // Read the zip file and output it to the browser
                readfile($zipFilePath);

                // Delete the zip file after download
                unlink($zipFilePath);

                // Stop further execution after the file download
                exit();
            }
        }
	}

    public function downloadBatchCenterPhotos($photo)
    {
		// Load the download helper
        $this->load->helper('download');

        // Path to the image
        $filepath = $this->config->item('assessors_assements_path').$photo;
        //echo "<br> file path ".$filepath;exit;
    
        // Check if file exists
        if (file_exists($filepath)) {
            // Force download
            $new_name = $this->sanitizeFileName($photo);
            force_download($new_name, file_get_contents($filepath)); 
        } else {
            show_404(); // Show a 404 error if the file does not exist
        }
	}

    protected function sanitizeFileName($fileName) { 
        $parts = explode('-', $fileName);
 
        // Use a regular expression to replace the 14-digit segment - To replace datetime 
        $new_filename = preg_replace('/-\d{14}-/', '-', $fileName);
        
        return $new_filename;
    }

    public function processStudentResult() {
        $student_id = 3136;;
        $tb_id = 122;
        $trade_id = 12;
        $total_max_marks = 0;
        $pass_percentage = 0;
        $type = 'error';

        //Check whether any candidate student_attendance is Pending, If Yes mark Result as Pending
        $condition = "student_attendance = 'Pending' AND tb_id = ".$tb_id;
        $arr_pending_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
        //echo "<br> str ".$this->db->last_query();exit;
        if($arr_pending_students != false) {
            $type = 'error';
            $message = 'Attendance is not marked for '.count($arr_pending_students).' students';
        }
        else {
            $condition = "trade_id = ".$trade_id;
            $arr_trade = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_id','ASC');
            if($arr_trade != false) {
                $pass_percentage = $arr_trade[0]['pass_percentage'];
                $total_max_marks = $arr_trade[0]['total_marks'];
                $arr_optional_exam_type = explode(",",$arr_trade[0]['optional_exam_type']);
            }    

            $arrStudentAttendance = array();
            $arrStudentTheoryResults = array();
            $arrStudentPracticalActivityResults = array();
            $arrStudentVivaResults = array();
            $arrStudentResults = array();

            //Get All students who are Present for the assessment
            $condition = "student_attendance = 'Present' AND tb_id = ".$tb_id;
            $arr_present_students = $this->Mdmaster->getAllRecords('tbl_students',$condition,'student_id','ASC');
            if($arr_present_students != false) {
                foreach($arr_present_students as $stdRow) {
                    $studentId = $stdRow['student_id'];
                    $student_attendance = $stdRow['student_attendance'];

                    $arrStudentAttendance[$studentId] =  $student_attendance;
                    $arrStudentTheoryResults[$studentId]['theory_marks'] = 0;
                    $arrStudentTheoryResults[$studentId]['total_theory_marks'] =  0;
                    $arrStudentTheoryResults[$studentId]['practical_skill_marks'] = 0;
                }
            }

            //Get Result
            $arrCandidatesTheoryAnswersList = $this->batch_model->getCandidatesTheoryAnswersList($tb_id,$student_id);
            //echo "<br> str ".$this->db->last_query();exit;
            if($arrCandidatesTheoryAnswersList != false) {
                foreach($arrCandidatesTheoryAnswersList as $details) {
                    $student_id = $details['student_id'];
                    $question_type = $details['question_type'];
                    $marks = $details['marks'];
                    
                    if($question_type == 'Theory') {
                        $arrStudentTheoryResults[$student_id]['theory_marks'] = $marks;
                        
                        if(!array_key_exists('total_theory_marks',$arrStudentTheoryResults[$student_id])) {
                            $arrStudentTheoryResults[$student_id]['total_theory_marks'] =  $marks;
                        }
                        else {
                            $arrStudentTheoryResults[$student_id]['total_theory_marks'] +=  $marks;
                        }
                    }
                    if($question_type == 'PracticalSkill') {
                        $arrStudentTheoryResults[$student_id]['practical_skill_marks'] = $marks;
                    }
                }
            }

            if(in_array('practicalActivity',$arr_optional_exam_type)) {
                $arrCandidatesPracticalActivityAnswersList = $this->batch_model->getCandidatesPracticalActivityAnswersList($tb_id,$student_id);
                if($arrCandidatesPracticalActivityAnswersList != false) {
                    foreach($arrCandidatesPracticalActivityAnswersList as $pa_details) {
                        $student_id = $pa_details['student_id'];
                        $marks = $pa_details['marks'];
                        
                        if(!array_key_exists($student_id,$arrStudentPracticalActivityResults)) {
                            $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'] =  $marks;
                        }
                        else {
                            $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'] +=  $marks;
                        }
                    }
                }
            }
            
            if(in_array('viva',$arr_optional_exam_type)) {
                $arrCandidatesVivaAnswersList = $this->batch_model->getCandidatesVivaAnswersList($tb_id,$student_id);
                if($arrCandidatesVivaAnswersList != false) {
                    foreach($arrCandidatesVivaAnswersList as $viva_details) {
                        $student_id = $viva_details['student_id'];
                        $marks = $viva_details['marks'];
                        
                        if(!array_key_exists($student_id,$arrStudentVivaResults)) {
                            $arrStudentVivaResults[$student_id]['viva_marks'] =  $marks;
                        }
                        else {
                            $arrStudentVivaResults[$student_id]['viva_marks'] +=  $marks;
                        }
                    }
                }
            }    
            
            //echo "<pre>";
            //print_r($arr_present_students);
            //print_r($arrStudentTheoryResults);
            //print_r($arrStudentPracticalActivityResults);
            //print_r($arrStudentVivaResults);
            //echo "</pre>";
            //exit;

            if(count($arrStudentTheoryResults) > 0) {
                foreach($arrStudentTheoryResults as $student_id => $arr_result) {
                    //echo "<br> student id ".$student_id;
                    $total_theory_marks = $arr_result['total_theory_marks'];

                    $total_practical_marks = 0;
                    
                    if(array_key_exists('practical_skill_marks',$arr_result)) {
                        $total_practical_marks += $arr_result['practical_skill_marks'];
                    }
                    if(in_array('practicalActivity',$arr_optional_exam_type) && array_key_exists($student_id,$arrStudentPracticalActivityResults)) {
                        $total_practical_marks += $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'];
                    }
                    if(in_array('viva',$arr_optional_exam_type) && array_key_exists($student_id,$arrStudentVivaResults)) {
                        $total_practical_marks += $arrStudentVivaResults[$student_id]['viva_marks'];
                    }  

                    $total_marks = $total_theory_marks + $total_practical_marks;

                    //Calculate percentage if the candidate is present
                    if($arrStudentAttendance[$student_id] != 'Absent') {
                        $marks_percentage = ($total_max_marks > 0) ? (($total_marks / $total_max_marks) * 100) : 0;
                        $result = ($marks_percentage >= $pass_percentage) ? 'Pass' : 'Fail';
                    }
                    else {
                        $marks_percentage = 0;
                        $result = 'Absent';
                    }
                    
                    
                    $arr_result['practical_activity_marks'] = array_key_exists($student_id,$arrStudentPracticalActivityResults) ? $arrStudentPracticalActivityResults[$student_id]['practical_activity_marks'] : 0;
                    $arr_result['viva_marks'] = array_key_exists($student_id,$arrStudentVivaResults) ? $arrStudentVivaResults[$student_id]['viva_marks'] : 0;
                    $arr_result['total_practical_marks'] = $total_practical_marks;
                    $arr_result['total_marks'] = $total_marks;
                    $arr_result['marks_percentage'] = $marks_percentage;
                    $arr_result['pass_percentage'] = $pass_percentage;
                    $arr_result['result'] = $result;

                    /*echo "<pre>";
                    print_r($arr_result);
                    echo "</pre>";*/
                    //exit;
                    
                    $this->db->where('student_id', $student_id);
                    $query = $this->db->update('tbl_students', $arr_result);
                }
            }
            
            $type = 'success';
            $message = 'Results processed successfully';
        }    

        $response = array('type' => $type,'message' => $message);
        echo json_encode($response);
    }
}