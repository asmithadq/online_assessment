<div class="content-body">
            <!-- row -->
			<div class="container-fluid">
                <?php 
                if ($this->session->flashdata('msg') != "") { ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                        
                        </button>
                    </div>
                <?php }  
                else if ($this->session->flashdata('error') != "") { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                        </button>
                    </div>
                <?php } ?>
				<div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php
                    //$student_photo_path = base_url().$this->config->item('student_photo_path');
                    if((array_key_exists('student_photo',$arrCandidateDetails)) && $arrCandidateDetails['student_photo'] != "") {
                        
                    ?>
                           <img src="<?php echo $arrCandidateDetails['student_photo']; ?>" width="100" class="img-fluid rounded-circle" alt="Profile Image">
                    <?php
                    }
                    ?>
                    
                    <h3 class="mt-3 mb-0"><?php echo (array_key_exists('student_name',$arrCandidateDetails)) ? $arrCandidateDetails['student_name'] : ""; ?></h3>
                    <div class="patient-details mt-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="rounded p-3 bg-light">
                                    <h4 class="mb-0"><?php echo (array_key_exists('gender',$arrCandidateDetails)) ? $arrCandidateDetails['gender'] : ""; ?></h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="rounded p-3 bg-light">
                                    <h4 class="mb-0">Date of Birth: <?php echo (array_key_exists('dob',$arrCandidateDetails) && $arrCandidateDetails['dob'] != '00-00-0000') ? date('d-m-Y',strtotime($arrCandidateDetails['dob'])) : ""; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-danger btn-lg btn-block" onclick="window.location.href='<?php echo base_url(); ?>candidate-profile'"><i class="fa-solid fa-user me-2"></i>Update Profile</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Assessment Details</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Exam Instructions</h5>
                   <p><?php echo (array_key_exists('theory_instructions',$arrBatchDetails)) ? $arrBatchDetails['theory_instructions'] : ""; ?></p>
                    <hr>
                    <h5 class="card-title">Exam Details</h5>
                    <p><strong>Job Role/QP Name:</strong> <?php echo (array_key_exists('trade_name',$arrBatchDetails)) ? $arrBatchDetails['trade_name'] : ""; ?></p>
                    <p><strong>Exam Date and Time:</strong> <?php echo (array_key_exists('assessment_date',$arrBatchDetails)) ? $arrBatchDetails['assessment_date'] : ""; ?></p>
                    <p><strong>Duration:</strong> <?php echo (array_key_exists('exam_duration_mins',$arrBatchDetails)) ? $arrBatchDetails['exam_duration_mins'] : ""; ?></p>
					<p><strong>Assessment Status:</strong> 
                    <?php 
                        $student_assessment_status = (array_key_exists('student_assessment_status',$arrBatchDetails)) ? $arrBatchDetails['student_assessment_status'] : "";
                        if($student_assessment_status == 'Practical Activity') {
                        ?>    
                            <span class="badge light badge-info"><i class="fa fa-circle text-info me-1"></i><?php echo $student_assessment_status; ?></span>
                        <?php 
                        }
                        else if($student_assessment_status == 'Viva') {
                        ?>    
                            <span class="badge light badge-primary"><i class="fa fa-circle text-primary me-1"></i><?php echo $student_assessment_status; ?></span>
                        <?php 
                        }
                        else {
                            echo $student_assessment_status;
                        }
                    ?>
                    </p>
                </div>
                <div class="card-footer">
                    <?php
                    $assessmentStatus = (array_key_exists('assessment_status',$arrBatchDetails)) ? $arrBatchDetails['assessment_status'] : "";
                    
                    //echo "<br> assessmentStatus ".$assessmentStatus;
                    //echo "<br> student_assessment_status ".$student_assessment_status;
                    
                    if($assessmentStatus == "Assessment Not Yet Started") {
                    ?>
                        <button type="button" class="btn btn-light"><i class="fa-solid fa-lock me-2"></i><?php echo $assessmentStatus; ?></button>
                    <?php
                    }
                    else if($assessmentStatus == "Assessment Completed" || $assessmentStatus == "Assessment Link Expired") {
                    ?>
                        <button type="button" class="btn btn-success"><i class="fa-solid fa-gear me-2"></i><?php echo $assessmentStatus; ?></button>
                    <?php
                    }
                    else if($assessmentStatus == "Take Assessment") {
                    ?>
                        <button type="button" class="btn btn-primary" onclick="window.location.href='<?php echo base_url(); ?>assessment-page'"><i class="fa-solid fa-table-cells-large me-2"></i><?php echo $assessmentStatus; ?></button>
                    <?php
                    }
                    ?>
				</div>
            </div>
        </div>
    </div>
            </div>
        </div>
