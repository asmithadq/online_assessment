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
          <a href="javascript:void(0);" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#add_user">+ Add New User</a>  
        </div>
      </div>
			<div class="col-xl-12 col-lg-12">
				<?php 
                            if ($this->session->flashdata('msg') != "") { ?>
					<div class="alert alert-success alert-dismissible fade show">
						<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
							<polyline points="9 11 12 14 22 4"></polyline>
							<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
						</svg> <strong>Success!</strong>
						<?php echo $this->session->flashdata('msg'); ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
					</div>
					<?php } 
							                    else if ($this->session->flashdata('error') != "") { ?>
						<div class="alert alert-danger alert-dismissible fade show">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
								<polyline points="9 11 12 14 22 4"></polyline>
								<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
							</svg> <strong>Error!</strong>
							<?php echo $this->session->flashdata('error'); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
						</div>
						<?php } ?>
			</div>
			<div class="col-xl-12">
				<div class="card dz-card">
					<div class="card-body p-0">
						<div class="table-responsive active-projects">
							<div class="tbl-caption">
								<h4 class="heading mb-0"><?= $title ?></h4> </div>
							<table id="tblUsers" class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Email/Username</th>
										<th>Mobile No</th>
										<th>Role</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $serialNumber = 1; ?>
										<?php 
										if($arr_users != false) {
											foreach($arr_users as $row) {
												if($row['role_name'] != 'superadmin') {
										?>			
												<tr>
													<td>
														<?= $serialNumber++ ?>
													</td>
													<td><?php echo $row['firstname']; ?></td>
													<td><?php echo $row['lastname']; ?></td>
													<td><?php echo $row['email']; ?></td>
													<td><?php echo $row['mobile_no']; ?></td>
													<td><?php echo $row['role_name']; ?></td>
													<td><?php echo ($row['status'] == 1) ? 'Active' : 'Inactive'; ?></td>
													<td>
														<div class="d-flex">
														<a class="btn btn-primary shadow btn-xs sharp me-1" href="javascript:void(0);" id="edit_<?php echo $row['admin_id']; ?>" 
															data-firstname="<?php echo $row['firstname']; ?>" data-lastname="<?php echo $row['lastname']; ?>"
															data-email="<?php echo $row['email']; ?>" data-mobile_no="<?php echo $row['mobile_no']; ?>"
															data-role_id="<?php echo $row['role_id']; ?>" data-status="<?php echo $row['status']; ?>" 
															onclick="edit_popup(<?php echo $row['admin_id']; ?>);" alt="Edit"><i class="fas fa-pencil-alt"></i> 
														</a>
														<a href="<?php echo site_url('delete-user-details/'. $row['admin_id']); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
														</div>
													</td>
												</tr>
												<?php
												}
											}
										}
										?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Large modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" id="add_user"> 
      <div class="modal-dialog modal-lg add_user">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="modalH5">Add Role</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal">
                  </button>
              </div>
              <div class="modal-body">
                  
                  <form id="frmUser" class="needs-validation" novalidate action="<?php echo base_url(); ?>save-user" method="POST" autocomplete="OFF">	 
                    <input type="hidden" id="row_id" name="row_id" value="0">
                    <div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="contact_name" class="col-form-label">First Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="firstname" name="firstname" required>
								<div class="invalid-feedback">
									Please provide the user's first name.
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="contact_name" class="col-form-label">Last Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="lastname" name="lastname" required>
								<div class="invalid-feedback">
									Please provide the user's last name.
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
								<input type="email" class="form-control" id="email" name="email" required>
								<div class="invalid-feedback" id="err_email">
									Please provide a valid email address.
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="phone1" class="col-form-label">Mobile No <span class="text-danger">*</span></label>
								<input type="tel" class="form-control" id="mobile_no" name="mobile_no" required>
								<div class="invalid-feedback">
									Please provide contact no.
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3 col-md-6">
								<label for="membership_plan" class="col-form-label">Role <span class="text-danger">*</span></label>
								<select class="form-control" id="role_id" name="role_id" required>
									<option value="">Choose</option>
									<?php
									if($arr_roles != false) {
										foreach($arr_roles as $roles) {
										?>
											<option value="<?php echo $roles['id']; ?>"><?php echo $roles['role_name']; ?></option>
										<?php
										}	
									}
									?>
								</select>
								<div class="invalid-feedback">
									Please choose a role.
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="radio-wrap">
								<label class="col-form-label">Status</label>
								<div class="d-flex flex-wrap">
									<div class="radio-btn">
										<input type="radio" class="status-radio" id="active1" name="status" value="1" checked>
										<label for="active1">Active</label>
									</div>
									<div class="radio-btn">
										<input type="radio" class="status-radio" id="inactive1" name="status" value="0"> 
										<label for="inactive1">Inactive</label>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Row -->
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                      <button type="submit" id="btn_submit" class="btn btn-primary">Save</button> 
                    </div>
                  </form>
                  
                  
              </div>
              
          </div>
      </div>
  </div>
  <!-- End Large modal -->
<!--**********************************
            Content body end
***********************************-->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script>
$(document).ready(function() {
	$('#tblUsers').DataTable({
		responsive: true,
		searching: true,
		select: true,
		/* pageLength:5, */
		lengthChange: true,
		language: {
			paginate: {
				next: '<i class="fa-solid fa-angle-right"></i>',
				previous: '<i class="fa-solid fa-angle-left"></i>'
			}
		},
	});
});

$(document).ready(function(){
	$("#email").on("blur", function() {
		var user_id = $("#row_id").val();
		var email = $(this).val();
		
		$("#err_email").html("Please provide a valid email address.");
		$("#err_email").hide();

		// AJAX request
		$.ajax({
			url: "<?php echo base_url('check-duplicate-user-email'); ?>",
			method: 'post',
			data: { user_id: row_id,email: email },
			dataType: 'json',
			success: function(response){
				//console.log('validate '+response.validate); 
				if(response.validate == true) {
					$("#err_email").html(email+" this email already exists!");
					$("#err_email").show();
					$("#email").val('');
				}
			}
		});
	});
});

function edit_popup(id) {
	//console.log("Plan "+membersip_plan_id);
	var firstname = $("#edit_"+id).attr('data-firstname');
	var lastname = $("#edit_"+id).attr('data-lastname');
	var email = $("#edit_"+id).attr('data-email');
	var mobile_no = $("#edit_"+id).attr('data-mobile_no');
	var role_id = $("#edit_"+id).attr('data-role_id');
	var status = $("#edit_"+id).attr('data-status');

	console.log('role_id '+role_id);
	
	$("#row_id").val(id);
	$("#modalH5").text('Edit User');
	$("#firstname").val(firstname);
	$("#lastname").val(lastname);
	$("#email").val(email);
	$("#mobile_no").val(mobile_no);
	$("#role_id").val(role_id).trigger("change");
	$("#status").val(status);

	$("#add_user").modal("show");

	
	
}
</script>