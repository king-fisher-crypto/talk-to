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
				<?php if($admin_user=='1'){ include('assets/comp/stat-boxes.php');}else{ include('assets/comp/my-stat-boxes.php');}?>
				<div class="row">
					<!-- Start Panel -->
					<div class="col-lg-5">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php if($admin_user=='1') { echo $lang['TOP_AFFILIATES']; }else { echo $lang['RECENT_TRAFFIC'].' <span class="small">('.$DOMAIN.'?ref='.$owner.')</span>'; }?></span>
							</div>
							<div class="panel-content">
								
									<div id="status"></div>
									<table id="top-affiliates" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<?php if($admin_user=='1'){ ?>
											<th><?php echo $lang['NAME'];?></th>
											<th><?php echo $lang['TOTAL_REFERRED'];?></th>
											<th><?php echo $lang['TOTAL_SALES'];?></th>
											<th><?php echo $lang['CONVERSION_RATE'];?></th>
											<?php }else { ?>
											<th><?php echo $lang['IP_ADDRESS'];?></th>
											<th><?php echo $lang['LANDING_PAGE'];?></th>
											<?php $cpc_on = cpc_on(); if($cpc_on=='1'){ echo '<th>'.$lang['CPC_EARNINGS'].'</th>';}?>
											<th><?php echo $lang['DATETIME'];?></th>
											<?php } ?>
										</tr>
									</thead>

									<tbody>
										<?php if($admin_user=='1') { top_affiliates_table(); } else { recent_referrals($owner); }?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- End Panel -->
					<!-- Start Panel -->
					<div class="col-lg-4">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['REFERRAL_TRAFFIC'];?></span>
							</div>
							<div class="panel-content">
								<label for="recent_traffic" style="width:100%;" >Recent Traffic History<br />
									<canvas id="recent_traffic" style="width:100%;" height="205"></canvas>
								</label>
							</div>
						</div>
					</div>
					<!-- End Panel -->
					<!-- Start Panel -->
					<div class="col-lg-3">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title"><?php echo $lang['TOP_COUNTRY'];?></span>
							</div>
							<div class="panel-content">
								<ul class="top-countries">
									<?php if($admin_user=='1') {top_country_ts_table();}else{top_country_ts_table($owner);}?>
								</ul>
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-8">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['MONTHLY_REFERRALS_SALES'];?></span>
								<div class="pull-right mr">
									<a href="<?php if($admin_user=='1'){echo 'referral-traffic';}else{echo 'my-traffic';}?>" class="btn btn-sm btn-primary"><?php echo $lang['VIEW_ALL_TRAFFIC'];?></a>
									<a href="<?php if($admin_user=='1'){echo 'sales-profits';}else{echo 'my-sales';}?>" class="btn btn-sm btn-warning"><?php echo $lang['VIEW_ALL_SALES'];?></a>
								</div>
							</div>
							<div class="panel-content">
								<div style="width:99%;">
									<canvas id="lc" height="77"></canvas>
								</div>
							</div>
						</div>
					</div>
					<!-- End Panel -->
					<!-- Start Panel -->
					<div class="col-lg-4">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['DEVICE_STATISTICS'];?></span>
								<div class="pull-right mr">
									<a href="<?php if($admin_user=='1'){echo 'referral-traffic';}else{echo 'my-sales';}?>" class="btn btn-sm btn-primary"><?php echo $lang['VIEW_ALL_TRAFFIC'];?></a>
								</div>
							</div>
							<div class="panel-content">
								<div class="row">
									<div class="col-lg-3">
										<div class="desktop-legend"></div> <?php echo $lang['DESKTOP'];?><br>
										<div class="phone-legend"></div> <?php echo $lang['PHONE'];?> <br>
										<div class="tablet-legend"></div> <?php echo $lang['TABLET'];?>
									</div>
									<div class="col-lg-9">
										<canvas id="pa" style="width:100%;" height="190"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['RECENT_SALES'];?></span>
								<div class="pull-right mr">
									<a href="<?php if($admin_user=='1'){echo 'sales-profits';}else{echo 'my-sales';}?>" class="btn btn-sm btn-warning">View all Sales</a>
								</div>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="sales" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<?php if($admin_user=='1'){ ?><th><?php echo $lang['AFFILIATE'];?></th><?php } ?>
											<th><?php echo $lang['PRODUCT'];?></th>
											<th><?php echo $lang['SALE_AMOUNT'];?></th>
											<th><?php echo $lang['COMISSION'];?></th>
											<th><?php echo $lang['NET_EARNINGS'];?></th>
											<?php $rc_on = rc_on(); if($rc_on=='1'){ echo '<th>'.$lang['RECURRING'].'</th>';}?>
											<th><?php echo $lang['DATETIME'];?></th>
										</tr>
									</thead>

									<tbody>
										<?php if($admin_user=='1') { recent_sales_table(); } else { my_recent_sales_table($owner); } ?>
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
    $('#sales').dataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
	})
	$(document).ready( function() {
    $('#top-affiliates').dataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
	})	
	
	var pa_data = [
    {
        value: <?php $device = '1'; if($admin_user=='1'){total_device($device);}else{total_device($device, $owner);}?>,
        color: "#FF4D00",
        highlight: "#FF4D00",
        label: "<?php echo $lang['DESKTOP'];?>"
    },
		{
        value: <?php $device = '2'; if($admin_user=='1'){total_device($device);}else{total_device($device, $owner);}?>,
        color:"#0A5AA1",
        highlight: "#0A5AA1",
        label: "<?php echo $lang['PHONE'];?>"
    },
    {
        value: <?php $device = '3'; if($admin_user=='1'){total_device($device);}else{total_device($device, $owner);}?>,
        color: "#333",
        highlight: "#333",
        label: "<?php echo $lang['TABLET'];?>"
    },

	];
	var pa_options = {
		responsive: true,
    maintainAspectRatio: true
	}
	
	var pa = document.getElementById("pa").getContext("2d");
	new Chart(pa).Doughnut(pa_data, pa_options);
		
	var lc_data = {
    <?php line_chart($admin_user, $owner);?>
	};
	
	var lc_options = {
		pointDotStrokeWidth : 4,
		pointDotRadius : 8,
		bezierCurve : false,
		datasetStrokeWidth : 7,
		datasetFill : false,
		multiTooltipTemplate: "<%= value %> - <%= datasetLabel %>",
		responsive: true,
    maintainAspectRatio: true
	};
	var lc = document.getElementById("lc").getContext("2d");
	new Chart(lc).Line(lc_data, lc_options);
		
	var barData = {
	labels : [<?php if($admin_user=='1'){get_recent_dates();}else{get_recent_dates($owner);}?>],
	datasets : [
		{
			fillColor : "#E6EEF7",
			strokeColor : "#FF4D00",
			data : [<?php if($admin_user=='1'){bar_chart_unqiue_visits();}else{bar_chart_unqiue_visits($owner);}?>]
		}
		]
	}
	var recent_traffic = document.getElementById("recent_traffic").getContext("2d");
	new Chart(recent_traffic).Bar(barData);
		
</script>
</body>
</html>
