        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
						<div class="card dz-card">
							<div id="div_spin" style="display:none;">
								<span  class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
							</div>
							<div class="card-body p-0">
								<div class="table-responsive active-projects">
									<div class="tbl-caption">
										<h4 class="card-title"><?= $title ?></h4>
									</div>
									<input name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
									<table id="serverSideDataTable" class="display table">
										<thead>
											<tr>
												<th>#</th>
												<th>Batch Id</th>
												<th>Trade/QP Name</th>
												<th>Start Datetime</th>
												<th>End Datetime</th>
												<th>Candidates</th>
												<th>Assessor Name</th>
												<th>Assessment Type</th>
												<th>Result Processing Status</th>
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
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" id="batch_details">
        	<div class="modal-dialog modal-lg">
        		<div class="modal-content">
        			<div class="modal-header">
        				<h5 class="modal-title"><i class="fas fa-eye me-2"></i> Batch Details for <span id="spn_batch_id"></span> </h5>
        				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        			</div>
        			<div class="modal-body">
        				<div class="row mb-4">
        					<div class="col-md-6">
        						<h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i> Batch Information </h6>
        						<ul class="list-unstyled">
        							<li> <strong>
                                  <i class="fas fa-building me-1"></i> Assessment Agency: </strong> <span id="ag_id"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-sitemap me-1"></i> Scheme: </strong> <span id="scheme_id"></span></li>
        							<li> <strong>
                                  <i class="fas fa-layer-group me-1"></i> Subscheme: </strong> <span id="subscheme_id"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-industry me-1"></i> Sector Skill Council: </strong> <span id="spn_ssc_id"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-book me-1"></i> Trade/QP Name: </strong> <span id="spn_trade_id"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-user-friends me-1"></i> Training Partner: </strong> <span id="tp_id"></span></li>
        							<li> <strong>
                                  <i class="fas fa-building me-1"></i> Training Center: </strong> <span id="tc_id"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-id-card me-1"></i> Batch ID: </strong> <span id="batch_id"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-users me-1"></i> Students: </strong> <span id="tb_target"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-user me-1"></i> Center SPOC Name: </strong> <span id="spoc_name"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-phone me-1"></i> Center SPOC Mobile: </strong> <span id="spoc_mobile"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-user-check me-1"></i> Assessor: </strong> <span id="spn_assessor_id"></span> </li>
        						</ul>
        					</div>
        					<div class="col-md-6">
        						<h6 class="fw-bold">
                              <i class="fas fa-calendar-alt me-2"></i> Assessment Details
                            </h6>
        						<ul class="list-unstyled">
        							<li> <strong>
                                  <i class="fas fa-calendar-day me-1"></i> PDA Date: </strong> <span id="tb_assessment_date"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-clock me-1"></i> Start Date & Time: </strong> <span id="tb_start_date_time"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-clock me-1"></i> End Date & Time: </strong> <span id="tb_end_date_time"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-stopwatch me-1"></i> Exam Duration: </strong> <span id="exam_duration_mins"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-globe-asia me-1"></i> Regional Language: </strong> <span id="lid"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-pen me-1"></i> Assessment Type: </strong> <span id="tb_exam_type"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-list me-1"></i> Questions Pattern: </strong> <span id="qp_shuffling"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-camera me-1"></i> Student Snapshots: </strong> <span id="take_snapshots"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-id-card me-1"></i> Aadhar Verification: </strong> <span id="aadhar_verification"></span> </li>
        							<li> <strong>
                                  <i class="fas fa-tasks me-1"></i> Practical Activity: </strong> <span id="practical_answer_type"></span> (Duration:<span id="practicalactivity_duration_mins"></span>) </li>
        							<li> <strong>
                                  <i class="fas fa-microphone-alt me-1"></i> Viva: </strong> <span id="viva_answer_type"></span>(Duration:<span id="viva_duration_mins"></span>) </li>
        							<li> <strong>
                                  <i class="fas fa-tasks me-1"></i> Assessment Status: </strong> <span id="tb_assessment_status"></span> </li>
        						</ul>
        					</div>
        				</div>
        				<div class="row">
        					<div class="col-md-12">
        						<h6 class="fw-bold">
                              <i class="fas fa-clipboard-list me-2"></i> Exam Instructions
                            </h6>
        						<!-- Instructions for Theory, Practical Activity, Viva -->
        						<p>Instructions for Theory:-<br/><span id="theory_instructions"></span></p>
        						<p>Instructions for Practical Activity:-<br/><span id="practical_activity_instructions"></span></p>
        						<p>Instructions for Viva:-<br/><span id="viva_instructions"></span></p>
        					</div>
        				</div>
        			</div>
        			<div class="modal-footer">
        				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        			</div>
        		</div>
        	</div>
        </div>	
        <!--**********************************
            Content body end
        ***********************************-->
		<style>
		.details {
			word-wrap: break-word;
			overflow-wrap: break-word;
		}
		</style>	
		<link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
		<script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        <script>
		// Variable to store DataTable instance
		var dataTable;

		// Function to initialize or reload DataTable
		function initializeOrReloadDataTable() {
		// Check if DataTable is initialized
		if (typeof dataTable === 'undefined') {
			//console.log('undefined');
			// DataTable is not initialized, so initialize it
			// get the hash 
			var csrf_hash_name = $("input[name=csrf_hash_name]").val();
			
			dataTable = $('#serverSideDataTable').DataTable({
				// Processing indicator
				"processing": true,
				// DataTables server-side processing mode
				"serverSide": true,
				// Initial no order.
				"order": [],
				// Load data from an Ajax source
				"ajax": {
				"url": "<?php echo base_url('list-batches-ajax'); ?>",
				"type": "POST",
				"data": { 'type' : 'Completed','csrf_hash_name' : csrf_hash_name },
				},
				responsive: true,
				'dom': 'ZBfrltip',
				buttons: [
					{
						extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
						className: 'btn btn-sm border-0',
						title: 'Batches Inprocess', // Specify your custom file name here
						action: function (e, dt, node, config) {
							// Perform your AJAX call here
							$.ajax({
								"url": "<?php echo base_url('export-batches-completed/0/0/0/NULL/NULL/Completed'); ?>",
								type: 'POST',
								//data: { length: '-1', param2: 'value2' },
								success: function(response) {
									window.location.href = '<?php echo base_url('export-batches-completed'); ?>/0/0/0/NULL/NULL/Completed';
								},
								error: function(xhr, status, error) {
									// Handle error
								}
							});
						}
					}
				],
				//Set column definition initialisation properties
				//"columnDefs": [{ "targets": [0], "orderable": false },{ "className": "text-wrap", "targets": [2, 5] }],
				"columnDefs": [
					{
						"targets": [0],
						"orderable": false,
					},
					{
						"targets": [1],
						"width": "100px",
						"render": function (data, type, row) {
							return '<div style="white-space:normal;">' + data + '</div>';
						}
					},
					{
						"targets": [2],
						"width": "150px",
						"render": function (data, type, row) {
							return '<div style="white-space:normal;">' + data + '</div>'; 
						}
					},
					{
						"targets": [3],
						"width": "100px",
						"render": function (data, type, row) {
							return '<div style="white-space:normal;">' + data + '</div>';
						}
					},
					{
						"targets": [4],
						"width": "100px",
						"render": function (data, type, row) {
							return '<div style="white-space:normal;">' + data + '</div>';
						}
					},
					{
						"targets": [6],
						"width": "150px",
						"render": function (data, type, row) {
							return '<div style="white-space:normal;">' + data + '</div>';
						}
					}
				
				],
				"autoWidth": false,
				language: {
				paginate: {
					next: '<i class="fa-solid fa-angle-right"></i>',
					previous: '<i class="fa-solid fa-angle-left"></i>' 
				}
				}
				
			});
			} else {
			dataTable.ajax.reload(null, false);
			}
		}
		$(document).ready(function(){
			initializeOrReloadDataTable();
		});

		function processResult(tb_id,trade_id) {
			
			$("#spn_"+tb_id).text('Processing...');
			$("#btn_"+tb_id).removeClass('btn-danger');
			$("#btn_"+tb_id).addClass('btn-info');

			$.ajax({
			url: "<?php echo base_url('process-batch-result'); ?>",
			method: 'post',
			data: { tb_id: tb_id, trade_id: trade_id },
			dataType: 'json',
			success: function(response){
				//console.log('response '+response); 
				//console.log('type '+response.type); 

				if(response.type == 'success') {
					sweetAlert("Success", response.message, "success");
					initializeOrReloadDataTable();
				}
				else if(response.type == 'error') {
					sweetAlert("Oops...", response.message, "error");
					initializeOrReloadDataTable();
				}
			}
			});
		}
		
		function getBatchDetails(tb_id) {
    		$("#spin_"+tb_id).show();
    		
    		$("#ag_id").text($("#btn-"+tb_id).attr('data-ag_id'));
    		$("#scheme_id").text($("#btn-"+tb_id).attr('data-scheme_id'));
    		$("#subscheme_id").text($("#btn-"+tb_id).attr('data-subscheme_id'));
    		$("#spn_ssc_id").text($("#btn-"+tb_id).attr('data-ssc_id'));
    		$("#spn_trade_id").text($("#btn-"+tb_id).attr('data-trade_id'));
    		$("#tp_id").text($("#btn-"+tb_id).attr('data-tp_id'));
    		$("#tc_id").text($("#btn-"+tb_id).attr('data-tc_id'));
    		$("#batch_id").text($("#btn-"+tb_id).attr('data-batch_id'));
    		$("#tb_target").text($("#btn-"+tb_id).attr('data-tb_target'));
    		$("#spoc_name").text($("#btn-"+tb_id).attr('data-spoc_name'));
    		$("#spoc_mobile").text($("#btn-"+tb_id).attr('data-spoc_mobile'));
    		$("#spn_assessor_id").text($("#btn-"+tb_id).attr('data-assessor_id'));
    		$("#tb_id").text($("#btn-"+tb_id).attr('data-tb_id'));
    		$("#tb_assessment_date").text($("#btn-"+tb_id).attr('data-tb_assessment_date'));
    		$("#tb_start_date_time").text($("#btn-"+tb_id).attr('data-tb_start_date_time'));
    		$("#tb_end_date_time").text($("#btn-"+tb_id).attr('data-tb_end_date_time'));
    		$("#lid").text($("#btn-"+tb_id).attr('data-lid'));
    		$("#exam_duration_mins").text($("#btn-"+tb_id).attr('data-exam_duration_mins')+" Mins");
    		$("#tb_exam_type").text($("#btn-"+tb_id).attr('data-tb_exam_type'));
    		$("#qp_shuffling").text($("#btn-"+tb_id).attr('data-qp_shuffling'));
    		$("#take_snapshots").text($("#btn-"+tb_id).attr('data-take_snapshots'));
    		$("#aadhar_verification").text($("#btn-"+tb_id).attr('data-aadhar_verification'));
    		$("#practical_answer_type").text($("#btn-"+tb_id).attr('data-practical_answer_type'));
    		$("#practicalactivity_duration_mins").text($("#btn-"+tb_id).attr('data-practicalactivity_duration_mins')+" Mins");
    		$("#viva_answer_type").text($("#btn-"+tb_id).attr('data-viva_answer_type'));
    		$("#viva_duration_mins").text($("#btn-"+tb_id).attr('data-viva_duration_mins')+" Mins");
    		$("#tb_assessment_status").text($("#btn-"+tb_id).attr('data-tb_assessment_status'));
    		$("#theory_instructions").text($("#btn-"+tb_id).attr('data-theory_instructions'));
    		$("#practical_activity_instructions").text($("#btn-"+tb_id).attr('data-practical_activity_instructions'));
    		$("#viva_instructions").text($("#btn-"+tb_id).attr('data-viva_instructions'));
    		
            $("#spn_batch_id").text($("#btn-"+tb_id).attr('data-batch_id'));
    		
    		$("#batch_details").modal('show');
    
    		$("#spin_"+tb_id).hide();
    	} 
	
		</script>        