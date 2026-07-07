<?php
$arr_scc_details = get_ssc_details($arr_batch_details['ssc_id']);
$ssc_logo = $arr_scc_details[0]['ssc_logo'];

?>
<style>
body {
	font-size: 10px;
	font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif", "DejaVu Sans", "sans-serif";
}

.content-body .container-fluid .row .col-md-6 ul li {
	padding: 1px;
	/* Add padding to list items */
}

.content-body .container-fluid .row .col-md-6 h2 {
	font-size: 1.2rem;
	/* Reduce h2 font size to h4 */
}

table {
	width: 100%;
	border-collapse: collapse;
}

th,
td {
	border: 1px solid black;
	padding: 8px;
	font-size: 10px;
	font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif";
}

th {
	background-color: #f2f2f2;
	font-size: 10px;
	font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif";
}

.img-thumbnail {
	padding: 0.25rem;
	border: 1px solid #000000;
	/* Thin border */
	border-radius: 1.75rem;
	max-width: 100%;
}

.flex-column {
	flex-direction: column !important;
}

.d-inline {
	display: inline !important;
}
</style>
	<?php
	if($student_count  == 1) {
	?>
	<div class="content-body">
        <div class="container-fluid">
			<div class="row" style="display: flex; justify-content: space-between;">
				<div style="width: 50%; float: left; text-align: left;"> <img src="<?php echo base_url().$this->config->item('ssc_logo_path').$ssc_logo; ?>" alt="SSC Logo" width="150" /> </div>
				<div style="width: 50%; float: right; text-align: right;"> <img src="<?php echo base_url(); ?>assets/admin/images/logo/logo.png" alt="Logo" width="150" /> </div>
			</div>
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" style="text-align: center;"><u>Batch <?php echo ($type == 'detailed_pdf') ? "Detailed" : "Basic"; ?> Result Summary</u></h3> </div>
                        </div>
                    <?php
					}
					?>
					<div class="card-body"> 
						<div class="row">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: none; border-collapse: collapse;"> 
								<tbody>
									<tr>
										<td width="54%" style="border: none;">
											<h4><u>Student Information</u></h4>
											<!-- Change h2 to h4 -->
										</td>
										<td width="46%" style="border: none;">
											<h4><u>Assessment Details</u></h4> </td>
									</tr>
									<tr>
										<td style="border: none; vertical-align: top; line-height: 1.5;">
											<ul>
												<li>Candidate ID:
													<?php echo $arr_student_details[$student_id]['enrollment_number']; ?> 
												</li>
												<li>Student Name:
													<?php echo $arr_student_details[$student_id]['student_name']; ?>
												</li>
												<li>Father Name:
													<?php echo $arr_student_details[$student_id]['father_name']; ?>
												</li>
												<li>Date of Birth:
													<?php echo (!empty($arr_student_details[$student_id]['dob'])) ? date('d-m-Y',strtotime($arr_student_details[$student_id]['dob'])) : ""; ?>
												</li>
												<li>Aadhar number:
													<?php echo $arr_student_details[$student_id]['aadhar_number']; ?>
												</li>
												<li>Address:
													<?php echo $arr_student_details[$student_id]['address']; ?>
												</li>
												<li>Geo Location Details: Latitude -
													<?php echo $arr_student_details[$student_id]['lat']; ?>,Longitude -
														<?php echo $arr_student_details[$student_id]['lng']; ?>
												</li>
											</ul>
										</td>
										<td style="border: none; vertical-align: top; line-height: 1.5;">
											<ul>
												<li>Training Partner Name:
													<?php echo $arr_batch_details['tp_name']; ?>
												</li>
												<li>Training Center Name:
													<?php echo $arr_batch_details['tc_name']; ?>
												</li>
												<li>Batch Name:
													<?php echo $arr_batch_details['batch_id']; ?>
												</li>
												<li>Assessment Date:
													<?php echo date('d-m-Y',strtotime($arr_batch_details['tb_assessment_date'])); ?>
												</li>
												<li>Sector Skill Council:
													<?php echo $arr_batch_details['ssc_code']."-".$arr_batch_details['ssc_title']; ?>
												</li>
												<li>Trade/QP Name:
													<?php echo $arr_batch_details['trade_code']."-".$arr_batch_details['trade_name']; ?>
												</li>
											</ul>
											<h4><u>Result Information</u></h4>
											<ul>
												<li>Passing Percentage:
													<?php echo $arr_student_details[$student_id]['pass_percentage']; ?>% </li>
												<li>User Percentage:
													<?php echo $arr_student_details[$student_id]['marks_percentage']; ?>%</li>
												<li>Result:
													<?php echo $arr_student_details[$student_id]['result']; ?>
												</li>
											</ul>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="border: none;">
											<div class="d-inline flex-column"> <strong>Photo:</strong> <img class="img-thumbnail" src="<?php echo base_url().$this->config->item('student_photo_path').$arr_student_details[$student_id]['student_photo']; ?>" alt="Student Photo" height="75" width="75"> <strong>Aadhar Front: <img class="img-thumbnail" src="<?php echo base_url().$this->config->item('aadhaar_filename_path').$arr_student_details[$student_id]['aadhar_front_filename']; ?>" alt="Aadhar Front" height="75" width="75"></strong> <strong>Aadhar Back:</strong> <img class="img-thumbnail" src="<?php echo base_url().$this->config->item('aadhaar_filename_path').$arr_student_details[$student_id]['aadhar_back_filename']; ?>" alt="Aadhar Back" height="75" width="75"> </a>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!--<div class="row">
							<div class="d-inline flex-column"> <strong>Assessment Snapshots:</strong>
								<br>
								<?php
								/*if(array_key_exists($student_id,$arr_student_snapshot_details)) {
									$snapshot_image_path = base_url() . $this->config->item('student_snapshot_photo_path').'thumbs/';
									foreach($arr_student_snapshot_details[$student_id] as $snapshot_image) {
										$snapshot_file = $snapshot_image_path . $snapshot_image;
									?>
											<img class="img-thumbnail" src="<?php echo $snapshot_file; ?>" alt="" height="40" width="40">
								<?php
									}
								}*/
								?>
							</div>
						</div>--> 
						<div class="row">
							<h4>Assessment Details</h4>
							<table width="100%" border="0">
								<tbody>
									<tr>
										<th colspan="4" style="text-align: left">Theory</th>
									</tr>
									<tr>
										<th>Trade/QP Name</th>
										<th>Theory</th>
										<th>Full Score</th>
										<th>User Score</th>
									</tr>
									<?php
										$grand_total_qns = 0;
										$grand_total_theory_qns = 0;
										$grand_total_practical_skill_qns = 0;
										$grand_total_full_score = 0;
										$grand_total_user_score = 0;
					
										if(count($arr_trade_nos_details) > 0) {
											foreach($arr_trade_nos_details as $nos_id => $nos_details) {
												$total_theory_qns = (array_key_exists($student_id,$arr_qn_type_details) && array_key_exists($nos_id,$arr_qn_type_details[$student_id]) && array_key_exists('Theory',$arr_qn_type_details[$student_id][$nos_id])) ? count($arr_qn_type_details[$student_id][$nos_id]['Theory']) : 0;
												$full_score = $nos_details['theory_marks'];
												$user_score = (array_key_exists($student_id,$arr_nos_wise_user_score) && array_key_exists($nos_id,$arr_nos_wise_user_score[$student_id]) && array_key_exists('Theory',$arr_nos_wise_user_score[$student_id][$nos_id])) ? array_sum($arr_nos_wise_user_score[$student_id][$nos_id]['Theory']) : 0;
												
												$grand_total_theory_qns += $total_theory_qns;
												$grand_total_full_score += $full_score;
												$grand_total_user_score += $user_score;
										?>
												<tr>
													<td><?php echo $nos_details['nos_code']; ?> - <?php echo $nos_details['nos_title']; ?></td>
													<td style="text-align: center;"><?php echo $total_theory_qns; ?></td>
													<td style="text-align: center;"><?php echo $full_score; ?></td>
													<td style="text-align: center;"><?php echo $user_score; ?></td>
												</tr>
										<?php
											}
										}
										?>
												<tr>
													<td>&nbsp;</td>
													<td style="text-align: center;"><strong><?php echo $grand_total_theory_qns; ?></strong></td>
													<td style="text-align: center;"><strong><?php echo $grand_total_full_score; ?></strong></td>
													<td style="text-align: center;"><strong><?php echo $grand_total_user_score; ?></strong></td>
												</tr>
								</tbody>
							</table>
							<?php
							if($show_practical_activity == 1 || $show_viva == 1) {
								$grand_total_practical_qns = 0;
								$grand_total_practical_skill_qns = 0;
								$grand_total_practical_activity_qns = 0;
								$grand_total_viva_qns = 0;
								
								$grand_total_practical_full_score = 0;
								$grand_total_practical_user_score = 0;
							?>
							<table width="100%" border="0">
								<tbody>
									<tr>
										<th colspan="7" style="text-align: left">Practical</th>
									</tr>
									<tr>
										<th>Trade/QP Name</th>
										<th>Total Questions</th>
										<th>Practial Skill</th>
										<th>Practical Activity</th>
										<th>Viva</th>
										<th>Full Score</th>
										<th>User Score</th>
									</tr>
									<?php   
									if(count($arr_trade_nos_details) > 0) {
										foreach($arr_trade_nos_details as $nos_id => $nos_details) {
											$total_practical_skill_qns = (array_key_exists($student_id,$arr_qn_type_details) && array_key_exists($nos_id,$arr_qn_type_details[$student_id]) && array_key_exists('PracticalSkill',$arr_qn_type_details[$student_id][$nos_id])) ? count($arr_qn_type_details[$student_id][$nos_id]['PracticalSkill']) : 0;
											$total_practical_activity_qns = (array_key_exists($student_id,$arr_qn_type_details) && array_key_exists($nos_id,$arr_qn_type_details[$student_id]) && array_key_exists('PracticalActivity',$arr_qn_type_details[$student_id][$nos_id])) ? count($arr_qn_type_details[$student_id][$nos_id]['PracticalActivity']) : 0;
											$total_viva_qns = (array_key_exists($student_id,$arr_qn_type_details) && array_key_exists($nos_id,$arr_qn_type_details[$student_id]) && array_key_exists('Viva',$arr_qn_type_details[$student_id][$nos_id])) ? count($arr_qn_type_details[$student_id][$nos_id]['Viva']) : 0;
											$total_qns = $total_practical_skill_qns + $total_practical_activity_qns + $total_viva_qns;
											
											$practical_skill_full_score = $nos_details['practical_skill_marks'];
											$practical_activity_full_score = $nos_details['practical_marks'];
											$viva_full_score = $nos_details['viva_marks'];
											$practical_full_score = $practical_skill_full_score + $practical_activity_full_score + $viva_full_score;
													
											$practical_skill_user_score = (array_key_exists($student_id,$arr_nos_wise_user_score) && array_key_exists($nos_id,$arr_nos_wise_user_score[$student_id]) && array_key_exists('PracticalSkill',$arr_nos_wise_user_score[$student_id][$nos_id])) ? array_sum($arr_nos_wise_user_score[$student_id][$nos_id]['PracticalSkill']) : 0;
											$practical_activity_user_score = (array_key_exists($student_id,$arr_nos_wise_user_score) && array_key_exists($nos_id,$arr_nos_wise_user_score[$student_id]) && array_key_exists('PracticalActivity',$arr_nos_wise_user_score[$student_id][$nos_id])) ? array_sum($arr_nos_wise_user_score[$student_id][$nos_id]['PracticalActivity']) : 0;
											$viva_user_score = (array_key_exists($student_id,$arr_nos_wise_user_score) && array_key_exists($nos_id,$arr_nos_wise_user_score[$student_id]) && array_key_exists('Viva',$arr_nos_wise_user_score[$student_id][$nos_id])) ? array_sum($arr_nos_wise_user_score[$student_id][$nos_id]['Viva']) : 0;
											$practical_user_score = $practical_skill_user_score + $practical_activity_user_score + $viva_user_score;
											
											$grand_total_practical_qns += $total_qns;
											$grand_total_practical_skill_qns += $total_practical_skill_qns;
											$grand_total_practical_activity_qns += $total_practical_activity_qns;
											$grand_total_viva_qns += $total_viva_qns;
													
											$grand_total_practical_full_score += $practical_full_score;
											$grand_total_practical_user_score += $practical_user_score;
										?>
											<tr>
												<td><?php echo $nos_details['nos_code']; ?> - <?php echo $nos_details['nos_title']; ?></td>
												<td style="text-align: center;"><?php echo $total_qns; ?></td>
												<td style="text-align: center;"><?php echo $total_practical_skill_qns; ?></td>
												<td style="text-align: center;"><?php echo $total_practical_activity_qns; ?></td>
												<td style="text-align: center;"><?php echo $total_viva_qns; ?></td>
												<td style="text-align: center;"><?php echo $practical_full_score; ?></td>
												<td style="text-align: center;"><?php echo $practical_user_score; ?></td>
											</tr>
										<?php
										}
									}
								}
								?>
								<tr>
									<td>&nbsp;</td>
									<td style="text-align: center;"><strong><?php echo $grand_total_practical_qns; ?></strong></td>
									<td style="text-align: center;"><strong><?php echo $grand_total_practical_skill_qns; ?></strong></td>
									<td style="text-align: center;"><strong><?php echo $grand_total_practical_activity_qns; ?></strong></td>
									<td style="text-align: center;"><strong><?php echo $grand_total_viva_qns; ?></strong></td>
									<td style="text-align: center;"><strong><?php echo $grand_total_practical_full_score; ?></strong></td>
									<td style="text-align: center;"><strong><?php echo $grand_total_practical_user_score; ?></strong></td>
								</tr>
								</tbody>
							</table>
							<h4>Overall Result</h4>
							<table>
								<thead>
									<tr>
										<th>Exam Type</th>
										<th>Total Questions</th>
										<th>Full Score</th>
										<th>User Score</th>
										<th>Date of Exam</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$arr_optional_exam_type = explode(",",$arr_batch_details['optional_exam_type']);
									if(in_array('theory',$arr_optional_exam_type)) {
									?>
										<tr>
											<td>Theory</td>
											<td style="text-align: center;">
												<?php echo $grand_total_theory_qns; ?>
											</td>
											<td style="text-align: center;">
												<?php echo $grand_total_full_score; ?>
											</td>
											<td style="text-align: center;">
												<?php echo $grand_total_user_score; ?>
											</td>
											<td style="text-align: center;">
												<?php echo ($arr_student_details[$student_id]['theory_submission_dts'] != "") ? date('d-m-Y',strtotime($arr_student_details[$student_id]['theory_submission_dts'])) : ""; ?>
											</td>
										</tr>
										<?php
									}
									if($show_practical_activity == 1 || $show_viva == 1 || $grand_total_practical_qns > 0) {
									?>
											<tr>
												<td>Practical</td>
												<td style="text-align: center;">
													<?php echo $grand_total_practical_qns; ?>
												</td>
												<td style="text-align: center;">
													<?php echo $grand_total_practical_full_score; ?>
												</td>
												<td style="text-align: center;">
													<?php echo $grand_total_practical_user_score; ?>
												</td>
												<td style="text-align: center;">
													<?php
													$submission_dts = "";
													if($show_practical_activity == 1) {
														$submission_dts .= (isset($arr_student_details[$student_id]['practicalactivity_submission_dts']) && $arr_student_details[$student_id]['practicalactivity_submission_dts'] != "0000-00-00") ? date('d-m-Y',strtotime($arr_student_details[$student_id]['practicalactivity_submission_dts'])) : "";
													}
													if($show_viva == 1) {
														$submission_dts .= (isset($arr_student_details[$student_id]['viva_submission_dts']) && $arr_student_details[$student_id]['viva_submission_dts'] != "0000-00-00") ? "/".date('d-m-Y',strtotime($arr_student_details[$student_id]['viva_submission_dts'])) : "";
													}
													echo $submission_dts;
													?>
												</td>
											</tr>
											<?php      
									}
								?>
								</tbody>
							</table>
							<?php if($type == 'detailed_pdf') { ?>
								<h4>Theory Information</h4>
								<table>
									<thead>
										<tr>
											<th width="7%">SNo.</th>
											<th width="63%">Question</th>
											<th width="6%">Marks</th>
											<th width="8%">User's Response(s)</th>
											<th width="9%">Correct Answer</th>
											<th width="7%">Result</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$sl_no = 1;
										if(array_key_exists($student_id,$arr_theory_answers_list)) {
											if(count($arr_theory_answers_list[$student_id]) > 0) {
												foreach($arr_theory_answers_list[$student_id] as $qn_id => $theory_answer_data) {
													//echo "<br> qn_id ".$qn_id;
													$user_ans = strtoupper($arr_theory_answers_list[$student_id][$qn_id]['ans']);
													$correct_ans = strtoupper($arr_theory_answers_list[$student_id][$qn_id]['correct_ans']);
													$save_type = $arr_theory_answers_list[$student_id][$qn_id]['save_type'];
												?>
												<tr>
													<td style="text-align: center;">
														<?php echo $sl_no; ?>
													</td>
													<td>
														<h6><?php echo $arr_theory_answers_list[$student_id][$qn_id]['question']; ?></h6> </td>
													<td style="text-align: center;">
														<?php echo $arr_theory_answers_list[$student_id][$qn_id]['marks']; ?>
													</td>
													<td style="text-align: center;"><b><?php echo ($user_ans != "") ? $user_ans : $save_type; ?></b></td>
													<td style="text-align: center;">
														<?php echo $correct_ans; ?>
													</td>
													<td style="text-align: center;">
														<?php echo ($user_ans == $correct_ans) ? '<img src="'.base_url().'assets/admin/images/tick.png" alt="" height="20" width="20">' : '<img src="'.base_url().'assets/admin/images/cross.png" alt="" height="20" width="20">'; ?></td>
												</tr>
												<?php
													$sl_no++;
												}
											}
										}	
										?>
									</tbody>
								</table>
								<?php 
								if($show_practical_activity == 1) {
								?>
									<!-- PracticalActivity Detail Information -->
									<div class="row align-items-center">
										<div class="col">
											<h4>Practical Activity Information</h4> </div>
									</div>
									<table>
										<thead>
											<tr>
												<th width="7%">SNo.</th>
												<th width="70%">Question</th>
												<th width="5%">Marks Awarded</th>
												<th width="5%">Max Marks</th>
											</tr>
										</thead>
										<tbody>
											<?php
										$sl_no = 1;
										if(array_key_exists($student_id,$arr_practical_activity_answers_list)) {
											if(count($arr_practical_activity_answers_list[$student_id]) > 0) {
												foreach($arr_practical_activity_answers_list[$student_id] as $qn_id => $practical_activity_answer_data) {
													//echo "<br> qn_id ".$qn_id;
													
												?>
													<tr>
														<td style="text-align: center;">
															<?php echo $sl_no; ?>
														</td>
														<td>
															<h6><?php echo $arr_practical_activity_answers_list[$student_id][$qn_id]['question']; ?></h6> </div>
														</td>
														<td style="text-align: center;">
															<?php echo $arr_practical_activity_answers_list[$student_id][$qn_id]['marks']; ?>
														</td>
														<td style="text-align: center;">
															<?php echo $arr_practical_activity_answers_list[$student_id][$qn_id]['max_marks']; ?>
														</td>
													</tr>
													<?php
													$sl_no++;
												}
											}
										}	
										?>
										</tbody>
									</table>
								<?php } ?>
								<?php if($show_viva == 1) { ?>
										<!-- Viva Detail Information -->
										<div class="row align-items-center">
											<div class="col">
												<h4>Viva Information</h4> </div>
										</div>
										<table>
											<thead>
												<tr>
													<th width="7%">SNo.</th>
													<th width="70%">Question</th>
													<th width="5%">Marks Awarded</th>
													<th width="5%">Max Marks</th>
												</tr>
											</thead>
											<tbody>
												<!-- Details for Viva questions -->
												<?php
									$sl_no = 1;
									if(array_key_exists($student_id,$arr_viva_answers_list)) {
										if(count($arr_viva_answers_list[$student_id]) > 0) {
											foreach($arr_viva_answers_list[$student_id] as $qn_id => $viva_answer_data) {
												//echo "<br> qn_id ".$qn_id;
												
											?>
														<tr>
															<td style="text-align: center;">
																<?php echo $sl_no; ?>
															</td>
															<td>
																<h6><?php echo $arr_viva_answers_list[$student_id][$qn_id]['question']; ?></h6></div>
															</td>
															<td style="text-align: center;">
																<?php echo $arr_viva_answers_list[$student_id][$qn_id]['marks']; ?>
															</td>
															<td style="text-align: center;">
																<?php echo $arr_viva_answers_list[$student_id][$qn_id]['max_marks']; ?>
															</td>
														</tr>
														<?php
												$sl_no++;
											}
										}
									}	
									?>
								</tbody>
							</table>
							<?php }  
						} 
						?>
					</div>
	<?php
	if($student_count < $total_students) { 
		echo "<pagebreak />"; //Page break
	}
	if($total_students == $student_count) {
	?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php } ?>



    

