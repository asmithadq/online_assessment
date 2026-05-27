    <style>  code{	  font-size:1.2rem;  }  </style>       <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="d-flex justify-content-between align-items-center mb-4">
						<h4 class="heading mb-0">&nbsp;</h4>
						<div class="d-flex align-items-center">
							<a href="<?php echo base_url(); ?>create-trade-nos" class="btn btn-primary btn-sm ms-2">+ Add Trade/QP</a>
						</div>
					</div>

                    <div class="col-xl-12 col-lg-12">
                        <div class="card dz-card" id="accordion-three">
                            <div class="card-header flex-wrap d-flex justify-content-between">
                                <div>
                                <h4 class="card-title"><?= $title ?></h4>
                                </div>
                            </div>
                            
                            <!-- /tab-content -->	
                            <div class="tab-content" id="myTabContent-2">
                                <div class="tab-pane fade show active" id="withoutSpace" role="tabpanel" aria-labelledby="home-tab-2">
                                    <div id="div_spin" style="display:none;">
                                        <span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>&emsp;Processing ...
                                        </div> 
                                    <div class="card-body pt-0">
                                        <div class="table-responsive">
                                            <form id="myForm" method="post">
                                                <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                <table id="serverSideDataTable" class="display table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Trade/QP Code </th>
                                                            <th>Trade/QP Name</th>
                                                            <th>Sector Skill Council</th>
                                                            <th>Total Marks</th>
                                                            <th>Pass Percentage</th>
                                                            <th>Number of NOS</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </form>	
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /tab-content -->		
                        </div>
                    </div>
						
                    <!-- Large modal -->
                    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalH5">
                                        <div id="nos_details"></div>
                                        <div id="total_nos"></div>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th><strong>#</strong></th>
                                                    <th><strong>Nos Code</strong></th>
                                                    <th><strong>Theory Marks</strong></th>
                                                    <th><strong>Practical Skill Marks</strong></th>
                                                    <th><strong>Practical Activity Marks</strong></th>
                                                    <th><strong>Viva Marks</strong></th>
                                                    <th><strong>Total Marks</strong></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="nosData">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Large modal -->
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
                        "url": "<?php echo base_url('list-trades-ajax'); ?>",
                        "type": "POST",
            			"data": { 'csrf_hash_name' : csrf_hash_name },
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
            });
            
            function getNosDetails(trade_id) {
                $("#spin_"+trade_id).show();
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // Value specified
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                
                // Perform AJAX call to validate duplicate
                // AJAX request
                $.ajax({
                    url: "<?php echo base_url('get-mapped-trade-nos'); ?>",
                    method: 'post',
                    data: { trade_id: trade_id,[csrfName]: csrfHash },
                    dataType: 'json',
                    success: function(response){
                      //console.log(response); 
                      // Update CSRF hash
                      $('.txt_csrfname').val(response.token);
                      $("#spin_"+trade_id).hide();
                      $("#nos_details").text('Details For: '+response.nos_details);
                      $("#total_nos").text('Total No. of NOS: '+response.total_nos);
                      $("#nosData").html(response.output);
                      $(".bd-example-modal-lg").modal('show');
                      
                    }
                });
            }    

        </script>       
