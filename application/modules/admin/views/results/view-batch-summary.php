<!-- Lightbox2 CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<!-- jQuery (required by Lightbox2) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Lightbox2 JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<style>
.content-body .container-fluid .row .col-md-6 ul li {
	padding: 5px;
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
}

th {
	background-color: #f2f2f2;
}

.table-wrap td {
	word-wrap: break-word;
	/* Allows long words to break and wrap onto the next line */
	word-break: break-word;
	/* Breaks words to fit within the boundaries */
	white-space: normal;
	/* Allows normal wrapping of text */
}
</style>
<div class="content-body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Result Summary - Batch - <?php echo $arr_batch_details['batch_id']; ?></h4>
						<div class="bootstrap-badge"> <a href="javascript:void(0)" class="badge badge-info" onclick="download_batch_result_summary('Basic')"><i class="fa-solid fa-file-pdf"></i> Download Batch Basic Result</a> <a href="javascript:void(0)" class="badge badge-info" onclick="download_batch_result_summary('Detailed')"><i class="fa-solid fa-file-pdf"></i> Download Batch Detailed Result</a> </div>
					</div>
					<?php
                        if(count($arr_student_details) > 0) {
                            foreach($arr_student_details as $student_id => $student_data) {
                            ?>
								<div class="card-body">
									<div class="row">
										<div class="col-md-7">
											<div class="card">
												<div class="card-body">
													<h4 class="card-title">Student Information</h4>
													<!-- Change h2 to h4 -->
													<?php
													//$watermark_file = str_replace(".jpeg","-watermark.jpeg",$arr_student_details[$student_id]['student_photo']);
													$student_photo_file = $arr_student_details[$student_id]['student_photo'];
													$student_watermark_photo = ($arr_student_details[$student_id]['student_photo'] != "") ? base_url().$this->config->item('student_photo_path').$student_photo_file : base_url().'assets/admin/images/profile/small/pic8.jpg';
												
													//$watermark_aadhar_front_file = str_replace(".jpeg","-watermark.jpeg",$arr_student_details[$student_id]['aadhar_front_filename']);
													$aadhar_front_file = $arr_student_details[$student_id]['aadhar_front_filename'];
													$aadhar_front_watermark_photo = ($arr_student_details[$student_id]['aadhar_front_filename'] != "") ? base_url().$this->config->item('aadhaar_filename_path').$aadhar_front_file : base_url().'assets/admin/images/profile/small/pic8.jpg';
												
													//$watermark_aadhar_back_file = str_replace(".jpeg","-watermark.jpeg",$arr_student_details[$student_id]['aadhar_back_filename']);
													$aadhar_back_file = $arr_student_details[$student_id]['aadhar_back_filename'];
													$aadhar_back_watermark_photo = ($arr_student_details[$student_id]['aadhar_back_filename'] != "") ? base_url().$this->config->item('aadhaar_filename_path').$aadhar_back_file : base_url().'assets/admin/images/profile/small/pic8.jpg';
													?>
														<ul class="list-unstyled">
															<li><strong>Candidate ID:</strong>
																<?php echo $arr_student_details[$student_id]['enrollment_number']; ?>
															</li>
															<li><strong>Student Name:</strong>
																<?php echo $arr_student_details[$student_id]['student_name']; ?>
															</li>
															<li><strong>Father Name:</strong>
																<?php echo $arr_student_details[$student_id]['father_name']; ?>
															</li>
															<li><strong>Date of Birth:</strong>
																<?php echo (!empty($arr_student_details[$student_id]['dob'])) ? date('d-m-Y',strtotime($arr_student_details[$student_id]['dob'])) : ""; ?>
															</li>
															<li><strong>Aadhar number:</strong>
																<?php echo $arr_student_details[$student_id]['aadhar_number']; ?>
															</li>
															<li class="d-flex">
																<div class="d-inline flex-column"> <strong>Photo:</strong> <a href="<?php echo $student_watermark_photo; ?>" data-lightbox="student-photo" data-title="Student Photo">
															<img src="<?php echo $student_watermark_photo; ?>" alt="Student Photo" class="img-thumbnail" style="max-height: 75px;">
														</a> <strong>Aadhar Front:</strong> <a href="<?php echo $aadhar_front_watermark_photo; ?>" data-lightbox="aadhar" data-title="Aadhar Front">
																<img src="<?php echo $aadhar_front_watermark_photo; ?>" alt="Aadhar Front" class="img-thumbnail mb-2" style="max-height: 75px;">
															</a> <strong>Aadhar Back:</strong> <a href="<?php echo $aadhar_back_watermark_photo; ?>" data-lightbox="aadhar" data-title="Aadhar Back">
																<img src="<?php echo $aadhar_back_watermark_photo; ?>" alt="Aadhar Back" class="img-thumbnail" style="max-height: 75px;">
															</a> </div>
															</li>
															<li><strong>Address:</strong>
																<?php echo $arr_student_details[$student_id]['address']; ?>
															</li>
															<li><strong>Geo Location Details:</strong> Latitude -
																<?php echo $arr_student_details[$student_id]['lat']; ?>,Longitude -
																	<?php echo $arr_student_details[$student_id]['lng']; ?>
															</li>
														</ul>
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="card">
												<div class="card-body">
													<h4 class="card-title">Assessment Details</h4>
													<!-- Change h2 to h4 -->
													<ul class="list-unstyled">
														<li><strong>Training Partner Name:</strong>
															<?php echo $arr_batch_details['tp_name']; ?>
														</li>
														<li><strong>Training Center Name:</strong>
															<?php echo $arr_batch_details['tc_name']; ?>
														</li>
														<li><strong>Batch Name:</strong>
															<?php echo $arr_batch_details['batch_id']; ?>
														</li>
														<li><strong>Assessment Date:</strong>
															<?php echo date('d-m-Y',strtotime($arr_batch_details['tb_assessment_date'])); ?>
														</li>
														<li><strong>Sector Skill Council:</strong>
															<?php echo $arr_batch_details['ssc_code']."-".$arr_batch_details['ssc_title']; ?>
														</li>
														<li><strong>Trade/QP Name:</strong>
															<?php echo $arr_batch_details['trade_code']."-".$arr_batch_details['trade_name']; ?>
														</li>
													</ul>
													<hr>
													<h4>Result Information</h4>
													<ul class="list-unstyled">
														<li><strong>Passing Percentage:</strong>
															<?php echo $arr_student_details[$student_id]['pass_percentage']; ?>% </li>
														<li><strong>User Percentage:</strong>
															<?php echo $arr_student_details[$student_id]['marks_percentage']; ?>%</li>
														<li><strong>Result:</strong>
															<?php
													if($arr_student_details[$student_id]['result'] == 'Pass') {
													?> <span class="badge badge-rounded badge-success">Pass</span>
																<?php
													}
													else if($arr_student_details[$student_id]['result'] == 'Fail') {
													?> <span class="badge badge-rounded badge-danger">Fail</span>
																	<?php
													}
													else if($arr_student_details[$student_id]['result'] == 'Absent') {
													?> <span class="badge badge-rounded badge-secondary">Absent</span>
																		<?php
													}
													?>
														</li>
													</ul>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<h4 class="card-title">Assessment Snapshots</h4>
											<ul class="list-unstyled">
												<?php
													if(array_key_exists($student_id,$arr_student_snapshot_details)) {
														$snapshot_image_path = base_url() . $this->config->item('student_snapshot_photo_path').'thumbs/';
														foreach($arr_student_snapshot_details[$student_id] as $snapshot_image) {
															$snapshot_file = $snapshot_image_path . $snapshot_image;
															?>
																<li style="list-style-type: none; display: inline; flex-wrap: wrap; justify-content: flex-start; align-items: center;"> 
																	<a href="<?php echo $snapshot_file; ?>" data-lightbox="assessment-snapshots" data-title="Assessment Snapshot">
																		<img src="<?php echo $snapshot_file; ?>" alt="Assessment Snapshot" class="img-thumbnail" style="height:75px; width: 75px; margin-right: 10px; margin-bottom: 10px;">
																	</a> 
																</li>
													<?php
														}
													}
													?>
											</ul>
										</div>
										<div class="row">
											<h4>Assessment Details</h4>
											<table>
												<thead>
													<tr>
														<td align="left" colspan="4"><strong>Theory </strong></td>
													</tr>
													<tr>
														<th>Trade/QP Name</th>
														<th>Theory</th>
														<th>Full Score</th>
														<th>User Score</th>
													</tr>
												</thead>
												<tbody>
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
															<td align="left">
																<?php echo $nos_details['nos_code']; ?> -
																	<?php echo $nos_details['nos_title']; ?>
															</td>
															<td>
																<?php echo $total_theory_qns; ?>
															</td>
															<td>
																<?php echo $full_score; ?>
															</td>
															<td>
																<?php echo $user_score; ?>
															</td>
														</tr>
														<?php
														}
													}
													?>
													<tr>
														<td align="left">&nbsp;</td>
														<td><strong><?php echo $grand_total_theory_qns; ?></strong></td>
														<td><strong><?php echo $grand_total_full_score; ?></strong></td>
														<td><strong><?php echo $grand_total_user_score; ?></strong></td>
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
											<table>
											<thead>
												<tr>
													<td align="left" colspan="7"><strong>Practical </strong></td>
												</tr>
												<tr>
													<th>Trade/QP Name</th>
													<th>Total Questions</th>
													<th>Practical Skill</th>
													<th>Practical Activity</th>
													<th>Viva</th>
													<th>Full Score</th>
													<th>User Score</th>
												</tr>
											</thead>
											<tbody>
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
														<td align="left">
															<?php echo $nos_details['nos_code']; ?> -
																<?php echo $nos_details['nos_title']; ?>
														</td>
														<td>
															<?php echo $total_qns; ?>
														</td>
														<td>
															<?php echo $total_practical_skill_qns; ?>
														</td>
														<td>
															<?php echo $total_practical_activity_qns; ?>
														</td>
														<td>
															<?php echo $total_viva_qns; ?>
														</td>
														<td>
															<?php echo $practical_full_score; ?>
														</td>
														<td>
															<?php echo $practical_user_score; ?>
														</td>
													</tr>
											<?php
												}
											}
										}
										?>
										<tr>
											<td align="left">&nbsp;</td>
											<td><strong><?php echo $grand_total_practical_qns; ?></strong></td>
											<td><strong><?php echo $grand_total_practical_skill_qns; ?></strong></td>
											<td><strong><?php echo $grand_total_practical_activity_qns; ?></strong></td>
											<td><strong><?php echo $grand_total_viva_qns; ?></strong></td>
											<td><strong><?php echo $grand_total_practical_full_score; ?></strong></td>
											<td><strong><?php echo $grand_total_practical_user_score; ?></strong></td>
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
													$theoryQns = 0;
													$practicalActivityQns = 0;
													$vivaQns = 0;

													if(in_array('theory',$arr_optional_exam_type)) {
													?>
														<tr>
															<td>Theory</td>
															<td>
																<?php echo $grand_total_theory_qns; ?>
															</td>
															<td>
																<?php echo $grand_total_full_score; ?>
															</td>
															<td>
																<?php echo $grand_total_user_score; ?>
															</td>
															<td>
																<?php echo ($arr_student_details[$student_id]['theory_submission_dts'] != "") ? date('d-m-Y',strtotime($arr_student_details[$student_id]['theory_submission_dts'])) : ""; ?>
															</td>
														</tr>
														<?php
													}
													if($show_practical_activity == 1 || $show_viva == 1 || $grand_total_practical_qns > 0) {
													?>
															<tr>
																<td>Practical</td>
																<td>
																	<?php echo $grand_total_practical_qns; ?>
																</td>
																<td>
																	<?php echo $grand_total_practical_full_score; ?>
																</td>
																<td>
																	<?php echo $grand_total_practical_user_score; ?>
																</td>
																<td>
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
											<?php if($type == 'view_detailed') { ?>
												<h4>Theory Information</h4>
												<table>
													<thead>
														<tr>
															<th>SNo.</th>
															<th>Question</th>
															<th>Marks</th>
															<th>User's Response(s)</th>
															<th>Correct Answer</th>
															<th>Result</th>
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
																<td>
																	<?php echo $sl_no; ?>
																</td>
																<td>
																	<h6><?php echo $arr_theory_answers_list[$student_id][$qn_id]['question']; ?></h6></td>
																<td>
																	<?php echo $arr_theory_answers_list[$student_id][$qn_id]['marks']; ?>
																</td>
																<td><b><?php echo ($user_ans != "") ? $user_ans : $save_type; ?></b></td>
																<td>
																	<?php echo $correct_ans; ?>
																</td>
																<td>
																	<?php echo ($user_ans == $correct_ans) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>'; ?></td>
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
														<div class="col-auto">
															<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg" onclick="getuploadedVideo('Practical Activity','<?php echo $arr_student_details[$student_id]['practicalactivity_video_file']  ?>');">View Uploaded Video</button>
														</div>
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
																	<td>
																		<?php echo $sl_no; ?>
																	</td>
																	<td>
																		<h6><?php echo $arr_practical_activity_answers_list[$student_id][$qn_id]['question']; ?></h6></td>
																	<td>
																		<?php echo $arr_practical_activity_answers_list[$student_id][$qn_id]['marks']; ?>
																	</td>
																	<td>
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
																<div class="col-auto">
																	<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg" onclick="getuploadedVideo('Viva','<?php echo $arr_student_details[$student_id]['viva_video_file']  ?>');">View Uploaded Video</button>
																</div>
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
																			<td>
																				<?php echo $sl_no; ?>
																			</td>
																			<td>
																				<h6><?php echo $arr_viva_answers_list[$student_id][$qn_id]['question']; ?></h6></td>
																			<td>
																				<?php echo $arr_viva_answers_list[$student_id][$qn_id]['marks']; ?>
																			</td>
																			<td>
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
											} ?>
										</div>
									</div> <!-- End Row -->
								</div> <!-- End Card Body -->	
							<?php
                            } //End for each
                        } //End count
                        ?>
						
				</div>
			</div>
		</div>
	</div>
	<!-- Large modal -->
	<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"></h5>
					<button type="button" class="btn-close custom-close" data-bs-dismiss="modal"> </button>
				</div>
				<div class="modal-body">
					<iframe id="iframe_video" width="100%" height="315" src="" frameborder="0" allowfullscreen></iframe>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger light custom-close" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
	<script>
	function getuploadedVideo(type, video_file) {
		$('.modal-title').text(type + ' video');
		var video_url = '<?php echo base_url().$this->config->item('student_assessment_videos_path'); ?>' + video_file;
	    $('#iframe_video').attr('src', video_url);
		//console.log('video_file '+video_file+'  '+'<?php echo base_url().$this->config->item('student_assessment_videos_path'); ?>'+video_file);
		$(".bd-example-modal-lg").modal('show');
	}
	$(".custom-close").click(function() {
		$('#iframe_video').attr('src', '');
	});

	function download_batch_result_summary(summary_type) {
		if(summary_type == 'Basic') {
			url = "<?php echo base_url().'view-batch-result-summary/'.id_encode($arr_batch_details['tb_id']).'/basic_pdf' ?>";
		} else {
			url = "<?php echo base_url().'view-batch-result-summary/'.id_encode($arr_batch_details['tb_id']).'/detailed_pdf' ?>";
		}
		var newTab = $("<a>").attr("href", url).attr("target", "_blank");
    
        // Simulate a click on the <a> element
        newTab[0].click();
	}
	</script>