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
                                <h4 class="card-title"><?= $title ?></h4>
                            </div>
                            <div class="card-body">
                                <?php 
                                    if ($this->session->flashdata('msg') != "") { ?>
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                            <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                        </div>
                                    <?php } ?>
                                <div class="basic-form">
                                    <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('import-questions-save') ?>" enctype="multipart/form-data">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
										
                                        <div class="row">
                                           <div class="mb-3 col-md-4">
                                                <label class="form-label">Select File</label>
                                                <input type="file" class="form-control" id="file" name="file">
                                                <div class="invalid-feedback">
													File is required
												</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3">
                                            <label class="form-label">Select Languages</label> 
                                                <div class="form-check">
                                                    <?php
                                                    foreach($arr_languages as $lang) {
                                                    ?>
                                                        <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" value="<?php echo $lang['language_id'].'_'.$lang['language_name']; ?>" name="lid[]" <?php echo ($lang['language_id'] == 1) ? 'checked disabled' : ''; ?>>
                                                        <label class="form-check-label"><?php echo $lang['language_name']; ?></label>
                                                    </div>
                                                    <?php
                                                    }
                                                    ?>   
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="btn_save"><i class="fa fa-upload"></i>&nbsp; Submit </button>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
					</div>
                </div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
