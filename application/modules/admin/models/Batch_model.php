<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Batch_model extends CI_Model{
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_training_batches';
        // Set orderable column fields
	    $this->column_order = array(null,'batch_id','trade_name','trade_code','tb_target','tb_exam_type','qp_generated_status','tb_start_date_time','tb_end_date_time','tb_assessment_date');
	
	    // Set searchable column fields
        $this->column_search = array('batch_id','trade_name','trade_code','tb_target','tb_exam_type','qp_generated_status','tb_start_date_time',
                                        'tb_end_date_time','tb_assessment_date','assessor_name','tbl_training_partners.name','tbl_training_centers.name','scheme_name','subscheme_name');
	    
        // Set default order
        $this->order = array('tb_created_date' => 'desc');
    }
    
    /*
     * Fetch members data from the database
     * @param $_POST filter data based on the posted parameters
     */
    public function getRows($postData){
        $this->_get_datatables_query($postData);
        if($postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();exit;
        return $query->result_array();
    }
    
    /*
     * Count all records
     */
    public function countAll(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    /*
     * Count records based on the filter params
     * @param $_POST filter data based on the posted parameters
     */
    public function countFiltered($postData){
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    /*
     * Perform the SQL queries needed for an server-side processing requested
     * @param $_POST filter data based on the posted parameters
     */
    private function _get_datatables_query($postData){
         
        $this->db->from($this->table);
		//join
	    $this->db->select('tbl_training_batches.*,ag_name,tbl_training_partners.name as tp_name,tp_code,tbl_training_centers.name as tc_name,tc_code,ssc_code,ssc_title,
	                        trade_name,trade_code,scheme_name,subscheme_name,CONCAT(assessor_code,"-", assessor_name) AS assessor_name, language_name,assessor_code,assessor_name'); 
        $this->db->join('tbl_assessment_agency', 'tbl_assessment_agency.ag_id = tbl_training_batches.ag_id');	
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');	
        $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id');	
        $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_training_batches.ssc_id');
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');
        $this->db->join('tbl_schemes', 'tbl_schemes.scheme_id = tbl_training_batches.scheme_id');
        $this->db->join('tbl_subschemes', 'tbl_subschemes.subscheme_id = tbl_training_batches.subscheme_id');
        $this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id','LEFT');
		$this->db->join('tbl_languages', 'tbl_languages.language_id = tbl_training_batches.lid','LEFT');
		$this->db->where('tbl_training_batches.status',1);
        $this->db->where('tbl_training_batches.tb_assessment_status',$postData['type']);
		 
		if(array_key_exists('ssc_id',$postData) && $postData['ssc_id'] != "")
		{
			$this->db->where('tbl_training_batches.ssc_id',$postData['ssc_id']);
		}
		if(array_key_exists('trade_id',$postData) && $postData['trade_id'] != "")
		{
			$this->db->where('tbl_training_batches.trade_id',$postData['trade_id']);
		}
		if(array_key_exists('assessor_id',$postData) && $postData['assessor_id'] != "")
		{
			$this->db->where('tbl_training_batches.assessor_id',$postData['assessor_id']);
		}
		if(array_key_exists('start_date',$postData) && $postData['start_date'] != "")
		{
			$this->db->where("DATE_FORMAT(tbl_training_batches.tb_start_date_time,'%Y-%m-%d') >='".$postData['start_date']."'");
		}
		if(array_key_exists('end_date',$postData) && $postData['end_date'] != "")
		{
			$this->db->where("DATE_FORMAT(tbl_training_batches.tb_end_date_time,'%Y-%m-%d') <='".$postData['end_date']."'");
		}
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search as $item){
            // if datatable send POST for search
            if(array_key_exists('search',$postData) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
         
        if(isset($postData['order'])){
            $this->db->order_by($this->column_order[$postData['order']['0']['column']], $postData['order']['0']['dir']);
        }else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function getBatchDetails($tb_id){
	
    	$this->db->select('tbl_training_batches.*,ag_name,tbl_training_partners.name as tp_name,tp_code,tbl_training_centers.name as tc_name,tc_code,ssc_code,ssc_title'); 
        $this->db->join('tbl_assessment_agency', 'tbl_assessment_agency.ag_id = tbl_training_batches.ag_id');	
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');	
        $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id');	
        $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_training_batches.ssc_id');
    	$this->db->where('tbl_training_batches.tb_id', $tb_id);
    	$query=$this->db->get('tbl_training_batches');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	public function addStudents($data)
	{
		$this->db->insert('tbl_students', $data);
		return $this->db->insert_id();
	}
	
	public function get_student_by_enrollment_number($arrEnrollmentNo)
	{
		$this->db->select('tbl_students.student_id,enrollment_number, tbl_training_batches.batch_id');
		$this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id', "left");
        $this->db->where('tbl_students.status',1);
		$this->db->where_in('enrollment_number', $arrEnrollmentNo);
		$query=$this->db->get('tbl_students');
		$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
	}
	
	public function get_student_by_aadhar_number($arrAadharNo)
	{
		$this->db->select('tbl_students.student_id,aadhar_number, tbl_training_batches.batch_id');
		$this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id', "left");
        $this->db->where('tbl_students.status',1);
		$this->db->where_in('aadhar_number', $arrAadharNo);
		$query=$this->db->get('tbl_students');
		$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
	}
	
	public function addStudentsSkipped($data)
	{
		$this->db->insert('tbl_students_skipped', $data);
		return $this->db->insert_id();
	}
	
	function getBatchStudentsCount($type,$tb_id = 0){
        $this->db->select('count(student_id) as total_students,tbl_students.tb_id'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id ');	
        $this->db->where('tbl_students.status',1);
        $this->db->where('tbl_training_batches.tb_assessment_status', $type);
        if($tb_id > 0) {
            $this->db->where('tbl_training_batches.tb_id', $tb_id);
        }
        $this->db->group_by('tbl_students.tb_id');
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchCompleteDetails($tb_id){
        $this->db->select("tbl_training_batches.*,ag_name,tbl_training_partners.name as tp_name,tp_code,tbl_training_centers.name as tc_name,tc_code,ssc_code,ssc_title,
                            trade_code,trade_name,optional_exam_type,ssc_logo,ag_logo,scheme_name,subscheme_name,
                                CONCAT(
                                    IF(tbl_training_centers.address_1 IS NOT NULL, CONCAT(tbl_training_centers.address_1), ''),
                                    IF(tbl_training_centers.address_2 IS NOT NULL, CONCAT(tbl_training_centers.address_2, '-'), ''),
                                    IF(tbl_training_centers.city IS NOT NULL, CONCAT(tbl_training_centers.city, '-'), ''),
                                    IF(tbl_districts.dist_name IS NOT NULL, CONCAT(tbl_districts.dist_name, '-'), ''),
                                    IF(tbl_states.state_name IS NOT NULL, CONCAT(tbl_states.state_name), '')
                                )  AS center_address"); 
        $this->db->join('tbl_assessment_agency', 'tbl_assessment_agency.ag_id = tbl_training_batches.ag_id');	
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');	
        $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id');	
        $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_training_batches.ssc_id');
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');	
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_training_centers.state');	
        $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_training_centers.district');	
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

    function getBatchTradeNosDetails($tb_id){
        $this->db->select("tbl_training_batches.*,tbl_map_trade_nos.nos_id,theory_marks,practical_skill_marks,practical_marks,viva_marks,total_nos_marks,nos_code,nos_title"); 
        $this->db->join('tbl_map_trade_nos', 'tbl_map_trade_nos.trade_id = tbl_training_batches.trade_id');	
        $this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_map_trade_nos.nos_id');
        $this->db->where('tbl_training_batches.tb_id', $tb_id);
        $this->db->where('tbl_map_trade_nos.status', 1);
    	$query=$this->db->get('tbl_training_batches');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchTotalStudentsCount($tb_id){
        $this->db->select('count(student_id) as total_students'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');	
        $this->db->where('tbl_students.status',1);
        $this->db->where('tbl_training_batches.tb_id', $tb_id);
        $this->db->group_by('tbl_students.tb_id');
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0]['total_students'];
    	}else{
    	    return false;
    	}
    }

    function getBatchTotalPendingStudentsCount($tb_id){
        $this->db->select('count(student_id) as total_students'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');	
        $this->db->where('tbl_students.status',1);
        $this->db->where('tbl_students.student_assessment_status','Pending');
        $this->db->where('tbl_training_batches.tb_id', $tb_id);
        $this->db->group_by('tbl_students.tb_id');
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0]['total_students'];
    	}else{
    	    return false;
    	}
    }

    function getQuestionsByNosTrade($trade_id,$arr_nos_id,$arrNosQuestionType){
        $this->db->select('*'); 
        $this->db->where('status',1);
        $this->db->where('trade_id', $trade_id);
        $this->db->where_in('nos_id', $arr_nos_id);
        $this->db->where_in('question_type', $arrNosQuestionType);
        $this->db->where('status', 1);
        $this->db->order_by('nos_id,question_type');
        $query=$this->db->get('tbl_questions');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchStudentsIds($tb_id) {
        $this->db->select('student_id'); 
        $this->db->where('tbl_students.status',1);
        $this->db->where('tb_id', $tb_id);
        $this->db->where('qp_generated_status', 0);
        $query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchStudentsByIds($tb_id,$arrStudentIds) {
        $this->db->select('student_id'); 
        $this->db->where('tbl_students.status',1);
        $this->db->where('tb_id', $tb_id);
        $this->db->where_in('student_id', $arrStudentIds);
        //$this->db->where('qp_generated_status', 0);
        $query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getChecklistDocumentsDetailsFileUploaded($tb_id) {
        $this->db->select('document_file_uploaded'); 
        $this->db->where('tb_id', $tb_id);
        $this->db->where('watermarking_error', 0);
        $query=$this->db->get('tbl_checklist_documents_details');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getCandidatesTheoryAnswersList($tb_id,$student_id = 0) {
        $this->db->select('tbl_theory_answers.student_id,question_type,SUM(marks) as marks,student_attendance'); 
        $this->db->join('tbl_students', 'tbl_students.student_id = tbl_theory_answers.student_id AND tbl_students.tb_id = tbl_theory_answers.tb_id');	
        $this->db->join('tbl_questions', 'tbl_questions.qid = tbl_theory_answers.qid');	
        $this->db->where('student_assessment_status', 'Completed');
        $this->db->where('student_attendance', 'Present');
        $this->db->where('LOWER(tbl_theory_answers.ans) = LOWER(correct_ans)');
        $this->db->where('tbl_theory_answers.tb_id', $tb_id);
        if($student_id > 0) {
            $this->db->where('tbl_theory_answers.student_id', $student_id);
        }
        $this->db->group_by('tbl_theory_answers.student_id,question_type');
        $this->db->order_by('tbl_theory_answers.student_id,question_type');
        $query=$this->db->get('tbl_theory_answers');
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getCandidatesPracticalActivityAnswersList($tb_id,$student_id = 0) {
        $this->db->select('tbl_practical_activity_answers.student_id,SUM(marks) as marks'); 
        $this->db->join('tbl_students', 'tbl_students.student_id = tbl_practical_activity_answers.student_id AND tbl_students.tb_id = tbl_practical_activity_answers.tb_id');
        $this->db->where('student_assessment_status', 'Completed');
        $this->db->where('student_attendance', 'Present');
        $this->db->where('tbl_practical_activity_answers.tb_id', $tb_id);
        if($student_id > 0) {
            $this->db->where('tbl_practical_activity_answers.student_id', $student_id);
        }
        $this->db->group_by('tbl_practical_activity_answers.student_id');
        $this->db->order_by('tbl_practical_activity_answers.student_id');
        $query=$this->db->get('tbl_practical_activity_answers');
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getCandidatesVivaAnswersList($tb_id,$student_id = 0) {
        $this->db->select('tbl_viva_answers.student_id,SUM(marks) as marks'); 
        $this->db->join('tbl_students', 'tbl_students.student_id = tbl_viva_answers.student_id AND tbl_students.tb_id = tbl_viva_answers.tb_id');
        $this->db->where('student_assessment_status', 'Completed');
        $this->db->where('student_attendance', 'Present');
        $this->db->where('tbl_viva_answers.tb_id', $tb_id);
        if($student_id > 0) {
            $this->db->where('tbl_viva_answers.student_id', $student_id);
        }
        $this->db->group_by('tbl_viva_answers.student_id');
        $this->db->order_by('tbl_viva_answers.student_id');
        $query=$this->db->get('tbl_viva_answers');
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	public function get_assessor_by_ssc_id($ssc_id)
	{
		$this->db->select('tbl_assessor.assessor_id, tbl_assessor.assessor_name');
		$this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_map_assessor_sector_skill_councils.assessor_id');
        $this->db->where('tbl_map_assessor_sector_skill_councils.ssc_id',$ssc_id);
		$query=$this->db->get('tbl_map_assessor_sector_skill_councils');
		$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
	}

    function getBatchChecklistDocumentsDetails($tb_id) {
        $this->db->select('*'); 
        $this->db->where('tb_id', $tb_id);
        $query=$this->db->get('tbl_checklist_documents_details');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchStudentTheoryDetails($tb_id){
        $this->db->select("tbl_students.*,tbl_theory_answers.*,tbl_questions.*"); 
        $this->db->join('tbl_theory_answers','tbl_theory_answers.student_id = tbl_students.student_id AND tbl_theory_answers.tb_id = tbl_students.tb_id');
        $this->db->join('tbl_questions','tbl_questions.qid = tbl_theory_answers.qid');
        $this->db->where('tbl_students.tb_id', $tb_id);
        $this->db->where('student_attendance !=', 'Absent');
        $this->db->order_by('tbl_theory_answers.student_id,ta_id','ASC');
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchStudentSnapshots($tb_id){
	
    	$this->db->select('*'); 
        $this->db->where('tb_id', $tb_id);
        $this->db->order_by('student_id,ss_id','ASC');
    	$query=$this->db->get('tbl_student_snapshots');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchStudentAssessmentDetails($tb_id,$assessment_type){
        if($assessment_type == "practical_activity") {
            $tableName = 'tbl_practical_activity_answers';
            $orderBy = 'pa_id';
        }
        if($assessment_type == "viva") {
            $tableName = 'tbl_viva_answers';
            $orderBy = 'va_id';
        }

        $this->db->select("tbl_questions.*,tbl_questions.marks as max_marks,$tableName.*"); 
        $this->db->join('tbl_questions','tbl_questions.qid = '.$tableName.'.qid');
        $this->db->where('tb_id', $tb_id);
        $this->db->order_by('student_id,'.$orderBy,'ASC');
        $query=$this->db->get($tableName);
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchChecklistDocumentsGroupPhotos($tb_id) {
        $this->db->select('tbl_assessment_checklist_documents_master.document_title,tbl_assessment_checklist_documents_master.checklist_cat_id,
                           tbl_assessment_checklist_documents_category.name as category,tbl_checklist_documents_details.*'); 
        $this->db->join('tbl_assessment_checklist_documents_master','tbl_assessment_checklist_documents_master.acdm_id = tbl_checklist_documents_details.acdm_id');
        $this->db->join('tbl_assessment_checklist_documents_category','tbl_assessment_checklist_documents_category.checklist_cat_id = tbl_assessment_checklist_documents_master.checklist_cat_id');
        $this->db->where('tb_id', $tb_id);
        $this->db->order_by('tbl_assessment_checklist_documents_master.checklist_cat_id,tbl_checklist_documents_details.acdm_id');
        $query=$this->db->get('tbl_checklist_documents_details');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    } 
}