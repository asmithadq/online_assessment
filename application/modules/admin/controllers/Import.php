<?php defined('BASEPATH') or exit('No direct script access allowed');

require_once ('vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Import extends MY_Controller
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
        $this->load->model('Import_model');
        $this->load->model('Mdmaster');
        $this->load->model('Trades_model');
        
        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			redirect('admin-login');	
		}
    }

    
	
	public function importQuestions()
    {
        $data['title'] = 'Import Questions';  // Set the title here

        //Get Languages
        $condition = "status = 1";
        $data['arr_languages'] = $this->Mdmaster->getAllRecords('tbl_languages',$condition,'language_name','ASC');
     	
        $this->render_page('admin/import_questions',$data);
    }
	
	
	public function importQuestionsSave()
    {		
		$message = "";
		$type = "success";
        $arr_lids = $this->input->post('lid');
        
		$allowedFileType = [
			'application/vnd.ms-excel',
			'text/xls',
			'text/xlsx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		];

        $arrMandatoryColumnsHeaders = array('qn_no','question_type','question','option_a','option_b','option_c','option_d','correct_ans','marks');
        $arrLangHeaders = array();
        $arrLangName = array();

        if(count($arr_lids) > 0) { 
            foreach($arr_lids as $lidData) {
                list($lid,$lang_name) = explode("_",$lidData);
                $arrLangName[$lid] = $lang_name;
                
                for($col = 2; $col <= 6; $col++) {
                    $langCol = $arrMandatoryColumnsHeaders[$col].'_'.strtolower($lang_name);
                    array_push($arrMandatoryColumnsHeaders,$langCol);
                    $arrLangHeaders[$lid][$langCol] = $langCol;
                }
            }
        }

        //print "<pre>";
        //print_r($arr_lids);
        //print_r($arrMandatoryColumnsHeaders);
        //print_r($arrLangHeaders);
        //print "</pre>";
        //exit;

        $arrExcelColumnsHeaders = array();
        $missingColumnsHeaders = array();
        $missingQuestionType = array();

		if (in_array($_FILES["file"]["type"], $allowedFileType)) 
		{
			//$targetPath = 'uploads/students/' . $_FILES['file']['name'];
			
			$file_name = "Questions_". time() . '_' . str_replace(" ", "", $_FILES['file']['name']);
					
			$targetPath = 'uploads/questions/' . $file_name;
			
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

			$reader = IOFactory::createReaderForFile($targetPath);
            $spreadsheet = $reader->load($targetPath);
            
            $arrNosCodes = array();
            $arrNosIds = array();
            $arrNosCodeById = array();
            $arrImportedNosCodes = array();
            $arrNosIdMarks = array();
            $arrTradeNosWiseMarks = array();
            $totalSkipped = 0;
            $totalImported = 0;
            
            //Get the Nos for the Trade
            $trade_id = 1;
            
            $arrTradeDetails = $this->Trades_model->getTradeDetails($trade_id);
            if($arrTradeDetails != false) {
                foreach($arrTradeDetails as $tradeData) {
                    $nosCode = strtolower(str_replace(array('/N','/'),'',$tradeData['nos_code']));
                    $nosId = $tradeData['nos_id'];
                    $arrNosCodes[$nosCode] = $tradeData['nos_code']; 
                    $arrNosIds[$nosCode] = $nosId;
                    $arrNosCodeById[$nosId] = $nosCode;
                    $arrTradeNosWiseMarks[$nosId]['Theory'] = $tradeData['theory_marks'];
                    $arrTradeNosWiseMarks[$nosId]['PracticalSkill'] = $tradeData['practical_skill_marks'];
                    $arrTradeNosWiseMarks[$nosId]['PracticalActivity'] = $tradeData['practical_marks'];
                    $arrTradeNosWiseMarks[$nosId]['Viva'] = $tradeData['viva_marks'];
                }
            }
            
            /*print "<pre>";
		    print_r($arrNosCodes);
            print_r($arrNosIds);
		    print "</pre>";
            exit;*/

            $insertExcelData = array();
            $insertExcelNosIds = array();
            $arrQns = array();
            $totalQns = 0;
            
            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestDataRow();
                $highestColumn = $sheet->getHighestDataColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                
                $sheetLowerName = strtolower($sheetName);
                
                /*echo "<br><br> sheet name ".$sheetLowerName;
                echo "<br> highestRow ".$highestRow;
                echo "<br> highestColumn ".$highestColumn;
                echo "<br> highestColumnIndex ".$highestColumnIndex;*/

                if(array_key_exists($sheetLowerName,$arrNosCodes)) {
                    unset($arrNosCodes[$sheetLowerName]);
                    //unset($arrNosCodesActual[$sheetLowerName]);
                }
                else {
                    $arrImportedNosCodes[$sheetName] = $sheetName;
                }

                // Extract column headers (assuming they are in the first row) :: Read the header and validate if the headers are right
                $headers = [];
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    $header = trim($sheet->getCellByColumnAndRow($col, 1)->getValue());
                    $arrExcelColumnsHeaders[$sheetName][$header] = $header;
                }

                foreach($arrMandatoryColumnsHeaders as $columns) {
                    if(!array_key_exists($columns,$arrExcelColumnsHeaders[$sheetName])) {
                        $missingColumnsHeaders[$sheetName][] = $columns;
                    }
                }

                //Read the data cells
                $excelData = array();

                for ($row = 2; $row <= $highestRow; ++$row) {
                    // Check if all cells in the row are empty
                    $isEmptyRow = true;
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                        if (!empty($cellValue)) {
                            $isEmptyRow = false;
                            break;
                        }
                    }

                    if ($isEmptyRow) {
                        // Skip the row if it's entirely blank
                        continue;
                    }
                    
                    $sheetNosId = (array_key_exists($sheetLowerName,$arrNosIds)) ? $arrNosIds[$sheetLowerName] : 0;
                    //echo "<br><br> sheetNosId ".$sheetNosId;

                    $rowData = array();
                    
                    if($sheetNosId > 0) {
                        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                            $header = trim($sheet->getCellByColumnAndRow($col, 1)->getValue());
                            //echo "<br><br> header ".$header;
                            $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                            $rowData[$header] = $cellValue;
                            if($header == 'question') {
                                $arrQns[$cellValue] = $cellValue;
                            }
                        }
                        $excelData[$sheetNosId][] = $rowData;
                        $insertExcelNosIds[$sheetNosId] = $sheetNosId; //Capture the NosIds from the sheet to validate
                        $totalQns++;
                    }
                    
                }
                
                /*print "<pre>";
                print_r($excelData);		
                print "</pre>";*/

                array_push($insertExcelData,$excelData);

                /*print "<pre>";
                print_r($insertExcelData);		
                print "</pre>";*/
            }
            //exit;
            //print "<pre>";
            //print_r($arrMandatoryColumnsHeaders);		
            //print_r($arrExcelColumnsHeaders);	
            //print_r($missingColumnsHeaders);		
            //print "</pre>";

            //print "<pre>";
            //print_r($insertExcelData);	
            //print_r($arrNosIdMarks);			
            //print "</pre>";
            //exit;
		} else {
			$type = "error";
			$message = "Invalid File Type. Upload Excel File.";
		}

        if(count($missingColumnsHeaders) > 0) {
		    $type = "error";
            foreach($missingColumnsHeaders as $sheetName => $arrColumnNames) {
                $message .= "<br>Error in Sheet ".$sheetName;
                $message .= "<br>Missing Column Or Incorrect Column Name for Column(s): ".implode(",",$arrColumnNames);
            }
		}
		if(count($arrNosCodes) > 0) {
		    $type = "error";
			$message .= "<br>NOS Sheets are missing for NOS Codes- ".implode(",",$arrNosCodes);
		}
		if(count($arrImportedNosCodes) > 0) {
		    $type = "error";
			$message .= "<br>Additional NOS Sheets are existing - ".implode(",",$arrImportedNosCodes);
		}
        
        unlink($targetPath);

        if($type == "success") { 
            if(count($insertExcelNosIds) != count($arrNosIds)) {  //If Excel Sheet Nos doesnt match with the Mapped Nos for the trade
                $type = "error";
			    $message .= "<br>Nos Mismatch with the Trade mapped Nos";
            }
            else { //Import Questions
                //print "<pre>";
                //print_r($arrNosIds);
                //print_r($insertExcelNosIds);
                //print_r($insertExcelData);	
                //print_r($arrQns);	
                //print "</pre>";
                //exit;

                $arrQuestionTypeColFormat = array('theory' => 'Theory','practicalskill' => 'PracticalSkill','practicalactivity' => 'PracticalActivity','viva' => 'Viva');
                $arrAnsTypeColFormat = array('a','b','c','d');

                $arrExistingQuestions = array();
                $arrBatchInsert = array();
                $arrBatchLangInsert = array();
                $arrBatchSkippedQnsInsert = array();
                $arrSkippedQuestions = array();
                $uniqueId = $trade_id.'-'.random_strings(6);
                
                $arrExistingQuestionsDetails = $this->Import_model->get_questions_details($arrQns);
                if($arrExistingQuestionsDetails != false) {
                    foreach($arrExistingQuestionsDetails as $qdetails) {
                        $existingQn = strtolower($qdetails['question']);
                        $arrExistingQuestions[$existingQn] = $existingQn;
                    }
                }

                for($i = 0;$i < count($insertExcelData); $i++) {
                    foreach($insertExcelData[$i] as $nosId => $arrMainData) {
                        //echo "<br> Nos Id ".$nosId;
                        foreach($arrMainData as $arrSubData) {
                            /*print "<pre>";
                            print_r($arrExistingQuestions);	
                            print "</pre>";*/
                            //echo "<br> Q no ".$arrSubData['qn_no'];

                            $errorDupQns = 0;
                            $errorWrongQuestionType = 0;
                            $errorWrongAnswerType = 0;
                            $reasonSkipped = "";

                            //Check whether the question_type column is in right format
                            if(!array_key_exists(strtolower($arrSubData['question_type']),$arrQuestionTypeColFormat)) {
                                //echo "<br> Err type ".strtolower($arrSubData['question_type']);
                                $errorWrongQuestionType++;
                            }
                            if(array_key_exists(strtolower($arrSubData['question']),$arrExistingQuestions)) {
                                //echo "<br> Err Qn ".strtolower($arrSubData['question']);
                                $errorDupQns++;
                            }
                            if(strtolower($arrSubData['question_type']) == 'theory' || strtolower($arrSubData['question_type']) == 'practicalskill') {
                                if(!in_array(strtolower($arrSubData['correct_ans']),$arrAnsTypeColFormat)) {
                                    //echo "<br> Err Ans type ".strtolower($arrSubData['correct_ans']);
                                    $errorWrongAnswerType++;
                                }
                            }
                            
                            $question_type = $arrQuestionTypeColFormat[strtolower($arrSubData['question_type'])];
                            if($errorWrongQuestionType == 0 && $errorDupQns == 0 && $errorWrongAnswerType == 0) {
                                $option_a = strtolower($arrSubData['option_a']);
                                $option_b = strtolower($arrSubData['option_b']);
                                $option_c = strtolower($arrSubData['option_c']);
                                $option_d = strtolower($arrSubData['option_d']);
                                $correct_ans = strtolower($arrSubData['correct_ans']);

                                $arrInsert[0]['trade_id'] = $trade_id;
                                $arrInsert[0]['nos_id'] = $nosId;
                                $arrInsert[0]['question_type'] = $this->db->escape_str($question_type);
                                $arrInsert[0]['question'] = $this->db->escape_str($arrSubData['question']);
                                $arrInsert[0]['option_a'] = $this->db->escape_str($option_a);
                                $arrInsert[0]['option_b'] = $this->db->escape_str($option_b);
                                $arrInsert[0]['option_c'] = $this->db->escape_str($option_c);
                                $arrInsert[0]['option_d'] = $this->db->escape_str($option_d);
                                $arrInsert[0]['correct_ans'] = $this->db->escape_str($correct_ans);
                                $arrInsert[0]['marks'] = $arrSubData['marks'];
        
                                //Check whether Language questions are existing
                                if(count($arrLangHeaders) > 0) {
                                    foreach($arrLangHeaders as $lId => $arrLangData) {
                                        $langName = '_'.strtolower($arrLangName[$lId]);
                                        //echo "<br> lang name ".$langName;

                                        foreach($arrLangData as $langColName) {
                                            $langColNameWithoutLangName = str_replace($langName,'',$langColName);
                                            $arrInsert[$lId][$langColNameWithoutLangName] = $this->db->escape_str(trim($arrSubData[$langColName]));
                                        }
                                    }
                                }

                                array_push($arrBatchInsert,$arrInsert);
                            }
                            else {
                                if($errorWrongQuestionType > 0) {
                                    $reasonSkipped .= "question_type value (".$arrSubData['question_type'].") does not match as per the format for qn_no ".$arrSubData['qn_no']." in sheet ".$arrNosCodeById[$nosId];
                                }  
                                if($errorDupQns > 0) {
                                    $reasonSkipped .= ",Duplicate Qn. for qn_no ".$arrSubData['qn_no']." in sheet ".$arrNosCodeById[$nosId];
                                }
                                if($errorWrongAnswerType > 0) {
                                    $reasonSkipped .= ",correct_ans value should be a,b,c,d for qn_no ".$arrSubData['qn_no']." in sheet ".$arrNosCodeById[$nosId];
                                } 
                                
                                $reasonSkipped = ltrim($reasonSkipped,",");
                                
                                //Skipped Questions
                                $arrSkippedQuestions = array(
                                    'trade_id' => $trade_id,
                                    'nos_id' => $nosId,
                                    'question_type' => $this->db->escape_str($arrSubData['question_type']),
                                    'question' => $this->db->escape_str($arrSubData['question']),
                                    'marks' => $this->db->escape_str($arrSubData['marks']),
                                    'reason_skipped' => $reasonSkipped,
                                    'unique_id' => $uniqueId
                                );

                                array_push($arrBatchSkippedQnsInsert,$arrSkippedQuestions);
                                //$this->db->insert('tbl_questions_skipped', $arrSkippedQuestions);
                            }

                            $arrNosIdMarks[$nosId][$question_type][] = $arrSubData['marks'];

                            $arrExistingQuestions[strtolower($arrSubData['question'])] = strtolower($arrSubData['question']);
                        }
                    }
                }
                //print "<pre>";
                //print_r($arrBatchInsert);	
                //print_r($arrBatchLangInsert);
                //print_r($arrBatchSkippedQnsInsert);	
                //print_r($arrTradeNosWiseMarks);
                //print_r($arrNosIdMarks);	
                //print "</pre>";
                //exit;

                $errorInsufficientMarksForMatrix = array();

                //Check if the uploaded marks suffices the Trade Nos Marks Matrix
                if(count($arrTradeNosWiseMarks) > 0) {
                    foreach($arrTradeNosWiseMarks as $mNosId => $arrData) {
                        //echo "<br><br> Nos ID ".$mNosId;
                        foreach($arrData as $questionType => $totalMarks) {
                            //echo "<br> questionType ".$questionType." marks ".$totalMarks;
                            if(array_key_exists($mNosId,$arrNosIdMarks) && array_key_exists($questionType,$arrNosIdMarks[$mNosId])) {
                                /*echo "<br> questionType ".$questionType." marks ".$totalMarks;
                                print "<pre>";
                                print_r($arrNosIdMarks[$mNosId][$questionType]);
                                print "</pre>";*/

                                $result = $this->validateMarksCombinations($arrNosIdMarks[$mNosId][$questionType], $totalMarks);

                                if($result == false) {
                                    $errorInsufficientMarksForMatrix[$arrNosCodeById[$mNosId]][$questionType] = $totalMarks;
                                }
                            }
                            else {
                                $missingQuestionType[$arrNosCodeById[$mNosId]][$questionType] = $totalMarks;
                            }
                        }
                    }
                }
                /*print "<pre>";
                print_r($errorInsufficientMarksForMatrix);	
                print "</pre>";
                exit;*/
                if(count($missingQuestionType) > 0) {
                    $type = "error";
                    foreach($missingQuestionType as $sheetName => $arrQuestionTypes) {
                        foreach($arrQuestionTypes as $questionType => $marks) {
                            $message .= "<br>Error in Sheet ".$sheetName;
                            $message .= "<br>Missing question_type for : ".$questionType." for Marks ".$marks;
                        }
                    }
                }
                if(count($errorInsufficientMarksForMatrix) > 0) {
                    $type = "error";
                    foreach($errorInsufficientMarksForMatrix as $sheetName => $arrQuestionTypes) {
                        foreach($arrQuestionTypes as $questionType => $marks) {
                            $message .= "<br>Error in Sheet ".$sheetName;
                            $message .= "<br>Insufficient marks combination for the matrix : ".$questionType." Marks ".$marks;
                        }
                    }
                }
                //exit;
                
                if($type == "success" && (count($arrBatchInsert) > 0 || count($arrBatchSkippedQnsInsert) > 0)) {
                    try {
                        if(count($arrBatchInsert) > 0) {
                            for($i=0; $i<count($arrBatchInsert); $i++) {
                                // Insert the data into the tbl_questions
                                /*print "<pre>";
                                print_r($arrBatchInsert[$i][0]);	
                                print "</pre>";*/

                                $qid = $this->Mdmaster->addRecord($arrBatchInsert[$i][0],'tbl_questions'); //Master array
                                //echo "<br> qid ".$qid;

                                if($qid > 0) {
                                    // Insert into language questions
                                    foreach($arrLangHeaders as $lId => $lColData) {
                                        if(array_key_exists($lId,$arrBatchInsert[$i])) {
                                            $firstKey = key($arrBatchInsert[$i][$lId]);
                                            if (!empty($arrBatchInsert[$i][$lId][$firstKey])) {
                                                $arrBatchInsert[$i][$lId]['lid'] = $lId;
                                                $arrBatchInsert[$i][$lId]['qid'] = $qid;

                                                /*print "<pre>";
                                                print_r($arrBatchInsert[$i][$lId]);	
                                                print "</pre>";*/
                                                
                                                $langQid = $this->db->insert('tbl_language_questions', $arrBatchInsert[$i][$lId]); //Language Questions
                                            } 
                                        }
                                    }
                                }
                            }
                            
                            // Check for errors
                            if ($this->db->affected_rows() > 0) {
                                $type = 'success'; // Data was inserted successfully
                                $message = 'Questions Imported Successfully';

                                $totalImported = count($arrBatchInsert);
                            } else {
                                $type = 'error'; // Data was inserted successfully
                                $message = 'Error while importing Questions';
                            }
                        }
                        
                        if(count($arrBatchSkippedQnsInsert) > 0) {
                            // Insert the data into the tbl_questions_skipped
                            $this->db->insert_batch('tbl_questions_skipped', $arrBatchSkippedQnsInsert);
                
                            // Check for errors
                            if ($this->db->affected_rows() > 0) {
                                $type = 'success'; // Data was inserted successfully
                                $message = 'Skipped Questions Imported Successfully';

                                $totalSkipped = count($arrBatchSkippedQnsInsert);
                            } else {
                                $type = 'error'; // Data was inserted successfully
                                $message = 'Error while importing Skipped Questions';
                            }
                        }

                        $this->db->trans_commit(); // Commit the transaction
                    } catch (Exception $e) {
                        // Handle the exception, log it, or display an error message
                        $this->db->trans_rollback(); // Rollback the transaction
                        $type = 'error'; // Data was inserted successfully
                        $message =  $e->getMessage();
                    }
                }
            }
        }
		
		$response = array('type' => $type,'message' => $message,'totalQns' => $totalQns, 'totalImported' => $totalImported, 'totalSkipped' => $totalSkipped);
		
		print "<pre>";
		print_r($response);		
		exit;
	}

    function validateMarksCombinations($array, $target, $currentIndex = 0, $currentSum = 0) {
        //echo "<br> currentIndex ".$currentIndex;
        //echo "<br> curr sum ".$currentSum;
        
        // Base case: If the current sum equals the target, increment the count
        if ($currentSum == $target) {
            //echo "<br> Match ";
            return true;
        }
        
        // Iterate through the array and try different combinations
        for ($i = $currentIndex; $i < count($array); $i++) {
            $currentValue = $array[$i];

            // Recursively check with the current value included in the sum
            if ($this->validateMarksCombinations($array, $target, $i + 1, $currentSum + $currentValue)) {
                return true;
            }
        }

        // No combination found
        return false;
        
    }   
	
	public function importQuestionsSave1()
    {		
		$message = "";
		
		$allowedFileType = [
			'application/vnd.ms-excel',
			'text/xls',
			'text/xlsx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		];

		if (in_array($_FILES["file"]["type"], $allowedFileType)) 
		{
			//$targetPath = 'uploads/students/' . $_FILES['file']['name'];
			
			$file_name = "Questions_". time() . '_' . str_replace(" ", "", $_FILES['file']['name']);
					
			$targetPath = 'uploads/questions/' . $file_name;
			
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

			$reader = IOFactory::createReaderForFile($targetPath);
            $spreadsheet = $reader->load($targetPath);

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestDataRow();
                $highestColumn = $sheet->getHighestDataColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                
                echo "<br> sheet name ".$sheetName;
                echo "<br> highestRow ".$highestRow;
                echo "<br> highestColumn ".$highestColumn;
                echo "<br> highestColumnIndex ".$highestColumnIndex;

                $data = [];

                for ($row = 2; $row <= $highestRow; ++$row) {
                    // Check if all cells in the row are empty
                    $isEmptyRow = true;
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                        if (!empty($cellValue)) {
                            $isEmptyRow = false;
                            break;
                        }
                    }

                    if ($isEmptyRow) {
                        // Skip the row if it's entirely blank
                        continue;
                    }
                    
                    $rowData = [];
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $header = $sheet->getCellByColumnAndRow($col, 1)->getValue();
                        $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                        $rowData[$header] = $cellValue;
                    }
                    $data[] = $rowData;
                }
                
                print "<pre>";
        		print_r($data);		
        		print "</pre>";

            }
		} else {
			$type = "error";
			$message = "Invalid File Type. Upload Excel File.";
		}
		print "<pre>";
		print_r($arr_questions);		
		exit;
	}
}
