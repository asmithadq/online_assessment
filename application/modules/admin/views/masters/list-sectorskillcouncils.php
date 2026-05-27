        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
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
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="title">Add <?= $title ?></h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-sectorskillcouncil') ?>" enctype="multipart/form-data">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="ssc_id" name="ssc_id" value="0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">SSC Code</label>
                                                <input type="text" class="form-control" name="ssc_code" id="ssc_code" required>
                                                <div class="invalid-feedback">
													Please enter code.
												</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Sector Skill Council Title</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="ssc_title" id="ssc_title" required>
                                                <div class="invalid-feedback">
													Please enter title.
												</div>
                                            </div>
                                             <div class="mb-3 col-md-6">
                                                <label class="form-label">Logo</label>
                                                <input class="form-control" type="file" id="ssc_logo" name="ssc_logo">
                                                <img class="rounded-circle" width="35" src="" alt="" style="display_none;" id="ssc_logo_display">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Status</label>
                                                <span class="text-danger">*</span>
                                                 <select class="form-control" name="status" id="status" required>
                                                     <option value="">-Select-</option>
        											<option value="1">Active</option>
        											<option value = "0">In-active</option>
    										    </select>
    										    <div class="invalid-feedback">
													Please select status.
												</div>
                                            </div>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary" id="btn_save">Add Record</button>
                                    </form>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="col-xl-12">
                        <div class="card dz-card">
							<div class="card-body p-0">
								<div class="table-responsive active-projects">
									<div class="tbl-caption">
										<h4 class="heading mb-0"><?= $title ?></h4>
									</div>
									<table id="tblssc" class="table">
										<thead>
                                            <tr>
                                                <th>#</th>
                                                <th>SSC Code </th>
                                                <th>Sector Skill Council</th>
                                                <th>Logo</th>
                                                <th>Status</th>
                                                <th>Created Datetime</th>
                                                <th>Action</th>
                                            </tr>
										</thead>
										<tbody>
                                            <?php $serialNumber = 1; ?>
                                                <?php foreach ($ssc_data as $row): ?>
                                                <tr>
                                                    <td><?= $serialNumber++ ?></td>
                                                    <td><span><?= $row->ssc_code ?></span></td>
                                                    <td><span><?= $row->ssc_title ?></span></td>
                                                    <td>
                                                        <?php
                                                        if($row->ssc_logo != "") {
                                                        ?>
                                                            <img class="rounded-circle" width="35" src="<?= base_url().$this->config->item('ssc_logo_path').$row->ssc_logo ?>" alt="">
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row->status == 1) : ?>
                                                            <span class="badge light badge-success border-0">Active</span>
                                                        <?php else : ?>
                                                            <span class="badge light badge-danger border-0">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span><?= date('d-m-Y H:i:s', strtotime($row->created_dts)) ?></span></td>
                                                    <td>
                                                    <div class="d-flex">
                                                        <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->ssc_id;  ?>"
                                                            data-ssc_code="<?php echo $row->ssc_code ?>" data-ssc_title="<?php echo $row->ssc_title ?>"
                                                            data-ssc_logo="<?php echo $row->ssc_logo ?>" data-status="<?php echo $row->status ?>"
                                                            onclick="viewEditDetails(<?php echo $row->ssc_id; ?>);"> 
                                                            <i class="fas fa-pencil-alt"></i></a>
                                                        <a href="<?php echo site_url('delete-sectorskillcouncil/'. $row->ssc_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
            $('#tblssc').DataTable({
                //dom: 'Bfrtip',
                'dom': 'ZBfrltip',
                buttons: [
                    {
                        extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                        className: 'btn btn-sm border-0',
                        title: 'Sector Skill Councils Master', // Specify your custom file name here
                        filename: function() {
                            // Custom filename function can be used for dynamic file names
                            return 'Sector Skill Councils Master -' + '<?php echo date('d-m-Y H:i:s') ?>';
                        },
                        exportOptions: {
                            columns: [0, 1, 2,4,5] // Include all columns except 3rd and 6th
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
    
    function viewEditDetails(ssc_id) {
      var ssc_code = $("#edit_"+ssc_id).attr('data-ssc_code');
      var ssc_title = $("#edit_"+ssc_id).attr('data-ssc_title');
      var ssc_logo = '<?php echo base_url().$this->config->item('ssc_logo_path') ?>'+$("#edit_"+ssc_id).attr('data-ssc_logo');
      var status = $("#edit_"+ssc_id).attr('data-status');
      
      //console.log("ssc_code "+ssc_code);
      
      $("#ssc_id").val(ssc_id);
      $("#ssc_code").val(ssc_code);
      $("#ssc_title").val(ssc_title);
      if(ssc_logo != "") {
        $("#ssc_logo_display").show;  
        $("#ssc_logo_display").prop("src", ssc_logo);
      }
      else {
        $("#ssc_logo_display").hide;  
        $("#ssc_logo_display").prop("src", "");  
      }
      $("#status").val(status);
      $("#btn_save").html('Update Record');
      $("#title").html('Update <?php echo $title ?>');
      
      $("html, body").animate({ scrollTop: 0 }, "slow");
    }
  </script>        
