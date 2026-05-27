        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="d-flex justify-content-between align-items-center mb-4">
						<h4 class="heading mb-0">&nbsp;</h4>
						<div class="d-flex align-items-center"> <a class="btn btn-primary btn-sm ms-2" href="<?php echo site_url('download-batch-assessment-documents/'. $tb_id).'/'.$batch_id; ?>"><i class="fas fa-download"></i>&nbsp;Download Batch Assessment Documents</a> </div>
					</div>
                    <div class="col-xl-12 col-lg-12">
					    <div class="card dz-card">

							   <div class="card-body pt-0">
							       
							      	<div class="table-responsive">
												    <div class="tbl-caption">
                                                        <h4 class="heading mb-0">View Uploaded Checklist Documents</h4>
                                                    </div>
													<table id="example" class="display table">
														<thead>
															<tr>
																<th>#</th>
																<th>Document Title</th>
																<th>Type</th>
																<th>Upload</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php $serialNumber = 1; ?>
														     <?php foreach ($arr_checklist_documents_master as $row) { 
                                                                    $document_file_uploaded = "Not Uploaded";
                                                                    $document_description = "Not Updated";

                                                                    if(array_key_exists($row['acdm_id'],$arr_checklist_uploaded_details)) {
                                                                        $document_file_uploaded = $arr_checklist_uploaded_details[$row['acdm_id']]['document_file_uploaded'];
                                                                        $document_description   = $arr_checklist_uploaded_details[$row['acdm_id']]['document_description'];
                                                                        $watermarking_error     = $arr_checklist_uploaded_details[$row['acdm_id']]['watermarking_error'];
                                                                    }
                                                                    
                                                                ?>
                                                                <tr>
                                                                    <td><?= $serialNumber++ ?></td>
                                                                    <td><?= $row['document_title']; ?></td>
                                                                    <td><?= $row['document_type']; ?></td>
                                                                    <td><?= $row['document_requirement'] ?></td>
                                                                    <td>
                                                                        <?php
                                                                        if($row['document_type'] == 'Text') {
                                                                            echo $document_description;
                                                                        }
                                                                        else {
                                                                            if($document_file_uploaded != 'Not Uploaded') {
                                                                                if($watermarking_error == 0) {
                                                                                ?>
                                                                                    <a href="<?php echo base_url().$this->config->item('assessors_checklist_documents_path').$document_file_uploaded; ?>" target="_blank">View <?php echo $row['document_type']; ?></a>
                                                                                <?php
                                                                                }
                                                                                else {
                                                                                    echo "Watermark Error";
                                                                                }
                                                                            ?>
                                                                                
                                                                            <?php
                                                                            }
                                                                            else {
                                                                                echo $document_file_uploaded;
                                                                            }
                                                                        }
                                                                        ?>                                                                        
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
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
         
 