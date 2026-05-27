<style>
    .img-thumbnail {
	padding: 0.25rem;
	border: 1px solid #c0c0c0;
	/* Thin border */
	border-radius: 1.75rem;
	max-width: 100%;
}
</style>
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
							<a href="<?php echo base_url(); ?>create-training-partners" class="btn btn-primary btn-sm ms-2">+ Add Training Partners</a>
						</div>
                        </div>
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
                                <div class="card dz-card">
                                    <div id="div_spin" style="display:none;">
                                        <span  class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive active-projects">
                                            <div class="tbl-caption">
                                                <h4 class="card-title">Tranining Partners</h4>
                                            </div>
                                            <table id="serverSideDataTable" class="display table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Name (Code)</th>
                                                        <th>State</th>
                                                        <th>District</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
		<!-- Modal -->
		<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Training Partner Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <img id="logo" src="" alt="Training Partner Logo" class="img-responsive img-thumbnail" style="max-width: 150px; max-height: 150px;">
                    </div>
                    <div class="col-md-6 text-right">
                        <img id="contact_photo" src="" alt="SPOC Photo" class="img-responsive img-thumbnail" style="max-width: 150px; max-height: 150px;">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Training Partner Details</h6>
                        <ul class="list-unstyled">
                            <li><strong>Name:</strong> <span id="name"></span></li>
                            <li><strong>Training Partner Code:</strong> <span id="tp_code"></span></li>
                            <li><strong>Status:</strong> <span id="status"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-user"></i> SPOC Information</h6>
                        <ul class="list-unstyled">
                            <li><strong>Name:</strong> <span id="contact_first_name"></span>&nbsp;<span id="contact_middle_name"></span>&nbsp;<span id="contact_middle_name"></span></li>
                            <li><strong>Gender:</strong> <span id="contact_gender"></span></li>
                            <li><strong>Phone:</strong> <span id="contact_phone"></span></li>
                            <li><strong>Mobile:</strong> <span id="contact_mobile"></span></li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-address-card"></i> Contact Information</h6>
                        <ul class="list-unstyled">
                            <li><strong>Address:</strong>
							<span id="address"></span>, 
                            <span id="state"></span>,
							<span id="district"></span>-
							<span id="pincode"></span></li>
							
                            <li><strong>Email:</strong> <span id="email"></span></li>
                            <li><strong>Phone:</strong> <span id="phone"></span></li>
                            <li><strong>Mobile:</strong> <span id="mobile"></span></li>
                            <li><strong>Website:</strong> <span id="website"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-map-marked-alt"></i> Map Sector Skill Councils</h6>
                        <ul class="list-unstyled" id="mapped_ssc">
                            
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-university"></i> Bank Information</h6>
                        <ul class="list-unstyled">
                            <li><strong>Bank Name:</strong> <span id="bank_name"></span></li>
                            <li><strong>Branch:</strong> <span id="bank_branch"></span></li>
                            <li><strong>Account Number:</strong> <span id="bank_account_no"></span></li>
                        </ul>
                    </div>
                </div>
                <!--<div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <h6 class="mb-3"><i class="fas fa-camera"></i> Photo</h6>
                        <img id="contact_photo" src="" alt="SPOC Photo" class="img-responsive img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
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
                // get the hash 
                var csrf_hash_name = $("input[name=csrf_hash_name]").val();
                
                $('#serverSideDataTable').DataTable({
                    // Processing indicator
                    "processing": true,
                    // DataTables server-side processing mode
                    "serverSide": true,
                    // Initial no order.
                    "order": [],
                    // Load data from an Ajax source
                    "ajax": {
                        "url": "<?php echo base_url('list-training-partners-ajax'); ?>",
                        "type": "POST",
            			"data": { 'csrf_hash_name' : csrf_hash_name },
                    },
            		responsive: true,
            		'dom': 'ZBfrltip',
                    buttons: [
                        {
                            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
                            className: 'btn btn-sm border-0',
                            title: 'Training Partners Master', // Specify your custom file name here
                            filename: function() {
                                // Custom filename function can be used for dynamic file names
                                return 'Training Partners Master -' + '<?php echo date('d-m-Y H:i:s') ?>';
                            },
                            exportOptions: {
                                columns: [0, 1, 2,3,4,5] // Include all columns except 6th
                            }
                        }
                    ],
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
            });
			
		function getPartnerDetails(tp_id) {
		$("#spin_"+tp_id).show();
		
		var logo = '<?php echo base_url().$this->config->item('training_partner_images_path'); ?>'+$("#btn-"+tp_id).attr('data-logo');
		
		var contact_photo = '<?php echo base_url().$this->config->item('training_partner_images_path'); ?>'+$("#btn-"+tp_id).attr('data-contact_photo');
		
		$("#tp_code").text($("#btn-"+tp_id).attr('data-tp_code'));
		$("#name").text($("#btn-"+tp_id).attr('data-name'));
		//$("#address_1").text($("#btn-"+tp_id).attr('data-address_1'));
		//$("#address_2").text($("#btn-"+tp_id).attr('data-address_2'));
        $("#address").text($("#btn-"+tp_id).attr('data-address'));
		$("#state").text($("#btn-"+tp_id).attr('data-state'));
		$("#district").text($("#btn-"+tp_id).attr('data-district'));
		$("#pincode").text($("#btn-"+tp_id).attr('data-pincode'));
		$("#email").text($("#btn-"+tp_id).attr('data-email'));
		$("#phone").text($("#btn-"+tp_id).attr('data-phone'));
		$("#mobile").text($("#btn-"+tp_id).attr('data-mobile'));
		$("#website").html($("#btn-"+tp_id).attr('data-website'));
		$("#bank_name").html($("#btn-"+tp_id).attr('data-bank_name'));
		$("#bank_branch").html($("#btn-"+tp_id).attr('data-bank_branch'));
		$("#bank_branch").html($("#btn-"+tp_id).attr('data-bank_branch'));
		$("#bank_account_no").html($("#btn-"+tp_id).attr('data-bank_account_no'));
		$("#contact_first_name").html($("#btn-"+tp_id).attr('data-contact_first_name'));
		$("#contact_last_name").html($("#btn-"+tp_id).attr('data-contact_last_name'));
		$("#contact_middle_name").html($("#btn-"+tp_id).attr('data-contact_middle_name'));
		$("#contact_gender").html($("#btn-"+tp_id).attr('data-contact_gender'));
		$("#contact_phone").html($("#btn-"+tp_id).attr('data-contact_phone'));
		$("#contact_mobile").html($("#btn-"+tp_id).attr('data-contact_mobile'));
		$("#status").html($("#btn-"+tp_id).attr('data-status'));
        $("#mapped_ssc").html($("#btn-"+tp_id).attr('data-mapped_ssc'));
		
		$("#logo").attr("src", logo);
		$("#contact_photo").attr("src", contact_photo);
		
		$(".bd-example-modal-lg").modal('show');

		$("#spin_"+tp_id).hide();
	} 

        </script>


