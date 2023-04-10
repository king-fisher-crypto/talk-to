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
								<span class="title"><?php echo $lang['FIXED_COMMISSIONS'];?></span>
							</div>
							<div class="panel-content">
								<div class="alert alert-info">
									<?php echo $lang['FIXED_COMMISSIONS_DESCRIPTION'];;?>
								</div>
								
								<form action="data/set-default-commission" method="post">
									Default Commission <input type="text" name="dc" value="<?php default_commission();?>" placeholder="10"/>%
									<input type="submit" name="submit" value="Set" class="btn btn-success" />
								</form>
								<hr>
								<strong>To override the default commission rate or other commission rates add the commission variable to your integration</strong>
								<code><pre>$sale_amount = '21.98';<br>$product = 'My Product Description';<br><strong>$commission = '10';</strong> //percentage of original sale<br>include('affiliate-pro/controller/record-sale.php);</pre></code>
								
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
		
		<?php 
		if(isset($_SESSION['action_saved'])){ echo 'swal("Awesome Work!", "Your changes have been applied!", "success")';}
		if(isset($_SESSION['action_deleted'])){ echo 'swal("Deleted", "This has been deleted as requested!", "success")';}
		unset($_SESSION['action_saved']);
		unset($_SESSION['action_deleted']);
		?>
		
	</script>
</body>
</html>
