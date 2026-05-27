<style>
	.list-group-item {
        display: flex;
        align-items: center;
    }

    .list-group-item input[type="radio"] {
        margin-right: 10px;
		
    }
	
	 /* CSS for selected radio button */
    .list-group-item input[type="radio"]:checked + .option-label + label {
    background-color: #3A9B94; /* Set your desired background color for the selected option */
    color: white; /* Set text color for better visibility */
    padding: 8px; /* Optional: Add padding for better appearance */
    font-weight: normal;
    border-color: #bbe6e3;
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
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h4 class="heading mb-0"><?php echo ucwords(str_replace("_"," ",$exam_type)); ?> Questions for Candidate - <?php echo $arr_student_list['enrollment_number']; ?></h4>
				<h3 class="heading mb-0">Batch Id: <?php echo $arr_batch_details['batch_id']; ?></h3>
				<h3 class="heading mb-0">Trade: <?php echo $arr_batch_details['trade_name']; ?></h3>
				<h3 class="heading mb-0">Assessment Date: <?php echo $arr_batch_details['assessment_date']; ?></h3>
				<h3 class="heading mb-0">Duration: <?php echo $arr_batch_details['exam_duration_mins']; ?></h3>
				<div class="d-flex align-items-center">
					<a href="<?php echo base_url(); ?>view-batch-students/<?php echo $tb_id; ?>" class="btn btn-primary btn-sm ms-2"><< Students List</a>
				</div>
			</div>
			<div class="col-lg-12">
                <div class="card">
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
							<div class="card-body questionList" id="qid_<?php echo $q_id; ?>" data-serial_no="<?php echo $counter; ?>">
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
								<?php 
								if($exam_type == 'theory') {
								?>
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
												<input type="radio" name="answer_<?php echo $q_id; ?>" value="<?php echo $option; ?>">
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
								<?php } ?>		
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

<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
