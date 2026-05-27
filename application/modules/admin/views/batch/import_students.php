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
							<a href="<?php echo base_url(); ?>list-batches-inprocess" class="btn btn-primary btn-sm ms-2">Batches Listing</a>
						</div>
					</div>
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?= $title ?></h4>
                            </div>
                            <div class="card-body">
                                <?php
                                if($totalBatchStudents < $tb_target) { //&& ($qp_generated_status == 0  || $totalBatchStudents == 0 || ($totalBatchStudents == $totalPendingStudents)
                                ?>
                                    <div class="basic-form">
                                        <form class="needs-validation" novalidate id="upload_form" method="post" action="<?= site_url('import-students-save') ?>" enctype="multipart/form-data">
                                            <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                            <input type="hidden" name="tb_id" id="tb_id" value="<?php echo $tb_id; ?>">
                                            <input type="hidden" name="batch_id" id="batch_id" value="<?php echo $batch_id; ?>">
                                            <input type="hidden" name="tb_target" id="tb_target" value="<?php echo $tb_target; ?>">
                                            <input type="hidden" name="profile_updation" id="profile_updation" value="<?php echo $profile_updation; ?>">
                                            <input type="hidden" name="qp_generated_status" id="qp_generated_status" value="<?php echo $qp_generated_status; ?>">
                                            
                                            <div class="row">
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Select File</label>
                                                    <input type="file" class="form-control" id="file" name="file" required accept=".xls, .xlsx">
                                                    <div class="invalid-feedback">
                                                        File is required
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary" id="btn_save"><i class="fa fa-upload"></i>&nbsp; Submit </button>
                                            <a href="javascript:void(0);" type="submit" class="btn btn-info" onClick="window.location.href='<?= site_url($this->config->item('student_import_sample_file_path')); ?>'" id="btn_save"><i class="fa fa-download"></i>&nbsp; Download Sample Format</a>
                                        </form>
                                    </div>
                                <?php
                                }
                                else {
                                    if($totalBatchStudents >= $tb_target) {
                                ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                        <strong>Error!</strong> Sorry cannot import candidate(s) as Target(<?php echo $tb_target; ?>) already met for this batch.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                        </button>
                                    </div>
                                <?php
                                    }
                                    else if($qp_generated_status == 1) {   
                                    ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                        <strong>Error!</strong> Sorry cannot import candidate(s) as questions are already generated.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                        </button>
                                    </div>
                                    <?php
                                    }
                                }   
                                ?> 
                            </div>
                            <div class="card-body">
                                <div class="alert alert-dismissible fade show" id="div_message" style="display:none;">
                                    <svg id="svg_success" style="display:none;" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                        <polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                        
                                    <svg id="svg_error" style="display:none;" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                        <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>  
                                        
                                    <span id="spn_message"></span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                </div>
                            </div>
                            <div class="card dz-card" id="accordion-three" style="display:none;">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
									<h4 class="card-title">Skipped Students</h4>
									</div>
								</div>
							   
									<!-- /tab-content -->	
									<div class="tab-content" id="myTabContent-2">
										<div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
										     <div class="card-body pt-0">
												<div class="table-responsive">
												    <table id="serverSideDataTable" class="display table">
														<thead>
															<tr>
																<th></th>
																<th>CandidateID</th>
																<th>Aadharno</th>
																<th>Name of Candidate</th>
																<th>Skipped Reason</th>
															</tr>
														</thead>
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
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
        <!-- Font Awesome CSS -->
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css'>
        <link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        
        <script>
        $(document).ready(function() {
            $("#btn_save").on("click", function() {
                if ($("#file").val() === "") {
                    //sweetAlert("Oops...", "Please select the file to import !!", "error")
                }
                else {
                    $("#svg_success").hide();
                    $("#svg_error").hide();
                    $("#div_message").hide();
                    $("#accordion-three").hide();
                    
                    $('#btn_save').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
                    $('#btn_save').attr('disabled',true);
                    
                    var formData = new FormData($('#upload_form')[0]);
                    
                    $.ajax({
                        url: "<?php echo base_url('import-students-save'); ?>",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response){
                        //console.log('response '+response); 
                        //console.log('type '+response.type); 
                        // Update CSRF hash
                        $('#file').val('');
                        
                        if(response.type == 'success') {
                            $("#div_message").removeClass("alert-danger");
                            $("#div_message").addClass("alert-success");
                            $("#spn_message").html('<strong>Success!</strong>'+response.message);
                            $("#svg_success").show();
                            $("#div_message").show();
                        }
                        else if(response.type == 'error') {
                            $("#div_message").removeClass("alert-success");
                            $("#div_message").addClass("alert-danger");
                            $("#spn_message").html('<strong>Error!</strong>'+response.message);
                            $("#svg_error").show();
                            $("#div_message").show();
                        }
                        $('#btn_save').html('<i class="fa fa-upload"></i>&nbsp; Submit');
                        $('#btn_save').attr('disabled',false);
                        
                        if(response.skipped > 0) {
                            $('#serverSideDataTable').dataTable().fnDestroy();
                            viewSkippedStudents(response.unique_id);
                            $("#accordion-three").show();
                        }
                        }
                    });
                }
            });
            
            function viewSkippedStudents(unique_id) {
                $('#serverSideDataTable').DataTable({
                    // Processing indicator
                    "processing": true,
                    // DataTables server-side processing mode
                    "serverSide": true,
                    // Initial no order.
                    "order": [],
                    // Load data from an Ajax source
                    "ajax": {
                        "url": "<?php echo base_url('list-skipped-students-ajax'); ?>",
                        "type": "POST",
            			"data": { 'unique_id' : unique_id },
                    },
            		responsive: true,
            		/*dom: 'Bfrtip',
            			buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
            			],
            		*/
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
            }
        });
        
        </script>
