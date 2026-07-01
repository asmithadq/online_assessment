<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once ('vendor/autoload.php');

class Api_assessor extends MY_Controller
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
        $assessor_id = "";
        if ($this->input->post('assessor_id') != "") {
            if ($this->input->post('assessor_id') == '0') {
                $isValidated = true;
            } else {
                $assessor_id = id_decode($this->input->post('assessor_id'));
                //echo "<br> userid ".$assessor_id;exit;
                $checkUser = $this->mainModel->getAllDataByVal('tbl_assessor', array('assessor_id' => $assessor_id));
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
            $data['assessor_id'] = $assessor_id;
            $data['assessor_details'] = $userObj;
            return $data;
        } else {
            $data['status'] = false;
            $data['rcode'] = 500;
            $data['message'] = "Not A Valid Assessor";
            return $data;
        }
    }

    public function uploadBase64Image($base64_image, $destfolder, $new_name, $watermarkValue = "")
    {
        $this->load->library('image_lib');

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

        // Create thumbnail
        $thumb_dir = FCPATH . 'uploads/' . $destfolder . '/thumbs/';

        if (!is_dir($thumb_dir)) {
            mkdir($thumb_dir, 0755, true);
        }

        $thumb_path = $thumb_dir . $file_name;

        copy($file_path, $thumb_path);

        $config = [
            'image_library'  => 'gd2',
            'source_image'   => $thumb_path,
            'maintain_ratio' => TRUE,
            'width'          => 300,
            'height'         => 200
        ];

        $this->image_lib->initialize($config);

        if (!$this->image_lib->resize()) {
            log_message('error', $this->image_lib->display_errors());
        }

        $this->image_lib->clear();

        if (!empty($watermarkValue)) {
            $this->watermarkImage($watermarkValue, $file_path);
        }

        return $file_name;
    }

    public function uploadBase64ImageAndWatermark($base64_image, $destfolder, $new_name, $watermarkValue = "")
    {
        $this->load->library('image_lib');

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
            $this->watermarkImage($watermarkValue, $file_path);
        }

        return file_exists($file_path)
            ? $file_name
            : 'default-image.png';
    }
    
    public function watermarkImage($watermarkValue,$file_path)
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
        $overlay_height = 700; // Height of the overlay (adjust as needed)
        $overlay = imagecreatetruecolor($image_width, $overlay_height);

        // Make the overlay semi-transparent
        $overlay_color = imagecolorallocatealpha($overlay, 0, 0, 0, 75); // Black with 75/127 transparency
        imagefill($overlay, 0, 0, $overlay_color);

        // Add text to the overlay
        $font_path = './system/fonts/texb.ttf'; // Path to the TTF font
        $font_size = 60;
        $font_color = imagecolorallocate($overlay, 255, 255, 255); // White color
        $lines = explode("\n", $watermarkValue);

        // Position settings
        $padding = 20; 
        $top_space = 30; // Space at the top of the overlay
        $line_spacing = 50; // Space between lines 
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
        
        // Position overlay at the bottom right,Merge the overlay with the original image
        $overlay_x_position = $image_width - $overlay_width; // Position overlay at the right
        $overlay_y_position = $image_height - $overlay_height; // Position overlay at the bottom
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
    
    public function uploadBase64ImageNoThumb($base64_image, $destfolder, $new_name, $watermarkValue = "")
    {
        $this->load->library('image_lib');

        $upload = $this->validateAndSaveBase64Image(
            $base64_image,
            $destfolder,
            $new_name
        );

        if (!$upload) {
            return 'default-image.png';
        }

        return $upload['file_name'];
    }

    public function uploadFile($key, $destfolder, $new_name)
    {
        $upload = $this->validateAndSaveUploadedFile(
            $key,
            $destfolder,
            $new_name
        );

        if (!$upload) {
            return 'default-image.png';
        }

        return $upload['file_name'];
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

    private function validateAndSaveUploadedFile($file_key, $destfolder, $new_name)
    {
        if (
            !isset($_FILES[$file_key]) ||
            $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK
        ) {
            return false;
        }

        // Maximum upload size (5MB)
        $max_size = 5 * 1024 * 1024;

        // Allowed MIME Types
        $allowed_mimes = [
            'image/jpeg',
            'image/png',
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
            'application/vnd.ms-excel', // xls
            'application/msword', // doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // docx
        ];

        $tmp_file = $_FILES[$file_key]['tmp_name'];

        // Size validation
        if ($_FILES[$file_key]['size'] > $max_size) {
            log_message('error', 'Uploaded file exceeds maximum size');
            return false;
        }

        // MIME validation
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $tmp_file);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_mimes)) {
            log_message('error', 'Invalid MIME Type: ' . $mime_type);
            return false;
        }

        // Safe filename
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $new_name);

        $extension = strtolower(
            pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION)
        );

        $file_name = $safe_name . '.' . $extension;

        $upload_dir = FCPATH . 'uploads/' . $destfolder . '/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($tmp_file, $file_path)) {
            log_message('error', 'Failed to move uploaded file');
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Optional ClamAV Scan
        |--------------------------------------------------------------------------
        */
        /*if (function_exists('shell_exec')) {

            $scan_result = @shell_exec(
                'clamscan --no-summary ' .
                escapeshellarg($file_path) .
                ' 2>&1'
            );

            if (!empty($scan_result) &&
                strpos($scan_result, 'FOUND') !== false) {

                @unlink($file_path);

                log_message(
                    'error',
                    'Virus detected: ' . $scan_result
                );

                return false;
            }
        }*/

        return [
            'file_name' => $file_name,
            'file_path' => $file_path,
            'mime_type' => $mime_type
        ];
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
        
        $assessor_code = $this->input->post('assessor_code');
        $password = $this->input->post('password');
        $new_device_id = trim($this->input->post('device_id'));
        
        //if ($userRes['status'] == true) {
        
            //Get assessor user details
            $arr_user = $this->mainModel->getAllRecords('tbl_assessor',array("assessor_code" => $assessor_code,"assessor_password" => $password, "assessor_status" => "Active"));
            //echo "<br> str ".$this->db->last_query();exit; 
            
            if($arr_user == false){ //Invalid User
                $data['status'] = false;
                $data['rcode'] = 500;
                $data['message'] = 'Invalid Assessor Login';
            }
    		else{  
                $assessor_id = $arr_user[0]['assessor_id'];
                $unique_token = $arr_user[0]['unique_token'];
                $device_id = $arr_user[0]['device_id'];
                $multiple_login_error = 0;
                
                //Validate device_id to disallow multiple login
                if ($device_id != "") {
                    if ($new_device_id != $device_id) {
                        $multiple_login_error++;
                    }
                } else {
                    $updData['device_id'] = $new_device_id;
                }
                
                if ($multiple_login_error > 0) {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Invalid Login.You have already logged in to another device!';
                }
                else {
                    //Update logged_in_dts
                    if($arr_user[0]['logged_in_dts'] == "") {
                        $updData['logged_in_dts'] = date('Y-m-d H:i:s');
                    }
                    if($unique_token == "") {
                        $str_token = $arr_user[0]['assessor_code'].date('Ymdhis');
                        $unique_token =  md5($str_token);
                        $updData['unique_token'] = $unique_token;
                    }
                    $updData['logged_in_status'] = 1;
                    $query = $this->mainModel->updateData('assessor_id', $assessor_id, 'tbl_assessor', $updData);
    
        		    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $unique_token;
                    $data['assessor_status'] = $arr_user[0]['assessor_status'];
                    $data['profile_updated'] = $arr_user[0]['profile_updated'];
                    $data['auto_logout_secs'] = $this->config->item('assessor_auto_logout_mins') * 60;
                    $data['message'] = 'Assessor logged in successfully';
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
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $updData['logged_in_status'] = 0;
                $updData['device_id'] = "";
                $updData['logged_out_dts'] = date('Y-m-d H:i:s');
                $query = $this->mainModel->updateData('assessor_id', $assessor_id, 'tbl_assessor', $updData);
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = "";
                $data['unique_token'] = "";
                $data['message'] = 'Assessor logged out successfully';
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getAssessorProfile(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
            
                $arrAssessordata = $this->mainModel->getAssessorProfile($assessor_id);
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['user_details'] = $arrAssessordata;
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }

    public function updateAssessorProfile(){
        $userRes = $this->checkUserId();
        /*echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";
        exit;*/
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            $assessor_code = $arr_assessor_list->assessor_code;

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('name') != "" && $this->input->post('mobile') != "" && $this->input->post('email') != "" && $this->input->post('gender') != "" 
                    && $this->input->post('address') != "" && $this->input->post('city') != "" && $this->input->post('pincode') != "" 
                    && $this->input->post('aadhar_number') != "" && $this->input->post('pan_no') != "" && $this->input->post('state_id') != "" && $this->input->post('district_id') != ""){
                    //&& isset($_FILES['assessor_resume'])

                    //Update Assessor
                    $updData['assessor_name'] = $this->input->post('name');
                    $updData['assessor_email'] = $this->input->post('email');
                    $updData['assessor_mobile'] = $this->input->post('mobile');
                    $updData['assessor_gender'] = $this->input->post('gender');
                    $updData['address'] = $this->input->post('address');
                    $updData['city'] = $this->input->post('city');
                    $updData['pincode'] = $this->input->post('pincode');
                    $updData['state_id'] = $this->input->post('state_id');
                    $updData['district_id'] = $this->input->post('district_id');
                    $updData['aadhar_number'] = $this->input->post('aadhar_number');
                    $updData['pan_no'] = $this->input->post('pan_no');;
                    $updData['profile_updated'] = 1;
                    
                    $query = $this->mainModel->updateData('assessor_id', $assessor_id, 'tbl_assessor', $updData);
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
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

    public function saveAssessorProfileImage()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('image') != "" && $this->input->post('image_type') != "") {
                    $assessor_code = $arr_assessor_list->assessor_code;
                    $file = "";
                    $thumb_file = "";

                    if($this->input->post('image_type') == 'photo') {
                        $updData['assessor_photo'] = $this->uploadBase64Image($this->input->post('image'), 'assessors', $assessor_code.'-'.date('dmYHis').'-assessor-photo');
                        if($arr_assessor_list->assessor_photo != "" && $updData['assessor_photo'] != "") {
                            $file = $this->config->item('assessors_images_path').$arr_assessor_list->assessor_photo;
                            $thumb_file = $this->config->item('assessors_images_thumbs_path').$arr_assessor_list->assessor_photo;
                        }
                    }
                    else if($this->input->post('image_type') == 'pan_no') {
                        $updData['pan_filename'] = $this->uploadBase64Image($this->input->post('image'), 'assessors_pan', $assessor_code.'-'.date('dmYHis').'-assessor-pan');
                        if($arr_assessor_list->pan_filename != "" && $updData['pan_filename'] != "") {
                            $file = $this->config->item('assessors_pan_path').$arr_assessor_list->pan_filename;
                            $thumb_file = $this->config->item('assessors_pan_thumbs_path').$arr_assessor_list->pan_filename;
                        }
                    }
                    else if($this->input->post('image_type') == 'aadhaar_front') {
                        $updData['aadhar_front_filename'] = $this->uploadBase64Image($this->input->post('image'), 'assessors_aadhaar', $assessor_code.'-'.date('dmYHis').'-assessor-aadhaar-front');
                        if($arr_assessor_list->aadhar_front_filename != "" && $updData['aadhar_front_filename'] != "") {
                            $file = $this->config->item('assessors_aadhaar_path').$arr_assessor_list->aadhar_front_filename;
                            $thumb_file = $this->config->item('assessors_aadhaar_thumbs_path').$arr_assessor_list->aadhar_front_filename;
                        }
                    }
                    else if($this->input->post('image_type') == 'aadhaar_back') {
                        $updData['aadhar_back_filename'] = $this->uploadBase64Image($this->input->post('image'), 'assessors_aadhaar', $assessor_code.'-'.date('dmYHis').'-assessor-aadhaar-back');
                        if($arr_assessor_list->aadhar_back_filename != "" && $updData['aadhar_back_filename'] != "") {
                            $file = $this->config->item('assessors_aadhaar_path').$arr_assessor_list->aadhar_back_filename;
                            $thumb_file = $this->config->item('assessors_aadhaar_thumbs_path').$arr_assessor_list->aadhar_back_filename;
                        }
                    }                    

                    if($file != "" && $thumb_file != "") {
                        // Check if the file exists before attempting to delete it
                        if (file_exists($file)) {
                            // Attempt to delete the file
                            unlink($file);
                        }
                        if (file_exists($thumb_file)) {
                            unlink($thumb_file);
                        }
                    }
                    
                    $query = $this->mainModel->updateData('assessor_id ', $assessor_id, 'tbl_assessor', $updData);
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
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

    public function dashboard()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            $assessor_photo_thumbs_path = base_url().$this->config->item('assessors_images_thumbs_path');
            $arr_assessor_list->assessor_photo = $assessor_photo_thumbs_path.$arr_assessor_list->assessor_photo;

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                //Get Settings Data
		        $arr_settings_list = $this->mainModel->getDataResult('tbl_general_settings', 'id', 'ASC', 1);

                $current_app_version = $arr_settings_list[0]['assessor_app_version'];
                $check_version = ($this->input->post('version') != $current_app_version) ? 1 : 0;

                $arr_batches_pending = array();
                $arr_batches_completed = array();
                $arr_batches_student_attendance_pending = array();
                $uploaded_batches_checklist_documents_status = array();
                
                //Get Students count if the attendance is not marked based on batches
                $arr_batch_students_attendance_list = $this->mainModel->getAssessorBatchCandidateAttendenceDetails($assessor_id);
                if($arr_batch_students_attendance_list != false) {
                    foreach($arr_batch_students_attendance_list as $arr_batch_students_attendance_data) {
                        $arr_batches_student_attendance_pending[$arr_batch_students_attendance_data['tb_id']] = $arr_batch_students_attendance_data['pending_attendance'];
                    }
                } 
                
                //Check whether all mandatory documents are uploaded
                $arr_checklist_documents_master_list = $this->mainModel->getAllRecords('tbl_assessment_checklist_documents_master',array("document_requirement" => 'Mandatory'));
                if($arr_checklist_documents_master_list != false) {
                    foreach($arr_checklist_documents_master_list as $checklist_master_data) {
                        $arr_checklist_documents_master_ids[$checklist_master_data['acdm_id']] = $checklist_master_data['acdm_id'];
                    }

                    if(count($arr_checklist_documents_master_ids) > 0) {
                        $arr_batchwise_checklist_documents_count = $this->mainModel->getAssessorBatchChecklistDocumentsUploadedCount($assessor_id);
                        if($arr_batchwise_checklist_documents_count != false) {
                            foreach($arr_batchwise_checklist_documents_count as $checklist_data) {
                                if($checklist_data['uploaded_checklist_documents_count'] != count($arr_checklist_documents_master_ids)) {    
                                    $uploaded_batches_checklist_documents_status[$checklist_data['tb_id']] = 'Pending';
                                }
                                else {
                                    $uploaded_batches_checklist_documents_status[$checklist_data['tb_id']] = 'Completed';
                                }
                            }
                        }
                    }
                }
                
                //Get Batch Details assigned to the assessor
                $arr_batch_list = $this->mainModel->getAssessorBatchDetails($assessor_id);
                //echo "<br> str ".$this->db->last_query();exit;
                if($arr_batch_list != false) {
                    foreach($arr_batch_list as $arr_batch_data) {
                        $assessmentStartDateTime = strtotime($arr_batch_data['tb_start_date_time']);
                        $assessmentEndDateTime = strtotime($arr_batch_data['tb_end_date_time']);
                        $assessmentStatus = $arr_batch_data['tb_assessment_status'];
                        
                        $arr_batch_details['tb_id'] = $arr_batch_data['tb_id'];
                        $arr_batch_details['trade_name'] = $arr_batch_data['trade_code']." - ".$arr_batch_data['trade_name'];
                        $arr_batch_details['tp_name'] = $arr_batch_data['tp_code']." - ".$arr_batch_data['tp_name'];
                        $arr_batch_details['tc_name'] = $arr_batch_data['tc_code']." - ".$arr_batch_data['tc_name'];
                        $arr_batch_details['batch_id'] = $arr_batch_data['batch_id'];
                        $arr_batch_details['scheme'] = $arr_batch_data['scheme_name'].'('.$arr_batch_data['subscheme_name'].')';
                        $arr_batch_details['assessment_start_date'] = date('d-m-Y H:i:s',$assessmentStartDateTime);
                        $arr_batch_details['assessment_end_date'] = date('d-m-Y H:i:s',$assessmentEndDateTime);
                        $arr_batch_details['assessment_status'] = $assessmentStatus;
                        $arr_batch_details['center_building_photo'] = ($arr_batch_data['center_building_photo'] != "") ? base_url().$this->config->item('assessors_assements_path').$arr_batch_data['center_building_photo'] : "";
                        $arr_batch_details['selfie_with_center_board'] = ($arr_batch_data['selfie_with_center_board'] != "") ? base_url().$this->config->item('assessors_assements_path').$arr_batch_data['selfie_with_center_board'] : "";
                        $arr_batch_details['total_students'] = ($arr_batch_data['total_students'] > 0) ? $arr_batch_data['total_students']."" : 0;
                        $arr_batch_details['attendence_status'] = (array_key_exists($arr_batch_data['tb_id'],$arr_batches_student_attendance_pending)) ? "Pending" : "Completed";
                        $arr_batch_details['checklist_documents_status'] = (array_key_exists($arr_batch_data['tb_id'],$uploaded_batches_checklist_documents_status)) ? $uploaded_batches_checklist_documents_status[$arr_batch_data['tb_id']] : "Pending";
                        $arr_batch_details['tb_exam_type'] = $arr_batch_data['tb_exam_type'];
                        $arr_batch_details['qp_generated_status'] = (int) $arr_batch_data['qp_generated_status'];
                        $arr_batch_details['be_id'] = (int) $arr_batch_data['be_id'];
                        if($arr_batch_data['tb_exam_type'] == 'Offline') {
                            $arr_batch_details['download_omr_sheet_url_link'] = base_url().'download-omr-sheet/'.id_encode($arr_batch_data['tb_id']);
                            $arr_batch_details['download_question_paper_url_link'] = base_url().'download-batch-question-paper/'.id_encode($arr_batch_data['tb_id']);
                        }
                        else {
                            $arr_batch_details['download_omr_sheet_url_link'] = "";
                            $arr_batch_details['download_question_paper_url_link'] = "";
                        }
                        
                        if($assessmentStatus == 'Pending') {
                            $arr_batch_details['download_attendace_sheet_url_link'] = base_url().'download-attendance-sheet/'.id_encode($arr_batch_data['tb_id']);

                            array_push($arr_batches_pending,$arr_batch_details);
                        }
                        else if($assessmentStatus == 'Completed') {
                            array_push($arr_batches_completed,$arr_batch_details);
                        }
                    }
                }
                
                // Convert JSON string to associative array
                $arr_batches_pending = convertNullToEmptyString($arr_batches_pending);
                $arr_batches_completed = convertNullToEmptyString($arr_batches_completed);
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['contact'] = $arr_settings_list[0]['contact_us'];
                $data['batches_pending'] = $arr_batches_pending;
                $data['batches_completed'] = $arr_batches_completed;
                $data['check_version'] = $check_version;
                //$data['resume_upload_url_link'] = base_url().'get-assessor-resume-upload-form/'.id_encode($assessor_id);
                $data['profile_photo_link'] = $arr_assessor_list->assessor_photo;
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
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
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
                $data['assessor_id'] = id_encode($assessor_id);
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

    public function batchAssessmentCandidateList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            
            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $tb_id = $this->input->post('tb_id');

                $arr_batch_details = array();
                $arr_student_details = array();
                $arr_practical_activity_marked_details = array();
                $arr_viva_marked_details = array();
                $student_practical_activity_statistics = "0/0";
                $student_viva_statistics = "0/0";

                //Get Batch Details assigned to the assessor
                $arr_batch_list = $this->mainModel->getBatchCandidateDetails($tb_id);
                //echo "<br> str ".$this->db->last_query();exit;
                if($arr_batch_list != false) {
                    $arr_optional_exam_type = explode(",",$arr_batch_list[0]['optional_exam_type']);
                    $tb_exam_type = $arr_batch_list[0]['tb_exam_type'];
                    
                    //Get Candidate answered Practical Activity and Viva Questions count
                    if(in_array('practicalActivity',$arr_optional_exam_type)) {
                        $arr_practical_activity_marked_list = $this->mainModel->getCandidateMarkedQuestionCount($tb_id,'tbl_practical_activity_answers');
                        if($arr_practical_activity_marked_list != false) {
                            foreach($arr_practical_activity_marked_list as $pa_data) {
                                $arr_practical_activity_marked_details[$pa_data['student_id']] = $pa_data['total_qns_marked'];
                            }
                        }
                    }
                    if(in_array('viva',$arr_optional_exam_type)) {
                        $arr_viva_marked_list = $this->mainModel->getCandidateMarkedQuestionCount($tb_id,'tbl_viva_answers');
                        if($arr_viva_marked_list != false) {
                            foreach($arr_viva_marked_list as $viva_data) {
                                $arr_viva_marked_details[$viva_data['student_id']] = $viva_data['total_qns_marked'];
                            }
                        }
                    }

                    /*echo "<pre>";
                    print_r($arr_practical_activity_marked_details);
                    print_r($arr_viva_marked_details);
                    echo "</pre>";
                    exit;*/

                    foreach($arr_batch_list as $key => $arr_batch_data) {
                        if($key == 0) {
                            $assessmentStartDateTime = strtotime($arr_batch_data['tb_start_date_time']);
                            $assessmentEndDateTime = strtotime($arr_batch_data['tb_end_date_time']);
                            $assessmentStatus = $arr_batch_data['tb_assessment_status'];
                            $aadhar_verification = $arr_batch_data['aadhar_verification'];
                            
                            $arr_batch_details['tb_id'] = $arr_batch_data['tb_id'];
                            $arr_batch_details['trade_name'] = $arr_batch_data['trade_code']." - ".$arr_batch_data['trade_name'];
                            $arr_batch_details['batch_id'] = $arr_batch_data['batch_id'];
                            $arr_batch_details['scheme'] = $arr_batch_data['scheme_name'].'('.$arr_batch_data['subscheme_name'].')';
                            $arr_batch_details['assessment_start_date'] = date('d-m-Y H:i:s',$assessmentStartDateTime);
                            $arr_batch_details['assessment_end_date'] = date('d-m-Y H:i:s',$assessmentEndDateTime);
                            $arr_batch_details['assessment_status'] = $assessmentStatus;
                            $arr_batch_details['tb_exam_type'] = $arr_batch_data['tb_exam_type'];
                        }
                        $student_id = $arr_batch_data['student_id'];

                        $password = "Aadhaar Verification is ".$arr_batch_data['profile_verification_status'];
                        if($aadhar_verification == 'Optional') {
                            $password = $arr_batch_data['password'];
                        }
                        else if($aadhar_verification == 'Mandatory' && $arr_batch_data['profile_verification_status'] == 'Verified') {
                            $password = $arr_batch_data['password'];
                        }

                        if($arr_batch_data['practical_activity_questions'] != "" && $arr_batch_data['viva_questions'] != "") {
                            $total_practical_activity_questions = count(explode(",",$arr_batch_data['practical_activity_questions']));
                            $marked_practical_activity_questions = (array_key_exists($student_id,$arr_practical_activity_marked_details)) ? $arr_practical_activity_marked_details[$student_id] : 0;

                            $student_practical_activity_statistics = $marked_practical_activity_questions."/".$total_practical_activity_questions;

                            $total_viva_questions = count(explode(",",$arr_batch_data['viva_questions']));
                            $marked_viva_questions = (array_key_exists($student_id,$arr_viva_marked_details)) ? $arr_viva_marked_details[$student_id] : 0;

                            $student_viva_statistics = $marked_viva_questions."/".$total_viva_questions;
                        }
                        else if($arr_batch_data['practical_activity_questions'] != "" && $arr_batch_data['viva_questions'] == "") {
                            $total_practical_activity_questions = count(explode(",",$arr_batch_data['practical_activity_questions']));
                            $marked_practical_activity_questions = (array_key_exists($student_id,$arr_practical_activity_marked_details)) ? $arr_practical_activity_marked_details[$student_id] : 0;

                            $student_practical_activity_statistics = $marked_practical_activity_questions."/".$total_practical_activity_questions;
                        }
                        else if($arr_batch_data['practical_activity_questions'] == "" && $arr_batch_data['viva_questions'] != "") {
                            $total_viva_questions = count(explode(",",$arr_batch_data['viva_questions']));
                            $marked_viva_questions = (array_key_exists($student_id,$arr_viva_marked_details)) ? $arr_viva_marked_details[$student_id] : 0;

                            $student_viva_statistics = $marked_viva_questions."/".$total_viva_questions;
                        }

                        if($arr_batch_data['student_assessment_status'] == 'Pending' && $tb_exam_type == 'Online') {
                            $assessmentStatus = "";
                            $assessmentStatusVal = 1;
                            $assessmentStatusValidationText = "Theory Assessment is Pending";
                        }
                        else {
                            if($arr_batch_data['practical_activity_questions'] != "" && $arr_batch_data['viva_questions'] != "") {
                                $assessmentStatus = "Take Practical Activity/Viva";
                                $assessmentStatusVal = 4;
                                $assessmentStatusValidationText = "";
                            }
                            else if($arr_batch_data['practical_activity_questions'] != "" && $arr_batch_data['viva_questions'] == "") {
                                $assessmentStatus = "Take Practical Activity";
                                $assessmentStatusVal = 2;
                                $assessmentStatusValidationText = "";
                            }
                            else if($arr_batch_data['practical_activity_questions'] == "" && $arr_batch_data['viva_questions'] != "") {
                                $assessmentStatus = "Take Viva";
                                $assessmentStatusVal = 3;
                                $assessmentStatusValidationText = "";
                            }
                        }

                        $student_assessment_status = ($arr_batch_data['student_assessment_status'] == 'Completed') ? 'Completed' : 'Pending';

                        $arr_student_list['student_id'] = $arr_batch_data['student_id'];
                        $arr_student_list['student_name'] = $arr_batch_data['student_name'];
                        $arr_student_list['enrollment_number'] = $arr_batch_data['enrollment_number'];
                        $arr_student_list['password'] = $password;
                        $arr_student_list['aadhar_number'] = $arr_batch_data['aadhar_number'];
                        $arr_student_list['aadhar_front_filename'] = $arr_batch_data['aadhar_front_filename'];
                        $arr_student_list['aadhar_back_filename'] = $arr_batch_data['aadhar_back_filename'];
                        $arr_student_list['student_photo_with_aadhar'] = $arr_batch_data['student_photo_with_aadhar'];
                        $arr_student_list['student_attendance'] = $arr_batch_data['student_attendance'];
                        $arr_student_list['assessment_status'] = $assessmentStatus;
                        $arr_student_list['assessment_status_val'] = $assessmentStatusVal;
                        $arr_student_list['assessment_status_validation_text'] = $assessmentStatusValidationText;
                        $arr_student_list['student_assessment_status'] = $student_assessment_status;
                        $arr_student_list['student_practical_activity_statistics'] = $student_practical_activity_statistics;
                        $arr_student_list['student_viva_statistics'] = $student_viva_statistics;
                        $arr_student_list['practical_activity_video_uploaded'] = ($arr_batch_data['practicalactivity_video_file'] == "" || $arr_batch_data['practicalactivity_video_file'] == NULL) ? 'Not Uploaded' : 'Uploaded'; 
                        $arr_student_list['viva_video_uploaded'] = ($arr_batch_data['viva_video_file'] == "" || $arr_batch_data['viva_video_file'] == NULL) ? 'Not Uploaded' : 'Uploaded';
                        $arr_student_list['practicalactivity_answers_record_generated'] = $arr_batch_data['practicalactivity_answers_record_generated'];
                        $arr_student_list['viva_answers_record_generated'] = $arr_batch_data['viva_answers_record_generated'];
                        $arr_student_list['omr_answers_submitted'] = ($arr_batch_data['theory_submission_dts'] != "") ? 1 : 0;

                        array_push($arr_student_details,$arr_student_list);
                    }
                }

                $arr_batch_details['total_students'] = (count($arr_student_details) > 0) ? count($arr_student_details) : 0;

                // Convert JSON string to associative array
                $arr_batch_details = convertNullToEmptyString($arr_batch_details);
                $arr_student_details = convertNullToEmptyString($arr_student_details);
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['batch_details'] = $arr_batch_details;
                $data['student_details'] = $arr_student_details;
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveCandidateAttendanceStatus()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $student_id = $this->input->post('student_id');
            $student_attendance = $this->input->post('student_attendance');

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($student_id > 0 && $student_attendance != "") {
                    $updData['student_attendance'] = trim($this->input->post('student_attendance'));
                    $updData['result'] = (trim($this->input->post('student_attendance')) == 'Absent') ? 'Absent' : '';
                    
                    $this->db->where('student_id', $student_id);
                    $this->db->update('tbl_students', $updData);  
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Attendance saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Student Id OR student_attendance is null';
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
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $student_id = $this->input->post('student_id');
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
            //log_message('debug','getCandidateBatchDetails: ' . $this->db->last_query()); 
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
            
                //log_message('debug','practicalactivity_answers_record_generated: ' . $practicalactivity_answers_record_generated); 
                //log_message('debug','viva_answers_record_generated: ' . $viva_answers_record_generated); 
                
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
                        log_message('debug','getCandidateQuestionDetails: ' . $this->db->last_query()); 
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
                            }

                            //echo "<br> count Qn Ids ".count($arrQuestionIds);
                            //echo "<br> count qn list ".count($question_list);
                            //exit;
                            
                            //echo "<br> practicalactivity_answers_record_generated ".$practicalactivity_answers_record_generated;
                            //log_message('debug','practicalactivity_answers_record_generated: ' . $practicalactivity_answers_record_generated);
                            //log_message('debug','count qn list: ' . count($question_list));
                            //log_message('debug','arrPracticalQuestionIds: ' . print_r($arrPracticalQuestionIds));

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
                        //log_message('debug','arrGetVivaQuestionDetails: ' . $this->db->last_query()); 
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

                            }

                            //echo "<br> count Qn Ids ".count($arrQuestionIds);
                            //echo "<br> count qn list ".count($question_list);
                            //exit;
                            
                            //log_message('debug','viva_answers_record_generated: ' . $viva_answers_record_generated); 
                            //log_message('debug','count viva Qn Ids: ' . count($arrVivaQuestionIds));
                            //log_message('debug','count viva qn list: ' . count($viva_question_list));
                            //log_message('debug','arrVivaQuestionIds: ' . print_r($arrVivaQuestionIds));
                            
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
                        $data['assessor_id'] = id_encode($assessor_id);
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

    public function getCandidateTheoryQuestionsList()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $candidate_id = $this->input->post('student_id');
            $qp_generated_status = 0;
            $question_list = array();
            
            $tokenRes = $this->checkToken($token);
            $arr_question_list = array();
            
            if ($tokenRes['status'] == true) {
                $arr_student_theory_details = $this->mainModel->getAnswerDetailsByCandidateId($candidate_id);
                if($arr_student_theory_details != false) {
                    foreach($arr_student_theory_details as $key => $details) {
                        $arr_question_list['qid'] = $details['qid'];
                        $arr_question_list['question_no'] = ($key + 1);
                        $arr_question_list['option'] = strtoupper($details['ans']);
                        
                        array_push($question_list,$arr_question_list);
                    }
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['question_list'] = $question_list;
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

    public function saveAssessorAssessmentImage()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('image') != "" && $this->input->post('image_type') != "" && $this->input->post('tb_id') > 0 
                    && $this->input->post('geo_address') != "" && $this->input->post('lat') != "" && $this->input->post('long') != "") {

                    $tb_id = $this->input->post('tb_id');
                    $arr_batch = $this->mainModel->getAllRecords('tbl_training_batches',array("tb_id" => $tb_id));
                    
                    $student_id = $this->input->post('student_id');
                    $file = "";

                    $assessor_code = $arr_assessor_list->assessor_code;
                    $assessor_name = trim($arr_assessor_list->assessor_name);
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
                        if ($count % 3 == 0) {
                            $geoAddress .="\n";
                        }
                    }
                    
                    // If there are remaining words less than four, add them to the last line
                    if ($count % 3 != 0) {
                        $geoAddress .="\n";
                    }
                    
                    $dateTime = date('d-m-Y h:i A')." GMT +05:30";
                    
                    //Create WaterMark
                    $watermarkValue = $arr_batch[0]['batch_id']."-".$assessor_code."\nLat ".$this->input->post('lat').",Long ".$this->input->post('long')."\n".$geoAddress.$dateTime."";
                    
                    $arr_batch_details = $this->mainModel->getAllRecords('tbl_training_batches',array("tb_id" => $tb_id));
                    
                    $batch_id = str_replace(array("/", " "),"-",$arr_batch[0]['batch_id']);
            
                    if($this->input->post('image_type') == 'center_building_photo') {
                        $updData['center_building_photo'] = $this->uploadBase64ImageAndWatermark($this->input->post('image'), 'assessors_assements', $batch_id.'-'.date('dmYHis').'-center-building-photo',$watermarkValue);
                        if($arr_batch_details[0]['center_building_photo'] != "" && $updData['center_building_photo'] != "") {
                            $file = $this->config->item('assessors_assements_path').$arr_batch_details[0]['center_building_photo'];
                        }
                    } 
                    else if($this->input->post('image_type') == 'selfie_with_center_board') {
                        $updData['selfie_with_center_board'] = $this->uploadBase64ImageAndWatermark($this->input->post('image'), 'assessors_assements', $batch_id.'-'.date('dmYHis').'-selfie-with-center-board',$watermarkValue);
                        if($arr_batch_details[0]['selfie_with_center_board'] != "" && $updData['selfie_with_center_board'] != "") {
                            $file = $this->config->item('assessors_assements_path').$arr_batch_details[0]['selfie_with_center_board'];
                        }
                    } 
                    else if($this->input->post('image_type') == 'aadhar_front_filename' && $student_id > 0) {
                        $arr_student = $this->mainModel->getAllRecords('tbl_students',array("student_id" => $student_id));

                        $updData['aadhar_front_filename'] = $this->uploadBase64ImageAndWatermark($this->input->post('image'), 'aadhaar', $arr_student[0]['enrollment_number'].'-'.date('dmYHis').'-aadhaar-image-front',$watermarkValue);
                        if($arr_student[0]['aadhar_front_filename'] != "" && $updData['aadhar_front_filename'] != "") {
                            $file = $this->config->item('aadhaar_filename_path').$arr_student[0]['aadhar_front_filename'];
                        }
                    } 
                    else if($this->input->post('image_type') == 'aadhar_back_filename' && $student_id > 0) {
                        $arr_student = $this->mainModel->getAllRecords('tbl_students',array("student_id" => $student_id));

                        $updData['aadhar_back_filename'] = $this->uploadBase64ImageAndWatermark($this->input->post('image'), 'aadhaar', $arr_student[0]['enrollment_number'].'-'.date('dmYHis').'-aadhaar-image-back',$watermarkValue);
                        if($arr_student[0]['aadhar_back_filename'] != "" && $updData['aadhar_back_filename'] != "") {
                            $file = $this->config->item('aadhaar_filename_path').$arr_student[0]['aadhar_back_filename'];
                        }
                    } 
                    else if($this->input->post('image_type') == 'student_photo_with_aadhar' && $student_id > 0) {
                        $arr_student = $this->mainModel->getAllRecords('tbl_students',array("student_id" => $student_id));

                        $updData['student_photo_with_aadhar'] = $this->uploadBase64ImageAndWatermark($this->input->post('image'), 'aadhaar', $arr_student[0]['enrollment_number'].'-'.date('dmYHis').'-student-photo-with-aadhar',$watermarkValue);
                        if($arr_student[0]['student_photo_with_aadhar'] != "" && $updData['student_photo_with_aadhar'] != "") {
                            $file = $this->config->item('student_photo_with_aadhar').$arr_student[0]['student_photo_with_aadhar'];
                        }
                    } 
                    
                    if($file != "") {
                        // Check if the file exists before attempting to delete it
                        if (file_exists($file)) {
                            // Attempt to delete the file
                            unlink($file);
                        }
                    }
                    if($student_id == 0) {
                        $query = $this->mainModel->updateData('tb_id ', $tb_id, 'tbl_training_batches', $updData);           
                    }
                    else {
                        $query = $this->mainModel->updateData('student_id ', $student_id, 'tbl_students', $updData);           
                    }   
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Image saved successfully';
                }
                else {
                    $error = "";
                    if($this->input->post('image') == "") {
                        $error .= "Image is null \n";
                    }
                    if($this->input->post('image_type') == "") {
                        $error .= "Image type is null \n";
                    }
                    if($this->input->post('tb_id') <= 0) {
                        $error .= "Invalid Batch \n";
                    }
                    if($this->input->post('geo_address') == "") {
                        $error .= "Geoaddress is null \n";
                    }
                    if($this->input->post('lat') == "") {
                        $error .= "Lat is null \n";
                    }
                    if($this->input->post('long') == "") {
                        $error .= "Long is null \n";
                    }
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please enter all mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveCandidateRecording() 
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            $assessor_code = $arr_assessor_list->assessor_code; 

            $token =  $arr_assessor_list->unique_token;
            $student_id = $this->input->post('student_id');
            $type = $this->input->post('type');
            $fieldName = ($type == 'Practical Activity') ? 'practicalactivity_video_file' : 'viva_video_file';
            $submissionFieldName = ($type == 'Practical Activity') ? 'practicalactivity_submission_dts' : 'viva_submission_dts';

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $arr_student_video_details = array();
                $total_video_uploaded = 0;
                $total_videos = 0;
                $old_video_file = "";
                
                $arr_student_video_list = $this->mainModel->getCandidateBatchDetails($student_id); 
                //echo "<br> str ".$this->db->last_query();exit;
                if($arr_student_video_list != false) {
                    $old_video_file = $arr_student_video_list[0][$fieldName]; 
                    $batch_id = $arr_student_video_list[0]['batch_id']; 
                }

                if($student_id > 0 && $type != "" && isset($_FILES['video_file']) && $this->input->post('geo_address') != "" && $this->input->post('lat') != "" && $this->input->post('long') != "") {
                    if(isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
                        // File paths
                        $temp_path = $_FILES['video_file']['tmp_name'];
                        $fileName = $student_id.'-'.date('dmYHis').'-'.$fieldName.'.mp4';
                        $input_file = './uploads/student_assessment_videos/temp/' . $_FILES['video_file']['name'];
                        $video_submitted_dts = date('d-m-Y H:i:s'); 

                        $video = $this->validateAndSaveUploadedVideo('video_file','student_assessment_videos/temp',$student_id . '-' . date('dmYHis') . '-' . $fieldName,25); // max size MB

                        if ($video) {

                            $input_file     = $video['file_path'];
                            $fileName       = $video['file_name'];
                            $output_file    = './uploads/student_assessment_videos/'.$fileName;

                            $updData[$submissionFieldName] = date('Y-m-d H:i:s');
                            $updData[$fieldName] = $fileName;

                            if ($old_video_file != "" && $updData[$fieldName] != "") {

                                $old_file = $this->config->item('student_assessment_videos_path') . $old_video_file;

                                if (file_exists($old_file)) {
                                    @unlink($old_file);
                                }
                            }

                            // Watermark video
                            $output = watermarkVideo(
                                $input_file,
                                $output_file,
                                $batch_id,
                                $assessor_code,
                                $this->input->post('lat'),
                                $this->input->post('long'),
                                $this->input->post('geo_address'),
                                $video_submitted_dts
                            );

                            if ($type == 'Practical Activity') {

                                $updData['practicalactivity_video_lat'] = $this->input->post('lat');
                                $updData['practicalactivity_video_lng'] = $this->input->post('long');
                                $updData['practicalactivity_video_geoaddress'] = $this->input->post('geo_address');
                                $updData['practicalactivity_video_watermark_status'] = ($output == 'Success') ? 1 : 2;
                                $updData['practicalactivity_video_submitted_dts'] = date('Y-m-d H:i:s');

                            } else {

                                $updData['viva_video_lat'] = $this->input->post('lat');
                                $updData['viva_video_lng'] = $this->input->post('long');
                                $updData['viva_video_geoaddress'] = $this->input->post('geo_address');
                                $updData['viva_video_watermark_status'] = ($output == 'Success') ? 1 : 2;
                                $updData['viva_video_submitted_dts'] = date('Y-m-d H:i:s');
                            }

                            $this->db->where('student_id', $student_id);
                            $this->db->update('tbl_students', $updData);

                            $arrInsert['student_id'] = $student_id;
                            $arrInsert['batch_id'] = $batch_id;
                            $arrInsert['assessor_code'] = $assessor_code;
                            $arrInsert['video_type'] = $type;
                            $arrInsert['status'] = ($output == 'Success') ? 'Success' : 'Error';
                            $arrInsert['response'] = ($output == 'Success') ? 'Success' : $output;
                            $arrInsert['created_dts'] = date('Y-m-d H:i:s');

                            $this->db->insert('tbl_cron_video_watermarking', $arrInsert);

                            $data['status'] = true;
                            $data['rcode'] = 200;
                            $data['message'] = ($output == 'Success')
                                ? 'Video saved successfully'
                                : 'Error watermarking Video. ' . $output;

                        } else {

                            $data['status'] = false;
                            $data['rcode'] = 500;
                            $data['message'] = 'Invalid or unsafe video uploaded.';
                        }
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = "Error! Video File size exceeds limit";
                    }  
                }
                else {
                    $message = "";
                    if(!isset($_FILES['video_file'])) {
                        $message .= "video file is missing";
                    }
                    if($type == "") {
                        $message .= "type is null ";
                    }
                    if($student_id <= 0) {
                        $message = "student_id is null ";
                    }
                    if($this->input->post('geo_address') == "") {
                        $message = "geo_address is null ";
                    }
                    if($this->input->post('lat') == "") {
                        $message = "lat is null ";
                    }
                    if($this->input->post('long') == "") {
                        $message = "long is null ";
                    }
                    
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = $message;
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data); 
    }

    public function saveCandidateRecordingMarks()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $student_id = $this->input->post('student_id');
            $student_assessment_status = $this->input->post('student_assessment_status');
            $qid = $this->input->post('qid');
            $marks = $this->input->post('marks');
            $max_marks = $this->input->post('max_marks');
            $tableName = ($student_assessment_status == 'Practical Activity') ? 'tbl_practical_activity_answers' : 'tbl_viva_answers';
            $fieldName = ($student_assessment_status == 'Practical Activity') ? 'practical-activity' : 'viva';

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $arr_student_video_details = array();
                $total_video_uploaded = 0;
                $total_videos = 0;
                $old_video_file = "";
                
                if($student_id > 0 && $student_assessment_status != "" && $qid > 0 && $marks != "" && $max_marks > 0) {
                    if($marks > $max_marks) {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = 'Marks cannot be greater than Max. Marks';
                    }
                    else {
                        $updData['marks'] = trim($marks);
                        $updData['assessor_id'] = $assessor_id;
                        
                        $this->db->where('student_id', $student_id);
                        $this->db->where('qid', $qid);
                        $this->db->update($tableName, $updData);  
                        
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['assessor_id'] = id_encode($assessor_id);
                        $data['unique_token'] = $token;
                        $data['message'] = 'Marks saved successfully';
                    }    
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Student Id OR student_assessment_status OR Qid OR Marks is missing';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getChecklistDocuments(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                $tb_id = $this->input->post('tb_id');

                $arr_checklist_documents_list = array();
            
                //Get assessor uploaded checklist docs details
                $arr_checklist_uploaded_documents_details = array();
                $arr_checklist_documents_description = array();

                $arr_checklist_uploaded_documents_list = $this->mainModel->getAllRecords('tbl_checklist_documents_details',array("assessor_id" => $assessor_id, 'tb_id' => $tb_id));
                if($arr_checklist_uploaded_documents_list != false) {
                    foreach($arr_checklist_uploaded_documents_list as $uploaded_docs) {
                        if($uploaded_docs['document_file_uploaded'] != "") {
                            $arr_checklist_uploaded_documents_details[$uploaded_docs['acdm_id']] = $uploaded_docs['document_file_uploaded'];
                        }
                        $arr_checklist_documents_description[$uploaded_docs['acdm_id']] = ($uploaded_docs['document_description'] != "") ? $uploaded_docs['document_description'] : "";
                    }
                }

                /*echo "<pre>";
                print_r($arr_checklist_uploaded_documents_details);
                echo "</pre>";
                exit;*/

                $arr_checklist_documents_master_list = $this->mainModel->getAllRecords('tbl_assessment_checklist_documents_master',array("status" => 1));
                if($arr_checklist_documents_master_list != false) {
                    
                    foreach($arr_checklist_documents_master_list as $checklist_docs) {
                        $acdm_id = $checklist_docs['acdm_id'];
                        $checklist_docs['document_uploaded'] = (array_key_exists($acdm_id,$arr_checklist_uploaded_documents_details)) ? base_url().$this->config->item('assessors_checklist_documents_path').$arr_checklist_uploaded_documents_details[$acdm_id] : 'No';
                        $checklist_docs['document_uploaded_file_name'] = (array_key_exists($acdm_id,$arr_checklist_uploaded_documents_details)) ? $arr_checklist_uploaded_documents_details[$acdm_id] : '';
                        $checklist_docs['document_description'] = (array_key_exists($acdm_id,$arr_checklist_documents_description)) ? $arr_checklist_documents_description[$acdm_id] : '';

                        array_push($arr_checklist_documents_list,$checklist_docs);
                    }
                }
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['checklist_documents_list'] = $arr_checklist_documents_list;
                
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }

    public function saveAssessorChecklistDocuments()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($this->input->post('acdm_id') != "" && $this->input->post('document_type') != "" && $this->input->post('tb_id') > 0 && $this->input->post('document_title') != ""
                    && $this->input->post('geo_address') != "" && $this->input->post('lat') != "" && $this->input->post('long') != "") {

                    $tb_id = $this->input->post('tb_id');
                    $arr_batch = $this->mainModel->getAllRecords('tbl_training_batches',array("tb_id" => $tb_id));
                    $batch_id = str_replace(array("/", " "), "-", $arr_batch[0]['batch_id']);
                   
                    $assessor_code = $arr_assessor_list->assessor_code;
                    $assessor_name = trim($arr_assessor_list->assessor_name);
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
                        if ($count % 3 == 0) {
                            $geoAddress .="\n";
                        }
                    }
                    
                    // If there are remaining words less than four, add them to the last line
                    if ($count % 3 != 0) {
                        $geoAddress .="\n";
                    }
                    $dateTime = date('d-m-Y h:i A')." GMT +05:30";

                    //Create WaterMark
                    $watermarkValue = $arr_batch[0]['batch_id']."-".$assessor_code."\nLat ".$this->input->post('lat').",Long ".$this->input->post('long')."\n".$geoAddress.$dateTime."";

                    $old_file = "";
                    $tcdd_id = 0;
                    $video_error = 0;
                    $updData['document_file_uploaded'] = "";
                    $updData['document_description'] = "";
                    $document_type = strtolower($this->input->post('document_type'));
                    
                    $arr_checklist_documents_details = $this->mainModel->getAllRecords('tbl_checklist_documents_details',array("acdm_id" => $this->input->post('acdm_id'),"assessor_id" => $assessor_id,"tb_id" => $tb_id));
                    if($arr_checklist_documents_details != false) {
                        $tcdd_id = $arr_checklist_documents_details[0]['tcdd_id'];
                        $old_file = $arr_checklist_documents_details[0]['document_file_uploaded'];
                    }
                    //echo "<br> str ".$this->db->last_query();
                    //echo "<br>tcdd_id ".$tcdd_id;exit;
                    $video_upload = 0;
                    if(isset($_FILES['file'])) {
                        // Extract the file extension from the file name
                        $fileExtension = strtolower(pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION)); 
                        if($fileExtension == "mp4") {
                            $video_upload = 1;
                        } 
                    }
                    
                    if($document_type == 'file' && $video_upload == 0) {
                        if(isset($_FILES['file'])) {
                            $updData['document_file_uploaded'] = $this->uploadFile('file', 'assessors_checklist_documents', $batch_id.'-'.seo_friendly_url($this->input->post('document_title')).'-'.date('dmYHis'));
                        }
                        else {
                            $updData['document_file_uploaded'] = $this->uploadBase64ImageAndWatermark($this->input->post('file'), 'assessors_checklist_documents', $batch_id.'-'.seo_friendly_url($this->input->post('document_title')).'-'.date('dmYHis'),'');
                        }  
                    }
                    else if($document_type == 'image') {
                        $updData['document_file_uploaded'] = $this->uploadBase64ImageAndWatermark($this->input->post('file'), 'assessors_checklist_documents', $batch_id.'-'.seo_friendly_url($this->input->post('document_title')).'-'.date('dmYHis'),$watermarkValue);
                    }
                    else if($this->input->post('document_type') == 'text') {
                         $description = trim($this->input->post('file', true));

                        // Remove all HTML tags
                        $description = strip_tags($description);

                        // Length validation
                        if (strlen($description) > 5000) {

                            $data['status'] = false;
                            $data['rcode'] = 400;
                            $data['message'] = 'Description exceeds maximum length.';
                            echo json_encode($data);
                            exit;
                        }

                        $updData['document_description'] = $this->input->post('file'); 
                    }
                    else if($video_upload == 1) {
                        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                            // File paths
                            $temp_path = $_FILES['file']['tmp_name'];
                            $fileName = $batch_id.'-'.seo_friendly_url($this->input->post('document_title')).'-'.date('dmYHis').'.mp4';
                            //$output_file = './uploads/assessors_checklist_documents/'.$fileName;
                            $input_file = './uploads/assessors_checklist_documents/temp/' . $_FILES['file']['name']; 
                            $video_submitted_dts = date('d-m-Y H:i:s'); 
    
                            $video = $this->validateAndSaveUploadedVideo('video_file','student_assessment_videos/temp',$batch_id . '-' . date('dmYHis') . '-document',5);

                            if ($video) {

                                $input_file = $video['file_path'];
                                $fileName   = $video['file_name'];

                                $output_file = './uploads/student_assessment_videos/' . $fileName;

                                $updData['document_file_uploaded'] = $fileName;

                                /*
                                |--------------------------------------------------------------------------
                                | Watermark Video
                                |--------------------------------------------------------------------------
                                */
                                $output = watermarkVideo(
                                    $input_file,
                                    $output_file,
                                    $batch_id,
                                    $assessor_code,
                                    $this->input->post('lat', true),
                                    $this->input->post('long', true),
                                    $this->input->post('geo_address', true),
                                    $video_submitted_dts
                                );

                                /*
                                |--------------------------------------------------------------------------
                                | Log Watermark Status
                                |--------------------------------------------------------------------------
                                */
                                $arrInsertCron = [
                                    'student_id'   => 0,
                                    'batch_id'     => $batch_id,
                                    'assessor_code'=> $assessor_code,
                                    'video_type'   => 'Checklist Document ' .
                                                    seo_friendly_url($this->input->post('document_title', true)),
                                    'status'       => ($output == 'Success') ? 'Success' : 'Error',
                                    'response'     => ($output == 'Success') ? 'Success' : $output,
                                    'created_dts'  => date('Y-m-d H:i:s')
                                ];

                                $this->db->insert('tbl_cron_video_watermarking',$arrInsertCron);

                                $video_error = ($output == 'Success') ? 0 : 1;

                            } else {

                                // Validation failed
                                $video_error = 2;

                                log_message(
                                    'error',
                                    'Video upload validation failed for checklist document.'
                                );
                            }
                        }
                        else {
                            $video_error = 3;
                        }
                    }
                    
                    if($old_file != "" && $updData['document_file_uploaded'] != "") {
                        $file = $this->config->item('assessors_checklist_documents_path').$old_file;
                        if (file_exists($file)) {
                            // Attempt to delete the file
                            unlink($file);
                        }
                    }
                    $updData['watermarking_error'] = $video_error;
                    if($tcdd_id == 0) {
                        $arrInsert['tb_id'] = $tb_id;
                        $arrInsert['acdm_id'] = $this->input->post('acdm_id');
                        $arrInsert['assessor_id'] = $assessor_id;
                        $arrInsert['document_file_uploaded'] = $updData['document_file_uploaded'];
                        $arrInsert['document_description'] = $updData['document_description'];
                        $arrInsert['lat'] = $this->input->post('lat');
                        $arrInsert['lng'] = $this->input->post('long');
                        $arrInsert['geo_address'] = $this->input->post('geo_address');
                        $arrInsert['watermarking_error'] = $updData['watermarking_error'];
                        $arrInsert['created_dts'] = date('Y-m-d H:i:s');
    
                        $this->db->insert('tbl_checklist_documents_details', $arrInsert);
                    }
                    else {
                        $query = $this->mainModel->updateData('tcdd_id', $tcdd_id, 'tbl_checklist_documents_details', $updData);
                    }
                       
                    if($video_error == 1) {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = "Error! Watermarking Video.";
                    }
                    else if($video_error == 2) {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = "Error! Uploading the file";
                    }
                    else if($video_error == 3) {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = "Error! Video File size exceeds limit";
                    }
                    else {
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['assessor_id'] = id_encode($assessor_id);
                        $data['unique_token'] = $token;
                        $data['message'] = 'Document saved successfully';
                    }
                }
                else {
                    $error = "";
                    if($this->input->post('acdm_id') == "") {
                        $error .= "acdm_id is null \n";
                    }
                    if($this->input->post('document_type') == "") {
                        $error .= "document_type is null \n";
                    }
                    if($this->input->post('tb_id') <= 0) {
                        $error .= "Invalid Batch \n";
                    }
                    if($this->input->post('document_title') == "") {
                        $error .= "document_type is null \n";
                    }
                    if($this->input->post('geo_address') == "") {
                        $error .= "Geoaddress is null \n";
                    }
                    if($this->input->post('lat') == "") {
                        $error .= "Lat is null \n";
                    }
                    if($this->input->post('long') == "") {
                        $error .= "Long is null \n";
                    }
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please enter all mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getResumeUploadForm($enc_assessor_id) {
        $assessor_id = id_decode($enc_assessor_id);

        $data['assessor_id'] = $assessor_id;
        
        $this->load->view('header',$data);
        $this->load->view('upload-resume',$data);
        $this->load->view('footer',$data);
    }
    
    public function saveResumeUpload() {
        /*echo "<pre>";
        print_r($_FILES);
        echo "</pre>";
        exit;*/
        $assessor_id = $this->input->post('assessor_id');
        $checkUser = $this->mainModel->getAllDataByVal('tbl_assessor', array('assessor_id' => $assessor_id));
        if ($checkUser->num_rows() > 0) {
            $arr_assessor_list = $checkUser->row();

            if(isset($_FILES['assessor_resume'])) {
                $updData['assessor_resume'] = $this->uploadFile('assessor_resume', 'assessors_resume', $assessor_id.'-'.date('dmYHis').'-assessor-resume');
                if($updData['assessor_resume'] != "") {
                    $file = $this->config->item('assessors_resume_path').$arr_assessor_list->assessor_resume;
                    if (file_exists($file)) {
                        // Attempt to delete the file
                        unlink($file);
                    }
                }
    
                $query = $this->mainModel->updateData('assessor_id', $assessor_id, 'tbl_assessor', $updData);
            }
        }
    }

    public function saveResumeUploadData() {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if(isset($_FILES['assessor_resume'])) {
                    $updData['assessor_resume'] = $this->uploadFile('assessor_resume', 'assessors_resume', $assessor_id.'-'.date('dmYHis').'-assessor-resume');
                    if($updData['assessor_resume'] != "" && $arr_assessor_list->assessor_resume != "") {
                        $file = $this->config->item('assessors_resume_path').$arr_assessor_list->assessor_resume;
                        if (file_exists($file)) {
                            // Attempt to delete the file
                            unlink($file);
                        }
                    }
        
                    $query = $this->mainModel->updateData('assessor_id', $assessor_id, 'tbl_assessor', $updData);
                }   
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['message'] = 'Resume saved successfully';
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getSectorsList(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {

                $arr_sector_skill_council_list = array();
                $arr_assessor_mapped_sectors_list = array();

                $arr_sector_skill_council_data = $this->mainModel->getAllRecords('tbl_sector_skill_council');

                //Get assessor mapped sectors
                $arr_assessor_mapped_sectors_data = $this->mainModel->getAllRecords('tbl_map_assessor_sector_skill_councils',array('assessor_id' => $assessor_id,'status' => 1));
                if($arr_assessor_mapped_sectors_data != false) {
                    foreach($arr_assessor_mapped_sectors_data as $arrMapData) {
                        $arr_assessor_mapped_sectors_list[$arrMapData['ssc_id']] = $arrMapData['ssc_id'];
                    }
                }
            
                if($arr_sector_skill_council_data != false) {
                    foreach($arr_sector_skill_council_data as $arrData) {
                        $arrData['ssc_description'] = ($arrData['ssc_description'] != "") ? $arrData['ssc_description'] : "";
                        $arrData['ssc_logo'] = ($arrData['ssc_logo'] != "") ? base_url().$this->config->item('ssc_logo_path').$arrData['ssc_logo'] : "";

                        if(array_key_exists($arrData['ssc_id'],$arr_assessor_mapped_sectors_list)) {
                            $arrData['mapped'] =  'Yes';
                        }
                        else {
                            $arrData['mapped'] = 'No';
                        }
                        array_push($arr_sector_skill_council_list, $arrData);
                    }
                    
                }

                /*echo "<pre>";
                print_r($arrStateDistrictList);
                echo "</pre>";
                exit;*/
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['sector_list'] = $arr_sector_skill_council_list;
                
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }

    public function saveMappedSectors()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $ssc_id = $this->input->post('ssc_id');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($ssc_id != "") {
                    $arr_ssc_id = explode(",",$ssc_id );

                    $this->db->where('assessor_id', $assessor_id);
                    $result=$this->db->delete('tbl_map_assessor_sector_skill_councils');
                    
                    foreach($arr_ssc_id as $scc_id) {
                        //Map partner to scc_id
                        $insData = array(
                            'assessor_id' => $assessor_id,
                            'ssc_id' => $scc_id,
                        );

                        $this->db->insert('tbl_map_assessor_sector_skill_councils', $insData);
                    }
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Sectors mapped successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please select Sectors';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getAssociatedAgenciesList(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {

                //Get assessor associated agencies
                $arr_assessor_associated_agencies_data = $this->mainModel->getAllRecords('tbl_assessor_associated_agencies',array('assessor_id' => $assessor_id));
                
                $data['status'] = true;
                $data['rcode'] = 200;
                $data['assessor_id'] = id_encode($assessor_id);
                $data['unique_token'] = $token;
                $data['associated_agencies'] = ($arr_assessor_associated_agencies_data != false) ? $arr_assessor_associated_agencies_data : array();
                
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }

    public function saveAssociatedAgencies()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $agency_name = $this->input->post('agency_name');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($agency_name != "") {
                    $arr_agency_name = explode(",",$agency_name );

                    $this->db->where('assessor_id', $assessor_id);
                    $result=$this->db->delete('tbl_assessor_associated_agencies');
                    
                    foreach($arr_agency_name as $agency_name) {
                        //Map partner to scc_id
                        $insData = array(
                            'assessor_id' => $assessor_id,
                            'agency_name' => $agency_name,
                        );

                        $this->db->insert('tbl_assessor_associated_agencies', $insData);
                    }
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Agencies saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please enter Agencies';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveCandidateAssessmentStatus()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $student_id = $this->input->post('student_id');
            $student_assessment_status = $this->input->post('student_assessment_status');
            $arr_optional_exam_type = array();
            $err_message = "";

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($student_id > 0 && $student_assessment_status != "" && $tb_id > 0) {
                    $arr_batch_details = $this->mainModel->getBatchDetails($tb_id);
                    if($arr_batch_details != false) {
                        $arr_optional_exam_type = explode(",",$arr_batch_details[0]['optional_exam_type']);
                    }    

                    if($student_assessment_status == 'Pending') {
                        //Get Candidate answered Practical Activity and Viva Questions count
                        if(in_array('practicalActivity',$arr_optional_exam_type)) {
                            $updData['student_assessment_status'] = 'Practical Activity';
                        }
                        else if(in_array('viva',$arr_optional_exam_type)) {
                            $updData['student_assessment_status'] = 'Viva';
                        }
                    }
                    else {
                        $arr_candidate_list = $this->mainModel->getAllRecords('tbl_students',array("student_id" => $student_id));
                        if($arr_candidate_list != false) {
                            //Check whether the candidates attendace is marked
                            if($arr_candidate_list[0]['student_attendance'] == "Pending") {
                                $err_message .= "Attendance is not marked \n";
                            }
                            else if($arr_candidate_list[0]['student_attendance'] == "Present") {
                                //Check whether the candidates practical/viva recordings are uploaded and marks are alloted
                                if(in_array('practicalActivity',$arr_optional_exam_type)) {
                                    $arr_pending_practical_activity_list = $this->mainModel->getCandidatePendingQuestionCount($student_id,'tbl_practical_activity_answers');
                                    //echo "<br> str ".$this->db->last_query();exit;
                                    if($arr_pending_practical_activity_list > 0) {
                                        $err_message .= "Marks awarding is pending for ".$arr_pending_practical_activity_list." practical activity questions \n";
                                    }
                                }
                                if(in_array('viva',$arr_optional_exam_type)) {
                                    $arr_pending_viva_list = $this->mainModel->getCandidatePendingQuestionCount($student_id,'tbl_viva_answers');
                                    if($arr_pending_viva_list > 0) {
                                        $err_message .= "Marks awarding is pending for ".$arr_pending_viva_list." viva questions \n";
                                    }
                                }                        
                            
                                if(in_array('practicalActivity',$arr_optional_exam_type) && $arr_candidate_list[0]['practicalactivity_video_file'] == "") {
                                    $err_message .= "Practical Activity recording is not uploaded \n";
                                }
                                if(in_array('viva',$arr_optional_exam_type) && $arr_candidate_list[0]['viva_video_file'] == "") {
                                    $err_message .= "Viva recording is not uploaded \n";
                                }
                            }
                        }
                        
                        $updData['student_assessment_status'] = $student_assessment_status;
                    }

                    if($err_message == "") {
                        $this->db->where('student_id', $student_id);
                        $this->db->update('tbl_students', $updData);  
                        
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['assessor_id'] = id_encode($assessor_id);
                        $data['unique_token'] = $token;
                        $data['message'] = 'Assessment Status saved successfully';
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = $err_message;
                    }                  
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Student Id OR student_assessment_status OR tb_id is null';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function saveBatchAssessmentStatus()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $tb_assessment_status = $this->input->post('tb_assessment_status');
            $arr_checklist_documents_master_ids = array();
            $arr_optional_exam_type = array();
            $err_message = "";

            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($tb_assessment_status != "" && $tb_id > 0) {
                    if($tb_assessment_status == 'Completed') {
                        $arr_batch_details = $this->mainModel->getBatchDetails($tb_id);
                        if($arr_batch_details != false) {
                            $arr_optional_exam_type = explode(",",$arr_batch_details[0]['optional_exam_type']);
                        } 

                        //Check whether the candidates practical/viva recordings are uploaded and marks are alloted
                        if(in_array('practicalActivity',$arr_optional_exam_type)) {
                            $arr_pending_practical_activity_list = $this->mainModel->getCandidatePendingQuestionCountByBatch($tb_id,'tbl_practical_activity_answers');
                            //echo "<br> str ".$this->db->last_query();exit;
                            if($arr_pending_practical_activity_list > 0) {
                                $err_message .= "Marks awarding is pending for ".$arr_pending_practical_activity_list." practical activity questions \n";
                            }
                        }
                        if(in_array('viva',$arr_optional_exam_type)) {
                            $arr_pending_viva_list = $this->mainModel->getCandidatePendingQuestionCountByBatch($tb_id,'tbl_viva_answers');
                            if($arr_pending_viva_list > 0) {
                                $err_message .= "Marks awarding is pending for ".$arr_pending_viva_list." viva questions \n";
                            }
                        }                        
                    
                        //Check whether all the candidates student_assessment_status is marked as Completed
                        /*$arr_candidate_assessment_status_pending = $this->mainModel->getCandidateAssessmentStatusPendingCount($tb_id);
                        if($arr_candidate_assessment_status_pending > 0) { //Dont allow to mark as completed
                            $err_message .= "Assesssment Status is not completed for all the candidates in batch\n";
                        }*/

                        //check wheter all the candidates student_attendance is marked as Present/Absent
                        $arr_batch_students_attendance_list = $this->mainModel->getAssessorBatchCandidateAttendenceDetails($assessor_id,$tb_id);
                        if($arr_batch_students_attendance_list != false) {
                            $err_message .= "Attendance Status is not marked for all the candidates in batch\n";
                        } 
                        //Check whether all mandatory documents are uploaded
                        $arr_checklist_documents_master_list = $this->mainModel->getAllRecords('tbl_assessment_checklist_documents_master',array("document_requirement" => 'Mandatory'));
                        if($arr_checklist_documents_master_list != false) {
                            foreach($arr_checklist_documents_master_list as $checklist_master_data) {
                                $arr_checklist_documents_master_ids[$checklist_master_data['acdm_id']] = $checklist_master_data['acdm_id'];
                            }

                            if(count($arr_checklist_documents_master_ids) > 0) { 
                                $uploaded_checklist_documents_count = $this->mainModel->getBatchChecklistDocumentsUploadedCount($tb_id);
                                //echo "<br> str ".$this->db->last_query();
                                //echo "<br> uploaded_checklist_documents_count ".$uploaded_checklist_documents_count;
                                //echo "<br> count ".count($arr_checklist_documents_master_ids);
                                //exit;
                                if($uploaded_checklist_documents_count < count($arr_checklist_documents_master_ids)) {
                                    $err_message .= ($err_message != "") ? ".Upload all the mandatory assessment documents to update the batch status as completed \n" : "Upload all the mandatory assessment documents to update the batch status as completed \n";
                                }
                            }
                        }    
                    }
                    
                    if($err_message == "") {
                        $updData['tb_assessment_status'] = $tb_assessment_status;
                                        
                        $this->db->where('tb_id', $tb_id);
                        $this->db->update('tbl_training_batches', $updData);  

                        //Mark candidate assessment status as completed if tb_assessment_status = completed
                        $stdUpdData['student_assessment_status'] = 'Completed';

                        $this->db->where('tb_id', $tb_id);
                        $query = $this->db->update('tbl_students', $stdUpdData);  
                        
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['assessor_id'] = id_encode($assessor_id);
                        $data['unique_token'] = $token;
                        $data['message'] = 'Batch Assessment Status saved successfully';
                    }
                    else {
                        $data['status'] = false;
                        $data['rcode'] = 500;
                        $data['message'] = $err_message;
                    }
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'tb_assessment_status OR tb_id is null';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function saveCandidateTheoryMarks()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $student_id = $this->input->post('student_id');
            $question_list = json_decode($this->input->post('question_list'),true);
            /*echo "<pre>";
            print_r($_POST);
            echo "</pre>";
            exit;*/
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($student_id > 0 && count($question_list) > 0) {
                    $arr_candidate_list = $this->mainModel->getAllRecords('tbl_students',array("student_id" => $student_id));
                    if($arr_candidate_list != false) {
                        $practical_activity_questions =  $arr_candidate_list[0]['practical_activity_questions'];
                        $viva_questions = $arr_candidate_list[0]['viva_questions'];
                    }
                
                    foreach($question_list as $details) {
                        $qid = trim($details['qid']);
                        $ans = strtolower(trim($details['option']));
                        
                        //Save the questions in tbl_theory_answers table
                        $updData['ans'] = $ans;
                        $updData['save_type'] = ($ans != "" ) ? "Save" : "NA";
                        $updData['modified_dts'] = date('Y-m-d H:i:s');
                        
                        /*echo "<pre>";
                        print_r($updData);
                        echo "</pre>";*/
    
                        $this->db->where('student_id', $student_id);
                        $this->db->where('tb_id', $tb_id);
                        $this->db->where('qid', $qid);
                        $this->db->update('tbl_theory_answers', $updData); 
                        
                        if($practical_activity_questions != "" ) {
                            $updCandidateData['student_assessment_status'] = "Practical Activity";
                        }
                        else if($viva_questions != "" ) {
                            $updCandidateData['student_assessment_status'] = "Viva";
                        }
                        else {
                            $updCandidateData['student_assessment_status'] = "Completed";
                        }
                        $updCandidateData['theory_submission_dts'] = date('Y-m-d H:i:s');
                        
                        $this->db->where('student_id', $student_id);
                        $this->db->where('tb_id', $tb_id);
                        $this->db->update('tbl_students', $updCandidateData); 
                        
                        $data['status'] = true;
                        $data['rcode'] = 200;
                        $data['assessor_id'] = id_encode($assessor_id);
                        $data['unique_token'] = $token;
                        $data['message'] = 'Theory Assessment Submitted successfully';
                    }
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Student Id OR student_assessment_status OR Qid OR Marks is missing';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function saveBatchExpense()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            
            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $ted_id = $this->input->post('ted_id');
            $travel_date = date('Y-m-d',strtotime($this->input->post('travel_date')));
            $mode = $this->input->post('mode');
            $travel_from = $this->input->post('travel_from');
            $travel_to = $this->input->post('travel_to');
            $travel_amount = $this->input->post('travel_amount');
            $breakfast = $this->input->post('breakfast');
            $lunch = $this->input->post('lunch');
            $dinner = $this->input->post('dinner');
            $hotel_stay = $this->input->post('hotel_stay');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($tb_id > 0 && $travel_date != "") {
                    $printing_charges = 0;
                    $courier_charges = 0;
                    $professional_charges = 0;
                    $advance_amount = 0;
                    
                    //Check if already expense is added 
                    $arr_expense_list = $this->mainModel->getAllRecords('tbl_batch_expenses',array("tb_id" => $tb_id));
                    if($arr_expense_list != false) {
                        $be_id = $arr_expense_list[0]['be_id'];
                        $printing_charges = $arr_expense_list[0]['printing_charges'];
                        $courier_charges = $arr_expense_list[0]['courier_charges'];
                        $professional_charges = $arr_expense_list[0]['professional_charges'];
                        $advance_amount = $arr_expense_list[0]['advance_amount'];
                    }
                    else {
                        $insData = array(
                            'tb_id' => $tb_id,
                        );

                        $this->db->insert('tbl_batch_expenses', $insData);
                        $be_id = $this->db->insert_id();
                        
                        $updBatch['be_id'] = $be_id;
                        $this->db->where('tb_id', $tb_id);
                        $this->db->update('tbl_training_batches', $updBatch); 
                    }
                    
                    //Insert into tbl_batch_expense_details
                    $insDetailsData = array(
                        'be_id' => $be_id,
                        'travel_date' => $travel_date,
                        'mode' => $mode,
                        'travel_from' => $travel_from,
                        'travel_to' => $travel_to,
                        'travel_amount' => $travel_amount,
                        'breakfast' => $breakfast,
                        'lunch' => $lunch,
                        'dinner' => $dinner,
                        'hotel_stay' => $hotel_stay,
                    );
                    
                    if($ted_id == 0) {
                        $this->db->insert('tbl_batch_expense_details', $insDetailsData);
                    }
                    else {
                        $this->db->where('ted_id', $ted_id);
                        $this->db->update('tbl_batch_expense_details', $insDetailsData); 
                    }
                    
                    $total_travel_charges = $this->mainModel->getTotalTravelChargesByBatch($be_id);
                    $grand_total = $total_travel_charges + $printing_charges + $courier_charges + $professional_charges; 
                    
                    //calculate total travel charges and update in tbl_batch_expenses 
                    $updExpenses['total_travel_charges'] = $total_travel_charges;
                    $updExpenses['grand_total'] = $grand_total;
                    $updExpenses['total_amount_due'] = ($grand_total - $advance_amount);
                    
                    $this->db->where('tb_id', $tb_id);
                    $this->db->update('tbl_batch_expenses', $updExpenses); 
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Expense saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please send mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function getAssessorBatchExpenseList(){
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $assessor_batch_expenses_data = array();
            $total_travel_expenses = 0;
            $total_food_stay_expenses = 0;
            $total_other_expenses = 0;
            $grand_total = 0;
            $folderPath = base_url().$this->config->item('assessors_expenses_documents_path');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {

                //Get expense details
                $arr_assessor_expenses_list = $this->mainModel->getExpenseDetailsByBatch($tb_id);
                if ($arr_assessor_expenses_list != false) {
                    foreach($arr_assessor_expenses_list as $exp_data) {
                        $arr_batch_expenses_details['be_id'] = $exp_data['be_id'];
                        $arr_batch_expenses_details['ted_id'] = $exp_data['ted_id'];
                        $arr_batch_expenses_details['travel_date'] = date('d-m-Y',strtotime($exp_data['travel_date']));
                        $arr_batch_expenses_details['mode'] = $exp_data['mode'];
                        $arr_batch_expenses_details['travel_from'] = $exp_data['travel_from'];
                        $arr_batch_expenses_details['travel_to'] = $exp_data['travel_to'];
                        $arr_batch_expenses_details['travel_amount'] = ($exp_data['travel_amount'] > 0) ? $exp_data['travel_amount'] : "";
                        $arr_batch_expenses_details['breakfast'] = ($exp_data['breakfast'] > 0) ? $exp_data['breakfast'] : "";
                        $arr_batch_expenses_details['lunch'] = ($exp_data['lunch'] > 0) ? $exp_data['lunch'] : "";
                        $arr_batch_expenses_details['dinner'] = ($exp_data['dinner'] > 0) ? $exp_data['dinner'] : "";
                        $arr_batch_expenses_details['hotel_stay'] = ($exp_data['hotel_stay'] > 0) ? $exp_data['hotel_stay'] : "";
                        $arr_batch_expenses_details['travel_file'] = ($exp_data['travel_file'] != "") ? $folderPath.$exp_data['travel_file'] : "";
                        $arr_batch_expenses_details['breakfast_file'] = ($exp_data['breakfast_file'] != "") ? $folderPath.$exp_data['breakfast_file'] : "";
                        $arr_batch_expenses_details['lunch_file'] = ($exp_data['lunch_file'] != "") ? $folderPath.$exp_data['lunch_file'] : "";
                        $arr_batch_expenses_details['dinner_file'] = ($exp_data['dinner_file'] != "") ? $folderPath.$exp_data['dinner_file'] : "";
                        $arr_batch_expenses_details['hotel_stay_file'] = ($exp_data['hotel_stay_file'] != "") ? $folderPath.$exp_data['hotel_stay_file'] : "";
                        
                        array_push($assessor_batch_expenses_data,$arr_batch_expenses_details);
                        
                        $total_travel_expenses += $exp_data['travel_amount'];
                        $total_food_stay_expenses += ($exp_data['breakfast'] + $exp_data['lunch'] + $exp_data['dinner'] + $exp_data['hotel_stay']);
                    }
                    
                    $total_other_expenses = $arr_assessor_expenses_list[0]['printing_charges'] + $arr_assessor_expenses_list[0]['courier_charges'];
                    
                    $grand_total = $total_travel_expenses + $total_food_stay_expenses +  $total_other_expenses + $arr_assessor_expenses_list[0]['professional_charges'];
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['assessor_batch_expenses_data'] = $assessor_batch_expenses_data;
                    $data['total_travel_charges'] = ($arr_assessor_expenses_list[0]['total_travel_charges'] > 0) ? $arr_assessor_expenses_list[0]['total_travel_charges'] : 0;
                    $data['total_travel_expenses'] = ($total_travel_expenses > 0) ? $total_travel_expenses : 0;
                    $data['total_food_stay_expenses'] = ($total_food_stay_expenses > 0) ? $total_food_stay_expenses : 0;
                    $data['total_other_expenses'] = ($total_other_expenses > 0) ? $total_other_expenses : 0;
                    $data['printing_charges'] = ($arr_assessor_expenses_list[0]['printing_charges']) ? $arr_assessor_expenses_list[0]['printing_charges'] : 0;
                    $data['courier_charges'] = ($arr_assessor_expenses_list[0]['courier_charges']) ? $arr_assessor_expenses_list[0]['courier_charges'] : 0;
                    $data['professional_charges'] = ($arr_assessor_expenses_list[0]['professional_charges']) ? $arr_assessor_expenses_list[0]['professional_charges'] : 0;
                    $data['grand_total'] = $grand_total;
                    $data['advance_amount'] = ($arr_assessor_expenses_list[0]['advance_amount']) ? $arr_assessor_expenses_list[0]['advance_amount'] : 0;
                    $data['total_amount_due'] = $grand_total - $arr_assessor_expenses_list[0]['advance_amount'];
                    $data['expense_status'] = $arr_assessor_expenses_list[0]['expense_status'];
                    $data['assessor_comments'] = ($arr_assessor_expenses_list[0]['assessor_comments'] != "") ? $arr_assessor_expenses_list[0]['assessor_comments'] : "";
                    $data['comments'] = ($arr_assessor_expenses_list[0]['comments'] != "") ? $arr_assessor_expenses_list[0]['comments'] : "";
                    $data['paid_receipt_file'] = ($arr_assessor_expenses_list[0]['paid_receipt_file'] != "") ? $folderPath.$arr_assessor_expenses_list[0]['paid_receipt_file'] : "";
                    $data['paid_date'] = ($arr_assessor_expenses_list[0]['paid_date'] != "") ? date('d-m-Y',strtotime($arr_assessor_expenses_list[0]['paid_date'])) : ""; 
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'No Expenses added yet';
                }
            } else {
                $data = $tokenRes;
            }    
        } else {
            $data = $userRes;
        }	
     	echo json_encode($data);
    }
    
    public function deleteBatchExpense() {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];

            $token =  $arr_assessor_list->unique_token;
            $ted_id = $this->input->post('ted_id');
            $be_id = $this->input->post('be_id');
            $files = array();
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($ted_id > 0) {
                    //Delete uploaded files 
                    $arr_expense_details = $this->mainModel->getAllRecords('tbl_batch_expense_details',array("ted_id" => $ted_id));
                    if($arr_expense_details != false) {
                        foreach ($arr_expense_details as $details) {
                            if($details['travel_file'] != "") {
                                $files[] = $details['travel_file'];
                            }
                            if($details['breakfast_file'] != "") {
                                $files[] = $details['breakfast_file'];
                            }
                            if($details['lunch_file'] != "") {
                                $files[] = $details['lunch_file'];
                            }
                            if($details['dinner_file'] != "") {
                                $files[] = $details['dinner_file'];
                            }
                            if($details['hotel_stay_file'] != "") {
                                $files[] = $details['hotel_stay_file'];
                            }
                        }
                    }
                    /*echo "<pre>";
                    print_r($files);
                    echo "</pre>";*/
                    
                    if(count($files) > 0) {
                        $folderPath = $this->config->item('assessors_expenses_documents_path');
                        // Loop through the files and delete each one
                        foreach ($files as $file) {
                            if (is_file($folderPath.$file)) {
                                unlink($folderPath.$file);
                            }
                        }
                    }
                    
                    $this->db->where('ted_id', $ted_id);
                    $result=$this->db->delete('tbl_batch_expense_details');
                    
                    //calculate total travel charges and update in tbl_batch_expenses 
                    $updExpenses['total_travel_charges'] = $this->mainModel->getTotalTravelChargesByBatch($be_id);
                    $this->db->where('be_id', $be_id);
                    $this->db->update('tbl_batch_expenses', $updExpenses); 
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Expense detail deleted successfully';
                }   
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Invalid Data';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function createBatchExpenseDetailId()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            
            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $be_id = 0;
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                //Check if already expense is added 
                $arr_expense_list = $this->mainModel->getAllRecords('tbl_batch_expenses',array("tb_id" => $tb_id));
                if($arr_expense_list != false) {
                    $be_id = $arr_expense_list[0]['be_id'];
                }
                else {
                    $insData = array(
                        'tb_id' => $tb_id,
                    );

                    $this->db->insert('tbl_batch_expenses', $insData);
                    $be_id = $this->db->insert_id();
                    
                    $updBatch['be_id'] = $be_id;
                    $this->db->where('tb_id', $tb_id);
                    $this->db->update('tbl_training_batches', $updBatch); 
                }
                
                if($be_id > 0) {
                    $insDetailsData = array(
                        'be_id' => $be_id,
                    );

                    $this->db->insert('tbl_batch_expense_details', $insDetailsData);
                    $ted_id = $this->db->insert_id();
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['ted_id'] = $ted_id;
                    $data['be_id'] = $be_id;
                    $data['message'] = 'Success';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please send mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function saveBatchExpenseFile()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            
            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $be_id = $this->input->post('be_id');
            $ted_id = $this->input->post('ted_id');
            $document_file_type = $this->input->post('document_file_type');
            $document_name = $this->input->post('document_name');
            
            $arr_batch_details = $this->mainModel->getBatchDetails($tb_id);
            if($arr_batch_details != false) {
                $batch_id = str_replace(array("/", " "),"-",$arr_batch_details[0]['batch_id']);
            }
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($tb_id > 0 && $ted_id > 0) {
                    if($document_file_type == 'file') {
                        if(isset($_FILES['file'])) {
                            $insDetailsData[$document_name] = $this->uploadFile('file', 'assessors_expenses', $batch_id.'-'.$ted_id.'-'.$document_name.'-'.date('dmYHis'));
                        }  
                    }
                    else if($document_file_type == 'image') {
                        $insDetailsData[$document_name] = $this->uploadBase64ImageNoThumb($this->input->post('file'), 'assessors_expenses', $batch_id.'-'.$ted_id.'-'.$document_name.'-'.date('dmYHis'));
                    }
                    
                    $this->db->where('ted_id', $ted_id);
                    $this->db->update('tbl_batch_expense_details', $insDetailsData); 
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'File saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please send mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function saveBatchOtherExpense()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            
            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $be_id = $this->input->post('be_id');
            $printing_charges = $this->input->post('printing_charges');
            $courier_charges = $this->input->post('courier_charges');
            $professional_charges = $this->input->post('professional_charges');
            $advance_amount = intval($this->input->post('advance_amount'));
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($tb_id > 0 && $be_id > 0) {
                    //calculate total travel charges and update in tbl_batch_expenses 
                    $total_travel_charges = $this->mainModel->getTotalTravelChargesByBatch($be_id);
                    $grand_total = intval($total_travel_charges) + intval($printing_charges) + intval($courier_charges) + intval($professional_charges); 
                    
                    $updExpenses['printing_charges'] = $printing_charges;
                    $updExpenses['courier_charges'] = $courier_charges;
                    $updExpenses['professional_charges'] = $professional_charges;
                    $updExpenses['advance_amount'] = $advance_amount;
                    $updExpenses['total_travel_charges'] = $total_travel_charges;
                    $updExpenses['grand_total'] = $grand_total;
                    $updExpenses['total_amount_due'] = $grand_total - $advance_amount;
                    
                    $this->db->where('tb_id', $tb_id);
                    $this->db->update('tbl_batch_expenses', $updExpenses); 
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Other Expenses saved successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please send mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }
    
    public function submitBatchExpense()
    {
        $userRes = $this->checkUserId();
        
        if ($userRes['status'] == true) {
            $assessor_id = $userRes['assessor_id'];
            $arr_assessor_list = $userRes['assessor_details'];
            
            $token =  $arr_assessor_list->unique_token;
            $tb_id = $this->input->post('tb_id');
            $assessor_comments = $this->input->post('assessor_comments');
            
            $tokenRes = $this->checkToken($token);
            if ($tokenRes['status'] == true) {
                if($tb_id > 0) {
                    $updExpenses['expense_status'] = 'Submitted';
                    $updExpenses['assessor_comments'] = $assessor_comments;
                    
                    $this->db->where('tb_id', $tb_id);
                    $this->db->update('tbl_batch_expenses', $updExpenses); 
                    
                    $data['status'] = true;
                    $data['rcode'] = 200;
                    $data['assessor_id'] = id_encode($assessor_id);
                    $data['unique_token'] = $token;
                    $data['message'] = 'Batch Expense submitted successfully';
                }
                else {
                    $data['status'] = false;
                    $data['rcode'] = 500;
                    $data['message'] = 'Please send mandatory fields';
                }
            } else {
                $data = $tokenRes;
            }
        } else {
            $data = $userRes;
        }
        echo json_encode($data);
    }

    public function getEncUserID() {
        $assessor_id = 2;

        $data['assessor_id'] = id_encode($assessor_id);

        echo json_encode($data);
    }

    private function validateAndSaveUploadedVideo($fileKey,$destFolder,$newName,$maxSizeMB = 100) {

        if (
            !isset($_FILES[$fileKey]) ||
            $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK
        ) {
            log_message('error', 'Video upload failed.');
            return false;
        }

        $tmpFile = $_FILES[$fileKey]['tmp_name'];

        /*
        |--------------------------------------------------------------------------
        | Size Validation
        |--------------------------------------------------------------------------
        */
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;

        if ($_FILES[$fileKey]['size'] > $maxSizeBytes) {
            log_message('error', 'Video exceeds size limit.');
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Extension Validation
        |--------------------------------------------------------------------------
        */
        $allowedExtensions = [
            'mp4',
            'mov',
            'avi',
            'mkv',
            'webm'
        ];

        $extension = strtolower(
            pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowedExtensions)) {
            log_message('error', 'Invalid video extension.');
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | MIME Validation
        |--------------------------------------------------------------------------
        */
        $allowedMimes = [
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-matroska',
            'video/webm',
            'application/octet-stream'
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpFile);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            log_message(
                'error',
                'Invalid video MIME type: ' . $mimeType
            );
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Actual Video Using FFProbe
        |--------------------------------------------------------------------------
        */
        if (function_exists('shell_exec')) {

            $ffprobe = @shell_exec(
                'ffprobe -v error -show_format -show_streams ' .
                escapeshellarg($tmpFile) .
                ' 2>&1'
            );

            if (empty($ffprobe)) {
                log_message(
                    'error',
                    'FFProbe validation failed.'
                );
                return false;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Optional ClamAV Scan
        |--------------------------------------------------------------------------
        */
        /*
        if (function_exists('shell_exec')) {

            $scanResult = @shell_exec(
                'clamscan --no-summary ' .
                escapeshellarg($tmpFile) .
                ' 2>&1'
            );

            if (
                !empty($scanResult) &&
                strpos($scanResult, 'FOUND') !== false
            ) {

                log_message(
                    'error',
                    'Virus detected: ' . $scanResult
                );

                return false;
            }
        }
        */

        /*
        |--------------------------------------------------------------------------
        | Create Upload Directory
        |--------------------------------------------------------------------------
        */
        $uploadDir = FCPATH . 'uploads/' . $destFolder . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        /*
        |--------------------------------------------------------------------------
        | Safe File Name
        |--------------------------------------------------------------------------
        */
        $safeName = preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '',
            $newName
        );

        $fileName = $safeName . '.' . $extension;

        $filePath = $uploadDir . $fileName;

        /*
        |--------------------------------------------------------------------------
        | Save Video
        |--------------------------------------------------------------------------
        */
        if (!move_uploaded_file($tmpFile, $filePath)) {

            log_message(
                'error',
                'Failed to save uploaded video.'
            );

            return false;
        }

        return [
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => filesize($filePath)
        ];
    }
    
    
}
