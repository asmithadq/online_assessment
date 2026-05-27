<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trades_model extends CI_Model{
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_trades';
        // Set orderable column fields
	    $this->column_order = array(null,'trade_name','trade_code','ssc_code','ssc_title');
	
	    // Set searchable column fields
        $this->column_search = array('trade_name','trade_code','ssc_code','ssc_title');
	    
        // Set default order
        $this->order = array('trade_name' => 'asc');
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
	    $this->db->select('tbl_trades.*,ssc_code,ssc_title'); 
        $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_trades.ssc_id');		
        $this->db->where('tbl_trades.status != 2');
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
    
    function getTradeDetails($trade_id){
	
    	$this->db->select('tbl_trades.*,ssc_code,ssc_title,tmtn_id,tbl_map_trade_nos.nos_id,theory_marks,practical_skill_marks,practical_marks,viva_marks,total_nos_marks,nos_code,nos_title'); 
    	$this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_trades.ssc_id');	
    	$this->db->join('tbl_map_trade_nos', 'tbl_map_trade_nos.trade_id = tbl_trades.trade_id');
    	$this->db->join('tbl_national_occupational_standards', 'tbl_national_occupational_standards.nos_id = tbl_map_trade_nos.nos_id');
    	$this->db->where('tbl_trades.trade_id', $trade_id);
    	$this->db->where('tbl_trades.status', 1);
    	//$this->db->where('tbl_map_trade_nos.status', 1);
    	$this->db->order_by('tbl_map_trade_nos.tmtn_id','ASC');
    	$query=$this->db->get('tbl_trades');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

}