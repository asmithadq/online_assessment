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
							<a href="<?php echo base_url(); ?>list-training-partners" class="btn btn-primary btn-sm ms-2"><< Training Partners List</a>
						</div>
					</div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Training Partner</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-validation">
                                    <div class="basic-form">
                                    <form class="needs-custom-validation" novalidate id="myForm" method="post" action="<?= site_url('save-training-partner') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                        <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input name="tp_id" id="tp_id" type="hidden" value="<?php echo $tp_id; ?>">
                                        <input name="dist_id" id="dist_id" type="hidden" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['district'] : ''; ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Training Partner Name</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control"  id="name" name="name" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['name'] : ''; ?>" required>
                                                <div class="invalid-feedback">
													Please enter name.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Training Partner Code</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" id="tp_code" name="tp_code" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['tp_code'] : ''; ?>"> 
                                                <div class="invalid-feedback" id="err_tp_code">
													Please enter code.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Logo</label>
                                                <input type="file" class="form-control" id="logo" name="logo">
                                                <?php
                                                if($tp_id > 0 && $arr_tp_details[0]['logo'] != "") {
                                                ?>
                                                    <img class="rounded-circle" width="35" src="<?php echo base_url().$this->config->item('training_partner_images_path').$arr_tp_details[0]['logo'] ?>" alt="">
                                                <?php
                                                }
                                                ?>
                                                
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Address 1</label>
                                                <textarea class="form-txtarea form-control"  id="address_1" name="address_1" rows="2"><?php echo ($tp_id > 0) ? $arr_tp_details[0]['address_1'] : ''; ?></textarea>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Address 2</label>
                                                <input type="text" class="form-control"  id="address_2" name="address_2" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['address_2'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label>City</label>
                                                <input type="text" class="form-control"  id="city" name="city" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['city'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">State</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <select class="form-control"  id="state" name="state">
                                                    <option value="" <?php echo ($tp_id > 0 && $arr_tp_details[0]['state'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_state as $state) {
                                                    ?>
                                                        <option value="<?php echo $state['state_id']; ?>" <?php echo ($tp_id > 0 && $arr_tp_details[0]['state'] == $state['state_id']) ? 'selected' : ''; ?>><?php echo $state['state_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													Please enter state.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">District</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <select class="form-control" id="district" name="district">
                                                    <option value="" data-state_id="" <?php echo ($tp_id > 0 && $arr_tp_details[0]['district'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_district as $district) {
                                                    ?>
                                                        <option data-state_id="<?php echo $district['state_id']; ?>" value="<?php echo $district['dist_id']; ?>" <?php echo ($tp_id > 0 && $arr_tp_details[0]['district'] == $district['state_id']) ? 'selected' : ''; ?>><?php echo $district['dist_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">
													Please enter district.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Zip</label>
                                                <input type="text" class="form-control"  id="pincode" name="pincode" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['pincode'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Email</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="email" class="form-control"  id="email" name="email" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['email'] : ''; ?>">
                                                <div class="invalid-feedback">
													Please enter email.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control"  id="phone" name="phone" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['phone'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Mobile</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="text" class="form-control"  id="mobile" name="mobile" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['mobile'] : ''; ?>">
                                                <div class="invalid-feedback">
													Please enter mobile.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Website</label>
                                                <input type="text" class="form-control"  id="website" name="website" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['website'] : ''; ?>">
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
                                                        <input type="checkbox" class="form-check-input" value="<?php echo $ssc['ssc_id']; ?>" name="ssc_id[]" <?php echo ($tp_id > 0 && array_key_exists($ssc['ssc_id'],$arrSscMappedDetails)) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label"><?php echo $ssc['ssc_title'].' ('.$ssc['ssc_code'].')'; ?></label>
                                                    </div>
                                                    <?php
                                                    }
                                                    ?>   
                                                </div>
                                            </div>
                                        </div>
                                        <h4>Bank Information</h4>
                                        <hr/>
                                        <div class="row">
                                          <div class="mb-3 col-md-4">
                                                <label class="form-label">Bank Name</label>
                                                <select id="bank_name" name="bank_name" class="default-select form-control wide">
                                                    <option value="" <?php echo ($tp_id > 0 && $arr_tp_details[0]['bank_name'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <?php
                                                    foreach($arr_banks as $banks) {
                                                    ?>
                                                        <option value="<?php echo $banks['bank_id']; ?>" <?php echo ($tp_id > 0 && $arr_tp_details[0]['bank_name'] == $banks['bank_id']) ? 'selected' : ''; ?>><?php echo $banks['bank_name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Branch</label>
                                                <input type="text" class="form-control"  id="bank_branch" name="bank_branch" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['bank_branch'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label>Bank Account Number</label>
                                                <input type="text" class="form-control"  id="bank_account_no" name="bank_account_no" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['bank_account_no'] : ''; ?>">
                                            </div>
                                        </div>
                                        <h4>SPOC Information</h4>
                                        <hr/>
                                        <div class="row">
                                          <div class="mb-3 col-md-3">
                                                <label>First Name</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="text" class="form-control"  id="contact_first_name" name="contact_first_name" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['contact_first_name'] : ''; ?>">
                                                <div class="invalid-feedback">
													Please enter first name.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label>Middle Name</label>
                                                <input type="text" class="form-control"  id="contact_middle_name" name="contact_middle_name" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['contact_middle_name'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label>Last Name</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="text" class="form-control"  id="contact_last_name" name="contact_last_name" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['contact_last_name'] : ''; ?>">
                                                <div class="invalid-feedback">
													Please enter last name.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label>Gender</label>
                                                <select id="contact_gender" name="contact_gender" class="default-select form-control wide">
                                                    <option value="" <?php echo ($tp_id > 0 && $arr_tp_details[0]['contact_gender'] == "") ? 'selected' : ''; ?>>Choose...</option>
                                                    <option value="Male" <?php echo ($tp_id > 0 && $arr_tp_details[0]['contact_gender'] == "Male") ? 'selected' : ''; ?>>Male</option>
                                                    <option value="Female" <?php echo ($tp_id > 0 && $arr_tp_details[0]['contact_gender'] == "Female") ? 'selected' : ''; ?>>Female</option>
                                                    <option value="Transgender" <?php echo ($tp_id > 0 && $arr_tp_details[0]['contact_gender'] == "Transgender") ? 'selected' : ''; ?>>Transgender</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label>Phone</label>
                                                <input type="text" class="form-control"  id="contact_phone" name="contact_phone" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['contact_phone'] : ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label>Mobile</label>
                                                <!--<span class="text-danger">*</span>-->
                                                <input type="text" class="form-control"  id="contact_mobile" name="contact_mobile" value="<?php echo ($tp_id > 0) ? $arr_tp_details[0]['contact_mobile'] : ''; ?>" >
                                                <div class="invalid-feedback">
													Please enter mobile.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label>Photo</label>
                                                <input type="file" class="form-control" id="contact_photo" name="contact_photo">
                                                <?php
                                                if($tp_id > 0 && $arr_tp_details[0]['contact_photo'] != "") {
                                                ?>
                                                    <img class="rounded-circle" width="35" src="<?php echo base_url().$this->config->item('training_partner_images_path').$arr_tp_details[0]['contact_photo'] ?>" alt="">
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Status</label>
                                                <span class="text-danger">*</span>
                                                 <select  class="form-control"  name="status" id="status" required>
                                                     <option value="" <?php echo ($tp_id > 0 && $arr_tp_details[0]['status'] == "") ? 'selected' : ''; ?>>-Select-</option>
        											<option value="1" <?php echo ($tp_id > 0 && $arr_tp_details[0]['status'] == 1) ? 'selected' : ''; ?>>Active</option>
        											<option value = "0" <?php echo ($tp_id > 0 && $arr_tp_details[0]['status'] == 0) ? 'selected' : ''; ?>>In-active</option>
    										    </select>
    										    <div class="invalid-feedback">
													Please select status.
												</div>
                                            </div>
                                        </div>
                                       <button type="submit" class="btn me-2 btn-primary" id="btn_submit">Submit</button>
                                       <button type="button" class="btn btn-danger light" id="cancel">Cancel</button> 
                                    </form>
                                </div>
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

        <link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script>
        $(document).ready(function() {
            getDistricts();

            $("#name").on("input", function() {
                // Get the input value
                var inputValue = $(this).val();

                // Remove special characters using a regular expression
                var sanitizedValue = inputValue.replace(/[^a-zA-Z0-9\s_-]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
            });
            
            $("#tp_code").on("blur", function() {
                var tp_id = $("#tp_id").val();
                var tp_code = $(this).val();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                $("#err_tp_code").html("Please enter code.");
                $("#err_tp_code").hide();

                // Remove special characters using a regular expression
                var sanitizedValue = tp_code.replace(/[^a-zA-Z0-9\s_-]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
                
                //console.log('tp_code '+tp_code);
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                  $.ajax({
                    url: "<?php echo base_url('check-duplicate-tp-code'); ?>",
                    method: 'post',
                    data: { tp_id: tp_id,tp_code: tp_code,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                       //console.log('validate '+response.validate); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      if(response.validate == true) {
                         $("#err_tp_code").html(tp_code+" this code already exists!");
                         $("#err_tp_code").show();
                         $("#tp_code").val('');
                      }
                    }
                 });
            });
        });
        
        $("#state").change(function() {
            getDistricts();
            $("#district").val('');
        });
        
        function getDistricts() {
          var state_id = $("#state option:selected").val();
          var selected_dist_id = $("#dist_id").val();
          //console.log('selected_dist_id '+selected_dist_id);
          
          $("#district option").hide();
          
          $("#district option[data-state_id='"+state_id+"']").show();
          if(selected_dist_id > 0) {
              $("#district").val(selected_dist_id);
          }
          
        }

        $("#cancel").click(function() {
            window.location.href = '<?php echo base_url(); ?>list-training-partners';
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
        