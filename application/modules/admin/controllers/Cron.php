<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends MY_Controller
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
        $this->load->model('mainModel');
    }
	
	public function watermarkVideoRecording()
    {
		$this->db->select('student_id,practicalactivity_video_file,practicalactivity_video_lat,practicalactivity_video_lng,practicalactivity_video_geoaddress,practicalactivity_video_watermark_status,practicalactivity_video_submitted_dts,
                            viva_video_file,viva_video_watermark_status,viva_video_lat,viva_video_lng,viva_video_geoaddress,viva_video_watermark_status,viva_video_submitted_dts,
                            batch_id,assessor_code'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');
        $this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id');
        $this->db->where('student_assessment_status', 'Completed');
        $this->db->where('practicalactivity_video_watermark_status', 0);
        $this->db->or_where('viva_video_watermark_status', 0);
        $this->db->limit(1);
        $query=$this->db->get('tbl_students');
    	$arr_student_details = $query->result_array();
        //echo "<br> str ".$this->db->last_query();exit; 
    	if(count($arr_student_details) > 0){ 
    		$student_id = $arr_student_details[0]['student_id'];
            $batch_id = $arr_student_details[0]['batch_id'];
            $assessor_code = $arr_student_details[0]['assessor_code'];

            $practicalactivity_video_file = $arr_student_details[0]['practicalactivity_video_file'];
            $practicalactivity_video_lat = $arr_student_details[0]['practicalactivity_video_lat'];
            $practicalactivity_video_lng = $arr_student_details[0]['practicalactivity_video_lng'];
            $practicalactivity_video_geoaddress = $arr_student_details[0]['practicalactivity_video_geoaddress'];
            $practicalactivity_video_watermark_status = $arr_student_details[0]['practicalactivity_video_watermark_status'];
            $practicalactivity_video_submitted_dts = $arr_student_details[0]['practicalactivity_video_submitted_dts'];

            $viva_video_file = $arr_student_details[0]['viva_video_file'];
            $viva_video_lat = $arr_student_details[0]['viva_video_lat'];
            $viva_video_lng = $arr_student_details[0]['viva_video_lng'];
            $viva_video_geoaddress = $arr_student_details[0]['viva_video_geoaddress'];
            $viva_video_watermark_status = $arr_student_details[0]['viva_video_watermark_status'];
            $viva_video_submitted_dts = $arr_student_details[0]['viva_video_submitted_dts'];

            if($practicalactivity_video_file != "" && $practicalactivity_video_watermark_status == 0) {
                $file = './uploads/student_assessment_videos/'.$practicalactivity_video_file;
                //echo "<br> file ".$file;exit;
                if (file_exists($file)) {
                    // Attempt to watermark the video
                    // Define input and output file names
                    $input_file = $file;
                    $output_file = './uploads/student_assessment_watermarked_videos/'.$practicalactivity_video_file;

                    if (file_exists($output_file)) {
                        unlink($output_file);
                    }    
                    
                    $output = watermarkVideo($input_file,$output_file,$batch_id,$assessor_code,$practicalactivity_video_lat,$practicalactivity_video_lng,$practicalactivity_video_geoaddress,$practicalactivity_video_submitted_dts);
                
                    //echo "<br> Output ".$output;
                    if($output == 'Success') {
                        //Update tbl_students
                        $updData['practicalactivity_video_watermark_status'] = 1; 
                    }
                    else {
                        //Update tbl_students
                        $updData['practicalactivity_video_watermark_status'] = 2; //Error
                    }

                    $this->db->where('student_id', $student_id);
                    $this->db->update('tbl_students', $updData); 

                    //Insert into tbl_cron_video_watermarking
                    $arrInsert['student_id'] = $student_id;
                    $arrInsert['batch_id'] = $batch_id;
                    $arrInsert['assessor_code'] = $assessor_code;
                    $arrInsert['video_type'] = 'practicalactivity';
                    $arrInsert['status'] = ($output == 'Success') ? 'Success' : 'Error';
                    $arrInsert['response'] = ($output == 'Success') ? 'Success' : $output;
                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_cron_video_watermarking', $arrInsert); 
                }
                else {
                    //Insert into tbl_cron_video_watermarking
                    $arrInsert['student_id'] = $student_id;
                    $arrInsert['batch_id'] = $batch_id;
                    $arrInsert['assessor_code'] = $assessor_code;
                    $arrInsert['video_type'] = 'practicalactivity';
                    $arrInsert['status'] = 'Error';
                    $arrInsert['response'] = 'Video File does not exist';
                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_cron_video_watermarking', $arrInsert);
                }
            }
            if($viva_video_file != "" && $viva_video_watermark_status == 0) {
                $file = './uploads/student_assessment_videos/'.$viva_video_file;
                //echo "<br> file ".$file;exit;
                if (file_exists($file)) {
                    // Attempt to watermark the video
                    // Define input and output file names
                    $input_file = $file;
                    $output_file = './uploads/student_assessment_watermarked_videos/'.$viva_video_file;

                    if (file_exists($output_file)) {
                        unlink($output_file);
                    }    
                    
                    $output = watermarkVideo($input_file,$output_file,$batch_id,$assessor_code,$viva_video_lat,$viva_video_lng,$viva_video_geoaddress,$viva_video_submitted_dts);
                
                    //echo "<br> Output ".$output;
                    if($output == 'Success') {
                        //Update tbl_students
                        $updData['viva_video_watermark_status'] = 1; 
                    }
                    else {
                        //Update tbl_students
                        $updData['viva_video_watermark_status'] = 2; //Error
                    }

                    $this->db->where('student_id', $student_id);
                    $this->db->update('tbl_students', $updData); 

                    //Insert into tbl_cron_video_watermarking
                    $arrInsert['student_id'] = $student_id;
                    $arrInsert['batch_id'] = $batch_id;
                    $arrInsert['assessor_code'] = $assessor_code;
                    $arrInsert['video_type'] = 'viva';
                    $arrInsert['status'] = ($output == 'Success') ? 'Success' : 'Error';
                    $arrInsert['response'] = ($output == 'Success') ? 'Success' : $output;
                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_cron_video_watermarking', $arrInsert); 
                }
                else {
                    //Insert into tbl_cron_video_watermarking
                    $arrInsert['student_id'] = $student_id;
                    $arrInsert['batch_id'] = $batch_id;
                    $arrInsert['assessor_code'] = $assessor_code;
                    $arrInsert['video_type'] = 'viva';
                    $arrInsert['status'] = 'Error';
                    $arrInsert['response'] = 'Video File does not exist';
                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_cron_video_watermarking', $arrInsert);
                }
            }
    	}
    }

    public function uploadFile() {
        // Directory to save the uploaded chunks
        $uploadDir = 'uploads/';
        $tempDir = 'temp/';

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunk = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : null;
        $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : null;
        $chunkIndex = isset($_POST['chunkIndex']) ? $_POST['chunkIndex'] : 0;
        $totalChunks = isset($_POST['totalChunks']) ? $_POST['totalChunks'] : 0;

        if ($chunk && $fileName) {
            $chunkFile = $tempDir . $fileName . '_' . $chunkIndex;
            move_uploaded_file($chunk, $chunkFile);

            if ($chunkIndex == $totalChunks - 1) {
                // All chunks uploaded, merge them
                $finalFile = $uploadDir . $fileName;
                $fp = fopen($finalFile, 'ab');

                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkFile = $tempDir . $fileName . '_' . $i;
                    $chunkData = file_get_contents($chunkFile);
                    fwrite($fp, $chunkData);
                    unlink($chunkFile); // Remove the chunk file after merging
                }

                fclose($fp);

                echo json_encode(['status' => 'success', 'message' => 'File uploaded and merged successfully', 'file' => $finalFile]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Chunk uploaded successfully']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid upload request']);
        }

    }

}
