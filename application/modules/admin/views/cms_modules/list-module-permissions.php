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
          <a href="javascript:void(0);" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#add_permission">+ Add New Permission</a>  
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
								<h4 class="heading mb-0">Module Permissions for <?php echo $module_name; ?></h4> </div>
							<table id="tblmodulepermissions" class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>Name</th>
										<th>Description</th>
										<th>Text</th>
										<th class="no-sort">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $serialNumber = 1; ?>
										<?php 
										if($arr_module_permissions != false) {
											foreach ($arr_module_permissions as $row): ?>
												<tr>
													<td>
														<?= $serialNumber++ ?>
													</td>
													<td><?php echo $row['name']; ?></td>
													<td><?php echo $row['description']; ?></td>
													<td><?php echo $row['text']; ?></td>
													<td>
														<div class="d-flex">
														<a class="btn btn-primary shadow btn-xs sharp me-1" href="javascript:void(0);" id="edit_<?php echo $row['id']; ?>" 
															data-name="<?php echo $row['name']; ?>" data-description="<?php echo $row['description']; ?>" 
															data-text="<?php echo $row['text']; ?>" onclick="edit_popup(<?php echo $row['id']; ?>);" alt="Edit">
															<i class="fas fa-pencil-alt"></i> 
														</a>
														<a href="<?php echo site_url('delete-permission/'. $row['id']); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
<div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" aria-hidden="true" id="add_permission"> 
      <div class="modal-dialog modal-md add_permission">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="modalH5">Add Permission</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal">
                  </button>
              </div>
              <div class="modal-body">
                  <div class="table-responsive">
                  <form id="frmPermissions" class="needs-validation" novalidate action="<?php echo base_url(); ?>save-module-permission" method="POST" autocomplete="OFF">	 
                    <input type="hidden" id="row_id" name="row_id" value="0">
					<input type="hidden" id="module_id" name="module_id" value="<?php echo $module_id; ?>">
                    <div class="form-wrap">
                      <label class="col-form-label">Permission Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="name" name="name" required>
                      <div class="invalid-feedback" id="err_name">
                        Please provide the permission name.
                      </div>
                    </div>
                    <div class="form-wrap">
                      <label class="col-form-label">Description <span class="text-danger">*</span></label>
                      <textarea class="form-control" id="description" name="description" required></textarea>
                      <div class="invalid-feedback" id="err_description">
                        Please provide the description.
                      </div>
                    </div>
                    <div class="form-wrap">
						<label class="col-form-label">Text <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="text" name="text" required>
						<div class="invalid-feedback" id="err_description">
							Please provide the text.
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
	$('#tblmodulepermissions').DataTable({
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
		$("#name").on("blur", function() {
			var id = $("#row_id").val();
			var name = $(this).val();
			var module_id = $("#module_id").val();
			
			$("#err_name").html("Please provide the name.");
			$("#err_name").hide();

			// AJAX request
			$.ajax({
				url: "<?php echo base_url('check-duplicate-permission'); ?>",
				method: 'post',
				data: { id: id,name: name,module_id:module_id },
				dataType: 'json',
				success: function(response){
					//console.log('validate '+response.validate); 
					if(response.validate == true) {
						$("#err_name").html(name+" this permission already exists!");
						$("#err_name").show();
						$("#name").val('');
					}
					else {
						// Convert the name to lowercase
						var slug = name.toLowerCase()
						// Replace spaces with hyphens
						.replace(/\s+/g, '-')
						// Remove all special characters except hyphens and alphanumeric characters
						.replace(/[^a-z0-9-]/g, '')
						// Remove any leading or trailing hyphens
						.replace(/^-+|-+$/g, '');

						// Set the slug to the input field
						$('#slug').val(slug);
					}
				}
			});
		});
	});

	function edit_popup(id) {
		//console.log("Plan "+membersip_plan_id);
		var name = $("#edit_"+id).attr('data-name');
		var description = $("#edit_"+id).attr('data-description');
		var text = $("#edit_"+id).attr('data-text');
		
		$("#row_id").val(id);
		$("#modalH5").text('Edit Permission');
		$("#name").val(name);
		$("#description").val(description);
		$("#text").val(text);
		
		$("#add_permission").modal("show");
	}
</script>