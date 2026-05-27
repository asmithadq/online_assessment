<!--**********************************
	Sidebar start
***********************************-->
<?php
$role_id = $this->session->userdata('role_id');
$user_permissions = $this->User_model->get_permissions($role_id);
$userModulePermissions = array();
$userPagePermissions = array();
// Get all user permissions
foreach ($user_permissions as $perm) {
	//echo "<br> module_name_slug ".$perm['module_name_slug'];
	$userModulePermissions[$perm['module_name_slug']] = $perm['module_name_slug'];
	$userPagePermissions[$perm['permission_name']] = $perm['permission_name'];
}
?>
<div class="deznav">
	<div class="deznav-scroll">
		<ul class="metismenu" id="menu">
			<li class="menu-title">YOUR COMPANY</li>
			<li><a href="<?php echo base_url(); ?>admin-dashboard" class="" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="url(#clip0_113_177)">
						<path d="M17 4H6C4.79111 4 4 4.7 4 6V18C4 19.3 4.79111 20 6 20H18C19.2 20 20 19.3 20 18V7.20711C20 7.0745 19.9473 6.94732 19.8536 6.85355L17 4ZM17 11H7V4H17V11Z" fill="#90959F"/>
						<path opacity="0.3" d="M14.5 4H12.5C12.2239 4 12 4.22386 12 4.5V8.5C12 8.77614 12.2239 9 12.5 9H14.5C14.7761 9 15 8.77614 15 8.5V4.5C15 4.22386 14.7761 4 14.5 4Z" fill="white"/>
						</g>
						<defs>
						<clipPath id="clip0_113_177">
						<rect width="24" height="24" fill="white"/>
						</clipPath>
						</defs>
				</svg>
				</div>	
					<span class="nav-text">Dashboard</span>
				</a>
			</li>
			<?php
			if (in_array('training_partners', $userModulePermissions)) { 
			?>
			<li><a href="<?php echo base_url(); ?>list-training-partners" class="" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.13478 20.7733V17.7156C9.13478 16.9351 9.77217 16.3023 10.5584 16.3023H13.4326C13.8102 16.3023 14.1723 16.4512 14.4393 16.7163C14.7063 16.9813 14.8563 17.3408 14.8563 17.7156V20.7733C14.8539 21.0978 14.9821 21.4099 15.2124 21.6402C15.4427 21.8705 15.756 22 16.0829 22H18.0438C18.9596 22.0024 19.8388 21.6428 20.4872 21.0008C21.1356 20.3588 21.5 19.487 21.5 18.5778V9.86686C21.5 9.13246 21.1721 8.43584 20.6046 7.96467L13.934 2.67587C12.7737 1.74856 11.1111 1.7785 9.98539 2.74698L3.46701 7.96467C2.87274 8.42195 2.51755 9.12064 2.5 9.86686V18.5689C2.5 20.4639 4.04738 22 5.95617 22H7.87229C8.55123 22 9.103 21.4562 9.10792 20.7822L9.13478 20.7733Z" fill="#90959F"></path>
					</svg>
				</div>	
					<span class="nav-text">Training Partners</span>
				</a>
			</li>
			<?php 
			}
			if (in_array('training_centers', $userModulePermissions)) { 
			?>	
			<li><a href="<?php echo base_url(); ?>list-training-centers" class="" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.13478 20.7733V17.7156C9.13478 16.9351 9.77217 16.3023 10.5584 16.3023H13.4326C13.8102 16.3023 14.1723 16.4512 14.4393 16.7163C14.7063 16.9813 14.8563 17.3408 14.8563 17.7156V20.7733C14.8539 21.0978 14.9821 21.4099 15.2124 21.6402C15.4427 21.8705 15.756 22 16.0829 22H18.0438C18.9596 22.0024 19.8388 21.6428 20.4872 21.0008C21.1356 20.3588 21.5 19.487 21.5 18.5778V9.86686C21.5 9.13246 21.1721 8.43584 20.6046 7.96467L13.934 2.67587C12.7737 1.74856 11.1111 1.7785 9.98539 2.74698L3.46701 7.96467C2.87274 8.42195 2.51755 9.12064 2.5 9.86686V18.5689C2.5 20.4639 4.04738 22 5.95617 22H7.87229C8.55123 22 9.103 21.4562 9.10792 20.7822L9.13478 20.7733Z" fill="#90959F"></path>
					</svg>
				</div>	
					<span class="nav-text">Training Centers</span>
				</a>
			</li>
			<?php 
			}
			if (in_array('assessors', $userModulePermissions)) { 
			?>
			<li><a href="<?php echo base_url(); ?>list-assessors" class="" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g opacity="0.5">
						<path d="M9.34933 14.8577C5.38553 14.8577 2 15.47 2 17.9174C2 20.3666 5.364 21 9.34933 21C13.3131 21 16.6987 20.3877 16.6987 17.9404C16.6987 15.4911 13.3347 14.8577 9.34933 14.8577Z" fill="white"/>
						<path opacity="0.4" d="M9.34935 12.5248C12.049 12.5248 14.2124 10.4062 14.2124 7.76241C14.2124 5.11865 12.049 3 9.34935 3C6.65072 3 4.48633 5.11865 4.48633 7.76241C4.48633 10.4062 6.65072 12.5248 9.34935 12.5248Z" fill="white"/>
						<path opacity="0.4" d="M16.1734 7.84876C16.1734 9.19508 15.7605 10.4513 15.0364 11.4948C14.9611 11.6022 15.0276 11.7468 15.1587 11.7698C15.3407 11.7996 15.5276 11.8178 15.7184 11.8216C17.6167 11.8705 19.3202 10.6736 19.7908 8.87119C20.4885 6.19677 18.4415 3.79544 15.8339 3.79544C15.5511 3.79544 15.2801 3.82419 15.0159 3.87689C14.9797 3.88456 14.9405 3.9018 14.921 3.93247C14.8955 3.97176 14.9141 4.02254 14.9395 4.05608C15.7233 5.13217 16.1734 6.44208 16.1734 7.84876Z" fill="white"/>
						<path d="M21.7791 15.1693C21.4318 14.444 20.5932 13.9466 19.3173 13.7023C18.7155 13.5586 17.0854 13.3545 15.5697 13.3832C15.5472 13.3861 15.5345 13.4014 15.5325 13.411C15.5296 13.4263 15.5365 13.4493 15.5658 13.4656C16.2664 13.8048 18.9738 15.2805 18.6333 18.3928C18.6187 18.5289 18.7292 18.6439 18.8672 18.6247C19.5335 18.5318 21.2478 18.1705 21.7791 17.0475C22.0737 16.4534 22.0737 15.7634 21.7791 15.1693Z" fill="white"/>
						</g>
					</svg>
				</div>	
					<span class="nav-text">Assessors</span>
				</a>
			</li>
			<?php 
			}
			if (in_array('batches_inprocess', $userModulePermissions) || in_array('batches_completed', $userModulePermissions) || in_array('batch_results', $userModulePermissions)) { 
			?>
			<li><a class="has-arrow " href="javascript:void(0);" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g opacity="0.5">
						<path opacity="0.4" d="M16.0755 2H19.4615C20.8637 2 22 3.14585 22 4.55996V7.97452C22 9.38864 20.8637 10.5345 19.4615 10.5345H16.0755C14.6732 10.5345 13.537 9.38864 13.537 7.97452V4.55996C13.537 3.14585 14.6732 2 16.0755 2Z" fill="white"></path>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="white"></path>
						</g>
					</svg>
				</div>	
				<span class="nav-text">Assessments</span>
				</a>
				<ul>
					<?php if (in_array('batches_inprocess', $userModulePermissions)) { ?>
					<li><a href="<?php echo base_url(); ?>list-batches-inprocess">Batches Inprocess</a></li>
					<?php } if (in_array('batches_completed', $userModulePermissions)) { ?>
					<li><a href="<?php echo base_url(); ?>list-batches-completed">Batches Completed</a></li>
					<?php } if (in_array('batch_results', $userModulePermissions)) { ?>
					<li><a href="<?php echo base_url(); ?>search-results/All">Results</a></li>
					<?php } ?>
				</ul>
			</li>
			<?php 
			}
			if (in_array('expenses', $userModulePermissions)) { 
			?>
			<li><a class="has-arrow " href="javascript:void(0);" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g opacity="0.5">
						<path opacity="0.4" d="M16.0755 2H19.4615C20.8637 2 22 3.14585 22 4.55996V7.97452C22 9.38864 20.8637 10.5345 19.4615 10.5345H16.0755C14.6732 10.5345 13.537 9.38864 13.537 7.97452V4.55996C13.537 3.14585 14.6732 2 16.0755 2Z" fill="white"></path>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="white"></path>
						</g>
					</svg>
				</div>	
				<span class="nav-text">Expenses</span>
				</a>
				<ul >
					
					<li><a href="<?php echo base_url(); ?>list-pending-expenses">Expenses Inprocess</a></li>
					<li><a href="<?php echo base_url(); ?>list-paid-rejected-expenses">Expenses Paid/Rejected</a></li>
					<!--<li><a href="<?php echo base_url(); ?>expense-report">Expense Report</a></li>--> 
				</ul>
			</li>
			<?php 
			}
			?>
			<!--<li><a href="<?php echo base_url(); ?>" class="" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="url(#clip0_113_177)">
						<path d="M17 4H6C4.79111 4 4 4.7 4 6V18C4 19.3 4.79111 20 6 20H18C19.2 20 20 19.3 20 18V7.20711C20 7.0745 19.9473 6.94732 19.8536 6.85355L17 4ZM17 11H7V4H17V11Z" fill="#90959F"/>
						<path opacity="0.3" d="M14.5 4H12.5C12.2239 4 12 4.22386 12 4.5V8.5C12 8.77614 12.2239 9 12.5 9H14.5C14.7761 9 15 8.77614 15 8.5V4.5C15 4.22386 14.7761 4 14.5 4Z" fill="white"/>
						</g>
						<defs>
						<clipPath id="clip0_113_177">
						<rect width="24" height="24" fill="white"/>
						</clipPath>
						</defs>
				</svg>
				</div>	
					<span class="nav-text">Reports</span>
				</a>
			</li>-->
			<?php 
			if (in_array('question_bank', $userModulePermissions)) { 
			?>
			<li><a class="has-arrow " href="javascript:void(0);" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g opacity="0.5">
						<path opacity="0.4" d="M2.00018 11.0785C2.05018 13.4165 2.19018 17.4155 2.21018 17.8565C2.28118 18.7995 2.64218 19.7525 3.20418 20.4245C3.98618 21.3675 4.94918 21.7885 6.29218 21.7885C8.14818 21.7985 10.1942 21.7985 12.1812 21.7985C14.1762 21.7985 16.1122 21.7985 17.7472 21.7885C19.0712 21.7885 20.0642 21.3565 20.8362 20.4245C21.3982 19.7525 21.7592 18.7895 21.8102 17.8565C21.8302 17.4855 21.9302 13.1445 21.9902 11.0785H2.00018Z" fill="white"></path>
						<path d="M11.2454 15.3842V16.6782C11.2454 17.0922 11.5814 17.4282 11.9954 17.4282C12.4094 17.4282 12.7454 17.0922 12.7454 16.6782V15.3842C12.7454 14.9702 12.4094 14.6342 11.9954 14.6342C11.5814 14.6342 11.2454 14.9702 11.2454 15.3842Z" fill="white"></path>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M10.2113 14.5564C10.1113 14.9194 9.7623 15.1514 9.38431 15.1014C6.8333 14.7454 4.39531 13.8404 2.33731 12.4814C2.12631 12.3434 2.00031 12.1074 2.00031 11.8554V8.3894C2.00031 6.2894 3.71231 4.5814 5.81731 4.5814H7.78431C7.97231 3.1294 9.20231 2.0004 10.7043 2.0004H13.2863C14.7873 2.0004 16.0183 3.1294 16.2063 4.5814H18.1833C20.2823 4.5814 21.9903 6.2894 21.9903 8.3894V11.8554C21.9903 12.1074 21.8633 12.3424 21.6543 12.4814C19.5923 13.8464 17.1443 14.7554 14.5763 15.1104C14.5413 15.1154 14.5073 15.1174 14.4733 15.1174C14.1343 15.1174 13.8313 14.8884 13.7463 14.5524C13.5443 13.7564 12.8213 13.1994 11.9903 13.1994C11.1483 13.1994 10.4333 13.7444 10.2113 14.5564ZM13.2863 3.5004H10.7043C10.0313 3.5004 9.46931 3.9604 9.30131 4.5814H14.6883C14.5203 3.9604 13.9583 3.5004 13.2863 3.5004Z" fill="white"></path>
						</g>
					</svg>
				</div>	
				<span class="nav-text">Question Bank</span>
				</a>
				<ul>
					<li><a href="<?php echo base_url(); ?>import-questionbank">Import Question Bank</a></li>
					<li><a href="<?php echo base_url(); ?>list-questions">View Question Bank</a></li>
				</ul>
			</li>
			<?php 
			}
			if (in_array('masters', $userModulePermissions)) { 
			?>
			<li><a class="has-arrow " href="javascript:void(0);" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g opacity="0.5">
						<path opacity="0.4" d="M16.0755 2H19.4615C20.8637 2 22 3.14585 22 4.55996V7.97452C22 9.38864 20.8637 10.5345 19.4615 10.5345H16.0755C14.6732 10.5345 13.537 9.38864 13.537 7.97452V4.55996C13.537 3.14585 14.6732 2 16.0755 2Z" fill="white"></path>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="white"></path>
						</g>
					</svg>
				</div>	
				<span class="nav-text">Masters</span>
				</a>
				<ul >
					<li><a href="<?php echo base_url(); ?>list-sectorskillcouncils">Sector Skill Councils</a></li>
					<li><a href="<?php echo base_url(); ?>list-trades">Trade/QP Name</a></li>
					<li><a href="<?php echo base_url(); ?>list-schemes">Schemes</a></li>
					<li><a href="<?php echo base_url(); ?>list-subschemes">Subschemes</a></li>
					<li><a href="<?php echo base_url(); ?>list-nos">NOS</a></li>
					<li><a href="<?php echo base_url(); ?>list-nsfq-levels">NSFQ Level</a></li>
					<li><a href="<?php echo base_url(); ?>list-versions">Trade/QP Versions</a></li>
					<li><a href="<?php echo base_url(); ?>list-languages">Languages</a></li>
					<li><a href="<?php echo base_url(); ?>list-states">States</a></li>
					<li><a href="<?php echo base_url(); ?>list-districts">Districts</a></li>
					<li><a href="<?php echo base_url(); ?>list-banks">Banks</a></li>
					<li><a href="<?php echo base_url(); ?>list-assessment-documents-master">Assessment Documents</a></li>
					<li><a href="<?php echo base_url(); ?>list-email-templates">Email Templates</a></li>
				</ul>
			</li>
			<?php 
			}
			if (in_array('user_management', $userModulePermissions)) { 
			?>
			<li><a class="has-arrow " href="javascript:void(0);" >
				<div class="menu-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g opacity="0.5">
						<path d="M9.34933 14.8577C5.38553 14.8577 2 15.47 2 17.9174C2 20.3666 5.364 21 9.34933 21C13.3131 21 16.6987 20.3877 16.6987 17.9404C16.6987 15.4911 13.3347 14.8577 9.34933 14.8577Z" fill="white"/>
						<path opacity="0.4" d="M9.34935 12.5248C12.049 12.5248 14.2124 10.4062 14.2124 7.76241C14.2124 5.11865 12.049 3 9.34935 3C6.65072 3 4.48633 5.11865 4.48633 7.76241C4.48633 10.4062 6.65072 12.5248 9.34935 12.5248Z" fill="white"/>
						<path opacity="0.4" d="M16.1734 7.84876C16.1734 9.19508 15.7605 10.4513 15.0364 11.4948C14.9611 11.6022 15.0276 11.7468 15.1587 11.7698C15.3407 11.7996 15.5276 11.8178 15.7184 11.8216C17.6167 11.8705 19.3202 10.6736 19.7908 8.87119C20.4885 6.19677 18.4415 3.79544 15.8339 3.79544C15.5511 3.79544 15.2801 3.82419 15.0159 3.87689C14.9797 3.88456 14.9405 3.9018 14.921 3.93247C14.8955 3.97176 14.9141 4.02254 14.9395 4.05608C15.7233 5.13217 16.1734 6.44208 16.1734 7.84876Z" fill="white"/>
						<path d="M21.7791 15.1693C21.4318 14.444 20.5932 13.9466 19.3173 13.7023C18.7155 13.5586 17.0854 13.3545 15.5697 13.3832C15.5472 13.3861 15.5345 13.4014 15.5325 13.411C15.5296 13.4263 15.5365 13.4493 15.5658 13.4656C16.2664 13.8048 18.9738 15.2805 18.6333 18.3928C18.6187 18.5289 18.7292 18.6439 18.8672 18.6247C19.5335 18.5318 21.2478 18.1705 21.7791 17.0475C22.0737 16.4534 22.0737 15.7634 21.7791 15.1693Z" fill="white"/>
						</g>
					</svg>

				</div>
				<span class="nav-text">User Management</span>
				</a>
				<ul>
					<li><a href="<?php echo base_url(); ?>list-users">Manage Users</a></li>
					<li><a href="<?php echo base_url(); ?>list-roles">Roles & Permissions</a></li>
					<li><a href="<?php echo base_url(); ?>list-modules">Modules</a></li>
				</ul>
			</li>
			<?php 
			}
			?>
			<li><a href="<?php echo base_url(); ?>change-password" class="" >
				<div class="menu-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g opacity="0.5">
						<path opacity="0.4" d="M10.0833 15.9579H3.50777C2.67555 15.9579 2 16.6217 2 17.4393C2 18.2558 2.67555 18.9206 3.50777 18.9206H10.0833C10.9155 18.9206 11.5911 18.2558 11.5911 17.4393C11.5911 16.6217 10.9155 15.9579 10.0833 15.9579Z" fill="white"></path>
						<path opacity="0.4" d="M22 6.37855C22 5.56202 21.3244 4.89832 20.4933 4.89832H13.9178C13.0856 4.89832 12.41 5.56202 12.41 6.37855C12.41 7.19617 13.0856 7.85988 13.9178 7.85988H20.4933C21.3244 7.85988 22 7.19617 22 6.37855Z" fill="white"></path>
						<path d="M8.87774 6.37856C8.87774 8.24523 7.33886 9.75821 5.43887 9.75821C3.53999 9.75821 2 8.24523 2 6.37856C2 4.51298 3.53999 3 5.43887 3C7.33886 3 8.87774 4.51298 8.87774 6.37856Z" fill="white"></path>
						<path d="M22 17.3992C22 19.2648 20.4611 20.7778 18.5611 20.7778C16.6622 20.7778 15.1223 19.2648 15.1223 17.3992C15.1223 15.5325 16.6622 14.0196 18.5611 14.0196C20.4611 14.0196 22 15.5325 22 17.3992Z" fill="white"></path>
					</g>
				</svg>
				</div>	
					<span class="nav-text">Change Password</span>
				</a>
			</li>
			<li><a href="<?php echo base_url(); ?>admin-logout" class="" >
				<div class="menu-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g opacity="0.5">
						<path opacity="0.4" d="M10.0833 15.9579H3.50777C2.67555 15.9579 2 16.6217 2 17.4393C2 18.2558 2.67555 18.9206 3.50777 18.9206H10.0833C10.9155 18.9206 11.5911 18.2558 11.5911 17.4393C11.5911 16.6217 10.9155 15.9579 10.0833 15.9579Z" fill="white"></path>
						<path opacity="0.4" d="M22 6.37855C22 5.56202 21.3244 4.89832 20.4933 4.89832H13.9178C13.0856 4.89832 12.41 5.56202 12.41 6.37855C12.41 7.19617 13.0856 7.85988 13.9178 7.85988H20.4933C21.3244 7.85988 22 7.19617 22 6.37855Z" fill="white"></path>
						<path d="M8.87774 6.37856C8.87774 8.24523 7.33886 9.75821 5.43887 9.75821C3.53999 9.75821 2 8.24523 2 6.37856C2 4.51298 3.53999 3 5.43887 3C7.33886 3 8.87774 4.51298 8.87774 6.37856Z" fill="white"></path>
						<path d="M22 17.3992C22 19.2648 20.4611 20.7778 18.5611 20.7778C16.6622 20.7778 15.1223 19.2648 15.1223 17.3992C15.1223 15.5325 16.6622 14.0196 18.5611 14.0196C20.4611 14.0196 22 15.5325 22 17.3992Z" fill="white"></path>
					</g>
				</svg>
				</div>	
					<span class="nav-text">Logout</span>
				</a>
			</li>
			
		</ul>
	</div>
</div>

<!--**********************************
	Sidebar end
***********************************-->