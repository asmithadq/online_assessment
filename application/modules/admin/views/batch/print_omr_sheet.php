<?php
$student_count = 0;
foreach($arr_batch_student_details as $student_details) {
?>
    <div style="display: flex;">
    	<div style="width: 33.33%;float: left; text-align: left;"> <img src="<?php echo base_url().$this->config->item('ssc_logo_path').$arr_batch_details['ssc_logo']; ?>" alt="SSC Logo" width="125" /> </div>
    	<div style="width: 33.33%; text-align: center; vertical-align: bottom; float: left;">
			<h3 align="center" style="margin:1px;"><u><?php echo $arr_batch_details['ag_name']; ?></u></h3>
			<img src="<?php echo base_url(); ?>assets/admin/images/OMR_head.jpg" alt="Head" width="125" height="30" /> 
    	</div>
    		<div style="width: 33.33%;float: right; text-align: right;"> <img src="<?php echo base_url(); ?>assets/admin/images/logo/hemsenlogo.png" alt="Hemsen Logo" width="125" /> 
    	</div>
    </div>
    		
    <div style="display: flex;">
    	<div style="width: 60%; float: left; text-align: left; line-height: 20px;">
    		<span style="font-size: 12px;">Candidate ID: <?php echo $student_details['enrollment_number']; ?></span><br>
    		<span style="font-size: 12px;">Candidate Name: <?php echo $student_details['student_name']; ?></span><br>
    		<span style="font-size: 12px;">Batch ID: <?php echo $arr_batch_details['batch_id'] ?></span><br>
    		<span style="font-size: 12px;">Trade/QPName: <?php echo $arr_batch_details['trade_code']."-".$arr_batch_details['trade_name'] ?></span>
    		
    	</div>
    	<div style="width: 40%; float: right; text-align: left; line-height: 20px;">
    		<span style="font-size: 12px;">Assessment Date: <?php echo date('d-m-Y',strtotime($arr_batch_details['tb_assessment_date'])); ?></span><br>
    		<span style="font-size: 12px;">Center Location: <?php echo $arr_batch_details['tc_code']." -".$arr_batch_details['tc_name'] ?>
    		                                                <?php echo ($arr_batch_details['center_address'] != "") ? "-".$arr_batch_details['center_address'] : ""; ?>
    		                                                </span><br>
    		<span style="font-size: 12px;">Scheme: <?php echo $arr_batch_details['scheme_name']."-".$arr_batch_details['subscheme_name'] ?></span>                                                
    	</div>
    </div>
    <div style="width: 100%; text-align: center;"> 
    	<p style="font-weight: bold;">Mark your Answer</p>
    	<img src="<?php echo base_url(); ?>assets/admin/images/OMR150.jpg" width="595px">
    	</div>
    	<div style="display: flex; margin-top: 80px;">
        <div style="width: 33.33%; text-align: center; float: left; font-size:12px; border-top:1px #000000 solid; height: 60"><strong>Candidate Signature</strong></div>
        <div style="width: 33.33%; text-align: center; float: left; font-size:12px; border-top:1px #000000 solid; border:1px #000000 solid; height: 60"><strong>Assessor Signature</strong></div>
    		<div style="width: 33.33%; text-align: center; float: left; font-size:12px; border-top:1px #000000 solid; border:1px #000000 solid; height: 60"><strong>Stamp Seal of Training Center</strong></div>
    </div>
<?php 
    $student_count++;
    if($student_count < count($arr_batch_student_details)) {
        echo "<pagebreak />"; //Page break
    }
} ?>    
		