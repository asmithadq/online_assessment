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
                                <h4 class="card-title">Add <?= $title ?></h4>
                            </div>
                            <div class="card-body">
                                <?php 
                                if ($this->session->flashdata('msg') != "") { ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                    </div>
                                <?php } 
                                else if ($this->session->flashdata('error') != "") { ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                        <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                                    </div>
                                <?php } ?>  
                                <div class="basic-form">
                                     <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-assessment-document') ?>" autocomplete="OFF">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="acdm_id" name="acdm_id" value="0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Document Title</label>
                                                <input type="text" class="form-control" placeholder="" name="document_title" id="document_title" required>
                                                <div class="invalid-feedback" id="err_document_title">
													Document Title is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Document Type</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="document_type" id="document_type" required> 
        											<option value="">Please select</option>
        											<option value="File">File</option>	
                                                    <option value="Image">Image</option>																							
                                                    <option value="Text">Text</option>
                                                    <option value="Video">Video</option>
    										    </select>
    										    <div class="invalid-feedback">
														Document Type is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Category Type</label>
                                                <select class="form-control" name="checklist_cat_id" id="checklist_cat_id"> 
        											<option value="0">Please select</option>
                                                    <?php
                                                    if($arr_assessment_checklist_documents_category != false) {
                                                        foreach($arr_assessment_checklist_documents_category as $row) {
                                                        ?>
                                                            <option value="<?php echo $row['checklist_cat_id']; ?>"><?php echo $row['name']; ?></option>	
                                                        <?php
                                                        }
                                                    ?>
                                                        
                                                    <?php
                                                    }
                                                    ?>
        										</select>
    										</div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Is Required</label>
                                                 <select class="form-control" name="document_requirement" id="document_requirement" required> 
        											<option value="">Please select</option>
        											<option value="Mandatory">Mandatory</option>
        											<option value="Optional">Optional</option>
    										    </select>
                                                <div class="invalid-feedback">
													Document Requirement is required.
												</div>
                                            </div>
                                             <div class="mb-3 col-md-6">
                                                <label class="form-label">Status</label>
                                                <span class="text-danger">*</span>
                                                 <select class="form-control" name="status" id="status" required> 
        											<option value="">Please select</option>
        											<option value="1">Active</option>
        											<option value="0">In-active</option>
    										    </select>
    										    <div class="invalid-feedback">
													Status is required.
												</div>
                                            </div>
                                            
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="btn_save">Add Record</button>
                                    </form>
                                </div>
                            </div>
                        </div>
					</div>
					
					<div class="col-xl-12 col-lg-12">
					    <div class="card dz-card">

							   <div class="card-body pt-0">
							       
							      	<div class="table-responsive active-projects">
												    <div class="tbl-caption">
                                                        <h4 class="heading mb-0"><?= $title ?></h4>
                                                    </div>
													<table id="tbldocuments" class="table">
														<thead>
															<tr>
																<th>#</th>
																<th>Document Title</th>
																<th>Type</th>
                                                                <th>Category</th>
																<th>Upload</th>
																<th>Status</th>
																<th>Created Datetime</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php $serialNumber = 1; ?>
														     <?php foreach ($documents_data as $row): ?>
                                                                <tr>
                                                                    <td><?= $serialNumber++ ?></td>
                                                                    <td><?= $row->document_title ?></td>
                                                                    <td><?= $row->document_type ?></td>
                                                                    <td><?= $row->category ?></td>
                                                                    <td><?= $row->document_requirement ?></td>
                                                                    <td>
                                                                        <?php if ($row->status == 1) : ?>
                                                                            <span class="badge light badge-success border-0">Active</span>
                                                                        <?php else : ?>
                                                                            <span class="badge light badge-danger border-0">Inactive</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?= date('d-m-Y H:i:s', strtotime($row->created_dts)) ?></td>
                                                                    <td>
    																<div class="d-flex">
    																	<a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->acdm_id;  ?>"
    																	    data-document_title="<?php echo $row->document_title ?>" data-document_type="<?php echo $row->document_type ?>"
    																	    data-document_requirement="<?php echo $row->document_requirement ?>" data-status="<?php echo $row->status ?>" 
                                                                            data-checklist_cat_id="<?php echo $row->checklist_cat_id ?>"
    																	    onclick="viewEditDetails(<?php echo $row->acdm_id; ?>);"> 
    																	    <i class="fas fa-pencil-alt"></i></a>
    																	<a href="<?php echo site_url('delete-assessment-document/'. $row->acdm_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
    																</div>												
    															</td>
                                                                </tr>
                                                                <?php endforeach; ?>
														</tbody>
													</table>
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
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script>
        $(document).ready(function(){
                $('#tbldocuments').DataTable({
                    //dom: 'Bfrtip',
                    'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'Assessment Documents', // Specify your custom file name here
                            filename: function() {
                                // Custom filename function can be used for dynamic file names
                                return 'Assessment Documents -' + '<?php echo date('d-m-Y H:i:s') ?>';
                            },
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5] // Include all columns except 3rd and 6th
                            }
                        }
                    ],
                    searching: true,
                    select: true,   
                    /* pageLength:5, */			
                    lengthChange:true ,
                    language: {
                        paginate: {
                            next: '<i class="fa-solid fa-angle-right"></i>',
                            previous: '<i class="fa-solid fa-angle-left"></i>' 
                        }
                        
                    },
                    
                });
            });
            $("#document_title").on("blur", function() {
                var acdm_id = $("#acdm_id").val();
                var document_title = $.trim($(this).val());
                
                $("#err_document_title").html("Please enter Language Name.");
                $("#err_document_title").hide();

                // Remove special characters using a regular expression
                var sanitizedValue = document_title.replace(/[^a-zA-Z0-9\s_\-/]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
                
                //console.log('nos_code '+nos_code);
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                $.ajax({
                    url: "<?php echo base_url('check-duplicate-document-title'); ?>",
                    method: 'post',
                    data: { acdm_id: acdm_id,document_title: document_title },
                    dataType: 'json',
                    success: function(response){
                        //console.log('validate '+response.validate); 
                        if(response.validate == true) {
                            $("#err_document_title").html(document_title+" this document already exists!");
                            $("#err_document_title").show();
                            $("#document_title").val('');
                        }
                    }
                });
            });

            function viewEditDetails(acdm_id) {
                var document_title = $("#edit_"+acdm_id).attr('data-document_title');
                var document_type = $("#edit_"+acdm_id).attr('data-document_type');
                var document_requirement = $("#edit_"+acdm_id).attr('data-document_requirement');
                var checklist_cat_id = $("#edit_"+acdm_id).attr('data-checklist_cat_id');
                var status = $("#edit_"+acdm_id).attr('data-status');
                
                //console.log("document_title "+document_title);
                
                $("#acdm_id").val(acdm_id);
                $("#document_title").val(document_title);
                $("#document_type").val(document_type);
                $("#checklist_cat_id").val(checklist_cat_id);
                $("#document_requirement").val(document_requirement);
                $("#status").val(status);
                $("#btn_save").html('Update Record');
                $("#title").html('Update <?php echo $title ?>');
                
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
    </script>  
 