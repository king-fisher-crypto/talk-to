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
				<?php include('assets/comp/referral-stat-boxes.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-6">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['TOP_REFERRING_URLS'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" name="start_date" value="<?php echo $start_date;?>"> to <input type="date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								
									<div id="status"></div>
									<table id="tru" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['LANDING_PAGE'];?></th>
										</tr>
									</thead>

									<tbody>
										<?php top_url_referral_table($start_date, $end_date); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->
					<div class="col-lg-6">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['TOP_REFERRING_AFFILIATES'];?></span>
								<div class="date-filter pull-right">
									<form method="post" action="data/set-filter">
										From <input type="date" name="start_date" value="<?php echo $start_date;?>"> to <input type="date" name="end_date" value="<?php echo $end_date;?>">
										<input type="hidden" name="redirect" value="../<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);?>">
										<input type="submit" class="btn btn-xs btn-primary" value="Filter">
									</form>
								</div>
							</div>
							<div class="panel-content">
								<table id="tra" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['REFERRED_VISITS'];?></th>
											<?php $cpc_on = cpc_on(); if($cpc_on=='1'){ echo '<td>'.$lang['CPC_EARNINGS'].'</td>';}?>
										</tr>
									</thead>

									<tbody>
										<?php top_referring_affiliates($start_date, $end_date); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->
	
					<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['ALL_REFERRAL_TRAFFIC'];?></span>
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
									<table id="art" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['AFFILIATE'];?></th>
											<th><?php echo $lang['IP_ADDRESS'];?></th>
											<th><?php echo $lang['BROWSER'];?></th>
											<th><?php echo $lang['OS'];?></th>
											<th><?php echo $lang['COUNTRY'];?></th>
											<th><?php echo $lang['DEVICE'];?></th>
											<th><?php echo $lang['LANDING_PAGE'];?></th>
											<?php $cpc_on = cpc_on(); if($cpc_on=='1'){ echo '<th>'.$lang['CPC_EARNINGS'].'</th>';}?>
											<th><?php echo $lang['DATETIME'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
										</tr>
									</thead>
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
    $('#tru').DataTable();
		$('#tra').DataTable();
	});	
	$(document).ready(function(){
		$('#art').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "data/referral-data.php"
		});
	});	
	</script>
	
</body>
</html>
