<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assessors_model extends CI_Model{
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_assessor';
        // Set orderable column fields
	    $this->column_order = array(null,'assessor_name','assessor_code','assessor_email','assessor_mobile','state_name','dist_name','assessor_status');
	
	    // Set searchable column fields
        $this->column_search = array('assessor_name','assessor_code','assessor_email','assessor_mobile','state_name','dist_name','assessor_status');
	    
        // Set default order
        $this->order = array('assessor_name' => 'asc');
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
	    $this->db->select("tbl_assessor.*, state_name,dist_name,GROUP_CONCAT(ssc_code ORDER BY ssc_code SEPARATOR ', ') AS ssc_codes"); 
	    $this->db->join('tbl_states', 'tbl_states.state_id = tbl_assessor.state_id','LEFT');
	    $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_assessor.district_id','LEFT');	
        $this->db->join('tbl_map_assessor_sector_skill_councils','tbl_map_assessor_sector_skill_councils.assessor_id = tbl_assessor.assessor_id');
        $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_map_assessor_sector_skill_councils.ssc_id');
        $this->db->group_by('tbl_map_assessor_sector_skill_councils.assessor_id');
		
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
    
    function getAssessorDetails($assessor_id)
	{	
    	$this->db->select('*'); 
    	$this->db->join('tbl_map_assessor_sector_skill_councils','tbl_map_assessor_sector_skill_councils.assessor_id = tbl_assessor.assessor_id','LEFT');
    	$this->db->where('tbl_assessor.assessor_id', $assessor_id);
    	$query=$this->db->get('tbl_assessor');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
    
    function getAssessorDetailsBySsc($ssc_id)
	{	
    	$this->db->select('tbl_assessor.*'); 
    	$this->db->join('tbl_map_assessor_sector_skill_councils','tbl_map_assessor_sector_skill_councils.assessor_id = tbl_assessor.assessor_id');
        $this->db->where('ssc_id', $ssc_id);
    	$query=$this->db->get('tbl_assessor');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
	
	function getAssessorSectorSkillCouncil($assessor_id = 0)
	{	
    	$this->db->select('tbl_map_assessor_sector_skill_councils.assessor_id,tbl_sector_skill_council.ssc_title'); 
    	$this->db->join('tbl_map_assessor_sector_skill_councils','tbl_map_assessor_sector_skill_councils.assessor_id = tbl_assessor.assessor_id','LEFT');
		$this->db->join('tbl_sector_skill_council','tbl_sector_skill_council.ssc_id = tbl_map_assessor_sector_skill_councils.ssc_id','LEFT');
    	if($assessor_id > 0) {
            $this->db->where('tbl_assessor.assessor_id', $assessor_id); 
        }
        $query=$this->db->get('tbl_assessor');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
    
}