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
						Question Paper
            			<br>
            			<?php echo $arr_batch_details[0]['trade_name']; ?> -
            				<?php echo $arr_batch_details[0]['trade_code']; ?>
            					</b>
            					<br />
            		</td>
            		<td width="160" align="center" style="font-size:14px; font-family:arial; "> <img src="<?php echo base_url(); ?>assets/admin/images/logo/logo.png" alt="logo" width="120" /> </td>
            	</tr>
            </table>
            <?php
            if(count($arr_qn_type_details) > 0) {
              $arrOptions = array('option_a' => 'a','option_b' => 'b','option_c' => 'c','option_d' => 'd');
              $examTypeKeys = array_keys($arr_qn_type_details);
              $display_header = array('Theory' => 0, 'PracticalActivity' => 0, 'Viva' => 0);
			  $header_text = array('Theory' => 'Theory and Practical Skills', 'PracticalActivity' => 'Practical Activity', 'Viva' => 'Viva');
			  $instructutions_column = array('Theory' => 'theory_instructions', 'PracticalActivity' => 'practical_activity_instructions', 'Viva' => 'viva_instructions');
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
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Scheme : <?php echo $arr_batch_details[0]['scheme_name']."-".$arr_batch_details[0]['subscheme_name']; ?></strong></td>	
								<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">&nbsp;</td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Duration : <?php echo $arr_batch_details[0]['exam_duration_mins']; ?> Mins</strong></td>
                    		</tr>
                    	<?php
                    	}
                    	?>
                    		<tr>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><?php echo ($qp_shuffling !== 'Same') ? '<strong>Candidate ID: '.$students['enrollment_number'].'</strong>' : '&nbsp;' ?></td>	
								<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">&nbsp;</td>
                    			<td align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><?php echo ($examType == 'Theory' && $qp_shuffling !== 'Same') ? '<strong>Candidate Name: '.$students['student_name'].'</strong>' : '&nbsp;' ?></td>
							</tr> 
                    </table>
                <?php 
                }
                ?>
                  
            	<table width="100%" border="0" cellspacing="1" cellpadding="4">
            		<tr>
            			<td colspan="4" align="center"><strong><?php echo $header_text[$examType]; ?> Questions</strong></td>  
            		</tr>
					<tr>
						<td align="left" colspan="2" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Questions : <?php echo count($arr_qn_type_details[$examType]); ?></strong></td>
						<td align="center" colspan="2" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Max Marks : <?php echo $arr_qn_type_marks[$examType]; ?> </strong></td>
					</tr>
            		<tr>
            			<td colspan="4" align="left" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;"><strong>Instructions to Candidate: <?php echo $arr_batch_details[0][$instructutions_column[$examType]]; ?></strong></td> 
            		</tr>
            		<?php      
                  $sl_no = 1;
                  foreach($arr_qn_type_details[$examType] as $qid) {
                    //echo "<br> qid ".$qid;
                      $arrQnsSplit = explode('|lang|',$arr_qns_details[$student_id][$qid]['question']);
                      $question = trim($arrQnsSplit[0]);
                      //echo "<br> question ".$question;
                      $langQuestion = (count($arrQnsSplit) > 1) ? $arrQnsSplit[1] : "";
                    ?>
            			<tr>
            				<td colspan="4" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            					<p><b><?php echo ($sl_no).". ".$question ?> (<?php echo preg_replace('/\.00$/', '', $arr_qns_details[$student_id][$qid]['marks']); ?> Marks)</b></p>
            					<?php
            									if(trim($langQuestion) != "") {
            									?>
            						<p>
            							<?php echo trim($langQuestion); ?>
            						</p>
            						<?php
            									}
            									?>
            				</td>
            			</tr>
            			<?php if($examType == 'Theory') { 
                          foreach($arrOptions as $optCols => $option) {
                            $arrQnsOptionSplit[$optCols] = explode('|lang|',$arr_qns_details[$student_id][$qid][$optCols]);
                            $options[$optCols] =  $arrQnsOptionSplit[$optCols][0];
                            //echo "<br> options ".$options;
                            $langOptions[$optCols] = (count($arrQnsOptionSplit[$optCols]) > 1) ? $arrQnsOptionSplit[$optCols][1] : "";
                          }  
                        ?>
            				<tr style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            					<td width="3%" align="center" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">A)</td>
            					<td width="47%" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            						<p>
            							<?php echo ($options['option_a']); ?>
            						</p>
            						<?php
                              if(trim($langOptions['option_a']) != "") {
                              ?>
            							<p>
            								<?php echo trim($langOptions['option_a']); ?>
            							</p>
            							<?php
                              }
                              ?>
            					</td>
            					<td width="3%" align="center" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">B)</td>
            					<td width="46%" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            						<p>
            							<?php echo ($options['option_b']); ?>
            						</p>
            						<?php
                              if(trim($langOptions['option_b']) != "") {
                              ?>
            							<p>
            								<?php echo trim($langOptions['option_b']); ?>
            							</p>
            							<?php
                              }
                              ?>
            					</td>
            				</tr>
            				<tr>
            					<td align="center" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">C)</td>
            					<td style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            						<p>
            							<?php echo ($options['option_c']); ?>
            						</p>
            						<?php
                              if(trim($langOptions['option_c']) != "") {
                              ?>
            							<p>
            								<?php echo trim($langOptions['option_c']); ?>
            							</p>
            							<?php
                              }
                              ?>
            					</td>
            					<td align="center" style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">D)</td>
            					<td style="border:0px solid #282828; font-size:12px; font-family:arial; color:#111111;">
            						<p>
            							<?php echo ($options['option_d']); ?>
            						</p>
            						<?php
                              if(trim($langOptions['option_d']) != "") {
                              ?>
            							<p>
            								<?php echo trim($langOptions['option_d']); ?>
            							</p>
            							<?php
                              }
                              ?>
            					</td>
            				</tr>
            				<?php } ?>
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
