<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');
use Phppot\DataSource;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Expenses extends MY_Controller
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
        $this->load->model('batch_model');
        $this->load->model('expenses_model');
        $this->load->model('Mdmaster');
        $this->load->model('mainModel');
        
        $this->require_module_permission('expenses');
    }
    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function inprocess()
    {
        $this->require_permission('view_expenses');

        $data['title'] = 'Expenses Inprocess';  // Set the title here
        
        $condition = "assessor_status = 'Active'";
        $data['arr_assessors'] = $this->Mdmaster->getAllRecords('tbl_assessor',$condition,'assessor_name','ASC');

        $this->render_page('admin/expenses/list-expenses-inprocess',$data);
    }

    public function completed()
    {
        $this->require_permission('view_expenses');

        $data['title'] = 'Expenses Paid/Rejected';  // Set the title here
        
        $condition = "assessor_status = 'Active'";
        $data['arr_assessors'] = $this->Mdmaster->getAllRecords('tbl_assessor',$condition,'assessor_name','ASC');

        $this->render_page('admin/expenses/list-expenses-paid-rejected',$data);
    }

    function getLists(){
        $data = $row = array();
        $expense_status = $this->input->post('expense_status');
        
        // Fetch expenses records
        $expensesData = $this->expenses_model->getRows($_POST);
        //echo "<br> str ".$this->db->last_query();exit;
        
        $i = $_POST['start'];
        foreach($expensesData as $row){
            $i++;
            
            $action =   '<div class="d-flex">
                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" onclick="getExpenseDetails('.$row['be_id'].');"><i class="fas fa-eye"></i></a> 
						</div>';

            $batch_details = '<div class="products">
                                <div>
                                    <h6>'.$row['batch_id'].'</h6>
                                    <span>Assessment Date: '.date('d-m-Y',strtotime($row['tb_assessment_date'])).'<br>
                                          TP Name: '.$row['tp_name'].'</span>
                                </div>	
                            </div>';            

            if($expense_status == 'Submitted') {
                $data[] = array($i, $batch_details,$row['assessor_name'],$row['grand_total'],$row['advance_amount'],$row['total_amount_due'],date('d-m-Y',strtotime($row['created_dts'])),$action);
            }            
            else {
                if($row['expense_status'] == 'Paid') {
                    $expense_status = '<span class="badge badge-success badge-sm">'.$row['expense_status'].'<span class="ms-1 fa fa-check"></span></span>'; 
                }
                else if($row['expense_status'] == 'Rejected') {
                    $expense_status = '<span class="badge badge-danger badge-sm">'.$row['expense_status'].'<span class="ms-1 fa fa-times"></span></span>';
                }
                $data[] = array($i, $batch_details,$row['assessor_name'],$row['grand_total'],$row['advance_amount'],$row['total_amount_paid'],$row['total_balance_amount'],date('d-m-Y',strtotime($row['paid_date'])),$expense_status,$action); //$row['total_travel_charges'],($row['printing_charges'] + $row['courier_charges']),$row['professional_charges'],
            }
            
        }
        
        /*echo "<pre>";
        print_r($expensesData);
        echo "</pre>";
        exit;*/

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->expenses_model->countAll(),
            "recordsFiltered" => $this->expenses_model->countFiltered($_POST),
            "data" => $data,
        );
        
        // Output to JSON format
        echo json_encode($output);
    }

    public function ViewExpenseDetails() {
        $be_id = $this->input->post('be_id');
        $output = "";
        $totalExpenseData = "";
        
        $arr_expense_details = $this->expenses_model->getExpenseDetails($be_id);
        //echo "<br> str ".$this->db->last_query();exit;
        if($arr_expense_details != false) {
            $grand_total = 0;
            $grand_total_travel_expenses = 0;
            $grand_total_food_stay = 0;
            $other_charges = ($arr_expense_details[0]['printing_charges'] + $arr_expense_details[0]['courier_charges']);
            $professional_charges = ($arr_expense_details[0]['professional_charges'] > 0) ? $arr_expense_details[0]['professional_charges'] : 0;
            $advance_amount = ($arr_expense_details[0]['advance_amount'] > 0) ? $arr_expense_details[0]['advance_amount'] : 0;
            $expense_status = $arr_expense_details[0]['expense_status'];
            $paid_receipt_file = $arr_expense_details[0]['paid_receipt_file'];
            $folderPath = base_url().$this->config->item('assessors_expenses_documents_path');
            $assessor_id = $arr_expense_details[0]['assessor_id'];

            foreach($arr_expense_details as $key => $details) {
                $ted_id = $details['ted_id'];
                $travel_file = $details['travel_file'];
                $breakfast_file = $details['breakfast_file'];
                $lunch_file = $details['lunch_file'];
                $dinner_file = $details['dinner_file'];
                $hotel_stay_file = $details['hotel_stay_file'];

                $travel_file_link = "";
                $breakfast_file_link = "";
                $lunch_file_link = "";
                $dinner_file_link = "";
                $hotel_stay_link = "";

                if($travel_file != "") {
                    $travel_file_link = '<a href="'.$folderPath.$travel_file.'" class="btn btn-primary shadow btn-xs sharp me-1" id="btn-2" target="_blank"><i class="fas fa-file"></i></a>';
                }
                if($breakfast_file != "") {
                    $breakfast_file_link = '<a href="'.$folderPath.$breakfast_file.'" class="btn btn-primary shadow btn-xs sharp me-1" id="btn-2" target="_blank"><i class="fas fa-file"></i></a>';
                }
                if($lunch_file != "") {
                    $lunch_file_link = '<a href="'.$folderPath.$lunch_file.'" class="btn btn-primary shadow btn-xs sharp me-1" id="btn-2" target="_blank"><i class="fas fa-file"></i></a>';
                }
                if($dinner_file != "") {
                    $dinner_file_link = '<a href="'.$folderPath.$dinner_file.'" class="btn btn-primary shadow btn-xs sharp me-1" id="btn-2" target="_blank"><i class="fas fa-file"></i></a>';
                }
                if($hotel_stay_file != "") {
                    $hotel_stay_link = '<a href="'.$folderPath.$hotel_stay_file.'" class="btn btn-primary shadow btn-xs sharp me-1" id="btn-2" target="_blank"><i class="fas fa-file"></i></a>'; 
                }

                $daywise_food_stay_expenses = ($details['breakfast'] + $details['lunch'] + $details['dinner'] + $details['hotel_stay']);
                
                $output .= '<tr>';
                $output.= '<td>'.($key+1).'</td>';
                $output.= '<td>'.date('d-m-Y',strtotime($details['travel_date'])).'</td>'; 
                $output.= '<td>'.$details['mode'].'</td>';
                $output.= '<td>'.$details['travel_from'].'</td>';
                $output.= '<td>'.$details['travel_to'].'</td>';  
                $output.= '<td>INR <input type="number" class="form-control travel" style="width:85px;" id="travel_amount_'.$ted_id.'" name="travel_amount['.$ted_id.']" value="'.$details['travel_amount'].'">'.'&nbsp;'.$travel_file_link.'</td>';
                $output.= '<td>INR <input type="number" class="form-control food_stay_expenses" style="width:85px;" data-ted_id="'.$ted_id.'" id="breakfast_'.$ted_id.'" name="breakfast['.$ted_id.']" value="'.$details['breakfast'].'">'.'&nbsp;'.$breakfast_file_link.'</td>';
                $output.= '<td>INR <input type="number" class="form-control food_stay_expenses" style="width:85px;" data-ted_id="'.$ted_id.'" id="lunch_'.$ted_id.'" name="lunch['.$ted_id.']" value="'.$details['lunch'].'">'.'&nbsp;'.$lunch_file_link.'</td>';
                $output.= '<td>INR <input type="number" class="form-control food_stay_expenses" style="width:85px;" data-ted_id="'.$ted_id.'" id="dinner_'.$ted_id.'" name="dinner['.$ted_id.']" value="'.$details['dinner'].'">'.'&nbsp;'.$dinner_file_link.'</td>';
                $output.= '<td>INR <input type="number" class="form-control food_stay_expenses" style="width:85px;" data-ted_id="'.$ted_id.'" id="hotel_stay_'.$ted_id.'" name="hotel_stay['.$ted_id.']" value="'.$details['hotel_stay'].'">'.'&nbsp;'.$hotel_stay_link.'</td>';
                $output.= '<td id="td_daywise_food_stay_expenses_'.$ted_id.'" data-daywise_food_stay_expenses="'.$daywise_food_stay_expenses.'">INR '.$daywise_food_stay_expenses.'</td>';
                $output .= '</tr>';

                $grand_total_travel_expenses += $details['travel_amount'];
                $grand_total_food_stay += ($details['breakfast'] + $details['lunch'] + $details['dinner'] + $details['hotel_stay']); 
            }
            
            $totalExpenseData = '<table class="table table-clear">
                    <tbody>
                        <tr>
                            <td class="left"><strong class="text-dark">Total Travel Expenses</strong></td>
                            <td class="right" id="td_total_travel_expenses">INR '.$grand_total_travel_expenses.'</td>
                        </tr>
                        <tr>
                            <td class="left"><strong class="text-dark">Total Food & Stay Expenses</strong></td>
                            <td class="right" id="td_total_food_stay_expenses" data-total_food_stay_expenses="'.$grand_total_food_stay.'">INR '.$grand_total_food_stay.'</td>
                        </tr>
                        <tr>
                            <td class="left"><strong class="text-dark">Total Other Expenses</strong><input type="hidden" id="other_expenses" name="other_expenses" value="'.$other_charges.'"></td>
                            <td class="right" id="td_other_expenses" data-other_expenses="'.$other_charges.'">INR '.$other_charges.'</td>
                        </tr>
                        <tr>
                            <td class="left"><strong class="text-dark">Total Professional Charges</strong></td>
                            <td class="right">INR <input type="number" class="form-control" style="width:85px;" id="professional_charges" name="professional_charges" value="'.$professional_charges.'"></td> 
                        </tr>
                        <tr>
                            <td class="left"><strong class="text-dark">Grand Total</strong></td>
                            <td class="right"><strong class="text-dark" id="td_grand_total">INR '.$arr_expense_details[0]['grand_total'].'</strong></td>
                        </tr>
                        <tr>
                            <td class="left"><strong class="text-dark">Advance Paid, If any</strong><input type="hidden" id="advance_amount" name="advance_amount" value="'.$advance_amount.'"></td>
                            <td class="right"><strong class="text-dark" id="td_advance_amount" data-advance_amount="'.$advance_amount.'">INR '.$advance_amount.'</strong></td>
                        </tr>
                        <tr>
                            <td class="left"><strong class="text-dark">Amount to be Paid</strong></td>
                            <td class="right"><strong class="text-dark" id="td_total_amount_due">INR '.$arr_expense_details[0]['total_amount_due'].'</strong></td>
                        </tr>';
                        if($expense_status == 'Paid') {
                            $totalExpenseData .= '<tr>
                                                    <td class="left"><strong class="text-dark">Amount Paid</strong></td>
                                                    <td class="right"><strong class="text-dark">INR '.$arr_expense_details[0]['total_amount_paid'].'</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="left"><strong class="text-dark">Balance Amount</strong></td>
                                                    <td class="right"><strong class="text-dark">INR '.$arr_expense_details[0]['total_balance_amount'].'</strong></td>
                                                </tr>';
                        }
            $totalExpenseData .= '</tbody>
                </table>';        
        }
        
        // Read new token and assing in $data['token']
        $data['expense_details'] = ($arr_expense_details == false) ? "" : $arr_expense_details[0]['batch_id']." Assessment Date ".date('d-m-Y',strtotime($arr_expense_details[0]['tb_assessment_date']));
        $data['output'] = $output;
        $data['totalExpenseData'] = $totalExpenseData;
        $data['assessorComments'] = $arr_expense_details[0]['assessor_comments'];
        $data['be_id'] = $be_id;
        $data['assessor_id'] = $assessor_id;
        $data['batch_id'] = $arr_expense_details[0]['batch_id'];
        $data['total_amount_due'] = $arr_expense_details[0]['total_amount_due'];
        $data['adminComments'] = $arr_expense_details[0]['comments'];
        $data['expenseStatus'] = $expense_status;
        $data['paidReceiptFile'] = ($paid_receipt_file != "") ? $folderPath.$paid_receipt_file : "";
        
        echo json_encode($data);
    }

    public function saveExpenseStatus() {
        $this->require_permission('edit_expenses');

        //echo "<pre>";
        //print_r($_POST);
        //print_r($_FILES);
        //echo "</pre>";
        //exit;  

        $be_id = $this->input->post('be_id');
        $batch_id = $this->input->post('batch_id');
        $total_amount_due = $this->input->post('total_amount_due');
        $assessor_id = $this->input->post('hdn_assessor_id');
        $other_expenses = $this->input->post('other_expenses');
        $professional_charges = $this->input->post('professional_charges');
        $advance_amount = $this->input->post('advance_amount');
        $arr_travel = $this->input->post('travel_amount');
        $arr_breakfast = $this->input->post('breakfast');
        $arr_lunch = $this->input->post('lunch');
        $arr_dinner = $this->input->post('dinner');
        $arr_hotel_stay = $this->input->post('hotel_stay');
        $total_travel_charges = 0;
        $grand_total = 0;
        $output['msg'] = "";

        if(count($arr_travel) > 0) { 
            foreach($arr_travel as $ted_id => $travel_amount) {
                $arrUpdData = array(
                    'travel_amount' => $travel_amount,
                    'breakfast' => $arr_breakfast[$ted_id],
                    'lunch' => $arr_lunch[$ted_id],
                    'dinner' => $arr_dinner[$ted_id],
                    'hotel_stay' => $arr_hotel_stay[$ted_id],
                );
                //echo "<pre>";
                //print_r($arrUpdData);
                //echo "</pre>";
                $this->db->where('be_id', $be_id);
                $this->db->where('ted_id', $ted_id);
                $query = $this->db->update('tbl_batch_expense_details', $arrUpdData);

                $total_travel_charges += ($travel_amount + $arr_breakfast[$ted_id] + $arr_lunch[$ted_id] + $arr_dinner[$ted_id] + $arr_hotel_stay[$ted_id]); 
            }
        }

        $grand_total = $total_travel_charges + $other_expenses + $professional_charges; 
        
        $data = array(
            'professional_charges' => $professional_charges,
            'total_travel_charges' => $total_travel_charges,
            'grand_total' => $grand_total,
            'total_amount_due' => $total_amount_due,
			'comments' => $this->input->post('comments'),
            'total_amount_paid' => $this->input->post('total_amount_paid'),
            'expense_status' => $this->input->post('expense_status'),
            'total_balance_amount' => ($this->input->post('expense_status') == 'Paid') ? ($total_amount_due - $this->input->post('total_amount_paid')) : $total_amount_due,
            'paid_date' => date('Y-m-d H:i:s'),
        );
        /*echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;*/
		
		if (!empty($_FILES)) {
            if (isset($_FILES['paid_receipt_file']) && $_FILES['paid_receipt_file']['name'] != '') {
                $file_ext = pathinfo($_FILES["paid_receipt_file"]["name"], PATHINFO_EXTENSION);
                
                $data['paid_receipt_file'] = uploadImage('paid_receipt_file', 'assessors_expenses', $batch_id . mt_rand(11, 99) . '.'.$file_ext);
            }
        }
        
        if($be_id > 0) { //Insert
            $this->db->where('be_id', $be_id);
            $query = $this->db->update('tbl_batch_expenses', $data);
            
            //$this->session->set_flashdata('msg', 'Data updated successfully'); 
            $output['msg'] = "Data updated successfully";
            
        }
        if($this->input->post('expense_status') == 'Rejected') {
            //Send Rejected Details via Email
            $getMailTemplateDetails = get_email_template_details(3);
            foreach($getMailTemplateDetails as $details) {
                $subject = $details['email_subject'];
                $email_content = $details['email_content'];

                //Get assessor details
                $condition = "assessor_id = ".$assessor_id;
                $assessorDetails = $this->Mdmaster->getAllRecords('tbl_assessor',$condition,'assessor_name','ASC');
                if($assessorDetails != false) {
                    $assessor_name = $assessorDetails[0]['assessor_name'];
                    $assessor_email = $assessorDetails[0]['assessor_email'];

                    $message = str_replace(array('$assessor_name','$comments'),array($assessor_name,$this->input->post('comments')),$email_content);

                    send_email($subject,$message,$assessor_name,$assessor_email); 
                } 
            }       
        }
        
        echo json_encode($output);
    }

}