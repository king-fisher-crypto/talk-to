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
								<span class="title"><?php echo $lang['SETTINGS'];?></span>
							</div>
							<div class="panel-content">
								<div>
									<form class="form-horizontal" method="post" action="data/update-settings">
										<?php settings_form();?>
									</form>
									<form class="form-horizontal" method="post" action="data/upload-logo" enctype="multipart/form-data">
										<fieldset>
										<hr><h3>Branding Settings</h3><hr>
											<div class="control-group">
												<label class="col-md-1 control-label" for="" style="margin-left:-40px;">Site Logo</label>
												<div class="controls col-lg-10">
													<div class="col-lg-2">
														<input id="" type="file" name="file" placeholder="" class="input-xlarge" value="">
														<input type="submit" placeholder="" class="btn btn-success" value="Upload">
													</div>
													<div class="col-lg-10">
														<?php $width = '350px'; site_logo($width);?>
														
													</div>
													<br>
													
												</div>
											</div>
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
								<span class="title"><?php echo $lang['INTEGRATION'];?></span>
							</div>
							<div class="panel-content">
								<div>
									<h3><?php echo $lang['STEP_1'];?></h3> 
									<code><pre>include('affiliate-pro/controller/affiliate-tracking.php);</pre></code>
									* File located in affiliate-pro/controller/affiliate-tracking by default.
									
									<h3><?php echo $lang['STEP_2'];?></h3> 
									<code><pre>$sale_amount = '21.98';<br>$product = 'My Product Description';<br>include('affiliate-pro/controller/record-sale.php);</pre></code>
									* File located in affiliate-pro/controller/record-sale by default
									
									<h3><?php echo $lang['STEP_3'];?></h3> 
									If you want to use PayPal IPN - You can use the sample PayPal IPN file located in affiliate-pro/controller/paypal-ipn.php for additional purchase confirmation security (optional)
									<code><pre>$sale_amount = '21.98';<br>$product = 'My Product Description';<br>include('affiliate-pro/controller/<b>ipn-</b>record-sale.php);</pre></code>
									
								</div>
								
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
	<?php 
	if(isset($_SESSION['action_saved'])){ echo 'swal("Cool!", "Your settings have been saved!", "success")';}
	unset($_SESSION['action_saved']);
	?>	
	</script>
	

	
</body>
</html>
