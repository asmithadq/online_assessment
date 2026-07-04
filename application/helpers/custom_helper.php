<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once ('vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * This function is used to generate seo friendly url
 */

function seo_friendly_url($string){
    $string = str_replace(array('[\', \']'), '', $string);
    $string = preg_replace('/\[.*\]/U', '', $string);
    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
    return strtolower(trim($string, '-'));
}

function random_strings($length_of_string)
{
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
 
    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result),0, $length_of_string);
}

function random_numeric($length_of_string)
{
    // String of all alphanumeric character
    $str_result = '0123456789';
 
    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result),0, $length_of_string);
}

function uploadImage($key, $destfolder, $new_name, $watermarkValue = "")
{
    if (
        !isset($_FILES[$key]) ||
        $_FILES[$key]['error'] !== UPLOAD_ERR_OK ||
        empty($_FILES[$key]['name'])
    ) {
        return "default-image.png";
    }

    // Max size 10MB
    $max_size = 10 * 1024 * 1024;

    if ($_FILES[$key]['size'] > $max_size) {
        log_message('error', 'Uploaded file exceeds size limit');
        return "default-image.png";
    }

    $tmp_file = $_FILES[$key]['tmp_name'];

    /*
    |--------------------------------------------------------------------------
    | MIME Validation
    |--------------------------------------------------------------------------
    */
    $allowed_mimes = [
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',

        // PDF
        'application/pdf',

        // DOC
        'application/msword',

        // DOCX
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $tmp_file);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_mimes)) {
        log_message('error', 'Invalid MIME Type: ' . $mime_type);
        return "default-image.png";
    }

    /*
    |--------------------------------------------------------------------------
    | Extension Validation
    |--------------------------------------------------------------------------
    */
    $extension = strtolower(
        pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION)
    );

    $allowed_extensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'pdf',
        'doc',
        'docx'
    ];

    if (!in_array($extension, $allowed_extensions)) {
        log_message('error', 'Invalid file extension');
        return "default-image.png";
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Validation For Images
    |--------------------------------------------------------------------------
    */
    $is_image = in_array(
        $extension,
        ['jpg', 'jpeg', 'png', 'gif']
    );

    if ($is_image) {

        $image_info = @getimagesize($tmp_file);

        if ($image_info === false) {
            log_message('error', 'Invalid image file');
            return "default-image.png";
        }

        if (
            $image_info[0] > 5000 ||
            $image_info[1] > 5000
        ) {
            log_message('error', 'Image dimensions too large');
            return "default-image.png";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Upload Directory
    |--------------------------------------------------------------------------
    */
    $upload_dir = FCPATH . 'uploads/' . $destfolder . '/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Safe File Name
    |--------------------------------------------------------------------------
    */
    $safe_filename = preg_replace(
        '/[^a-zA-Z0-9._-]/',
        '',
        $new_name
    );

    $destination = $upload_dir . $safe_filename;

    /*
    |--------------------------------------------------------------------------
    | Optional ClamAV Scan
    |--------------------------------------------------------------------------
    */
    /*
    if (function_exists('shell_exec')) {

        $scan_result = @shell_exec(
            'clamscan --no-summary ' .
            escapeshellarg($tmp_file) .
            ' 2>&1'
        );

        if (
            !empty($scan_result) &&
            strpos($scan_result, 'FOUND') !== false
        ) {
            log_message('error', 'Virus detected');
            return "default-image.png";
        }
    }
    */

    /*
    |--------------------------------------------------------------------------
    | Move Uploaded File
    |--------------------------------------------------------------------------
    */
    if (!move_uploaded_file($tmp_file, $destination)) {
        log_message('error', 'Failed to move uploaded file');
        return "default-image.png";
    }

    /*
    |--------------------------------------------------------------------------
    | Watermark & Thumbnail (Images Only)
    |--------------------------------------------------------------------------
    */
    if ($is_image && !empty($watermarkValue)) {

        $CI =& get_instance();
        $CI->load->library('image_lib');

        $thumb_dir = $upload_dir . 'thumbs/';

        if (!is_dir($thumb_dir)) {
            mkdir($thumb_dir, 0755, true);
        }

        $config_resize = [
            'image_library'  => 'gd2',
            'source_image'   => $destination,
            'maintain_ratio' => TRUE,
            'width'          => 300,
            'height'         => 200
        ];

        $CI->image_lib->initialize($config_resize);

        if (!$CI->image_lib->resize()) {

            log_message(
                'error',
                $CI->image_lib->display_errors('', '')
            );

        } else {

            $resized_image_path =
                $thumb_dir . $safe_filename;

            copy(
                $destination,
                $resized_image_path
            );
        }

        $CI->image_lib->clear();

        $file_extension = '.' . pathinfo(
            $safe_filename,
            PATHINFO_EXTENSION
        );

        $watermark_file_name = str_replace(
            $file_extension,
            '-watermark' . $file_extension,
            $safe_filename
        );

        $watermark_destination =
            $upload_dir . $watermark_file_name;

        if (copy($destination, $watermark_destination)) {

            watermarkImage(
                $watermarkValue,
                $watermark_destination
            );
        }
    }

    return $safe_filename;
}

function watermarkImage($watermarkValue,$source_image)
{
    // Load the image manipulation library
    $CI =& get_instance();
    $CI->load->library('image_lib');

    // Configuration for text watermark
    $config['source_image'] = $source_image; // Path to the source image
    $config['wm_text'] = $watermarkValue; // Text to be used as the watermark
    $config['wm_type'] = 'text'; // Type of watermark
    $config['wm_font_path'] = './system/fonts/texb.ttf'; // Path to the font file
    $config['wm_font_size'] = 14; // Small font size
    $config['wm_font_color'] = 'ff0000'; // Font color in hexadecimal format (red in this case)
    $config['wm_vrt_alignment'] = 'top'; // Vertical alignment of the watermark
    $config['wm_hor_alignment'] = 'left'; // Horizontal alignment of the watermark
    $config['wm_padding'] = '25'; // Padding around the watermark text

    // Initialize the image manipulation library with the configuration
    $CI->image_lib->initialize($config);

    // Apply watermark
    if (!$CI->image_lib->watermark()) {
        echo $CI->image_lib->display_errors(); // Display any errors
    } else {
        //echo 'Watermark applied successfully.';
    }

    // Clear any cached image data
    $CI->image_lib->clear();
}


function get_email_template_details($template_id){
    $CI = &get_instance();
    
    $query="SELECT * FROM tbl_email_templates where id =".$template_id;
            
    $result=$CI->db->query($query);
	$data=$result->result_array();
	return $data;        
}
  
function get_general_settings_details(){
    $CI = &get_instance();
    
    $query="SELECT * FROM tbl_general_settings";
            
    $result=$CI->db->query($query);
	$data=$result->result_array();
	return $data;        
}

function get_ssc_details($ssc_id){
    $CI = &get_instance();
    
    $query="SELECT * FROM tbl_sector_skill_council WHERE ssc_id = ".$ssc_id;
            
    $result=$CI->db->query($query);
	$data=$result->result_array();
	return $data;        
}

function send_email_smtp($subject,$message,$name,$to){
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    //Get general settings details
    $generalSettingDetails = get_general_settings_details();
    foreach($generalSettingDetails as $details) {
        $smtp_host = $details['smtp_host'];
        $smtp_port = $details['smtp_port'];
        $smtp_user = $details['smtp_user'];
        $smtp_pass = $details['smtp_pass'];
        $smtp_encryption = ""; //$details['smtp_encryption'];
        $email_from = $details['email_from'];

        echo "<br> host ".$smtp_host;
        echo "<br> smtp_port ".$smtp_port;
        echo "<br> smtp_user ".$smtp_user;
        echo "<br> smtp_pass ".$smtp_pass;
        echo "<br> email_from ".$email_from; 
        //exit; 

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $smtp_host;             // SMTP server
            //$mail->SMTPAuth   = true;
            $mail->Username   = $smtp_user;             // SMTP username
            $mail->Password   = $smtp_pass;             // SMTP password
            $mail->SMTPSecure = $smtp_encryption;       // Enable SSL encryption; `tls` also accepted
            $mail->Port       = $smtp_port;             // TCP port to connect to for SSL*/

            //$mail->isMail();  // Use the PHP mail() function

            // Sender and recipient settings
            $mail->setFrom($smtp_user, $email_from); 
            $mail->addAddress($to, $name);
    
            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
    
            // Send the email
            $mail->send();
            echo 'Email has been sent successfully!';
        } catch (Exception $e) {
            echo "Error: {$mail->ErrorInfo}";
        } 
    }
}

function send_email($subject,$message,$name,$to){
    //$to = 'asmitharshetty@gmail.com';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";   
    
    // Custom "From" name and email
    $fromName = 'Hemsen Support Team';
    $fromEmail = 'support@hemsen.online';
    $headers .= "From: $fromName <$fromEmail>" . "\r\n" .
           'Reply-To: support@hemsen.in' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);    

    /*if(mail($to, $subject, $message, $headers)) { 
        echo 'Mail sent successfully.';
    } else {
        echo 'Failed to send mail.';
    }*/
}

function getResponseApi($api_url,$post_data,$return_type = 0){
    /*echo "<pre>";
    print_r($post_data);
    echo "</pre>";
    exit;*/
    $CI = &get_instance();
    
    // Set CURL options
    $curl_options = array(
        CURLOPT_URL            => $api_url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($post_data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true, // Use this if your API uses self-signed SSL certificates (not recommended for production)
    );
    
    // Set the authentication header
    $headers = array();

    // Initialize cURL session
    $ch = curl_init($api_url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Use this if your API uses self-signed SSL certificates (not recommended for production)
    
    // Execute the cURL request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'CURL Error: ' . curl_error($ch);
        $response = array(
            "status"       => false,
            "rcode"        => 500,
            "message"      => 'Curl Technical Error'
        );
        $output = json_encode($response);
    }
    else {
        $arrResponse = ($return_type == 0) ? json_decode($response) : $response;
        /*echo "<pre>";
        print_r($arrResponse);
        echo "</pre>";*/
        
        $output = $arrResponse;
    }
    
    // Close cURL session
    curl_close($ch);
    
    return $output;
    
}

function convertNullToEmptyString(array $array): array {
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            // If the value is an array, recursively call the function
            $value = convertNullToEmptyString($value);
        } elseif ($value === null) {
            // If the value is null, convert it to an empty string
            $value = "";
        }
    }
    unset($value); // Unset reference to the last element
    return $array;
}

//Function to watermark the video
function watermarkVideo($input_file,$output_file,$batch_id,$assessor_code,$lat,$long,$geoaddress,$video_submitted_dts) {
    $font_file = './system/fonts/texb.ttf'; // Path to the TTF font
    $output = "";

    //echo "<br> input_file ".$input_file;
    //echo "<br> output_file ".$output_file;
    //exit;

    // Get the original file size in kilobytes
    $originalFileSize = filesize($input_file) / 1024;
    //echo "<br> originalFileSize ".$originalFileSize;exit;
        
    // Calculate the target file size (50% of the original)
    $targetFileSize = $originalFileSize * 0.75; //$originalFileSize / 2;

    // Get the original bitrate
    $bitrateOutput = [];
    exec("ffmpeg -i $input_file 2>&1 | grep 'bitrate:'", $bitrateOutput);
    preg_match('/bitrate: (\d+) kb\/s/', implode("\n", $bitrateOutput), $matches);
    $originalBitrate = isset($matches[1]) ? (int)$matches[1] : 0;

    //if ($originalBitrate > 0) {
        // Calculate the target bitrate (50% of the original)
        $targetBitrate = $originalFileSize * 0.75; //$originalBitrate / 2;

        // Assuming an average character width in pixels (this is an estimate)
        $average_char_width = 13; // This can vary based on the font and fontsize 
        $average_char_height = 22; // Estimate based on fontsize
        $video_width = 480;
        $line_spacing = 15; // Spacing between lines
        $max_chars_per_line = intval($video_width / $average_char_width);

        $assessor_details = $batch_id." - ".$assessor_code."\f";
        $latLong = "Lat ".$lat.", Long ".$long."\f";
        $text = split_text($geoaddress, $max_chars_per_line);  
        $arr_text = explode("\n", $text);    

        $datetime = $video_submitted_dts;  // Define the start time for the timestamp
        list($date, $time) = explode(' ', $datetime);
        $formattedDate = date('M d, Y',strtotime($datetime));

        // Calculate seconds since midnight for the given time
        list($hours, $minutes, $seconds) = explode(':', $time);
        $startSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
        
        $content = $assessor_details.$latLong.$text;

        // Calculate number of lines
        $lines = substr_count($content, "\f") + 1;
        $box_height = ($lines * $average_char_height) + (($lines - 1) * $line_spacing) + 80; // 40 is padding
        $date_height = $box_height-28;
        $assessor_details_text_height = $date_height-20;
        $latLong_text_height = $assessor_details_text_height-25;
        $text_height = $latLong_text_height-12; 

        /*echo "<br> content ".$content;
        echo "<br> lines ".$lines;
        echo "<br> box_height ".$box_height;    
        echo "<br> date_height ".$date_height;
        echo "<br> assessor_details_text_height ".$assessor_details_text_height;    
        echo "<br> latLong_text_height ".$latLong_text_height;
        echo "<br> text_height ".$text_height;*/
        
        // FFmpeg command
        $cmd = "ffmpeg -i $input_file -vf " .
                "\"format=yuv444p, " .
                "drawbox=y=ih-{$box_height}:color=black@0.4:width=iw:height={$box_height}:t=fill, " . // Dynamic Background box
                "drawtext=fontfile=$font_file:text='$formattedDate %{pts\:gmtime\\:$startSeconds\\:%T}':fontcolor=white:fontsize=18:x=w-tw-5:y=h-{$date_height}-th, " . // Date and Time
                "drawtext=fontfile=$font_file:text='$formattedDate':fontcolor=white:fontsize=18:x=w-tw-5:y=h-{$assessor_details_text_height}-(th/2), " . // Batch and Assessor details
                "drawtext=fontfile=$font_file:text='$formattedDate':fontcolor=white:fontsize=18:x=w-tw-5:y=h-{$latLong_text_height}-(th/2), "; // Latitude and Longitude
                // Address
                if(count($arr_text) > 0) {
                    foreach($arr_text as $textData) {
                        $cmd .= "drawtext=fontfile=$font_file:text='$assessor_details':fontcolor=white:fontsize=18:x=w-tw-5:y=h-{$text_height}-(th/2), ";
                        $text_height = $text_height-20;
                    }
                }
        $cmd .= "format=yuv420p\" " .
                "-c:v libx264 -crf 23 -c:a copy -movflags +faststart $output_file 2>&1";

        /*$cmd    .= "format=yuv420p\" " .
                    "-c:v libx264 -b:v {$targetBitrate}k -bufsize {$targetBitrate}k -maxrate {$targetBitrate}k -c:a aac -b:a 128k -movflags +faststart $output_file 2>&1"; */   

        //echo "<br> cmd ".$cmd;exit;  
        
        echo "<br> output_file ".$output_file;
        
        exec($cmd, $output, $return_var);

        if ($return_var !== 0) {
            $error_message = implode("\n", $output);
            $output = "Error processing video:<br><pre>$error_message</pre>";  
        } else {
            $output = "Success";
            // Delete the original uploaded file
            unlink($input_file);  
        } 
    /*} else {
        $output = "Could not determine the original bitrate.";
    }*/

    //echo "<br> Output ".$output;exit;
    return $output;
}


// Function to split text based on the video width
function split_text($text, $max_chars_per_line) {
    $words = explode(' ', $text);
    $lines = [];
    $current_line = '';

    foreach ($words as $word) {
        if (strlen($current_line . ' ' . $word) <= $max_chars_per_line) {
            $current_line .= ($current_line === '' ? '' : ' ') . $word;
        } else {
            $lines[] = $current_line;
            $current_line = $word;
        }
    }

    if ($current_line !== '') {
        $lines[] = $current_line;
    }

    return implode("\n", $lines); 
}
    

