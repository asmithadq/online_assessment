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
                                     <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-district') ?>" enctype="multipart/form-data">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="dist_id" name="dist_id" value="0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">District Code</label>
                                                <input type="text" class="form-control" placeholder="District Code" name="dist_code" id="dist_code">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">District Name</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" placeholder="District Name" name="dist_name" id="dist_name" required>
                                                <div class="invalid-feedback">
													District Name is required.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">State</label>
                                                <span class="text-danger">*</span>
                                                <select class="form-control" name="state_id" id="state_id" required>
        											 <option value="">-Select-</option>
                                                    <?php foreach ($dropdown_data as $key => $value): ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                    <?php endforeach; ?>
    										    </select>
    										    <div class="invalid-feedback">
														State Name is required.
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

													<table id="tbldistricts" class="table">
														<thead>
															<tr>
																<th>#</th>
																<th>District Code</th>
																<th>District Name</th>
																<th>State</th>
																<th>Status</th>
																<th>Created Datetime</th>
																<th>Action</th>
															</tr>
														</thead>
															<tbody>
														     <?php $serialNumber = 1; ?>
														    <?php foreach ($districts as $row) : ?>
															<tr>
															    <td><?= $serialNumber++ ?></td>
															    <td><?= $row->dist_code ?></td>
                                                                <td><?= $row->dist_name ?></td>
                                                                <td><?= $row->state_name ?>(<?= $row->state_code ?>)</td>
                                                                <td>
                                                                    <?php if ($row->dist_status == 1) : ?>
                                                                        <span class="badge light badge-success border-0">Active</span>
                                                                    <?php else : ?>
                                                                        <span class="badge light badge-danger border-0">Inactive</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= date('d-m-Y H:i:s', strtotime($row->dist_created_dts)) ?></td>
																<td>
    																<div class="d-flex">
    																	<a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->dist_id; ?>"
    																	    data-dist_code="<?php echo $row->dist_code ?>" data-dist_name="<?php echo $row->dist_name ?>" data-state_id="<?php echo $row->state_id ?>" data-status="<?php echo $row->dist_status ?>"
    																	    onclick="viewEditDetails(<?php echo $row->dist_id; ?>);"> 
    																	    <i class="fas fa-pencil-alt"></i></a>
    																	<a href="<?php echo site_url('delete-district/'. $row->dist_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
                $('#tbldistricts').DataTable({
                    //dom: 'Bfrtip',
                    'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'Districts', // Specify your custom file name here
                            filename: function() {
                                // Custom filename function can be used for dynamic file names
                                return 'Districts -' + '<?php echo date('d-m-Y H:i:s') ?>';
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
    function viewEditDetails(dist_id) {
      var dist_code = $("#edit_"+dist_id).attr('data-dist_code');
      var dist_name = $("#edit_"+dist_id).attr('data-dist_name');
      var state_id = $("#edit_"+dist_id).attr('data-state_id');
      var status = $("#edit_"+dist_id).attr('data-status');
      
      //console.log("ssc_code "+ssc_code);
      
      $("#dist_id").val(dist_id);
      $("#dist_code").val(dist_code);
      $("#dist_name").val(dist_name);
      $("#state_id").val(state_id);
      
      $("#status").val(status);
      $("#btn_save").html('Update Record');
      $("#title").html('Update <?php echo $title ?>');
      
      $("html, body").animate({ scrollTop: 0 }, "slow");
    }
  </script>         
