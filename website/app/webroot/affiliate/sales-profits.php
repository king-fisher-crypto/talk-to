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
				<?php include('assets/comp/sales-stat-boxes.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['SALES_AND_PROFITS'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" name="start_date" value="<?php echo $start_date;?>"> to <input type="date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="sales" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['PRODUCT'];?></th>
											<th><?php echo $lang['SALE_AMOUNT'];?></th>
											<th><?php echo $lang['COMISSION'];?></th>
											<th><?php echo $lang['NET_EARNINGS'];?></th>
											<th><?php echo 'Mode';?></th>
											<th><?php echo 'Cookie ID';?></th>
											<th><?php echo 'IP';?></th>
											<th><?php echo 'Nb paiements';?></th>
											<th><?php echo 'Nouveau client';?></th>
											<th><?php echo $lang['DATETIME'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
										</tr>
									</thead>

									<tbody>
										<?php sales_table($start_date, $end_date); ?>
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
	$(document).ready( function() {
    $('#sales').dataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
	})	
		
	<?php 
	if(isset($_SESSION['action_deleted'])){ echo 'swal("Deleted", "This has been deleted as requested!", "success")';}
	unset($_SESSION['action_deleted']);
	?>	
	</script>
	

	
</body>
</html>
