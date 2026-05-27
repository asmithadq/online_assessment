<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class MainModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function getDataByField($getColumn, $table, $whereColumn, $whereValue, $statusType)
    {
        $this->db->where('status', $statusType);
        $this->db->where($whereColumn, $whereValue);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                return $row->$getColumn;
            }
        } else {
            return false;
        }
    }

    function getAllData($table, $whereColumn, $whereValue, $statusType,$orderColumn = NULL, $type = 'ASC')
    {
        $this->db->where('status', $statusType);
        $this->db->where($whereColumn, $whereValue);
        if($orderColumn != NULL) {
            $this->db->order_by($orderColumn, $type);
        }
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return $query;
        }
    }

    function getData($table, $orderColumn, $type, $statusType)
    {
        $this->db->where('status', $statusType);
        $this->db->order_by($orderColumn, $type);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return $query;
        }
    }
    
    function getDataResult($table, $orderColumn, $type, $statusType)
    {
        $this->db->where('status', $statusType);
        $this->db->order_by($orderColumn, $type);
        $query = $this->db->get($table);
        $result=$query->result_array();
        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function insertData($table_name, $data)
    {
        $this->db->insert($table_name, $data);
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function insertDataReturnId($table_name, $data){
        $this->db->insert($table_name, $data);
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows > 0) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    function updateData($whereColumn, $id, $table_name, $data)
    {
        $this->db->where($whereColumn, $id);
        $this->db->update($table_name, $data);
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function deleteData($whereColumn, $id, $table_name)
    {
        $this->db->where($whereColumn, $id);
        $this->db->delete($table_name);
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function searchData($table_name, $data)
    {
        foreach ($data as $row => $value) {
            if ($row != "" && $value != "") {
                if ($row == "start_date") {
                    $this->db->where('date >=', $value);
                } elseif ($row == "end_date") {
                    $this->db->where('date <=', $value);
                } else {
                    $this->db->where($row, $value);
                }
            }
        }
        $query = $this->db->get($table_name);
        return $query;
    }

    function getAllDataWithoutStatus($table, $whereColumn, $whereValue)
    {
        $this->db->where($whereColumn, $whereValue);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return $query;
        }
    }

    function getDataByVal($getColumn, $tableName, $whereArray)
    {
        foreach ($whereArray as $key => $value) {
            $this->db->where($key, $value);
        }
        $query = $this->db->get($tableName);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $getval) {
                $retValue = $getval->$getColumn;
            }
        } else {
            $retValue = "";
        }
        return $retValue;
    }

    function getAllDataByVal($tableName, $whereArray)
    {
        foreach ($whereArray as $key => $value) {
            if ($key == "limit") {
                $this->db->limit($value);
            } elseif ($key == "name") {
                $this->db->like('name', $value);
            } elseif ($key == "full_name") {
                $this->db->like('full_name', $value);
            } elseif ($key == "order_by_desc") {
                $this->db->order_by($value, "DESC");
            } elseif ($key == "order_by_asc") {
                $this->db->order_by($value, "ASC");
            } elseif ($key == "start_date_order") {
                $this->db->where('order_date >=', $value);
            } elseif ($key == "end_date_order") {
                $this->db->where('order_date <=', $value);
            } elseif ($key == "not_in_order_status") {
                $this->db->where_not_in('order_status', $value);
            } elseif ($key == "in_order_status") {
                $this->db->where_in('order_status', $value);
            } else {
                $this->db->where($key, $value);
            }
        }
        $query = $this->db->get($tableName);
        return $query;
    }

    function getDataWithoutWhere($tableName)
    {
        $query = $this->db->get($tableName);
        return $query;
    }

    function checkDuplicate($field,$value,$table,$condition=NULL){
    	if($condition!=NULL){
    		$this->db->where($condition);
    	}
    	$this->db->where($field,$value);
    	$query=$this->db->get($table);
    	$result=$query->result_array();
    	if(count($result)>0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }
    
    function getAllRecords($tableName,$condition=NULL,$order_by_field = NULL,$order_by = NULL){
    	if($condition!=NULL){
    		$this->db->where($condition);
    	}
    	if($order_by_field != NULL){
    		$this->db->order_by($order_by_field,$order_by);
    	}
    	$query=$this->db->get($tableName);
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getAllRecordsLimit($tableName,$condition=NULL,$order_by_field = NULL,$order_by = NULL){
    	if($condition!=NULL){
    		$this->db->where($condition);
    	}
    	if($order_by_field != NULL){
    		$this->db->order_by($order_by_field,$order_by);
    	}
    	$this->db->limit(12);
    	$query=$this->db->get($tableName);
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getAllDataByValMultiple($tableName, $whereArray,$orWhereArray)
    {
        $this->db->where('status', 1);
        foreach ($whereArray as $key => $value) {
            $this->db->where($key, $value);
        }
        foreach ($orWhereArray as $key => $value) {
            $this->db->or_where($key, $value);
        }
        $query = $this->db->get($tableName);
        return $query;
    }

    public function getCandidateProfile($candidate_id) {
        $student_photo_thumbs_path = base_url().$this->config->item('student_photo_thumbs_path');
        $aadhaar_filename_path = base_url().$this->config->item('aadhaar_filename_path');
        
        $this->db->select("enrollment_number,COALESCE(student_name, '') as student_name,COALESCE(student_email, '') as student_email,student_mobile,COALESCE(gender, '') as gender,COALESCE(father_name, '') as father_name,
                            COALESCE(DATE_FORMAT(dob, '%d-%m-%Y'), '') as dob,COALESCE(aadhar_number, '') as aadhar_number,COALESCE(address, '') as address,COALESCE(city, '') as city,COALESCE(pincode, '') as pincode,
                            COALESCE(tbl_students.state_id, '') as state_id,COALESCE(state_code, '') as state_code,COALESCE(state_name, '') as state_name,COALESCE(tbl_students.district_id, '') as district_id,
                            COALESCE(dist_code, '') as dist_code,COALESCE(dist_name, '') as dist_name,
                            IF(student_photo != '', CONCAT('$student_photo_thumbs_path', student_photo), '') AS student_photo,
                            IF(aadhar_front_filename != '', CONCAT('$aadhaar_filename_path', aadhar_front_filename), '') AS aadhar_front_filename,
                            IF(aadhar_back_filename != '', CONCAT('$aadhaar_filename_path', aadhar_back_filename), '') AS aadhar_back_filename");
        $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_students.district_id','LEFT');		
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_students.state_id','LEFT');
        $this->db->where('student_id',$candidate_id);
        $query = $this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return -1;
    	}
    }

    function getBatchDetails($tb_id){
        $this->db->select('tbl_training_batches.*,trade_code,trade_name,scheme_name,subscheme_name'); 
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');	
        $this->db->join('tbl_schemes', 'tbl_schemes.scheme_id = tbl_training_batches.scheme_id');	
        $this->db->join('tbl_subschemes', 'tbl_subschemes.subscheme_id = tbl_training_batches.subscheme_id');	
        $this->db->where('tbl_training_batches.tb_id', $tb_id);
    	$query=$this->db->get('tbl_training_batches');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getStateDistrictList(){
        $this->db->select('dist_id,dist_name,tbl_districts.state_id,state_name'); 
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_districts.state_id');	
        $query=$this->db->get('tbl_districts');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    public function getQuestionDetails($arrQuestionIds,$candidate_id,$answers_record_generated,$assessment_type = "theory",$lid = 0)
	{      
		$this->db->select('tbl_questions.*,CONCAT(nos_code, "-", nos_title) AS nos_title,CONCAT(trade_code, "-", trade_name) AS exam_name');  
		$this->db->from("tbl_questions");
        $this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_questions.nos_id');
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_questions.trade_id');
        if($lid > 0) {
            $this->db->select('GROUP_CONCAT(DISTINCT tbl_language_questions.question SEPARATOR "|%lang%|") AS lang_question,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_a SEPARATOR "|%lang%|") AS lang_option_a,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_b SEPARATOR "|%lang%|") AS lang_option_b,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_c SEPARATOR "|%lang%|") AS lang_option_c,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_d SEPARATOR "|%lang%|") AS lang_option_d');

            $this->db->join('tbl_language_questions', 'tbl_language_questions.qid = tbl_questions.qid','LEFT');
            $this->db->where('tbl_language_questions.lid',$lid);
        }    
        $this->db->where_in('tbl_questions.qid',$arrQuestionIds);
        if($answers_record_generated == 1) {
            if($assessment_type == "theory") {
                $this->db->select('tbl_theory_answers.ans,save_type');
                $this->db->join('tbl_theory_answers', 'tbl_theory_answers.qid = tbl_questions.qid');
            }
            if($assessment_type == "practical_activity") {
                $this->db->select('tbl_practical_activity_answers.video_file as video_file');
                $this->db->join('tbl_practical_activity_answers', 'tbl_practical_activity_answers.qid = tbl_questions.qid');
            }
            if($assessment_type == "viva") {
                $this->db->select('tbl_viva_answers.video_file as video_file');
                $this->db->join('tbl_viva_answers', 'tbl_viva_answers.qid = tbl_questions.qid');
            }
            $this->db->where('student_id',$candidate_id);
        }
        $this->db->group_by('tbl_questions.qid');
        $this->db->order_by('FIELD(tbl_questions.qid, ' . implode(',', $arrQuestionIds) . ')'); 
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        return $query->result_array();
    }

    public function getAnswerDetails($tb_id,$candidate_id)
	{      
		$this->db->select('*');  
		$this->db->from("tbl_theory_answers");
        $this->db->where('tb_id',$tb_id);
        $this->db->where('student_id',$candidate_id);
        $this->db->order_by('ta_id','ASC'); 
        $query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        return $query->result_array();
    }

    public function getQuestionAnswerDetails($tb_id,$candidate_id,$qid)
	{      
		$this->db->select('tbl_questions.*,tbl_theory_answers.ans,save_type,GROUP_CONCAT(tbl_language_questions.question SEPARATOR "|%lang%|") AS lang_question,
                                           GROUP_CONCAT(tbl_language_questions.option_a SEPARATOR "|%lang%|") AS lang_option_a,
                                           GROUP_CONCAT(tbl_language_questions.option_b SEPARATOR "|%lang%|") AS lang_option_b,
                                           GROUP_CONCAT(tbl_language_questions.option_c SEPARATOR "|%lang%|") AS lang_option_c,
                                           GROUP_CONCAT(tbl_language_questions.option_d SEPARATOR "|%lang%|") AS lang_option_d');  
		$this->db->from("tbl_questions");
        $this->db->join('tbl_language_questions', 'tbl_language_questions.qid = tbl_questions.qid','LEFT');
        $this->db->join('tbl_theory_answers', 'tbl_theory_answers.qid = tbl_questions.qid');
        $this->db->where('tbl_questions.qid',$qid);
        $this->db->where('tb_id',$tb_id);
        $this->db->where('student_id',$candidate_id);
        $this->db->where('tbl_theory_answers.qid',$qid);
        $this->db->group_by('tbl_questions.qid');
        $this->db->order_by('ta_id','ASC'); 
        $query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        return $query->result_array();
    }

    public function getAssessorProfile($assessor_id) {
        $aassessors_images_thumbs_path = base_url().$this->config->item('assessors_images_thumbs_path');
        $assessor_resume_path  = base_url().$this->config->item('assessors_resume_path');
        $assessors_pan_thumbs_path  = base_url().$this->config->item('assessors_pan_thumbs_path');
        $assessors_aadhaar_thumbs_path  = base_url().$this->config->item('assessors_aadhaar_thumbs_path');
        
        $this->db->select("assessor_code,COALESCE(assessor_name, '') as assessor_name,COALESCE(assessor_email, '') as assessor_email,assessor_mobile,COALESCE(assessor_gender, '') as assessor_gender,
                            COALESCE(address, '') as address,COALESCE(city, '') as city,COALESCE(pincode, '') as pincode,COALESCE(tbl_assessor.state_id, '') as state_id,COALESCE(state_code, '') as state_code,
                            COALESCE(state_name, '') as state_name, COALESCE(tbl_assessor.district_id, '') as district_id,COALESCE(dist_code, '') as dist_code,COALESCE(dist_name, '') as dist_name,aadhar_number,pan_no,
                            IF(assessor_photo != '', CONCAT('$aassessors_images_thumbs_path', assessor_photo), '') AS assessor_photo,
                            IF(assessor_resume != '', CONCAT('$assessor_resume_path', assessor_resume), '') AS assessor_resume,
                            IF(pan_filename != '', CONCAT('$assessors_pan_thumbs_path', pan_filename), '') AS assessor_pan_filename,
                            IF(aadhar_front_filename != '', CONCAT('$assessors_aadhaar_thumbs_path', aadhar_front_filename), '') AS aadhar_front_filename,
                            IF(aadhar_back_filename != '', CONCAT('$assessors_aadhaar_thumbs_path', aadhar_back_filename), '') AS aadhar_back_filename");
        $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_assessor.district_id','LEFT');		
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_assessor.state_id','LEFT');
        $this->db->where('assessor_id',$assessor_id);
        $query = $this->db->get('tbl_assessor');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return -1;
    	}
    }

    function getAssessorBatchDetails($assessor_id,$tb_id = 0){
        $this->db->select('tbl_training_batches.*,trade_code,trade_name,scheme_name,subscheme_name,tc_code,tbl_training_centers.name as tc_name,tp_code,tbl_training_partners.name as tp_name,count(student_id) as total_students'); 
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');	
        $this->db->join('tbl_schemes', 'tbl_schemes.scheme_id = tbl_training_batches.scheme_id');	
        $this->db->join('tbl_subschemes', 'tbl_subschemes.subscheme_id = tbl_training_batches.subscheme_id');	
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');
        $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id');
        $this->db->join('tbl_students', 'tbl_students.tb_id = tbl_training_batches.tb_id','LEFT');
        $this->db->where('tbl_training_batches.assessor_id', $assessor_id);
        if($tb_id > 0) {
            $this->db->where('tbl_training_batches.tb_id', $tb_id);
        }
        $this->db->group_by('tbl_training_batches.tb_id');
        $this->db->order_by('tbl_training_batches.tb_start_date_time');
    	$query=$this->db->get('tbl_training_batches');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchCandidateDetails($tb_id){
        $aadhaar_filename_path = base_url().$this->config->item('aadhaar_filename_path');

        $this->db->select('tbl_training_batches.*,trade_code,trade_name,scheme_name,subscheme_name'); 
        $this->db->select("student_id,enrollment_number,COALESCE(student_name, '') as student_name,COALESCE(password, '') as password,profile_verification_status,practical_activity_questions,viva_questions,
                            COALESCE(aadhar_number, '') as aadhar_number,COALESCE(student_assessment_status, '') as student_assessment_status,COALESCE(student_attendance, '') as student_attendance,
                            IF(aadhar_front_filename != '', CONCAT('$aadhaar_filename_path', aadhar_front_filename), '') AS aadhar_front_filename,
                            IF(aadhar_back_filename != '', CONCAT('$aadhaar_filename_path', aadhar_back_filename), '') AS aadhar_back_filename");
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');	
        $this->db->join('tbl_schemes', 'tbl_schemes.scheme_id = tbl_training_batches.scheme_id');	
        $this->db->join('tbl_subschemes', 'tbl_subschemes.subscheme_id = tbl_training_batches.subscheme_id');	
        $this->db->join('tbl_students', 'tbl_students.tb_id = tbl_training_batches.tb_id','LEFT');
        $this->db->where('tbl_training_batches.tb_id', $tb_id);
        $query=$this->db->get('tbl_training_batches');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getCandidateBatchDetails($student_id){
        $aadhaar_filename_path = base_url().$this->config->item('aadhaar_filename_path');

        $this->db->select('tbl_students.*,lid,practical_activity_instructions,viva_instructions'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');
        $this->db->where('student_id', $student_id);
        $query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    public function getCandidateQuestionDetails($arrQuestionIds,$candidate_id,$answers_record_generated,$assessment_type = "theory")
	{      
		$this->db->select('tbl_questions.*,CONCAT(nos_code, "-", nos_title) AS nos_title,CONCAT(trade_code, "-", trade_name) AS exam_name');  
		$this->db->from("tbl_questions");
        $this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_questions.nos_id');
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_questions.trade_id');
        $this->db->where_in('tbl_questions.qid',$arrQuestionIds);
        if($answers_record_generated == 1) {
            if($assessment_type == "theory") {
                $this->db->select('tbl_theory_answers.ans,save_type');
                $this->db->join('tbl_theory_answers', 'tbl_theory_answers.qid = tbl_questions.qid');
            }
            if($assessment_type == "practical_activity") {
                $this->db->select('tbl_practical_activity_answers.video_file as video_file,tbl_practical_activity_answers.marks as practical_activity_marks');
                $this->db->join('tbl_practical_activity_answers', 'tbl_practical_activity_answers.qid = tbl_questions.qid');
            }
            if($assessment_type == "viva") {
                $this->db->select('tbl_viva_answers.video_file as video_file,tbl_viva_answers.marks as viva_marks');
                $this->db->join('tbl_viva_answers', 'tbl_viva_answers.qid = tbl_questions.qid');
            }
            $this->db->where('student_id',$candidate_id);
        }
        $this->db->order_by('FIELD(tbl_questions.qid, ' . implode(',', $arrQuestionIds) . ')'); 
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    public function getCandidateLanguageQuestionDetails($arrQuestionIds,$lid = 0)
	{      
		$this->db->select('qid,GROUP_CONCAT(DISTINCT tbl_language_questions.question SEPARATOR "|%lang%|") AS lang_question,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_a SEPARATOR "|%lang%|") AS lang_option_a,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_b SEPARATOR "|%lang%|") AS lang_option_b,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_c SEPARATOR "|%lang%|") AS lang_option_c,
                            GROUP_CONCAT(DISTINCT tbl_language_questions.option_d SEPARATOR "|%lang%|") AS lang_option_d');
        $this->db->from("tbl_language_questions");
        $this->db->where('tbl_language_questions.lid',$lid);    
        $this->db->where_in('tbl_language_questions.qid',$arrQuestionIds);
        $this->db->order_by('FIELD(tbl_language_questions.qid, ' . implode(',', $arrQuestionIds) . ')'); 
        $this->db->group_by('tbl_language_questions.qid');
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    public function getQuestionAnswerVideoDetails($candidate_id,$tableName)
	{      
		$this->db->select('*');  
		$this->db->from($tableName);
        $this->db->where('student_id',$candidate_id);
        $query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
}



