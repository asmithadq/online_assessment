<?php
$student_count = 0;
if($arr_batch_students != false) {
    foreach($arr_batch_students as $students) {
        $student_id = $students['student_id'];
        if($student_count < count($arr_batch_students)) {
?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #333;">
            	<tr>
            		<td width="103" align="right" valign="top">
            			<?php
                                    if($arr_batch_details[0]['ssc_logo'] !== "" && file_exists('./uploads/ssc_logo/'.$arr_batch_details[0]['ssc_logo'])) {
                                    ?> <img src="<?php echo base_url(); ?>uploads/ssc_logo/<?php echo $arr_batch_details[0]['ssc_logo']; ?>" alt="logo" width="120" />
            				<?php
                                    }
                                    ?>
            		</td>
            		<td width="319" align="center" style="font-size:12px; font-family:arial; font-weight: bold">
						<h3 align="center"><u><?php echo $arr_batch_details[0]['ag_name']; ?></u></h3>
						Answer Key
            			<br>
            			<?php echo $arr_batch_details[0]['trade_name']; ?> -
            				<?php echo $arr_batch_details[0]['trade_code']; ?>
            					</b>
            					<br />
            		</td>
            		<td width="160" align="center" style="font-size:14px; font-family:arial; "> <img src="<?php echo base_url(); ?>assets/admin/images/logo/hemsenlogo.png" alt="logo" width="120" /> </td>
            	</tr>
            </table>
            <?php
            if(count($arr_qn_type_details) > 0) {
              $arrOptions = array('option_a' => 'a','option_b' => 'b','option_c' => 'c','option_d' => 'd');
              $examTypeKeys = array_keys($arr_qn_type_details);
              $display_header = array('Theory' => 0, 'PracticalActivity' => 0, 'Viva' => 0);
              foreach($examTypeKeys as $examType) {
                  //echo "<br> Exam Type ".$examType;
                
                if($display_header[$examType] == 0) {
                ?>
                    <table width="100%" border="0" cellspacing="1" cellpadding="1">
                    	<?php
                    	if($examType == 'Theory') {
                    	?>
                    		<tr>
                    			<td width="57%" align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Batch ID: <?php echo $arr_batch_details[0]['batch_id']; ?></strong></td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">&nbsp;</td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Assessment Date: <?php echo date('d-m-Y',strtotime($arr_batch_details[0]['tb_assessment_date'])); ?></strong></td>
                    		</tr>
                    		<tr>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><?php echo ($qp_shuffling !== 'Same') ? '<strong>Candidate ID: '.$students['enrollment_number'].'</strong>' : '&nbsp;' ?></td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">&nbsp;</td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Duration : <?php echo $arr_batch_details[0]['exam_duration_mins']; ?> Mins</strong></td>
                    		</tr>
                    	<?php
                    	}
                    	?>
                    		<tr>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><?php echo ($examType == 'Theory' && $qp_shuffling !== 'Same') ? '<strong>Candidate Name: '.$students['student_name'].'</strong>' : '&nbsp;' ?></td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">&nbsp;</td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Questions : <?php echo count($arr_qn_type_details[$examType]); ?></strong></td>
                    		</tr>
                    		<tr>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Scheme : <?php echo $arr_batch_details[0]['scheme_name']."-".$arr_batch_details[0]['subscheme_name']; ?></strong></td>
                    			<td width="10%" align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">&nbsp;</td>
                    			<td width="33%" align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Max Marks : <?php echo $arr_qn_type_marks[$examType]; ?> </strong></td>
                    		</tr>
                    </table>
                <?php
                }
                ?>
                  
            	<table width="100%" border="0" cellspacing="1" cellpadding="4">
            		<tr>
            			<td colspan="4" align="center"><strong><?php echo $examType; ?> Questions</strong></td>
            		</tr>
            		<?php      
                  $sl_no = 1;
                  foreach($arr_qn_type_details[$examType] as $qid) { 
                    //echo "<br> qid ".$qid;
                      $question = trim($arr_qns_details[$student_id][$qid]['question']);
                      //echo "<br> question ".$question;
                    ?>
            			<tr>
            				<td colspan="4" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            					<p><b><?php echo ($sl_no).". ".$question ?> (<?php echo $arr_qns_details[$student_id][$qid]['marks']; ?> Marks) <span style="font-size:12px; color:#3A9B94;font-weight: bold;">[<?php echo $arr_qns_details[$student_id][$qid]['correct_ans']; ?>]</span></b></p>
            				</td>
            			</tr>
            			<?php
                      	$sl_no++;
                  }
              ?>
            	</table>
            	<?php
              }
            }
            if($qp_shuffling == 'Same') { //Same qns with same order will be saved in to all students of the batch
                $student_count = count($arr_batch_students); //Break the for loop as the questions sequence will be same for all candidates
            }
            else {
                $student_count++;
                if($student_count < count($arr_batch_students)) {
                    echo "<pagebreak />"; //Page break
                }    
            }
        } // End count if    
    } //End candidates for each    
} //End candidates if
?>