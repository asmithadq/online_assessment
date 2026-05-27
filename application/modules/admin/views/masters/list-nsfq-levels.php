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

													<table id="tblnsfqlevels" class="table">

														<thead>

															<tr>

																<th>#</th>

																<th>NSFQ Level</th>

																<th>Action</th>

															</tr>

														</thead>

														<tbody>

														    <?php $serialNumber = 1; ?>

														    <?php foreach ($nsfq_level as $row) : ?>

															<tr>

															   <td><?= $serialNumber++ ?></td>

                                                                <td><?= $row->nsfq_level ?></td>

																<td>

    																<div class="d-flex">

    																	<a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1" id="edit_<?php echo $row->nsfq_id; ?>"

    																	    data-nsfq_level="<?php echo $row->nsfq_level ?>" 

    																	    onclick="viewEditDetails(<?php echo $row->nsfq_id; ?>);"> 

    																	    <i class="fas fa-pencil-alt"></i></a>

    																	<a href="<?php echo site_url('delete-nsfqlevel/'. $row->nsfq_id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>

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

                                <form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('save-nsfqlevel') ?>" enctype="multipart/form-data">

                                        <input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">

                                        <input type="hidden" id="nsfq_id" name="nsfq_id" value="0">

                                        <div class="row">

                                            <div class="mb-3 col-md-6">

                                                <label class="form-label">NSFQ Level</label>

                                                <span class="text-danger">*</span>

                                                <input type="text" class="form-control" name="nsfq_level" id="nsfq_level" placeholder="NSFQ Level" required>

                                                <div class="invalid-feedback" id="err_nsfq_level">

													NSFQ Level is required.

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

        <!--**********************************

            Content body end

        ***********************************-->



<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>

    <script>
    $(document).ready(function(){
                $('#tblnsfqlevels').DataTable({
                    //dom: 'Bfrtip',
                    'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'NSFQ Levels', // Specify your custom file name here
                            filename: function() {
                                // Custom filename function can be used for dynamic file names
                                return 'NSFQ Levels -' + '<?php echo date('d-m-Y H:i:s') ?>';
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

        $("#nsfq_level").on("blur", function() {

            var nsfq_id = $("#nsfq_id").val();

            var nsfq_level = $.trim($(this).val());

            

            $("#err_nsfq_level").html("Please enter NSQF Level.");

            $("#nsfq_level").hide();



            // Remove special characters using a regular expression

            var sanitizedValue = nsfq_level.replace(/[^a-zA-Z0-9\s_\-/]/g, '');



            // Update the input value

            $(this).val(sanitizedValue);

            

            //console.log('nos_code '+nos_code);

            // Perform AJAX call to validate duplicate

            

            // AJAX request

            $.ajax({

                url: "<?php echo base_url('check-duplicate-nsfq_level'); ?>",

                method: 'post',

                data: { nsfq_id: nsfq_id ,nsfq_level: nsfq_level },

                dataType: 'json',

                success: function(response){

                    //console.log('validate '+response.validate); 

                    if(response.validate == true) {

                        $("#err_nsfq_level").html(nsfq_level+" this level already exists!");

                        $("#err_nsfq_level").show();

                        $("#nsfq_level").val('');

                    }

                }

            });

        });



        function viewEditDetails(nsfq_id) {

            var nsfq_level = $("#edit_"+nsfq_id).attr('data-nsfq_level');

            var status = $("#edit_"+nsfq_id).attr('data-status');

            

            //console.log("ssc_code "+ssc_code);

            

            $("#nsfq_id").val(nsfq_id);

            $("#nsfq_level").val(nsfq_level);

            

            $("#status").val(status);

            $("#btn_save").html('Update Record');

            $("#title").html('Update <?php echo $title ?>');

            

            $("html, body").animate({ scrollTop: 0 }, "slow");

        }

  </script>     