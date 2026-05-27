<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import_model extends CI_Model{
    
    function __construct() {
      
    }
    
    public function get_questions_details($arrQns)
	{
		$this->db->select('qid,question');
		$this->db->where_in('question', $arrQns);
		$query=$this->db->get('tbl_questions');
		$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
	}

}