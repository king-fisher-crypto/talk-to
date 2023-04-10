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

        <div id="page-content-wrapper">
            <div class="container-fluid">

				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['CPC_COMMISSIONS'];?></span>
							</div>
							<div class="panel-content">
								<div class="alert alert-info">
									<?php echo $lang['CPC_DESCRIPTION'];?>
								</div>
								<form method="post" action="data/update-cpc">
									<?php?>
									Enable <?php echo $lang['CPC_COMMISSIONS'];?> <input type="checkbox" name="cpc_on" value="1" data-on-color="success" data-off-color="danger" 
										<?php $cpc_on = cpc_on(); if($cpc_on=='1'){echo 'checked';}?>> <br><br> 
									Earnings per Click (Unique Visitor) <input type="text" name="epc" value="<?php epc();?>" placeholder="0.0001">
									<input type="submit" class="btn btn-success" value="Save Settings">
								</form>
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>	
            </div>
        </div>
        <!-- End Page Content -->

	</div><!-- End Main Wrapper  -->
	
  <?php include('assets/comp/footer.php'); ?>
	
	<script>
	$(document).ready(function() {
    $('#users').DataTable();
	} );
		
	$("[name='cpc_on']").bootstrapSwitch();
		
		<?php 
		if(isset($_SESSION['action_saved'])){ echo 'swal("Awesome Work!", "Your changes have been applied!", "success")';}
		if(isset($_SESSION['action_deleted'])){ echo 'swal("Deleted", "This has been deleted as requested!", "success")';}
		unset($_SESSION['action_saved']);
		unset($_SESSION['action_deleted']);
		?>
		
	</script>
</body>
</html>
