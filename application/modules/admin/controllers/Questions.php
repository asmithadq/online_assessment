<?php defined('BASEPATH') or exit('No direct script access allowed');

require_once ('vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xls; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Questions extends MY_Controller
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
        
        $this->require_module_permission('question_bank');
        
        $this->load->model('Mdmaster');
		$this->load->model('Trades_model');
		$this->load->model('Questions_model');
        $this->load->model('questions_skipped_model');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function import()
    {
        $this->require_permission('add_question_bank');
        
        $data['title'] = 'Import Question Bank';
        
        $condition = "status = 1";
		$data['arr_trades'] = $this->Mdmaster->getAllRecords('tbl_trades',$condition,'trade_code','ASC');

		//Get Languages
        $condition = "status = 1";
        $data['arr_languages'] = $this->Mdmaster->getAllRecords('tbl_languages',$condition,'language_name','ASC');
        
        $this->render_page('admin/questions/import-questionbank',$data);
    }
    
    public function importQuestionsSave()
    {		
		//print "<pre>";
        //print_r($_POST);
        //print_r($_FILES);
        //print "</pre>";
        //exit;
		
		$message = "";
        $messageSkippedQuestions = "";
        $upload_message_error = "";
		$type = "success";
        $arrUploadMessage = array();
        $arr_lids = (array_key_exists('lid',$this->input->post())) ? $this->input->post('lid') : array();
        
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
			//$targetPath = 'uploads/questions/' . $_FILES['file']['name'];
			
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
            $trade_id = $this->input->post('trade_id');
            $uniqueId = $trade_id.'-'.random_strings(6);
            
            $arrTradeDetails = $this->Trades_model->getTradeDetails($trade_id);
            if($arrTradeDetails != false) {
                foreach($arrTradeDetails as $tradeData) {
                    $sheetCode = str_replace(array('/N','/'),'',$tradeData['nos_code']);
                    $nosCode = strtolower($sheetCode);
                    $nosId = $tradeData['nos_id'];
                    $arrNosCodes[$nosCode] = $tradeData['nos_code']; 
                    $arrNosIds[$nosCode] = $nosId;
                    $arrNosCodeById[$nosId] = $sheetCode;
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
                    $header = trim((string) $sheet->getCellByColumnAndRow($col, 1)->getValue());
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
                            $header = trim((string) $sheet->getCellByColumnAndRow($col, 1)->getValue());
                            //echo "<br><br> header ".$header;
                            $cellValue = trim($sheet->getCellByColumnAndRow($col, $row)->getFormattedValue());
                            $rowData[$header] = $cellValue;
                            if($header == 'question' && $cellValue != "") {
                                $cellValue = preg_replace("/\/'+/", "'", $cellValue);
                                $nosQuestion = trim(strtolower(str_replace("'","\\\\\\'",$cellValue))); //Mysql interprets \' as \\\\\\'
                                $arrQns[$nosQuestion] = $nosQuestion;
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
                //$message .= "<br>Error in Sheet ".$sheetName;
                //$message .= "<br>Missing Column Or Incorrect Column Name for Column(s): ".implode(",",$arrColumnNames);

                $arrUploadMessage[$sheetName][] = "Missing Column Or Incorrect Column Name for Column(s): ".implode(",",$arrColumnNames);
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

        if($type == "success") {  //Start first Success
            if(count($insertExcelNosIds) != count($arrNosIds)) {  //If Excel Sheet Nos doesnt match with the Mapped Nos for the trade
                $type = "error";
			    $message .= "<br>Nos Mismatch with the Trade mapped Nos";
            }
            else { //Import Questions
                //echo "<br> --- NOS Questions Array --- ";
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
                $arrBatchSkippedQnsInsert = array();
                $arrSkippedQuestions = array();
                
                if(count($arrQns) > 0) {
                    $arrExistingQuestionsDetails = $this->Questions_model->get_questions_details($arrQns,$trade_id);
                    //echo "<br> str ".$this->db->last_query();exit;
                    if($arrExistingQuestionsDetails != false) {
                        foreach($arrExistingQuestionsDetails as $qdetails) {
                            $existingQn = strtolower(trim(str_replace("\'","'",$qdetails['question']))); 
                            $arrExistingQuestions[$existingQn] = $existingQn;
                        }
                    }
                }

                //Validate existing qns for the trade
                $arrExistingTradeQuestionsDetails = $this->Questions_model->get_questions_by_trade($trade_id);
                //echo "<br> str ".$this->db->last_query();//exit;
                if($arrExistingTradeQuestionsDetails != false) {
                    foreach($arrExistingTradeQuestionsDetails as $q_trade_details) {
                        $arrNosIdMarks[$q_trade_details['nos_id']][$q_trade_details['question_type']][] = $q_trade_details['marks'];
                    }
                }
                
                //echo "<br> --- Existing Questions Array --- ";
                //print "<pre>";
                //print_r($arrExistingQuestions);
                //print "</pre>";
                //exit;

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
                            $errorMissingMarks = 0;
                            $reasonSkipped = "";

                            //Check whether the question_type column is in right format
                            if(!array_key_exists(strtolower(trim($arrSubData['question_type'])),$arrQuestionTypeColFormat)) {
                                //echo "<br> Err type ".strtolower($arrSubData['question_type']);
                                $errorWrongQuestionType++;
                            }
                            if(array_key_exists(strtolower(trim($arrSubData['question'])),$arrExistingQuestions)) {
                                //echo "<br> Err Qn ".strtolower($arrSubData['question']);
                                $errorDupQns++;
                            }
                            /*else {
                                echo "<br> qn ".strtolower(trim($arrSubData['question']));
                            }*/
                            if(strtolower(trim($arrSubData['question_type'])) == 'theory' || strtolower(trim($arrSubData['question_type'])) == 'practicalskill') {
                                if(!in_array(strtolower(trim($arrSubData['correct_ans'])),$arrAnsTypeColFormat)) {
                                    //echo "<br> Err Ans type ".strtolower($arrSubData['correct_ans']);
                                    $errorWrongAnswerType++;
                                }
                            }
                            if(trim($arrSubData['marks']) == "" || !is_numeric(trim($arrSubData['marks']))) {
                                //echo "<br> Err marks ".trim($arrSubData['marks']);
                                $errorMissingMarks++;
                            }
                            
                            $question_type = (array_key_exists(strtolower(trim($arrSubData['question_type'])),$arrQuestionTypeColFormat)) ? $arrQuestionTypeColFormat[strtolower(trim($arrSubData['question_type']))] : trim($arrSubData['question_type']);
                            if($errorWrongQuestionType == 0 && $errorDupQns == 0 && $errorWrongAnswerType == 0 && $errorMissingMarks == 0) {
                                $question = trim(str_replace("'","\'",$arrSubData['question']));
                                $option_a = trim($arrSubData['option_a']);
                                $option_b = trim($arrSubData['option_b']);
                                $option_c = trim($arrSubData['option_c']);
                                $option_d = trim($arrSubData['option_d']);
                                $correct_ans = strtolower(trim($arrSubData['correct_ans']));
                                $marks = trim($arrSubData['marks']);

                                $arrInsert[0]['trade_id'] = $trade_id;
                                $arrInsert[0]['nos_id'] = $nosId;
                                $arrInsert[0]['question_type'] = $question_type;
                                $arrInsert[0]['question'] = $question;
                                $arrInsert[0]['option_a'] = $option_a;
                                $arrInsert[0]['option_b'] = $option_b;
                                $arrInsert[0]['option_c'] = $option_c;
                                $arrInsert[0]['option_d'] = $option_d;
                                $arrInsert[0]['correct_ans'] = $correct_ans;
                                $arrInsert[0]['marks'] = $marks;

                                //Check whether Language questions are existing
                                if(count($arrLangHeaders) > 0) {
                                    foreach($arrLangHeaders as $lId => $arrLangData) {
                                        $langName = '_'.strtolower($arrLangName[$lId]);
                                        //echo "<br> lang name ".$langName;

                                        foreach($arrLangData as $langColName) {
                                            $langColNameWithoutLangName = str_replace($langName,'',$langColName);
                                            $arrInsert[$lId][$langColNameWithoutLangName] = trim($arrSubData[$langColName]);
                                        }
                                    }
                                }

                                array_push($arrBatchInsert,$arrInsert);

                                $arrNosIdMarks[$nosId][$question_type][] = (trim($arrSubData['marks']) > 0) ? trim($arrSubData['marks']) : 0;
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
                                if($errorMissingMarks > 0) {
                                    $reasonSkipped .= ",marks value should be greater then 0 and numeric for qn_no ".$arrSubData['qn_no']." in sheet ".$arrNosCodeById[$nosId];
                                } 
                                
                                $reasonSkipped = ltrim($reasonSkipped,",");
                                
                                //Skipped Questions
                                $arrSkippedQuestions = array(
                                    'trade_id' => $trade_id,
                                    'nos_id' => $nosId,
                                    'question_type' => $arrSubData['question_type'],
                                    'question' => $arrSubData['question'],
                                    'marks' => $arrSubData['marks'],
                                    'reason_skipped' => $reasonSkipped,
                                    'unique_id' => $uniqueId
                                );

                                array_push($arrBatchSkippedQnsInsert,$arrSkippedQuestions);
                            }

                            $arrExistingQuestions[strtolower($arrSubData['question'])] = strtolower($arrSubData['question']);
                        }
                    }
                }
                //print "<pre>";
                //print_r($arrBatchInsert);	
                //print_r($arrBatchSkippedQnsInsert);	
                //print_r($arrTradeNosWiseMarks);
                //print_r($arrNosIdMarks);	
                //print "</pre>";
                //exit;    

                if($type == "success" && count($arrBatchSkippedQnsInsert) > 0) {
                    try {
                        // Insert the data into the tbl_questions
                        $this->db->insert_batch('tbl_questions_skipped', $arrBatchSkippedQnsInsert);
            
                        // Check for errors
                        if ($this->db->affected_rows() > 0) {
                            $type = 'success'; // Data was inserted successfully
                            $messageSkippedQuestions = 'Skipped Questions Imported Successfully';

                            $totalSkipped = count($arrBatchSkippedQnsInsert);
                        } else {
                            $type = 'error'; // Data was inserted successfully
                            $messageSkippedQuestions = 'Error while importing Skipped Questions';
                        }
                        
                        $this->db->trans_commit(); // Commit the transaction
                    } catch (Exception $e) {
                        // Handle the exception, log it, or display an error message
                        $this->db->trans_rollback(); // Rollback the transaction
                        $type = 'error'; // Data was inserted successfully
                        $message =  $e->getMessage();
                    }
                }
                
                $errorInsufficientMarksForMatrix = array();

                //Check if the uploaded marks suffices the Trade Nos Marks Matrix
                if(count($arrTradeNosWiseMarks) > 0) {
                    foreach($arrTradeNosWiseMarks as $mNosId => $arrData) {
                        //echo "<br><br> Nos ID ".$mNosId;
                        foreach($arrData as $questionType => $totalMarks) {
                            //echo "<br> questionType ".$questionType." marks ".$totalMarks;
                            if($totalMarks > 0) {
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
                }
                /*print "<pre>";
                print_r($errorInsufficientMarksForMatrix);	
                print "</pre>";
                exit;*/
                if(count($missingQuestionType) > 0) {
                    $type = "error";
                    foreach($missingQuestionType as $sheetName => $arrQuestionTypes) {
                        foreach($arrQuestionTypes as $questionType => $marks) {
                            $arrUploadMessage[$sheetName][] = "Missing ".$questionType." questions for Marks ".$marks;
                        }
                    }
                }
                if(count($errorInsufficientMarksForMatrix) > 0) {
                    $type = "error";
                    foreach($errorInsufficientMarksForMatrix as $sheetName => $arrQuestionTypes) {
                        foreach($arrQuestionTypes as $questionType => $marks) {
                            $arrUploadMessage[$sheetName][] = "Insufficient marks combination for the matrix : ".$questionType." Marks ".$marks;
                        }
                    }
                }
                //exit;
                
                if($type == "success" && count($arrBatchInsert) > 0) {
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
                        
                        $this->db->trans_commit(); // Commit the transaction
                    } catch (Exception $e) {
                        // Handle the exception, log it, or display an error message
                        $this->db->trans_rollback(); // Rollback the transaction
                        $type = 'error'; // Data was inserted successfully
                        $message =  $e->getMessage();
                    }
                }
            }
        } //End First Success

        $errCnt = 1;

        if(count($arrUploadMessage) > 0) {
            foreach($arrUploadMessage as $sheetName => $arrSheetError) {
                foreach($arrSheetError as $sheetError) {
                    $upload_message_error .= "<tr class='showError'><td>".$errCnt."</td><td><b>".strtoupper($sheetName)."</b></td><td>".$sheetError."</td></tr>";

                    $errCnt++;
                }
            }
        }
		
		$response = array('type' => $type,'message' => $message,'totalQns' => $totalQns, 'totalImported' => $totalImported, 'totalSkipped' => $totalSkipped,
                            'unique_id' => $uniqueId,'messageSkippedQuestions' => $messageSkippedQuestions,'upload_message_error' => $upload_message_error);
		
		/*print "<pre>";
		print_r($response);		
		exit;*/
        echo json_encode($response);
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

    function getSkippedQuestionsLists(){
        $data = $row = array();
        
        // Fetch member's records
        $skippedQuestionsData = $this->questions_skipped_model->getRows($_POST);
        
        $i = $_POST['start'];
        foreach($skippedQuestionsData as $skpQn){
            $i++;
            
            $data[] = array($i, $skpQn['nos_code'], $skpQn['question_type'],$skpQn['question'],$skpQn['marks'],$skpQn['reason_skipped']);
        }
        
        /*echo "<pre>";
        print_r($tradesData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->questions_skipped_model->countAll(),
            "recordsFiltered" => $this->questions_skipped_model->countFiltered($_POST),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
    
    public function list_questions()    
    {        
		$this->require_permission('view_question_bank');
        
        $data['trades'] = $this->Questions_model->getTrades();
		
        $data['title'] = 'View Question Bank';        
        $this->render_page('admin/questions/list-questions',$data);    
    }
	
	
	function getSelectNos()
	{
        $data = array();
        $list_select_nos = "";
		
		$sel_trade_id = $this->input->post('sel_trade_id');
		
        $map_trade_nos = $this->Questions_model->get_map_trade_nos_by_trade_id($sel_trade_id);
     
		foreach($map_trade_nos as $row)
		{
			$list_select_nos .= '<div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" name="chk_select_nos[]" id="chk_select_nos" value="'.$row['nos_id'].'"><label class="form-check-label">'.$row['nos_code'].'-'.$row['nos_title'].'</label></div>';
		}        
        
        echo json_encode($list_select_nos);
    }
	
	    
    function getQuestions()
	{
        $data = array();
		
        $trade_id = $this->input->post('sel_trade_id');
		$select_nos = $this->input->post('select_nos');
		
        $questionsData = $this->Questions_model->getRows($_POST, $trade_id, $select_nos);        
        //echo "<br> str ".$this->db->last_query();exit;
               
        $i = $_POST['start'];
		
        foreach($questionsData as $ques)
		{
            $i++;   

            $question = ($ques['lang_question'] != "") ? $ques['question']."<br>".$ques['lang_question'] : $ques['question'];
            $option_a =  ($ques['lang_option_a'] != "") ? $ques['option_a']."<br>".$ques['lang_option_a'] : $ques['option_a'];
            $option_b =  ($ques['lang_option_b'] != "") ? $ques['option_b']."<br>".$ques['lang_option_b'] : $ques['option_b'];
            $option_c =  ($ques['lang_option_c'] != "") ? $ques['option_c']."<br>".$ques['lang_option_c'] : $ques['option_c'];
            $option_d =  ($ques['lang_option_d'] != "") ? $ques['option_d']."<br>".$ques['lang_option_d'] : $ques['option_d'];

            $question = trim(str_replace("\'","'",$question));
            $option_a = trim(str_replace("\'","'",$option_a));
            $option_b = trim(str_replace("\'","'",$option_b));
            $option_c = trim(str_replace("\'","'",$option_c));
            $option_d = trim(str_replace("\'","'",$option_d)); 

            $action = '<div class="dropdown ms-auto text-end c-pointer">
                            <div class="btn-link" data-bs-toggle="dropdown">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="'.site_url('edit-question/'. $ques['qid']).'"><i class="fas fa-pencil-alt"></i>&nbsp;Edit Question</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteQuestion('. $ques['qid'].',1);"><i class="fas fa-trash"></i>&nbsp;Delete Question</a> 
                            </div>
                        </div>';
            
            $data[] = array($i, $ques['nos_code'],$ques['question_type'],$question,$option_a,$option_b,$option_c,$option_d,$ques['correct_ans'],$ques['marks'],$action);
        }
        
        /*echo "<pre>";
        print_r($questionsData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Questions_model->countAll(),
            "recordsFiltered" => $this->Questions_model->countFiltered($_POST, $trade_id, $select_nos),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
	
	
	function getQuestionTypeCounts()
	{
        $data = array();
		
        $trade_id = $this->input->post('sel_trade_id');
		$select_nos = $this->input->post('select_nos');
		$arr_select_nos = ($select_nos != "") ? explode(',', $select_nos) : array();
        $questionsTypeCounts = $this->Questions_model->getQuestionTypeCounts($trade_id, $arr_select_nos);
        
        $languages = "";
		
		$output = array(
            "Theory" => 0,
			"PracticalSkill" => 0,
            "PracticalActivity" => 0,
            "Viva" => 0,
            "Language" => '',
        );
	   
	    foreach($questionsTypeCounts as $row)
	    {
		   $output[$row['question_type']] = $row['ques_count'];	
           if($row['language_name'] != "") {
            $languages .= ",".str_replace(" ","",trim($row['language_name'])); 
           }
        }

        //echo "<br> languages ".$languages;

        if($languages != "") {
            $arrLanguage = explode(",", $languages);

            /*echo "<pre>";
            print_r($arrLanguage);
            echo "</pre>";*/
            
            $arrLanguageVal = array_unique($arrLanguage);

            $strLanguage = implode(",",$arrLanguageVal);

            // Remove duplicates using array_unique
            $output['Language'] = 'English'.$strLanguage;
        }
	   
        // Output to JSON format
        echo json_encode($output);
    }
	
	
	function getPracticalActivityQuestions()
	{
        $data = array();
		
        $trade_id = $this->input->post('sel_trade_id');
		$select_nos = $this->input->post('select_nos');
		
        $questionsData = $this->Questions_model->getRows($_POST, $trade_id, $select_nos, "PracticalActivity");        
               
        $i = $_POST['start'];
		
        foreach($questionsData as $ques)
		{
            $i++;  
            
            $question = ($ques['lang_question'] != "") ? $ques['question']."<br>".$ques['lang_question'] : $ques['question'];
            $question = strtolower(trim(str_replace("\'","'",$question)));
            $action = '<div class="dropdown ms-auto text-end c-pointer">
                            <div class="btn-link" data-bs-toggle="dropdown">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="'.site_url('edit-question/'. $ques['qid']).'"><i class="fas fa-pencil-alt"></i>&nbsp;Edit Question</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteQuestion('. $ques['qid'].',2);"><i class="fas fa-trash"></i>&nbsp;Delete Question</a> 
                            </div>
                        </div>';
         			
            $data[] = array($i, $ques['nos_code'],$question,$ques['question_type'],$ques['marks'],$action);
        }
        
      /*  echo "<pre>";
        print_r($questionsData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Questions_model->countAll(),
            "recordsFiltered" => $this->Questions_model->countFiltered($_POST, $trade_id, $select_nos, "PracticalActivity"),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }
	
	
	function getVivaQuestions()
	{
        $data = array();
		
        $trade_id = $this->input->post('sel_trade_id');
		$select_nos = $this->input->post('select_nos');
		
        $questionsData = $this->Questions_model->getRows($_POST, $trade_id, $select_nos, "Viva");        
               
        $i = $_POST['start'];
		
        foreach($questionsData as $ques)
		{
            $i++;     
            
            $question = ($ques['lang_question'] != "") ? $ques['question']."<br>".$ques['lang_question'] : $ques['question'];
            $question = strtolower(trim(str_replace("\'","'",$question)));
            $action = '<div class="dropdown ms-auto text-end c-pointer">
                            <div class="btn-link" data-bs-toggle="dropdown">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="'.site_url('edit-question/'. $ques['qid']).'"><i class="fas fa-pencil-alt"></i>&nbsp;Edit Question</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteQuestion('. $ques['qid'].',3);"><i class="fas fa-trash"></i>&nbsp;Delete Question</a> 
                            </div>
                        </div>';
         			
            $data[] = array($i, $ques['nos_code'],$question,$ques['question_type'],$ques['marks'],$action);
        }
        
      /*  echo "<pre>";
        print_r($questionsData);
        echo "</pre>";
        exit;*/
        
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();  
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Questions_model->countAll(),
            "recordsFiltered" => $this->Questions_model->countFiltered($_POST, $trade_id, $select_nos, "Viva"),
            "data" => $data,
        );
        
        $output[$csrf_name] = $csrf_hash; 
        
        // Output to JSON format
        echo json_encode($output);
    }

    public function delete() {
        $this->require_permission('delete_question_bank');

        $qid = $this->input->post('qid');
        $type = $this->input->post('type');
        
        $updData = array('status' => 0);
        $this->db->where('qid', $qid);
        $query = $this->db->update('tbl_questions', $updData);

        $this->db->where('qid', $qid);
        $query = $this->db->update('tbl_language_questions', $updData);
        
        $data['delete'] = true;
        
        echo json_encode($data);

    }
    
	public function edit_question($qid)
    {
        $this->require_permission('edit_question_bank');

        $data['title'] = 'Update Question Bank';
		
		$data['rec_ques'] = $this->Questions_model->getQuestionById($qid);
		        
        $this->render_page('admin/questions/edit-questions',$data);
    }
	
	public function save()
    {
		$qid = $this->input->post('ques_id');
	
		// Update tbl_questions
        $data = array(
            'question_type' => $this->input->post('questionType'),
			'question' => $this->input->post('question_English'),
            'option_a' => $this->input->post('option_a_English'),
            'option_b' => $this->input->post('option_b_English'),
            'option_c' => $this->input->post('option_c_English'),
            'option_d' => $this->input->post('option_d_English'),
            'correct_ans' => $this->input->post('correctAnswer'),
            'marks' => $this->input->post('marks')
        );
		$this->db->where('qid', $qid);
		$query = $this->db->update('tbl_questions', $data);  
		
		// Delete previous data from tbl_language_questions
		$this->db->where('qid', $qid);
	    $this->db->delete('tbl_language_questions');
		
		$arr_lang = $this->Questions_model->getLanguageIds();
		$arr_lang_ids = array();
		foreach($arr_lang as $row)
		{
			$arr_lang_ids[$row['language_name']] = $row['language_id'];
		}
		
		if($this->input->post('question_Hindi'))
		{
			$insData = array(
				'lid' => $arr_lang_ids['Hindi'],
				'qid' => $qid,
				'question' => $this->input->post('question_Hindi'),
				'option_a' => $this->input->post('option_a_Hindi'),
				'option_b' => $this->input->post('option_b_Hindi'),
				'option_c' => $this->input->post('option_c_Hindi'),
				'option_d' => $this->input->post('option_d_Hindi'),
				'created_dts' => date("Y-m-d H:i:s"),
			);
			
			$this->Mdmaster->addRecord($insData,'tbl_language_questions');
		}

		if($this->input->post('question_Tamil'))
		{
			$insData = array(
				'lid' => $arr_lang_ids['Tamil'],
				'qid' => $qid,
				'question' => $this->input->post('question_Tamil'),
				'option_a' => $this->input->post('option_a_Tamil'),
				'option_b' => $this->input->post('option_b_Tamil'),
				'option_c' => $this->input->post('option_c_Tamil'),
				'option_d' => $this->input->post('option_d_Tamil'),
				'created_dts' => date("Y-m-d H:i:s"),
			);
			
			$this->Mdmaster->addRecord($insData,'tbl_language_questions');
		}
		
		if($this->input->post('question_Telugu'))
		{
			$insData = array(
				'lid' => $arr_lang_ids['Telugu'],
				'qid' => $qid,
				'question' => $this->input->post('question_Telugu'),
				'option_a' => $this->input->post('option_a_Telugu'),
				'option_b' => $this->input->post('option_b_Telugu'),
				'option_c' => $this->input->post('option_c_Telugu'),
				'option_d' => $this->input->post('option_d_Telugu'),
				'created_dts' => date("Y-m-d H:i:s"),
			);
			
			$this->Mdmaster->addRecord($insData,'tbl_language_questions');
		}
		
		$this->session->set_flashdata('msg', 'Data updated successfully');	
        
        redirect('list-questions');
    }
	
	public function download_questions()
	{
		$fileName = "download_questions.xlsx";
				
		$trade_id = $this->input->post('trade_id');
		$chk_select_nos = $this->input->post('chk_select_nos');
		if(isset($chk_select_nos))
		{
			$spreadsheet = new Spreadsheet();
			
			$str_nos_ids = implode(",", $chk_select_nos);
			$arr_national_occupational_standards = $this->Questions_model->get_national_occupational_standards_by_ids($str_nos_ids);
			
			foreach($arr_national_occupational_standards as $key=>$sel_nos)
			{
				$sheetCode = str_replace(array('/N','/'),'',$sel_nos['nos_code']);
				$nosCode = strtolower($sheetCode);
				$arr_excel_rec = array();
				$arr_ques = $this->Questions_model->getQuestionByTradeIdNosCode($trade_id, $sel_nos['nos_id']);
				
				if(count($arr_ques) > 0)
				{
					foreach($arr_ques as $row)
					{
						if(!isset($arr_excel_rec[$row['question_id']]))
						{
							$arr_excel_rec[$row['question_id']]['question_id'] = $row['question_id'];
							$arr_excel_rec[$row['question_id']]['question_type'] = $row['question_type'];
							$arr_excel_rec[$row['question_id']]['question'] = $row['question'];
							$arr_excel_rec[$row['question_id']]['option_a'] = $row['option_a'];
							$arr_excel_rec[$row['question_id']]['option_b'] = $row['option_b'];
							$arr_excel_rec[$row['question_id']]['option_c'] = $row['option_c'];
							$arr_excel_rec[$row['question_id']]['option_d'] = $row['option_d'];
							$arr_excel_rec[$row['question_id']]['correct_ans'] = $row['correct_ans'];
							$arr_excel_rec[$row['question_id']]['marks'] = $row['marks'];
						}
						if($row['language_name'] == "Hindi")
						{
							$arr_excel_rec[$row['question_id']]['question_hindi'] = $row['lan_ques'];
							$arr_excel_rec[$row['question_id']]['option_a_hindi'] = $row['lan_option_a'];
							$arr_excel_rec[$row['question_id']]['option_b_hindi'] = $row['lan_option_b'];
							$arr_excel_rec[$row['question_id']]['option_c_hindi'] = $row['lan_option_c'];
							$arr_excel_rec[$row['question_id']]['option_d_hindi'] = $row['lan_option_d'];
						}
						if($row['language_name'] == "Tamil")
						{
							$arr_excel_rec[$row['question_id']]['question_tamil'] = $row['lan_ques'];
							$arr_excel_rec[$row['question_id']]['option_a_tamil'] = $row['lan_option_a'];
							$arr_excel_rec[$row['question_id']]['option_b_tamil'] = $row['lan_option_b'];
							$arr_excel_rec[$row['question_id']]['option_c_tamil'] = $row['lan_option_c'];
							$arr_excel_rec[$row['question_id']]['option_d_tamil'] = $row['lan_option_d'];
						}
						if($row['language_name'] == "Telugu")
						{
							$arr_excel_rec[$row['question_id']]['question_telugu'] = $row['lan_ques'];
							$arr_excel_rec[$row['question_id']]['option_a_telugu'] = $row['lan_option_a'];
							$arr_excel_rec[$row['question_id']]['option_b_telugu'] = $row['lan_option_b'];
							$arr_excel_rec[$row['question_id']]['option_c_telugu'] = $row['lan_option_c'];
							$arr_excel_rec[$row['question_id']]['option_d_telugu'] = $row['lan_option_d'];
						}
					}				
					
					$arr_data = array();
					foreach ($arr_excel_rec as $item)
					{
						$arr_data[] = $item;
					}
					
					$headers = array();
					foreach ($arr_data[0] as $hed=>$item)
					{
						$headers[] = $hed;
					}
									
					if($key == 0){
						$sheet = $spreadsheet->getActiveSheet();					
					}
					else{
						$sheet = $spreadsheet->createSheet();	
					}
					
					$sheet->setTitle($nosCode);	
					
					for ($i = 0, $l = sizeof($headers); $i < $l; $i++) {
						$sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
					}
					
					for ($i = 0, $l = sizeof($arr_data); $i < $l; $i++) { // row $i
						$j = 0;
						foreach ($arr_data[$i] as $k => $v) { // column $j
							$sheet->setCellValueByColumnAndRow($j + 1, ($i + 1 + 1), $v);
							$j++;
						}
					}	
				}				
			}
			$writer = new Xlsx($spreadsheet);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
			$writer->save('php://output');
		}
		else {
            $this->session->set_flashdata('error', 'Please select Trade and Select Nos');
			redirect('list-questions');
        }       
		
	}
}