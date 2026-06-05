<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once ('vendor/autoload.php');

class Api_candidate extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('mainModel');
    }

    function getToken()
    {
        echo $this->token;
    }

    function checkToken($token)
    {
        $requested_token = $this->input->post('token');
        
        if ($requested_token == $token) {
            $data['status'] = true;
            return $data;
        } else {
            $data['status'] = false;
            $data['rcode'] = 500;
            $data['message'] = "Token Mismatch Exception";
            return $data;
        }
    }

    function checkUserId()
    {
        $isValidated = true;
        $server_key_user = '';
        $candidate_id = "";
        if ($this->input->post('candidate_id') != "") {
            if ($this->input->post('candidate_id') == '0') {
                $isValidated = true;
            } else {
                $candidate_id = id_decode($this->input->post('candidate_id'));
                //echo "<br> userid ".$candidate_id;exit;
                $checkUser = $this->mainModel->getAllDataByVal('tbl_students', array('student_id' => $candidate_id));
                if ($checkUser->num_rows() == 0) $isValidated = false;
                else {
                    $userObj = $checkUser->row();
                }
            }
        } else {
            $isValidated = false;
        }
        if ($isValidated == true) {
            $data['status'] = true;
            $data['candidate_id'] = $candidate_id;
            $data['candidate_details'] = $userObj;
            return $data;
        } else {
            $data['status'] = false;
            $data['rcode'] = 500;
            $data['message'] = "Not A Valid Candidate";
            return $data;
        }
    }

    public function uploadBase64Image($base64_image,$destfolder,$new_name,$watermarkValue = "",$snapshot = 0)
    {
        $this->load->library('image_lib');

        /*
        |--------------------------------------------------------------------------
        | Validate & Save Image
        |--------------------------------------------------------------------------
        */
        $upload = $this->validateAndSaveBase64Image(
            $base64_image,
            $destfolder,
            $new_name
        );

        if (!$upload) {
            return 'default-image.png';
        }

        $file_name = $upload['file_name'];
        $file_path = $upload['file_path'];

        /*
        |--------------------------------------------------------------------------
        | Apply Watermark
        |--------------------------------------------------------------------------
        */
        if (!empty($watermarkValue) && file_exists($file_path)) {
            $this->watermarkImage(
                $watermarkValue,
                $file_path,
                $snapshot
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Thumbnail Directory
        |--------------------------------------------------------------------------
        */
        $thumb_dir = FCPATH . 'uploads/' . $destfolder . '/thumbs/';

        if (!is_dir($thumb_dir)) {
            mkdir($thumb_dir, 0755, true);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Thumbnail
        |--------------------------------------------------------------------------
        */
        $resized_image_path = $thumb_dir . $file_name;

        if (!copy($file_path, $resized_image_path)) {

            log_message(
                'error',
                'Failed to create thumbnail copy for ' . $file_name
            );

            return $file_name; // Original image already saved
        }

        $config_resize = [
            'image_library'  => 'gd2',
            'source_image'   => $resized_image_path,
            'maintain_ratio' => TRUE,
            'width'          => 300,
            'height'         => 200
        ];

        $this->image_lib->initialize($config_resize);

        if (!$this->image_lib->resize()) {

            log_message(
                'error',
                $this->image_lib->display_errors('', '')
            );
        }

        $this->image_lib->clear();

        return file_exists($file_path)
            ? $file_name
            : 'default-image.png';
    }
    
    public function watermarkImage($watermarkValue,$file_path,$snapshot)
    {
        // Load the original image
        $original_image = imagecreatefromjpeg($file_path);
        if (!$original_image) {
            die("Failed to load the original image.");
        }

        // Get the image dimensions
        $image_width = imagesx($original_image);
        $image_height = imagesy($original_image);

        // Create the overlay
        $overlay_width = $image_width; // Width of the overlay (adjust as needed)
        $overlay_height = 350; // Height of the overlay (adjust as needed)
        $overlay = imagecreatetruecolor($image_width, $overlay_height);

        // Make the overlay semi-transparent
        $overlay_color = imagecolorallocatealpha($overlay, 0, 0, 0, 75); // Black with 75/127 transparency
        imagefill($overlay, 0, 0, $overlay_color);

        // Add text to the overlay
        $font_path = './system/fonts/texb.ttf'; // Path to the TTF font
        $font_size = ($snapshot <= 1) ? 40 : 20; //If screen sanpshot then reduce the font
        $font_color = imagecolorallocate($overlay, 255, 255, 255); // White color
        $lines = explode("\n", $watermarkValue);

        // Position settings
        $padding = 15;
        $top_space = 30; // Space at the top of the overlay
        $line_spacing = 20; // Space between lines
        /*$y = $top_space + $font_size; // Start position for the first line of text
        foreach ($lines as $line) {
            imagettftext($overlay, $font_size, 0, $padding, $y, $font_color, $font_path, trim($line));
            $y += $font_size + $line_spacing; // Adjust line spacing as needed
        }*/
        
        // Calculate the starting position for the first line of text
        $y = $overlay_height - $padding; // Start from the bottom of the overlay
        foreach (array_reverse($lines) as $line) { // Reverse lines to start from the bottom
            $textbox = imagettfbbox($font_size, 0, $font_path, $line);
            $text_width = $textbox[2] - $textbox[0];
            $x = $overlay_width - $text_width - $padding; // Position text at the right
            imagettftext($overlay, $font_size, 0, $x, $y, $font_color, $font_path, $line);
            $y -= ($font_size + $line_spacing); // Move up for the next line
        }
    
        // Position overlay at the bottom right
        $overlay_x_position = $image_width - $overlay_width - $padding; // Position overlay at the right
        $overlay_y_position = $image_height - $overlay_height - $padding; // Position overlay at the bottom
        imagecopy($original_image, $overlay, $overlay_x_position, $overlay_y_position, 0, 0, $overlay_width, $overlay_height);

        // Save the new image
        if (!imagejpeg($original_image, $file_path, 90)) {
            die("Failed to save the new image.");
        }

        // Clean up
        imagedestroy($original_image);
        imagedestroy($overlay);

        //echo "Watermark applied and image saved successfully!";
    }

    function notification($data, $fcm_token)
    {
        $this->db->insert('notification', $data);

        $server_key = $this->mainModel->getDataByVal('firebase_server_key', 'ci_general_settings', array('id' => 1));

        $msgArr = array(
            'body'     => $data['notification'],
            'title'    => $data['title'],
        );

        $fields = array(
            'to'        => $fcm_token,
            'data'    => $msgArr
        );
        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function login(){
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/
        
        //$userRes = $this->checkUserId();
        
        $enrollment_number = $this->input->post('enrollment_number');
        $password = $this->input->post('password');
        $new_device_id = trim($this->input->post('device_id') ?? '');
        
        //if ($userRes['status'] == true) {
        
            //Get candidate user details
            $arr_user = $this->mainModel->getAllRecords('tbl_students',array("enrollment_number" => $enrollment_number,"password" => $password, "status" => 1));
            
            if($arr_user == false){ //Invalid User
                $data['status'] = false;
                $data['rcode'] = 500;
                $data['message'] = 'Invalid Candidate';
            }
    		else{  
                $candidate_id = $arr_user[0]['student_id'];
                $unique_token = $arr_user[0]['unique_token'];
                $device_id = $arr_user[0]['device_id'];
                $student_attendance = $arr_user[0]['student_attendance'];
                $multiple_login_error = 0;
                
                //Validate device_id to disallow multiple login
                if ($device_id != "") {
                    if ($new_device_id != $device_id) {
                        $multiple_login_error++;
                    }
                } else {
                    $updData['device_id'] = $new_device_id;
                }
                
                if($student_attendance != 'Present') {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = ($student_attendance == 'Absent') ? 'Invalid Login.Your attendance is marked as absent!' : 'Invalid Login.Your attendance is not marked!';
                }
                else if ($multiple_login_error > 0) {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Invalid Login.You have already logged in to another device!';
                }
                else {
                    //Update logged_in_dts
                    if($arr_user[0]['logged_in_dts'] == "") {
                        $updData['logged_in_dts'] = date('Y-m-d H:i:s');
                    }
                    $updData['logged_in_status'] = 1;
                    $query = $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);
    
        		    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $unique_token;
                    $data['enrollment_number'] = $enrollment_number;
                    $data['candidate_status'] = $arr_user[0]['status'];
                    $data['profile_updated'] = $arr_user[0]['profile_updated'];
                    $data['auto_logout_secs'] = $this->config->item('auto_logout_mins') * 60;
                    $data['message'] = 'Candidate logged in successfully';
                }    
    		}
    	/*}
    	else {
            $data = $userRes;
        }	*/
        
    	echo json_encode($data);
    }

    public function logout(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $updData['logged_in_status'] = 0;
                $updData['device_id'] = "";
                $updData['logged_out_dts'] = date('Y-m-d H:i:s');
                $query = $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['candidate_id'] = "";
                $data['unique_token'] = "";
                $data['message'] = 'Candidate logged out successfully';
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getCandidateProfile(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
            
                $arrCandidatedata = $this->mainModel->getCandidateProfile($candidate_id);
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['candidate_id'] = id_encode($candidate_id);
                $data['unique_token'] = $token;
                $data['user_details'] = $arrCandidatedata;
                
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }

    public function updateCandidateProfile(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('name') != "" && $this->input->post('mobile') != "" && $this->input->post('gender') != "" && $this->input->post('father_name') != ""
                    && $this->input->post('dob') != "" && $this->input->post('aadhar_number') != "" && $this->input->post('city') != ""
                    && $this->input->post('state_id') != "" && $this->input->post('district_id') != "" && $this->input->post('pincode') != "" 
                    && $this->input->post('lat') != "" && $this->input->post('long') != ""){

                    $candiateName = trim($this->input->post('name'));    
                    
                    //Update Candidate
                    $updData['student_name'] = $candiateName;
                    $updData['student_email'] = $this->input->post('email');
                    $updData['student_mobile'] = $this->input->post('mobile');
                    $updData['gender'] = $this->input->post('gender');
                    $updData['father_name'] = $this->input->post('father_name');
                    $updData['dob'] = date('Y-m-d',strtotime($this->input->post('dob')));
                    $updData['aadhar_number'] = $this->input->post('aadhar_number');
                    $updData['address'] = $this->input->post('address');
                    $updData['city'] = $this->input->post('city');
                    $updData['state_id'] = $this->input->post('state_id');
                    $updData['district_id'] = $this->input->post('district_id');
                    $updData['pincode'] = $this->input->post('pincode');
                    //$updData['aadhar_front_filename'] = $this->uploadBase64Image($this->input->post('aadhar_front_filename'), 'aadhaar', $candidate_id.'-aadhaar-image-front',$watermarkValue);
                    //$updData['aadhar_back_filename'] = $this->uploadBase64Image($this->input->post('aadhar_back_filename'), 'aadhaar', $candidate_id.'-aadhaar-image-back',$watermarkValue);
                    $updData['lat'] = $this->input->post('lat');
                    $updData['lng'] = $this->input->post('long');
                    $updData['geo_address'] = "";
                    $updData['profile_updated'] = 1;
                    
                    $query = $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Profile updated successfully';
                    
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please enter mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveCandidateProfileImage()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('student_photo') != "") {
                    //Check whether new image is selected    
                    $student_photo_path = base_url().$this->config->item('student_photo_path');
                    $position = strpos($this->input->post('student_photo'), $student_photo_path); //If its old image link

                    if ($position == false) { //New image uploded
                        $enrollmentNumber = $arr_student_list->enrollment_number;
                        $candiateName = trim($arr_student_list->student_name);
                        $getGeoAddress = trim($this->input->post('geo_address'));
                        $geoAddress = ""; //$getGeoAddress;
                        $arr_geo_address = explode(",",$getGeoAddress); 
                        $arr_count = count($arr_geo_address); 
                        
                        // Counter to keep track of words
                        $count = 0;
                        
                        // Loop through the words
                        foreach ($arr_geo_address as $address) {
                            // Display the address
                            $geoAddress .= $address.",";
                            
                            // Increment the counter
                            $count++;
                            
                            // If four words have been displayed, start a new line
                            if ($count % 4 == 0) {
                                $geoAddress .="\n";
                            }
                        }
                        
                        // If there are remaining words less than four, add them to the last line
                        if ($count % 4 != 0) {
                            $geoAddress .="\n";
                        }
                        $dateTime = date('d-m-Y h:i A')." GMT +05:30";
                        
                        //Create WaterMark
                        $watermarkValue = $enrollmentNumber."-".$candiateName."\nLat ".$this->input->post('lat').",Long ".$this->input->post('long')."\n".$geoAddress.$dateTime;

                        $updData['student_photo'] = $this->uploadBase64Image($this->input->post('student_photo'), 'student_photo', $enrollmentNumber.'-'.date('YmdHis').'-student-photo',$watermarkValue);

                        if($arr_student_list->student_photo != "" && $updData['student_photo'] != "") {
                            $file = $this->config->item('student_photo_path').$arr_student_list->student_photo;
                            $watermark_file = str_replace(".jpeg","-watermark.jpeg",$this->config->item('student_photo_path').$arr_student_list->student_photo);
                            $thumb_file = $this->config->item('student_photo_thumbs_path').$arr_student_list->student_photo;
                            
                            // Check if the file exists before attempting to delete it
                            if (file_exists($file)) {
                                // Attempt to delete the file
                                unlink($file);
                            }
                            if (file_exists($watermark_file)) {
                                unlink($watermark_file);
                            } 
                            if (file_exists($thumb_file)) {
                                unlink($thumb_file);
                            }
                        }

                        $query = $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);
                    } 
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Image saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Image data is empty';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function updateCandidateProfileWeb(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('student_name') != "" && $this->input->post('student_mobile') != "" && $this->input->post('gender') != "" && $this->input->post('father_name') != ""
                    && $this->input->post('dob') != "" && $this->input->post('aadhar_number') != "" && $this->input->post('address') != "" && $this->input->post('city') != ""
                    && $this->input->post('state_id') != "" && $this->input->post('district_id') != "" && $this->input->post('pincode') != "" 
                    && $this->input->post('student_photo') != "" && $this->input->post('lat') != "" && $this->input->post('long') != ""){

                    //Update Candidate
                    $updData['student_name'] = $this->input->post('student_name');
                    $updData['student_email'] = $this->input->post('student_email');
                    $updData['student_mobile'] = $this->input->post('student_mobile');
                    $updData['gender'] = $this->input->post('gender');
                    $updData['father_name'] = $this->input->post('father_name');
                    $updData['dob'] = $this->input->post('dob');
                    $updData['aadhar_number'] = $this->input->post('aadhar_number');
                    $updData['address'] = $this->input->post('address');
                    $updData['city'] = $this->input->post('city');
                    $updData['state_id'] = $this->input->post('state_id');
                    $updData['district_id'] = $this->input->post('district_id');
                    $updData['pincode'] = $this->input->post('pincode');
                    $updData['student_photo'] = $this->input->post('student_photo');
                    //$updData['aadhar_front_filename'] = $this->input->post('aadhar_front_filename');
                    //$updData['aadhar_back_filename'] = $this->input->post('aadhar_back_filename');
                    $updData['lat'] = $this->input->post('lat');
                    $updData['lng'] = $this->input->post('long');
                    $updData['geo_address'] = $this->input->post('geo_address');
                    $updData['profile_updated'] = 1;
                    
                    $query = $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Profile updated successfully';
                    $data['updData'] = $updData;
                    
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please enter mandatory fields';
                    $data['updData'] = $updData;
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function dashboard()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];
            $student_photo_thumbs_path = base_url().$this->config->item('student_photo_thumbs_path');
            $arr_student_list->student_photo = $student_photo_thumbs_path.$arr_student_list->student_photo;

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $student_assessment_status =  $arr_student_list->student_assessment_status;

            $arr_student_list_json_string = json_encode($arr_student_list);

            // Convert JSON string to associative array
            $arr_student_list = convertNullToEmptyString(json_decode($arr_student_list_json_string, true));
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                //Get Settings Data
		        $arr_settings_list = $this->mainModel->getDataResult('tbl_general_settings', 'id', 'ASC', 1);

                $current_app_version = $arr_settings_list[0]['candidate_app_version'];
                $check_version = ($this->input->post('version') != $current_app_version) ? 1 : 0;

                $arr_batch_details = array();
                
                //Get Batch Details
                $arr_batch_list = $this->mainModel->getBatchDetails($tb_id);
                if($arr_batch_list != false) {
                    $currentDateTime = strtotime(date('Y-m-d H:i:s'));
                    $assessmentStartDateTime = strtotime($arr_batch_list[0]['tb_start_date_time']);
                    $assessmentEndDateTime = strtotime($arr_batch_list[0]['tb_end_date_time']);
                    $assessmentStatus = $arr_batch_list[0]['tb_assessment_status'];

                    //echo "<br> assessmentStartDateTime ".$assessmentStartDateTime.' assessmentEndDateTime '.$assessmentEndDateTime.' currentDateTime '.$currentDateTime;exit;
                    
                    $assessmentStatusVal = 0;

                    //Imp note: if the text are changed then you need to change in candidate web login dashboard screen as well

                    if($currentDateTime < $assessmentStartDateTime && $currentDateTime < $assessmentEndDateTime) { //Assessment Not Yet Started
                        $assessmentStatus = "Assessment Not Yet Started";
                    }
                    else if($currentDateTime > $assessmentStartDateTime && $currentDateTime > $assessmentEndDateTime) { //Assessment Expired
                        if($student_assessment_status == 'Pending') {
                            $assessmentStatus = "Assessment Link Expired";
                            $assessmentStatusVal = 5;
                        }
                        else {
                            $assessmentStatus = "Assessment Completed";
                        }
                    }
                    else if($currentDateTime >= $assessmentStartDateTime && $currentDateTime <= $assessmentEndDateTime) { //Assessment Starts
                        if($student_assessment_status == 'Pending') {
                            $assessmentStatus = "Take Assessment";
                            $assessmentStatusVal = 1;
                        }
                        else if($student_assessment_status == 'Practical Activity') {
                            $assessmentStatus = "Take Practical Activity";
                            $assessmentStatusVal = 2;
                        }
                        else if($student_assessment_status == 'Viva') {
                            $assessmentStatus = "Take Viva";
                            $assessmentStatusVal = 3;
                        }
                        else if($student_assessment_status == 'Completed') {
                            $assessmentStatus = "Assessment Completed";
                            $assessmentStatusVal = 4;
                        }
                    }

                    $arr_batch_details['theory_instructions'] = $arr_batch_list[0]['theory_instructions'];
                    $arr_batch_details['trade_name'] = $arr_batch_list[0]['trade_code']." - ".$arr_batch_list[0]['trade_name'];
                    $arr_batch_details['batch_id'] = $arr_batch_list[0]['batch_id'];
                    $arr_batch_details['scheme'] = $arr_batch_list[0]['scheme_name'].'('.$arr_batch_list[0]['subscheme_name'].')';
                    $arr_batch_details['exam_duration_mins'] = convertMinutesToHoursAndMinutes($arr_batch_list[0]['exam_duration_mins']);
                    $arr_batch_details['snapshot_duration_mins'] = floor($arr_batch_list[0]['exam_duration_mins'] / 10); 
                    $arr_batch_details['assessment_date'] = date('d-m-Y H:i:s',$assessmentStartDateTime)." To ".date('d-m-Y H:i:s',$assessmentEndDateTime);
                    $arr_batch_details['assessment_status'] = $assessmentStatus;
                    $arr_batch_details['assessment_status_val'] = $assessmentStatusVal;
                    $arr_batch_details['student_assessment_status'] = $student_assessment_status;
                    $arr_batch_details['auto_logout_mins'] = $this->config->item('auto_logout_mins') * 60;
                }
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['candidate_id'] = id_encode($candidate_id);
                $data['unique_token'] = $token;
                $data['contact'] = $arr_settings_list[0]['contact_us'];
                $data['batch_details'] = $arr_batch_details;
                $data['candidate_details'] = $arr_student_list;
                $data['check_version'] = $check_version;
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getStateDistrictList(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {

                $arrStateDistrictList = array();
                $arrStateList = array();
            
                $arrStateDistrictdata = $this->mainModel->getStateDistrictList();
                if($arrStateDistrictdata != false) {
                    foreach($arrStateDistrictdata as $arrData) {
                        $state_id = $arrData["state_id"];
                        $state_name = $arrData["state_name"];
                        $district_id = $arrData["dist_id"];
                        $district_name = $arrData["dist_name"];

                        $state_index = array_search($state_id, array_column($arrStateDistrictList, 'state_id'));

                        if ($state_index === false) {
                            // If state doesn't exist, create a new state object
                            $state = [
                                "state_id" => $state_id,
                                "name" => $state_name,
                                "districts" => []
                            ];
                            array_push($arrStateDistrictList, $state);
                            $state_index = count($arrStateDistrictList) - 1;
                        }

                        // Add district to the corresponding state
                        $district = [
                            "district_id" => $district_id,
                            "district_name" => $district_name
                        ];
                        array_push($arrStateDistrictList[$state_index]["districts"], $district);

                    }
                    
                }

                /*echo "<pre>";
                print_r($arrStateDistrictList);
                echo "</pre>";
                exit;*/
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['candidate_id'] = id_encode($candidate_id);
                $data['unique_token'] = $token;
                $data['state_district_list'] = $arrStateDistrictList;
                
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }

    public function getCandidateQuestionsList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $qp_generated_status =  $arr_student_list->qp_generated_status;
            $theory_questions =  $arr_student_list->theory_questions;
            $theory_answers_record_generated = $arr_student_list->theory_answers_record_generated;
            $theory_start_dts = $arr_student_list->theory_start_dts;
            $time_left_secs = $arr_student_list->time_left_secs;

            $arr_batch_details = array();
            $exam_duration_secs = 0;
            $arr_batch_list = $this->mainModel->getBatchDetails($tb_id);
            $lid = 0;

            if($arr_batch_list != 'failure') {
                $currentDateTime = strtotime(date('Y-m-d H:i:s'));
                $assessmentStartDateTime = strtotime($arr_batch_list[0]['tb_start_date_time']);
                $assessmentEndDateTime = strtotime($arr_batch_list[0]['tb_end_date_time']);

                $arr_batch_details['trade_name'] = $arr_batch_list[0]['trade_code']." - ".$arr_batch_list[0]['trade_name'];
                $arr_batch_details['batch_id'] = $arr_batch_list[0]['batch_id'];
                $arr_batch_details['scheme'] = $arr_batch_list[0]['scheme_name'].'('.$arr_batch_list[0]['subscheme_name'].')';
                $arr_batch_details['assessment_date'] = date('d-m-Y H:i:s',$assessmentStartDateTime)." To ".date('d-m-Y H:i:s',$assessmentEndDateTime);
                $arr_batch_details['exam_duration_mins'] = convertMinutesToHoursAndMinutes($arr_batch_list[0]['exam_duration_mins']);
                $arr_batch_details['snapshot_duration_mins'] = floor($arr_batch_list[0]['exam_duration_mins'] / 10); 
                $arr_batch_details['auto_logout_mins'] = $this->config->item('auto_logout_mins');
                $arr_batch_details['lid'] = $arr_batch_list[0]['lid'];
                $lid = ($arr_batch_list[0]['lid'] > 0) ? $arr_batch_list[0]['lid'] : 0;
            }    

            if($arr_batch_list != 'failure' && $theory_start_dts == "") {
                $exam_duration_secs = convertMinutesToSeconds($arr_batch_list[0]['exam_duration_mins']);
            }
            else {
                $exam_duration_secs = $time_left_secs;
            }
            
            $question_list = array();
            $arrLangQuestions = array();
            $question_images_path = base_url().$this->config->item('question_images_path');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($qp_generated_status == 1 && $theory_questions != "") {
                    $arrQuestionIds = explode(",",$theory_questions);

                    if($lid > 0) {
                        $arrGetQuestionLanguageDetails = $this->mainModel->getCandidateLanguageQuestionDetails($arrQuestionIds,$lid);
                        //echo "<br> str ".$this->db->last_query();exit;
                        if($arrGetQuestionLanguageDetails != false) {
                            foreach($arrGetQuestionLanguageDetails as $qnLangData) {
                                $arrLangQuestions[$qnLangData['qid']]['question'] = $qnLangData['lang_question'];
                                $arrLangQuestions[$qnLangData['qid']]['option_a'] = $qnLangData['lang_option_a'];
                                $arrLangQuestions[$qnLangData['qid']]['option_b'] = $qnLangData['lang_option_b'];
                                $arrLangQuestions[$qnLangData['qid']]['option_c'] = $qnLangData['lang_option_c'];
                                $arrLangQuestions[$qnLangData['qid']]['option_d'] = $qnLangData['lang_option_d'];
                            }    
                        }   
                    }

                    //Get the questions list and details
                    $arrGetQuestionDetails = $this->mainModel->getCandidateQuestionDetails($arrQuestionIds,$candidate_id,$theory_answers_record_generated,'theory');
                    //echo "<br> str ".$this->db->last_query();exit;

                    if($arrGetQuestionDetails != false) {
                        foreach($arrGetQuestionDetails as $qnData) {
                            if(strtoupper($qnData['option_c']) == "NA") {
                                $qnData['option_c'] = "";
                            }
                            if(strtoupper($qnData['option_d']) == "NA") {
                                $qnData['option_d'] = "";
                            }

                            $question = $qnData['question'];
                            $option_a = $qnData['option_a'];
                            $option_b = $qnData['option_b'];
                            $option_c = $qnData['option_c'];
                            $option_d = $qnData['option_d'];

                            if($lid > 0) {
                                if(strtoupper($qnData['option_c']) == "NA") {
                                   $arrLangOptions[$qnData['qid']]['option_c'] = "";
                                }
                                if(strtoupper($qnData['option_d']) == "NA") {
                                    $arrLangOptions[$qnData['qid']]['option_d'] = "";
                                }
        
                                $question = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['question']."|lang|".$arrLangQuestions[$qnData['qid']]['question'] : $qnData['question'];
                                $option_a = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_a']."|lang|".$arrLangQuestions[$qnData['qid']]['option_a'] : $qnData['option_a'];
                                $option_b = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_b']."|lang|".$arrLangQuestions[$qnData['qid']]['option_b'] : $qnData['option_b'];
                                $option_c = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_c']."|lang|".$arrLangQuestions[$qnData['qid']]['option_c'] : $qnData['option_c'];
                                $option_d = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_d']."|lang|".$arrLangQuestions[$qnData['qid']]['option_d'] : $qnData['option_d'];
                            }
                            
                            // Use regular expression to capture text between |% and %|
							if (preg_match_all('/\|%([^%]+)%\|/', $question, $qmatches)) {
								// Extract captured text
								$arrImgNameQn = $qmatches[1];
								if(count($arrImgNameQn) > 0) {
									foreach($arrImgNameQn as $imgNameQn) {
										$imgUrlQn = "<img src='".$question_images_path.$imgNameQn."'>";
										$question = str_replace('|%'.$imgNameQn.'%|',$imgUrlQn,$question);
									}
								}
							} 

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_a, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_a = str_replace('|%'.$imgName.'%|',$imgUrl,$option_a);
                                    }
                                }
                            }

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_b, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_b = str_replace('|%'.$imgName.'%|',$imgUrl,$option_b);
                                    }
                                }
                            }

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_c, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_c = str_replace('|%'.$imgName.'%|',$imgUrl,$option_c);
                                    }
                                }
                            }

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_d, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_d = str_replace('|%'.$imgName.'%|',$imgUrl,$option_d);
                                    }
                                }
                            }

                            $arr_question_list['qid'] = $qnData['qid'];
                            $arr_question_list['nos_title'] = $qnData['nos_title'];
                            $arr_question_list['exam_name'] = $qnData['exam_name'];
                            $arr_question_list['question'] = $question;
                            $arr_question_list['option_a'] = $option_a;
                            $arr_question_list['option_b'] = $option_b;
                            $arr_question_list['option_c'] = $option_c;
                            $arr_question_list['option_d'] = $option_d;
                            $arr_question_list['ans']      = ($theory_answers_record_generated == 1 && $qnData['ans'] != NULL) ? $qnData['ans'] : "";
                            $arr_question_list['save_type'] = ($theory_answers_record_generated == 1 && $qnData['save_type'] != 'NV') ? $qnData['save_type'] : "";

                            array_push($question_list,$arr_question_list);

                            //Save the questions in tbl_theory_answers table
                            if($theory_answers_record_generated == 0) {
                                $updData['theory_start_dts'] = date('Y-m-d H:i:s');
                            }    
                        }

                        //echo "<br> count Qn Ids ".count($arrQuestionIds);
                        //echo "<br> count qn list ".count($question_list);
                        //exit;

                        if(count($question_list) == count($arrQuestionIds)) {
                            $updData['time_left_secs'] = $exam_duration_secs;
                            $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);

                            $data['status'] = true;
                            $data['rcode'] = 200;
                            $data['candidate_id'] = id_encode($candidate_id);
                            $data['unique_token'] = $token;
                            $data['exam_duration_secs'] = (int)$exam_duration_secs;
                            $data['batch_details'] = $arr_batch_details;
                            $data['question_list'] = $question_list;
                        }
                        else {
                            /*$this->db->where('tb_id', $tb_id);
                            $this->db->where('student_id', $candidate_id);
                            $this->db->delete('tbl_theory_answers');*/ 

                            $data['status'] = false;
                            $data['rcode'] = 500;
                            $data['message'] = 'Questions are not matching with the matrix. Please contact technical team';
                        }
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = 'Please contact technical team';
                    }
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getCandidateSingleQuestionsList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $qp_generated_status =  $arr_student_list->qp_generated_status;
            $theory_questions =  $arr_student_list->theory_questions;
            $theory_answers_record_generated = $arr_student_list->theory_answers_record_generated;
            $theory_start_dts = $arr_student_list->theory_start_dts;
            $time_left_secs = $arr_student_list->time_left_secs;

            $qn_no = $this->input->post('qn_no');
            
            $arr_batch_details = array();
            $exam_duration_secs = 0;
            $arr_batch_list = $this->mainModel->getBatchDetails($tb_id);
            $lid = 0;

            if($arr_batch_list != 'failure') {
                $currentDateTime = strtotime(date('Y-m-d H:i:s'));
                $assessmentStartDateTime = strtotime($arr_batch_list[0]['tb_start_date_time']);
                $assessmentEndDateTime = strtotime($arr_batch_list[0]['tb_end_date_time']);

                $arr_batch_details['trade_name'] = $arr_batch_list[0]['trade_code']." - ".$arr_batch_list[0]['trade_name'];
                $arr_batch_details['batch_id'] = $arr_batch_list[0]['batch_id'];
                $arr_batch_details['scheme'] = $arr_batch_list[0]['scheme_name'].'('.$arr_batch_list[0]['subscheme_name'].')';
                $arr_batch_details['assessment_date'] = date('d-m-Y H:i:s',$assessmentStartDateTime)." To ".date('d-m-Y H:i:s',$assessmentEndDateTime);
                $arr_batch_details['exam_duration_mins'] = convertMinutesToHoursAndMinutes($arr_batch_list[0]['exam_duration_mins']);
                $arr_batch_details['snapshot_duration_mins'] = floor($arr_batch_list[0]['exam_duration_mins'] / 10); 
                $arr_batch_details['auto_logout_mins'] = $this->config->item('auto_logout_mins');
                $arr_batch_details['lid'] = $arr_batch_list[0]['lid'];
                $lid = ($arr_batch_list[0]['lid'] > 0) ? $arr_batch_list[0]['lid'] : 0;
            }    

            if($arr_batch_list != 'failure' && $theory_start_dts == "") {
                $exam_duration_secs = convertMinutesToSeconds($arr_batch_list[0]['exam_duration_mins']);
            }
            else {
                $exam_duration_secs = $time_left_secs;
            }
            
            $question_list = array();
            $arrLangQuestions = array();
            $question_images_path = base_url().$this->config->item('question_images_path');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($qp_generated_status == 1 && $theory_questions != "") {
                    $arrQuestionIds = explode(",",$theory_questions);
                    //Get only required question no details

                    $arrQuestionId[0] = $arrQuestionIds[$qn_no-1]; //use key index

                    /*echo "<pre>";
                    print_r($arrQuestionIds);
                    echo "</pre>";*/
                    //exit;

                    if($lid > 0) {
                        $arrGetQuestionLanguageDetails = $this->mainModel->getCandidateLanguageQuestionDetails($arrQuestionId,$lid);
                        //echo "<br> str ".$this->db->last_query();exit;
                        if($arrGetQuestionLanguageDetails != false) {
                            foreach($arrGetQuestionLanguageDetails as $qnLangData) {
                                $arrLangQuestions[$qnLangData['qid']]['question'] = $qnLangData['lang_question'];
                                $arrLangQuestions[$qnLangData['qid']]['option_a'] = $qnLangData['lang_option_a'];
                                $arrLangQuestions[$qnLangData['qid']]['option_b'] = $qnLangData['lang_option_b'];
                                $arrLangQuestions[$qnLangData['qid']]['option_c'] = $qnLangData['lang_option_c'];
                                $arrLangQuestions[$qnLangData['qid']]['option_d'] = $qnLangData['lang_option_d'];
                            }    
                        }   
                    }

                    //Get the questions list and details
                    $arrGetQuestionDetails = $this->mainModel->getCandidateQuestionDetails($arrQuestionId,$candidate_id,$theory_answers_record_generated,'theory');
                    //echo "<br> str ".$this->db->last_query();exit;

                    if($arrGetQuestionDetails != false) {
                        foreach($arrGetQuestionDetails as $qnData) {
                            if(strtoupper($qnData['option_c']) == "NA") {
                                $qnData['option_c'] = "";
                            }
                            if(strtoupper($qnData['option_d']) == "NA") {
                                $qnData['option_d'] = "";
                            }

                            $question = $qnData['question'];
                            $option_a = $qnData['option_a'];
                            $option_b = $qnData['option_b'];
                            $option_c = $qnData['option_c'];
                            $option_d = $qnData['option_d'];

                            if($lid > 0) {
                                if(strtoupper($qnData['option_c']) == "NA") {
                                   $arrLangOptions[$qnData['qid']]['option_c'] = "";
                                }
                                if(strtoupper($qnData['option_d']) == "NA") {
                                    $arrLangOptions[$qnData['qid']]['option_d'] = "";
                                }
        
                                $question = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['question']."|lang|".$arrLangQuestions[$qnData['qid']]['question'] : $qnData['question'];
                                $option_a = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_a']."|lang|".$arrLangQuestions[$qnData['qid']]['option_a'] : $qnData['option_a'];
                                $option_b = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_b']."|lang|".$arrLangQuestions[$qnData['qid']]['option_b'] : $qnData['option_b'];
                                $option_c = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_c']."|lang|".$arrLangQuestions[$qnData['qid']]['option_c'] : $qnData['option_c'];
                                $option_d = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['option_d']."|lang|".$arrLangQuestions[$qnData['qid']]['option_d'] : $qnData['option_d'];
                            }
                            
                            // Use regular expression to capture text between |% and %|
							if (preg_match_all('/\|%([^%]+)%\|/', $question, $qmatches)) {
								// Extract captured text
								$arrImgNameQn = $qmatches[1];
								if(count($arrImgNameQn) > 0) {
									foreach($arrImgNameQn as $imgNameQn) {
										$imgUrlQn = "<img src='".$question_images_path.$imgNameQn."'>";
										$question = str_replace('|%'.$imgNameQn.'%|',$imgUrlQn,$question);
									}
								}
							} 

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_a, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_a = str_replace('|%'.$imgName.'%|',$imgUrl,$option_a);
                                    }
                                }
                            }

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_b, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_b = str_replace('|%'.$imgName.'%|',$imgUrl,$option_b);
                                    }
                                }
                            }

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_c, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_c = str_replace('|%'.$imgName.'%|',$imgUrl,$option_c);
                                    }
                                }
                            }

                            if (preg_match_all('/\|%([^%]+)%\|/', $option_d, $matches)) {
                                // Extract captured text
                                $arrImgName = $matches[1];
                                /*echo "<pre>";
                                print_r($arrImgName);
                                echo "</pre>";*/
                                
                                if(count($arrImgName) > 0) {
                                    foreach($arrImgName as $imgName) {
                                        $imgUrl = "<img src='".$question_images_path.$imgName."'>";
                                        $option_d = str_replace('|%'.$imgName.'%|',$imgUrl,$option_d);
                                    }
                                }
                            }

                            $arr_question_list['qid'] = $qnData['qid'];
                            $arr_question_list['nos_title'] = $qnData['nos_title'];
                            $arr_question_list['exam_name'] = $qnData['exam_name'];
                            $arr_question_list['question'] = $question;
                            $arr_question_list['option_a'] = $option_a;
                            $arr_question_list['option_b'] = $option_b;
                            $arr_question_list['option_c'] = $option_c;
                            $arr_question_list['option_d'] = $option_d;
                            $arr_question_list['ans']      = ($theory_answers_record_generated == 1 && $qnData['ans'] != NULL) ? $qnData['ans'] : "";
                            $arr_question_list['save_type'] = ($theory_answers_record_generated == 1 && $qnData['save_type'] != 'NV') ? $qnData['save_type'] : "";

                            array_push($question_list,$arr_question_list);

                            //Save the questions in tbl_theory_answers table
                            if($theory_answers_record_generated == 0) {
                                $updData['theory_start_dts'] = date('Y-m-d H:i:s');
                            }    
                        }

                        //echo "<br> count Qn Ids ".count($arrQuestionIds);
                        //echo "<br> count qn list ".count($question_list);
                        //exit;

                        //if(count($question_list) == count($arrQuestionIds)) {
                            $updData['time_left_secs'] = $exam_duration_secs;
                            $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);

                            $data['status'] = true;
                            $data['rcode'] = 200;
                            $data['candidate_id'] = id_encode($candidate_id);
                            $data['unique_token'] = $token;
                            $data['exam_duration_secs'] = (int)$exam_duration_secs;
                            $data['batch_details'] = $arr_batch_details;
                            $data['total_questions'] = count($arrQuestionIds);
                            $data['question_list'] = $question_list;
                        //}
                        /*else {
                            /*$this->db->where('tb_id', $tb_id);
                            $this->db->where('student_id', $candidate_id);
                            $this->db->delete('tbl_theory_answers');*/ 

                            /*$data['status'] = false;
                            $data['rcode'] = 500;
                            $data['message'] = 'Questions are not matching with the matrix. Please contact technical team';
                        }*/
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = 'Please contact technical team';
                    }
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveCandidateAnswer()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $qp_generated_status =  $arr_student_list->qp_generated_status;
            $theory_questions =  $arr_student_list->theory_questions;
            $theory_answers_record_generated = $arr_student_list->theory_answers_record_generated;

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($qp_generated_status == 1 && $theory_questions != "" && $theory_answers_record_generated == 1) {
                    $time_left_secs = trim($this->input->post('time_left_secs'));
                    //Save the questions in tbl_theory_answers table
                    $updData['ans'] = strtolower(trim($this->input->post('ans')));
                    $updData['save_type'] = (strtolower(trim($this->input->post('ans'))) != "") ? trim($this->input->post('save_type')) : "NA";
                    $updData['time_left_secs'] = $time_left_secs;
                    $updData['modified_dts'] = date('Y-m-d H:i:s');

                    $this->db->where('student_id', $candidate_id);
                    $this->db->where('tb_id', $tb_id);
                    $this->db->where('qid', trim($this->input->post('qid')));
                    $this->db->update('tbl_theory_answers', $updData); 

                    //Update time left in students table
                    $time_left_secs = trim($this->input->post('time_left_secs'));

                    $updStudentsData['time_left_secs'] = $time_left_secs;

                    $this->db->where('student_id', $candidate_id);
                    $this->db->where('tb_id', $tb_id);
                    $this->db->update('tbl_students', $updStudentsData);  
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Answer saved successfully';
                    //$data['query'] = $this->db->last_query();
                    //$data['updData'] = $updData;
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function submitTheoryExam()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $practical_activity_questions =  $arr_student_list->practical_activity_questions;
            $viva_questions = $arr_student_list->viva_questions;

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $time_left_secs = trim($this->input->post('time_left_secs'));

                if($practical_activity_questions != "" ) {
                    $updData['student_assessment_status'] = "Practical Activity";
                }
                else if($viva_questions != "" ) {
                    $updData['student_assessment_status'] = "Viva";
                }
                else {
                    $updData['student_assessment_status'] = "Completed";
                }
                $updData['theory_submission_dts'] = date('Y-m-d H:i:s');

                //Update time left in students table
                $time_left_secs = trim($this->input->post('time_left_secs'));

                $updData['time_left_secs'] = $time_left_secs;
                
                $this->db->where('student_id', $candidate_id);
                $this->db->where('tb_id', $tb_id);
                $this->db->update('tbl_students', $updData);  

                $data['status'] = true;
                $data['rcode'] = 200;
                $data['candidate_id'] = id_encode($candidate_id);
                $data['unique_token'] = $token;
                $data['message'] = 'Theory Assessment Submitted successfully';
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getPaletteList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            
            $palette_list = array();
            $totalAnswered = 0;
            $totalNotAnswered = 0;
            $totalNotVisited = 0;
            $totalMarkedForReview = 0;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                
                //Get the questions list and details
                $arrGetQuestionDetails = $this->mainModel->getAnswerDetails($tb_id,$candidate_id);

                if($arrGetQuestionDetails != false) {
                    foreach($arrGetQuestionDetails as $qnData) {
                        $arr_palette_list['qid'] = $qnData['qid'];
                        $arr_palette_list['ans'] = $qnData['ans'];
                        $arr_palette_list['save_type'] = $qnData['save_type'];

                        if($qnData['save_type'] == 'Save') {
                            $totalAnswered++;
                        }
                        if($qnData['save_type'] == 'NA') {
                            $totalNotAnswered++;
                        }
                        if($qnData['save_type'] == 'NV') {
                            $totalNotVisited++;
                        }
                        if($qnData['save_type'] == 'Review') {
                            $totalMarkedForReview++;
                        }

                        array_push($palette_list,$arr_palette_list);

                    }

                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['palette_list'] = $palette_list;
                    $data['totalAnswered'] = $totalAnswered;
                    $data['totalNotAnswered'] = $totalNotAnswered;
                    $data['totalNotVisited'] = $totalNotVisited;
                    $data['totalMarkedForReview'] = $totalMarkedForReview;
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getQuestionDetails()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $qid = $this->input->post('qid');
            
            $question_list = array();
                        
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                
                //Get the questions list and details
                $arrGetQuestionDetails = $this->mainModel->getQuestionAnswerDetails($tb_id,$candidate_id,$qid);

                if($arrGetQuestionDetails != false) {
                    foreach($arrGetQuestionDetails as $qnData) {
                        $arr_question_list['qid']           = $qnData['qid'];
                        $arr_question_list['question']      = ($qnData['lang_question'] != "") ? $qnData['question']."|lang|".$qnData['lang_question'] : $qnData['question'];
                        $arr_question_list['option_a']      = ($qnData['lang_option_a'] != "") ? $qnData['option_a']."|lang|".$qnData['lang_option_a'] : $qnData['option_a'];
                        $arr_question_list['option_b']      = ($qnData['lang_option_b'] != "") ? $qnData['option_b']."|lang|".$qnData['lang_option_b'] : $qnData['option_b'];
                        $arr_question_list['option_c']      = ($qnData['lang_option_c'] != "") ? $qnData['option_c']."|lang|".$qnData['lang_option_c'] : $qnData['option_c'];
                        $arr_question_list['option_d']      = ($qnData['lang_option_d'] != "") ? $qnData['option_d']."|lang|".$qnData['lang_option_d'] : $qnData['option_d'];
                        $arr_question_list['ans']           = ($qnData['ans'] != "") ? $qnData['ans'] : "";
                        $arr_question_list['save_type']     = ($qnData['save_type'] != 'NV') ? $qnData['save_type'] : "";

                        array_push($question_list,$arr_question_list);
                    }

                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['question_list'] = $question_list;
                }
                
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveCandidateSnapshot()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];
            
            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('snapshot_photo') != "") {
                    $enrollmentNumber = $arr_student_list->enrollment_number;
                    $candiateName = $arr_student_list->student_name;
                    $getGeoAddress = trim($this->input->post('geo_address'));
                    $geoAddress = ""; //$getGeoAddress;
                    $arr_geo_address = explode(",",$getGeoAddress); 
                    $arr_count = count($arr_geo_address); 
                    
                    // Counter to keep track of words
                    $count = 0;
                    
                    // Loop through the words
                    foreach ($arr_geo_address as $address) {
                        // Display the address
                        $geoAddress .= $address.",";
                        
                        // Increment the counter
                        $count++;
                        
                        // If four words have been displayed, start a new line
                        if ($count % 4 == 0) {
                            $geoAddress .="\n";
                        }
                    }
                    
                    // If there are remaining words less than four, add them to the last line
                    if ($count % 4 != 0) {
                        $geoAddress .="\n";
                    }
                    $dateTime = date('d-m-Y h:i A')." GMT +05:30";
                    
                    //Create WaterMark
                    $watermarkValue = $enrollmentNumber."-".$candiateName."\nLat ".$this->input->post('lat').",Long ".$this->input->post('long')."\n".$geoAddress.$dateTime;

                    //Save the snapshots
                    $arrInsert['tb_id'] = $tb_id;
                    $arrInsert['student_id'] = $candidate_id;
                    $arrInsert['snapshot_image'] = $this->uploadBase64Image($this->input->post('snapshot_photo'), 'student_snapshot', $enrollmentNumber.'-snapshot-'.date('YmdHis'),$watermarkValue,1);
                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_student_snapshots', $arrInsert);
                    
                    if($this->input->post('screen_snapshot_photo') != "") {
                        //Save the snapshots
                        $arrInsert['tb_id'] = $tb_id;
                        $arrInsert['student_id'] = $candidate_id;
                        $arrInsert['snapshot_image'] = $this->uploadBase64Image($this->input->post('screen_snapshot_photo'), 'student_snapshot', $enrollmentNumber.'-screen-snapshot-'.date('YmdHis'),$watermarkValue,2);
                        $arrInsert['created_dts'] = date('Y-m-d H:i:s');
    
                        $this->db->insert('tbl_student_snapshots', $arrInsert);
                    }    

                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['candidate_id'] = id_encode($candidate_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Snapshot saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Snapshot data is empty';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getCandidatePracticalActivityQuestionsList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $qp_generated_status =  $arr_student_list->qp_generated_status;
            $practical_activity_questions =  $arr_student_list->practical_activity_questions;
            $practicalactivity_answers_record_generated = $arr_student_list->practicalactivity_answers_record_generated;
            
            $question_list = array();
            $practical_activity_instructions = "";
            $lid = 0;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($qp_generated_status == 1 && $practical_activity_questions != "") {
                    $arr_batch_list = $this->mainModel->getBatchDetails($tb_id);
                    if($arr_batch_list != false) {
                        $practical_activity_instructions = $arr_batch_list[0]['practical_activity_instructions'];
                        $lid = ($arr_batch_list[0]['lid'] > 0) ? $arr_batch_list[0]['lid'] : 0;
                    } 

                    $arrQuestionIds = explode(",",$practical_activity_questions);
                    //Get the questions list and details
                    $arrGetQuestionDetails = $this->mainModel->getQuestionDetails($arrQuestionIds,$candidate_id,$practicalactivity_answers_record_generated,'practical_activity',$lid);
                    //echo "<br> str ".$this->db->last_query();exit;

                    if($arrGetQuestionDetails != false) {
                        foreach($arrGetQuestionDetails as $qnData) {
                            $ans = "Not Uploaded";
                            if($practicalactivity_answers_record_generated == 1) {
                                $ans = ($qnData['video_file'] != "") ? "Uploaded" : "Not Uploaded";
                            }

                            $arr_question_list['qid'] = $qnData['qid'];
                            $arr_question_list['nos_title'] = $qnData['nos_title'];
                            $arr_question_list['exam_name'] = $qnData['exam_name'];
                            $arr_question_list['question'] = ($lid > 0 && $qnData['lang_question'] != "") ? $qnData['question']."|lang|".$qnData['lang_question'] : $qnData['question'];
                            $arr_question_list['ans']      = $ans;
                            
                            array_push($question_list,$arr_question_list);

                            //Save the questions in tbl_practical_activity_answers table
                            /*if($practicalactivity_answers_record_generated == 0) {
                                $arrInsert['tb_id'] = $tb_id;
                                $arrInsert['student_id'] = $candidate_id;
                                $arrInsert['qid'] = $qnData['qid'];
                                $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                                $this->db->insert('tbl_practical_activity_answers', $arrInsert);
                            } */   
                        }

                        //echo "<br> count Qn Ids ".count($arrQuestionIds);
                        //echo "<br> count qn list ".count($question_list);
                        //exit;

                        /*if($practicalactivity_answers_record_generated == 0) {
                            if(count($question_list) == count($arrQuestionIds)) {
                                $updData['practicalactivity_answers_record_generated'] = 1;
                                $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);
    
                                $data['status'] = true;
                                $data['rcode'] = 200;
                                $data['candidate_id'] = id_encode($candidate_id);
                                $data['unique_token'] = $token;
                                $data['question_list'] = $question_list;
                                $data['instructions'] = $practical_activity_instructions;
                            }
                            else {
                                $this->db->where('tb_id', $tb_id);
                                $this->db->where('student_id', $candidate_id);
                                $this->db->delete('tbl_practical_activity_answers');
    
                                $data['status'] = false;
                                $data['rcode'] = 500;
                                $data['message'] = 'Questions are not matching with the matrix. Please contact technical team';
                            }
                        }
                        else {
                            $data['status'] = true;
                            $data['rcode'] = 200;
                            $data['candidate_id'] = id_encode($candidate_id);
                            $data['unique_token'] = $token;
                            $data['question_list'] = $question_list;
                            $data['instructions'] = $practical_activity_instructions;
                        }*/
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['candidate_id'] = id_encode($candidate_id);
                        $data['unique_token'] = $token;
                        $data['question_list'] = $question_list;
                        $data['instructions'] = $practical_activity_instructions;
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = 'Please contact technical team';
                    }
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getCandidateVivaQuestionsList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $candidate_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            $tb_id =  $arr_student_list->tb_id;
            $qp_generated_status =  $arr_student_list->qp_generated_status;
            $viva_questions =  $arr_student_list->viva_questions;
            $viva_answers_record_generated = $arr_student_list->viva_answers_record_generated;
            
            $question_list = array();
            $viva_instructions = "";
            $lid = 0;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($qp_generated_status == 1 && $viva_questions != "") {
                    $arr_batch_list = $this->mainModel->getBatchDetails($tb_id);
                    if($arr_batch_list != false) {
                        $viva_instructions = $arr_batch_list[0]['viva_instructions'];
                        $lid = ($arr_batch_list[0]['lid'] > 0) ? $arr_batch_list[0]['lid'] : 0;
                    }    

                    $arrQuestionIds = explode(",",$viva_questions);
                    //Get the questions list and details
                    $arrGetQuestionDetails = $this->mainModel->getQuestionDetails($arrQuestionIds,$candidate_id,$viva_answers_record_generated,'viva',$lid);

                    if($arrGetQuestionDetails != false) {
                        foreach($arrGetQuestionDetails as $qnData) {
                            $ans = "Not Uploaded";
                            if($viva_answers_record_generated == 1) {
                                $ans = ($qnData['video_file'] != "") ? "Uploaded" : "Not Uploaded";
                            }

                            $arr_question_list['qid'] = $qnData['qid'];
                            $arr_question_list['nos_title'] = $qnData['nos_title'];
                            $arr_question_list['exam_name'] = $qnData['exam_name'];
                            $arr_question_list['question'] = ($lid > 0 && $qnData['lang_question'] != "") ? $qnData['question']."|lang|".$qnData['lang_question'] : $qnData['question'];
                            $arr_question_list['ans']      = $ans;
                            
                            array_push($question_list,$arr_question_list);

                            //Save the questions in tbl_viva_answers table
                            /*if($viva_answers_record_generated == 0) {
                                $arrInsert['tb_id'] = $tb_id;
                                $arrInsert['student_id'] = $candidate_id;
                                $arrInsert['qid'] = $qnData['qid'];
                                $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                                $this->db->insert('tbl_viva_answers', $arrInsert);
                            }*/    
                        }

                        //echo "<br> count Qn Ids ".count($arrQuestionIds);
                        //echo "<br> count qn list ".count($question_list);
                        //exit;

                        /*if($viva_answers_record_generated == 1) {
                            if(count($question_list) == count($arrQuestionIds)) {
                                $updData['viva_answers_record_generated'] = 1;
                                $this->mainModel->updateData('student_id', $candidate_id, 'tbl_students', $updData);

                                $data['status'] = true;
                                $data['rcode'] = 200;
                                $data['candidate_id'] = id_encode($candidate_id);
                                $data['unique_token'] = $token;
                                $data['question_list'] = $question_list;
                                $data['instructions'] = $viva_instructions;
                            }
                            else {
                                $this->db->where('tb_id', $tb_id);
                                $this->db->where('student_id', $candidate_id);
                                $this->db->delete('tbl_viva_answers');

                                $data['status'] = false;
                                $data['rcode'] = 500;
                                $data['message'] = 'Questions are not matching with the matrix. Please contact technical team';
                            }
                        }
                        else {
                            $data['status'] = true;
                            $data['rcode'] = 200;
                            $data['candidate_id'] = id_encode($candidate_id);
                            $data['unique_token'] = $token;
                            $data['question_list'] = $question_list;
                            $data['instructions'] = $viva_instructions;
                        } */
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['candidate_id'] = id_encode($candidate_id);
                        $data['unique_token'] = $token;
                        $data['question_list'] = $question_list;
                        $data['instructions'] = $viva_instructions;
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = 'Please contact technical team';
                    }
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function getCandidatePracticalActivityVivaQuestionsList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $student_id = $userRes['candidate_id'];
            $arr_student_list = $userRes['candidate_details'];

            $token =  $arr_student_list->unique_token;
            
            $qp_generated_status = 0;
            $question_list = array();
            $viva_question_list = array();
            $practical_activity_instructions = "";
            $viva_instructions = "";
            $practical_activity_video_uploaded = 'Not Uploaded'; 
            $viva_video_uploaded = 'Not Uploaded'; 
            $practical_activity_video_file = "";
            $viva_video_file = "";
            
            $arr_student = $this->mainModel->getCandidateBatchDetails($student_id);
            if($arr_student != false) {
                $student_id = $arr_student[0]['student_id'];
                $tb_id =  $arr_student[0]['tb_id'];
                $qp_generated_status =  $arr_student[0]['qp_generated_status'];
                $lid = $arr_student[0]['lid'];
                $practical_activity_questions =  $arr_student[0]['practical_activity_questions'];
                $practical_activity_instructions  = $arr_student[0]['practical_activity_instructions'];
                $practicalactivity_answers_record_generated = $arr_student[0]['practicalactivity_answers_record_generated'];
                $viva_questions =  $arr_student[0]['viva_questions'];
                $viva_instructions  = $arr_student[0]['viva_instructions'];
                $viva_answers_record_generated = $arr_student[0]['viva_answers_record_generated'];
                $practical_activity_video_uploaded = ($arr_student[0]['practicalactivity_video_file'] == "") ? 'Not Uploaded' : 'Uploaded'; 
                $viva_video_uploaded = ($arr_student[0]['viva_video_file'] == "") ? 'Not Uploaded' : 'Uploaded';
                $practical_activity_video_file = ($arr_student[0]['practicalactivity_video_file'] != "") ? base_url().$this->config->item('student_assessment_videos_path').$arr_student[0]['practicalactivity_video_file'] : "";
                $viva_video_file = ($arr_student[0]['viva_video_file'] != "") ? base_url().$this->config->item('student_assessment_videos_path').$arr_student[0]['viva_video_file'] : "";
            }
            
            $tokenRes = $this->checkToken($token);
            $error = 0;
            $practicalActivityData = array();
            $vivaData = array();
            $arrPracticalQuestionIds = array();
            $arrVivaQuestionIds = array();

            if ($tokenRes['status'] == true) {
                if($qp_generated_status == 1) {
                    if($practical_activity_questions != "") {
                        $arrPracticalQuestionIds = explode(",",$practical_activity_questions);
                    }
                    if($viva_questions != "") {
                        $arrVivaQuestionIds = explode(",",$viva_questions);
                    }

                    $arrQuestionIds = array_merge($arrPracticalQuestionIds,$arrVivaQuestionIds);
                    $arrLangQuestions = array();

                    if($lid > 0) {
                        $arrGetQuestionLanguageDetails = $this->mainModel->getCandidateLanguageQuestionDetails($arrQuestionIds,$lid);
                        if($arrGetQuestionLanguageDetails != false) {
                            foreach($arrGetQuestionLanguageDetails as $qnLangData) {
                                $arrLangQuestions[$qnLangData['qid']] = $qnLangData['lang_question'];
                            }    
                        }   
                    }

                    $arrPracticalActivityDetails = array();
                    $arrVivaDetails = array();
                    $errMessage = "";

                    if($practical_activity_questions != "") {
                        
                        //Get the questions list and details
                        $arrGetQuestionDetails = $this->mainModel->getCandidateQuestionDetails($arrPracticalQuestionIds,$student_id,$practicalactivity_answers_record_generated,'practical_activity');
                        //echo "<br> str ".$this->db->last_query();exit;

                        if($arrGetQuestionDetails != false) {
                            foreach($arrGetQuestionDetails as $qnData) {
                                $ans = "Not Uploaded";
                                $marks = 0;
                                if($practicalactivity_answers_record_generated == 1) {
                                    $ans = ($qnData['video_file'] != "") ? "Uploaded" : "Not Uploaded";
                                    $marks = ($qnData['practical_activity_marks'] > 0) ? $qnData['practical_activity_marks'] : 0;
                                }

                                $arr_question_list['qid'] = $qnData['qid'];
                                $arr_question_list['nos_title'] = $qnData['nos_title'];
                                $arr_question_list['exam_name'] = $qnData['exam_name'];
                                $arr_question_list['question'] = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['question']."|lang|".$arrLangQuestions[$qnData['qid']] : $qnData['question'];
                                $arr_question_list['ans']      = $ans;
                                $arr_question_list['marks']    = (int)$marks;
                                $arr_question_list['max_marks'] = (int)$qnData['marks'];
                                
                                array_push($question_list,$arr_question_list);

                                //Save the questions in tbl_practical_activity_answers table
                                /*if($practicalactivity_answers_record_generated == 0) {
                                    $arrInsert['tb_id'] = $tb_id;
                                    $arrInsert['student_id'] = $student_id;
                                    $arrInsert['qid'] = $qnData['qid'];
                                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                                    $this->db->insert('tbl_practical_activity_answers', $arrInsert);
                                }   */ 
                            }

                            //echo "<br> count Qn Ids ".count($arrQuestionIds);
                            //echo "<br> count qn list ".count($question_list);
                            //exit;

                            /*if($practicalactivity_answers_record_generated == 0) {
                                if(count($question_list) == count($arrPracticalQuestionIds)) {
                                    $updData['practicalactivity_answers_record_generated'] = 1;
                                    $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);

                                    $practicalActivityData['practical_activity_question_list'] = $question_list;
                                    $practicalActivityData['practical_activity_instructions'] = $practical_activity_instructions;
                                }
                                else {
                                    $this->db->where('tb_id', $tb_id);
                                    $this->db->where('student_id', $student_id);
                                    $this->db->delete('tbl_practical_activity_answers');

                                    $errMessage .= 'Practical Activity Questions are not matching with the matrix. Please contact technical team';
                                }
                            }
                            else {
                                $practicalActivityData['practical_activity_question_list'] = $question_list;
                                $practicalActivityData['practical_activity_instructions'] = $practical_activity_instructions;
                            } */
                            
                            $practicalActivityData['practical_activity_question_list'] = $question_list;
                            $practicalActivityData['practical_activity_instructions'] = $practical_activity_instructions;
                            
                            array_push($arrPracticalActivityDetails,$practicalActivityData);
                        }
                        else {
                            $errMessage .= 'Please contact technical team';
                        }
                    }
                    if($viva_questions != "") {
                        
                        //Get the questions list and details
                        $arrGetVivaQuestionDetails = $this->mainModel->getCandidateQuestionDetails($arrVivaQuestionIds,$student_id,$viva_answers_record_generated,'viva');
                        //echo "<br> str ".$this->db->last_query();exit;

                        if($arrGetVivaQuestionDetails != false) {
                            foreach($arrGetVivaQuestionDetails as $qnData) {
                                $ans = "Not Uploaded";
                                $marks = 0;
                                if($viva_answers_record_generated == 1) {
                                    $ans = ($qnData['video_file'] != "") ? "Uploaded" : "Not Uploaded";
                                    $marks = ($qnData['viva_marks'] > 0) ? $qnData['viva_marks'] : 0;
                                }

                                $arr_question_list['qid'] = $qnData['qid'];
                                $arr_question_list['nos_title'] = $qnData['nos_title'];
                                $arr_question_list['exam_name'] = $qnData['exam_name'];
                                $arr_question_list['question'] = (array_key_exists($qnData['qid'],$arrLangQuestions)) ? $qnData['question']."|lang|".$arrLangQuestions[$qnData['qid']] : $qnData['question'];
                                $arr_question_list['max_marks'] = (int)$qnData['marks'];
                                $arr_question_list['ans']      = $ans;
                                $arr_question_list['marks']    = (int)$marks;
                                
                                array_push($viva_question_list,$arr_question_list);

                                //Save the questions in tbl_viva_answers table
                                if($viva_answers_record_generated == 0) {
                                    $arrInsert['tb_id'] = $tb_id;
                                    $arrInsert['student_id'] = $student_id;
                                    $arrInsert['qid'] = $qnData['qid'];
                                    $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                                    $this->db->insert('tbl_viva_answers', $arrInsert);
                                }    
                            }

                            //echo "<br> count Qn Ids ".count($arrQuestionIds);
                            //echo "<br> count qn list ".count($question_list);
                            //exit;

                            /*if($viva_answers_record_generated == 0) {
                                if(count($viva_question_list) == count($arrVivaQuestionIds)) {
                                    $updData['viva_answers_record_generated'] = 1;
                                    $this->mainModel->updateData('student_id', $student_id, 'tbl_students', $updData);

                                    $vivaData['viva_question_list'] = $viva_question_list;
                                    $vivaData['viva_instructions'] = $viva_instructions;
                                }
                                else {
                                    $this->db->where('tb_id', $tb_id);
                                    $this->db->where('student_id', $student_id);
                                    $this->db->delete('tbl_viva_answers');

                                    $errMessage .= 'Viva Questions are not matching with the matrix. Please contact technical team';
                                }
                            }
                            else {
                                $vivaData['viva_question_list'] = $viva_question_list;
                                $vivaData['viva_instructions'] = $viva_instructions;
                            } */
                            
                            $vivaData['viva_question_list'] = $viva_question_list;
                            $vivaData['viva_instructions'] = $viva_instructions;
                            
                            array_push($arrVivaDetails,$vivaData);
                        }
                        else {
                            $errMessage .= 'Please contact technical team';
                        }
                    }
                    if($errMessage == "") {
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['candidate_id'] = id_encode($student_id);
                        $data['unique_token'] = $token;
                        $data['practical_activity'] = $arrPracticalActivityDetails;
                        $data['viva'] = $arrVivaDetails;
                        $data['practical_activity_video_uploaded'] = $practical_activity_video_uploaded;
                        $data['viva_video_uploaded'] = $viva_video_uploaded;
                        $data['practical_activity_video_file'] = $practical_activity_video_file;
                        $data['viva_video_file'] = $viva_video_file;
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = $errMessage;
                    }
                }  
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Questions are not generated. Please contact technical team';
                }  
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    private function validateAndSaveBase64Image($base64_image, $destfolder, $new_name)
    {
        // Maximum upload size (5MB)
        $max_size = 5 * 1024 * 1024;

        // Allowed image MIME types
        $allowed_mimes = [
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];

        if (empty($base64_image)) {
            return false;
        }

        // Remove Data URI prefix if present
        if (strpos($base64_image, ',') !== false) {
            $base64_image = explode(',', $base64_image)[1];
        }

        // Decode Base64
        $image_data = base64_decode($base64_image, true);

        if ($image_data === false) {
            log_message('error', 'Invalid Base64 image data');
            return false;
        }

        // File size validation
        if (strlen($image_data) > $max_size) {
            log_message('error', 'Image exceeds maximum allowed size');
            return false;
        }

        $upload_dir = FCPATH . 'uploads/' . $destfolder . '/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Temp file for validation
        $tmp_file = tempnam(sys_get_temp_dir(), 'IMG_');

        if (!$tmp_file) {
            log_message('error', 'Unable to create temp file');
            return false;
        }

        file_put_contents($tmp_file, $image_data);

        // Validate image
        $image_info = @getimagesize($tmp_file);

        if ($image_info === false) {
            unlink($tmp_file);
            log_message('error', 'Invalid image file');
            return false;
        }

        // Validate MIME
        $mime_type = $image_info['mime'];

        if (!in_array($mime_type, $allowed_mimes)) {
            unlink($tmp_file);
            log_message('error', 'Invalid MIME type: ' . $mime_type);
            return false;
        }

        // Extension
        switch ($mime_type) {
            case 'image/png':
                $extension = 'png';
                break;

            case 'image/jpeg':
            case 'image/jpg':
            default:
                $extension = 'jpeg';
                break;
        }

        // Safe filename
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $new_name);

        $file_name = $safe_name . '.' . $extension;

        $file_path = $upload_dir . $file_name;

        // Save file
        if (!file_put_contents($file_path, $image_data)) {
            unlink($tmp_file);
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | ClamAV Scan
        |--------------------------------------------------------------------------
        */
        /*if (function_exists('shell_exec')) {

            $scan_result = @shell_exec(
                'clamscan --no-summary ' . escapeshellarg($file_path) . ' 2>&1'
            );

            if (!empty($scan_result) && strpos($scan_result, 'FOUND') !== false) {

                @unlink($file_path);
                @unlink($tmp_file);

                log_message('error', 'Virus detected: ' . $scan_result);

                return false;
            }
        }

        unlink($tmp_file);*/

        return [
            'file_name' => $file_name,
            'file_path' => $file_path,
            'mime_type' => $mime_type
        ];
    }
}
