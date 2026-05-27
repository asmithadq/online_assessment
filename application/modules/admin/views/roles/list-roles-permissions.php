<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
	<div class="container-fluid">
		<!-- row -->
		<div class="row">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="heading mb-0">&nbsp;</h4>
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
				<form id="frmRolePermissions" action="<?php echo base_url(); ?>save-role-permissions" class="needs-validation" novalidate method="POST" autocomplete="OFF">
				<input type="hidden" id="role_id" name="role_id" value="<?php echo $role_id; ?>">	
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<div class="col-md-5 col-sm-4">
										<div class="role-name">
											<h4>Role Name : <span class="text-danger"><?php echo $role_name; ?></span></h4>
										</div>						
									</div>
									<div class="clearfix ms-auto">		
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" role="switch" id="allow_all_modules">
											<label class="form-check-label" for="switch-md">Allow All Modules</label>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="tab-content">
									<div class="tab-pane active" id="tabWeek2" role="tabpanel" aria-labelledby="week-tab2" tabindex="0">
										<div class="table-responsive">
											<table class="table mb-1 table-striped-thead table-wide table-sm table-border-last-0">
												<thead>
													<tr>
														<th>Modules</th>
														<th>Permissions</th>
														<th>Allow All</th>
													</tr>
												</thead>
												<tbody>
												<?php
												if($arrModules != false) {
													foreach($arrModules as $module_id => $module_name) {
													?>
													<tr>
														<td><?php echo $module_name; ?></td>
														<td>
															<div class="row modules" id="<?php echo $module_id; ?>">
															<?php
																foreach($arrPermissions[$module_id] as $permission_id => $permission_text) {
																	$checked = "";
																	if(array_key_exists($permission_id,$arr_assigned_permission)) {
																		$checked = "checked";
																	}
															?>
																<div class="col-md-2">
																	<div class="form-check form-check-md form-switch">
																		<input class="form-check-input module_<?php echo $module_id; ?>" type="checkbox" role="switch" id="permission_id_<?php echo $permission_id; ?>" name="permission_id[<?php echo $permission_id; ?>]" onchange="toggleAll(<?php echo $module_id; ?>);" <?php echo $checked; ?>>
																		<label class="form-check-label" for="switch-md"><?php echo $permission_text; ?></label>
																	</div>
																</div>	
															<?php } ?>
															</div>
														</td>
														<td>
															<div class="form-check form-check-md form-switch">
																<input class="form-check-input allow_all" type="checkbox" role="switch" data-id="<?php echo $module_id; ?>" id="allow_all_<?php echo $module_id; ?>">
																<label class="form-check-label" for="switch-md"></label>
															</div>
														</td>
													</tr>
													<?php
													}
												}
												?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								</div>
								<div class="card-footer text-end">
									<button type="submit" class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>
					</div>
				</form>	
			</div>
			
		</div>
	</div>
</div>
<!--**********************************
            Content body end
***********************************-->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script>
	validateModulesToggle();
	
	$(document).ready(function() {
		// Listen for changes on the checkbox with the id 'allow_all_modules'
			$('#allow_all_modules').on('change', function() {
			// Check or uncheck all '.allow_all' checkboxes based on 'allow_all_modules' checkbox
			$('.allow_all').prop('checked', $(this).prop('checked')).trigger('change');
		});

		$('.allow_all').on('change', function() {
			// Get the data-id of the clicked 'allow_all' checkbox
			var dataId = $(this).data('id');

			// Check or uncheck all checkboxes with the class 'module_' that share the same data-id
			if ($(this).is(':checked')) {
				// If the allow_all checkbox is checked, check all related module_ checkboxes
				$('.module_'+ dataId).prop('checked', true);
			} else {
				// If the allow_all checkbox is unchecked, uncheck all related module_ checkboxes
				$('.module_'+ dataId).prop('checked', false);
			}

			allowAllModules();
		});
	});

	function toggleAll(module_id) {
		//console.log('module_id '+module_id); 
		// Check if all item-checkbox elements are checked
        if ($('.module_'+module_id+':checked').length === $('.module_'+module_id).length) {
            $('#allow_all_'+module_id).prop('checked', true); // Check the "Select All" checkbox
        } else {
            $('#allow_all_'+module_id).prop('checked', false); // Uncheck the "Select All" checkbox 
        }

		allowAllModules();
	}

	function validateModulesToggle() {
		$('.modules').each(function () {
			var moduleId = $(this).attr('id');
			//console.log(moduleId); // Output the ID to the console (or do something else with it)
			toggleAll(moduleId);
		});
	}

	function allowAllModules() {
		if ($('.allow_all:checked').length === $('.allow_all').length) {
			$('#allow_all_modules').prop('checked', true); // Check the "All modules" checkbox
		} else {
			$('#allow_all_modules').prop('checked', false); // Uncheck the "All modules" checkbox 
		}
	}
</script>