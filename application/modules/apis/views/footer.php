		
        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
               <p>Copyright © <a href="https://hemsen.in" target="_blank">HEMSEN EXIM LLP</a></p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->

		<!--**********************************
           Support ticket button start
        ***********************************-->
		
        <!--**********************************
           Support ticket button end
        ***********************************-->


	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="<?php echo base_url(); ?>vendor/global/global.min.js"></script>
	<script src="<?php echo base_url(); ?>vendor/chart.js/Chart.bundle.min.js"></script>
	<script src="<?php echo base_url(); ?>vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<!--<script src="<?php echo base_url(); ?>vendor/apexchart/apexchart.js"></script>-->
	
	<script src="<?php echo base_url(); ?>vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="<?php echo base_url(); ?>vendor/select2/js/select2.full.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/select2-init.js"></script>
	
	<!-- Dashboard 1 -->
	<!--<script src="<?php echo base_url(); ?>assets/admin/js/dashboard/dashboard-3.js"></script>-->
	
	
	
	<!-- tagify -->
	 
	<script src="<?php echo base_url(); ?>vendor/datatables/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo base_url(); ?>vendor/datatables/js/dataTables.buttons.min.js"></script>
	<script src="<?php echo base_url(); ?>vendor/datatables/js/buttons.html5.min.js"></script>
	<script src="<?php echo base_url(); ?>vendor/datatables/js/jszip.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/datatables.init.js"></script>
   
	<!-- Apex Chart -->
	
	<script src="<?php echo base_url(); ?>vendor/bootstrap-datetimepicker/js/moment.js"></script>
	<script src="<?php echo base_url(); ?>vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
	
	<!-- Daterangepicker -->
    <!-- momment js is must -->
    <!--<script src="<?php echo base_url(); ?>vendor/moment/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- clockpicker -->
    <!--<script src="<?php echo base_url(); ?>vendor/clockpicker/js/bootstrap-clockpicker.min.js"></script>
    <!-- asColorPicker -->
    <!--<script src="<?php echo base_url(); ?>vendor/jquery-asColor/jquery-asColor.min.js"></script>
    <script src="<?php echo base_url(); ?>vendor/jquery-asGradient/jquery-asGradient.min.js"></script>
    <script src="<?php echo base_url(); ?>vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js"></script>
    <!-- Material color picker -->
    <!--<script src="<?php echo base_url(); ?>vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <!-- pickdate -->
    <!--<script src="<?php echo base_url(); ?>vendor/pickadate/picker.js"></script>
    <script src="<?php echo base_url(); ?>vendor/pickadate/picker.time.js"></script>
    <script src="<?php echo base_url(); ?>vendor/pickadate/picker.date.js"></script>

    <!-- Daterangepicker -->
    <!--<script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/bs-daterange-picker-init.js"></script>
    <!-- Clockpicker init -->
    <!--<script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/clock-picker-init.js"></script>
    <!-- asColorPicker init -->
    <!--<script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/jquery-asColorPicker.init.js"></script>
    <!-- Material color picker init -->
    <!--<script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/material-date-picker-init.js"></script>
    <!-- Pickdate -->
    <!--<script src="<?php echo base_url(); ?>assets/admin/js/plugins-init/pickadate-init.js"></script>
	

	<!-- Vectormap -->
    <script src="<?php echo base_url(); ?>assets/admin/js/custom.js"></script>
	<script src="<?php echo base_url(); ?>assets/admin/js/deznav-init.js"></script>
	
   <script>
		(function () {
		  'use strict'

		  // Fetch all the forms we want to apply custom Bootstrap validation styles to
		  var forms = document.querySelectorAll('.needs-validation')

		  // Loop over them and prevent submission
		  Array.prototype.slice.call(forms)
			.forEach(function (form) {
			  form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
				  event.preventDefault()
				  event.stopPropagation()
				}

				form.classList.add('was-validated')
			  }, false)
			})
		})()
	</script>
	
	
	
</body>
</html>