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
					<div class="col-xl-12 col-lg-8">
						<div class="card profile-card card-bx m-b30">
							<div class="card-header">
								<h6 class="title">Upload Resume</h6>
							</div>
							<form class="needs-validation" novalidate id="frmProfile" method="post" action="<?= site_url('save-resume-upload') ?>" enctype="multipart/form-data" autocomplete="OFF">
								<input type="hidden" name="assessor_id" id="assessor_id" value="<?php echo $assessor_id; ?>"/>
            					<div class="card-body">
									<div class="row">
										<div class="col-sm-3 m-b30">
											<label class="form-label">Resume<span class="text-danger">*</span></label>
											<div class="upload-container">
												<input type="file" class="form-control" id="assessor_resume" name="assessor_resume" required accept="image/*,.pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
											</div>
										</div>
                                    </div>
								</div>
								<div class="card-footer">
									<button class="btn btn-primary">Upload</button>
								</div>
							</form>
						</div>
					</div>
				</div>
            </div>
        </div>
		<!--**********************************
            Content body end
        ***********************************-->
		