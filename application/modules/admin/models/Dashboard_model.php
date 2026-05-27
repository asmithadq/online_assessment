<?php
class Dashboard_model extends CI_Model {

    protected $table;
    protected $column_order = array();
    protected $column_search = array();
    protected $order = array();
    
    function __construct() {
        // Set table name
          $this->table = 'tbl_training_batches';
          // Set orderable column fields
          $this->column_order = array('batch_id','trade_id',null,'tb_start_date_time','tb_end_date_time','assessor_id','tb_assessment_status');
      
          // Set searchable column fields
          $this->column_search = array('batch_id','trade_id',null,'tb_start_date_time','tb_end_date_time','assessor_id','tb_assessment_status');
          
          // Set default order
          $this->order = array('batch_id' => 'asc');
      }
      
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
           
          $first_date = date("Y-m-d"); 
          $second_date = date("Y-m-d", strtotime("+7 day"));
          
          $this->db->from($this->table);
          //join
          $this->db->select("count(tbl_students.student_id) as student_count, tbl_training_batches.*, tbl_sector_skill_council.ssc_code, tbl_sector_skill_council.ssc_title, tbl_assessor.assessor_name, tbl_trades.trade_code, tbl_trades.trade_name, tbl_training_centers.name as tc_name"); 
          
          $this->db->join('tbl_students', 'tbl_students.tb_id = tbl_training_batches.tb_id');
          $this->db->join('tbl_sector_skill_council', 'tbl_sector_skill_council.ssc_id = tbl_training_batches.ssc_id','LEFT');$this->db->join('tbl_trades', 'tbl_trades.trade_id = tbl_training_batches.trade_id','LEFT');	
          $this->db->join('tbl_training_centers', 'tbl_training_centers.tc_id = tbl_training_batches.tc_id','LEFT');	
          $this->db->join('tbl_assessor','tbl_training_batches.assessor_id = tbl_assessor.assessor_id','LEFT');  
          
          $this->db->where("DATE_FORMAT(tbl_training_batches.tb_assessment_date,'%Y-%m-%d') >='$first_date'");
          $this->db->where("DATE_FORMAT(tbl_training_batches.tb_assessment_date,'%Y-%m-%d') <='$second_date'");
          
          $this->db->group_by('tbl_training_batches.tb_id');
          
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
	
	// Method to get the count of partners
    public function get_partners_count() {
        return $this->db->count_all_results('tbl_training_partners');
    }
	
	// Method to get the count of centers
    public function get_centers_count() {
        return $this->db->count_all_results('tbl_training_centers');
    }
	
    // Method to get the count of batches
    public function get_batch_count($start_date = NULL,$end_date = NULL) {
        $start_date = ($start_date != NULL) ? $start_date : date('Y-m-01');
        $end_date   = ($end_date != NULL)   ? $end_date : date("Y-m-t");
        return  $this->db->where('tb_assessment_date >=', $start_date)
                        ->where('tb_assessment_date <=', $end_date)
                        ->count_all_results('tbl_training_batches');
    }
	
	 // Method to get the count of assessors
    public function get_assessors_count() {
        return $this->db->count_all_results('tbl_assessor');
    }
	
	// Method to get the count of students with assesment status "Completed"
	public function get_students_assessment_completed_count() {
    // Assuming $this->db is your database object
    // Adjust the column names as per your actual table structure
    return $this->db->where('student_assessment_status', 'Completed')->count_all_results('tbl_students');
	}	
	
	// Method to get the count of students with assesment status "Pending"
	public function get_students_assessment_pending_count() {
    // Assuming $this->db is your database object
    // Adjust the column names as per your actual table structure
    return $this->db->where('student_assessment_status', 'Pending')->count_all_results('tbl_students');
	}

    // Method to get the count of batches with tb_assessment_status "Pending"
	public function get_batch_inprocess_count($start_date = NULL,$end_date = NULL) {
        $start_date = ($start_date != NULL) ? $start_date : date('Y-m-01');
        $end_date   = ($end_date != NULL)   ? $end_date : date("Y-m-t");

        return $this->db->where('tb_assessment_status', 'Pending')
                        ->where('tb_assessment_date >=', $start_date)
                        ->where('tb_assessment_date <=', $end_date)
                        ->count_all_results('tbl_training_batches');
    }

    // Method to get the count of batches with tb_assessment_status "Pending"
	public function get_batch_completed_count($start_date = NULL,$end_date = NULL) {
        $start_date = ($start_date != NULL) ? $start_date : date('Y-m-01');
        $end_date   = ($end_date != NULL)   ? $end_date : date("Y-m-t");

        return $this->db->where('tb_assessment_status', 'Completed')
                        ->where('tb_assessment_date >=', $start_date)
                        ->where('tb_assessment_date <=', $end_date)
                        ->count_all_results('tbl_training_batches');
    }
	
	// Method to get the count of batches with result status "Pending"
	public function get_batch_results_pending_count($start_date = NULL,$end_date = NULL) {
        $start_date = ($start_date != NULL) ? $start_date : date('Y-m-01');
        $end_date   = ($end_date != NULL)   ? $end_date : date("Y-m-t");

        return $this->db->where('result_processing', 'Pending')
                        ->where('tb_assessment_date >=', $start_date)
                        ->where('tb_assessment_date <=', $end_date)
                        ->count_all_results('tbl_training_batches');
    }
	
	
	// Method to get the count of batches with result status "Completed"
	public function get_batch_results_completed_count($start_date = NULL,$end_date = NULL) {
        $start_date = ($start_date != NULL) ? $start_date : date('Y-m-01');
        $end_date   = ($end_date != NULL)   ? $end_date : date("Y-m-t");

        return $this->db->where('result_processing', 'Completed')
                        ->where('tb_assessment_date >=', $start_date)
                        ->where('tb_assessment_date <=', $end_date)
                        ->count_all_results('tbl_training_batches');
    }
}
?>