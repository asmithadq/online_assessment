<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Results_model extends CI_Model{
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_students';
        // Set orderable column fields
	    $this->column_order = array(null,'enrollment_number','student_name','total_theory_marks','total_practical_marks','marks_percentage','result');
	
	    // Set searchable column fields
        $this->column_search = array('enrollment_number','student_name','total_theory_marks','total_practical_marks','marks_percentage','result');
	    
        // Set default order
        $this->order = array('enrollment_number' => 'asc');
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
        $this->db->select("tbl_students.*,trade_id,batch_id");  
        $this->db->from($this->table);
		$this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');	
	    
		if($postData['batch_id'] != "")
		{
			$this->db->where('tbl_students.tb_id',$postData['batch_id']);
		}
		$i = 0;
        // loop searchable columns 
        foreach($this->column_search as $item){
            // if datatable send POST for search
            if($postData['search']['value']){
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
   
     function getPercentageSheetData($tb_id) 
	 {
        $this->db->select('enrollment_number, student_name, gender, student_mobile, student_email, sum(tbl_map_trade_nos.theory_marks) as max_marks_theory,
							sum(tbl_map_trade_nos.practical_skill_marks) + sum(tbl_map_trade_nos.practical_marks) + sum(tbl_map_trade_nos.viva_marks) as max_marks_practical,
							total_theory_marks,total_practical_marks,result,marks_percentage,batch_id'); 
		$this->db->join('tbl_training_batches','tbl_training_batches.tb_id = tbl_students.tb_id');
		//$this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');
		$this->db->join('tbl_map_trade_nos','tbl_map_trade_nos.trade_id = tbl_training_batches.trade_id');
        $this->db->where('tbl_students.status',1);
		$this->db->group_by('student_id,tbl_map_trade_nos.trade_id');
		
		if($tb_id != "" && $tb_id > 0)
		{
			$this->db->where('tbl_students.tb_id', $tb_id);
		}
		/*if($student_id != "" && $student_id > 0)
		{
			$this->db->where('student_id', $student_id);
		}*/
        $query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getAllNOS($batch_id) 
	{
        $this->db->select('tbl_national_occupational_standards.*'); 
		
		$this->db->join('tbl_map_trade_nos','tbl_map_trade_nos.nos_id = tbl_national_occupational_standards.nos_id');
		$this->db->join('tbl_training_batches','tbl_training_batches.trade_id = tbl_map_trade_nos.trade_id');
		
        $this->db->where('tbl_national_occupational_standards.status',1);
		if($batch_id != "" && $batch_id > 0)
		{
			$this->db->where('tbl_training_batches.tb_id', $batch_id);
		}
        $query=$this->db->get('tbl_national_occupational_standards');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getBatchCompleteDetails($tb_id)
	{
        $this->db->select("tbl_training_batches.*,ag_name,tbl_training_partners.name as tp_name,tbl_training_centers.name as tc_name, trade_code, 
							trade_name, scheme_name,optional_exam_type,assessor_name,assessor_code"); 
        $this->db->join('tbl_assessment_agency', 'tbl_assessment_agency.ag_id = tbl_training_batches.ag_id');	
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');	
        $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id');	
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');
		$this->db->join('tbl_schemes', 'tbl_schemes.scheme_id = tbl_training_batches.scheme_id');
		$this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id');		
    	$this->db->where('tbl_training_batches.tb_id', $tb_id);
    	$query=$this->db->get('tbl_training_batches');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0];
    	}else{
    	    return false;
    	}
    }
	
	
	function getStudentsByBatchId($batch_id) 
	{
        $this->db->select('*'); 
        $this->db->where('status',1);
		
		if($batch_id != "" && $batch_id > 0)
		{
			$this->db->where('tb_id', $batch_id);
		}
		
        $query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getStudentsCountByBatchId($batch_id) 
	{
        $this->db->select('student_id'); 
        $this->db->where('status',1);
		
		if($batch_id != "" && $batch_id > 0)
		{
			$this->db->where('tb_id', $batch_id);
		}
		
        $query=$this->db->get('tbl_students');
    	$result=$query->num_rows();
    	return $result;
    }
	
	function getResultCountByBatchId($batch_id) 
	{
        $this->db->select('result, count(result) as res_count'); 
        $this->db->where('status',1);
		
		if($batch_id != "" && $batch_id > 0)
		{
			$this->db->where('tb_id', $batch_id);
		}
		
		$this->db->group_by('result');
		
        $query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getTheoryQuestionsByStudentID($student_id)
	{
        $this->db->select("theory_questions"); 
        $this->db->where('student_id', $student_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0];
    	}else{
    	    return false;
    	}
    }
	
	function getStudentByID($student_id)
	{
        $this->db->select("enrollment_number, student_name,tbl_students.tb_id,tbl_trades.total_marks as total_max_marks,
							tbl_students.pass_percentage,tbl_students.marks_percentage,tbl_students.theory_marks,tbl_students.total_marks,
							total_practical_marks,practical_skill_marks,batch_id"); 
		$this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');	
		$this->db->join('tbl_trades','tbl_trades.trade_id = tbl_training_batches.trade_id');
        $this->db->where('student_id', $student_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0];
    	}else{
    	    return false;
    	}
    }
	
	function getTheoryModeration($student_id, $theory_questions)
	{
        $this->db->select("tbl_questions.qid, tbl_questions.question, tbl_questions.correct_ans, tbl_questions.marks, tbl_theory_answers.ans, tbl_theory_answers.ta_id"); 
        $this->db->join('tbl_theory_answers', 'tbl_theory_answers.qid = tbl_questions.qid');	
    	$this->db->where('tbl_theory_answers.student_id', $student_id);
		$this->db->where_in('tbl_questions.qid', $theory_questions);
    	$query=$this->db->get('tbl_questions');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getPracticalActivityQuestionsByStudentID($student_id)
	{
        $this->db->select("practical_activity_questions"); 
        $this->db->where('student_id', $student_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0];
    	}else{
    	    return false;
    	}
    }
	
	function getPracticalActivityModeration($student_id, $practical_activity_questions)
	{
        $this->db->select("tbl_questions.qid, tbl_questions.question, tbl_questions.correct_ans, tbl_questions.marks, tbl_practical_activity_answers.marks as student_marks, tbl_practical_activity_answers.pa_id"); 
        $this->db->join('tbl_practical_activity_answers', 'tbl_practical_activity_answers.qid = tbl_questions.qid');	
    	$this->db->where('tbl_practical_activity_answers.student_id', $student_id);
		$this->db->where_in('tbl_questions.qid', $practical_activity_questions);
    	$query=$this->db->get('tbl_questions');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getVivaQuestionsByStudentID($student_id)
	{
        $this->db->select("viva_questions"); 
        $this->db->where('student_id', $student_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result[0];
    	}else{
    	    return false;
    	}
    }
	
	function getVivaModeration($student_id, $viva_questions)
	{
        $this->db->select("tbl_questions.qid, tbl_questions.question, tbl_questions.correct_ans, tbl_questions.marks, tbl_viva_answers.marks as student_marks, tbl_viva_answers.va_id"); 
        $this->db->join('tbl_viva_answers', 'tbl_viva_answers.qid = tbl_questions.qid');	
    	$this->db->where('tbl_viva_answers.student_id', $student_id);
		$this->db->where_in('tbl_questions.qid', $viva_questions);
    	$query=$this->db->get('tbl_questions');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

	function getNosWiseBatchStudentsTheoryDetails($tb_id)
	{
        $this->db->select("tbl_questions.qid, tbl_questions.nos_id, tbl_questions.question, tbl_questions.correct_ans, tbl_questions.marks, tbl_questions.question_type,tbl_theory_answers.ans,student_id"); 
		$this->db->join('tbl_questions', 'tbl_questions.qid = tbl_theory_answers.qid');	
        $this->db->where('tbl_theory_answers.tb_id', $tb_id);
		$query=$this->db->get('tbl_theory_answers');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

	function getNosWiseBatchStudentsPracticalActivityDetails($tb_id)
	{
        $this->db->select("tbl_questions.qid, tbl_questions.nos_id, tbl_practical_activity_answers.marks,student_id"); 
		$this->db->join('tbl_questions', 'tbl_questions.qid = tbl_practical_activity_answers.qid');	
        $this->db->where('tbl_practical_activity_answers.tb_id', $tb_id);
		$query=$this->db->get('tbl_practical_activity_answers');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

	function getNosWiseBatchStudentsVivaDetails($tb_id)
	{
        $this->db->select("tbl_questions.qid, tbl_questions.nos_id, tbl_viva_answers.marks,student_id"); 
		$this->db->join('tbl_questions', 'tbl_questions.qid = tbl_viva_answers.qid');	
        $this->db->where('tbl_viva_answers.tb_id', $tb_id);
		$query=$this->db->get('tbl_viva_answers');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

	function getTradeMaxMarks($tb_id)
	{
        $this->db->select("sum(theory_marks) + sum(practical_skill_marks) as max_marks_theory,sum(practical_marks) + sum(viva_marks) as max_marks_viva,optional_exam_type"); 
		$this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_map_trade_nos.trade_id');
		$this->db->join('tbl_training_batches','tbl_training_batches.trade_id = tbl_map_trade_nos.trade_id');
		$this->db->where('tb_id', $tb_id);
		$this->db->group_by('trade_id');
		$query=$this->db->get('tbl_map_trade_nos');
    	$result=$query->result_array();
		
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
}