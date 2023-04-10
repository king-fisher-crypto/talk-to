<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
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
				<?php if($admin_user=='1'){ include('assets/comp/leads-stat-boxes.php');}else{ include('assets/comp/leads-stat-boxes-i.php');}?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['LEADS'];?></span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="users" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<?php if($admin_user=='1') { ?>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['NAME'];?></th>
											<th><?php echo $lang['EMAIL'];?></th>
											<th><?php echo $lang['PHONE'];?></th>
											<th><?php echo $lang['MESSAGE'];?></th>
											<th><?php echo $lang['NET_EARNINGS'];?></th>
											<th><?php echo $lang['CONVERSION'];?></th>
											<th><?php echo $lang['DATETIME'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
											<?php }else{ ?>
											<th><?php echo $lang['LEAD_ID'];?></th>
											<th><?php echo $lang['NET_EARNINGS'];?></th>
											<th><?php echo $lang['CONVERSION'];?></th>
											<th><?php echo $lang['DATETIME'];?></th>
											<?php } ?>
										</tr>
									</thead>

									<tbody>
										<?php if($admin_user=='1') { leads_table(); } else { my_leads_table($owner); }?>
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
		
	<?php 
	if(isset($_SESSION['action_deleted'])){ echo 'swal("Deleted", "This has been deleted as requested!", "success")';}
	unset($_SESSION['action_deleted']);
	?>
	</script>
	

	
</body>
</html>
