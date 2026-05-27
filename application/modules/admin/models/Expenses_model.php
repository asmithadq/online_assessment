<?php
class Expenses_model extends CI_Model {
    
    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
        $this->table = 'tbl_batch_expenses';
        // Set orderable column fields
	    $this->column_order = array(null,'batch_id','assessor_code','assessor_name','total_travel_charges','printing_charges','courier_charges','professional_charges','grand_total','advance_amount','total_amount_due');
	
	    // Set searchable column fields
        $this->column_search = array('batch_id','assessor_code','assessor_name','total_travel_charges','printing_charges','courier_charges','professional_charges','grand_total','advance_amount','total_amount_due');
	    
        // Set default order
        $this->order = array('created_dts' => 'asc');
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
	    $this->db->select('tbl_batch_expenses.*,CONCAT(assessor_code,"-", assessor_name) AS assessor_name, tbl_training_batches.assessor_id as assessor_id,batch_id,tb_assessment_date,
                            tbl_training_partners.name as tp_name');   
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_batch_expenses.tb_id');
        $this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id'); 
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');
        if($postData['expense_status'] == 'Paid-Rejected') {
            $this->db->where_in('expense_status',array('Paid','Rejected'));
        }
        else {
            $this->db->where('expense_status',$postData['expense_status']);
        }
        
		 
		if(array_key_exists('assessor_id',$postData) && $postData['assessor_id'] != "")
		{
			$this->db->where('tbl_training_batches.assessor_id',$postData['assessor_id']);
		}
		if(array_key_exists('start_date',$postData) && $postData['start_date'] != "")
		{
			$this->db->where("DATE_FORMAT(tbl_batch_expenses.created_dts,'%Y-%m-%d') >='".$postData['start_date']."'");
		}
		if(array_key_exists('end_date',$postData) && $postData['end_date'] != "")
		{
			$this->db->where("DATE_FORMAT(tbl_batch_expenses.created_dts,'%Y-%m-%d') <='".$postData['end_date']."'");
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
    
    function getExpenseDetails($be_id){
	
    	$this->db->select('tbl_batch_expense_details.*,tbl_batch_expenses.*,batch_id,tb_assessment_date,assessor_id');  
        $this->db->join('tbl_batch_expenses', 'tbl_batch_expenses.be_id = tbl_batch_expense_details.be_id');
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_batch_expenses.tb_id');
        $this->db->where("STR_TO_DATE(travel_date, '%Y-%m-%d') IS NOT NULL");
        $this->db->where('tbl_batch_expense_details.be_id',$be_id);
    	$this->db->order_by('tbl_batch_expense_details.ted_id','ASC');
    	$query=$this->db->get('tbl_batch_expense_details');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getExpenses($expense_status){
        $this->db->select('tbl_batch_expenses.*,batch_id'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_batch_expenses.tb_id');
        $this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id');
        $this->db->where('expense_status',$expense_status);
        $query=$this->db->get('tbl_batch_expenses');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

    function getAssessorsExpensesReport($postData) {
        $this->db->select('tbl_batch_expenses.*,CONCAT(assessor_code,"-", assessor_name) AS assessor_name, tbl_training_batches.assessor_id as assessor_id,batch_id,tb_assessment_date,
                            tbl_training_partners.name as tp_name,tbl_batch_expense_details.*');   
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_batch_expenses.tb_id');
        $this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id'); 
        $this->db->join('tbl_training_partners', 'tbl_training_partners.tp_id = tbl_training_batches.tp_id');
        $this->db->join('tbl_batch_expense_details', 'tbl_batch_expense_details.be_id = tbl_batch_expenses.be_id');
        $this->db->where("STR_TO_DATE(travel_date, '%Y-%m-%d') IS NOT NULL");
        if($postData['expense_status'] == 'Paid-Rejected') {
            $this->db->where_in('expense_status',array('Paid','Rejected'));  
        }
        else {
            $this->db->where('expense_status',$postData['expense_status']);
        }
        		 
		if(array_key_exists('assessor_id',$postData) && $postData['assessor_id'] != "")
		{
			$this->db->where('tbl_training_batches.assessor_id',$postData['assessor_id']);
		}
		if(array_key_exists('start_date',$postData) && $postData['start_date'] != "")
		{
			$this->db->where("DATE_FORMAT(tbl_batch_expenses.created_dts,'%Y-%m-%d') >='".$postData['start_date']."'");
		}
		if(array_key_exists('end_date',$postData) && $postData['end_date'] != "")
		{
			$this->db->where("DATE_FORMAT(tbl_batch_expenses.created_dts,'%Y-%m-%d') <='".$postData['end_date']."'");
		}
        $this->db->order_by('tbl_batch_expenses.be_id,tbl_batch_expense_details.ted_id','ASC');
        $query=$this->db->get('tbl_batch_expenses');
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }
}
?>
