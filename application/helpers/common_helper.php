<?php
function sys_date()
{
    date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d");
    return $date;
}
function sys_time()
{
    date_default_timezone_set("Asia/Kolkata");
    $time = date("H:i");
    return $time;
}
function password($value)
{
    $md5_value = md5($value);
    $base64_value = base64_encode($md5_value);
    $bin_value = bin2hex($base64_value);
    return ($bin_value);
}

function id_encode($id)
{
    $base64_id = base64_encode($id);
    $bin_id = bin2hex($base64_id);
    return ($bin_id);
}

function id_decode($id)
{
    $bin_decode = pack("H*", $id);
    $base_decode = base64_decode($bin_decode);
    return ($base_decode);
}

function date_conversion($input_date)
{
    $return_date = date("d-M-Y", strtotime($input_date));
    return $return_date;
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function dynamic_no($max_no, $payment_date)
{
    if ($max_no == "") {
        $sno = 1;
        $year = date("y", strtotime($payment_date));
        $month = date("m", strtotime($payment_date));
        $invoice_no = $year . $month . $sno;
        return ($invoice_no);
    } else {
        $sno = $max_no;
        $sno += 1;
        $year = date("y", strtotime($payment_date));
        $month = date("m", strtotime($payment_date));
        $invoice_no = $year . $month . $sno;
        return ($invoice_no);
    }
}

function getDataByVal($getColumn, $tableName, $whereArray)
{
    $ci = &get_instance();
    $ci->load->database();
    foreach ($whereArray as $key => $value) {
        $ci->db->where($key, $value);
    }
    $query = $ci->db->get($tableName);
    if ($query->num_rows() > 0) {
        foreach ($query->result() as $getval) {
            $retValue = $getval->$getColumn;
        }
    } else {
        $retValue = "";
    }
    return $retValue;
}

function getAllDataByVal($tableName, $whereArray)
{
    $ci = &get_instance();
    $ci->load->database();
    foreach ($whereArray as $key => $value) {
        if ($key == "limit") {
            $ci->db->limit($value);
        } elseif ($key == "name") {
            $ci->db->like('name', $value);
        } elseif ($key == "full_name") {
            $ci->db->like('full_name', $value);
        } elseif ($key == "order_by_desc") {
            $ci->db->order_by($value, "DESC");
        } elseif ($key == "order_by_asc") {
            $ci->db->order_by($value, "ASC");
        } elseif ($key == "start_date_order") {
            $ci->db->where('order_date >=', $value);
        } elseif ($key == "end_date_order") {
            $ci->db->where('order_date <=', $value);
        } elseif ($key == "not_in_order_status") {
            $ci->db->where_not_in('order_status',$value);
        } elseif ($key == "in_order_status") {
            $ci->db->where_in('order_status',$value);
        } else {
            $ci->db->where($key, $value);
        }
    }
    $query = $ci->db->get($tableName);
    return $query;
}

function getMaxData($tableName, $whereArray, $getColumn)
{
    $ci = &get_instance();
    $ci->load->database();
    $ci->db->select_max($getColumn);
    foreach ($whereArray as $key => $value) {
        if ($key == "limit") {
            $ci->db->limit($value);
        } elseif ($key == "order_by_desc") {
            $ci->db->order_by($value, "DESC");
        } elseif ($key == "order_by_asc") {
            $ci->db->order_by($value, "ASC");
        } elseif ($key == "start_date_order") {
            $ci->db->where('order_date >=', $value);
        } elseif ($key == "end_date_order") {
            $ci->db->where('order_date <=', $value);
        } else {
            $ci->db->where($key, $value);
        }
    }
    $query = $ci->db->get($tableName);
    if ($query->num_rows() > 0) {
        $queryData = $query->row();
        return $queryData->$getColumn;
    } else {
        return 0;
    }
}

function getAllDataWithoutWhere($tableName)
{
    $ci = &get_instance();
    $ci->load->database();
    $query = $ci->db->get($tableName);
    return $query;
}

function dehliveryToken()
{
    $token = "0055db1893f78292af0ef7949a235b17ff904add";
    return $token;
}


function getSumData($tableName, $whereArray, $getColumn)
{
    $ci = &get_instance();
    $ci->load->database();
    $ci->db->select_sum($getColumn);
    foreach ($whereArray as $key => $value) {
        if ($key == "limit") {
            $ci->db->limit($value);
        } elseif ($key == "order_by_desc") {
            $ci->db->order_by($value, "DESC");
        } elseif ($key == "order_by_asc") {
            $ci->db->order_by($value, "ASC");
        } elseif ($key == "start_date_order") {
            $ci->db->where('order_date >=', $value);
        } elseif ($key == "end_date_order") {
            $ci->db->where('order_date <=', $value);
        } else {
            $ci->db->where($key, $value);
        }
    }
    $query = $ci->db->get($tableName);
    if ($query->num_rows() > 0) {
        $queryData = $query->row();
        return $queryData->$getColumn;
    } else {
        return 0;
    }
}

function validate($data)
{
    $data = trim($data); //remove spaces
    $data = strip_tags($data); //remove xml, html, php tags
    $data = stripslashes($data); //remove backslashes
    return $data;
}

function onlyText($data)
{
    if ($data != "") {
        if (!preg_match("/^[a-zA-Z ]*$/", $data)) {
            $data = false;
        }
    } else {
        $data = false;
    }
    return $data;
}

function onlyNumber($data)
{
    if ($data != "") {
        if (!preg_match("/^[0-9]*$/", $data)) {
            $data = false;
        }
        if (!is_numeric($data)) {
            $data = false;
        }
    } else {
        $data = false;
    }
    return $data;
}

function checkMobileFormate($data)
{
    if ($data != "") {
        $data = (int)$data;
        if (!preg_match("/^[0-9]*$/", $data)) {
            $data = false;
        }
        if (!is_numeric("" . $data . "")) {
            $data = false;
        }
        if (strlen("$data") != 10) {
            $data = false;
        }
    } else {
        $data = false;
    }
    return $data;
}

function validateGSTNumber($data)
{
    if ($data != "") {
        if (strlen($data) != 15) {
            $data = false;
        } else {
            $data = trim($data);
            $data = strip_tags($data);
            $data = stripslashes($data);
        }
    } else {
        $data = false;
    }
    return $data;
}

function checkAadhaarNumber($data)
{
    if ($data != "") {
        if (!preg_match("/^[0-9]*$/", $data)) {
            $data = false;
        }
        if (!is_numeric($data)) {
            $data = false;
        }
        if (strlen("$data") != 12) {
            $data = false;
        }
    } else {
        $data = false;
    }
    return $data;
}

function checkPincode($data)
{
    if ($data != "") {
        if (!preg_match("/^[0-9]*$/", $data)) {
            $data = false;
        }
        if (!is_numeric($data)) {
            $data = false;
        }
        if (strlen("$data") != 6) {
            $data = false;
        }
    } else {
        $data = false;
    }
    return $data;
}

function emailCheck($data)
{
    if ($data != "") {
        $data = filter_var($data, FILTER_SANITIZE_EMAIL);
        if (filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
            $data = false;
        }
    } else {
        $data = false;
    }
    return $data;
}

function checkPassword($data)
{
    if ($data != "") {
        $uppercase = preg_match('@[A-Z]@', $data);
        $lowercase = preg_match('@[a-z]@', $data);
        $number    = preg_match('@[0-9]@', $data);
        $specialChars = preg_match('@[^\w]@', $data);

        if (strlen($data) < 6) {
            return false;
        } else {
            return $data;
        }
    } else {
        return false;
    }
}

function convertMinutesToHoursAndMinutes($minutes) {
    // Calculate hours and remaining minutes
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    // Format the result
    if($hours > 0 && $remainingMinutes > 0) {
        $formattedTime = ($hours > 1) ? sprintf("%d Hr(s) and %d Mins", $hours, $remainingMinutes) : sprintf("%d Hr and %d Mins", $hours, $remainingMinutes);
    }
    else if($hours > 0 && $remainingMinutes <= 0) {
        $formattedTime = ($hours > 1) ? sprintf("%d Hr(s)", $hours) : sprintf("%d Hr", $hours);
    }
    else if($hours <= 0 && $remainingMinutes > 0) {
        $formattedTime = sprintf("%d Mins", $remainingMinutes);
    }
    
    return $formattedTime;
}

function convertMinutesToSeconds($minutes) {
    // Calculate seconds
    $seconds = $minutes * 60; // Convert minutes to seconds
    
    return $seconds;
}



