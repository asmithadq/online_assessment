<?php
class Mdmaster extends CI_Model{
public function __construct()
	{
		parent::__construct();
	}
function addRecord($data=null,$tablename)
	{
	    if(!empty($data)){
			
			
			if($this->db->insert($tablename,$data))
			{
				
				return $this->db->insert_id();
				
			}
			return false;
		 }
		return false;
	}
function updateRecord($updateArray=null,$condition="",$table){
	if(!empty($updateArray)){		
			
			
			if($this->db->update($table,$updateArray,$condition))
			{
				
				return true;
				
			}
			return false;
		 }
		return false;
}
function copyRecord($tableName,$record){
	$isAdded="";
	if(!empty($record)){
		$isAdded=$this->db->insert($tableName,$record);
	}
	return $isAdded;
}

public function checkIfExists($field,$value,$table){
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

public function checkIfExistsMultiple($where,$table){
	$this->db->where($where);
	$query=$this->db->get($table);
	$result=$query->result_array();
	if(count($result)>0){
		return true;
	}
	else{
		return false;
	}
}

public function checkIfExistsUpdate($field,$value,$table){
	$this->db->where($field,$value);
	$query=$this->db->get($table);
	$result=$query->row_array();
	if(count($result)>1){
		return $result;
	}
	else{
		return false;
	}
}


function run_query($query)
	{
		$result=$this->db->query($query);
		$data=$result->result_array();
		return $data;
	}
function run_query1($query)
	{
		$result=$this->db->query($query);
		$data=$result->row_array();
		return $data;
	}	
	
function run_update_query($query){
	
	$result = $this->db->query($query);
	if($this->db->affected_rows()>0)
		return true;
	else
		return false;
}	
function deleteRecord($field,$value,$table){
	$this->db->where($field, $value);
	$result=$this->db->delete($table);
	$cnt=$this->db->affected_rows();
	if($cnt>0){
		return true;
	}
	else{
		return false;
	}
}
function deleteRecord1($field,$value,$table){
	$this->db->where($field, $value);
	$result=$this->db->delete($table);
	$cnt=$this->db->affected_rows();
	if($cnt>0){
		return "1";
	}
	else{
		return "0";
	}
}
function getMaxSeq($table_name,$field_name,$status=null,$condition=null){
	$this->db->select_max($field_name);
	if($status!=null)
	{
		$this->db->where($status,1);	
	}
	if($condition!=null){
	 	$condition;
	 	$this->db->where($condition);
	}	
	$query = $this->db->get($table_name);
	$result = $query->row_array();
	return $result[$field_name];
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
function checkDuplicate1($field,$value,$table,$condition=NULL){
	if($condition!=NULL){
		$this->db->where($condition);
	}
	$this->db->where($field,$value);
	$query=$this->db->get($table);
	$result=$query->result_array();
	if(count($result)>0){
		return 1;
	}
	else{
		return 0;
	}
}
function checkDuplicateMultiple($condArr,$table,$id=""){
	
	$this->db->where($condArr);
	$query=$this->db->get($table);
	$result=$query->result_array();
	if(count($result)>0){
		return true;
	}
	else{
		return false;
	}
}
function checkDuplicateMultiple1($condArr,$table,$id=""){
	
	$this->db->where($condArr);
	$query=$this->db->get($table);
	$result=$query->result_array();
	if(count($result)>0){
		return 1;
	}
	else{
		return 0;
	}
}
function checkCompDuplicate($field,$value,$table,$comapre_field="",$id=""){
	
	$this->db->where($comapre_field.'!=',$id);
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
function getConditionalRecords($field,$value,$tableName,$order='ASC',$orderByField=NULL){
	$this->db->where($field,$value);
	if($orderByField!=NULL){
		$this->db->order_by($orderByField, $order);
	}
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getConditionalRecordsFields($field,$value,$fields_array,$tableName,$order="ASC",$orderByField=NULL){
	$this->db->select(implode(",",$fields_array));
	$this->db->where($field,$value);
	if($orderByField!=NULL){
		$this->db->order_by($orderByField, $order);
	}
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getConditionalRecordOneField($field,$value,$select_field,$tableName,$order="ASC",$orderByField=NULL){
	$this->db->select($select_field);
	$this->db->where($field,$value);
	if($orderByField!=NULL){
		$this->db->order_by($orderByField, $order);
	}
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getMultiCondRecordOneField($condArray,$select_field,$tableName,$order="ASC",$orderByField=NULL){
	$this->db->select($select_field);
	$this->db->where($condArray);
	if($orderByField!=NULL){
		$this->db->order_by($orderByField, $order);
	}
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getMultiCondRecordOneField1($condArray,$select_field,$tableName,$order="ASC",$orderByField=NULL){
	$this->db->select($select_field);
	$this->db->where($condArray);
	if($orderByField!=NULL){
		$this->db->order_by($orderByField, $order);
	}
	$query=$this->db->get($tableName);
	$result=$query->row_array();
	if($result){
	return $result[$select_field];
	}
	else{
		$result="";
		return $result;
	}
}
function getMultiCondRecordmultiFields($condArray,$fields_array,$tableName,$order="ASC",$orderByField=NULL){
	$this->db->select(implode(",",$fields_array));
	$this->db->where($condArray);
	if($orderByField!=NULL){
		$this->db->order_by($orderByField, $order);
	}
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getAllRecordsFields($fields_array,$tableName){
	$this->db->select(implode(",",$fields_array));
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getOneRecordsFields($field,$value,$fields_array,$tableName){
	$this->db->select(implode(",",$fields_array));
	$this->db->where($field,$value);
	$query=$this->db->get($tableName);
	$result=$query->row_array();
	return $result;
}
function getOneField($field,$value,$select_field,$tableName){
	$this->db->select($select_field);
	$this->db->where($field,$value);
	$query=$this->db->get($tableName);
	$result=$query->row_array();
	if(!empty($result)){
	return $result[$select_field];
	}
	else{
		$result="";
		return $result;
	}
}
function getDetails($field,$value,$tableName){
	$this->db->where($field,$value);
	$query=$this->db->get($tableName);
	$result=$query->row_array();
	return $result;
}
function getMultiConditionalRecords($condArr,$tableName){
	$this->db->where($condArr);
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function get_count($tableName){
	$result=$this->db->count_all($tableName);
	return $result;	 
}

function getRecordsForPagination($limit,$start,$tableName){
	$this->db->limit($limit,$start);
	$query =$this->db->get($tableName);
	return $query->result();
}
function getConditionalCount($field,$value,$tableName){
	$this->db->where($field,$value);
	$num_rows = $this->db->count_all_results($tableName);
	return $num_rows;
}
function getMultiConditionalCount($conditionArray,$tableName){
	$this->db->where($conditionArray);
	$num_rows = $this->db->count_all_results($tableName);
	return $num_rows;
}
function getLimitedRecords($field,$value,$table,$limit){
	
	$this->db->where($field,$value);
	$this->db->limit($limit);
	$query=$this->db->get($table);
	$result=$query->result_array();
	return $result;
}
function getSingleFieldLimitedRecords($field,$value,$select_field,$table,$limit){
	$this->db->select($select_field);
	$this->db->where($field,$value);
	$this->db->limit($limit);
	$query=$this->db->get($table);
	$result=$query->result_array();
	return $result;
}
function getSingleFieldMultiCondLmtdRcrds($condArray,$select_field,$table,$limit){
	$this->db->select($select_field);
	$this->db->where($condArray);
	$this->db->limit($limit);
	$query=$this->db->get($table);
	$result=$query->result_array();
	return $result;
}
function getAllRecordsIn($field,$inArray,$tableName,$condition=NULL){
	$this->db->where_in($field, $inArray);
	if($condition!=NULL){
		$this->db->where($condition);
	}
	$query=$this->db->get($tableName);
	$result=$query->result_array();
	return $result;
}
function getAllModules($status=NULL){
	if($status!=NULL){
	$this->db->where('module_status',$status);
	}
	$query=$this->db->get('modules_mst');
	$result=$query->result_array();
	return $result;
}

public function FetchQuery($sql)
{
	$result = $this->db->query($sql)->result_array();
	return (count($result) > 0) ? $result : false;
}

public function GetAllRecord($table){
	$query=$this->db->get($table);
	$result=$query->result_array();
	return $result;
}

}

?>
