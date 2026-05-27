        <!--**********************************
            Content body start
        ***********************************-->
		<form action="<?php echo base_url('download-ques'); ?>" method="post" name="frmListQuestions" id="frmListQuestions" enctype="multipart/form-data">
		<input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
	
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
                                <h4 class="card-title"><?= $title ?></h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                        <div class="row">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Trade/QP Name</label>
                                                <span class="text-danger">*</span>
                                                												
												<select class="form-control"  id="trade_id" name="trade_id" required>
													<option value="">Choose...</option>
													<?php
													if($trades != false) {
														foreach($trades as $row) {
														?>
															<option value="<?php echo $row['trade_id']; ?>" ><?php echo $row['trade_code'] . "-" . $row['trade_name']; ?></option>
														<?php
														}
													}	
													?>
												</select>
											
                                                 <div class="invalid-feedback">
													Trade/QP Code is required.
												</div>
                                            </div>
                                        </div>
                                        <div  id="dvMainSelNos" style="display:none;" class="row">
                                            <div class="mb-3">
                                            <label class="form-label">Select Nos</label> 
                                                <div id="dvSelNos" class="form-check">
                                                       
                                                </div>
                                            </div>
                                        </div>
									
                                        <a href="#" id="btnGetQuestions"  name="btnGetQuestions" class="btn btn-primary"><i class="fa fa-download"></i>&nbsp; Get Questions</a>
                                        &nbsp;
										
										<a href="#" type="submit" id="dwnld_ques" onclick="document.getElementById('frmListQuestions').submit();" class="btn btn-info"><i class="fa fa-download"></i>&nbsp; Download Questions</a>
										
										
                                    </form>
                                </div>
                            </div>
                            
                        </div>
					</div>
					
					
				<div id="dvDisplayTables" style="display:none;">	
					<div class="col-xl-12 col-lg-12">
						<div class="mb-3 col-md-6">
							<div class="bootstrap-badge">
								<span class="badge badge-pill badge-primary" id="ctTheory">&nbsp;</span>
								<span class="badge badge-pill badge-secondary" id="ctPracticalSkills">&nbsp;</span>
								<span class="badge badge-pill badge-success" id="ctPracticalActivity">&nbsp;</span>
								<span class="badge badge-pill badge-danger" id="ctViva">&nbsp;</span>
								<span class="badge badge-pill badge-info" id="ctLanguage">&nbsp;</span>
							</div>
						</div>
					</div>
					
					
					<div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
								<?php 
								if ($this->session->flashdata('msg_theory') != "") { ?>
									<div class="alert alert-success alert-dismissible fade show">
										<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
										<strong>Success!</strong> <?php echo $this->session->flashdata('msg_theory'); ?>
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
									</div>
								<?php }  ?>
                                <div class="card-body" id="accordion-three">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
									<h4 class="card-title">Theory and Practical Skill Questions</h4>
									</div>
								</div>
								
								
								<div class="tab-content" id="myTabContent-2">
										<div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
											<div id="div_spin" style="display:none;">
                            				  <span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
                            				 </div> 
											<div class="card-body pt-0">
												<div class="table-responsive">
												    <form id="myForm" method="post">
    												    <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
    													<table id="theoryAndPracticalSkillDataTable" class="display table">
    														<thead>
    															<tr>
																<th>S.No.</th>
																<th>NOS Code</th>
																<th>Question Type</th>
																<th>Question</th>
																<th>Option A</th>
																<th>Option B</th>
																<th>Option C</th>
																<th>Option D</th>
																<th>Correct Ans</th>
																<th>Marks</th>
																<th>Actions</th>
															</tr>
															</thead>
    													</table>
    												</form>	
												</div>
											</div>
										</div>
									</div>									
																			
					
                </div>
            </div>
        </div>
		</div>
		
		
		
		<div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-body" id="accordion-three">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
									<h4 class="card-title">Practical Activity Questions</h4>
									</div>
								</div>
								
							
									<div class="tab-content" id="myTabContent-2">
										<div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
											<div id="div_spin" style="display:none;">
                            				  <span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
                            				 </div> 
											<div class="card-body pt-0">
												<div class="table-responsive">
												    <form id="myForm" method="post">
    												    <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
    													<table id="practicalActivityDataTable" class="display table">
    														<thead>
    															<tr>														
																<th>S.No.</th>
																<th>NOS Code</th>
																<th>Question</th>
																<th>Type of Question</th>
																<th>Marks</th>
																<th>Actions</th>
															</tr>
															</thead>
    													</table>
    												</form>	
												</div>
											</div>
										</div>
									</div>
									
				</div>
            </div>
        </div>
		</div>
		
		
		
		<div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-body" id="accordion-three">
								<div class="card-header flex-wrap d-flex justify-content-between">
									<div>
									<h4 class="card-title">Viva Questions</h4>
									</div>
								</div>							
							
																
									<div class="tab-content" id="myTabContent-2">
										<div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
											<div id="div_spin" style="display:none;">
                            				  <span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
                            				 </div> 
											<div class="card-body pt-0">
												<div class="table-responsive">
												    <form id="myForm" method="post">
    												    <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
    													<table id="vivaDataTable" class="display table">
    														<thead>
    															<tr>														
																<th>S.No.</th>
																<th>NOS Code</th>
																<th>Question</th>
																<th>Type of Question</th>
																<th>Marks</th>
																<th>Actions</th>
															</tr>
															</thead>
    													</table>
    												</form>	
												</div>
											</div>
										</div>
									</div>
                               
											
					
                </div>
            </div>
        </div>
		</div>
		
		
		</div>
		</form>
        <!--**********************************
            Content body end
        ***********************************-->
        <!-- Font Awesome CSS -->
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css'>
		<link href="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/admin/js/jquery-3.3.1.min.js"></script>
        
 <script>
	
	$("#trade_id").change(function(e) 
	{
		e.preventDefault();
		var sel_trade_id = $('#trade_id').val();
		
		var formData = new FormData($("#frmListQuestions")[0]);
		
		formData.append( 'sel_trade_id', sel_trade_id );
	//	$('#dvMainSelNos').css("display","none");
	//	$('#dvSelNos').html("");
		
		$.ajax({
			url: "<?php echo base_url() . 'get-select-nos'; ?>",
			type: "POST",
			data: formData,
			dataType: "JSON",
			contentType: false,
			cache: false,
			processData:false,
			success: function(data)
			{
				$('#dvMainSelNos').css("display","block");
				 $('#dvSelNos').html(data);
			}
		});
	});
	
	$('#btnGetQuestions').click(function (e) 
	{	
		$("#dvDisplayTables").css("display","block");
			// get the hash 
		var csrf_hash_name = $("input[name=csrf_hash_name]").val();
		var sel_trade_id = $('#trade_id').val();
		//var select_nos = $('#chk_select_nos').val();
		
		var select_nos = [];
		$('input[name="chk_select_nos[]"]:checked').each(function() {
			select_nos.push($(this).val());
		});
		
		
		// getQuestionTypeCounts
		e.preventDefault();
		getQuestionTypeCounts();		

		// Get Theory And Practical Skill Questions				
		$("#theoryAndPracticalSkillDataTable").dataTable().fnDestroy();
		
			var table =  $('#theoryAndPracticalSkillDataTable').DataTable({
			// Processing indicator
			"processing": true,
			// DataTables server-side processing mode
			"serverSide": true,
			// Initial no order.
			"order": [],
			// Load data from an Ajax source
			"ajax": {
				"url": "<?php echo base_url('get-questions'); ?>",
				"type": "POST",
				"data": { 'type' : 'Theory','csrf_hash_name' : csrf_hash_name,'sel_trade_id' : sel_trade_id,'select_nos' : select_nos },
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

		//$('#na_datatable').DataTable().ajax.reload();
		$('#theoryAndPracticalSkillDataTable').DataTable()
		.ajax.url(
			"<?php echo base_url('get-questions'); ?>"
		)
		.load(); 
		
		// Get Practical Activity Questions				
		$("#practicalActivityDataTable").dataTable().fnDestroy();
		
			var table =  $('#practicalActivityDataTable').DataTable({
			// Processing indicator
			"processing": true,
			// DataTables server-side processing mode
			"serverSide": true,
			// Initial no order.
			"order": [],
			// Load data from an Ajax source
			"ajax": {
				"url": "<?php echo base_url('get-practical-activity-questions'); ?>",
				"type": "POST",
				"data": { 'type' : 'Theory','csrf_hash_name' : csrf_hash_name,'sel_trade_id' : sel_trade_id,'select_nos' : select_nos },
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

		//$('#na_datatable').DataTable().ajax.reload();
		$('#practicalActivityDataTable').DataTable()
		.ajax.url(
			"<?php echo base_url('get-practical-activity-questions'); ?>"
		)
		.load(); 
		
	
	
		// Get Viva Questions				
		$("#vivaDataTable").dataTable().fnDestroy();
		
			var table =  $('#vivaDataTable').DataTable({
			// Processing indicator
			"processing": true,
			// DataTables server-side processing mode
			"serverSide": true,
			// Initial no order.
			"order": [],
			// Load data from an Ajax source
			"ajax": {
				"url": "<?php echo base_url('get-viva-questions'); ?>",
				"type": "POST",
				"data": { 'type' : 'Theory','csrf_hash_name' : csrf_hash_name,'sel_trade_id' : sel_trade_id,'select_nos' : select_nos },
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

		//$('#na_datatable').DataTable().ajax.reload();
		$('#vivaDataTable').DataTable()
		.ajax.url(
			"<?php echo base_url('get-viva-questions'); ?>"
		)
		.load(); 
		
	});

	function getQuestionTypeCounts() {
		var formData = new FormData($("#frmListQuestions")[0]);
		var csrf_hash_name = $("input[name=csrf_hash_name]").val();
		var sel_trade_id = $('#trade_id').val();
		var select_nos = [];
		$('input[name="chk_select_nos[]"]:checked').each(function() {
			select_nos.push($(this).val());
		});
		
		formData.append( 'sel_trade_id', sel_trade_id );
		formData.append( 'select_nos', select_nos );
		$.ajax({
			url: "<?php echo base_url() . 'get-question-type-counts'; ?>",
			type: "POST",
			data: formData,
			dataType: "JSON",
			contentType: false,
			cache: false,
			processData:false,
			success: function(data)
			{
				//console.log(data['Language']);
				$('#ctTheory').html('Theory ('+data['Theory']+')');
				$('#ctPracticalSkills').html('Practical Skill ('+data['PracticalSkill']+')');
				$('#ctPracticalActivity').html('Practical Activity ('+data['PracticalActivity']+')');
				$('#ctViva').html('Viva ('+data['Viva']+')');
				if(data['Language'] != "") {
					$('#ctLanguage').html('Available Languages: '+data['Language']);
				}
				else {
					$('#ctLanguage').html('Available Languages: NA');
				}
			}
		});
	}

	function deleteQuestion(qid,type) {
		// Show SweetAlert confirmation box
		swal({
			title: 'Are you sure you want to delete this question?',
			text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
			showLoaderOnConfirm: true,
			preConfirm: function() {
				return new Promise(function(resolve) {
					$.ajax({
						url: "<?php echo base_url('delete-question'); ?>",
						method: 'post',
						data: { qid: qid,type: type },
						dataType: 'json',
						success: function(response){
							//console.log('validate '+response.validate); 
							if(response.delete == true) {
								sweetAlert("Success", "Question deleted successfully", "success")
								getQuestionTypeCounts();
								if(type == 1) { //Theory/PracticalSkill
									$('#theoryAndPracticalSkillDataTable').DataTable()
									.ajax.url(
										"<?php echo base_url('get-questions'); ?>"
									)
									.load();
								}
								else if(type == 2) { //PracticalActivity
									$('#practicalActivityDataTable').DataTable()
									.ajax.url(
										"<?php echo base_url('get-practical-activity-questions'); ?>"
									)
									.load(); 
								}
								else if(type == 3) { //Vive
									$('#vivaDataTable').DataTable()
									.ajax.url(
										"<?php echo base_url('get-viva-questions'); ?>"
									)
									.load(); 
								} 
							}
						}
					});
				});
			},
			allowOutsideClick: false     
		}); 
	}
	
	
	/* $("#dwnld_ques").on("click", function() 
	 {
		
		var formData = new FormData($('#frmListQuestions')[0]);
                    
                    $.ajax({
                        url: "<?php echo base_url('download-ques'); ?>",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response){
                            console.log('response '+response); 
                            //console.log('type '+response.type); 
                            // Update CSRF hash
                            $('#file').val('');
                        }
                    });
	 });*/
	

</script>       

	