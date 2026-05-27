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
                                <div class="basic-form">
                                    <form class="needs-validation" novalidate id="upload_form" method="post" action="<?= site_url('save-trade') ?>" enctype="multipart/form-data">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Trade/QP Name</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="trade_id" id="single-select" required>
                                                    <option value="">Choose...</option>
                                                    <?php
                                                    foreach($arr_trades as $trades) {
                                                    ?>
                                                        <option value="<?php echo $trades['trade_id']; ?>"><?php echo $trades['trade_name']."-".$trades['trade_code']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                 <div class="invalid-feedback">
													Trade/QP Code is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label class="form-label">Select File</label>
                                                <input type="file" class="form-control" id="file" name="file" required accept=".xls, .xlsx">
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
                                        <button type="submit" class="btn btn-primary" id="btn_save"><i class="fa fa-upload"></i>&nbsp; Import Questions</button>
                                        <a href="javascript:void(0);" type="submit" class="btn btn-info" onClick="window.location.href='<?= site_url($this->config->item('question_import_sample_file_path')); ?>'" id="btn_save"><i class="fa fa-download"></i>&nbsp; Download Sample Format</a>
                                         
                                    </form>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="alert alert-dismissible fade show alert-info" id="total_questions_uploaded" style="display:none;">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                        <polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                        
                                    <span id="spn_questions_uploaded_message"></span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                </div>
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

                                <div class="table-responsive">
                                    <table id="tblError" class="table table-responsive-sm" style="display:none;">
                                        <thead class="table-danger">
                                            <tr>
                                                <th>#</th>
                                                <th>Sheet Name</th>
                                                <th>Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-body" id="accordion-three" style="display:none;">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
									<h4 class="card-title">Skipped Questions</h4>
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
                                                            <th>NOS Code</th>
															<th>Question Type</th>
															<th>Question</th>
                                                            <th>Marks</th>
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
            // Attach a submit event handler to the form
            $("#btn_save").on("click", function() {
                if ($("#file").val() === "") {
                    //sweetAlert("Oops...", "Please select the file to import !!", "error")
                }
                else {
                    $("#svg_success").hide();
                    $("#svg_error").hide();
                    $("#div_message").hide();
                    $("#accordion-three").hide();
                    $("#tblError").hide();
                    $("#total_questions_uploaded").hide();
                    $('#tblError .showError').remove();
                    
                    $('#btn_save').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
                    $('#btn_save').attr('disabled',true);
                    
                    var formData = new FormData($('#upload_form')[0]);
                    
                    $.ajax({
                        url: "<?php echo base_url('import-questions-save'); ?>",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response){
                            console.log('response '+response); 
                            //console.log('type '+response.type); 
                            // Update CSRF hash
                            $('#file').val('');
                            
                            if(response.type == 'success') {
                                if(response.totalImported > 0){
                                    $("#div_message").removeClass("alert-danger");
                                    $("#div_message").addClass("alert-success");
                                    //$("#spn_message").html('<strong>Success!</strong> '+response.message);
                                    //$("#svg_success").show();
                                    //$("#div_message").show();
                                    $("#tblError").hide();
                                }
                            }
                            else if(response.type == 'error') {
                                $("#div_message").removeClass("alert-success");
                                $("#div_message").addClass("alert-danger");
                                $("#spn_message").html('<strong>Error!</strong> '+response.message);
                                $("#svg_error").show();
                                $("#div_message").show();
                                if(response.upload_message_error != "") {
                                    $("#tblError").append(response.upload_message_error);
                                    $("#tblError").show();
                                }
                            }

                            $("#total_questions_uploaded").show();
                            $("#spn_questions_uploaded_message").html('<strong>Total Questions: </strong> '+response.totalQns+' <strong>Total Imported: </strong> '+response.totalImported+' <strong>Total Skipped: </strong> '+response.totalSkipped);

                            $('#btn_save').html('<i class="fa fa-upload"></i>&nbsp; Submit');
                            $('#btn_save').attr('disabled',false);
                            
                            if(response.totalSkipped > 0) {
                                $('#serverSideDataTable').dataTable().fnDestroy();
                                viewSkippedQuestions(response.unique_id);
                                $("#accordion-three").show();
                            }
                        }
                    });
                }    
            });
                
            function viewSkippedQuestions(unique_id) {
                $('#serverSideDataTable').DataTable({
                    // Processing indicator
                    "processing": true,
                    // DataTables server-side processing mode
                    "serverSide": true,
                    // Initial no order.
                    "order": [],
                    // Load data from an Ajax source
                    "ajax": {
                        "url": "<?php echo base_url('list-skipped-questions-ajax'); ?>",
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
