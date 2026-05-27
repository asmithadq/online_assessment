<!--**********************************
            Content body start
        ***********************************-->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor5-build-classic/ckeditor.js"></script>
		
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?= $title ?></h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form class="needs-validation" novalidate id="upload_form" method="post" action="<?= site_url('save-question') ?>">
									<input name="ques_id" id="ques_id" type="hidden" value="<?php echo $rec_ques[0]['qid']; ?>">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <!-- English Section -->
                                        <h3 class="mb-3">English Section</h3>
                                
                                        <!-- Theory Question English -->
                                        <div class="mb-3">
                                            <label for="question_English" class="form-label">Theory Question:</label>
                                            <textarea class="form-control" id="question_English" name="question_English" rows="4" required><?php echo $rec_ques[0]['question']; ?></textarea>
                                            <div class="invalid-feedback">
												Question is required
											</div>
                                        </div>
                                        <!-- Options A, B, C, D English -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="option_a_English" class="form-label">Option A:</label>
                                                <input type="text" class="form-control" id="option_a_English" name="option_a_English" value="<?php echo $rec_ques[0]['option_a']; ?>" required>
                                                <div class="invalid-feedback">
    												Option A is required
    											</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="option_b_English" class="form-label">Option B:</label>
                                                <input type="text" class="form-control" id="option_b_English" value="<?php echo $rec_ques[0]['option_b']; ?>" name="option_b_English" required>
                                                <div class="invalid-feedback">
    												Option B is required
    											</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="option_c_English" class="form-label">Option C:</label>
                                                <input type="text" class="form-control" id="option_c_English" name="option_c_English" value="<?php echo $rec_ques[0]['option_c']; ?>" required>
                                                <div class="invalid-feedback">
    												Option C is required
    											</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="option_d_English" class="form-label">Option D:</label>
                                                <input type="text" class="form-control" id="option_d_English" name="option_d_English" value="<?php echo $rec_ques[0]['option_d']; ?>" required>
                                                <div class="invalid-feedback">
    												Option D is required
    											</div>
                                            </div>
                                        </div>
										
										<?php foreach($rec_ques as $row) { ?>
                                        
                                        <h3 class="mb-3"><?php echo $row['language_name']; ?> Section</h3>
                                
                                        <div class="mb-3">
                                            <label for="question_<?php echo $row['language_name']; ?>" class="form-label">Theory Question:</label>
                                            <textarea class="form-control" id="question_<?php echo $row['language_name']; ?>" name="question_<?php echo $row['language_name']; ?>" rows="4"><?php echo $row['lan_ques']; ?></textarea>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="option_a_<?php echo $row['language_name']; ?>" class="form-label">Option A:</label>
                                                <input type="text" class="form-control" id="option_a_<?php echo $row['language_name']; ?>" name="option_a_<?php echo $row['language_name']; ?>" value="<?php echo $row['lan_option_a']; ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="option_b_<?php echo $row['language_name']; ?>" class="form-label">Option B:</label>
                                                <input type="text" class="form-control" id="option_b_<?php echo $row['language_name']; ?>" name="option_b_<?php echo $row['language_name']; ?>" value="<?php echo $row['lan_option_b']; ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="option_c_<?php echo $row['language_name']; ?>" class="form-label">Option C:</label>
                                                <input type="text" class="form-control" id="option_c_<?php echo $row['language_name']; ?>" name="option_c_<?php echo $row['language_name']; ?>" value="<?php echo $row['lan_option_c']; ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="option_d_<?php echo $row['language_name']; ?>" class="form-label">Option D:</label>
                                                <input type="text" class="form-control" id="option_d_<?php echo $row['language_name']; ?>" name="option_d_<?php echo $row['language_name']; ?>" value="<?php echo $row['lan_option_d']; ?>">
                                            </div>
                                        </div>
										
										<script>
											let editor<?php echo $row['language_name']; ?>

											ClassicEditor
												.create( document.querySelector( '#question_<?php echo $row['language_name']; ?>' ) )
												.then( newEditor => {
													editor<?php echo $row['language_name']; ?> = newEditor;
												} )
												.catch( error => {
													console.error( error );
											} );
										</script>
										
										<?php } ?>
										
										
										
                                        <!-- Actions Section -->
                                        <h3 class="mb-3">Actions</h3>
                                
                                        <!-- Additional Information -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <?php
                                                $arrQuestionTypeColFormat = array('theory' => 'Theory','practicalskill' => 'PracticalSkill','practicalactivity' => 'PracticalActivity','viva' => 'Viva');
                                                ?>
                                                <label for="questionType" class="form-label">Question Type:</label>
                                                <select class="form-control"  id="questionType" name="questionType">
                                                    <?php
                                                    foreach($arrQuestionTypeColFormat as $questionType => $questionTypeText) {
                                                    ?>
                                                        <option value="<?php echo $questionTypeText; ?>" <?php if($questionTypeText == $rec_ques[0]['question_type']) { ?>selected <?php }?> ><?php echo $questionTypeText; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
    												Question Type is required
    											</div>
                                            </div>
                                            <div class="col-md-3">
                                                <?php $arrAnsTypeColFormat = array('a','b','c','d'); ?>
                                                <label for="correctAnswer" class="form-label">Correct Answer:</label>
                                                <select class="form-control"  id="correctAnswer" name="correctAnswer">
                                                    <?php
                                                    foreach($arrAnsTypeColFormat as $correctAnswer => $correctAnswerText) {
                                                    ?>
                                                        <option value="<?php echo $correctAnswerText; ?>" <?php if($correctAnswerText == $rec_ques[0]['correct_ans']) { ?>selected <?php }?> ><?php echo $correctAnswerText; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
    												Correct Answer is required
    											</div>
                                            </div>
                                
                                            <div class="col-md-3">
                                                <label for="marks" class="form-label">Marks:</label>
                                                <input type="number" class="form-control" id="marks" name="marks" value="<?php echo $rec_ques[0]['marks']; ?>" required>
                                                <div class="invalid-feedback">
    												Marks is required
    											</div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button> 
                                    </form>
                                </div>
                            </div>
                        </div>
					</div>
                </div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
<script>
	let editor;

	ClassicEditor
		.create( document.querySelector( '#question_English' ) )
		.then( newEditor => {
			editor = newEditor;
		} )
		.catch( error => {
			console.error( error );
    } );
</script>	