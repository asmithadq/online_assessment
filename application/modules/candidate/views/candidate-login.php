<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="pvrscpl.in:Assessment Management System Admin">
	<meta property="og:title" content="pvrscpl.in:Assessment Management System Admin">
	<meta property="og:description" content="pvrscpl.in:Assessment Management System Admin">
	<!--meta property="og:image" content="https:/yashadmin.dexignzone.com/xhtml/social-image.png"-->
	<meta name="format-detection" content="telephone=no">
	
	<!-- PAGE TITLE HERE -->
	<title>PVR SKill - Assesssment Management System - Admin</title>
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="<?php echo base_url() ; ?>assets/admin/images/favicon1.png">
	<link href="<?php echo base_url() ; ?>vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ; ?>assets/admin/css/style.css" rel="stylesheet">

</head>

<body class="vh-100">
	<div class="page-wraper">

		<!-- Content -->
		<div class="browse-job login-style3">
			<!-- Coming Soon -->
			<div class="bg-img-fix overflow-hidden" style="background:#fff url('<?php echo base_url() ; ?>assets/admin/images/background/bg6.jpg'); height: 100vh;">
				<div class="row gx-0">
					<div class="col-xl-4 col-lg-5 col-md-6 col-sm-12 vh-100 bg-white ">
						<div id="mCSB_1" class="mCustomScrollBox mCS-light mCSB_vertical mCSB_inside" style="max-height: 653px;" tabindex="0">
							<div id="mCSB_1_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
								<div class="login-form style-2">
									
									
									<div class="card-body">
										<div class="logo-header">
											<a href="<?php echo base_url(); ?>" class="logo"><img style="width:90px" src="<?php echo base_url(); ?>assets/admin/images/logo/logo.png" alt="" class="width-230 light-logo"></a>
										</div>
									
										<nav>
											<div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
												
										<div class="tab-content w-100" id="nav-tabContent">
										  <div class="tab-pane fade show active" id="nav-personal" role="tabpanel" aria-labelledby="nav-personal-tab">
											<form class="needs-validation" novalidate id="myForm" method="post" action="<?= site_url('candidate-validate-login') ?>" autocomplete="OFF">
                                        		<input class="txt_csrfname" name="<?php echo $this->security->get_csrf_token_name(); ?>" type="hidden" value="<?php echo $this->security->get_csrf_hash(); ?>">
												<h3 class="form-title m-t0">Login Information</h3>
												<div class="dz-separator-outer m-b5">
													<div class="dz-separator bg-primary style-liner"></div>
												</div>
												
													<?php 
													if ($this->session->flashdata('error') != "") { ?>
														<div class="alert alert-danger alert-dismissible fade show">															
															<?php echo $this->session->flashdata('error'); ?>
															<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"><span><i class="fa-solid fa-xmark"></i></span></button>
														</div>
													<?php } ?> 
												
												<p>Enter your enrollment number and your password. </p>
												
												<div class="form-group mb-3">
													<input type="text" name="enrollment_number" id="enrollment_number" class="form-control" placeholder="Enter Enrollment Number" required>
												</div>
												<div class="form-group mb-3">
													<input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
												</div>
												<div class="form-group text-left mb-5 forget-main">
													<input type="submit" name="submit" id="submit" class="btn btn-primary" value="Sign Me In">
												</div>
											</form>
												
										  </div>
										</div>
										
										</div>
									</nav>
									</div>
										<div class="card-footer">
											<div class=" bottom-footer clearfix m-t10 m-b20 row text-center">
											<div class="col-lg-12 text-center">
												<span> © Copyright by <span class="heart"></span>
												<a href="javascript:void(0);">PVR Skill </a> All rights reserved.</span> 
											</div>
										</div>
									</div>	
											
								</div>
							</div>
							<div id="mCSB_1_scrollbar_vertical" class="mCSB_scrollTools mCSB_1_scrollbar mCS-light mCSB_scrollTools_vertical" style="display: block;">
								<div class="mCSB_draggerContainer">
								<div id="mCSB_1_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 0px; display: block; height: 652px; max-height: 643px; top: 0px;">
								<div class="mCSB_dragger_bar" style="line-height: 0px;"></div><div class="mCSB_draggerRail"></div></div></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Full Blog Page Contant -->
		</div>
		<!-- Content END-->
	</div>

<!--**********************************
	Scripts
***********************************-->
<!-- Required vendors -->
<script src="<?php echo base_url() ; ?>vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ; ?>vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="<?php echo base_url() ; ?>assets/admin/js/deznav-init.js"></script>
<script src="<?php echo base_url() ; ?>assets/admin/js/custom.js"></script>

</body>
</html>