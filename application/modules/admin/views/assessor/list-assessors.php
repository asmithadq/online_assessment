<style>
    .img-thumbnail {
	padding: 0.25rem;
	border: 1px solid #c0c0c0;
	/* Thin border */
	border-radius: 1.75rem;
	max-width: 100%;
}
</style>		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="row">
					<div class="d-flex justify-content-between align-items-center mb-4">
					<h4 class="heading mb-0">&nbsp;</h4>
						<div class="d-flex align-items-center">
							<a href="<?php echo base_url(); ?>create-assessor" class="btn btn-primary btn-sm ms-2">+ Add Assessor</a>
						</div>
					</div>
					<div class="col-xl-12 active-p">
					<div class="tab-content" id="pills-tabContent">
						<div class="col-xl-12 col-lg-12">
                            <?php 
                            if ($this->session->flashdata('msg') != "") { ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                    <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                </div>
                            <?php } 
							else if ($this->session->flashdata('error') != "") { ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                    <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                </div>
                            <?php } ?> 
							<div class="card dz-card">
								<div id="div_spin" style="display:none;">
									<span  class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
								</div>
								<div class="card-body p-0">
									<div class="table-responsive active-projects">
										<div class="tbl-caption">
											<h4 class="card-title">Assessors</h4>
										</div>
										<input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
										<table id="serverSideDataTable" class="display table">
											<thead>
												<tr>
													<th></th>
													<th>Assessor Name (Code)</th>
													<th>Email</th>
													<th>Mobile</th>
													<th>Password</th>
													<th>SSC's</th>
													<th>State</th>
													<th>District</th>
													<th>Status</th>
													<th>Action</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
						</div>
						
						</div>
					</div>
					</div>
				</div>
			</div>
        </div>
		<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assessor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-id-badge"></i> Assessor Profile</h6>
                        <ul class="list-unstyled">
                            <li><strong>Assessor Code:</strong> <span id="assessor_code"></span></li>
                            <li><strong>Assessor Name:</strong> <span id="assessor_name"></span></li>
                            <li><strong>Gender:</strong> <span id="assessor_gender"></span></li>
                            <li><strong>Mobile:</strong> <span id="assessor_mobile"></span></li>
                            <li><strong>Email Address:</strong> <span id="assessor_email"></span></li>
                            <li><strong>Status:</strong> <span id="assessor_status"></span></li>
                        </ul>
                    </div>
					<div class="col-md-4">
                        <h6 class="mb-3"><i class="fas fa-address-card"></i> Address</h6>
                        <ul class="list-unstyled">
                            <li><strong>Address:</strong> <span id="address"></span></li>
                            <li><strong>State:</strong> <span id="state_name"></span></li>
                            <li><strong>District:</strong> <span id="dist_name"></span></li>
							<h6 class="mb-3"><i class="fas fa-file"></i> Resume</h6>
							<a href="" target="_blank" id="assessor_resume">View Resume</a>
                        </ul>
					</div>
					<div class="col-md-2 text-center">
                       <img id="assessor_photo" src="" alt="Assessor Photo" class="img-responsive img-thumbnail" style="max-width: 150px; max-height: 150px;">
					</div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-map-marked-alt"></i> Map Sector Skill Councils</h6>
						<span id="mapssc"></span>
                    </div>
					<div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-map-marked-alt"></i> Associated Agencies</h6> 
						<ul class="list-unstyled" id="mapAssociatedAgencies"></ul>
					</div>
                </div>
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
 <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script>
            $(document).ready(function(){
                // get the hash 
                var csrf_hash_name = $("input[name=csrf_hash_name]").val();
                
                $('#serverSideDataTable').DataTable({ 
                    // Processing indicator
                    "processing": true,
                    // DataTables server-side processing mode
                    "serverSide": true,
                    // Initial no order.
                    "order": [],
                    // Load data from an Ajax source
                    "ajax": {
                        "url": "<?php echo base_url('list-assessors-ajax'); ?>",
                        "type": "POST",
            			"data": { 'csrf_hash_name' : csrf_hash_name },
                    },
            		responsive: true,
            		'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'Assessors Master', // Specify your custom file name here
                            action: function (e, dt, node, config) {
								// Perform your AJAX call here
								$.ajax({
									"url": "<?php echo base_url('export-assessors'); ?>",
									type: 'POST',
									//data: { length: '-1', param2: 'value2' },
									success: function(response) {
										window.location.href = '<?php echo base_url('export-assessors'); ?>';
									},
									error: function(xhr, status, error) {
										// Handle error
									}
								});
							}
                        }
                    ],
                    //Set column definition initialisation properties
                    "columnDefs": [{ 
                        "targets": [0],
                        "orderable": false
                    }],
            		language: {
            			paginate: {
            			  next: '<i class="fa-solid fa-angle-right"></i>',
            			  previous: '<i class="fa-solid fa-angle-left"></i>' 
            			}
            		}
            	  
                });
            });


	function getAssessorDetails(assessor_id) {
		$("#spin_"+assessor_id).show();
		
		var assessor_photo = '<?php echo base_url().$this->config->item('assessors_images_path'); ?>'+$("#btn-"+assessor_id).attr('data-assessor_photo');
		
		var assessor_resume = '<?php echo base_url().$this->config->item('assessors_images_path'); ?>'+$("#btn-"+assessor_id).attr('data-assessor_resume');
		
		$("#assessor_code").text($("#btn-"+assessor_id).attr('data-assessor_code'));
		$("#assessor_name").text($("#btn-"+assessor_id).attr('data-assessor_name'));
		$("#assessor_gender").text($("#btn-"+assessor_id).attr('data-assessor_gender'));
		$("#assessor_mobile").text($("#btn-"+assessor_id).attr('data-assessor_mobile'));
		$("#assessor_email").text($("#btn-"+assessor_id).attr('data-assessor_email'));
		$("#address").text($("#btn-"+assessor_id).attr('data-address'));
		$("#state_name").text($("#btn-"+assessor_id).attr('data-state_name'));
		$("#dist_name").text($("#btn-"+assessor_id).attr('data-dist_name'));
		$("#assessor_status").text($("#btn-"+assessor_id).attr('data-assessor_status'));
		$("#mapssc").html($("#btn-"+assessor_id).attr('data-mapssc'));
		$("#mapAssociatedAgencies").html($("#btn-"+assessor_id).attr('data-mapAssociatedAgencies'));
		
		$("#assessor_photo").attr("src", assessor_photo);
		$("#assessor_resume").attr("href", assessor_resume);
	
		/*var enrollment_number = $("#btn-"+student_id).attr('data-enrollment_number');
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
		$("#student_photo").attr("src", student_photo);
		$("#img_front").attr("src", aadhar_front_filename);
		$("#aadhar_number").text(aadhar_number);
		$("#img_front").attr("src", aadhar_front_filename);
		$("#img_back").attr("src", aadhar_back_filename);
		$("#profile_verification_status").val(profile_verification_status);
		$("#profile_rejection_comment").val(profile_rejection_comment);
		if(profile_verification_status == 'Rejected') {
			$("#comments").show();
		}
		*/
		
		$(".bd-example-modal-lg").modal('show');

		$("#spin_"+assessor_id).hide();
	} 
        </script>


