<style>
    .list-group {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        max-width: 600px; /* Adjust the max-width as needed */
    }

   
    .list-group-item input[type="radio"] {
        margin-right: 10px;
        flex: 1 0 50%; /* Grow and shrink equally, basis is 50% */
        box-sizing: border-box; /* Ensure padding and border are included in the width */
        padding: 10px;
		
    }
	
	 /* CSS for selected radio button */
    .list-group-item input[type="radio"]:checked + .option-label + label {
    background-color: #3A9B94; /* Set your desired background color for the selected option */
    color: white; /* Set text color for better visibility */
    padding: 8px; /* Optional: Add padding for better appearance */
    font-weight: normal;
    border-color: #bbe6e3;
    display:inline-flex;
    flex-direction:column;
	}
	
	/* Question styling */
	.question {
		margin-bottom: 20px; /* Adjust spacing between question and options */
		border: 0px solid #ccc; /* Add border */
		padding: 10px; /* Add padding */
	}

	/* Option styling */
	.option {
		display: flex;
		align-items: center;
		margin-bottom: 10px; /* Adjust spacing between options */
		padding: 10px; /* Add padding */
	}

	.option input[type="radio"] {
		margin-right: 10px;
	}

	.option label {
		margin-bottom: 0; /* Reset margin for better alignment */
		
	}

	/* Borders for options */
	.option label {
		border: 1px solid #ccc; /* Add border */
		padding: 8px; /* Add padding to the label */
	}

.option-label {
        margin-right:20px;
		font-weight:bold;
    }
    #questionPalette {
        display: none;
    }

    .exam-details p {
        margin-bottom: 5px;
    }

    .bootstrap-badge {
        margin-top: 10px;
    }

    .badge {
        margin-right: 5px;
        margin-bottom: 5px;
    }
	
	.button-group {
        display: flex;
        flex-wrap: wrap; /* Allow buttons to wrap to the next line on smaller screens */
        justify-content: center;
        gap: 10px;
    }

    .button-group button {
        flex: 1; /* Take up equal space within the container */
        max-width: 150px; /* Limit the maximum width of buttons */
        margin: 5px; /* Add margin around each button */
    }

    .button-group button i {
        margin-right: 5px; /* Add margin to the right of the icon */
    }
	
	.bootstrap-badge {
    text-align: left;
	}

	.badge-container {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 5px; /* Adjust the gap between badges as needed */
	}
	.badge {
		flex-basis: calc(10% - 5px); /* Set the width of each badge to occupy 10% of the container width with 5px gap */
	}
	
	@media (min-width: 768px) {
        #questionPalette {
            display: block; /* Show for desktop/laptop screens */
        }
    }
	
</style>

<div class="content-body">
    <div class="container-fluid">
        <!-- row -->
        <div class="row">
		<div class="col-lg-3">
                <div class="card">
				<div class="card-header">
					<h4 class="card-title">Assessment Details</h4>
				</div>
                    <div class="card-body">
					<p>Job Role/QP Name: <strong><?php echo (array_key_exists('trade_name',$arrBatchDetails)) ? $arrBatchDetails['trade_name'] : ""; ?></strong></p>
					<p>Exam Date and Time: <strong><br><?php echo (array_key_exists('assessment_date',$arrBatchDetails)) ? $arrBatchDetails['assessment_date'] : ""; ?></strong></p>
					<p>Duration: <strong><?php echo (array_key_exists('exam_duration_mins',$arrBatchDetails)) ? $arrBatchDetails['exam_duration_mins'] : ""; ?></strong></p>
					<input type="hidden" id="auto_logout_mins" value="<?php echo $this->config->item('auto_logout_mins'); ?>">
					
					<button type="button" id="togglePalette" class="btn btn-primary">Questions Palette</button>
						<div class="bootstrap-badge" id="questionPalette">
							<h6>Questions Palette</h6><hr/>
							<div class="row">
								<?php
								$totalQuestions = 0;
								$totalAnswered = 0;
								$totalReview = 0;
								$totalNotAnswered = 0;
								$arrQnsList = array();

								if(count($arrQuestionDetails) > 0)	{
									foreach($arrQuestionDetails as $key => $arrQuestions) {
										$q_id = $arrQuestions['qid'];
										$save_type = ($arrQuestions['save_type'] != '') ? $arrQuestions['save_type'] : 'NV';
										$arrQnsList[0] = 0;
										$arrQnsList[($key + 1)] = $q_id;

										$badgeColor = "dark";
										if($save_type == 'Save') {
											$badgeColor = "success";
											$totalAnswered++;
										}
										if($save_type == 'Review') {
											$badgeColor = "warning";
											$totalReview++;
										}
										if($save_type == 'NA') {
											$badgeColor = "danger";
											$totalNotAnswered++;
										}
										$totalQuestions++;
									?>
									<div class="col-2 col-md-3 col-lg-2">
										<a href="javascript:void(0)" class="badge badge-<?php echo $badgeColor; ?>" data-badge-color="badge-<?php echo $badgeColor; ?>" data-palette_qid="<?php echo $q_id; ?>" id="palette_qid_<?php echo $q_id; ?>"><?php echo ($key + 1); ?></a>
									</div>
									<?
									}
								}
								?>
							</div>
						</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
					<div class="card-body" style="margin-top: 10px; padding: 10px;">
						<div class="bootstrap-badge">
							<a href="javascript:void(0)" class="badge badge-rounded badge-primary">Total Questions : <span id="totalQuestions"><?php echo $totalQuestions; ?></span></a>
							<a href="javascript:void(0)" class="badge badge-rounded badge-success">Answered : <span id="totalAnswered"><?php echo $totalAnswered; ?></span></a>
							<a href="javascript:void(0)" class="badge badge-rounded badge-warning">Marked for Review : <span id="totalReview"><?php echo $totalReview; ?></span></a>
							<a href="javascript:void(0)" class="badge badge-rounded badge-light">Not Answered : <span id="totalNotAnswered"><?php echo $totalNotAnswered; ?></span></a>
						</div>
					</div>
					<?php
					$arrOptions = array('option_a' => 'a','option_b' => 'b','option_c' => 'c','option_d' => 'd');
					$question_images_path = base_url().$this->config->item('question_images_path');
					$counter = 0;
					if(count($arrQuestionDetails) > 0)	{
						foreach($arrQuestionDetails as $key => $arrQuestions) {
							$q_id = $arrQuestions['qid'];
							$arrQnsSplit = explode('|lang|',$arrQuestions['question']);

							$question = $arrQnsSplit[0];
							//echo "<br> question ".$question;

							$langQuestion = (count($arrQnsSplit) > 1) ? $arrQnsSplit[1] : "";

							$counter++;
						?>
							<div class="card-body questionList" id="qid_<?php echo $q_id; ?>" data-serial_no="<?php echo $counter; ?>" style="display:none;">
								<div class="card-header" style="display: flex; align-items: center;">
									<h4 class="text-primary" style="flex: 1; text-align: left;">Question.<?php echo ($key + 1); ?></h4>
									<h4 class="card-title" style="flex: 1; text-align: right;"><?php echo ($arrQuestions['nos_title']); ?></h4>
								</div>
								
								<div class="question">
									<h5><?php echo trim($question); ?></h5>
									<?php
									if(trim($langQuestion) != "") {
									?>
										<h5><?php echo trim($langQuestion); ?></h5>
									<?php
									}
									?>
								</div>
								<!-- English Options -->
								<div class="options">
									<ul class="list-group list-group-flush">
										<?php
										foreach($arrOptions as $optCols => $option) {
											$arrQnsOptionSplit = explode('|lang|',$arrQuestions[$optCols]);
											$options =  $arrQnsOptionSplit[0];
											//echo "<br> options ".$options;
											
											$langOptions = (count($arrQnsOptionSplit) > 1) ? $arrQnsOptionSplit[1] : "";

											if(trim($options) != "") {
										?>
										<li class="list-group-item" onclick="highlightRadioOption(<?php echo $q_id; ?>,'<?php echo $option; ?>');">
											<input type="radio" name="answer_<?php echo $q_id; ?>" value="<?php echo $option; ?>" <?php echo (strtolower(trim($arrQuestions['ans'])) == $option) ? "checked" : ""; ?>>
											<span class="option-label"><?php echo strtoupper($option); ?>)</span>
											<label for="option1"><?php echo ($options); ?>
											<?php
											if(trim($langOptions) != "") {
											?>
												<br/><br/><?php echo trim($langOptions); ?></label>
											<?php
											}
											else {
											?>
											</label>
											<?php
											}
											?>
										</li>
										<?php
											}
										}	
										?>
									</ul>
								</div>		
								<hr/>
								<div class="button-group">
									<?php
									if($counter > 1 ) {
									?>
										<button type="button" id="btnPrevious<?php echo $q_id; ?>" class="btn me-2 btn-primary" onclick="jumpToQuestion(<?php echo $arrQnsList[($counter - 1)]; ?>);"><i class="fas fa-chevron-left"></i> Previous</button>
									<?php
									}
									if($counter < $totalQuestions) {
										$nextCounter = $counter + 1;
										$saveLabel = "Save & Next";
									}
									else { //If at end of the question
										$nextCounter = $totalQuestions;
										$saveLabel = "Save";
									}	
									?>
									<button type="button" id="btnNext<?php echo $q_id; ?>" class="btn me-2 btn-primary" onclick="goToNextQn(<?php echo $q_id; ?>,<?php echo $arrQnsList[$nextCounter]; ?>,'Save');"><?php echo $saveLabel; ?> <i class="fas fa-chevron-right"></i></button>
									<button type="button" id="btnMarkForReview<?php echo $q_id; ?>" class="btn me-2 btn-warning" onclick="goToNextQn(<?php echo $q_id; ?>,<?php echo $arrQnsList[$nextCounter]; ?>,'Review');">Mark for Review <i class="fas fa-bookmark"></i></button>
									<button type="button" id="btnSubmitExam<?php echo $q_id; ?>" class="btn me-2 btn-danger btn-lg" onclick="submitExam();">Submit Exam <i class="fas fa-check"></i></button>
								</div>
							</div>
						<?
						}
					}
					?>			
				</div>
            </div>
        </div>
    </div>
	<!--<div id="responseContainer"></div> // For Api Debug --> 
</div>
<!-- Confirmation Modal -->
<div class="modal fade modal-submit-confirm" id="exampleModalCenter">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Theory Submission Confirmation</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal">
				</button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to submit the assessment?<br>
				<div class="alert alert-outline-danger">You wont be able to make any changes to the answers once you submit.</div></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger light" data-bs-dismiss="modal">No</button>
				<button type="button" class="btn btn-primary" id="btn_submit_assessment">Yes</button>
			</div>
		</div>
	</div>
</div>
<!-- End Confirmation modal -->
<link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script>
 document.getElementById('togglePalette').addEventListener('click', function() {
        var palette = document.getElementById('questionPalette');
        if (palette.style.display === 'none' || palette.style.display === '') {
            palette.style.display = 'block';
        } else {
            palette.style.display = 'none';
        }
    });
   // Function to check screen size and toggle palette accordingly
    function togglePaletteBasedOnScreenSize() {
        var palette = document.getElementById('questionPalette');
        if (window.innerWidth <= 768) { // Check if screen width is less than or equal to 768px (smallest screen size for mobile devices)
            palette.style.display = 'none'; // Keep palette closed for smaller screens
        } else {
            palette.style.display = 'block'; // Display palette for larger screens
        }
    }

    // Call the function when the page loads
    window.onload = function() {
        togglePaletteBasedOnScreenSize(); // Initial check on page load
    };

    // Call the function when the window is resized
    window.onresize = function() {
        togglePaletteBasedOnScreenSize(); // Check on window resize
    };
	
	 // Timer code
    function startTimer(duration, display) {
        var timer = duration, hours, minutes, seconds;
        setInterval(function () {
			hours = parseInt(timer / 3600, 10);
            minutes = parseInt((timer % 3600) / 60, 10);
            seconds = parseInt(timer % 60, 10);

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = hours + ":" + minutes + ":" + seconds;

            if (timer == 600) { //Alert user that assessment will get auto submitted in 10mins.
				//console.log('timer duration '+timer);
				sweetAlert("Your assessment will get auto submitted in 10 mins!!");
				$("#div_timer").removeClass('alert-primary');
                $("#div_timer").addClass('alert-danger');
            }
			if (timer == 1) { //Alert user that assessment will get auto submitted in 10mins.
				swal({ title: "Assessment auto submission", text: "Your assessment will get auto submitted now!!", timer: 2e3, showConfirmButton: !1 });
                
            }
			if (timer == 0) { //Auto Submit the exam
				submitAssessment();
                
            }
			--timer;
        }, 1000);
    }

    window.onload = function () {
        /*var duration = '<?php echo $exam_duration_secs; ?>'; // Duration in seconds
        var display = document.querySelector('#timer');
        startTimer(duration, display);*/

		//Display the Questions and highlight the palette
		var totalQuestions = parseInt($("#totalQuestions").text());
		var totalAnswered = parseInt($("#totalAnswered").text());
		var totalReview = parseInt($("#totalReview").text());
		var totalNotAnswered = parseInt($("#totalNotAnswered").text());
		var totalQnsAttempted = totalAnswered + totalReview + totalNotAnswered;

		/*console.log('totalQuestions '+totalQuestions);
		console.log('totalAnswered '+totalAnswered);
		console.log('totalReview '+totalReview);
		console.log('totalNotAnswered '+totalNotAnswered);
		console.log('totalQnsAttempted '+totalQnsAttempted);*/

		var qid = 0;

		if(totalQnsAttempted == 0) {
			//Show the first Qn
			qid = '<?php echo $arrQnsList[1];  ?>';
		}
		else {
			qid = getFirstNotVisitedQid();
		}
		if(qid > 0) {
			jumpToQuestion(qid);
		}
	};
    
	// Script to jump to selected question when badge is clicked
    var badges = document.querySelectorAll('.bootstrap-badge .badge');
    badges.forEach(function(badge) {
        badge.addEventListener('click', function() {
            var questionNumber = $(this).data("palette_qid"); // Extract the question number from badge text
			//console.log('qid '+$(this).data("palette_qid"));
            // Assuming you have some function to jump to the selected question based on its number
            jumpToQuestion(questionNumber);
        });
    });

    // Function to jump to selected question
    function jumpToQuestion(qid) {
        // Implement your logic to jump to the selected question
        // For example, scroll to the corresponding question or change its visibility
        //console.log('Jumping to question ' + qid);

		if(qid == 0) {
			qid = getFirstNotVisitedQid();
		}
		//console.log('getFirstNotVisitedQid ' + qid);

		var counter = 0;
		var totalQuestions = parseInt($("#totalQuestions").text());
		var totalAnswered = 0; //parseInt($("#totalAnswered").text());
		var totalReview = 0; //parseInt($("#totalReview").text());
		var totalNotAnswered = 0; //parseInt($("#totalNotAnswered").text());
		var totalQnsAttempted = 0;
		
		$(".questionList").each(function() {
			var id = $(this).attr("id");
			//console.log("ID:", id+" Q ID: ", "qid_"+qid);

			if ($("#palette_"+id).hasClass("badge-success")) {
				totalAnswered++;
			}
			else if ($("#palette_"+id).hasClass("badge-warning")) {
				totalReview++;
			}
			else if ($("#palette_"+id).hasClass("badge-danger")) {
				totalNotAnswered++;
			}
			else if ($("#palette_"+id).hasClass("badge-primary")) {
				var getCurrBadgeColor = $("#palette_"+id).data("badge-color"); 
				if (getCurrBadgeColor == "badge-success") {
					totalAnswered++;
				}
				else if (getCurrBadgeColor == "badge-warning") {
					totalReview++;
				}
				else if (getCurrBadgeColor == "badge-danger") {
					totalNotAnswered++;
				}
			}

			counter++;

			//console.log("counter:"+ counter);

			if("qid_"+qid == id) {
				//console.log("if "+id);

				$("#qid_"+qid).show();
				$("#qnCounter").text(counter);

				$("#palette_qid_"+qid).removeClass(function(index, className) {
					return (className.match(/\bbadge-\S+/g) || []).join(' ');
				});
				$("#palette_qid_"+qid).addClass("badge-primary");
			}
			else {
				$("#"+id).hide();
				
				if ($("#palette_"+id).hasClass("badge-primary")) { //Required only when Previous is clicked
					$("#palette_"+id).removeClass("badge-primary");
					var currBadgeColor = $("#palette_"+id).data("badge-color"); 
					//console.log("currBadgeColor "+currBadgeColor+" id "+id);
					$("#palette_"+id).addClass(currBadgeColor);
				}
			}
		});
		//console.log('totalAnswered '+parseInt(totalAnswered)+' totalReview '+ parseInt(totalReview)+ ' totalNotAnswered ' + parseInt(totalNotAnswered));
		totalQnsAttempted = parseInt(totalAnswered) + parseInt(totalReview) + parseInt(totalNotAnswered);
		$("#totalAnswered").text(totalAnswered);
		$("#totalReview").text(totalReview);
		$("#totalNotAnswered").text(totalNotAnswered);

		// Calculate progress percentage
		var progressPercentage = (parseInt(totalQnsAttempted) / parseInt(totalQuestions)) * 100;

		// Update progress bar width
		$(".progress-bar").css("width", progressPercentage + "%");
		
	}
	
    // Function to handle clicking the "Next" button
    function goToNextQn(current_qid,next_qid,save_type) {
		// Implement logic to navigate to the previous question
        //console.log('Navigating to next question');
		
		//Call ajax to save the data
		if(current_qid > 0 && save_type != "") {
			var ans = $("input[name='answer_"+current_qid+"']:checked").val();
			var timeString = $('#timer').text(); //  hours,  minutes,  seconds
			var totalSeconds = timeToSeconds(timeString);

			if(save_type == "Save" && typeof ans === 'undefined') {
				save_type = "NA";
			}

			if(save_type != "Review" ) {
				$('#btnNext'+current_qid).html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
				$('#btnNext'+current_qid).attr('disabled',true);
			}
			else {
				$('#btnMarkForReview'+current_qid).html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
				$('#btnMarkForReview'+current_qid).attr('disabled',true);
			}

			//console.log('ans '+ans+' timer '+$('#timer').text());
			var formData = new FormData();
			formData.append("candidate_id", '<?php echo $this->session->userdata('candidate_id') ?>');
			formData.append("token", '<?php echo $this->session->userdata('unique_token') ?>');
			formData.append("qid", current_qid);
			formData.append("ans", ans);
			formData.append("save_type", save_type);
			formData.append("time_left_secs", totalSeconds);
						
			$.ajax({
				url: "Api-Save-Answer",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(response) {
					//console.log("API Response:", response);
					// Print response to HTML
					//$("#responseContainer").text(JSON.stringify(response));

					//Add Color to Palette Badges
					$("#palette_qid_"+current_qid).removeClass(function(index, className) {
						return (className.match(/\bbadge-\S+/g) || []).join(' ');
					});
					
					if(save_type == "Save") {
						$("#palette_qid_"+current_qid).addClass("badge-success");
						$("#palette_qid_"+current_qid).attr("data-badge-color", "badge-success");

						$('#btnNext'+current_qid).attr('disabled',false);
						if(current_qid != next_qid) {
							$('#btnNext'+current_qid).html('Save & Next <i class="fas fa-chevron-right"></i>');
						}
						else if(current_qid == next_qid) {
							$('#btnNext'+current_qid).html('Save <i class="fas fa-chevron-right"></i>');
						}
					}
					else if(save_type == "Review") {
						$("#palette_qid_"+current_qid).addClass("badge-warning");
						$("#palette_qid_"+current_qid).attr("data-badge-color", "badge-warning");

						$('#btnMarkForReview'+current_qid).attr('disabled',false);
						$('#btnMarkForReview'+current_qid).html('Mark for Review <i class="fas fa-bookmark"></i>');

					}
					else if(save_type == "NA") {
						$("#palette_qid_"+current_qid).addClass("badge-danger");
						$("#palette_qid_"+current_qid).attr("data-badge-color", "badge-danger");

						$('#btnNext'+current_qid).attr('disabled',false);
						$('#btnNext'+current_qid).html('Save & Next <i class="fas fa-chevron-right"></i>');

					}

					if(current_qid != next_qid) {
						jumpToQuestion(next_qid);
					}
				},
				error: function(xhr, status, error) {
					//console.error("Error:", error);
					sweetAlert("Oops...", "Error while saving answer, please contact technical support team!!", "error");
				}
    		});
		} 
	}

	// Function to handle clicking the "Submit Exam" button
    function submitExam() {
		// Implement logic to navigate to the previous question
        console.log('Navigating to submit exam');

		$(".modal-submit-confirm").modal('show'); 

	}

	$("#btn_submit_assessment").click(function() {
		submitAssessment();
	});

	function submitAssessment() {
		var timeString = $('#timer').text(); //  hours,  minutes,  seconds
		var totalSeconds = timeToSeconds(timeString);

		var formData = new FormData();
		formData.append("candidate_id", '<?php echo $this->session->userdata('candidate_id') ?>');
		formData.append("token", '<?php echo $this->session->userdata('unique_token') ?>');
		formData.append("time_left_secs", totalSeconds);
					
		$.ajax({
			url: "Api-Submit-Theory",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function(response) {
				window.location.href = "candidate-dashboard";
			},
			error: function(xhr, status, error) {
				//console.error("Error:", error);
				sweetAlert("Oops...", "Error while submitting assessment, please contact technical support team!!", "error")
			}
		});
	}

	function highlightRadioOption(qid,option) {
		$('input[type="radio"][name="answer_'+qid+'"][value="'+option+'"]').prop('checked', true);
	}

	function timeToSeconds(timeString) {
		var parts = timeString.split(':');
		var hours = parseInt(parts[0], 10);
		var minutes = parseInt(parts[1], 10);
		var seconds = parseInt(parts[2], 10);
		return hours * 3600 + minutes * 60 + seconds;
	}

	function getFirstNotVisitedQid() {
		var NotVisitedQid = 0;
		$(".questionList").each(function() {
			var id = $(this).attr("id");
			
			if ($("#palette_"+id).hasClass("badge-dark") && NotVisitedQid == 0) { //identify the first not visited Qid
				var newQid = $.trim(id.replace(/qid_/g, ""));
				NotVisitedQid = parseInt(newQid);
			}
		});
		//console.log("NotVisitedQid "+NotVisitedQid);
		NotVisitedQid = (NotVisitedQid > 0) ? NotVisitedQid : '<?php echo $arrQnsList[1];  ?>';
		return NotVisitedQid;	
	}

    // Disable right-click context menu
	document.addEventListener('contextmenu', function(event) {
		event.preventDefault();
	});

	// Disable text selection
	document.addEventListener('selectstart', function(event) {
		event.preventDefault();
	});

	// Disable keyboard shortcuts for copy
	document.addEventListener('keydown', function(event) {
		// Check if Ctrl key (Cmd on Mac) is pressed along with 'c' key
		if ((event.ctrlKey || event.metaKey) && event.key === 'c') {
			event.preventDefault();
		}
	});

	// Disable screenshots
	window.addEventListener('keyup', function(event) {
		// Check if PrtScn (Print Screen) key is pressed
		if (event.key === 'PrintScreen' || event.key === 'PrtScn' || event.key === 'prtscn') {
			event.preventDefault();
			alert('Screenshots are disabled on this page.');
		}
	});
	
	var logoutTimer; // Variable to hold the timer

	// Function to reset the logout timer
	function resetLogoutTimer() {
		//console.log('resetLogoutTimer');
		clearTimeout(logoutTimer); // Clear the existing timer
		var auto_logout_mins = parseInt($('#auto_logout_mins').val());
		//console.log('auto_logout_mins '+auto_logout_mins);
		logoutTimer = setTimeout(logout, auto_logout_mins * 60 * 1000); // Set a new timer for 20 minutes
	}

	// Function to log out the user
	function logout() {
		//console.log('logout');
		// Send an AJAX request to logout.php
		swal({ title: "Auto Logout", text: "You will be logged out due to inactivity!!", timer: 2e3, showConfirmButton: !1 });
		$.ajax({
			url: "<?php echo base_url('candidate-logout'); ?>",
			method: 'post',
			success: function(response){
				//console.log('logged out ');
				window.location.href = "<?php echo base_url('candidate-login'); ?>";
			}
		});
	}

	// Reset the logout timer on user activity
	$(document).on('mousemove keypress', resetLogoutTimer);

	// Initial setup of logout timer
	resetLogoutTimer();
	
</script>
