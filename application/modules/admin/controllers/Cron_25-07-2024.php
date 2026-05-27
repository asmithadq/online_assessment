<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends MY_Controller
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

        $this->load->model('Mdmaster');
        $this->load->model('mainModel');
    }
	
	public function watermarkVideoRecording()
    {
		$this->db->select('student_id,practicalactivity_video_file,practicalactivity_video_lat,practicalactivity_video_lng,practicalactivity_video_geoaddress,practicalactivity_video_watermark_status,practicalactivity_video_submitted_dts,
                            viva_video_file,viva_video_watermark_status,viva_video_lat,viva_video_lng,viva_video_geoaddress,viva_video_watermark_status,viva_video_submitted_dts,
                            batch_id,assessor_code'); 
        $this->db->join('tbl_training_batches', 'tbl_training_batches.tb_id = tbl_students.tb_id');
        $this->db->join('tbl_assessor', 'tbl_assessor.assessor_id = tbl_training_batches.assessor_id');
        $this->db->where('student_assessment_status', 'Completed');
        $this->db->where('practicalactivity_video_watermark_status', 0);
        $this->db->or_where('viva_video_watermark_status', 0);
        $this->db->limit(1);
        $query=$this->db->get('tbl_students');
    	$arr_student_details = $query->result_array();
        //echo "<br> str ".$this->db->last_query();exit; 
    	if(count($arr_student_details) > 0){
    		$student_id = $arr_student_details[0]['student_id'];
            $batch_id = $arr_student_details[0]['batch_id'];
            $assessor_code = $arr_student_details[0]['assessor_code'];

            $practicalactivity_video_file = $arr_student_details[0]['practicalactivity_video_file'];
            $practicalactivity_video_lat = $arr_student_details[0]['practicalactivity_video_lat'];
            $practicalactivity_video_lng = $arr_student_details[0]['practicalactivity_video_lng'];
            $practicalactivity_video_geoaddress = $arr_student_details[0]['practicalactivity_video_geoaddress'];
            $practicalactivity_video_watermark_status = $arr_student_details[0]['practicalactivity_video_watermark_status'];
            $practicalactivity_video_submitted_dts = $arr_student_details[0]['practicalactivity_video_submitted_dts'];

            $viva_video_file = $arr_student_details[0]['viva_video_file'];
            $viva_video_lat = $arr_student_details[0]['viva_video_lat'];
            $viva_video_lng = $arr_student_details[0]['viva_video_lng'];
            $viva_video_geoaddress = $arr_student_details[0]['viva_video_geoaddress'];
            $viva_video_watermark_status = $arr_student_details[0]['viva_video_watermark_status'];
            $viva_video_submitted_dts = $arr_student_details[0]['viva_video_submitted_dts'];

            if($practicalactivity_video_file != "" && $practicalactivity_video_watermark_status == 0) {
                $file = './uploads/student_assessment_videos/'.$practicalactivity_video_file;
                //echo "<br> file ".$file;exit;
                if (file_exists($file)) {
                    // Attempt to watermark the video
                    // Define input and output file names
                    $input_file = $file;
                    $output_file = './uploads/student_assessment_watermarked_videos/'.$practicalactivity_video_file;
                    $font_file = './system/fonts/texb.ttf'; // Path to the TTF font

                    echo "<br> input_file ".$input_file;
                    echo "<br> output_file ".$output_file;
                    //exit;

                    // Assuming an average character width in pixels (this is an estimate)
                    $average_char_width = 13; // This can vary based on the font and fontsize 
                    $video_width = 480;
                    $max_chars_per_line = intval($video_width / $average_char_width);

                    $assessor_details = $batch_id." - ".$assessor_code."\f";
                    $latitude = "Lat ".$practicalactivity_video_lat.", Long ".$practicalactivity_video_lng."\f";
                    $text = $assessor_details.$latitude.$this->split_text($practicalactivity_video_geoaddress, $max_chars_per_line);     

                    $datetime = $practicalactivity_video_submitted_dts;  // Define the start time for the timestamp
                    list($date, $time) = explode(' ', $datetime);

                    // Calculate seconds since midnight for the given time
                    list($hours, $minutes, $seconds) = explode(':', $time);
                    $startSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;  

                    $cmd = "ffmpeg -i $input_file -vf " .
                            "\"format=yuv444p, " .
                            "drawbox=y=ih-168:color=black@0.4:width=iw:height=168:t=fill, " . // Background box
                            "drawtext=fontfile=$font_file:text='$date %{pts\:gmtime\\:$startSeconds\\:%T}':fontcolor=white:fontsize=24:x=20:y=h-140-th, " . // Date and Time (adjusted position)
                            "drawtext=fontfile=$font_file:text='$text':fontcolor=white:fontsize=24:x=(w-tw)/2:y=h-70-(th/2), " . // Additional Text
                            "format=yuv420p\" " .
                            "-c:v libx264 -c:a copy -movflags +faststart $output_file"; 
                    exec($cmd, $output, $return_var);

                    if ($return_var !== 0) {
                        $error_message = implode("\n", $output);
                        echo "<br>Error processing video:<br><pre>$error_message</pre>";
                    } else {
                        echo "<br>Video watermarked successfully.";
                    }
                }
                else {
                    echo "File doesnt exist";
                }
            }
    	}
    }

    //Function to watermark the video
    protected function watermarkVideo($input_file,$output_file,$batch_id,$assessor_code,$lat,$long,$geoaddress,$video_submitted_dts) {
        $font_file = './system/fonts/texb.ttf'; // Path to the TTF font
        $output = "";

        echo "<br> input_file ".$input_file;
        echo "<br> output_file ".$output_file;
        //exit;

        // Assuming an average character width in pixels (this is an estimate)
        $average_char_width = 13; // This can vary based on the font and fontsize 
        $video_width = 480;
        $max_chars_per_line = intval($video_width / $average_char_width);

        $assessor_details = $batch_id." - ".$assessor_code."\f";
        $latitude = "Lat ".$lat.", Long ".$long."\f";
        $text = $assessor_details.$latitude.$this->split_text($geoaddress, $max_chars_per_line);      

        $datetime = $video_submitted_dts;  // Define the start time for the timestamp
        list($date, $time) = explode(' ', $datetime);

        // Calculate seconds since midnight for the given time
        list($hours, $minutes, $seconds) = explode(':', $time);
        $startSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;  

        $cmd = "ffmpeg -i $input_file -vf " .
                "\"format=yuv444p, " .
                "drawbox=y=ih-168:color=black@0.4:width=iw:height=168:t=fill, " . // Background box
                "drawtext=fontfile=$font_file:text='$date %{pts\:gmtime\\:$startSeconds\\:%T}':fontcolor=white:fontsize=24:x=20:y=h-140-th, " . // Date and Time (adjusted position)
                "drawtext=fontfile=$font_file:text='$text':fontcolor=white:fontsize=24:x=(w-tw)/2:y=h-70-(th/2), " . // Additional Text
                "format=yuv420p\" " .
                "-c:v libx264 -c:a copy -movflags +faststart $output_file"; 
        exec($cmd, $output, $return_var);

        if ($return_var !== 0) {
            $error_message = implode("\n", $output);
            $output = "<br>Error processing video:<br><pre>$error_message</pre>";
        } else {
            $output = "<br>Video watermarked successfully.";
        }
        return $output;
    }


    // Function to split text based on the video width
    public function split_text($text, $max_chars_per_line) {
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
	
}
