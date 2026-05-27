		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<!-- row -->
				<div class="row">
				    <?php
				    if($assessor_id > 0) {
				    ?>
					<div class="col-xl-3 col-lg-4">
						<div class="clearfix">
						    <div class="card card-bx profile-card author-profile m-b30">
								<div class="card-body">
									<div class="p-5">
										<div class="author-profile">
											<div class="author-media">
												<img src="<?php echo base_url().$this->config->item('assessors_images_path').$arr_assessor_details[0]['assessor_photo']; ?>" alt="">
											</div>
											<div class="author-info">
												<h6 class="title"><?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['assessor_name'] : ''; ?></h6>
												<span>Assessor</span>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
								<?php
                                if($arr_assessor_associated_agencies_data != false) {
                                ?>
                                    <div class="info-list">
                                        <h6 class="mb-3"><i class="fas fa-map-marked-alt"></i> Associated Agencies</h6> 
                                        <ul class="list-unstyled">
                                            <?php    
                                            foreach($arr_assessor_associated_agencies_data as $details) {
                                                echo '<li>'.$details['agency_name'].'</li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                <?php 
                                }
                                if($assessor_id > 0 && $arr_assessor_details[0]['assessor_resume'] != "") {
								?>
    								<div class="input-group mb-3">
    								    <a href="<?php echo base_url().$this->config->item('assessors_images_path').$arr_assessor_details[0]['assessor_resume'] ?>" class="form-control text-primary text-start bg-white" target="_blank">Resume</a>
    								</div>
								<?php
								}
								?>
                                </div>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="col-xl-9 col-lg-8">
						<div class="card profile-card card-bx m-b30">
							<div class="card-header">
								<h6 class="title"><?= $title ?></h6>
								
							</div>
							<form class="needs-custom-validation" novalidate id="myForm" method="post" action="<?= site_url('save-assessor') ?>" enctype="multipart/form-data" autocomplete="OFF">
							<input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<input name="assessor_id" id="assessor_id" type="hidden" value="<?php echo $assessor_id; ?>">
							<input name="dist_id" id="dist_id" type="hidden" value="<?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['district_id'] : ''; ?>">
							
								<div class="card-body">
									<div class="row">
									    <div class="col-sm-6 m-b30">
											<label class="form-label">Asessor Code<span class="text-danger">*</span></label>
											 <input type="text" class="form-control"  id="assessor_code" name="assessor_code" value="<?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['assessor_code'] : ''; ?>" required>
                                                <div class="invalid-feedback" id="err_assessor_code">
													Please enter Assessor Code.
												</div>
										</div>
										
										<div class="col-sm-6 m-b30">
											<label class="form-label">Assessor Name<span class="text-danger">*</span></label>
											<input type="text" class="form-control"  id="assessor_name" name="assessor_name" value="<?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['assessor_name'] : ''; ?>" required>
                                                <div class="invalid-feedback">
													Please enter Assessor Name.
												</div>
										</div>
										
										<div class="col-sm-6 m-b30">
											<label class="form-label">Gender</label>
											<select class="form-control" id="assessor_gender" name="assessor_gender">
												<option value="">Please select</option>
												<option value="Male" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['assessor_gender'] == "Male") ? 'selected' : ''; ?> >Male</option>
												<option value="Female" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['assessor_gender'] == "Female") ? 'selected' : ''; ?> >Female</option>
												<option value="Transgender" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['assessor_gender'] == "Transgender") ? 'selected' : ''; ?> >Transgender</option>
											</select>											
											<div class="invalid-feedback">
												Please select Gender.
											</div>
										</div>
										
										<div class="col-sm-6 m-b30">
											<label class="form-label">Photo</label>
											<input type="file" class="form-control" id="assessor_photo" name="assessor_photo">
										</div>	
										
										<div class="col-sm-6 m-b30">
											<label class="form-label">Mobile<span class="text-danger">*</span></label>
											<input type="text" class="form-control"  id="assessor_mobile" name="assessor_mobile" value="<?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['assessor_mobile'] : ''; ?>" required>
                                                <div class="invalid-feedback" id="err_assessor_mobile">
													Please enter Mobile.
												</div>
										</div>
										<div class="col-sm-6 m-b30">
											<label class="form-label">Email Address<span class="text-danger">*</span></label>
											<input type="text" class="form-control"  id="assessor_email" name="assessor_email" value="<?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['assessor_email'] : ''; ?>" required>
                                                <div class="invalid-feedback" id="err_assessor_email">
													Please enter Email Address.
												</div>
										</div>
										<div class="col-sm-6 m-b30">
											<label class="form-label">Address</label>
											<input type="text" class="form-control"  id="address" name="address" value="<?php echo ($assessor_id > 0) ? $arr_assessor_details[0]['address'] : ''; ?>">
                                                <div class="invalid-feedback">
													Please enter Address.
												</div>
										</div>
										<div class="col-sm-6 m-b30">
											<label class="form-label">State</label>
											<select class="form-control wide"  id="state_id" name="state_id">
												<option value="" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['state_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
												<?php
												foreach($arr_state as $state) {
												?>
													<option value="<?php echo $state['state_id']; ?>" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['state_id'] == $state['state_id']) ? 'selected' : ''; ?>><?php echo $state['state_name']; ?></option>
												<?php
												}
												?>
											</select>
											<div class="invalid-feedback">
												Please enter state.
											</div>
										</div>
										<div class="col-sm-6 m-b30">
											<label class="form-label">District</label>
											<select class="form-control" id="district_id" name="district_id">
												<option value="" data-state_id="" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['district_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
												<?php
												foreach($arr_district as $district) {
												?>
													<option data-state_id="<?php echo $district['state_id']; ?>" value="<?php echo $district['dist_id']; ?>" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['district_id'] == $district['dist_id']) ? 'selected' : ''; ?>><?php echo $district['dist_name']; ?></option>
												<?php
												}
												?>
											</select>
											<div class="invalid-feedback">
												Please enter district.
											</div>
										</div>
										<div class="col-sm-6 m-b30">
											<label class="form-label">Resume</label>
											<input type="file" class="form-control" id="assessor_resume" name="assessor_resume">
										</div>
										<div class="col-sm-6 m-b30">
											<label class="form-label">Status</label>
                                            <span class="text-danger">*</span>
											<select  class="form-control"  name="assessor_status" id="assessor_status" required>
                                                     <option value="" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['assessor_status'] == "") ? 'selected' : ''; ?>>-Select-</option>
        											<option value="Active" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['assessor_status'] == "Active") ? 'selected' : ''; ?>>Active</option>
        											<option value = "Inactive" <?php echo ($assessor_id > 0 && $arr_assessor_details[0]['assessor_status'] == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
    										    </select>
    										    <div class="invalid-feedback">
													Please select status.
												</div>
										</div>
										<h4>Map Sector Skill Councils<span class="text-danger">*</span></h4>
                                        <hr/>
                                        <div class="row">
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <?php
                                                    foreach($arr_ssc as $ssc) {
                                                    ?>
                                                        <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" value="<?php echo $ssc['ssc_id']; ?>" name="ssc_id[]" <?php echo (array_key_exists($ssc['ssc_id'],$arr_mapped_ssc)) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label"><?php echo $ssc['ssc_title'].' ('.$ssc['ssc_code'].')'; ?></label>
                                                    </div>
                                                    <?php
                                                    }
                                                    ?>   
                                                </div>
                                            </div>
                                        </div>
									</div>
								</div>
								<div class="card-footer">
									<button class="btn btn-primary" id="btn_submit"><?php echo ($assessor_id > 0) ? 'Update' : 'Add'; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
		<link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
   		<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script>
        $(document).ready(function() {
            getDistricts(); 

			$("#assessor_name").on("input", function() {
                // Get the input value
                var inputValue = $(this).val();

                // Remove special characters using a regular expression
                var sanitizedValue = inputValue.replace(/[^a-zA-Z0-9\s_-]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
            });
            
            $("#assessor_code").on("input", function() {
                var assessor_id = $("#assessor_id").val();
                var assessor_code = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_assessor_code").html("Please enter Assessor Code.");
                $("#err_assessor_code").hide();

				// Remove special characters using a regular expression
                var sanitizedValue = assessor_code.replace(/[^a-zA-Z0-9\s_-]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
                
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-assessor-code'); ?>",
                    method: 'post',
                    data: { assessor_id: assessor_id,assessor_code: assessor_code,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_assessor_code").html(assessor_code+" this Code already exists!");
                         $("#err_assessor_code").show();
                         $("#assessor_code").val('');
                      }
                    }
                 });
            });
            
            $("#assessor_mobile").on("input", function() {
                var assessor_id = $("#assessor_id").val();
                var assessor_mobile = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_assessor_mobile").html("Please enter Assessor Mobile.");
                $("#err_assessor_mobile").hide();
                
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-assessor-mobile'); ?>",
                    method: 'post',
                    data: { assessor_id: assessor_id,assessor_mobile: assessor_mobile,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_assessor_mobile").html(assessor_mobile+" this No. already exists!");
                         $("#err_assessor_mobile").show();
                         $("#assessor_mobile").val('');
                      }
                    }
                 });
            });
            
            $("#assessor_email").on("input", function() {
                var assessor_id = $("#assessor_id").val();
                var assessor_email = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_assessor_email").html("Please enter Assessor Email.");
                $("#err_assessor_email").hide();
                
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-assessor-email'); ?>",
                    method: 'post',
                    data: { assessor_id: assessor_id,assessor_email: assessor_email,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_assessor_email").html(assessor_email+" this Email already exists!");
                         $("#err_assessor_email").show();
                         $("#assessor_email").val('');
                      }
                    }
                 });
            });
           
        });
        
        $("#state_id").change(function() {
            getDistricts();
            $("#district_id").val('');
        });
        
        function getDistricts() {
          var state_id = $("#state_id option:selected").val();
          var selected_dist_id = $("#dist_id").val();
          //console.log('selected_dist_id '+selected_dist_id);
          
          $("#district_id option").hide();
          
          $("#district_id option[data-state_id='"+state_id+"']").show();
          if(selected_dist_id > 0) {
              $("#district_id").val(selected_dist_id);
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
                        //Check whether SSC checkbox is checked
                        var chk_ssc_count  = $(".form-check-input:checked").length; 

                        if(chk_ssc_count > 0) {
                            $('#btn_submit').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
                            $('#btn_submit').attr('disabled',true);
                            form.submit();
                        }	
                        else {
                            // Use SweetAlert for an error alert with custom text
                            sweetAlert("Oops...", "Please select the Sector Skill Councils !!", "error")

                            //toastr.error("Please select the Sector Skill Councils"); 
                        }
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
      </script> 
        