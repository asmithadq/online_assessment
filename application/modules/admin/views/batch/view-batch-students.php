<link href="<?php echo base_url(); ?>assets/admin/vendor/lightgallery/css/lightgallery.min.css" rel="stylesheet">
<style>
 .assessment-info {
      font-style: italic;
    }
    .img-thumbnail {
	padding: 0.25rem;
	border: 1px solid #c0c0c0;
	/* Thin border */
	max-width: 100%;
}
</style>
<!--**********************************
Content body start
***********************************-->
<div class="content-body">
	<div class="container-fluid">
		<!-- row -->
		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<form class="needs-custom-mass-validation" novalidate id="frmMassUpdate" method="post" action="<?= site_url('update-mass-candidate-details') ?>" autocomplete="OFF">
					<input type="hidden" name="tb_id" value="<?php echo $tb_id; ?>">
						<div class="card dz-card">
							<div id="div_spin" style="display:none;">
								<span  class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
							</div>
							<div class="card-body p-0">
								<div class="table-responsive active-projects">
									<div class="tbl-caption">
										<h4 class="card-title"><?= $title ?></h4>
									</div>
									<table id="serverSideDataTable" class="display table" style="min-width: 845px">
										<thead>
											<tr>
												<th>
													<div class="custom-control d-inline custom-checkbox">
														<input type="checkbox" class="form-check-input" id="checkAll" required="">
														<label class="form-check-label" for="checkAll"></label>
													</div>
												</th>
												<th></th>
												<th>Candidate ID</th>
												<th>Candidate Name</th>
												<th>Password</th>
												<th>Aadhar Verification</th>
												<th>Attendance</th>
												<th>Assessment</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
										<th colspan="8">
											<div class="btn-group" role="group">
												<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" id="attendance_status">Attendance</button>
												<div class="dropdown-menu" id="attendance_status_sub_menu">
													<a class="dropdown-item" href="javascript:void(0);" onclick="updateAttendanceStatus('Absent');">Absent</a>
													<a class="dropdown-item" href="javascript:void(0);" onclick="updateAttendanceStatus('Present');">Present</a>
												</div>
											</div>
											<div class="btn-group" role="group">
												<button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" id="profile_verification">Profile Verification</button>
												<div class="dropdown-menu" id="profile_verification_sub_menu">
													<a class="dropdown-item" href="javascript:void(0);" onclick="updateProfileVerificationStatus('Pending');">Pending</a>
													<a class="dropdown-item" href="javascript:void(0);" onclick="updateProfileVerificationStatus('Verified');">Verified</a>
													<a class="dropdown-item" href="javascript:void(0);" onclick="updateProfileVerificationStatus('Rejected');">Rejected</a>
												</div>
											</div>
											<button type="button" class="btn btn-danger" id="btn_delete">Delete</button>
											<div class="btn-group" role="group">
												<button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" id="reset_exam">Reset Exam</button>
												<div class="dropdown-menu" id="reset_exam_sub_menu">
													<a class="dropdown-item" href="javascript:void(0);" onclick="resetExam('all');">Reset Exam</a>
													<a class="dropdown-item" href="javascript:void(0);" onclick="resetExam('theory');">Reset Theory</a>
													<a class="dropdown-item" href="javascript:void(0);" onclick="resetExam('practical_activity');">Reset Practical Activity</a>
													<a class="dropdown-item" href="javascript:void(0);" onclick="resetExam('viva');">Reset Viva</a>
												</div>
											</div>
											<button type="button" class="btn btn-danger" id="btn_generate_qp">Generate QP</button>
											<button type="button" class="btn btn-light" id="btn_reset_device_login">Reset Device Login</button>
											<div id="toastrContainer">
											<!-- Toastr notifications will appear here -->
											</div>
										</th>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
				</form>			
			</div>
			
		</div>
	</div>
</div>
<!-- Large modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalH5">
					<div>Candidate Details for Enrollment No: <span class="enrollment_number"></span></div>
				</h5>
				<button type="button" class="btn-close custom-close">
				</button>
			</div>
			<div class="modal-body">
				<form class="needs-custom-validation" novalidate id="frmProfile" method="post" action="<?= site_url('update-candidate-profile-verification-status') ?>" autocomplete="OFF">
					<div class="card-body">
						<div class="row">
							<div class="col-sm-12 m-b30">
								<input type="hidden" name="student_id" id="student_id">
								<label class="form-label"><i class="fas fa-id-badge"></i> Candidate ID : <span class="enrollment_number"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-user"></i> Candidate Name : <span id="student_name"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-venus-mars"></i> Gender : <span id="gender"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-male"></i> Father Name : <span id="father_name"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-id-card"></i> Aadhar Number : <span id="aadhar_number"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-calendar-alt"></i> Date of Birth : <span id="dob"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-map-marker-alt"></i> Address : <span id="address"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-city"></i> City : <span id="city"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-flag"></i> State : <span id="state_name"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-map-pin"></i> District : <span id="dist_name"></span></label>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-thumbtack"></i> Pincode : <span id="pincode"></span></label>
							</div>
							<div class="col-sm-6 m-b30">&nbsp;</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-image"></i><u>Supporting Candidate Photo's</u></label> 
							</div>
							<div id="lightgallery" class="row">
								<a id="ahref_student_photo" href="" data-exthumbimage="" data-src="" class=" lg-item col-lg-3 col-md-6 mb-4">
									<img id="student_photo" src="" alt="" class="w-100 rounded">
								</a>
								<a id="ahref_student_photo_with_aadhar" href="" data-exthumbimage="" data-src="" class=" lg-item col-lg-3 col-md-6 mb-4">
									<img id="student_photo_with_aadhar" src="" alt="" class="w-100 rounded">
								</a>
								<a id="ahref_img_front" href="" data-exthumbimage="" data-src="" class=" lg-item col-lg-3 col-md-6 mb-4">
									<img id="img_front" src="" alt="" class="w-100 rounded">
								</a>
								<a id="ahref_img_back" href="" data-exthumbimage="" data-src="" class=" lg-item col-lg-3 col-md-6 mb-4">
									<img id="img_back" src="" alt="" class="w-100 rounded">
								</a> 
							</div>
							<!--
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="fas fa-image"></i> Profile Photo </label> 
								<div class="author-media">
									<img id="student_photo" src="" alt="" height="240" width="278" class="img-thumbnail">
								</div>    
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="far fa-image"></i> Student with Aadhaar</label>
								<div class="author-media">
									<img id="student_photo_with_aadhar" src="" alt="" height="240" width="278" class="img-thumbnail">
								</div>
							</div>
							
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="far fa-address-card"></i> Aadhaar Front Photo</label>
								<div class="author-media">
									<img id="img_front" src="" alt="" height="240" width="278" class="img-thumbnail">
								</div>
							</div>
							<div class="col-sm-6 m-b30">
								<label class="form-label"><i class="far fa-address-card"></i> Aadhaar Back Photo</label>
								<div class="author-media">
									<img id="img_back" src="" alt="" height="240" width="278"  class="img-thumbnail">
								</div>
							</div>-->
							
						</div>
						<div class="row">
							<div class="col-sm-6 m-b30">
								<label class="form-label">Profile Verified Status<span class="text-danger">*</span></label>
									<select  class="form-control"  name="profile_verification_status" id="profile_verification_status" required>
										<option value="">-Select-</option>
										<option value="Pending">Pending</option>
										<option value="Verified">Verified</option>
										<option value="Rejected">Rejected</option>
									</select>
									<div class="invalid-feedback">
										Please select status.
									</div>
							</div>
							<div class="col-sm-6 m-b30" id="comments" style="display:none;">
								<label class="form-label">Reason For Rejection<span class="text-danger">*</span></label>
								<textarea class="form-control" id="profile_rejection_comment" name="profile_rejection_comment" required></textarea>
								<div class="invalid-feedback">
									Please enter reason.
								</div>
							</div>
						</div>
						<div class="row" id="div_row_message" style="display:none;">	
							<div class="col-sm-12 m-b30">
								<div class="alert alert-dismissible fade show" id="div_message" style="display:none;">
									<svg id="svg_success" style="display:none;" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
										<polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
										
									<svg id="svg_error" style="display:none;" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
										<polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>  
										
									<span id="spn_message"></span>
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
								</div>
							</div>	
						</div>	
					</div>	
					<div class="card-footer">
						<input type="hidden" id="btn_update_clicked" value="0">
						<button class="btn btn-primary" id="btn_update" type="submit">Update</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger light custom-close">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End Large modal -->
<!-- Confirmation Modal -->
<div class="modal fade modal-delete-confirm" id="exampleModalCenter">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Confirmation</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal">
				</button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete the selected candidates?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger light" data-bs-dismiss="modal">No</button>
				<button type="button" class="btn btn-primary" id="btn_delete_candidates">Yes</button>
			</div>
		</div>
	</div>
</div>
<!-- End Confirmation modal -->
<!--**********************************
Content body end
***********************************-->

<link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>

<script>
	// Variable to store DataTable instance
	var dataTable;

	// Function to initialize or reload DataTable
    function initializeOrReloadDataTable() {
      // Check if DataTable is initialized
      if (typeof dataTable === 'undefined') {
			//console.log('undefined');
			// DataTable is not initialized, so initialize it
			// get the hash 
			var csrf_hash_name = $("input[name=csrf_hash_name]").val();
			var tb_id = '<?php echo $tb_id; ?>';
			
			dataTable = $('#serverSideDataTable').DataTable({
				// Processing indicator
				"processing": true,
				// DataTables server-side processing mode
				"serverSide": true,
				// Initial no order.
				"order": [],
				// Load data from an Ajax source
				"ajax": {
					"url": "<?php echo base_url('list-batches-students-ajax'); ?>",
					"type": "POST",
					"data": { 'tb_id' : tb_id,'csrf_hash_name' : csrf_hash_name },
				},
				pageLength: 50,
				responsive: true,
				'dom': 'ZBfrltip',
				buttons: [
                    {
                        extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                        className: 'btn btn-sm border-0',
                        title: '<?php echo $batch_id; ?>-Candidates-List', // Specify your custom file name here
                        filename: function() {
                            // Custom filename function can be used for dynamic file names
                            return '<?php echo $batch_id; ?>-Candidates-List-' + '<?php echo date('d-m-Y H:i:s') ?>';
                        },
                        exportOptions: {
                            columns: [2,3,4,5,6] // Include all columns except 0,1,7
                        }
                    }
                ],
				//Set column definition initialisation properties
				"columnDefs": [{ 
					"targets": [0,1],
					"orderable": false
				}],
				language: {
					paginate: {
						next: '<i class="fa-solid fa-angle-right"></i>',
						previous: '<i class="fa-solid fa-angle-left"></i>' 
					}
				}
				
			});
		} else {
			dataTable.ajax.reload(null, false);
        }
    }

	$(document).ready(function(){
		initializeOrReloadDataTable();

		$(".custom-close").click(function() {
			$('#frmProfile')[0].reset();
			$("#student_photo").attr("src", "");
			$("#ahref_student_photo").attr("data-exthumbimage", "");
			$("#ahref_student_photo").attr("data-src", "");
			$("#ahref_student_photo").attr("href", "");

			$("#student_photo_with_aadhar").attr("src", "");
			$("#ahref_student_photo_with_aadhar").attr("data-exthumbimage", "");
			$("#ahref_student_photo_with_aadhar").attr("data-src", "");
			$("#ahref_student_photo_with_aadhar").attr("href", "");

			$("#img_front").attr("src", "");
			$("#ahref_img_front").attr("data-exthumbimage", "");
			$("#ahref_img_front").attr("data-src", "");
			$("#ahref_img_front").attr("href", "");

			$("#img_back").attr("src", "");
			$("#ahref_img_back").attr("data-exthumbimage", "");
			$("#ahref_img_back").attr("data-src", "");
			$("#ahref_img_back").attr("href", "");
			$(".bd-example-modal-lg").modal('hide');
			var btn_update_clicked = $("#btn_update_clicked").val();
			//console.log('btn_update_clicked '+btn_update_clicked);
			if(btn_update_clicked == 1) { // Reload only if data is updated
				// Reload the DataTable
				initializeOrReloadDataTable();
			}
		});

	});

	function getAadharDetails(student_id) {
		$("#spin_"+student_id).show();
		$("#comments").hide();
		$("#div_row_message").hide();
		$("#btn_update_clicked").val(0);
		$('#frmProfile')[0].reset();
		$("#student_photo").attr("src", "");
		$("#ahref_student_photo").attr("data-exthumbimage", "");
		$("#ahref_student_photo").attr("data-src", "");
		$("#ahref_student_photo").attr("href", "");

		$("#student_photo_with_aadhar").attr("src", "");
		$("#ahref_student_photo_with_aadhar").attr("data-exthumbimage", "");
		$("#ahref_student_photo_with_aadhar").attr("data-src", "");
		$("#ahref_student_photo_with_aadhar").attr("href", "");

		$("#img_front").attr("src", "");
		$("#ahref_img_front").attr("data-exthumbimage", "");
		$("#ahref_img_front").attr("data-src", "");
		$("#ahref_img_front").attr("href", "");

		$("#img_back").attr("src", "");
		$("#ahref_img_back").attr("data-exthumbimage", "");
		$("#ahref_img_back").attr("data-src", "");
		$("#ahref_img_back").attr("href", "");

		var enrollment_number = $("#btn-"+student_id).attr('data-enrollment_number');
		var student_name = $("#btn-"+student_id).attr('data-student_name');
		var dob = $("#btn-"+student_id).attr('data-dob');
		var gender = $("#btn-"+student_id).attr('data-gender');
		var father_name = $("#btn-"+student_id).attr('data-father_name');
		var address = $("#btn-"+student_id).attr('data-father_name');
		var city = $("#btn-"+student_id).attr('data-city');
		var pincode = $("#btn-"+student_id).attr('data-pincode');
		var dist_name = $("#btn-"+student_id).attr('data-dist_name');
		var state_name = $("#btn-"+student_id).attr('data-state_name');
		var aadhar_number = $("#btn-"+student_id).attr('data-aadhar_number');
		var profile_verification_status = $("#btn-"+student_id).attr('data-profile_verification_status');
		var profile_rejection_comment = $("#btn-"+student_id).attr('data-profile_rejection_comment');
		var student_photo = $("#btn-"+student_id).attr('data-student_photo');
		var aadhar_front_filename = '<?php echo base_url().$this->config->item('aadhaar_filename_path'); ?>'+$("#btn-"+student_id).attr('data-aadhar_front_filename');
		var aadhar_back_filename = '<?php echo base_url().$this->config->item('aadhaar_filename_path'); ?>'+$("#btn-"+student_id).attr('data-aadhar_back_filename');
		var student_photo_with_aadhar = '<?php echo base_url().$this->config->item('aadhaar_filename_path'); ?>'+$("#btn-"+student_id).attr('data-student_photo_with_aadhar');
		//console.log('aadhar_front_filename '+aadhar_front_filename+' aadhar_back_filename '+aadhar_back_filename);
		//console.log('enrollment_number '+enrollment_number);
		//console.log('profile_verification_status '+profile_verification_status);

		$(".enrollment_number").text(enrollment_number);
		$("#student_id").val(student_id);
		$("#student_name").text(student_name);
		$("#gender").text(gender);
		$("#father_name").text(father_name);
		$("#address").text(address);
		$("#city").text(city);
		$("#pincode").text(pincode);
		$("#dist_name").text(dist_name);
		$("#state_name").text(state_name);
		$("#dob").text(dob);
		$("#aadhar_number").text(aadhar_number);
		$("#student_photo").attr("src", student_photo);
		$("#ahref_student_photo").attr("data-exthumbimage", student_photo);
		$("#ahref_student_photo").attr("data-src", student_photo);
		$("#ahref_student_photo").attr("href", student_photo);

		$("#student_photo_with_aadhar").attr("src", student_photo_with_aadhar);
		$("#ahref_student_photo_with_aadhar").attr("data-exthumbimage", student_photo_with_aadhar);
		$("#ahref_student_photo_with_aadhar").attr("data-src", student_photo_with_aadhar);
		$("#ahref_student_photo_with_aadhar").attr("href", student_photo_with_aadhar);

		$("#img_front").attr("src", aadhar_front_filename);
		$("#ahref_img_front").attr("data-exthumbimage", aadhar_front_filename);
		$("#ahref_img_front").attr("data-src", aadhar_front_filename);
		$("#ahref_img_front").attr("href", aadhar_front_filename);

		$("#img_back").attr("src", aadhar_back_filename);
		$("#ahref_img_back").attr("data-exthumbimage", aadhar_back_filename);
		$("#ahref_img_back").attr("data-src", aadhar_back_filename);
		$("#ahref_img_back").attr("href", aadhar_back_filename);

		$("#profile_verification_status").val(profile_verification_status);
		$("#profile_rejection_comment").val(profile_rejection_comment);
		if(profile_verification_status == 'Rejected') {
			$("#comments").show();
		}
		
		$(".bd-example-modal-lg").modal('show');

		$("#spin_"+student_id).hide();
	} 
	
	$("#profile_verification_status").change(function() {
        // Get the selected value
        var selectedOption = $(this).val();
		$("#profile_rejection_comment").val("NA");
		$("#comments").hide();

		if(selectedOption == 'Rejected') {
			$("#comments").show();
		}
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
				$('#btn_update').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
				$('#btn_update').attr('disabled',true);
				$('#btn_update_clicked').val(1);
				
				var formData = new FormData($('#frmProfile')[0]);
				
				$.ajax({
					url: "<?php echo base_url('update-candidate-profile-verification-status'); ?>",
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(response){
						//console.log('response '+response); 
						//console.log('type '+response.type); 

						$('#btn_update').html('Update');
						$('#btn_update').attr('disabled',false);
							
						if(response.type == 'success') {
							$("#div_row_message").show();
							$("#div_message").removeClass("alert-danger");
							$("#div_message").addClass("alert-success");
							$("#spn_message").html('<strong>Success!</strong> Status updated successfully');
                            $("#svg_success").show();
                            $("#div_message").show();
						}
						else if(response.type == 'error') {
							$("#div_row_message").show();
							$("#div_message").removeClass("alert-success");
							$("#div_message").addClass("alert-danger");
							$("#spn_message").html('<strong>Error!</strong> Error while updating status');
                            $("#svg_error").show();
                            $("#div_message").show();
						}
					}
				});
			}

			form.classList.add('was-validated')
			}, false)
		})
	})()

	function updateProfileVerificationStatus(status) {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
			// Toggle the aria-expanded attribute
			$('#profile_verification_sub_menu').removeClass('show');
			$('#profile_verification-sub-menu a').removeClass('show');
			$('#profile_verification').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
			$('#profile_verification').attr('disabled',true);
			
			var formData = new FormData($('#frmMassUpdate')[0]);
			formData.append("profile_verification_status", status);
			
			$.ajax({
				url: "<?php echo base_url('update-candidate-profile-verification-status'); ?>",
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response){
					//console.log('response '+response); 
					//console.log('type '+response.type); 

					$('#profile_verification').html('Profile Verification');
					$('#profile_verification').attr('disabled',false);

					if(response.type == 'success') {
						sweetAlert("Success", "Status updated successfully!!", "success");
					}
					else if(response.type == 'error') {
						sweetAlert("Oops...", "Error while updating status!!", "error");
					}
					
					initializeOrReloadDataTable();
				}
			});
		}
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	}

	$("#btn_delete").click(function() {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
        	$(".modal-delete-confirm").modal('show'); 
		}	
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	});

	$("#btn_delete_candidates").click(function() {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
			$(".modal-delete-confirm").modal('hide'); 
        	var formData = new FormData($('#frmMassUpdate')[0]);
			formData.append("profile_verification_status", status);
			
			$.ajax({
				url: "<?php echo base_url('update-candidate-delete-status'); ?>",
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response){
					//console.log('response '+response); 
					//console.log('type '+response.type); 

					$('#profile_verification').html('Profile Verification');
					$('#profile_verification').attr('disabled',false);

					if(response.type == 'success') {
						sweetAlert("Success", "Candidate deleted successfully!!", "success");
					}
					else if(response.type == 'error') {
						sweetAlert("Oops...", "Error while deleting candidate!!", "error");
					}
					
					initializeOrReloadDataTable();
				}
			});
		}	
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	});
	
	$("#btn_reset_device_login").click(function() {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
			var formData = new FormData($('#frmMassUpdate')[0]);
			
			$('#btn_reset_device_login').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
			$('#btn_reset_device_login').attr('disabled',true);
			
			$.ajax({
				url: "<?php echo base_url('update-candidate-device-login-status'); ?>",
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response){
					//console.log('response '+response); 
					//console.log('type '+response.type); 

					$('#btn_reset_device_login').html('Reset Device Login');
					$('#btn_reset_device_login').attr('disabled',false);

					if(response.type == 'success') {
						sweetAlert("Success", "Device has been reset successfully!!", "success");
					}
					else if(response.type == 'error') {
						sweetAlert("Oops...", "Error while resetting device!!", "error");
					}

					initializeOrReloadDataTable();
				}
			});
		}
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	});
	
	function updateAttendanceStatus(status) {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
			// Toggle the aria-expanded attribute
			$('#attendance_status_sub_menu').removeClass('show');
			$('#attendance_status_sub_menu a').removeClass('show');
			$('#attendance_status').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
			$('#attendance_status').attr('disabled',true);
			
			var formData = new FormData($('#frmMassUpdate')[0]);
			formData.append("student_attendance", status);
			
			$.ajax({
				url: "<?php echo base_url('update-candidate-attendance-status'); ?>",
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response){
					//console.log('response '+response); 
					//console.log('type '+response.type); 

					$('#attendance_status').html('Attendance');
					$('#attendance_status').attr('disabled',false);

					if(response.type == 'success') {
						sweetAlert("Success", "Attendance updated successfully!!", "success");
					}
					else if(response.type == 'error') {
						sweetAlert("Oops...", "Error while updating attendance!!", "error");
					}

					initializeOrReloadDataTable();
				}
			});
		}
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	}

	function resetExam(exam_type) {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
			// Toggle the aria-expanded attribute
			$('#reset_exam_sub_menu').removeClass('show');
			$('#reset_exam_sub_menu a').removeClass('show');
			$('#reset_exam').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
			$('#reset_exam').attr('disabled',true);
			
			var formData = new FormData($('#frmMassUpdate')[0]);
			formData.append("exam_type", exam_type);
			
			$.ajax({
				url: "<?php echo base_url('reset-exam'); ?>",
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response){
					//console.log('response '+response); 
					//console.log('type '+response.type); 

					$('#reset_exam').html('Reset Exam');
					$('#reset_exam').attr('disabled',false);

					if(response.type == 'success') {
						sweetAlert("Success", "Reset Exam was successfull!!", "success");
					}
					else if(response.type == 'error') {
						sweetAlert("Oops...", "Error while resetting exam!!", "error");
					}

					initializeOrReloadDataTable();
				}
			});
		}
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	}

	$("#btn_generate_qp").click(function() {
		//Check whether checkbox is checked
		var chk_students_count  = $(".chk_students:checked").length; 
		//console.log('chk_students_count '+chk_students_count); 

		if(chk_students_count > 0) {
			var formData = new FormData($('#frmMassUpdate')[0]);
			
			$('#btn_generate_qp').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
			$('#btn_generate_qp').attr('disabled',true);
			
			$.ajax({
				url: "<?php echo base_url('generate-candidates-qp'); ?>",
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response){
					//console.log('response '+response); 
					//console.log('type '+response.type); 

					$('#btn_generate_qp').html('Generate QP');
					$('#btn_generate_qp').attr('disabled',false);

					if(response.type == 'success') {
						sweetAlert("Success", "QP has been generated successfully!!", "success");
					}
					else if(response.type == 'error') {
						sweetAlert("Oops...", "Error while generating QP!!", "error");
					}

					initializeOrReloadDataTable();
				}
			});
		}
		else {
			sweetAlert("Oops...", "Please select the candidates!!", "error");
		}
	});

</script>  
    