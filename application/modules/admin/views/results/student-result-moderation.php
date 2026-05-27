<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
			 <h3>Batch Id - <?php echo $student['batch_id']." :: ".$student['enrollment_number']." - ".$student['student_name']; ?></h3>
			<div class="row">

                    <div class="col-xl-12 col-lg-12">
                            
                        <div class="card">
							<div class="card-header flex-wrap d-flex justify-content-between"> 
								<div><h4 class="card-title" id="title">Theory Moderation</h4></div>
								<div class="bootstrap-badge">
									<?php
									$passing_marks = ($student['pass_percentage'] / 100) * $student['total_max_marks'];
									$required_marks_to_pass = ($student['total_marks'] < $passing_marks) ? ($passing_marks - $student['total_marks']) : 0;
									?>
									<input type="hidden" id="theory_marks" value="<?php echo $student['theory_marks']; ?>">
									<input type="hidden" id="total_practical_marks" value="<?php echo ($student['total_practical_marks'] - $student['practical_skill_marks']); ?>">
									<a href="javascript:void(0)" class="badge badge-rounded badge-primary">Max Marks : <?php echo $student['total_max_marks']; ?></a> 
									<a href="javascript:void(0)" class="badge badge-rounded badge-secondary">Pass Percentage : <?php echo $student['pass_percentage']; ?></span>%</a>
									<a href="javascript:void(0)" class="badge badge-rounded badge-info">Passing Marks : <span id="spn_passing_marks"><?php echo $passing_marks; ?></span></a>
									<a href="javascript:void(0)" class="badge badge-circle badge-outline-primary">Candidate Marks : <span id="spn_candidate_marks"><?php echo $student['total_marks']; ?></span></a> 
									<?php
									if($required_marks_to_pass > 0) {
									?>
										<a href="javascript:void(0)" class="badge badge-circle badge-outline-success">Required Marks to Pass : <span id="spn_required_marks_to_pass"><?php echo $required_marks_to_pass; ?></span></a> 
									<?php } ?>	
								</div>
							</div>

                            <div class="card-body">
							 <?php 
                                    if ($this->session->flashdata('msg') != "") { ?>
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                            <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                        </div>
                                    <?php } ?>
							   <form class="needs-custom-validation" novalidate id="frmTheoryQues" method="post" action="<?= site_url('save-theory-moderation') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <input name="student_id" id="student_id" type="hidden" value="<?php echo $student_id; ?>">
								<input name="tb_id" id="tb_id" type="hidden" value="<?php echo $student['tb_id']; ?>">
														
								<?php
								if($theory_moderation != false){
								foreach($theory_moderation as $i=>$row) { ?>
								<div class="mb-3">
									 <label for="optionAEnglish" class="form-label"><?php echo ($i+1); ?>.</label>
									 <label for="theoryQuestionEnglish" class="form-label"><?php echo $row['question']; ?></label> 
								</div>
								
								<div class="row mb-3">
								<div class="col-md-2">
										<label for="TheoryMaxMarks" class="form-label text-warning">Marks: <?php echo $row['marks']; ?></label>
									</div>
									<div class="col-md-2">
										<label for="CorrectAnswer" class="form-label text-primary">Correct Answer: <?php echo ($row['correct_ans'] != "") ? strtoupper($row['correct_ans']) : ""; ?>							
									</div>
									<div class="col-md-2">
										<label for="StudentAnswer" class="form-label text-success">Student Answer: <?php echo ($row['ans'] != "") ? strtoupper($row['ans']) : ""; ?></label>
										<?php 
											$ans = "no";
											if($row['ans'] != "" && $row['correct_ans'] == $row['ans']) { 
												$ans = "yes";
											?>
											<i class="fa fa-check-circle text-success"></i>
										<?php }else if($row['ans'] != "" && $row['correct_ans'] != $row['ans']) { 
												$ans = "no";
											?>	
											<i class="fa fa-times-circle text-danger"></i>
										<?php } ?>		
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control theory_answer" id="text_theory_<?php echo $row['ta_id']; ?>" name="text_theory_<?php echo $row['ta_id']; ?>" required 
															placeholder="Enter Your Answer" maxlength="1" data-correct_ans="<?php echo ($row['correct_ans'] != "") ? strtoupper($row['correct_ans']) : ""; ?>"
															data-marks="<?php echo $row['marks']; ?>" data-ans="<?php echo $ans; ?>"> 
										<div id="invalid-theory-<?php echo $row['ta_id']; ?>" style="display:none;color:red;">
											Allowed characters are a,b,c,d
										</div>
									</div>
								</div>
								
								<div>&nbsp;</div>
								<?php }} ?>
								<button type="submit" class="btn btn-primary">Update Answers</button> 
								<!--<div class="mb-3">
									 <label for="optionAEnglish" class="form-label">1.</label>
									 <label for="theoryQuestionEnglish" class="form-label">To Book a package ____stationary is required by Consignment Booking Assistant</label>
								</div>
								
								<div class="row mb-3">
								<div class="col-md-2">
										<label for="TheoryMaxMarks" class="form-label text-warning">Marks: 2</label>
									</div>
									<div class="col-md-2">
										<label for="CorrectAnswer" class="form-label text-primary">Correct Answer:D
									</div>
									<div class="col-md-2">
										<label for="StudentAnswer" class="form-label text-success">Student Answer: A</label>
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control" id="UpdateAnswer" name="UpdateAnswer" required placeholder="Enter Your Answer">
									</div>
								</div>

								<button type="submit" class="btn btn-primary">Update Answers</button> -->

							</form>
                                

                            </div>

                        </div>

					</div>
					
					<div class="col-xl-12 col-lg-12">
                            
                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title" id="title">Practical Activity Moderation</h4>
								
								
                            </div>

                            <div class="card-body">
							
							<form class="needs-custom-validation" novalidate id="frmPAQues" method="post" action="<?= site_url('save-pa-moderation') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                 <input name="student_id" id="student_id" type="hidden" value="<?php echo $student_id; ?>">
								 <input name="tb_id" id="tb_id" type="hidden" value="<?php echo $student['tb_id']; ?>">
							
								<?php 
								if($practical_activity_moderation != false){
								foreach($practical_activity_moderation as $i=>$row) { ?>
								<div class="mb-3">
									 <label for="optionAEnglish" class="form-label"><?php echo ($i+1); ?>.</label>
									 <label for="theoryQuestionEnglish" class="form-label"><?php echo $row['question']; ?></label>
								</div>
								
								<div class="row mb-3">
									<div class="col-md-3">
										<label for="MaxMarks" class="form-label text-warning">MaxMarks: <?php echo $row['marks']; ?></label>
									</div>
									<div class="col-md-3">
										<label for="AwardedMarks" class="form-label text-success">Awarded Marks: <?php echo $row['student_marks']; ?></label>
									</div>
									<div class="col-md-3">
										<input type="number" class="form-control award_marks" data-max_marks="<?php echo $row['marks']; ?>" id="text_pa_<?php echo $row['pa_id']; ?>" name="text_pa_<?php echo $row['pa_id']; ?>" required placeholder="Update Marks">
										
									</div>
								</div>
								
								<div>&nbsp;</div>
								<?php }} ?>
								
								<button type="submit" class="btn btn-primary">Update Marks</button>

							</form>
	

                            </div>

                        </div>

					</div>	
					
					<div class="col-xl-12 col-lg-12">
                            
                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title" id="title">Viva Moderation</h4>
								
								
                            </div>

                            <div class="card-body">
							
							<form class="needs-custom-validation" novalidate id="frmVivaQues" method="post" action="<?= site_url('save-viva-moderation') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                 <input name="student_id" id="student_id" type="hidden" value="<?php echo $student_id; ?>">
								 <input name="tb_id" id="tb_id" type="hidden" value="<?php echo $student['tb_id']; ?>">
							
								<?php 
								if($viva_moderation != false){
								foreach($viva_moderation as $i=>$row) { ?>
								<div class="mb-3">
									 <label for="optionAEnglish" class="form-label"><?php echo ($i+1); ?>.</label>
									 <label for="theoryQuestionEnglish" class="form-label"><?php echo $row['question']; ?></label>
								</div>
								
								<div class="row mb-3">
									<div class="col-md-3">
										<label for="MaxMarks" class="form-label text-warning">MaxMarks: <?php echo $row['marks']; ?></label>
									</div>
									<div class="col-md-3">
										<label for="AwardedMarks" class="form-label text-success">Awarded Marks: <?php echo $row['student_marks']; ?></label>
									</div>
									<div class="col-md-3">
										<input type="number" class="form-control award_marks" data-max_marks="<?php echo $row['marks']; ?>" id="text_viva_<?php echo $row['va_id']; ?>" name="text_viva_<?php echo $row['va_id']; ?>" required placeholder="Update Marks">
									</div>
								</div>
								
								<div>&nbsp;</div>
								<?php }} ?>
								
								<button type="submit" class="btn btn-primary">Update Marks</button>

								</form>
			

                            </div>

                        </div>

					</div>	
                    
					

                </div>
				
	
	
	
					
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
 		<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/toastr/css/toastr.min.css">
        <link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script>

	$(document).ready(function() {
		
	<?php foreach($ta_ids as $item){ ?>
		
		$('input[name="text_theory_<?php echo $item; ?>"]').on('change', function(e) {
			var theory_ans = $('input[name="text_theory_<?php echo $item; ?>"]').val();

			var regEx = /^[A-Da-d]{1}$/;
			var valid = regEx.test(theory_ans);
			if (!valid) {
			$('#invalid-theory-<?php echo $item; ?>').show();
			}
			else
			{
			$('#invalid-theory-<?php echo $item; ?>').hide();
			}
		});

	<?php }?>

		$('.theory_answer').on('input', function() {
			var inputValue = $(this).val();
			if(inputValue.match(/[^ABCDabcd]/g)) {
				$(this).val($(this).val().replace(/[^ABCDabcd]/g, ''));
			}
			//Check whether the answer is correct. If correct then calculate the total marks
			// Initialize total marks
			var total_practical_marks = parseFloat($('#total_practical_marks').val());
			var totalMarks = total_practical_marks;
			var passingMarks = parseFloat($('#spn_passing_marks').text()); // Get passing marks

			//console.log('Initial totalMarks '+totalMarks);

			// Loop through all textboxes with class theory_answer
			$('.theory_answer').each(function() {
				var answer = $(this).val().toUpperCase(); // Get the current value (convert to uppercase for comparison)
				var correctAnswer = $(this).data('correct_ans').toUpperCase(); // Get the correct answer
				var marks = parseFloat($(this).data('marks')); // Get the marks
				var candiateAns = $(this).data('ans');

				//console.log('candiateAns '+candiateAns+' answer '+answer+' marks '+marks);

				// Check if the entered answer is correct
				if(candiateAns == 'yes' && answer == "") {
					totalMarks += marks; // Add marks to total if already answered is correct
					//console.log('totalMarks '+totalMarks);
				}
				else if(answer === correctAnswer) {
					totalMarks += marks; // Add marks to total if correct
					//console.log('totalMarks '+totalMarks);
				}
			});

			//console.log('totalMarks '+totalMarks);
			//console.log('passingMarks '+passingMarks);

			// Update the total marks in the target span
			$('#spn_candidate_marks').text(totalMarks);

			// Calculate remaining marks needed to pass
			if(totalMarks < passingMarks) {
				var remainingMarks = passingMarks - totalMarks;
				//console.log('remainingMarks '+remainingMarks);
				$('#spn_required_marks_to_pass').text(remainingMarks);
			} else {
				$('#spn_required_marks_to_pass').text('0'); // No more marks needed  
				// Show SweetAlert when target is reached
				Swal.fire({
					icon: 'success',
					title: 'Congratulations!',
					text: 'You have reached the required marks to pass!',
					confirmButtonText: 'OK'
				});
			}
		});

		$('.award_marks').on('input', function() {
			var inputValue = $(this).val();
			var maxMarks = parseInt($(this).attr('data-max_marks'));
			//console.log('maxMarks '+maxMarks);

			if(inputValue > maxMarks) {
				$(this).val('');
			}
		});
	});  

</script>