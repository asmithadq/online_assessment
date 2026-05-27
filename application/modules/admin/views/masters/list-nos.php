        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
					<div class="col-xl-8 col-lg-12">
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
							<div class="card dz-card" id="accordion-three">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
									<h4 class="card-title"><?= $title ?></h4>
									</div>
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
																<th>Nos Code</th>
																<th>Nos Title </th>
																<th>Status</th>
																<th>Created DateTime</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
														    <?php $serialNumber = 1; ?>
														    <?php foreach ($nos as $row) : ?>
															<tr>
															   <td><?= $serialNumber++ ?></td>
															   <td><?= $row->nos_code ?></td>
                                                                <td><?= $row->nos_title ?></td>
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
    																	<a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->nos_id; ?>"
    																	    data-nos_title="<?php echo $row->nos_title ?>" data-nos_code="<?php echo $row->nos_code ?>" data-status="<?php echo $row->status ?>"
    																	    onclick="viewEditDetails(<?php echo $row->nos_id; ?>);"> 
    																	    <i class="fas fa-pencil-alt"></i></a>
    																	<a href="<?php echo site_url('delete-nos/'. $row->nos_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
									<!-- /tab-content -->		
							   
							</div>
						</div>
					<div class="col-xl-4 col-lg-12"> 
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
                        <?php } ?> 
                                <div class="form-validation">
                                   <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-nos') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="nos_id" name="nos_id" value="0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">NOS Code</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="nos_code" id="nos_code" placeholder="NOS Code" required>
                                                <div class="invalid-feedback" id="err_nos_code">
                                                    NOS Code is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">NOS Title</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="nos_title" id="nos_title" placeholder="NOS Title" required>
                                                <div class="invalid-feedback">
                                                    NOS Title is required.
												</div>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Status</label>
                                                <span class="text-danger">*</span>
                                                 <select name="status" id="status" class="form-control" required>
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
                </div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
    <script>
    function viewEditDetails(nos_id) {
      var nos_title = $("#edit_"+nos_id).attr('data-nos_title');
      var nos_code = $("#edit_"+nos_id).attr('data-nos_code');
      var status = $("#edit_"+nos_id).attr('data-status');
      
     // console.log("nos_title "+nos_title);
      
      $("#nos_title").val(nos_title);
      $("#nos_code").val(nos_code);
      $("#nos_id").val(nos_id);
      
      $("#status").val(status);
      $("#btn_save").html('Update Record');
      $("#title").html('Update <?php echo $title ?>');
      
      $("html, body").animate({ scrollTop: 0 }, "slow");
    }

    $(document).ready(function() {
        $("#nos_title").on("input", function() {
            // Get the input value
            var inputValue = $(this).val();

            // Remove special characters using a regular expression
            var sanitizedValue = inputValue.replace(/[^a-zA-Z0-9\s_\-/]/g, '');
            
            // Update the input value
            $(this).val(sanitizedValue);
        });
        
        $("#nos_code").on("input", function() {
            var nos_id = $("#nos_id").val();
            var nos_code = $(this).val();
            
            $("#err_nos_code").html("Please enter code.");
            $("#err_nos_code").hide();

            // Remove special characters using a regular expression
            var sanitizedValue = nos_code.replace(/[^a-zA-Z0-9\s_\-/]/g, '');

            // Update the input value
            $(this).val(sanitizedValue);
            
            //console.log('nos_code '+nos_code);
            // Perform AJAX call to validate duplicate
            
            // AJAX request
                $.ajax({
                url: "<?php echo base_url('check-duplicate-nos-code'); ?>",
                method: 'post',
                data: { nos_id: nos_id,nos_code: nos_code },
                dataType: 'json',
                success: function(response){
                    //console.log('validate '+response.validate); 
                    if(response.validate == true) {
                        $("#err_nos_code").html(nos_code+" this code already exists!");
                        $("#err_nos_code").show();
                        $("#nos_code").val('');
                    }
                }
                });
        });
    });
  </script>     
