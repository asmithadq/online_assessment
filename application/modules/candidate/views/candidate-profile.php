<style>
    .upload-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    #preview {
        width: 100px;
        height: 100px;
        border: 2px dashed #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }
    #preview img {
        max-width: 100%;
        max-height: 100%;
    }
    #upload-btn {
        padding: 10px 20px;
        border: none;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
    }
    #upload-file {
        display: none;
    }

	.error {
		border: 1px solid #FF5E5E;
	}
</style>
<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<!-- row -->
				<div class="row">
					<?php 
					if ($this->session->flashdata('msg') != "") { ?>
						<div class="alert alert-success alert-dismissible fade show">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
							<strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
							
							</button>
						</div>
					<?php }  
					else if ($this->session->flashdata('error') != "") { ?>
						<div class="alert alert-danger alert-dismissible fade show">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
							<strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
							</button>
						</div>
					<?php } ?>
					<div class="col-xl-12 col-lg-8">
						<div class="card profile-card card-bx m-b30">
							<div class="card-header">
								<h6 class="title">Update Profile</h6>
							</div>
							<form class="needs-validation" novalidate id="frmProfile" method="post" action="<?= site_url('save-candidate-profile') ?>" enctype="multipart/form-data" autocomplete="OFF">
								<input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
								<?php
									$student_photo_path = base_url().$this->config->item('student_photo_path');
									$aadhaar_filename_path = base_url().$this->config->item('aadhaar_filename_path');
								?>
								<input type="hidden" name="hdn_student_photo" value="<?php echo (array_key_exists('student_photo',$arrCandidateDetails)) ? str_replace($student_photo_path,"",$arrCandidateDetails['student_photo']) : ""; ?>">
								<input type="hidden" name="hdn_aadhar_front_filename" value="<?php echo (array_key_exists('aadhar_front_filename',$arrCandidateDetails)) ? str_replace($aadhaar_filename_path,"",$arrCandidateDetails['aadhar_front_filename']) : ""; ?>">
								<input type="hidden" name="hdn_aadhar_back_filename" value="<?php echo (array_key_exists('aadhar_back_filename',$arrCandidateDetails)) ? str_replace($aadhaar_filename_path,"",$arrCandidateDetails['aadhar_back_filename']) : ""; ?>">
								<input type="hidden" name="long" id="long" />
            					<input type="hidden" name="lat" id="lat" />
								<div class="card-body">
									<div class="row">
										<div class="col-sm-3 m-b30">
											<label class="form-label">Name (as in Aadhar)<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo (array_key_exists('student_name',$arrCandidateDetails)) ? $arrCandidateDetails['student_name'] : ""; ?>" required>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Father Name<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo (array_key_exists('father_name',$arrCandidateDetails)) ? $arrCandidateDetails['father_name'] : ""; ?>" required>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Gender<span class="text-danger">*</span></label>
											<select class="form-control" id="gender" name="gender" required>
												<option value="">Please select</option>
												<option value="Male" <?php echo (array_key_exists('gender',$arrCandidateDetails) && $arrCandidateDetails['gender'] == 'Male') ? "selected" : ""; ?>>Male</option>
												<option value="Female" <?php echo (array_key_exists('gender',$arrCandidateDetails) && $arrCandidateDetails['gender'] == 'Female') ? "selected" : ""; ?>>Female</option>
												<option value="Other" <?php echo (array_key_exists('gender',$arrCandidateDetails) && $arrCandidateDetails['gender'] == 'Other') ? "selected" : ""; ?>>Other</option>
											</select>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Date of Birth<span class="text-danger">*</span></label>
											<input type="date" class="form-control" id="dob" name="dob" value="<?php echo (array_key_exists('dob',$arrCandidateDetails) && ($arrCandidateDetails['dob'] != "" && $arrCandidateDetails['dob'] != '0000-00-00')) ? date('Y-m-d',strtotime($arrCandidateDetails['dob'])) : ""; ?>" required>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Aadhar Number<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="aadhar_number" name="aadhar_number" maxlength="12" value="<?php echo (array_key_exists('aadhar_number',$arrCandidateDetails)) ? $arrCandidateDetails['aadhar_number'] : ""; ?>" required>
											<span id="aadhaarError" style="color: #FF5E5E;"></span>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Email<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="student_email" name="student_email" value="<?php echo (array_key_exists('student_email',$arrCandidateDetails)) ? $arrCandidateDetails['student_email'] : ""; ?>">
											<span id="emailError" style="color: #FF5E5E;"></span>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Mobile<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="student_mobile" name="student_mobile" maxlength="10" value="<?php echo (array_key_exists('student_mobile',$arrCandidateDetails)) ? $arrCandidateDetails['student_mobile'] : ""; ?>" required>
											<span id="mobileError" style="color: #FF5E5E;"></span>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Photo<span class="text-danger">*</span></label>
											<div class="upload-container">
												<?php
												$photoRequired = "required";
                                                if((array_key_exists('student_photo',$arrCandidateDetails)) && $arrCandidateDetails['student_photo'] != "") {
													$actual_photo = str_replace('thumbs/','',$arrCandidateDetails['student_photo']);
                                                ?>
                                                    <div id="preview">
														<img src="<?php echo $arrCandidateDetails['student_photo']; ?>" style="cursor: pointer;" onclick="showImage('Photo','<?php echo $actual_photo; ?>');">
													</div>
                                                <?php
													$photoRequired = "";
                                                }
                                                ?>
												
												<input type="file" class="form-control" id="student_photo" name="student_photo" accept="image/*" <?php echo $photoRequired; ?>>
											</div>
										</div>
                                        <!--<div class="col-sm-4 m-b30">
											<label class="form-label">Aadhar(Front)<span class="text-danger">*</span></label>
											<div class="upload-container">
												<?php
												/*$aadharFrontRequired = "required";
                                                if((array_key_exists('aadhar_front_filename',$arrCandidateDetails)) && $arrCandidateDetails['aadhar_front_filename'] != "") {
                                                ?>
                                                    <div id="preview">
														<img src="<?php echo $arrCandidateDetails['aadhar_front_filename']; ?>" style="cursor: pointer;" onclick="showImage('Aadhar(Front)','<?php echo $arrCandidateDetails['aadhar_front_filename']; ?>');">
													</div>
                                                <?php
													$aadharFrontRequired = "";
                                                }*/
                                                ?>
												<input type="file" class="form-control" id="aadhar_front_filename" name="aadhar_front_filename" accept="image/*" <?php //echo $aadharFrontRequired; ?>>
											</div>
										</div>
                                         <div class="col-sm-4 m-b30">
											<label class="form-label">Aadhar(Back)<span class="text-danger">*</span></label>
											<div class="upload-container">
												<?php
												/*$aadharBackRequired = "required";
                                                if((array_key_exists('aadhar_back_filename',$arrCandidateDetails)) && $arrCandidateDetails['aadhar_back_filename'] != "") {
                                                ?>
                                                    <div id="preview">
														<img src="<?php echo $arrCandidateDetails['aadhar_back_filename']; ?>" alt="" style="cursor: pointer;" onclick="showImage('Aadhar(Back)','<?php echo $arrCandidateDetails['aadhar_back_filename']; ?>');">
													</div>
                                                <?php
													$aadharBackRequired = "";
                                                }*/
                                                ?>
												<input type="file" class="form-control" id="aadhar_back_filename" name="aadhar_back_filename" accept="image/*" <?php //echo $aadharBackRequired; ?>>
											</div> 
										</div>-->
                                        <div class="col-sm-12 m-b30">
											<label class="form-label">Address<span class="text-danger">*</span></label>
											<textarea class="form-control" rows="5" id="address" name="address"><?php echo (array_key_exists('address',$arrCandidateDetails)) ? $arrCandidateDetails['address'] : ""; ?></textarea>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">City<span class="text-danger">*</span></label>
											<input type="text" class="form-control" value="<?php echo (array_key_exists('city',$arrCandidateDetails)) ? $arrCandidateDetails['city'] : ""; ?>" id="city" name="city" required>
										</div>
                                        <div class="col-sm-3 m-b30">
											<label class="form-label">State<span class="text-danger">*</span></label>
											<select class="form-control"  id="state_id" name="state_id" required>
												<option value="" <?php echo (array_key_exists('state_id',$arrCandidateDetails) > 0 && $arrCandidateDetails['state_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
												<?php
												foreach($arr_state as $state) {
												?>
													<option value="<?php echo $state['state_id']; ?>" <?php echo (array_key_exists('state_id',$arrCandidateDetails) > 0 && $arrCandidateDetails['state_id'] == $state['state_id']) ? 'selected' : ''; ?>><?php echo $state['state_name']; ?></option>
												<?php
												}
												?>
											</select>
										</div>
                                        
										<div class="col-sm-3 m-b30">
											<label class="form-label">District<span class="text-danger">*</span></label>
											<select class="form-control" id="district_id" name="district_id" required>
												<option value="" data-state_id="" <?php echo (array_key_exists('district_id',$arrCandidateDetails) > 0 && $arrCandidateDetails['district_id'] == "") ? 'selected' : ''; ?>>Choose...</option>
												<?php
												foreach($arr_district as $district) {
												?>
													<option data-state_id="<?php echo $district['state_id']; ?>" value="<?php echo $district['dist_id']; ?>" <?php echo (array_key_exists('district_id',$arrCandidateDetails) > 0 && $arrCandidateDetails['district_id'] == $district['dist_id']) ? 'selected' : ''; ?>><?php echo $district['dist_name']; ?></option>
												<?php
												}
												?>
											</select>
										</div>
										<div class="col-sm-3 m-b30">
											<label class="form-label">Pin<span class="text-danger">*</span></label>
											<input type="text" class="form-control" value="<?php echo (array_key_exists('pincode',$arrCandidateDetails)) ? $arrCandidateDetails['pincode'] : ""; ?>" id="pincode" name="pincode" required>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<button class="btn btn-primary">UPDATE</button>
								</div>
							</form>
						</div>
					</div>
				</div>
            </div>
        </div>
		<div class="modal fade bd-example-modal-lg" id="imageView" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title"><span id="title"></span></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal">
						</button>
					</div>
					<div class="modal-body">
						<img src="" id="imgData" width="500" height="500">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
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
			getDistricts();

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

			function showImage(title,imgPath) {
				$("#imageView").modal('show');
				$("#title").text(title);
				$("#imgData").attr('src',imgPath);
			}

			$(document).ready(function(){
				if(navigator.geolocation){
					navigator.geolocation.getCurrentPosition(showLocation);
				}else{ 
					$('#location').html('Geolocation is not supported by this browser.');
				}
			});

			function showLocation(position){
				var latitude = position.coords.latitude;
				var longitude = position.coords.longitude;

				$("#lat").val(latitude);
				$("#long").val(longitude);
			}

			$(document).ready(function() {
				$('#student_mobile').on('input', function() {
					var mobile = $(this).val();
					if (!/^[0-9]{10}$/.test(mobile)) {
						$(this).addClass('error');
						$('#mobileError').text('Invalid mobile number');
					} else {
						$(this).removeClass('error');
						$('#mobileError').text('');
					}
				});

				$('#aadhar_number').on('input', function() {
					var aadhaar = $(this).val();
					if (!/^[0-9]{12}$/.test(aadhaar)) {
						$(this).addClass('error');
						$('#aadhaarError').text('Invalid Aadhaar number');
					} else {
						$(this).removeClass('error');
						$('#aadhaarError').text('');
					}
				});

				$('#student_email').on('input', function() {
					var email = $(this).val();
					if (!validateEmail(email)) {
						$(this).addClass('error');
						$('#emailError').text('Invalid email address');
					} else {
						$(this).removeClass('error');
						$('#emailError').text('');
					}
				});

				// Function to validate email address using regular expression
				function validateEmail(email) {
					var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
					return re.test(email);
				}
			});
		</script>