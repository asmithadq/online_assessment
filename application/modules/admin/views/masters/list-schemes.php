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
                                        <table id="tblScheme" class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Scheme Name </th>
                                                    <th>Status</th>
                                                    <th>Created DateTime</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $serialNumber = 1; ?>
                                                <?php foreach ($schemes as $row) : ?>
                                                <tr>
                                                    <td><?= $serialNumber++ ?></td>
                                                    <td><?= $row->scheme_name ?></td>
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
                                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->scheme_id; ?>"
                                                                data-scheme_name="<?php echo $row->scheme_name ?>" data-status="<?php echo $row->status ?>"
                                                                onclick="viewEditDetails(<?php echo $row->scheme_id; ?>);"> 
                                                                <i class="fas fa-pencil-alt"></i></a>
                                                            <a href="<?php echo site_url('delete-scheme/'. $row->scheme_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
					<div class="col-xl-6 col-lg-12">
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
                                   <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-scheme') ?>" enctype="multipart/form-data" autocomplete="OFF">
                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" id="scheme_id" name="scheme_id" value="0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Scheme Name</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="scheme_name" id="scheme_name" placeholder="Scheme Name" required>
                                                <div class="invalid-feedback" id="err_scheme_name">
													Scheme Name is required.
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
                $('#tblScheme').DataTable({
                    //dom: 'Bfrtip',
                    'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'Scheme Master', // Specify your custom file name here
                            filename: function() {
                                // Custom filename function can be used for dynamic file names
                                return 'Scheme Master -' + '<?php echo date('d-m-Y H:i:s') ?>';
                            },
                            exportOptions: {
                                columns: [0, 1, 2] // Include all columns except 3rd and 6th
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
            $("#scheme_name").on("blur", function() {
                var scheme_id = $("#scheme_id").val();
                var scheme_name = $.trim($(this).val());
                
                $("#err_scheme_name").html("Please enter Scheme Name.");
                $("#err_scheme_name").hide();

                // Remove special characters using a regular expression
                var sanitizedValue = scheme_name.replace(/[^a-zA-Z0-9\s_\-/]/g, '');

                // Update the input value
                $(this).val(sanitizedValue);
                
                //console.log('nos_code '+nos_code);
                // Perform AJAX call to validate duplicate
                
                // AJAX request
                $.ajax({
                url: "<?php echo base_url('check-duplicate-scheme'); ?>",
                method: 'post',
                data: { scheme_id: scheme_id,scheme_name: scheme_name },
                dataType: 'json',
                success: function(response){
                    //console.log('validate '+response.validate); 
                    if(response.validate == true) {
                        $("#err_scheme_name").html(scheme_name+" this scheme already exists!");
                        $("#err_scheme_name").show();
                        $("#scheme_name").val('');
                    }
                }
                });
            });

            function viewEditDetails(scheme_id) {
                var scheme_name = $("#edit_"+scheme_id).attr('data-scheme_name');
                var status = $("#edit_"+scheme_id).attr('data-status');
                
                //console.log("ssc_code "+ssc_code);
                
                $("#scheme_id").val(scheme_id);
                $("#scheme_name").val(scheme_name);
                
                $("#status").val(status);
                $("#btn_save").html('Update Record');
                $("#title").html('Update <?php echo $title ?>');
                
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
    </script>     
