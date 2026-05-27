<?php defined('BASEPATH') or exit('No direct script access allowed');  


class Trades extends MY_Controller
{
    //
    public $CI;

    /**
     * An array of variables to be passed through to the
     * view, layout,....
     */
    protected $data = array();

    /**
     * [__construct description]
     *
     * @method __construct
     */
    public function __construct()
    {
        // To inherit directly the attributes of the parent class.
        parent::__construct();
        $this->load->model('trades_model');
        $this->load->model('Mdmaster');

        $this->require_module_permission('masters');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function list()
    {
        $this->require_permission('view_masters');

        $data['title'] = 'Trade/QP Name';  // Set the title here
        
        $this->render_page('admin/masters/list-trades',$data);
    }
    
    function getLists(){
        $data = $row = array();
        
        // Fetch member's records
        $tradesData = $this->trades_model->getRows($_POST);
        
        $i = $_POST['start'];
        foreach($tradesData as $trade){
            $no_of_nos = ($trade['no_of_nos'] > 0) ? '<button type="button" class="btn btn-primary mb-2" onclick="getNosDetails('.$trade['trade_id'].');">'.$trade['no_of_nos'].'</button><span id="spin_'.$trade['trade_id'].'" style="display:none;" class="fa-stack fa-lg"><i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span>' : 0;
            
            $i++;
            
            $status = ($trade['status'] == 1) ? '<span class="badge light badge-success border-0">Active</span>' : '<span class="badge light badge-danger border-0">Inactive</span>';
            $action =   '<div class="d-flex">
							<a href="'.site_url('edit-trade-nos/'. $trade['trade_id']).'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>';
                            //if($no_of_nos == 0) {
                                $action .= '<a href="'.site_url('delete-trade-nos/'. $trade['trade_id']).'" onclick="return confirm(\'Are you sure you want to delete this record?\');" class="btn btn-danger shadow btn-xs sharp">
                                                <i class="fa fa-trash"></i>
                                            </a>';     
                            //}
            $action .=  '</div>';
            
            $data[] = array($i, $trade['trade_code'],$trade['trade_name'],$trade['ssc_code'],$trade['total_marks'],$trade['pass_percentage'],$no_of_nos,$status,$action);
        }
        
        /*echo "<pre>";
        print_r($tradesData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->trades_model->countAll(),
            "recordsFiltered" => $this->trades_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
    
    public function viewAddEditForm($trade_id = 0)
    {
        $this->require_permission('add_masters');

        $data['title'] = 'Add Trade/QP';  // Set the title here
        $condition = "status = 1";
        $data['arr_ssc'] = $this->Mdmaster->getAllRecords('tbl_sector_skill_council',$condition,'ssc_title','ASC');
        
        $condition = "status = 1";
        $data['arr_nos'] = $this->Mdmaster->getAllRecords('tbl_national_occupational_standards',$condition,'nos_code','ASC');

        $condition = "";
        $data['arr_nsfq_level'] = $this->Mdmaster->getAllRecords('tbl_nsfq_levels',$condition,'nsfq_level','ASC');

        $condition = "";
        $data['arr_trade_version'] = $this->Mdmaster->getAllRecords('tbl_trade_version',$condition,'trade_version','ASC');
        
        $arr_trade_nos_details = array();
        $arr_trade_mapped_nos_master_ids = array();
        
        if($trade_id > 0) {
            $data['title'] = 'Edit Trade/QP';  // Set the title here
            
            $arr_trade_details = $this->trades_model->getTradeDetails($trade_id);
            if($arr_trade_details != false) {
                foreach($arr_trade_details as $key => $details) {
                    $arr_trade_mapped_nos_master_ids[$details['tmtn_id']]  = $details['tmtn_id'];
                    
                    $arr_trade_nos_details[$key]['nos_id'] = $details['nos_id'];
                    $arr_trade_nos_details[$key]['theory_marks'] = $details['theory_marks'];
                    $arr_trade_nos_details[$key]['practical_skill_marks'] = $details['practical_skill_marks'];
                    $arr_trade_nos_details[$key]['practical_marks'] = $details['practical_marks'];
                    $arr_trade_nos_details[$key]['viva_marks'] = $details['viva_marks'];
                    $arr_trade_nos_details[$key]['total_nos_marks'] = $details['total_nos_marks'];
                }
            }
            
            //echo "<br> str ".$this->db->last_query();
            //exit;
            
            /*echo "<pre>";
            print_r($arr_trade_nos_details);
            echo "</pre>";
            exit;*/
            
            $data['arr_trade_details'] = $arr_trade_details;
            $data['arr_trade_nos_details'] = $arr_trade_nos_details;
            $data['arr_trade_mapped_nos_master_ids'] = $arr_trade_mapped_nos_master_ids;
        }
        
        $data['trade_id'] = $trade_id;
        
        $this->render_page('admin/masters/add-edit-trades-nos',$data);
    }
    
    public function save()
    {
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/
        $trade_id = $this->input->post('trade_id');
        
        $data = array(
            'trade_code' => trim($this->input->post('trade_code')),
			'trade_name' => trim($this->input->post('trade_name')),
			'ssc_id' => $this->input->post('ssc_id'),
			'pass_percentage' => trim($this->input->post('pass_percentage')),
			'no_of_nos' => $this->input->post('no_of_nos'),
            'nsfq_id' => $this->input->post('nsfq_id'),
            'trade_version_id' => $this->input->post('trade_version_id'),
            'nqr_code' => $this->input->post('nqr_code'),
            'optional_exam_type'  => (array_key_exists('optional_exam_type',$this->input->post())) ? implode(",",$this->input->post('optional_exam_type')) : "",
            'total_marks' => $this->input->post('total_marks'),
			'status' => $this->input->post('status'),
		);
		
        if($trade_id == 0) { //Insert
            $trade_id = $this->Mdmaster->addRecord($data,'tbl_trades');
            
            $total_nos = $this->input->post('no_of_nos');
            if($total_nos > 0) {
                $arr_nos_id = $this->input->post('nos_id');
                $arr_theory_marks = $this->input->post('theory_marks');
                $arr_practical_skill_marks = $this->input->post('practical_skill_marks');
                $arr_practical_marks = $this->input->post('practical_marks');
                $arr_viva_marks = $this->input->post('viva_marks');
                $arr_total_nos_marks = $this->input->post('total_nos_marks');
                
                for($i=0; $i<$total_nos; $i++) {
                    //Map nos to trade
                    $insData[] = array(
            			'trade_id' => $trade_id,
                        'nos_id' => $arr_nos_id[$i],
                        'theory_marks' => trim($arr_theory_marks[$i]),
                        'practical_skill_marks' => trim($arr_practical_skill_marks[$i]),
                        'practical_marks' => trim($arr_practical_marks[$i]),
                        'viva_marks' => trim($arr_viva_marks[$i]),
                        'total_nos_marks' => trim($arr_total_nos_marks[$i]),
                    );
                }
                if(count($insData) > 0) {
                    $this->db->insert_batch('tbl_map_trade_nos', $insData);
                }
            }
            
            $this->session->set_flashdata('msg', 'Data created successfully');
        }
        else { //Update
            $this->db->where('trade_id', $trade_id);
            $query = $this->db->update('tbl_trades', $data);
            
            $arr_trade_mapped_nos_master_ids = explode(",",$this->input->post('hdn_trade_mapped_nos_master_ids'));
            $total_mapped_nos = count($arr_trade_mapped_nos_master_ids);
            $total_nos = $this->input->post('no_of_nos');
            $arr_nos_id = $this->input->post('nos_id');
            $arr_theory_marks = $this->input->post('theory_marks');
            $arr_practical_skill_marks = $this->input->post('practical_skill_marks');
            $arr_practical_marks = $this->input->post('practical_marks');
            $arr_viva_marks = $this->input->post('viva_marks');
            $arr_total_nos_marks = $this->input->post('total_nos_marks');
            
            if($total_nos > 0) {
                if($total_mapped_nos == $total_nos) { //Update
                    for($i=0; $i<$total_nos; $i++) {
                        //Map nos to trade
                        $updData = array(
                			'nos_id' => $arr_nos_id[$i],
                            'theory_marks' => trim($arr_theory_marks[$i]),
                            'practical_skill_marks' => trim($arr_practical_skill_marks[$i]),
                            'practical_marks' => trim($arr_practical_marks[$i]),
                            'viva_marks' => trim($arr_viva_marks[$i]),
                            'total_nos_marks' => trim($arr_total_nos_marks[$i]),
                        );
                        
                        $this->db->where('tmtn_id', $arr_trade_mapped_nos_master_ids[$i]);
                        $this->db->where('trade_id', $trade_id);
                        $query = $this->db->update('tbl_map_trade_nos', $updData);
                    }
                }
                else if($total_mapped_nos > $total_nos) { //Update
                    $arr_assigned_ids = array();
                    //Update the nos and Delete remaining Nos 
                    //Map nos to trade
                    for($i=0; $i<$total_nos; $i++) {
                        $updData = array(
                			'nos_id' => $arr_nos_id[$i],
                            'theory_marks' => trim($arr_theory_marks[$i]),
                            'practical_skill_marks' => trim($arr_practical_skill_marks[$i]),
                            'practical_marks' => trim($arr_practical_marks[$i]),
                            'viva_marks' => trim($arr_viva_marks[$i]),
                            'total_nos_marks' => trim($arr_total_nos_marks[$i]),
                        );
                        
                        if(array_key_exists($i,$arr_trade_mapped_nos_master_ids)) {
                            $this->db->where('tmtn_id', $arr_trade_mapped_nos_master_ids[$i]);
                            $this->db->where('trade_id', $trade_id);
                            $query = $this->db->update('tbl_map_trade_nos', $updData);
                            
                            $arr_assigned_ids[$arr_trade_mapped_nos_master_ids[$i]] = $arr_trade_mapped_nos_master_ids[$i];
                        }
                    }
                    //Delete Remaining Data
                    foreach($arr_trade_mapped_nos_master_ids as $tmtn_id) {
                        if(!array_key_exists($tmtn_id,$arr_assigned_ids)) {
                             $this->db->delete('tbl_map_trade_nos', array('tmtn_id' => $tmtn_id));
                        }
                    }
                }
                else if($total_mapped_nos < $total_nos) { //Update
                    //Update the nos and Insert Nos 
                    for($i=0; $i<$total_nos; $i++) {
                        $updData = array(
                			'nos_id' => $arr_nos_id[$i],
                            'theory_marks' => trim($arr_theory_marks[$i]),
                            'practical_skill_marks' => trim($arr_practical_skill_marks[$i]),
                            'practical_marks' => trim($arr_practical_marks[$i]),
                            'viva_marks' => trim($arr_viva_marks[$i]),
                            'total_nos_marks' => trim($arr_total_nos_marks[$i]),
                        );
                        
                        if(array_key_exists($i,$arr_trade_mapped_nos_master_ids)) {
                            $this->db->where('tmtn_id', $arr_trade_mapped_nos_master_ids[$i]);
                            $this->db->where('trade_id', $trade_id);
                            $query = $this->db->update('tbl_map_trade_nos', $updData);
                            
                            $arr_assigned_ids[$arr_trade_mapped_nos_master_ids[$i]] = $arr_trade_mapped_nos_master_ids[$i];
                        }
                        else {
                            $updData['trade_id'] = $trade_id;
                            $this->db->insert('tbl_map_trade_nos', $updData);
                        }
                    }
                }
            }
            
            $this->session->set_flashdata('msg', 'Data updated successfully');
        }
        
        redirect('list-trades');
    }
    
    public function delete($trade_id) {
        $this->require_permission('delete_masters');
        
        $this->db->where('trade_id', $trade_id);
        $query = $this->db->update('tbl_trades', array('status' => 2));

        $this->db->where('trade_id', $trade_id);
        $query = $this->db->update('tbl_map_trade_nos', array('status' => 2));
	    //$result=$this->db->delete('tbl_trades');
	    //$this->db->delete('tbl_map_trade_nos', array('trade_id' => $trade_id));
        
        $this->session->set_flashdata('msg', 'Data deleted successfully');
        redirect('list-trades');
    }
    
    public function CheckDuplicateTradeCode() {
        $trade_code = $this->input->post('trade_code');
        $trade_id = $this->input->post('trade_id');
        
        $condition = ($trade_id > 0) ? " trade_id != ".$trade_id : "";
        $validate = $this->Mdmaster->checkDuplicate('trade_code',$trade_code,'tbl_trades',$condition);
        //echo "<br> str ".$this->db->last_query();exit;
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }

    public function CheckDuplicateNqrCode() {
        $nqr_code = $this->input->post('nqr_code');
        $trade_id = $this->input->post('trade_id');
        
        $condition = ($trade_id > 0) ? " trade_id != ".$trade_id : "";
        $validate = $this->Mdmaster->checkDuplicate('nqr_code',$nqr_code,'tbl_trades',$condition);
        //echo "<br> str ".$this->db->last_query();exit;
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }

    public function ViewMappedTradeNos() {
        $trade_id = $this->input->post('trade_id');
        $output = "";
        
        $arr_trade_details = $this->trades_model->getTradeDetails($trade_id);
        //echo "<br> str ".$this->db->last_query();exit;
        if($arr_trade_details != false) {
            $grand_total = 0;
            $grand_total_theory = 0;
            $grand_total_practical_skill = 0;
            $grand_total_practical = 0;
            $grand_total_viva = 0;
            foreach($arr_trade_details as $key => $details) {
                
                $output .= '<tr>';
                $output.= '<td>'.($key+1).'</td>';
                $output.= '<td>'.$details['nos_code'].'</td>';
                $output.= '<td>'.$details['theory_marks'].'</td>';
                $output.= '<td>'.$details['practical_skill_marks'].'</td>';
                $output.= '<td>'.$details['practical_marks'].'</td>';
                $output.= '<td>'.$details['viva_marks'].'</td>';
                $output.= '<td><code>'.$details['total_nos_marks'].'</code></td>';
                $output .= '</tr>';

                $grand_total += $details['total_nos_marks'];
                $grand_total_theory += $details['theory_marks'];
                $grand_total_practical_skill += $details['practical_skill_marks'];
                $grand_total_practical += $details['practical_marks'];
                $grand_total_viva += $details['viva_marks'];
            }
                $output .= '<tr>';
                $output.= '<td colspan="2" style="text-align:center;">Total</td>';
                $output.= '<td><code style="padding:0px;">'.$grand_total_theory.'</code></td>';
                $output.= '<td><code style="padding:0px;">'.$grand_total_practical_skill.'</code></td>';
                $output.= '<td><code style="padding:0px;">'.$grand_total_practical.'</code></td>';
                $output.= '<td><code style="padding:0px;">'.$grand_total_viva.'</code></td>';
                $output.= '<td><code style="padding:0px;">'.$grand_total.'</code></td>';
                $output .= '</tr>';
        }
        
        // Read new token and assing in $data['token']
        $data['token'] = $this->security->get_csrf_hash();
        $data['total_nos'] = ($arr_trade_details == false) ? 0 : count($arr_trade_details);
        $data['nos_details'] = ($arr_trade_details == false) ? 0 : $arr_trade_details[0]['trade_code']."-".$arr_trade_details[0]['trade_name'];
        $data['output'] = $output;
        
        echo json_encode($data);
    }
    
    
   
}
