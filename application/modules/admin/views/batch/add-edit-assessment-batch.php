        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="d-flex justify-content-between align-items-center mb-4">
						<h4 class="heading mb-0">&nbsp;</h4>
						<div class="d-flex align-items-center">
							<a href="<?php echo base_url(); ?>list-batches-inprocess" class="btn btn-primary btn-sm ms-2"><< Training Batches List</a>
						</div>
					</div>
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo ($tb_id > 0) ? 'Update' : 'Create'; ?> Assessment Batch</h4>
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
                                <div class="basic-form">
                                    <form class="needs-custom-validation" novalidate id="frmBatch" method="post" action="<?= site_url('save-batch') ?>" autocomplete="OFF">
                                        <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input name="tb_id" id="tb_id" type="hidden" value="<?php echo $tb_id; ?>">
                                        <input name="hdn_subscheme_id" id="hdn_subscheme_id" type="hidden" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['subscheme_id'] : ''; ?>">
                                        <input name="hdn_trade_id" id="hdn_trade_id" type="hidden" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['trade_id'] : ''; ?>">
                                        <input name="hdn_tp_id" id="hdn_tp_id" type="hidden" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['tp_id'] : ''; ?>">
                                        <input name="hdn_tc_id" id="hdn_tc_id" type="hidden" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['tc_id'] : ''; ?>">
                                        <input name="hdn_assessor_id" id="hdn_assessor_id" type="hidden" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['assessor_id'] : ''; ?>">
                                       <h4>Batch Information</h4><hr/>
                                        <div class="row">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Assessment Agency</label>
                                                <span class="text-danger">*</span>
                                                <select id="ag_id" name="ag_id" class="form-control" required>
                                                    <option value="" <?php echo ($tb_id > 0 && $arr_batch_details[0]['ag_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_assessment_agency as $assessment_agency) {
                                                    ?>
                                                        <option value="<?php echo $assessment_agency['ag_id']; ?>" <?php echo ($tb_id > 0 && $arr_batch_details[0]['ag_id'] == $assessment_agency['ag_id']) ? 'selected' : ''; ?>><?php echo $assessment_agency['ag_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Scheme</label>
                                                <span class="text-danger">*</span>
                                                <select id="scheme_id" name="scheme_id" class="form-control" required>
                                                    <option value="" <?php echo ($tb_id > 0 && $arr_batch_details[0]['scheme_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_schemes as $scheme) {
                                                    ?>
                                                        <option value="<?php echo $scheme['scheme_id']; ?>" <?php echo ($tb_id > 0 && $arr_batch_details[0]['scheme_id'] == $scheme['scheme_id']) ? 'selected' : ''; ?>><?php echo $scheme['scheme_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">Subscheme</label>
                                                <span class="text-danger">*</span>
                                                <select id="subscheme_id" name="subscheme_id" class="form-control" required>
                                                    <option data-scheme_id="" value="" <?php echo ($tb_id > 0 && $arr_batch_details[0]['subscheme_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_sub_schemes as $sub_scheme) {
                                                    ?>
                                                        <option data-scheme_id="<?php echo $sub_scheme['scheme_id']; ?>" value="<?php echo $sub_scheme['subscheme_id']; ?>" <?php echo ($tb_id > 0 && $arr_batch_details[0]['subscheme_id'] == $sub_scheme['subscheme_id']) ? 'selected' : ''; ?>><?php echo $sub_scheme['subscheme_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Sector Skill Council</label>
                                                <span class="text-danger">*</span>
                                                <select id="ssc_id" name="ssc_id" class="form-control" required>
                                                    <option value="" <?php echo ($tb_id > 0 && $arr_batch_details[0]['ssc_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_ssc as $ssc) {
                                                    ?>
                                                        <option value="<?php echo $ssc['ssc_id']; ?>" <?php echo ($tb_id > 0 && $arr_batch_details[0]['ssc_id'] == $ssc['ssc_id']) ? 'selected' : ''; ?>><?php echo $ssc['ssc_title'].' ('.$ssc['ssc_code'].')'; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Trade/QP Name</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="trade_id" id="trade_id" placeholder="" required>
                                                    <option value="">Choose...</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">Training Partner</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="tp_id" id="tp_id" placeholder="" required>
                                                    <option value="">Choose...</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">Training Center</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="tc_id" id="tc_id" placeholder="" required>
                                                    <option value="">Choose...</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">Batch ID</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="batch_id" id="batch_id" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['batch_id'] : ''; ?>" required>
                                                <div class="invalid-feedback" id="err_batch_id">
													Batch Id is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Students</label>
                                                <span class="text-danger">*</span>
                                                 <input type="number" class="form-control" name="tb_target" id="tb_target" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['tb_target'] : ''; ?>" required>
    										    <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Center SPOC Name</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="text" class="form-control" name="spoc_name" id="spoc_name" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['spoc_name'] : ''; ?>">
    										    <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Center SPOC Mobile</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="number" class="form-control" name="spoc_mobile" id="spoc_mobile" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['spoc_mobile'] : ''; ?>">
    										    <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Assessor</label>
                                                <select class="form-control" name="assessor_id" id="assessor_id" placeholder="">
                                                    <option value="">Choose...</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                        </div>
                                        <h4>Assessment Details</h4><hr/>
                                        <div class="row">
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">PDA Date</label>
                                                <span class="text-danger">*</span>
                                                <input type="date" class="form-control" name="tb_assessment_date" id="tb_assessment_date" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['tb_assessment_date'] : ''; ?>" required>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">Start Date & Time</label>
                                                <span class="text-danger">*</span>
                                                <input type="datetime-local" id="tb_start_date_time" name="tb_start_date_time"  class="form-control" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['tb_start_date_time'] : ''; ?>" required>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">End Date & Time</label>
                                                <span class="text-danger">*</span>
                                                 <input type="datetime-local" id="tb_end_date_time" name="tb_end_date_time" class="form-control" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['tb_end_date_time'] : ''; ?>" required>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Exam Duration <code>(in Mins)</code></label>
                                                <span class="text-danger">*</span>
                                                <input type="number" class="form-control" name="exam_duration_mins" id="exam_duration_mins" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['exam_duration_mins'] : ''; ?>" required>
    										    <code id="duration" style="display:none"></code>
                                                 <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Regional Language</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <select class="form-control" name="lid" id="lid" placeholder="">
                                                    <option value="" <?php echo ($tb_id > 0 && $arr_batch_details[0]['lid'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_languages as $lang) {
                                                        $selected = "";
                                                        if($tb_id > 0 && $arr_batch_details[0]['lid'] == $lang['language_id']) {
                                                            $selected = "selected";
                                                        }
                                                        else if($tb_id == 0 && $lang['default_language'] == 1) {
                                                            $selected = "selected";
                                                        }
                                                    ?>
                                                        <option value="<?php echo $lang['language_id']; ?>" <?php echo $selected; ?>><?php echo $lang['language_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-3">
                                                <label class="form-label">Assessment Type</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="tb_exam_type" id="" placeholder="tb_exam_type" required>
                                                    <option value="">Choose...</option>
                                                    <option value="Online" <?php echo (($tb_id > 0 && $arr_batch_details[0]['tb_exam_type'] == 'Online') || $tb_id == 0) ? 'selected' : ''; ?>>Online</option>
                                                    <option value="Offline" <?php echo ($tb_id > 0 && $arr_batch_details[0]['tb_exam_type'] == 'Offline') ? 'selected' : ''; ?>>Offline</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Questions Pattern</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="qp_shuffling" id="qp_shuffling" placeholder="" required>
                                                    <option value="">Choose...</option>
                                                    <option value="Random" <?php echo ($tb_id > 0 && $arr_batch_details[0]['qp_shuffling'] == 'Random') ? 'selected' : ''; ?>>Random</option>
                                                    <option value="Shuffled" <?php echo ($tb_id > 0 && $arr_batch_details[0]['qp_shuffling'] == 'Shuffled') ? 'selected' : ''; ?>>Shuffled</option>
                                                    <option value="Same" <?php echo (($tb_id > 0 && $arr_batch_details[0]['qp_shuffling'] == 'Same') || $tb_id == 0) ? 'selected' : ''; ?>>Same</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Student Snapshots</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="take_snapshots" id="take_snapshots" placeholder="" required>
                                                    <option value="">Choose...</option>
                                                    <option value="Yes" <?php echo (($tb_id > 0 && $arr_batch_details[0]['take_snapshots'] == 'Yes') || $tb_id == 0) ? 'selected' : ''; ?>>Yes</option>
                                                    <option value="No" <?php echo ($tb_id > 0 && $arr_batch_details[0]['take_snapshots'] == 'No') ? 'selected' : ''; ?>>No</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>																						
                                            <div class="mb-3 col-md-2">                                                
                                                <label class="form-label">Aadhar Verification</label>                                                
                                                <span class="text-danger">*</span>                                                
                                                <select class="form-control" name="aadhar_verification" id="aadhar_verification" placeholder="" required>                                                    
                                                    <option value="">Choose...</option>                                                    
                                                    <option value="Mandatory" <?php echo (($tb_id > 0 && $arr_batch_details[0]['aadhar_verification'] == 'Mandatory') || $tb_id == 0) ? 'selected' : ''; ?>>Mandatory</option>                                                    
                                                    <option value="Optional" <?php echo ($tb_id > 0 && $arr_batch_details[0]['aadhar_verification'] == 'Optional') ? 'selected' : ''; ?>>Optional</option>                                                
                                                </select>                                                
                                                <div class="invalid-feedback">													
                                                    This Field is required.												
                                                </div>                                            
                                            </div>
                                            <div class="mb-3 col-md-2">                                                
                                                <label class="form-label">Candidate Profile Updation</label>                                                
                                                <span class="text-danger">*</span>                                                
                                                <select class="form-control" name="profile_updation" id="profile_updation" placeholder="" required>                                                    
                                                    <option value="">Choose...</option>                                                    
                                                    <option value="Mandatory" <?php echo (($tb_id > 0 && $arr_batch_details[0]['profile_updation'] == 'Mandatory') || $tb_id == 0) ? 'selected' : ''; ?>>Mandatory</option>                                                    
                                                    <option value="Optional" <?php echo ($tb_id > 0 && $arr_batch_details[0]['profile_updation'] == 'Optional') ? 'selected' : ''; ?>>Optional</option>                                                
                                                </select>                                                
                                                <div class="invalid-feedback">													
                                                    This Field is required.												
                                                </div>                                            
                                            </div>	
                                            <div class="mb-3 col-md-2">                                                
                                                <label class="form-label">Practical Activity</label>                                                
                                                <span class="text-danger">*</span>                                                
                                                <select class="form-control" name="practical_answer_type" id="practical_answer_type" placeholder="" required>                                                    
                                                    <option value="">Choose...</option>                                                    
                                                    <option value="Video" <?php echo (($tb_id > 0 && $arr_batch_details[0]['practical_answer_type'] == 'Video') || $tb_id == 0) ? 'selected' : ''; ?>>Video Recording</option>                                                    
                                                    <option value="Descriptive" <?php echo ($tb_id > 0 && $arr_batch_details[0]['practical_answer_type'] == 'Descriptive') ? 'selected' : ''; ?>>Descriptive</option>                                                
                                                </select>                                                
                                                <div class="invalid-feedback">													
                                                    This Field is required.												
                                                </div>                                            
                                            </div>
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Duration<code>(Mins)</code></label> 
                                                <span class="text-danger">*</span>
                                                <input type="number" class="form-control" name="practicalactivity_duration_mins" id="practicalactivity_duration_mins" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['practicalactivity_duration_mins'] : ''; ?>" required>
    										    <code id="practicalactivity_duration" style="display:none"></code>
                                                 <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>																						
                                            <div class="mb-3 col-md-2">                                                
                                                <label class="form-label">Viva</label>                                                
                                                <span class="text-danger">*</span>                                                
                                                <select class="form-control" name="viva_answer_type" id="viva_answer_type" placeholder="" required>                                                    
                                                    <option value="">Choose...</option>                                                    
                                                    <option value="Video" <?php echo (($tb_id > 0 && $arr_batch_details[0]['viva_answer_type'] == 'Video') || $tb_id == 0) ? 'selected' : ''; ?>>Video Recording</option>                                                    
                                                    <option value="Descriptive" <?php echo ($tb_id > 0 && $arr_batch_details[0]['viva_answer_type'] == 'Descriptive') ? 'selected' : ''; ?>>Descriptive</option>                                                
                                                </select>                                                
                                                <div class="invalid-feedback">													
                                                    This Field is required.												
                                                </div>                                            
                                            </div>	
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Duration<code>(Mins)</code></label>
                                                <span class="text-danger">*</span>
                                                <input type="number" class="form-control" name="viva_duration_mins" id="viva_duration_mins" value="<?php echo ($tb_id > 0) ? $arr_batch_details[0]['viva_duration_mins'] : ''; ?>" required>
    										    <code id="viva_duration" style="display:none"></code>
                                                 <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>										
                                            <div class="mb-3 col-md-2">
                                                <label class="form-label">Assessment Status</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="tb_assessment_status" id="tb_assessment_status" placeholder="" required>
                                                    <option value="">Choose...</option>
                                                    <option value="Pending" <?php echo (($tb_id > 0 && $arr_batch_details[0]['tb_assessment_status'] == 'Pending') || $tb_id == 0) ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Completed" <?php echo ($tb_id > 0 && $arr_batch_details[0]['tb_assessment_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                        </div>
                                         <h4>Exam Instructions</h4><hr/>
                                        <div class="row">
                                             <div class="mb-3 col-md-12">
                                                <label class="form-label">Theory Instructions</label>
                                                
                                               <textarea type="text" class="form-control" name="theory_instructions" id="theory_instructions"><?php echo ($tb_id > 0) ? $arr_batch_details[0]['theory_instructions'] : ''; ?></textarea>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-12">
                                                <label class="form-label">Practical Activity Instructions</label>
                                                <textarea type="text" class="form-control" name="practical_activity_instructions" id="practical_activity_instructions"><?php echo ($tb_id > 0) ? $arr_batch_details[0]['practical_activity_instructions'] : ''; ?></textarea>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                              <div class="mb-3 col-md-12">
                                                <label class="form-label">Viva Instructions</label>
                                                <textarea type="text" class="form-control" name="viva_instructions" id="viva_instructions"><?php echo ($tb_id > 0) ? $arr_batch_details[0]['viva_instructions'] : ''; ?></textarea>
                                                <div class="invalid-feedback">
													This Field is required.
												</div>
                                            </div>
                                        </div>
                                         <button type="submit" class="btn btn-primary" id="btn_save"><?php echo ($tb_id > 0) ? 'Update' : 'Add'; ?> Record</button>
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

        <!-- Font Awesome CSS -->
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor5-build-classic/ckeditor.js"></script>
        
        <script>
        let theory_instructions;
        let practical_activity_instructions;
        let viva_instructions;

        ClassicEditor
            .create( document.querySelector( '#theory_instructions' ) )
            .then( newEditor => {
                theory_instructions = newEditor;
            } )
            .catch( error => {
                console.error( error );
        });
        
        ClassicEditor
            .create( document.querySelector( '#practical_activity_instructions' ) )
            .then( newEditor => {
                practical_activity_instructions = newEditor;
            } )
            .catch( error => {
                console.error( error );
        });

        ClassicEditor
            .create( document.querySelector( '#viva_instructions' ) )
            .then( newEditor => {
                viva_instructions = newEditor;
            } )
            .catch( error => {
                console.error( error );
        });


        $(document).ready(function() {
            getSubSchemes();
            getTradesBySsc();
            getPartnersBySsc();
            getCentersByPartner();
            getAssessorsBySsc();
            //convertDateTimeToHoursAndMinutes();
            //convertMinsToHoursAndMinutes('practicalactivity_duration_mins','practicalactivity_duration');
            //convertMinsToHoursAndMinutes('viva_duration_mins','viva_duration');

            $("#ag_id").select2();    
            $("#tp_id").select2();
            $("#trade_id").select2();  
            $("#assessor_id").select2();  
            
            $("#tp_id").on('select2:select select2:unselect', function(e) {
                getCentersByPartner();
            });
            
            function getCentersByPartner() {
                var tp_id = $("#tp_id option:selected").val();
                var ssc_id = $("#ssc_id option:selected").val();
                var tc_id = $("#hdn_tc_id").val();
                
                if(ssc_id > 0 && tp_id > 0) {
                    // CSRF Hash
                    var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                    var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                    
                    // AJAX request
                    $.ajax({
                        url: "<?php echo base_url('get-centers-by-partner'); ?>",
                        method: 'post',
                        data: { tc_id: tc_id,ssc_id: ssc_id,tp_id: tp_id,[csrfName]: csrfHash },
                        dataType: 'json',
                        success: function(response){
                        //console.log('output '+response.output); 
                        // Update CSRF hash
                        $('.txt_csrfname').val(response.token);
                        $("#tc_id").html(response.output);
                        }
                    });
                }    
            }
            
            $('#batch_id').blur(function() {
                var tb_id = $("#tb_id").val();
                var batch_id = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_batch_id").html("Please enter Batch Id.");
                $("#err_batch_id").hide();

                // Remove special characters using a regular expression
                var sanitizedValue = batch_id.replace(/[^a-zA-Z0-9\s\-_/]/g, '');  

                // Update the input value
                $(this).val(sanitizedValue);
                
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-batch-id'); ?>",
                    method: 'post',
                    data: { tb_id: tb_id,batch_id: batch_id,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_batch_id").html(batch_id+" this Batch Id already exists!");
                         $("#err_batch_id").show();
                         $("#batch_id").val('');
                      }
                    }
                 });
            });

            $("#scheme_id").change(function() {
                getSubSchemes();
                $("#subscheme_id").val('');
            });
            
            function getSubSchemes() {
            var scheme_id = $("#scheme_id option:selected").val();
            var selected_subscheme_id = $("#subscheme_id").val();
            //console.log('scheme_id '+scheme_id);
            //console.log('subscheme_id '+subscheme_id);
            
            $("#subscheme_id option").hide();
            
            $("#subscheme_id option[data-scheme_id='"+scheme_id+"']").show();
            if(selected_subscheme_id > 0) {
                $("#subscheme_id").val(selected_subscheme_id);
            }
            
            }
            
            $("#ssc_id").change(function() {
                getTradesBySsc();
                getPartnersBySsc();
                getAssessorsBySsc();
            });
            
            function getTradesBySsc() {
                var ssc_id = $("#ssc_id option:selected").val();
                var trade_id = $("#hdn_trade_id").val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                if(ssc_id > 0) {
                
                    // AJAX request
                    $.ajax({
                        url: "<?php echo base_url('get-trades-by-ssc'); ?>",
                        method: 'post',
                        data: { trade_id: trade_id,ssc_id: ssc_id,[csrfName]: csrfHash },
                        dataType: 'json',
                        success: function(response){
                        //console.log('output '+response.output); 
                        // Update CSRF hash
                        $('.txt_csrfname').val(response.token);
                        $("#trade_id").html(response.output);
                        }
                    });
                }    
            }
            
            function getPartnersBySsc() {
                var ssc_id = $("#ssc_id option:selected").val();
                var tp_id = $("#hdn_tp_id").val();
                
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                if(ssc_id > 0) {
                
                    // AJAX request
                    $.ajax({
                        url: "<?php echo base_url('get-partners-by-ssc'); ?>",
                        method: 'post',
                        data: { tp_id: tp_id,ssc_id: ssc_id,[csrfName]: csrfHash },
                        dataType: 'json',
                        success: function(response){
                        // console.log('output '+response.output); 
                        // Update CSRF hash
                        $('.txt_csrfname').val(response.token);
                        $("#tp_id").html(response.output);
                        getCentersByPartner();
                        }
                    });
                } 
            }
            
            $("#tc_id").change(function() {
                var spoc_name = $("#tc_id option:selected").attr('data-spoc_name');
                var spoc_mobile = $("#tc_id option:selected").attr('data-spoc_mobile');
                
                $('#spoc_name').val(spoc_name);
                $('#spoc_mobile').val(spoc_mobile);
                
            });
            
            function getAssessorsBySsc() {
                var ssc_id = $("#ssc_id option:selected").val();
                var assessor_id = $("#hdn_assessor_id").val();
                
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                if(ssc_id > 0) {
                
                    // AJAX request
                    $.ajax({
                        url: "<?php echo base_url('get-assessors-by-ssc'); ?>",
                        method: 'post',
                        data: { assessor_id: assessor_id,ssc_id: ssc_id,[csrfName]: csrfHash },
                        dataType: 'json',
                        success: function(response){
                        // console.log('output '+response.output); 
                        // Update CSRF hash
                        $('.txt_csrfname').val(response.token);
                        $("#assessor_id").html(response.output);
                        }
                    });
                } 
            }

            $('#tb_assessment_date').on('input', function() {
                // Get the date from tb_assessment_date
                let assessmentDate = $(this).val();
                
                if (assessmentDate) {
                    // Set the time to 9:00 AM
                    let startDateTime = assessmentDate + 'T09:00';
                    let endDateTime   = assessmentDate + 'T20:00';
                    
                    // Populate tb_start_date_time,tb_end_date_time with the formatted datetime
                    $('#tb_start_date_time').val(startDateTime);
                    $('#tb_end_date_time').val(endDateTime);
                    convertDateTimeToHoursAndMinutes();
                }
            });

            $("#tb_start_date_time, #tb_end_date_time").on("change", function() { 
                convertDateTimeToHoursAndMinutes();
            });

            $("#practicalactivity_duration_mins").on("keyup", function() {
                convertMinsToHoursAndMinutes('practicalactivity_duration_mins','practicalactivity_duration');
            });

            $("#viva_duration_mins").on("keyup", function() {
                convertMinsToHoursAndMinutes('viva_duration_mins','viva_duration');
            });

            function convertDateTimeToHoursAndMinutes(e) {
                // Get the start and end date-time values
                var startDateTime = new Date($("#tb_start_date_time").val());
                var endDateTime = new Date($("#tb_end_date_time").val());
                $("#duration").show();

                // Check if both start and end date-times are valid
                if (!isNaN(startDateTime) && !isNaN(endDateTime)) {
                    // Calculate the difference in milliseconds
                    var durationInMilliseconds = Math.abs(endDateTime - startDateTime);

                    // Calculate total minutes
                    var totalMinutes = Math.floor(durationInMilliseconds / (60 * 1000));

                    // Display the calculated total minutes
                    $("#exam_duration_mins").val(totalMinutes);

                    // Calculate hours and remaining minutes
                    var hours = Math.floor(totalMinutes / 60);
                    var remainingMinutes = totalMinutes % 60;

                    // Display the calculated duration in hours and minutes
                    $("#duration").text(hours + " Hrs:" + remainingMinutes + " Mins");
                } else {
                    // Display an error message if the date-time inputs are invalid
                    $("#exam_duration_mins").val("");
                    $("#duration").text("Invalid date-time inputs");
                }
            }

            function convertMinsToHoursAndMinutes(fieldName,displayName) {
                $("#"+displayName).show();
                var totalMinutes = $("#"+fieldName).val();

                // Check if minutes is valid
                if (!isNaN(totalMinutes)) {
                    // Calculate hours and remaining minutes
                    var hours = Math.floor(totalMinutes / 60);
                    var remainingMinutes = totalMinutes % 60;

                    // Display the calculated duration in hours and minutes
                    $("#"+displayName).text(hours + " Hrs:" + remainingMinutes + " Mins");
                } else {
                    // Display an error message if the date-time inputs are invalid
                    $("#"+fieldName).val("");
                    $("#"+displayName).text("Invalid date-time inputs");
                }
            }
            
            
        });

        $(document).ready(function(){
            // Attach keypress event listener to the input field
            $('#tb_target,#exam_duration_mins,#practicalactivity_duration_mins,#viva_duration_mins').keypress(function(event) {
                // Allow backspace, delete, tab, escape, enter, and .
                if(event.which == 8 || event.which == 0 || event.which == 46 || event.which == 9 || event.which == 27 || event.which == 13) {
                    return true;
                }
                // Allow only numbers
                if(event.which < 48 || event.which > 57) {
                    event.preventDefault();
                }
            });

            // Validate input value when focus is lost
            $('#tb_target,#exam_duration_mins,#practicalactivity_duration_mins,#viva_duration_mins').blur(function() {
                var value = $(this).val();
                // Check if value is a positive number
                if(value <= 0) {
                    $(this).val('');
                    alert('Please enter a positive number.');
                }
            });
        });

        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-custom-validation')
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault()
                    event.stopPropagation()
                    if (form.checkValidity()) {
                        $('#btn_save').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
                        $('#btn_save').attr('disabled',true);
                        form.submit();
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
        
    </script> 
