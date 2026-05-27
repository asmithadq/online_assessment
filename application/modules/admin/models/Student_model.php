<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_model extends CI_Model{
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_students';
        // Set orderable column fields
	    $this->column_order = array(null,'enrollment_number','student_name','aadhar_number','student_email','student_mobile','student_assessment_status','student_attendance','profile_verification_status');
	
	    // Set searchable column fields
        $this->column_search = array('enrollment_number','student_name','aadhar_number','student_email','student_mobile','student_assessment_status','student_attendance','profile_verification_status');
	    
        // Set default order
        $this->order = array('created_datetime' => 'desc');
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
	    $this->db->select('tbl_students.*,state_code,state_name,dist_code,dist_name'); 
        $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_students.district_id','LEFT');		
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_students.state_id','LEFT');	
        $this->db->where('tbl_students.status',1);
        $this->db->where('tbl_students.tb_id',$postData['tb_id']);
        
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
    
    function getBatchStudentDetails($tb_id){
	
    	$this->db->select('tbl_students.*'); 
        $this->db->where('tbl_students.status',1);
        $this->db->where('tbl_students.tb_id', $tb_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getStudentSnapshotsCount($tb_id){
	
    	$this->db->select('count(student_id) as total_snapshots,student_id'); 
        $this->db->where('tb_id', $tb_id);
        $this->db->group_by('student_id');
        $this->db->order_by('student_id','ASC');
    	$query=$this->db->get('tbl_student_snapshots');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getStudentCompleteDetails($student_id){
        $this->db->select("tbl_students.*,batch_id,tb_assessment_date,ag_name,tbl_training_partners.name as tp_name,tp_code,tbl_training_centers.name as tc_name,tc_code,ssc_code,ssc_title,
                            trade_code,trade_name,ssc_logo,ag_logo,optional_exam_type,tbl_trades.optional_exam_type,tbl_training_batches.trade_id,tbl_training_batches.ssc_id,
                                CONCAT(
                                    IF(tbl_training_centers.address_1 IS NOT NULL, CONCAT(tbl_training_centers.address_1, '-'), ''),
                                    IF(tbl_training_centers.address_2 IS NOT NULL, CONCAT(tbl_training_centers.address_2, '-'), ''),
                                    IF(tbl_training_centers.city IS NOT NULL, CONCAT(tbl_training_centers.city, '-'), ''),
                                    IF(tbl_districts.dist_name IS NOT NULL, CONCAT(tbl_districts.dist_name, '-'), ''),
                                    IF(tbl_states.state_name IS NOT NULL, CONCAT(tbl_states.state_name), '')
                                )  AS center_address"); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');	
        $this->db->join('tbl_assessment_agency', 'tbl_assessment_agency.ag_id = tbl_training_batches.ag_id');	
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');	
        $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id');	
        $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_training_batches.ssc_id');
        $this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id');	
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_training_centers.state');	
        $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_training_centers.district');	
    	$this->db->where('student_id', $student_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    public function getQuestionDetails($arrQuestionIds)
	{      
		$this->db->select('tbl_questions.*');  
		$this->db->from("tbl_questions");
        $this->db->where_in('tbl_questions.qid',$arrQuestionIds);
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

    public function getCandidateAnswerDetails($tb_id,$candidate_id,$assessment_type = "theory")
	{      
		$this->db->select('*');
        if($assessment_type == "theory") {
            $this->db->from('tbl_theory_answers');
        }
        if($assessment_type == "practical_activity") {
            $this->db->from('tbl_practical_activity_answers');
        }
        if($assessment_type == "viva") {
            $this->db->from('tbl_viva_answers');
        }
        $this->db->where('tb_id',$tb_id);
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

    function getStudentSnapshots($student_id){
	
    	$this->db->select('*'); 
        $this->db->where('student_id', $student_id);
        $this->db->order_by('ss_id','ASC');
    	$query=$this->db->get('tbl_student_snapshots');
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
        $this->db->order_by('ss_id','ASC');
    	$query=$this->db->get('tbl_student_snapshots');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getBatchPresentStudentDetails($tb_id){
	
    	$this->db->select('tbl_students.*'); 
        $this->db->where('tbl_students.student_attendance','Present');
        $this->db->where('tbl_students.status',1);
        $this->db->where('tbl_students.tb_id', $tb_id);
    	$query=$this->db->get('tbl_students');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
}