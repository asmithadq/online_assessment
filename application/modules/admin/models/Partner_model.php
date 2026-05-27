<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Partner_model extends CI_Model{
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_training_partners';
        // Set orderable column fields
	    $this->column_order = array(null,'name','tp_code','state_code','state_name','dist_code','dist_name','email','website','mobile');
	
	    // Set searchable column fields
        $this->column_search = array('name','tp_code','state_code','state_name','dist_code','dist_name','email','website','mobile');
	    
        // Set default order
        $this->order = array('name' => 'asc');
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
	    $this->db->select('tbl_training_partners.*,state_code,state_name,dist_code,dist_name, tbl_banks.bank_name as bk_name'); 
        $this->db->join('tbl_districts', 'tbl_districts.dist_id = tbl_training_partners.district','LEFT');		
        $this->db->join('tbl_states', 'tbl_states.state_id = tbl_training_partners.state','LEFT');		
		$this->db->join('tbl_banks', 'tbl_banks.bank_id = tbl_training_partners.bank_name','LEFT');
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
    
    function getPartnerDetails($tp_id = 0){
	
    	$this->db->select('tbl_training_partners.*,tbl_map_partner_sector_skill_councils.ssc_id,ssc_code,ssc_title'); 
    	$this->db->join('tbl_map_partner_sector_skill_councils','tbl_map_partner_sector_skill_councils.tp_id = tbl_training_partners.tp_id','LEFT');
        $this->db->join('tbl_sector_skill_council','tbl_sector_skill_council.ssc_id = tbl_map_partner_sector_skill_councils.ssc_id','LEFT');
        if($tp_id > 0) {
    	    $this->db->where('tbl_training_partners.tp_id', $tp_id);
        }    
    	$query=$this->db->get('tbl_training_partners');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
    
    function getPartnersDetailsBySsc($ssc_id){
	
    	$this->db->select('tbl_training_partners.*,ssc_id'); 
    	$this->db->distinct();
    	$this->db->join('tbl_map_partner_sector_skill_councils','tbl_map_partner_sector_skill_councils.tp_id = tbl_training_partners.tp_id');
    	$this->db->where('tbl_map_partner_sector_skill_councils.ssc_id', $ssc_id);
    	$this->db->where('tbl_training_partners.status', 1);
        $this->db->order_by('tbl_training_partners.name');
    	$query=$this->db->get('tbl_training_partners');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
    
    function getCenterDetailsByPartnerSsc($tp_id,$ssc_id){
	
    	$this->db->select('tbl_training_centers.*,ssc_id'); 
    	$this->db->distinct();
    	$this->db->join('tbl_map_center_sector_skill_councils','tbl_map_center_sector_skill_councils.tc_id = tbl_training_centers.tc_id');
    	$this->db->where('tp_id', $tp_id);
    	$this->db->where('tbl_map_center_sector_skill_councils.ssc_id', $ssc_id);
    	$this->db->where('tbl_training_centers.status', 1);
    	$query=$this->db->get('tbl_training_centers');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

}