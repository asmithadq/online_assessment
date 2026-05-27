  <style>  code{	  font-size:1.2rem;  }  </style>  <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="d-flex justify-content-between align-items-center mb-4">
						<h4 class="heading mb-0">&nbsp;</h4>
						<div class="d-flex align-items-center">
							<a href="<?php echo base_url(); ?>list-trades" class="btn btn-primary btn-sm ms-2">Trade/QP Listing</a>
						</div>
					</div>
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?= $title ?></h4>
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
                                    <form class="needs-custom-validation" novalidate id="frmTrade" method="post" action="<?= site_url('save-trade-nos') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                        <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="trade_id" name="trade_id" value="<?php echo $trade_id; ?>">
                                        <input type="hidden" id="hdn_trade_mapped_nos_master_ids" name="hdn_trade_mapped_nos_master_ids" value="<?php echo ($trade_id > 0) ? implode(",",$arr_trade_mapped_nos_master_ids) : 0; ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Trade/QP Code</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="trade_code" id="trade_code" placeholder="Enter Trade/QP Code..." value="<?php echo ($trade_id > 0) ? $arr_trade_details[0]['trade_code'] : ""; ?>" required>
                                                <div class="invalid-feedback" id="err_trade_code">
													Trade/QP Code is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Trade/QP Name</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="trade_name" id="trade_name" placeholder="Enter Trade/QP Name..." value="<?php echo ($trade_id > 0) ? $arr_trade_details[0]['trade_name'] : ""; ?>" required>
                                                 <div class="invalid-feedback">
													Trade/QP Name is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Sector Skill Council</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="ssc_id" id="ssc_id" placeholder="Select SSC Name..." required>
                                                    <option value="" <?php echo ($trade_id > 0 && $arr_trade_details[0]['ssc_id'] == "") ? 'selected' : ''; ?>>-Select-</option>
                                                    <?php 
                                                    foreach($arr_ssc as $ssc) { 
                                                    ?>
                                                        <option value="<?php echo $ssc['ssc_id']; ?>" <?php echo ($trade_id > 0 && $arr_trade_details[0]['ssc_id'] == $ssc['ssc_id']) ? 'selected' : ''; ?>><?php echo $ssc['ssc_title'].' ('.$ssc['ssc_code'].')'; ?></option>
                                                    <?php 
                                                    } 
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													Sector Skill Council is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Pass percentage</label><span class="text-danger">*</span>
                                               <input type="text" class="form-control" name="pass_percentage" id="pass_percentage" placeholder="Enter Percentage..." value="<?php echo ($trade_id > 0) ? $arr_trade_details[0]['pass_percentage'] : ""; ?>" required>
                                                 <div class="invalid-feedback">
													Pass Percentage is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">NSFQ Level</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="nsfq_id" id="nsfq_id" placeholder="Select NSFQ Level..." required>
                                                    <option value="" <?php echo ($trade_id > 0 && $arr_trade_details[0]['nsfq_id'] == "") ? 'selected' : ''; ?>>-Select-</option>
                                                    <?php 
                                                    foreach($arr_nsfq_level as $nsfq_level) { 
                                                    ?>
                                                        <option value="<?php echo $nsfq_level['nsfq_id']; ?>" <?php echo ($trade_id > 0 && $arr_trade_details[0]['nsfq_id'] == $nsfq_level['nsfq_id']) ? 'selected' : ''; ?>><?php echo $nsfq_level['nsfq_level']; ?></option>
                                                    <?php 
                                                    } 
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    NSFQ Level is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Trade Version</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="trade_version_id" id="trade_version_id" placeholder="Select Trade Version..." required>
                                                    <option value="" <?php echo ($trade_id > 0 && $arr_trade_details[0]['trade_version_id'] == "") ? 'selected' : ''; ?>>-Select-</option>
                                                    <?php 
                                                    foreach($arr_trade_version as $trade_version) { 
                                                    ?>
                                                        <option value="<?php echo $trade_version['trade_version_id']; ?>" <?php echo ($trade_id > 0 && $arr_trade_details[0]['trade_version_id'] == $trade_version['trade_version_id']) ? 'selected' : ''; ?>><?php echo $trade_version['trade_version']; ?></option>
                                                    <?php 
                                                    } 
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                Trade Version is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Number of NOS</label><span class="text-danger">*</span>
                                                <select name="no_of_nos" id="no_of_nos" class="form-control" placeholder="Select Number of NOS..."  required>
                                                    <option value="" <?php echo ($trade_id > 0 && $arr_trade_details[0]['no_of_nos'] == "") ? 'selected' : ''; ?>>Please select</option>
                                                    <?php
                                                    for($i=1; $i<=20; $i++) {
                                                    ?>
                                                        <option value="<?php echo $i; ?>" <?php echo ($trade_id > 0 && $arr_trade_details[0]['no_of_nos'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                                    <?php
                                                    }
                                                    ?>
        										</select>
                                                <div class="invalid-feedback">
												    Number of NOS is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">NQR Code</label>
                                                <input type="text" class="form-control" name="nqr_code" id="nqr_code" placeholder="Enter NQR Code..." value="<?php echo ($trade_id > 0) ? $arr_trade_details[0]['nqr_code'] : ""; ?>" required>
                                                <div class="invalid-feedback" id="err_nqr_code">
													NQR Code is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Status</label>
                                                <span class="text-danger">*</span>
                                                 <select name="status" id="status" class="form-control" required>
                                                    <!--<option value="" <?php //echo ($trade_id > 0 && $arr_trade_details[0]['status'] == "") ? 'selected' : ''; ?>>-Select-</option>-->
        											<option value="1" <?php echo ($trade_id > 0 && $arr_trade_details[0]['status'] == 1) ? 'selected' : ''; ?>>Active</option>
        											<option value = "0" <?php echo ($trade_id > 0 && $arr_trade_details[0]['status'] == 0) ? 'selected' : ''; ?>>In-active</option>
    										    </select>
    										    <div class="invalid-feedback">
													Status is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">De-select Optional Fields</label>
                                                <div class="form-check">
                                                    <?php 
                                                        $arrOptionalExamType = ($trade_id > 0) ? explode(",",$arr_trade_details[0]['optional_exam_type']) : array('theory','practicalSkill','practicalActivity','viva');
                                                    ?>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" value="theory" id="chk_theory" name="optional_exam_type[]" <?php echo (in_array('theory',$arrOptionalExamType)) ? 'checked' : ""; ?>>
                                                        <label class="form-check-label">Theory</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" value="practicalSkill" id="chk_practicalSkill" name="optional_exam_type[]" <?php echo (in_array('practicalSkill',$arrOptionalExamType)) ? 'checked' : ""; ?>>
                                                        <label class="form-check-label">Practical Skills</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" value="practicalActivity" id="chk_practicalActivity" name="optional_exam_type[]" <?php echo (in_array('practicalActivity',$arrOptionalExamType)) ? 'checked' : ""; ?>>
                                                        <label class="form-check-label">Practical Activity</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" value="viva" id="chk_viva" name="optional_exam_type[]" <?php echo (in_array('viva',$arrOptionalExamType)) ? 'checked' : ""; ?>>
                                                        <label class="form-check-label">Viva</label>
                                                    </div>
                                                </div> 
                                            </div>    
                                        </div>
                                        <h3 class="nosBlock" style="display:none;">Assign NOS & Marks</h3><hr/>
                                        <?php
                                        for($i=0; $i<=19; $i++) {
                                        ?>
                                            <div class="row nosBlock_<?php echo $i ?>" style="display:none;">
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">NOS <?php echo ($i+1); ?></label>
                                                    <span class="text-danger">*</span>
                                                     <select name="nos_id[<?php echo $i; ?>]" id="nos_id_<?php echo $i; ?>" class="form-control single-select selNos" onchange="validate_nos(<?php echo ($i); ?>);">
                                                        <option value="" <?php echo ($trade_id > 0 && array_key_exists($i,$arr_trade_nos_details) && $arr_trade_nos_details[$i]['nos_id'] == "") ? 'selected' : ''; ?>>Please select</option>
                                                        <?php 
                                                        foreach($arr_nos as $nos) { 
                                                        ?>
                                                            <option value="<?php echo $nos['nos_id']; ?>" <?php echo ($trade_id > 0 &&  array_key_exists($i,$arr_trade_nos_details) && $arr_trade_nos_details[$i]['nos_id'] == $nos['nos_id']) ? 'selected' : ''; ?>><?php echo $nos['nos_code'].'-'.$nos['nos_title']; ?></option>
                                                        <?php 
                                                        } 
                                                        ?>
        										    </select>
        										    <div class="invalid-feedback">
    													NOS is required.
    												</div>
                                                </div>
                                                <div class="mb-3 col-md-2 div_theory_marks">
                                                    <label class="form-label">Theory Marks</label>
                                                    <span class="text-danger spn_theory">*</span>
                                                    <input type="number" min="1" class="form-control theory" name="theory_marks[<?php echo $i; ?>]" id="theory_marks_<?php echo $i; ?>" onkeyup="calculateNosMarks();" value="<?php echo ($trade_id > 0 && array_key_exists($i,$arr_trade_nos_details)) ? $arr_trade_nos_details[$i]['theory_marks'] : ''; ?>">
        										    <div class="invalid-feedback">
    													Theory Marks is required.
    												</div>
                                                </div>
                                                <div class="mb-3 col-md-2 div_practicalSkill_marks">
                                                    <label class="form-label">Practical Skills Marks</label>
                                                    <span class="text-danger spn_practicalSkill">*</span>
                                                     <input type="number" min="1" class="form-control practicalSkill" name="practical_skill_marks[<?php echo $i; ?>]" id="practical_skill_marks_<?php echo $i; ?>" onkeyup="calculateNosMarks();" value="<?php echo ($trade_id > 0 && array_key_exists($i,$arr_trade_nos_details)) ? $arr_trade_nos_details[$i]['practical_skill_marks'] : ''; ?>">
        										    <div class="invalid-feedback">
    													PracticalSkill Marks is required.
    												</div>
                                                </div>
                                                <div class="mb-3 col-md-2 div_practicalActivity_marks">
                                                    <label class="form-label">Practical Activity Marks</label>
                                                    <!--<span class="text-danger spn_practicalActivity">*</span>-->
                                                     <input type="number" min="0" class="form-control practicalActivity" name="practical_marks[<?php echo $i; ?>]" id="practical_marks_<?php echo $i; ?>" onkeyup="calculateNosMarks();" value="<?php echo ($trade_id > 0 && array_key_exists($i,$arr_trade_nos_details)) ? $arr_trade_nos_details[$i]['practical_marks'] : ''; ?>">
        										    <div class="invalid-feedback">
    													Practical Activity Marks is required.
    												</div>
                                                </div>
                                                <div class="mb-3 col-md-2 div_viva_marks">
                                                    <label class="form-label">Viva  Marks</label>
                                                    <!--<span class="text-danger spn_viva">*</span>-->
                                                     <input type="number" min="0" class="form-control viva" name="viva_marks[<?php echo $i; ?>]" id="viva_marks_<?php echo $i; ?>" onkeyup="calculateNosMarks();" value="<?php echo ($trade_id > 0 && array_key_exists($i,$arr_trade_nos_details)) ? $arr_trade_nos_details[$i]['viva_marks'] : ''; ?>">
        										    <div class="invalid-feedback">
    													Viva Marks is required.
    												</div>
                                                </div>
                                                <div class="mb-3 col-md-1">
                                                    <label class="form-label">Nos Total Marks</label>
                                                    <input type="hidden" name="total_nos_marks[<?php echo $i; ?>]" id="total_nos_marks_<?php echo $i; ?>" value="<?php echo ($trade_id > 0 && array_key_exists($i,$arr_trade_nos_details)) ? $arr_trade_nos_details[$i]['total_nos_marks'] : ''; ?>">
                                                    <code id="nos_wise_marks_<?php echo $i; ?>"></code>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="mb-3 col-md-12">
                                            <div class="justify-content-between align-items-center" style="text-align:right">
                                                <input type="hidden" name="total_marks" id="hdn_grand_total_marks" value="<?php echo ($trade_id > 0) ? $arr_trade_details[0]['total_marks'] : 0; ?>">
                                                <label class="text-end" style="text-align:right">Total Marks <code id="grand_total_marks"></code></label>
                                            </div>
                                            </div>
                                        </div>    
                                        <button type="submit" class="btn btn-primary" id="btn_save"><?php echo ($trade_id > 0) ? 'Edit' : 'Add'; ?> Record</button>
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
        <link rel="stylesheet" href="<?php echo base_url(); ?>vendor/toastr/css/toastr.min.css">
        <link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script>
        $(document).ready(function() {
            getNos();
            getOptionalExamType();
            
            $("#trade_code").on("blur", function() {
                var trade_id = $("#trade_id").val();
                var trade_code = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_trade_code").html("Please enter code.");
                $("#err_trade_code").hide();

                // Remove special characters using a regular expression
                var sanitizedValue = trade_code.replace(/[^a-zA-Z0-9.,\/ \-]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
                
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-trade-code'); ?>",
                    method: 'post',
                    data: { trade_id: trade_id,trade_code: trade_code,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_trade_code").html(trade_code+" this code already exists!");
                         $("#err_trade_code").show();
                         $("#trade_code").val('');
                      }
                    }
                 });
            });

            $("#nqr_code").on("blur", function() {
                var trade_id = $("#trade_id").val();
                var nqr_code = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_nqr_code").html("Please enter code.");
                $("#err_nqr_code").hide();

                // Remove special characters using a regular expression
                var sanitizedValue = nqr_code.replace(/[^a-zA-Z0-9.,\/ \-]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
                
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-nqr-code'); ?>",
                    method: 'post',
                    data: { trade_id: trade_id,nqr_code: nqr_code,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_nqr_code").html(nqr_code+" this code already exists!");
                         $("#err_nqr_code").show();
                         $("#nqr_code").val('');
                      }
                    }
                 });
            });

        });

        $("#no_of_nos").change(function() {
            getNos();
        });
        
        function getNos() {
          var no_of_nos = $("#no_of_nos option:selected").val();
          //console.log('no_of_nos '+no_of_nos);
          var i;
          if(no_of_nos > 0) {
              $(".nosBlock").show();
              for(i=0; i<no_of_nos; i++) { //Show only those Nos for the count selected in dropdown
                $(".nosBlock_"+i).show();
                $("#nos_id_"+i).attr('required',true);
                if(!$('#chk_theory').is(':checked')) {
                    $("#theory_marks_"+i).attr('required',true);
                } 
                if(!$('#chk_practicalSkill').is(':checked')) {   
                    $("#practical_skill_marks_"+i).attr('required',true);
                } 
                if(!$('#chk_practicalActivity').is(':checked')) {        
                    //$("#practical_marks_"+i).attr('required',true);
                }    
                if(!$('#chk_viva').is(':checked')) {      
                    //$("#viva_marks_"+i).attr('required',true);
                }    
              }
              for(j=no_of_nos; j < 10; j++) { //Hide rest of the Nos 
                $(".nosBlock_"+j).hide();
                $("#nos_id_"+j).attr('required',false);
                $("#theory_marks_"+j).attr('required',false);
                $("#practical_skill_marks_"+j).attr('required',false);
                //$("#practical_marks_"+j).attr('required',false);
                //$("#viva_marks_"+j).attr('required',false); 
              }
          }
          else {
              $(".nosBlock").hide();
          }

        }

        function validate_nos(nos_id){
            var sel_nos = $("#nos_id_"+nos_id).val();
            var error = 0;

            //console.log('sel_nos '+sel_nos);
            
            $(".selNos").each(function(){
                var nos = $(this).val();
                if(nos > 0) {
                    //console.log('nos '+nos+' id '+$(this).attr("id"));
                    if(("nos_id_"+nos_id != $(this).attr("id")) && (sel_nos == nos)) {
                        error++;
                        //toastr.error("This Nos is already selected");
                        //console.log('nos_id '+nos_id);
                        //$("#nos_id_"+nos_id).val("");
                        $('#nos_id_'+nos_id).val([]).trigger('change');
                    }
                }
            });

            //console.log('error '+error);
            
            if(error > 0) {
                // Use SweetAlert for an error alert with custom text
                sweetAlert("Oops...", "This NOS is already selected !!", "error")
            }
         }

         $('.form-check-input').change(function(){
            getOptionalExamType();
        });

        function getOptionalExamType() {
            var totalUnChecked = $('.form-check-input:not(:checked)').length;
            //console.log("totalUnChecked "+totalUnChecked);
            if(totalUnChecked == 4) {
                sweetAlert("Oops...", "You cannot de-select all as optional fields", "error");
                $("#chk_theory").prop('checked', true);
                $(".div_theory_marks").show();
                $(".theory").attr('required',true);
                $(".spn_theory").show();
            }
            $('.form-check-input').each(function(){
                var chkVal = $(this).val();
                if(!$(this).is(':checked')) {
                    //console.log("Checked "+chkVal);
                    
                    $(".div_"+chkVal+"_marks").hide();
                    $("."+chkVal).removeAttr('required');
                    $("."+chkVal).val(0);
                    $(".spn_"+chkVal).hide();
                }
                else {
                    //console.log("unChecked "+chkVal);
                    $(".div_"+chkVal+"_marks").show();
                    if(chkVal != 'practicalActivity' && chkVal != 'viva') {
                        $("."+chkVal).attr('required',true);
                    }
                    $(".spn_"+chkVal).show();
                }
            });

            calculateNosMarks();
        }

        function calculateNosMarks() {
            var no_of_nos = $("#no_of_nos option:selected").val();
            //console.log('no_of_nos '+no_of_nos);
            
            var grand_total_marks = 0;
            if(no_of_nos > 0) {
                $(".nosBlock").show();
                for(rowId=0; rowId<no_of_nos; rowId++) { //Show only those Nos for the count selected in dropdown

                    var total = 0;
                    var theory_marks = parseFloat($("#theory_marks_"+rowId).val());
                    var practical_skill_marks = parseFloat($("#practical_skill_marks_"+rowId).val());
                    var practical_marks = parseFloat($("#practical_marks_"+rowId).val());
                    var viva_marks = parseFloat($("#viva_marks_"+rowId).val());

                    if(theory_marks > 0) {
                        total += theory_marks;
                    }
                    if(practical_skill_marks > 0) {
                        total += practical_skill_marks;
                    }
                    if(practical_marks > 0) {
                        total += practical_marks;
                    }
                    if(viva_marks > 0) {
                        total += viva_marks;
                    }

                    grand_total_marks += parseFloat(total);

                    $("#nos_wise_marks_"+rowId).text(total); 
                    $("#total_nos_marks_"+rowId).val(total);
                    
                    //console.log("theory_marks "+theory_marks+"practical_skill_marks "+practical_skill_marks+"practical_marks "+practical_marks+"viva_marks "+viva_marks);
                    //console.log("total "+rowId+" "+total);
                    //console.log("grand_total_marks "+rowId+" "+grand_total_marks);
                } 
                //console.log("grand_total_marks "+grand_total_marks);
                $("#grand_total_marks").text(grand_total_marks); 
                $("#hdn_grand_total_marks").val(grand_total_marks); 
            }    
        }

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
                        //alert('Valid');
                        form.submit();
                    }
                    else {
                        //alert('in Valid');
                        const form = document.getElementById('frmTrade');
                        // Get all input elements within the form
                        const inputs = form.querySelectorAll('input');

                        // Filter out hidden inputs using their styles
                        const visibleInputs = Array.from(inputs).filter(input => input.offsetParent !== null);

                        // Check validity of visible inputs
                        const isValid = visibleInputs.every(input => input.checkValidity());

                        if (isValid) {
                            //console.log('Form is not valid!');
                            form.submit();
                        }
                        /*else {
                           console.log('Form is valid!');
                        }*/
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
      </script> 
