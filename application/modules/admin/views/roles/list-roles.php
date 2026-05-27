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
          <a href="javascript:void(0);" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#add_role">+ Add New Role</a>  
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
							<table id="tblroles" class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>Role Name</th>
										<th>Created at</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $serialNumber = 1; ?>
										<?php 
										if($arr_roles != false) {
											foreach ($arr_roles as $row): ?>
												<tr>
													<td>
														<?= $serialNumber++ ?>
													</td>
													<td><?php echo $row['role_name']; ?></td>
													<td><?php echo date("d M Y, h:i a",strtotime($row['created_dts'])); ?></td>
													<td>
														<div class="d-flex">
														<a class="btn btn-primary shadow btn-xs sharp me-1" href="javascript:void(0);" id="edit_<?php echo $row['id']; ?>" 
															data-role_name="<?php echo $row['role_name']; ?>" onclick="edit_popup(<?php echo $row['id']; ?>);" alt="Edit"><i class="fas fa-pencil-alt"></i> 
														</a>
														<a class="btn btn-secondary shadow btn-xs sharp me-1" href="<?php echo base_url(); ?>list-roles-permissions/<?php echo $row['id']; ?>" alt="Permission">
															<i class="fas fa-shield-alt text-success"></i> 
														</a>
														<a href="<?php echo site_url('delete-role/'. $row['id']); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
														</div>
													</td>
												</tr>
											<?php endforeach; 
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
<div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" aria-hidden="true" id="add_role"> 
      <div class="modal-dialog modal-md add_role">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="modalH5">Add Role</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal">
                  </button>
              </div>
              <div class="modal-body">
                  <div class="table-responsive">
                  <form id="frmRole" class="needs-validation" novalidate action="<?php echo base_url(); ?>save-role" method="POST" autocomplete="OFF">	 
                    <input type="hidden" id="row_id" name="row_id" value="0">
                    <div class="form-wrap">
						<label class="col-form-label">Role Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="role_name" name="role_name" required>
						<div class="invalid-feedback" id="err_role_name">
							Please provide the role name.
						</div>
					</div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                      <button type="submit" id="btn_submit" class="btn btn-primary">Save</button>
                    </div>
                  </form>
                  </div>
                  
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
	$('#tblroles').DataTable({
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
	$("#role_name").on("input", function() {
		var company_id = '<?php echo $this->session->userdata('company_id'); ?>';
		var role_id = $("#row_id").val();
		var role_name = $(this).val();
		
		$("#err_role_name").html("Please provide the role name.");
		$("#err_role_name").hide();

		// AJAX request
		$.ajax({
			url: "<?php echo base_url('check-duplicate-role'); ?>",
			method: 'post',
			data: { company_id: company_id,role_id: role_id,role_name: role_name },
			dataType: 'json',
			success: function(response){
				//console.log('validate '+response.validate); 
				if(response.validate == true) {
					$("#err_role_name").html(role_name+" this role already exists!");
					$("#err_role_name").show();
					$("#role_name").val('');
				}
			}
		});
	});
});

function edit_popup(id) {
	//console.log("Plan "+membersip_plan_id);
	var role_name = $("#edit_"+id).attr('data-role_name');
	
	$("#row_id").val(id);
	$("#modalH5").text('Edit Role');
	$("#role_name").val(role_name);
	
	$("#add_role").modal("show");
}
</script>