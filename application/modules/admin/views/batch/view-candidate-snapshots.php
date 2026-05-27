<link href="<?php echo base_url(); ?>assets/admin/vendor/lightgallery/css/lightgallery.min.css" rel="stylesheet">
	<!--**********************************
		Content body start
	***********************************-->
	<div class="content-body">
	    <div class="container-fluid">
                <div class="row">
                    <div class="d-flex justify-content-between align-items-center mb-4">
					<h4 class="heading mb-0">&nbsp;</h4>
					<div class="d-flex align-items-center"> <a href="<?php echo base_url(); ?>view-batch-students/<?php echo $arr_student_list['tb_id']; ?>" class="btn btn-primary btn-sm ms-2"><< Candidate's List</a> </div>
				</div>
                    <div class="col-lg-12">
                        <div class="card">
							<div class="card-header">
								<h4 class="card-title">Snapshots for <?php echo $arr_student_list['student_name']; ?></h4>
							</div>
							<div class="card-body pb-1">
							    
							    <div id="lightgallery" class="row">
									<?php
									if($arr_snapshot_details != false) {
										$snapshot_image_path = base_url().$this->config->item('student_snapshot_photo_path');
										foreach($arr_snapshot_details as $details) {
											$snapshot_file = $snapshot_image_path.$details['snapshot_image'];
											$snapshot_thumbs_file = $snapshot_image_path.'thumbs/'.$details['snapshot_image'];
										?>
											<a href="<?php echo $snapshot_file; ?>" data-exthumbimage="<?php echo $snapshot_file; ?>" data-src="<?php echo $snapshot_file; ?>" class="lg-item col-lg-3 col-md-6 mb-4">
												<img src="<?php echo $snapshot_file; ?>" alt="" class="w-100 rounded">
											</a>
										<?php
										}
									}
									?>
								</div>
							</div>
                        </div>
                        <!-- /# card -->
                    </div>
                </div>
            </div>
	</div>
	<!--**********************************
		Content body end
	***********************************-->
