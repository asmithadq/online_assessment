<!--**********************************

            Content body start

        ***********************************-->
<div class="content-body">
	<div class="container-fluid">
		<!-- row -->
		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Add <?= $title ?></h4> </div>
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
						<?php } ?>
							<div class="card-body">
								<div class="form-validation">
									<form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-email') ?>" autocomplete="OFF">
										<input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
										<input type="hidden" id="id" name="id" value="0">
										<input type="hidden" id="ckedtr_content" name="ckedtr_content">
										<div class="row">
											<div class="mb-3 col-md-12">
												<label class="form-label">Email Subject</label> <span class="text-danger">*</span>
												<input type="text" class="form-control" name="email_subject" id="email_subject" placeholder="Subject" required>
												<div class="invalid-feedback"> Email Subject is required. </div>
											</div>
											<div class="mb-3 col-md-12">
												<label class="form-label">Email Content</label> <span class="text-danger">*</span>
												<textarea name="email_content" id="editor"></textarea>
												<div class="invalid-feedback"> Content is required. </div>
											</div>
											<div class="mb-3 col-md-6">
												<label class="form-label">Status</label> <span class="text-danger">*</span>
												<select name="status" id="status" class="form-control" required>
													<option value="">Please select</option>
													<option value="1">Active</option>
													<option value="0">In-active</option>
												</select>
												<div class="invalid-feedback"> Status is required. </div>
											</div>
										</div>
										<button type="submit" class="btn btn-primary" id="btn_save">Add Record</button>
									</form>
								</div>
							</div>
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
							<div class="card dz-card" id="accordion-three">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
										<h4 class="card-title"><?= $title ?></h4> </div>
								</div>
								<!-- /tab-content -->
								<div class="tab-content" id="myTabContent-2">
									<div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
										<div class="card-body pt-0">
											<div class="table-responsive">
												<table id="example3" class="display table">
													<thead>
														<tr>
															<th>#</th>
															<th>Subject</th>
															<th>Content</th>
															<th>Status</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														<?php $serialNumber = 1; ?>
															<?php foreach ($email_data as $row): ?>
																<tr>
																	<td>
																		<?= $serialNumber++ ?>
																	</td>
																	<td>
																		<?= $row->email_subject; ?>
																	</td>
																	<td> <a href="#" class="btn btn-primary shadow btn-xs sharp me-1" onclick="getEmailContent('<?= $row->id ?>');"><i class="fas fa-eye"></i></a>
																	<span id="spin_<?= $row->id ?>" style="display:none;" class="fa-stack fa-lg"><i class="fa fa-spinner fa-spin fa-stack-2x fa-fw"></i></span> </td>
																	<td>
																		<?php if ($row->status == 1) : ?> <span class="badge light badge-success border-0">Active</span>
																			<?php else : ?> <span class="badge light badge-danger border-0">Inactive</span>
																				<?php endif; ?>
																	</td>
																	<td>
																		<div class="d-flex">
																			<a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->id;  ?>" data-id="<?php echo $row->id ?>" onclick="viewEditDetails(<?php echo $row->id; ?>);"> <i class="fas fa-pencil-alt"></i></a> <a href="<?php echo site_url('delete-email/'. $row->id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a> </div>
																	</td>
																</tr>
																<?php endforeach; ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<!-- /tab-content -->
							</div>
			</div>
		</div>
	</div>
</div>
<!-- Large modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div>Email Content</div>
				<button type="button" class="btn-close" data-bs-dismiss="modal"> </button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<div id="nosData"> </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End Large modal -->
<!--**********************************
Content body end
 ***********************************-->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor5-build-classic/ckeditor.js"></script>
<script>
	let editor;

	ClassicEditor
		.create( document.querySelector( '#editor' ) )
		.then( newEditor => {
			editor = newEditor;
		} )
		.catch( error => {
			console.error( error );
    } );

	function viewEditDetails(template_id) {
		var csrfName = $('.txt_csrfname').attr('name'); // Value specified
		var csrfHash = $('.txt_csrfname').val(); // CSRF hash
		$.ajax({
			url: "<?php echo base_url('edit-email-template'); ?>",
			method: 'post',
			data: {
				template_id: template_id,
				[csrfName]: csrfHash
			},
			dataType: 'json',
			success: function(response) {
				// Update CSRF hash
				$('.txt_csrfname').val(response.token);
				$("#id").val(template_id);
				$("#email_subject").val(response.subject);
				editor.setData(response.content);
				$("#status").val(response.status);
			}
		});
	$("#btn_save").html('Update Record');
	$("#title").html('Update <?php echo $title ?>');
	$("html, body").animate({
		scrollTop: 0
	}, "slow");
}

function getEmailContent(template_id) {
	$("#spin_" + template_id).show();
	// CSRF Hash
	var csrfName = $('.txt_csrfname').attr('name'); // Value specified
	var csrfHash = $('.txt_csrfname').val(); // CSRF hash
	$.ajax({
		url: "<?php echo base_url('get-mapped-email-content'); ?>",
		method: 'post',
		data: {
			template_id: template_id,
			[csrfName]: csrfHash
		},
		dataType: 'json',
		success: function(response) {
			// Update CSRF hash
			$("#spin_" + template_id).hide();
			$("#nosData").html(response.output);
			$(".bd-example-modal-lg").modal('show');
		}
	});
}
</script>
