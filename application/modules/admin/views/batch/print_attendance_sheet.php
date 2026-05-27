<style>
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0;
		font-family: Arial, sans-serif;
    }
   /* Clear floats after the columns */
    .row:after {
        content: "";
        display: table;
        clear: both;
		padding:5px;
    }
.style1 {float: left; font-family: Arial, sans-serif; font-size: 11px; border-bottom: #000000 1px solid; border-top: #000000 1px solid;  border-left: #000000 1px solid; border-right: #000000 1px solid; font-weight: bold; padding:2px;}
.style2 {float: left; font-family: Arial, sans-serif; font-size: 11px; border-bottom: #000000 1px solid; border-top: #000000 1px solid; border-left: #000000 1px solid; border-right: #000000 1px solid; font-weight: normal; padding:2px;}
</style>

<table width="100%" border="0" align="center" style="font-size:11px;">
    <tr>
        <td width="65%">Batch ID:<strong><?php echo $arr_students[0]['batch_id']; ?></strong></td>
        <td width="35%"> Date: <?php echo date('d-m-Y',strtotime($arr_batch_details[0]['tb_assessment_date'])); ?></td>
    </tr>
    <tr>
      <td>JobRole Name: <strong><?php echo $arr_batch_details[0]['trade_name']; ?>(<?php echo $arr_batch_details[0]['trade_code']; ?>)</strong></td>
      <td>Scheme: <strong><?php echo $arr_batch_details[0]['scheme_name']."-".$arr_batch_details[0]['subscheme_name'] ?></strong></td>
    </tr>
    <tr>
        <td>Name of  Training Center : <strong><?php echo $arr_batch_details[0]['tc_code']." -".$arr_batch_details[0]['tc_name'] ?></strong></td>
        <td>Name of  Training Provider: <strong><?php echo $arr_batch_details[0]['tp_name']; ?></strong></td>
    </tr>
    <tr>
        <td colspan="2">Training Center Address: <strong><?php echo $arr_batch_details[0]['center_address'] ?></strong></td>
    </tr>
    <tr>
        <td width="65%"></td>
        <td></td>
    </tr>
</table>
<div class="row">
  <div class="style1" style="width:5%;  height:20px;"><strong>S.No</strong></div>
    <div class="style1" style="width:19%; height:20px;">Candidate ID.</div>
    <div class="style1" style="width:19%; height:20px;">Aadhar No.</div>
    <div class="style1" style="width:30%; height:20px;">Student Name</div>
    <div class="style1" style="width:20%; height:20px;">Signature</div>
    <?php
    $i = 1;
    foreach ($arr_students as $key => $student_list) {
    ?>
        <div class="style2" style="width:5%; height:20px; text-align: center;"><?php echo $i; ?></div>
        <div class="style2" style="width:19%; height:20px;"><b><?php echo $student_list['enrollment_number']; ?></b></div>
        <div class="style2" style="width:19%; height:20px;"><strong><?php echo ($student_list['aadhar_number'] != "") ? $student_list['aadhar_number'] : ""; ?></strong></div>
        <div class="style2" style="width:30%; height:20px;"><?php echo ucwords(strtolower($student_list['student_name'])); ?></div>
        <div class="style2" style="width:20%; height:20px;">&nbsp;</div>
        <?php
        if(($i) % 35 == 0 && count($arr_students) != $i) {
        ?>
            <pagebreak></pagebreak>
        <?php
        }
        $i++;
    }
    ?>
</div><br>

<table cellspacing="0" cellpadding="0" align="right" width="100%">
    <tr>
        <td valign="top" align="left" style="font-size:11px;" height="50" width="74%"><strong>Assessor Name: <br />
                <br />
                Assessor ID:</strong></td>
        <td width="26%" align="left" valign="top" style="font-size:11px;"><strong>Center Head Name<br />    
                <br />
                <br />
                Center Head Signature &amp; Seal</strong></td>
    </tr>
    <tr>
      <td valign="top" align="left" style="font-size:11px; line-height:2.0" height="40"><p><strong>Total Candidates:</strong></p>
       <p><strong>Total Present Candidates:</strong></p>
       <p><strong>Total Absent Candidates:</strong></p>
        <p><strong>Total Dropout Candidates:</strong></p></td>
      <td align="left" valign="top" style="font-size:11px;">&nbsp;</td>
    </tr>
</table>


