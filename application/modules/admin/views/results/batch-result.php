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
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
								<polyline points="9 11 12 14 22 4"></polyline>
								<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
							</svg> <strong>Success!</strong>
							<?php echo $this->session->flashdata('msg'); ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
						</div>
					<?php } 
					else if ($this->session->flashdata('error') != "") { ?>
						<div class="alert alert-danger alert-dismissible fade show">
							<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
								<polyline points="9 11 12 14 22 4"></polyline>
								<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
							</svg> <strong>Error!</strong>
							<?php echo $this->session->flashdata('error'); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
						</div>
					<?php } ?>
				</div>
				<div class="col-xl-12 col-lg-12 div_search_results">
					<div class="card dz-card" id="accordion-three">
						<div class="card-header flex-wrap d-flex justify-content-between">
							<div>
								<h4 class="card-title"><?= $title ?> for Batch - <span id="spn_batch_id"></span></h4> </div>
								<div class="bootstrap-badge"> 
								    <a href="javascript:void(0)" class="badge badge-rounded badge-primary">Total Marks : <span id="spn_total_marks"></span></a> 
								    <a href="javascript:void(0)" class="badge badge-rounded badge-secondary">Pass Percentage : <span id="spn_pass_percentage"></span>%</a>
									<a href="javascript:void(0)" class="badge badge-circle badge-outline-primary">Total : <span id="spn_total_students"></span></a> 
									<a href="javascript:void(0)" class="badge badge-circle badge-outline-success">Passed : <span id="spn_total_passed"></span></a> 
									<a href="javascript:void(0)" class="badge badge-circle badge-outline-danger">Failed : <span id="spn_total_failed"></span></a> 
									<a href="javascript:void(0)" class="badge badge-circle badge-outline-dark">Absent : <span id="spn_total_absent"></span></a> 
									<a href="javascript:void(0)" class="badge badge-info" onclick="viewBatchSummary('view_basic');"><i class="fa-solid fa-eye"></i> Batch Summary Basic</a> 
									<a href="javascript:void(0)" class="badge badge-info" onclick="viewBatchSummary('view_detailed');"><i class="fa-solid fa-eye"></i> Batch Summary Detailed</a> 
									<a href="javascript:void(0)" id="perSheet" class="badge badge-dark"><i class="fa-solid fa-file-excel"></i> Percentage Sheet</a> 
									<a href="javascript:void(0)" id="NOSResSheet" class="badge badge-dark"><i class="fa-solid fa-file-excel"></i> NOS Result Sheet</a> 
								</div>
						</div>
						<!-- /tab-content -->
						<div class="tab-content" id="myTabContent-2">
							<div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
								<div id="div_spin" style="display:none;"> <span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i>
												</span>&emsp;Processing ... </div>
								<div class="card-body pt-0">
									<div class="table-responsive">
										<input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
										<input name="batch_id" id="batch_id" type="hidden" value="<?php echo $tb_id; ?>">
										<input id="tb_id_encode" type="hidden" value="">
										<table id="serverSideDataTable" class="display table">
											<thead>
												<tr>
													<th>#</th>
													<th>Candidate ID </th>
													<th>Candidate Name</th>
													<th>MaxMarks<br/>Theory</th>
													<th>MaxMarks<br/>Practical</th>
													<th>Marks<br/>Theory</th>
													<th>Marks<br/>Practical</th>
													<th>MarksIn<br/>Percentage</th>
													<th>Result</th>
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
		</div>
	</div>
<!--**********************************
	Content body end
***********************************-->
<link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
<script>
	
$(document).ready(function() {
    var batch_id = $('#batch_id').val();
	var table = $('#serverSideDataTable').DataTable({
		// Processing indicator
		"processing": true,
		// DataTables server-side processing mode
		"serverSide": true,
		// Initial no order.
		"order": [],
		// Load data from an Ajax source
		"ajax": {
			"url": "<?php echo base_url('list-results-ajax'); ?>",
			"type": "POST",
			"data": {'batch_id': batch_id},
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

	// Handle success event
	table.on('xhr.dt', function (e, settings, response, xhr) {
		//console.log('response '+response.pass_percentage);
		$("#spn_total_marks").text(response.total_max_marks);
		$("#spn_pass_percentage").text(response.pass_percentage);
		$("#spn_total_students").text(response.total_students);
		$("#spn_total_passed").text(response.total_passed);
		$("#spn_total_failed").text(response.total_failed);
		$("#spn_total_absent").text(response.total_absent);
		$("#tb_id_encode").val(response.tb_id_encode);
		$("#spn_batch_id").text(response.batch_id);
		$('#btn_save').html('Get Results');
		$('#btn_save').attr('disabled',false);
	});
});

$('a#perSheet').click(function() {
	var batch_id = $("#batch_id").val();
	if(batch_id == "") {
		batch_id = 0;
		return false;
	}
	/*var sdms_enrollment_number = $("#sdms_enrollment_number option:selected").val();
	if(sdms_enrollment_number == "") {
		sdms_enrollment_number = 0;
	}*/
	window.location.replace("<?php echo base_url('download-percentage-sheet'); ?>/" + batch_id);
});
$('a#NOSResSheet').click(function() {
	var batch_id = $("#batch_id").val();
	if(batch_id == "") {
		batch_id = 0;
		return false;
	}
	/*var sdms_enrollment_number = $("#sdms_enrollment_number option:selected").val();
	if(sdms_enrollment_number == "") {
		sdms_enrollment_number = 0;
	}*/
	window.location.replace("<?php echo base_url('download-NOS-result-sheet'); ?>/" + batch_id);
});

function viewBatchSummary(type){
	var tb_id_encode = $("#tb_id_encode").val();
	var url = '<?php echo base_url(); ?>view-batch-result-summary/'+tb_id_encode+'/'+type;
    window.open(url, '_blank');
}
</script>