<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 active-p">
                <div class="tab-content" id="pills-tabContent">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card dz-card" id="accordion-three">
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
                            <div class="card-body">
                                <div class="basic-form">
                                    <form class="needs-validation" novalidate id="myForm" method="post" action="">
                                        <input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <div class="row">
                                            <div class="mb-2 col-md-2">
                                                <label class="form-label">Assessor</label>
                                                <select class="form-control" name="assessor_id" id="assessor_id" required>
                                                    <option value="">Choose...</option>
                                                    <?php
                                                    foreach($arr_assessors as $row) {
                                                    ?>
                                                            <option value="<?php echo $row['assessor_id']; ?>">
                                                                <?php echo $row['assessor_name'].' ('.$row['assessor_code'].')'; ?>
                                                            </option>
                                                            <?php
                                                    }
                                                    ?>
                                                </select>											
                                            </div>
                                            <div class="mb-2 col-md-2">
                                                <label class="form-label">Start Date</label>
                                                <input class="form-control" type="date" name="start_date" id="start_date">									
                                            </div>
                                            <div class="mb-2 col-md-2">
                                                <label class="form-label">End Date</label>
                                                <input class="form-control" type="date" name="end_date" id="end_date">										
                                            </div>
                                            <div class="mt-4 col-md-2">
                                                <button type="button" name="submit" class="btn btn-primary ms-2" id="btn_save">Filter</button>								
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div> 
                        </div> 	
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
                                                <th>Batch Id </th>
                                                <th>Assessor</th>
                                                <!--<th>Travel,Food & Stay Expenses</th>
                                                <th>Other Expenses</th> 
                                                <th>Professional Charges</th>-->
                                                <th>Grand Total</th>
                                                <th>Advance Paid</th>
                                                <th>Amount Paid</th>
                                                <th>Balance Amount</th> 
                                                <th>Paid Date</th> 
                                                <th>Approval Status</th>
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
        
            <!-- Large modal -->
            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalH5">
                                <div id="expense_details"></div>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="frmExpense" method="post" action="<?= site_url('save-expense-status') ?>" autocomplete="OFF" enctype="multipart/form-data">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th><strong>#</strong></th>
                                                <th colspan="5" style="text-align:center;background-color:#ffeccc;"><strong>Travel Expenses</strong></th>
                                                <th colspan="5" style="text-align:center;background-color:#d3edf5;"><strong>Food & Stay Expenses</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong></strong></th>
                                                <th style="background-color:#ffeccc;"><strong>Travel Date</strong></th>
                                                <th style="background-color:#ffeccc;"><strong>Mode</strong></th>
                                                <th style="background-color:#ffeccc;"><strong>From</strong></th>
                                                <th style="background-color:#ffeccc;"><strong>To</strong></th>
                                                <th style="background-color:#ffeccc;"><strong>Amount</strong></th>
                                                <th style="background-color:#d3edf5;"><strong>Breakfast</strong></th>
                                                <th style="background-color:#d3edf5;"><strong>Lunch</strong></th>
                                                <th style="background-color:#d3edf5;"><strong>Dinner</strong></th>
                                                <th style="background-color:#d3edf5;"><strong>Hotel Stay</strong></th>
                                                <th style="background-color:#d3edf5;"><strong>Amount</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody id="expenseData">
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-5">
                                        <h6>Assessor Comments:</h6>
                                        <div id="assessorComments"></div>
                                            
                                        <input type="hidden" id="be_id" name="be_id">  
                                        <input type="hidden" id="batch_id" name="batch_id">     
                                        <input type="hidden" id="total_amount_due" name="total_amount_due"> 
                                        <input type="hidden" id="hdn_assessor_id" name="hdn_assessor_id">  
                                        <div>
                                            <label for="Expense Status">Admin Comments:</label>
                                            <textarea id="comments" name="comments" rows="2" cols="50" class="form-control"></textarea>
                                        </div> 
                                        <div>
                                            <label for="Expense Status">Expense Status:</label>
                                            <select id="expense_status" name="expense_status" class="form-control"> 
                                                <option value="">Select</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="fileUpload" >Amount:</label>
                                            <input type="number" id="total_amount_paid" name="total_amount_paid" class="form-control">
                                            <div class="invalid-feedback">
                                                This Field is required.
                                            </div> 
                                        </div>                    
                                        <div>
                                            <label for="fileUpload" >Upload Receipt:</label>
                                            <input type="file" id="paid_receipt_file" name="paid_receipt_file" class="form-control">
                                        </div><br>
                                        <div>
                                            <button type="submit" name="submit" class="btn btn-primary" id="btn_save_expense_status">Submit</button>								
                                        </div>
                                    </div>                        
                                    <div class="col-lg-4 col-sm-5 ms-auto table-margin" id="totalExpenseData"></div>
                                </div>  
                            </form>       
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
        "url": "<?php echo base_url('list-expenses-ajax'); ?>",
        "type": "POST",
        "data": { 'expense_status' : 'Paid-Rejected' },
    },
    responsive: true,
    'dom': 'ZBfrltip',
    buttons: [
        {
            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
            className: 'btn btn-sm border-0',
            title: 'Expense Report', // Specify your custom file name here
            filename: function() {
                // Custom filename function can be used for dynamic file names
                return 'Expense Report for Paid-Rejected -' + '<?php echo date('d-m-Y H:i:s') ?>';
            },
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10,11] // Include all columns except 3rd and 6th
            }
        }
    ],
    //Set column definition initialisation properties
    "columnDefs": [{ 
        "targets": [0,1],
        "orderable": false
    }],
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
  
$('#btn_save').click(function(e) {
	var assessor_id = $('#assessor_id').val();
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	
    $('#btn_save').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
    $('#btn_save').attr('disabled',true);

    //$("#myTabContent-2").show();
    $("#serverSideDataTable").dataTable().fnDestroy();
    var table = $('#serverSideDataTable').DataTable({
        // Processing indicator
        "processing": true,
        // DataTables server-side processing mode
        "serverSide": true,
        // Initial no order.
        "order": [],
        // Load data from an Ajax source
        "ajax": {
            "url": "<?php echo base_url('list-expenses-ajax'); ?>",
            "type": "POST",
            "data": {'assessor_id': assessor_id,'start_date': start_date,'end_date': end_date,'expense_status' : 'Paid-Rejected' },
        },
        responsive: true,
        'dom': 'ZBfrltip',
        buttons: [
        {
            extend: 'excel', text: '<i class="fa-solid fa-file-excel"></i> Export Report',
            className: 'btn btn-sm border-0',
            title: 'Expense Master', // Specify your custom file name here
            filename: function() {
                // Custom filename function can be used for dynamic file names
                return 'Expense Master -' + '<?php echo date('d-m-Y H:i:s') ?>';
            },
            exportOptions: {
                columns: [0, 1, 2] // Include all columns except 3rd and 6th
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

    // Handle success event
    table.on('xhr.dt', function (e, settings, response, xhr) { 
        $('#btn_save').html('Filter');
        $('#btn_save').attr('disabled',false);
    });
		
});

function getExpenseDetails(be_id) {
    // Perform AJAX call to validate duplicate
    // AJAX request
    $.ajax({
        url: "<?php echo base_url('get-expense-details'); ?>",
        method: 'post',
        data: { be_id: be_id },
        dataType: 'json',
        success: function(response){
            //console.log(response); 
            $("#expense_details").text('Details For: '+response.expense_details);
            $("#expenseData").html(response.output);
            $("#totalExpenseData").html(response.totalExpenseData);
            $("#assessorComments").html(response.assessorComments);
            $("#be_id").val(response.be_id);
            $("#batch_id").val(response.batch_id);
            $("#hdn_assessor_id").val(response.assessor_id);
            $("#total_amount_due").val(response.total_amount_due);
            $(".bd-example-modal-lg").modal('show');
            
        }
    });
}

$(document).ready(function() {
    $('#frmExpense').on('submit', function(event) {
        event.preventDefault();

        var total_amount_paid = $("#total_amount_paid").val();
        var expense_status = $("#expense_status option:selected").val();
        var comments = $("#comments").val();
        //alert('in submit '+total_amount_paid);
        if(expense_status == "") {
            sweetAlert("Oops...", "Please select the status !!", "error");
            event.preventDefault();
        }
        else if(total_amount_paid <= 0 && expense_status != 'Rejected') {
            sweetAlert("Oops...", "Please enter amount that is being paid !!", "error");
            event.preventDefault();
        }
        else if(expense_status == "Rejected" && comments == "") {
            sweetAlert("Oops...", "Please enter comments for rejection !!", "error");
            event.preventDefault();
        }
        else {
            $('#btn_save_expense_status').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Please Wait...');
            $('#btn_save_expense_status').attr('disabled',true);
            $.ajax({
                url: '<?= site_url("save-expense-status"); ?>', 
                method: 'POST',
                data: $(this).serialize(), // Serialize form data
                dataType: 'json',
                success: function(response) {
                    if(response.msg != "") {
                        $("#hdn_assessor_id").val(0);
                        $('#btn_save_expense_status').html('Submit');
                        $('#btn_save_expense_status').attr('disabled',false);
                        $(".bd-example-modal-lg").modal('hide'); 
                        sweetAlert("Success!", response.msg, "success");
                        $('#frmExpense').trigger('reset');
                        $('#btn_save').trigger('click');
                    }
                    else {
                        sweetAlert("Oops...", "Please retry !!", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    $('#response').html("An error occurred, please try again.");
                }
            });
        }
    });
}); 

$(document).ready(function() {
    function calculateTravelTotal() {
        let total = 0;
        
        // Calculate the total of all textboxes with class 'travel'
        $('.travel').each(function() {
            let value = parseFloat($(this).val()) || 0;
            total += value;
        });
        
        // Set the total in the td with id 'td_total_travel_expenses'
        $('#td_total_travel_expenses').text(total);

        // Optionally, you can also calculate the grand total
        calculateGrandTotal();
    }

    function calculateFoodStayExpensesTotal() {
        let daywiseTotals = {};
        let grandTotal = 0;

        // Loop through all textboxes with class 'food_stay_expenses' and calculate totals per ted_id
        $('.food_stay_expenses').each(function() {
            let ted_id = $(this).data('ted_id');
            let value = parseFloat($(this).val()) || 0;

            // Accumulate total per ted_id
            if (!daywiseTotals[ted_id]) {
                daywiseTotals[ted_id] = 0;
            }
            daywiseTotals[ted_id] += value;

            // Accumulate the total
            grandTotal += value;
        });

        // Assign each ted_id total to the corresponding textbox
        $.each(daywiseTotals, function(ted_id, total) {
            //console.log('ted_id '+ted_id+' daywise '+total);
            $('#td_daywise_food_stay_expenses_'+ted_id).text('INR '+total);
            $('#td_daywise_food_stay_expenses_'+ted_id).attr('data-daywise_food_stay_expenses',total);
        });

        // Set the total in the td with id 'td_total_food_stay_expenses'
        $('#td_total_food_stay_expenses').text(grandTotal);
        $('#td_total_food_stay_expenses').attr('data-total_food_stay_expenses',grandTotal); 

        // Optionally, calculate the grand total across all ted_ids
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        let totalAmountDue = 0;

        // Get the travel total
        let travelTotal = parseFloat($('#td_total_travel_expenses').text()) || 0;
        let foodStayExpensesTotal = parseFloat($('#td_total_food_stay_expenses').attr('data-total_food_stay_expenses')) || 0;

        // Assuming other sections have their totals in similar IDs
        let otherExpenses = parseFloat($('#td_other_expenses').attr('data-other_expenses')) || 0;
        let professionalCharges = parseFloat($('#professional_charges').val()) || 0;

        let advance_amount = parseFloat($('#td_advance_amount').attr('data-advance_amount')) || 0;

        // Calculate the grand total
        grandTotal = travelTotal + foodStayExpensesTotal + otherExpenses + professionalCharges;
        totalAmountDue = grandTotal - advance_amount;

        // Set the grand total in the appropriate td element
        $('#td_grand_total').text(grandTotal);
        $("#td_total_amount_due").text(totalAmountDue);
        $("#total_amount_due").val(totalAmountDue);
    }

    // Call calculateTravelTotal() on input of textboxes with class 'travel'
    $(document).on('input', '.travel', calculateTravelTotal);
    $(document).on('input', '.food_stay_expenses', calculateFoodStayExpensesTotal);
    $(document).on('input', '#professional_charges', calculateGrandTotal);

    // Initial calculation if needed
    //calculateTravelTotal();
    //calculateFoodStayExpensesTotal();
});

</script>   


