<!--**********************************

            Content body start

        ***********************************-->
<div class="content-body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?= $title ?></h4>
					</div>
				    <?php if ($this->session->flashdata('msg') != "") { ?>
						<div class="alert alert-success alert-dismissible fade show">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
								<polyline points="9 11 12 14 22 4"></polyline>
								<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
							</svg> <strong>Success!</strong>
							<?php echo $this->session->flashdata('msg'); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
						</div>
					<?php } ?>
				    <?php if ($this->session->flashdata('error') != "") { ?>
						<div class="alert alert-danger alert-dismissible fade show">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
								<polyline points="9 11 12 14 22 4"></polyline>
								<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
							</svg> <strong>Error ! </strong>
							<?php echo $this->session->flashdata('error'); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
						</div>
					<?php } ?>
				    
					<div class="card-body">
						<div class="form-validation">
							<form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('submit-change-password') ?>" autocomplete="OFF">
								<input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
								<input type="hidden" id="id" name="id" value="0">
								<input type="hidden" id="ckedtr_content" name="ckedtr_content">
								<div class="row">
									<div class="mb-3 col-md-6">
										<label class="form-label">New Password</label> <span class="text-danger">*</span>
										<input type="password" class="form-control" name="new_password" id="new_password" required />
										<div class="invalid-feedback"> New Password is required. </div>
									</div>
									<div class="mb-3 col-md-6">
										<label class="form-label">Confirm Password</label> <span class="text-danger">*</span>
										<input type="password" class="form-control" name="confirm_password" id="confirm_password" required />
										<div class="invalid-feedback"> Confirm Password is required. </div>
									</div>
								</div>
								<button type="submit" class="btn btn-primary">Update Password</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>