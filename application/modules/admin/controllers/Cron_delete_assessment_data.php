<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cron_delete_assessment_data extends MY_Controller
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
        $this->load->model('batch_model');
    }
	
	public function index()
    {
		$this->db->select('*'); 
        $this->db->where('delete_batch', 1); 
        //$this->db->where('batch_id', '2771856');
        $this->db->limit(10);
        $query=$this->db->get('tbl_training_batches');
    	$arr_batch_details = $query->result_array();
        //echo "<br> str ".$this->db->last_query();exit;

        if(count($arr_batch_details) > 0){ 
            foreach($arr_batch_details as $details) {
                $tb_id = $details['tb_id'];
                $batch_id = $details['batch_id'];

                $file_path = "./".$this->config->item('assessors_assements_path');  
                $file_path_thumb = "./".$this->config->item('assessors_assements_path').'thumbs/';
                
                $center_building_photo = $details['center_building_photo'];
                $selfie_with_center_board = $details['selfie_with_center_board'];
                
                if($center_building_photo != "") {
                    $file = $file_path.$center_building_photo;
                    $fileThumb = $file_path_thumb.$center_building_photo;
                    //echo "<br> center_building_photo ".$file;//exit;
                    if (file_exists($file)) {
                        unlink($file);
                    } 
                    if (file_exists($fileThumb)) {
                        unlink($fileThumb);
                    } 
                }
                if($selfie_with_center_board != "") {
                    $file = $file_path.$selfie_with_center_board;
                    $fileThumb = $file_path_thumb.$selfie_with_center_board;
                    //echo "<br> selfie_with_center_board ".$file;
                    if (file_exists($file)) {
                        unlink($file);
                    } 
                    if (file_exists($fileThumb)) {
                        unlink($fileThumb);
                    } 
                }
            
                $file_path = "./".$this->config->item('assessors_checklist_documents_path');
                $file_path_thumb = "./".$this->config->item('assessors_checklist_documents_path').'thumbs/';
            
                $arrChecklistDocumentsDetails = $this->batch_model->getChecklistDocumentsDetailsFileUploaded($tb_id);
                //echo "<br> str ".$this->db->last_query();exit;
                
                if($arrChecklistDocumentsDetails != false)
                {
                    foreach($arrChecklistDocumentsDetails as $file)
                    {
                        if($file['document_file_uploaded'] != "") {
                            $file_path_full = $file_path . $file['document_file_uploaded'];
                            $fileThumb = $file_path_thumb. $file['document_file_uploaded'];
                            //echo "<br> checklist doc ".$file_path_full;

                            if (file_exists($file_path_full)) {
                                unlink($file_path_full);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }    
                    }
                }
                
                $this->db->select('*'); 
                $this->db->where('tb_id', $tb_id);
                $query=$this->db->get('tbl_students');
                $arr_student_details = $query->result_array();
                //echo "<br> str ".$this->db->last_query();exit;

                if(count($arr_student_details) > 0){ 
                    foreach($arr_student_details as $sdetails) {
                        $student_photo = $sdetails['student_photo'];
                        $aadhar_front_filename = $sdetails['aadhar_front_filename'];
                        $aadhar_back_filename = $sdetails['aadhar_back_filename'];
                        $student_photo_with_aadhar = $sdetails['student_photo_with_aadhar'];
            
                        $practicalactivity_video_file = $sdetails['practicalactivity_video_file'];
                        $viva_video_file = $sdetails['viva_video_file'];

                        $file_path = "./".$this->config->item('aadhaar_filename_path');
                        $file_path_thumb = "./".$this->config->item('aadhaar_filename_path').'thumbs/';
                        $student_photo_file_path = "./".$this->config->item('student_photo_path');
                        $student_photo_file_path_thumb = "./".$this->config->item('student_photo_path').'thumbs/';

                        if($aadhar_front_filename != "") {
                            $file = $file_path.$aadhar_front_filename;
                            $fileThumb = $file_path_thumb.$aadhar_front_filename;
                            //echo "<br> aadhar_front_filename ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        if($aadhar_back_filename != "") {
                            $file = $file_path.$aadhar_back_filename;
                            $fileThumb = $file_path_thumb.$aadhar_back_filename;
                            //echo "<br> aadhar_back_filename  ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            } 
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        if($student_photo_with_aadhar != "") {
                            $file = $file_path.$student_photo_with_aadhar;
                            $fileThumb = $file_path_thumb.$student_photo_with_aadhar;
                            //echo "<br> student_photo_with_aadhar  ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        if($student_photo != "") {
                            $file = $student_photo_file_path.$student_photo;
                            $fileThumb = $file_path_thumb.$student_photo;
                            //echo "<br> student_photo  ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        
                        if($practicalactivity_video_file != "") {
                            $file = './uploads/student_assessment_videos/'.$practicalactivity_video_file;
                            $file_path_thumb = './uploads/student_assessment_videos/thumbs/'.$practicalactivity_video_file;

                            //echo "<br> practicalactivity_video_file  ".$file;
                            //echo "<br> file ".$file;exit;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($file_path_thumb)) {
                                unlink($file_path_thumb);
                            }    
                        }
                        if($viva_video_file != "") {
                            $file = './uploads/student_assessment_videos/'.$viva_video_file;
                            $file_path_thumb = './uploads/student_assessment_videos/thumbs/'.$viva_video_file;
                            //echo "<br> viva_video_file ".$file;
                            //echo "<br> file ".$file;exit;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($file_path_thumb)) {
                                unlink($file_path_thumb);
                            }
                        }

                    }
                }

                $this->db->select('*'); 
                $this->db->where('tb_id', $tb_id);
                $query=$this->db->get('tbl_student_snapshots');
                $arr_student_snapshot_details = $query->result_array();
                //echo "<br> str ".$this->db->last_query();exit;

                if(count($arr_student_snapshot_details) > 0){ 
                    foreach($arr_student_snapshot_details as $sndetails) {
                        $snapshot_image = $sndetails['snapshot_image'];
                        
                        $file_path = "./".$this->config->item('student_snapshot_photo_path');
                        $file_path_thumb = "./".$this->config->item('student_snapshot_photo_path').'thumbs/';
                        
                        if($snapshot_image != "") {
                            $file = $file_path.$snapshot_image;
                            $fileThumb = $file_path_thumb.$snapshot_image;
                            //echo "<br> aadhar_front_filename ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                    }
                }

                $updData['delete_batch'] = 2;
                $this->db->where('tb_id', $tb_id);
                $query = $this->db->update('tbl_training_batches', $updData);

                echo "<br> Process completed for ".$batch_id; 
            }
        } 
        else {
            echo "<br> No Records";
        }   
    }

    public function deleteBatch()
    {
		$this->db->select('*'); 
        $this->db->where('delete_batch', 3);
        //$this->db->where('batch_id', 'T001');
        $this->db->limit(10);
        $query=$this->db->get('tbl_training_batches');
    	$arr_batch_details = $query->result_array();
        //echo "<br> str ".$this->db->last_query();exit;

        if(count($arr_batch_details) > 0){ 
            foreach($arr_batch_details as $details) {
                $tb_id = $details['tb_id'];
                $batch_id = $details['batch_id'];

                $file_path = "./".$this->config->item('assessors_assements_path');  
                $file_path_thumb = "./".$this->config->item('assessors_assements_path').'thumbs/';
                
                $center_building_photo = $details['center_building_photo'];
                $selfie_with_center_board = $details['selfie_with_center_board'];
                
                if($center_building_photo != "") {
                    $file = $file_path.$center_building_photo;
                    $fileThumb = $file_path_thumb.$center_building_photo;
                    //echo "<br> center_building_photo ".$file;//exit;
                    if (file_exists($file)) {
                        unlink($file);
                    } 
                    if (file_exists($fileThumb)) {
                        unlink($fileThumb);
                    } 
                }
                if($selfie_with_center_board != "") {
                    $file = $file_path.$selfie_with_center_board;
                    $fileThumb = $file_path_thumb.$selfie_with_center_board;
                    //echo "<br> selfie_with_center_board ".$file;
                    if (file_exists($file)) {
                        unlink($file);
                    } 
                    if (file_exists($fileThumb)) {
                        unlink($fileThumb);
                    } 
                }
            
                $file_path = "./".$this->config->item('assessors_checklist_documents_path');
                $file_path_thumb = "./".$this->config->item('assessors_checklist_documents_path').'thumbs/';
            
                $arrChecklistDocumentsDetails = $this->batch_model->getChecklistDocumentsDetailsFileUploaded($tb_id);
                //echo "<br> str ".$this->db->last_query();exit;
                
                if($arrChecklistDocumentsDetails != false)
                {
                    foreach($arrChecklistDocumentsDetails as $file)
                    {
                        if($file['document_file_uploaded'] != "") {
                            $file_path_full = $file_path . $file['document_file_uploaded'];
                            $fileThumb = $file_path_thumb. $file['document_file_uploaded'];
                            //echo "<br> checklist doc ".$file_path_full;

                            if (file_exists($file_path_full)) {
                                unlink($file_path_full);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }    
                    }
                }
                
                $this->db->select('*'); 
                $this->db->where('tb_id', $tb_id);
                $query=$this->db->get('tbl_students');
                $arr_student_details = $query->result_array();
                //echo "<br> str ".$this->db->last_query();exit;

                if(count($arr_student_details) > 0){ 
                    foreach($arr_student_details as $sdetails) {
                        $student_photo = $sdetails['student_photo'];
                        $aadhar_front_filename = $sdetails['aadhar_front_filename'];
                        $aadhar_back_filename = $sdetails['aadhar_back_filename'];
                        $student_photo_with_aadhar = $sdetails['student_photo_with_aadhar'];
            
                        $practicalactivity_video_file = $sdetails['practicalactivity_video_file'];
                        $viva_video_file = $sdetails['viva_video_file'];

                        $file_path = "./".$this->config->item('aadhaar_filename_path');
                        $file_path_thumb = "./".$this->config->item('aadhaar_filename_path').'thumbs/';
                        $student_photo_file_path = "./".$this->config->item('student_photo_path');
                        $student_photo_file_path_thumb = "./".$this->config->item('student_photo_path').'thumbs/';

                        if($aadhar_front_filename != "") {
                            $file = $file_path.$aadhar_front_filename;
                            $fileThumb = $file_path_thumb.$aadhar_front_filename;
                            //echo "<br> aadhar_front_filename ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        if($aadhar_back_filename != "") {
                            $file = $file_path.$aadhar_back_filename;
                            $fileThumb = $file_path_thumb.$aadhar_back_filename;
                            //echo "<br> aadhar_back_filename  ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            } 
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        if($student_photo_with_aadhar != "") {
                            $file = $file_path.$student_photo_with_aadhar;
                            $fileThumb = $file_path_thumb.$student_photo_with_aadhar;
                            //echo "<br> student_photo_with_aadhar  ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        if($student_photo != "") {
                            $file = $student_photo_file_path.$student_photo;
                            $fileThumb = $file_path_thumb.$student_photo;
                            //echo "<br> student_photo  ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                        
                        if($practicalactivity_video_file != "") {
                            $file = './uploads/student_assessment_videos/'.$practicalactivity_video_file;
                            $file_path_thumb = './uploads/student_assessment_videos/thumbs/'.$practicalactivity_video_file;

                            //echo "<br> practicalactivity_video_file  ".$file;
                            //echo "<br> file ".$file;exit;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($file_path_thumb)) {
                                unlink($file_path_thumb);
                            }    
                        }
                        if($viva_video_file != "") {
                            $file = './uploads/student_assessment_videos/'.$viva_video_file;
                            $file_path_thumb = './uploads/student_assessment_videos/thumbs/'.$viva_video_file;
                            //echo "<br> viva_video_file ".$file;
                            //echo "<br> file ".$file;exit;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($file_path_thumb)) {
                                unlink($file_path_thumb);
                            }
                        }

                    }
                }

                $this->db->select('*'); 
                $this->db->where('tb_id', $tb_id);
                $query=$this->db->get('tbl_student_snapshots');
                $arr_student_snapshot_details = $query->result_array();
                //echo "<br> str ".$this->db->last_query();exit;

                if(count($arr_student_snapshot_details) > 0){ 
                    foreach($arr_student_snapshot_details as $sndetails) {
                        $snapshot_image = $sndetails['snapshot_image'];
                        
                        $file_path = "./".$this->config->item('student_snapshot_photo_path');
                        $file_path_thumb = "./".$this->config->item('student_snapshot_photo_path').'thumbs/';
                        
                        if($snapshot_image != "") {
                            $file = $file_path.$snapshot_image;
                            $fileThumb = $file_path_thumb.$snapshot_image;
                            //echo "<br> aadhar_front_filename ".$file;
                            if (file_exists($file)) {
                                unlink($file);
                            }
                            if (file_exists($fileThumb)) {
                                unlink($fileThumb);
                            }
                        }
                    }
                }

                $updData['delete_batch'] = 2;
                $this->db->where('tb_id', $tb_id);
                $query = $this->db->update('tbl_training_batches', $updData);

                //Delete related students and their other records
                $this->db->where('tb_id', $tb_id);
                $query = $this->db->delete('tbl_students');
                //echo "<br> str ".$this->db->last_query();exit;
                
                //Delete from tbl_theory_answers,tbl_viva_answers,tbl_practical_activity_answers
                $this->db->where('tb_id', $tb_id);
                $this->db->delete('tbl_theory_answers');
                
                $this->db->where('tb_id', $tb_id);
                $this->db->delete('tbl_practical_activity_answers');
                
                $this->db->where('tb_id', $tb_id);
                $this->db->delete('tbl_viva_answers');

                $this->db->where('tb_id', $tb_id);
                $query = $this->db->delete('tbl_checklist_documents_details');

                $this->db->where('tb_id', $tb_id);
                $query = $this->db->delete('tbl_training_batches');

                echo "<br> Delete Process completed for ".$batch_id;
            }
        } 
        else {
            echo "<br> No Records";
        }   
    }

}
