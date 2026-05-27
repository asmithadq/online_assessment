<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questions_model extends CI_Model{
    
     function __construct() {
       
     }
    
    public function get_questions_details($arrQns,$trade_id)
	{
		$cond = "'".implode("','",$arrQns)."'";
		//echo "<br> cond ".$this->db->escape($cond);exit;
		
		$sql = "SELECT qid,question FROM tbl_questions WHERE status = 1 AND trade_id = ".$trade_id." AND question IN(".$cond.")";
		$query = $this->db->query($sql);
		if ($query) {
			$result=$query->result_array();
			if(count($result)>0){
				return $result;
			}else{
				return false;
			}
		}else{
    	    return false;
    	}
	}

	public function get_questions_by_trade($trade_id)
	{
		$sql = "SELECT * FROM tbl_questions WHERE status = 1 AND trade_id = ".$trade_id;
		$query = $this->db->query($sql);
		if ($query) {
			$result=$query->result_array();
			if(count($result)>0){
				return $result;
			}else{
				return false;
			}
		}else{
    	    return false;
    	}
	}
	
	function getTrades()
	{	
    	$this->db->select('trade_id, trade_code, trade_name'); 
      
		$query=$this->db->get('tbl_trades');
		
    	$result=$query->result_array();
		
    	if(count($result)>0)
		{
    		return $result;
    	}else{
    	    return false;
    	}
    }

	function get_map_trade_nos_by_trade_id($trade_id)
	{	
    	$this->db->select('tbl_national_occupational_standards.nos_id, tbl_national_occupational_standards.nos_code, tbl_national_occupational_standards.nos_title'); 
		
		$this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_map_trade_nos.nos_id');	
		
		$this->db->where('tbl_map_trade_nos.trade_id', $trade_id);
      
		$query=$this->db->get('tbl_map_trade_nos');
		
    	$result=$query->result_array();
		
    	if(count($result)>0)
		{
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	public function getRows($postData, $trade_id, $select_nos, $question_type="")
	{
        $this->_get_datatables_query($postData, $trade_id, $select_nos, $question_type);
        if($postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
       // echo "<br> str ".$this->db->last_query();exit;
        return $query->result_array();
    }    
   
    public function countAll(){
        $this->db->from("tbl_questions");
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData, $trade_id, $select_nos, $question_type="")
	{
        $this->_get_datatables_query($postData, $trade_id, $select_nos, $question_type);
        $query = $this->db->get();
        return $query->num_rows();
    }    
   
    private function _get_datatables_query($postData, $trade_id="", $select_nos="", $question_type="")
	{         
        $this->db->from("tbl_questions");
		
	    $this->db->select('tbl_questions.*,tbl_national_occupational_standards.nos_code,
						   GROUP_CONCAT(tbl_language_questions.question SEPARATOR "<br> ") AS lang_question,
						   GROUP_CONCAT(tbl_language_questions.option_a SEPARATOR "<br> ") AS lang_option_a,
						   GROUP_CONCAT(tbl_language_questions.option_b SEPARATOR "<br> ") AS lang_option_b,
						   GROUP_CONCAT(tbl_language_questions.option_c SEPARATOR "<br> ") AS lang_option_c,
						   GROUP_CONCAT(tbl_language_questions.option_d SEPARATOR "<br> ") AS lang_option_d');
							
        $this->db->join('tbl_language_questions', 'tbl_language_questions.qid = tbl_questions.qid','LEFT');	

		$this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_questions.nos_id');
		
		if($question_type !="")
		{
			$this->db->where('tbl_questions.question_type',$question_type);
		}
		else
		{
			$this->db->where_in('tbl_questions.question_type',array('Theory','PracticalSkill'));
		}
		
		$this->db->where('tbl_questions.trade_id',$trade_id);
		$this->db->where_in('tbl_questions.nos_id',$select_nos);
		$this->db->where('tbl_questions.status',1);
      
        $i = 0;
		$column_search = array('tbl_questions.question','tbl_questions.option_a','tbl_questions.option_b','tbl_questions.option_c','tbl_questions.option_d','marks');
		
        // loop searchable columns 
        foreach($column_search as $item){
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
                if(count($column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
		
		$column_order = array('created_dts' => 'desc');

		$this->db->group_by('tbl_questions.qid');
         
        if(isset($postData['order'])){
            $this->db->order_by($column_order[$postData['order']['0']['column']], $postData['order']['0']['dir']);
        }else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
	
	public function get_national_occupational_standards_by_ids($str_nos_ids)
	{
		$sql = "SELECT * FROM tbl_national_occupational_standards WHERE nos_id in (".$str_nos_ids.")";
		$query = $this->db->query($sql);
		if ($query) {
			$result=$query->result_array();
			if(count($result)>0){
				return $result;
			}else{
				return false;
			}
		}else{
    	    return false;
    	}
	}
	
	public function getQuestionTypeCounts($trade_id="", $select_nos= array())
	{      
		$this->db->from("tbl_questions");
		$this->db->select('count(tbl_questions.qid) as ques_count, tbl_questions.question_type,GROUP_CONCAT(DISTINCT language_name SEPARATOR ",") AS language_name');  
		$this->db->join('tbl_language_questions', 'tbl_language_questions.qid = tbl_questions.qid','LEFT');	
		$this->db->join('tbl_languages', 'tbl_languages.language_id = tbl_language_questions.lid','LEFT');
		$this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_questions.nos_id');		
		$this->db->where('tbl_questions.trade_id',$trade_id);
		if(count($select_nos) > 0) {
			$this->db->where_in('tbl_questions.nos_id',$select_nos);
		}
		$this->db->where('tbl_questions.status',1);
		$this->db->group_by('tbl_questions.question_type');
		
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();
        return $query->result_array();
    }
	
	public function getQuestionByTradeIdNosCode($trade_id, $nos_id)
	{      
		$this->db->from("tbl_questions");
		$this->db->select('tbl_questions.qid as question_id, tbl_questions.question_type, tbl_questions.question, tbl_questions.option_a, tbl_questions.option_b, tbl_questions.option_c, tbl_questions.option_d, tbl_questions.correct_ans, tbl_questions.marks, tbl_language_questions.question as lan_ques,tbl_language_questions.option_a as lan_option_a,tbl_language_questions.option_b as lan_option_b,tbl_language_questions.option_c as lan_option_c,tbl_language_questions.option_d as lan_option_d, tbl_languages.language_name');  
		
		$this->db->join('tbl_language_questions', 'tbl_language_questions.qid = tbl_questions.qid','LEFT');	
		$this->db->join('tbl_languages', 'tbl_languages.language_id = tbl_language_questions.lid','LEFT');
		
		$this->db->where('tbl_questions.trade_id',$trade_id);
		$this->db->where('tbl_questions.nos_id',$nos_id);		
		$this->db->where('tbl_questions.status',1);
		
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();
        return $query->result_array();
    }
	
	
	public function getQuestionById($qid)
	{      
		$this->db->from("tbl_questions");
		$this->db->select('tbl_questions.*, tbl_language_questions.question as lan_ques,tbl_language_questions.option_a as lan_option_a,tbl_language_questions.option_b as lan_option_b,tbl_language_questions.option_c as lan_option_c,tbl_language_questions.option_d as lan_option_d, tbl_languages.language_name');  
		
		$this->db->join('tbl_language_questions', 'tbl_language_questions.qid = tbl_questions.qid','LEFT');	
		$this->db->join('tbl_languages', 'tbl_languages.language_id = tbl_language_questions.lid','LEFT');
		
		$this->db->where('tbl_questions.qid',$qid);
		
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();
        return $query->result_array();
    }
	
	public function getLanguageIds()
	{      
		$this->db->from("tbl_languages");
		$this->db->select('language_id, language_name');  
		
		$query = $this->db->get();
        //echo "<br> str ".$this->db->last_query();
        return $query->result_array();
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
	
}