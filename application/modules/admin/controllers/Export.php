<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Export extends MY_Controller
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
        $this->load->model('Assessors_model');
        $this->load->model('batch_model');
        $this->load->model('Mdmaster');
        $this->load->model('expenses_model');
        
        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			Blackirect('admin-login');	
		}
    }

    public function export_assessors()
    {
        $_POST['length'] = '-1';
        $arrAssessorData = $this->Assessors_model->getRows($_POST);
        //echo "<br> str ".$this->db->last_query();
        /*echo "<pre>";
        print_r($arrAssessorData);
        echo "</pre>";*/
        //exit;

        $arrAssociatedAgencies = array();
        //Get assessor associated agencies
        $arr_assessor_associated_agencies_data = $this->Mdmaster->getAllRecords('tbl_assessor_associated_agencies');
        if($arr_assessor_associated_agencies_data != false) {
            foreach($arr_assessor_associated_agencies_data as $details) {
                if(array_key_exists($details['assessor_id'],$arrAssociatedAgencies)) {
                    $arrAssociatedAgencies[$details['assessor_id']] .= ','.$details['agency_name'];
                }
                else {
                    $arrAssociatedAgencies[$details['assessor_id']] = $details['agency_name'];
                }
            }
        }

        $arrMapSsc = array();
        //Get mapped ssc
        $arr_mapped_ssc_data = $this->Assessors_model->getAssessorSectorSkillCouncil();
        if($arr_mapped_ssc_data != false) {
            foreach($arr_mapped_ssc_data as $details) {
                if($details['ssc_title'] != "") {
                    if(array_key_exists($details['assessor_id'],$arrMapSsc)) {
                        $arrMapSsc[$details['assessor_id']] .= ','.$details['ssc_title']; 
                    }
                    else {
                        $arrMapSsc[$details['assessor_id']] = $details['ssc_title'];
                    }
                }
            }
        }

        /*echo "<pre>";
        print_r($arrMapSsc);
        echo "</pre>";
        exit;*/

        if(count($arrAssessorData) > 0)
		{
			// Create a new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set column headers
            $columns = ['S.No', 'Assessor Code', 'Assessor Name', 'Gender', 'Mobile', 'Email', 'Photo', 'Address', 'City', 'PinCode', 'Resume', 'Aadhar Number', 'Aadhar Front Copy', 
                                'Aadhar Back Copy', 'PAN Number', 'PAN Copy', 'State', 'District', 'Associated Sectors', 'Associated Agencies', 'Status'];
            $columnLetter = 'A';
            
            // Set bold font for headers
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
            ];

            foreach ($columns as $column) {
                $sheet->setCellValue($columnLetter . '1', $column);
                $sheet->getStyle($columnLetter . '1')->applyFromArray($headerStyle);
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                $columnLetter++;
            }

            // Set data to the spreadsheet
            $rowNumber = 2; // Start from the second row
            foreach ($arrAssessorData as $index => $data) {
                $mapSsc = (array_key_exists($data['assessor_id'],$arrMapSsc)) ? $arrMapSsc[$data['assessor_id']] : "";
                $mapAssociatedAgencies = (array_key_exists($data['assessor_id'],$arrAssociatedAgencies)) ? $arrAssociatedAgencies[$data['assessor_id']] : "";
                
                $sheet->setCellValue('A' . $rowNumber, $index + 1); // S.No
                $sheet->setCellValue('B' . $rowNumber, $data['assessor_code']);
                $sheet->setCellValue('C' . $rowNumber, $data['assessor_name']);
                $sheet->setCellValue('D' . $rowNumber, $data['assessor_gender']);
                $sheet->setCellValue('E' . $rowNumber, $data['assessor_mobile']);
                $sheet->setCellValue('F' . $rowNumber, $data['assessor_email']);
                $sheet->setCellValue('G' . $rowNumber, ($data['assessor_photo'] != "") ? base_url().$this->config->item('assessors_images_path').$data['assessor_photo'] : "");
                $sheet->setCellValue('H' . $rowNumber, $data['address']);
                $sheet->setCellValue('I' . $rowNumber, $data['city']);
                $sheet->setCellValue('J' . $rowNumber, $data['pincode']);
                $sheet->setCellValue('K' . $rowNumber, ($data['assessor_resume'] != "") ? base_url().$this->config->item('assessors_resume_path').$data['assessor_resume'] : "");
                $sheet->setCellValue('L' . $rowNumber, $data['aadhar_number']);
                $sheet->setCellValue('M' . $rowNumber, ($data['aadhar_front_filename'] != "") ? base_url().$this->config->item('assessors_aadhaar_path').$data['aadhar_front_filename'] : "");
                $sheet->setCellValue('N' . $rowNumber, ($data['aadhar_back_filename'] != "") ? base_url().$this->config->item('assessors_aadhaar_path').$data['aadhar_back_filename'] : "");
                $sheet->setCellValue('O' . $rowNumber, $data['pan_no']);
                $sheet->setCellValue('P' . $rowNumber, ($data['pan_filename'] != "") ? base_url().$this->config->item('assessors_pan_path').$data['pan_filename'] : "");
                $sheet->setCellValue('Q' . $rowNumber, $data['state_name']);
                $sheet->setCellValue('R' . $rowNumber, $data['dist_name']); 
                $sheet->setCellValue('S' . $rowNumber, $mapSsc);
                $sheet->setCellValue('T' . $rowNumber, $mapAssociatedAgencies);
                $sheet->setCellValue('U' . $rowNumber, $data['assessor_status']);
                
                $rowNumber++;
            }

            $fileName = "Assessors_Master_Report_".date('d-m-Y-His').".xlsx";

			$writer = new Xlsx($spreadsheet);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
			$writer->save('php://output');

        }
    }

    public function export_batches($ssc_id = 0,$trade_id = 0,$assessor_id = 0,$start_date = NULL,$end_date = NULL,$type = NULL)
    {
        $_POST['length'] = '-1';
        if($ssc_id > 0) {
            $_POST['ssc_id'] = $ssc_id;
        }
        if($trade_id > 0) {
            $_POST['trade_id'] = $trade_id;
        }
        if($assessor_id > 0) {
            $_POST['assessor_id'] = $assessor_id;
        }
        if($start_date != 'NULL') {
            $_POST['start_date'] = $start_date;
        }
        if($end_date != 'NULL') {
            $_POST['end_date'] = $end_date;
        }
        if($type != NULL) {
            $_POST['type'] = $type;      
        }

        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $batchData = $this->batch_model->getRows($_POST);
        //echo "<br> str ".$this->db->last_query();
        
        if(count($batchData) > 0)
		{
			// Create a new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set column headers
            $columns = [
                'S.No', 'Batch ID', 'Scheme', 'Sub-Scheme', 'Trade/QP Code', 'Trade/QP Name', 'Assigned Assessor', 'Regional Language', 
                'Assessment Type', 'Sector Skill Council Name', 'TP Code', 'Training Partner', 'Training Center', 'TC Code', 
                'Assessment Status', 'Assessment Completed Datetime', 'Result Processing','Assessment Date','No. of Candidates'
            ];
            $columnLetter = 'A';

            // Set bold font for headers
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
            ];

            foreach ($columns as $column) {
                $sheet->setCellValue($columnLetter . '1', $column);
                $sheet->getStyle($columnLetter . '1')->applyFromArray($headerStyle);
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                $columnLetter++;
            }

            // Add data to cells
            $rowNumber = 2; // Start from the second row
            foreach ($batchData as $index => $data) {
                $sheet->setCellValue('A' . $rowNumber, $index + 1); // S.No
                $sheet->setCellValue('B' . $rowNumber, $data['batch_id']);
                $sheet->setCellValue('C' . $rowNumber, $data['scheme_name']);
                $sheet->setCellValue('D' . $rowNumber, $data['subscheme_name']);
                $sheet->setCellValue('E' . $rowNumber, $data['trade_code']);
                $sheet->setCellValue('F' . $rowNumber, $data['trade_name']);
                $sheet->setCellValue('G' . $rowNumber, $data['assessor_name']);
                $sheet->setCellValue('H' . $rowNumber, $data['language_name']);
                $sheet->setCellValue('I' . $rowNumber, $data['tb_exam_type']);
                $sheet->setCellValue('J' . $rowNumber, $data['ssc_title']);
                $sheet->setCellValue('K' . $rowNumber, $data['tp_code']);
                $sheet->setCellValue('L' . $rowNumber, $data['tp_name']);
                $sheet->setCellValue('M' . $rowNumber, $data['tc_name']);
                $sheet->setCellValue('N' . $rowNumber, $data['tc_code']);
                $sheet->setCellValue('O' . $rowNumber, $data['tb_assessment_status']);
                $sheet->setCellValue('P' . $rowNumber, ($data['tb_assessment_completion_date_time'] != "") ? date('d-m-Y H:i:s',strtotime($data['tb_assessment_completion_date_time'])) : "");
                $sheet->setCellValue('Q' . $rowNumber, $data['result_processing']);
                $sheet->setCellValue('R' . $rowNumber, date('d-m-Y',strtotime($data['tb_assessment_date'])));
                $sheet->setCellValue('S' . $rowNumber, $data['tb_target']);
                $rowNumber++;
            }

            if($type == 'Completed') {
                $fileName = "Batches_Completed_Report_".date('d-m-Y-His').".xlsx";
            }
            else {
                $fileName = "Batches_Inprocess_Report_".date('d-m-Y-His').".xlsx";
            }    

			$writer = new Xlsx($spreadsheet);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"'); 
			$writer->save('php://output');
		}
    }

    public function export_expense_report($assessor_id = 0,$start_date = NULL,$end_date = NULL,$expense_status = NULL)
    {
        if($assessor_id > 0) {
            $_POST['assessor_id'] = $assessor_id;
        }
        if($start_date != 'NULL') {
            $_POST['start_date'] = $start_date;
        }
        if($end_date != 'NULL') {
            $_POST['end_date'] = $end_date;
        }
        if($expense_status != NULL) {
            $_POST['expense_status'] = $expense_status;      
        }

        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $expensesData = $this->expenses_model->getAssessorsExpensesReport($_POST);
        //echo "<br> str ".$this->db->last_query();exit;

        $arrAssessorBatchData = array();
        $arrBatchTotalTravelData = array();
        $arrBatchTotalFoodStayData = array();
        $arrBatchTotalOtherExpenses = array();
        $arrBatchTotalProfessionalCharges = array();
        $arrBatchTotalOtherExpenses = array();
        $arrBatchGrandTotal = array();
        $arrBatchAdvancePaid = array();
        $arrAssessorDetails = array();
        $arrAssessorBatchDetails = array();
        $arrAssessorTotalData = array();
        $arrBatchExpenseData = array();
        $arrBatchExpenseDetailsData = array();
        
        if(count($expensesData) > 0)
		{
			foreach($expensesData as $row) {
                $assessor_id = $row['assessor_id'];
                $assessor_name = $row['assessor_name'];
                $tb_id = $row['tb_id'];
                $be_id = $row['be_id'];
                $ted_id = $row['ted_id'];
                $batch_id = $row['batch_id'];
                $tb_assessment_date = date('d-m-Y',strtotime($row['tb_assessment_date']));
                $travel_date = date('d-m-Y',strtotime($row['travel_date']));
                $mode = $row['mode'];
                $travel_from = $row['travel_from'];
                $travel_to = $row['travel_to'];
                $travel_amount = $row['travel_amount'];
                $breakfast = $row['breakfast'];
                $lunch = $row['lunch'];
                $dinner = $row['dinner'];
                $hotel_stay = $row['hotel_stay'];
                $food_stay_amount = ($row['breakfast'] + $row['lunch'] + $row['dinner'] + $row['hotel_stay']);
                $printing_charges = $row['printing_charges'];
                $courier_charges = $row['courier_charges'];
                $other_expenses = ($printing_charges + $courier_charges);
                $professional_charges = $row['professional_charges'];
                $grand_total = ($travel_amount + $food_stay_amount + $other_expenses);  
                $advance_amount = $row['advance_amount'];

                $arrAssessorDetails[$assessor_id] = $assessor_name;
                $arrAssessorBatchDetails[$assessor_id][$tb_id] = $tb_id;

                //Assessor Data
                if(array_key_exists($assessor_id,$arrAssessorTotalData)) {
                    $arrAssessorTotalData[$assessor_id]['travel'] += $travel_amount;
                    $arrAssessorTotalData[$assessor_id]['food_stay'] += $food_stay_amount;
                }
                else {
                    $arrAssessorTotalData[$assessor_id]['travel'] = $travel_amount;
                    $arrAssessorTotalData[$assessor_id]['food_stay'] = $food_stay_amount;
                    $arrAssessorTotalData[$assessor_id]['other_expenses'] = 0;
                    $arrAssessorTotalData[$assessor_id]['professional_charges'] = 0;
                    $arrAssessorTotalData[$assessor_id]['grand_total'] = 0;
                    $arrAssessorTotalData[$assessor_id]['advance_amount'] = 0;
                }

                //Batch Expenses
                if(!array_key_exists($tb_id,$arrBatchExpenseData)) {
                    $arrBatchExpenseData[$tb_id]['batch_id'] = $batch_id;
                    $arrBatchExpenseData[$tb_id]['tb_assessment_date'] = $tb_assessment_date;
                    $arrBatchExpenseData[$tb_id]['printing_charges'] = $printing_charges;
                    $arrBatchExpenseData[$tb_id]['courier_charges'] = $courier_charges;
                    $arrBatchExpenseData[$tb_id]['professional_charges'] = $professional_charges;

                    $arrAssessorTotalData[$assessor_id]['other_expenses'] += $other_expenses;
                    $arrAssessorTotalData[$assessor_id]['professional_charges'] += $professional_charges;
                    $arrAssessorTotalData[$assessor_id]['grand_total'] += $grand_total;
                    $arrAssessorTotalData[$assessor_id]['advance_amount'] += $advance_amount;
                }

                //Expense Details
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['travel_date'] = $travel_date;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['mode'] = $mode;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['travel_from'] = $travel_from;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['travel_to'] = $travel_to;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['travel_amount'] = $travel_amount;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['breakfast'] = $breakfast;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['lunch'] = $lunch;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['dinner'] = $dinner;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['hotel_stay'] = $hotel_stay;
                $arrBatchExpenseDetailsData[$tb_id][$ted_id]['food_stay_amount'] = $food_stay_amount;
            }

            //echo "<pre>";
            //print_r($arrAssessorBatchDetails);
            //print_r($arrAssessorDetails);
            //print_r($arrBatchExpenseData);
            //print_r($arrBatchExpenseDetailsData);
            //print_r($arrAssessorTotalData); 
            //echo "</pre>";
            //exit;

            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();

            // Set bold font for headers
            $headerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];

            $headerStyleBorderThick = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, 
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,  // Thickest pBlackefined border
                        'color' => ['argb' => '000000'], // Black color
                    ],
                ],
            ];

            $headerStyleBorderThickRight = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, 
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,  // Medium thickness, close to 2px
                        'color' => ['argb' => '000000'], // Black color
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'], // Black color
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => '000000'], // Black color
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THICK,  // Thickest pBlackefined border
                        'color' => ['argb' => '000000'], // Black color
                    ],
                ],
            ];

            $headerStyleBorderThickBottom = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, 
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,  // Medium thickness, close to 2px
                        'color' => ['argb' => '000000'], // Black color
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => '000000'], // Black color
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => '000000'], // Black color
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THICK,  // Thickest pBlackefined border 
                        'color' => ['argb' => '000000'], // Black color
                    ],
                ],
            ];


            $headerStyle1 = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];

            // Loop through each assessor and create a sheet
            foreach ($arrAssessorDetails as $assessor_id => $assessor_name) { 
                $assessorGrandTotal = ($arrAssessorTotalData[$assessor_id]['travel'] + $arrAssessorTotalData[$assessor_id]['food_stay'] + $arrAssessorTotalData[$assessor_id]['other_expenses'] + $arrAssessorTotalData[$assessor_id]['professional_charges']);
                $sheet = $spreadsheet->createSheet();
                $sheetTitle = $assessor_name;
                // Truncate the title to 31 characters
                if (strlen($sheetTitle) > 31) {
                    $sheetTitle = substr($sheetTitle, 0, 31); 
                }
                $sheet->setTitle($sheetTitle);
    
                // Add assessor details
                $sheet->setCellValue('B2', 'Assessor Name');
                $sheet->setCellValue('C2', $assessor_name);
    
                $sheet->setCellValue('B3', 'Billing Period');
                $sheet->setCellValue('C3', 'From');
                $sheet->setCellValue('D3', 'To');
                $sheet->setCellValue('C4', ($start_date != 'NULL') ? date('d-m-Y',strtotime($start_date)) : "");  
                $sheet->setCellValue('D4', ($end_date != 'NULL') ? date('d-m-Y',strtotime($end_date)) : "");  
    
                $sheet->setCellValue('B5', 'Total Travel Expenses');
                $sheet->setCellValue('C5', $arrAssessorTotalData[$assessor_id]['travel']); 
                
                $sheet->setCellValue('B6', 'Total Food & Stay Expenses');
                $sheet->setCellValue('C6', $arrAssessorTotalData[$assessor_id]['food_stay']);
                
                $sheet->setCellValue('B7', 'Total Other Expenses');
                $sheet->setCellValue('C7', $arrAssessorTotalData[$assessor_id]['other_expenses']);
                
                $sheet->setCellValue('B8', 'Total Professional Charges');
                $sheet->setCellValue('C8', $arrAssessorTotalData[$assessor_id]['professional_charges']);
                
                $sheet->setCellValue('B9', 'Grand Total');
                $sheet->setCellValue('C9', $assessorGrandTotal);
                
                $sheet->setCellValue('B10', 'Advance Paid, If any');
                $sheet->setCellValue('V10', $arrAssessorTotalData[$assessor_id]['advance_amount']);
                
                $sheet->setCellValue('B11', 'To pay Assessor');
                $sheet->setCellValue('C11', ($assessorGrandTotal - $arrAssessorTotalData[$assessor_id]['advance_amount']));

                $sheet->mergeCells('B3:B4');
                $sheet->mergeCells('C2:D2');
                $sheet->mergeCells('C5:D5');
                $sheet->mergeCells('C6:D6');
                $sheet->mergeCells('C7:D7');
                $sheet->mergeCells('C8:D8');
                $sheet->mergeCells('C9:D9');
                $sheet->mergeCells('C10:D10');
                $sheet->mergeCells('C11:D11');

                // Make headers bold
                $sheet->getStyle('B2:B11')->applyFromArray($headerStyle);
                $sheet->getStyle('C2:D11')->applyFromArray($headerStyle1);

                // Apply blue background color to cells B3:C3
                $spreadsheet->getActiveSheet()->getStyle('C3:D3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => '9ccbe6'], // Blue color in ARGB format  
                    ],
                ]);
                $spreadsheet->getActiveSheet()->getStyle('B11:C11')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'e2f024'], // Blue color in ARGB format  
                    ],
                ]);
    
                // Adjust column width
                foreach (range('B', 'D') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Add merged headers
                $sheet->setCellValue('A14', 'Assessment Date');
                $sheet->mergeCells('A14:A15');
                $spreadsheet->getActiveSheet()->getStyle('A14:A15')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => '97c7eb'], // color in ARGB format  
                    ],
                ]);

                $sheet->setCellValue('B14', 'Batch ID');
                $sheet->mergeCells('B14:B15');
                $spreadsheet->getActiveSheet()->getStyle('B14:B15')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'c7c7c3'], // color in ARGB format  
                    ],
                ]);

                $sheet->setCellValue('C14', 'Travel Expenses');
                $sheet->mergeCells('C14:G14');
               
                $sheet->setCellValue('H14', 'Food & Stay Expenses');
                $sheet->mergeCells('H14:L14');
                
                $sheet->setCellValue('M14', 'Other Expenses');
                $sheet->mergeCells('M14:O14');
                
                $sheet->setCellValue('P14', 'Professional Charges');
                
                // Add sub-headers
                $sheet->setCellValue('C15', 'Travel Date');
                $sheet->setCellValue('D15', 'Mode');
                $sheet->setCellValue('E15', 'From');
                $sheet->setCellValue('F15', 'To');
                $sheet->setCellValue('G15', 'Amount');
                $sheet->setCellValue('H15', 'Breakfast');
                $sheet->setCellValue('I15', 'Lunch');
                $sheet->setCellValue('J15', 'Dinner');
                $sheet->setCellValue('K15', 'Hotel Stay');
                $sheet->setCellValue('L15', 'Amount');
                $sheet->setCellValue('M15', 'Printing');
                $sheet->setCellValue('N15', 'Courier');
                $sheet->setCellValue('O15', 'Amount');
                $sheet->setCellValue('P15', 'Amount');

                $spreadsheet->getActiveSheet()->getStyle('C14:G15')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFCC99'], // color in ARGB format  
                    ],
                ]);
                $spreadsheet->getActiveSheet()->getStyle('H14:L15')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'C0C0C0'], // color in ARGB format  
                    ],
                ]);
                $spreadsheet->getActiveSheet()->getStyle('M14:O15')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => '99FFCC'], // color in ARGB format  
                    ],
                ]); 
                $spreadsheet->getActiveSheet()->getStyle('P14:P15')->applyFromArray([ 
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => '99FFCC'], // color in ARGB format  
                    ],
                ]);

                // Adjust column width
                foreach (range('A', 'P') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true); 
                }

                $sheet->getStyle('A14:P15')->applyFromArray($headerStyleBorderThick);

                // Add data to the table
                $rowNumber = 16; 
                $rowDetailNumber = 16; 

                //echo "<br> rowNumber ".$rowNumber;
                foreach($arrAssessorBatchDetails[$assessor_id] as $tb_id) {
                    //echo "<br><br> Assessor Id ".$assessor_id." tb id ".$tb_id." Batch Id ".$arrBatchExpenseData[$tb_id]['batch_id']." rowNumber ".$rowNumber;
                    
                    $sheet->setCellValue('A' . $rowNumber, $arrBatchExpenseData[$tb_id]['tb_assessment_date']);
                    $sheet->setCellValue('B' . $rowNumber, $arrBatchExpenseData[$tb_id]['batch_id']);
                    $sheet->getStyle('A'.$rowNumber.':P'.$rowNumber)->applyFromArray($headerStyle1);
                    
                    $totalExpenseRows = count($arrBatchExpenseDetailsData[$tb_id]);
                    $count = 0;

                    //echo "<br> totalExpenseRows ".$totalExpenseRows." count ".$count;
                
                    foreach ($arrBatchExpenseDetailsData[$tb_id] as $ted_id => $data) {
                        //echo "<br> ted_id ".$ted_id." rowDetailNumber ".$rowDetailNumber;
                        /*echo "<pre>";
                        print_r($data);
                        echo "</pre>";*/
                        $sheet->setCellValue('C' . $rowDetailNumber, $data['travel_date']);
                        $sheet->setCellValue('D' . $rowDetailNumber, $data['mode']);
                        $sheet->setCellValue('E' . $rowDetailNumber, $data['travel_from']);
                        $sheet->setCellValue('F' . $rowDetailNumber, $data['travel_to']);
                        $sheet->setCellValue('G' . $rowDetailNumber, $data['travel_amount']);
                        $sheet->setCellValue('H' . $rowDetailNumber, $data['breakfast']);
                        $sheet->setCellValue('I' . $rowDetailNumber, $data['lunch']);
                        $sheet->setCellValue('J' . $rowDetailNumber, $data['dinner']);
                        $sheet->setCellValue('K' . $rowDetailNumber, $data['hotel_stay']);
                        $sheet->setCellValue('L' . $rowDetailNumber, $data['food_stay_amount']);     

                        $count++;

                        //echo "<br> count ".$count;

                        if($count == $totalExpenseRows) {
                            $rowDetailNumber = ($rowDetailNumber + 2);  
                        }
                        else {
                            $rowDetailNumber++;
                        }
                        //echo "<br> rowDetailNumber ".$rowDetailNumber;
                    }    
                    $sheet->setCellValue('M' . $rowNumber, $arrBatchExpenseData[$tb_id]['printing_charges']);
                    $sheet->setCellValue('N' . $rowNumber, $arrBatchExpenseData[$tb_id]['courier_charges']);
                    $sheet->setCellValue('O' . $rowNumber, ($arrBatchExpenseData[$tb_id]['printing_charges'] + $arrBatchExpenseData[$tb_id]['courier_charges']));
                    $sheet->setCellValue('P' . $rowNumber, $arrBatchExpenseData[$tb_id]['professional_charges']); 

                    $sheet->mergeCells('A'.$rowNumber.':A'.($rowDetailNumber-1));
                    $sheet->mergeCells('B'.$rowNumber.':B'.($rowDetailNumber-1));
                    $sheet->mergeCells('M'.$rowNumber.':M'.($rowDetailNumber-1));
                    $sheet->mergeCells('N'.$rowNumber.':N'.($rowDetailNumber-1));
                    $sheet->mergeCells('O'.$rowNumber.':O'.($rowDetailNumber-1));
                    $sheet->mergeCells('P'.$rowNumber.':P'.($rowDetailNumber-1));
                    //echo "<br> out rowNumber ".$rowNumber." rowDetailNumber ".$rowDetailNumber.' A'.$rowNumber.':O'.$rowDetailNumber;

                    $sheet->getStyle('A'.$rowNumber.':P'.($rowDetailNumber-1))->applyFromArray($headerStyle1); 

                    $sheet->getStyle('A'.$rowNumber.':A'.($rowDetailNumber-1))->applyFromArray($headerStyleBorderThickRight);
                    $sheet->getStyle('B'.$rowNumber.':B'.($rowDetailNumber-1))->applyFromArray($headerStyleBorderThickRight); 
                    //$sheet->getStyle('C'.$rowNumber.':F'.($rowDetailNumber-1))->applyFromArray($headerStyle1); 
                    $sheet->getStyle('C'.$rowNumber.':G'.($rowDetailNumber-1))->applyFromArray($headerStyleBorderThickRight);  
                    $sheet->getStyle('H'.$rowNumber.':L'.($rowDetailNumber-1))->applyFromArray($headerStyleBorderThickRight);
                    $sheet->getStyle('M'.$rowNumber.':O'.($rowDetailNumber-1))->applyFromArray($headerStyleBorderThickRight);  
                    $sheet->getStyle('P'.$rowNumber.':P'.($rowDetailNumber-1))->applyFromArray($headerStyleBorderThickRight);  

                    $rowNumber = $rowDetailNumber;   
                }

                $sheet->getStyle('A'.($rowNumber-1).':P'.($rowNumber-1))->applyFromArray($headerStyleBorderThickBottom);

                // Make headers bold
                $sheet->getStyle('C2:D11')->applyFromArray($headerStyle1);
    
                // Adjust column width
                foreach (range('B', 'D') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true); 
                }
            }

            //exit;
            // Remove the default sheet created with the spreadsheet
            $spreadsheet->removeSheetByIndex(0);
            // Set the first sheet as the active sheet
            $spreadsheet->setActiveSheetIndex(0);

    
            // Write the spreadsheet to a file
            $writer = new Xlsx($spreadsheet);
            if($start_date != 'NULL' && $end_date != 'NULL') {
                $start_date = date('d-m-Y',strtotime($start_date));
                $end_date = date('d-m-Y',strtotime($end_date));
                if($assessor_id > 0) {
                    $assessorName = $arrAssessorDetails[$assessor_id];
                    $filename = 'Assessor_Expense_Report_for_'.$assessorName.'_'.$start_date.'_to_'.$end_date.'.xlsx';
                }
                else {
                    $filename = 'Assessor_Expense_Report_'.$start_date.'_to_'.$end_date.'.xlsx';
                }
            }
            else if($assessor_id > 0) {
                $assessorName = $arrAssessorDetails[$assessor_id];
                $filename = 'Assessor_Expense_Report_for_'.$assessorName.'.xlsx'; 
            }
            else  {
                $filename = 'Assessor_Expense_Report.xlsx';
            }
            
            // Blackirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'. $filename .'"');
            header('Cache-Control: max-age=0');
    
            $writer->save('php://output');
		}
    }
}
