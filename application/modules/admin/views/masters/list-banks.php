        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
					<div class="col-xl-6 col-lg-12">
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
                            <div class="card dz-card">

							   <div class="card-body pt-0">
							       
							      	<div class="table-responsive active-projects">
												    <div class="tbl-caption">
                                                        <h4 class="heading mb-0"><?= $title ?></h4>
                                                    </div>
													<table id="tblbanks" class="table">
														<thead>
															<tr>
																<th>#</th>
																<th>Bank Name</th>
																<th>Branch</th>
																<th>Status</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
														    <?php $serialNumber = 1; ?>
														    <?php foreach ($banks as $row) : ?>
															<tr>
															   <td><?= $serialNumber++ ?></td>
                                                                <td><?= $row->bank_name ?></td>
                                                                <td><?= $row->branch ?></td>
                                                                <td>
                                                                    <?php if ($row->status == 1) : ?>
                                                                        <span class="badge light badge-success border-0">Active</span>
                                                                    <?php else : ?>
                                                                        <span class="badge light badge-danger border-0">Inactive</span>
                                                                    <?php endif; ?>
                                                                </td>
																	<td>
    																<div class="d-flex">
    																	<a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->bank_id; ?>"
    																	    data-bank_name="<?php echo $row->bank_name ?>" data-address="<?php echo $row->address ?>"  data-branch="<?php echo $row->branch ?>" data-status="<?php echo $row->status ?>"
    																	    onclick="viewEditDetails(<?php echo $row->bank_id; ?>);"> 
    																	    <i class="fas fa-pencil-alt"></i></a>
    																	<a href="<?php echo site_url('delete-bank/'. $row->bank_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
    																</div>													
    															</td>											
																</td>												
															</tr>
															<?php endforeach; ?>
														</tbody>
													</table>
												</div>

											</div>	

							</div>
							
						</div>
					<div class="col-xl-6 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add <?= $title ?></h4>
                            </div>
                            <?php 
                        if ($this->session->flashdata('msg') != "") { ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                                <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
                            </div>
                        <?php } ?> 
                            <div class="card-body">
                                <div class="form-validation">
                                    <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-bank') ?>" autocomplete="OFF">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="bank_id" name="bank_id" value="0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Bank Name</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" required>
                                                <div class="invalid-feedback">
													Bank Name is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Branch</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="branch" id="branch" placeholder="Branch" required>
                                                <div class="invalid-feedback">
												    Branch is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Address</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="address" id="address" placeholder="Address" required>
                                                <div class="invalid-feedback">
													Address is required.
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
    $(document).ready(function(){
                $('#tblbanks').DataTable({
                    //dom: 'Bfrtip',
                    'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'Banks', // Specify your custom file name here
                            filename: function() {
                                // Custom filename function can be used for dynamic file names
                                return 'Banks -' + '<?php echo date('d-m-Y H:i:s') ?>';
                            },
                            exportOptions: {
                                columns: [0, 1, 2, 3] // Include all columns except 3rd and 6th
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
    function viewEditDetails(bank_id) {
      var bank_name = $("#edit_"+bank_id).attr('data-bank_name');
      var branch = $("#edit_"+bank_id).attr('data-branch');
      var address = $("#edit_"+bank_id).attr('data-address');
      var status = $("#edit_"+bank_id).attr('data-status');
      
     // console.log("bank_name "+bank_name);
      
      $("#bank_name").val(bank_name);
      $("#branch").val(branch);
      $("#address").val(address);
      $("#bank_id").val(bank_id);
      
      $("#status").val(status);
      $("#btn_save").html('Update Record');
      $("#title").html('Update <?php echo $title ?>');
      
      $("html, body").animate({ scrollTop: 0 }, "slow");
    }
  </script>     

