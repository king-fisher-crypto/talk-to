<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
//REDIRECT ADMIN
if($admin_user!='1'){header('Location: dashboard');}
include('assets/comp/header.php');
?>

<body>
	<!-- Start Top Navigation -->
	<?php include('assets/comp/top-nav.php');?>
    <!-- Start Main Wrapper --> 
   	<div id="wrapper">
		<!-- Side Wrapper -->
        <div id="side-wrapper">
            <ul class="side-nav">
                <?php include('assets/comp/side-nav.php');?>
			</ul>
        </div><!-- End Main Navigation --> 

        <!-- YOUR CONTENT GOES HERE -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['SALES_VOLUME_COMMISSIONS'];?></span>
							</div>
							<div class="panel-content">
								
								<form method="post" action="data/update-sv">
									<?php?>
									Enable <?php echo $lang['SALES_VOLUME_COMMISSIONS'];?> <input type="checkbox" name="sv_on" value="1" data-on-color="success" data-off-color="danger" 
										<?php $sv_on = sv_on(); if($sv_on=='1'){echo 'checked';}?>>
									<input type="submit" class="btn btn-success" value="Save Settings">
								</form>
								<hr>
								<div class="alert alert-info">
									<?php echo $lang['COMMISSION_DESCRIPTION'];?>
								</div>
								<form action="data/add-commission-level" method="post">
									Sales from <input type="text" name="sf" value="<?php highest_level();?>" placeholder="0" /> to <input type="text" name="st" value="<?php highest_level_plus();?>" placeholder="999.99"/>
									= <input type="text" name="c"  value="20" />% Commission
									<input type="submit" name="submit" value="Add Level" class="btn btn-primary" />
								</form>
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>
				
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['COMMISSION_LEVELS'];?></span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="users" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['SALES_FROM'];?></th>
											<th><?php echo $lang['SALES_TO'];?></th>
											<th><?php echo $lang['PERCENTAGE'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
										</tr>
									</thead>

									<tbody>
										<?php commission_table(); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>
					
            </div>
        </div>
        <!-- End Page Content -->

	</div><!-- End Main Wrapper  -->
   
  <?php include('assets/comp/footer.php');?>
	
	<script>
	$(document).ready(function() {
    $('#users').DataTable();
	} );
	$("[name='sv_on']").bootstrapSwitch();
		
		<?php 
		if(isset($_SESSION['action_saved'])){ echo 'swal("Awesome Work!", "Your changes have been applied!", "success")';}
		if(isset($_SESSION['action_deleted'])){ echo 'swal("Deleted", "This has been deleted as requested!", "success")';}
		unset($_SESSION['action_saved']);
		unset($_SESSION['action_deleted']);
		?>
		
	</script>
</body>
</html>
