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
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['CREATE_PAYOUT_REQUEST'];?></span>
							</div>
							<div class="panel-content">
									<?php if($_GET['e']=='1') { echo '<span class="red">Payment emails do not match.</span>';}
										  if($_GET['e']=='2') { echo '<span class="red">Your request is less than the minimum payment amount.</span>';}
										  if($_GET['e']=='3') { echo '<span class="red">Your balance is lower than the requested amount OR you have pending requests open that total greater than your current balance.</span>';}
										?> <br><br>
								<form class="form-horizontal" method="post" action="data/add-payout-request" style="margin-left:-300px;">

										<div class="form-group">
										  <label class="col-md-4 control-label" for="textinput">Payment Method</label>  
										  <div class="col-md-6">
										  	<select id="payment_methods" name="pm">
											  <option value="">Select a Payment Method</option>
											  <?php available_payment();?>
											</select>
										  </div>
										</div>

										<div class="form-group">
										  <label class="col-md-4 control-label" for="textinput">Email</label>  
										  <div class="col-md-6">
										  <input id="textinput" name="ppe" type="text" placeholder="" class="form-control input-md" required="">
										  <span class="help-block">We are not responsible for lost payments due to filling this out incorrectly!</span>  
										  </div>
										</div>


										<div class="form-group">
										  <label class="col-md-4 control-label" for="textinput">Verify Email</label>  
										  <div class="col-md-6">
										  <input id="textinput" name="vppe" type="text" placeholder="" class="form-control input-md" required="">
										  <span class="help-block">Verify Payment Email</span>  
										  </div>
										</div>
										  
										<!-- SPECIAL FIELDS FOR STRIPE METHOD -->
										<div class="pmethods" id="2" style="display:none;">
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Bank Name</label>
												<div class="col-md-6">
													<input id="textinput" name="sb" type="text" placeholder="" class="form-control input-md">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Routing #</label>
												<div class="col-md-6">
													<input id="textinput" name="sr" type="text" placeholder="" class="form-control input-md">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Account #</label>
												<div class="col-md-6">
													<input id="textinput" name="sa" type="text" placeholder="" class="form-control input-md">
												</div>
											</div>
										</div>
									
										<!-- SPECIAL FIELDS FOR WIRE METHOD -->
										<div class="pmethods" id="4" style="display:none;">
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Bank Name</label>
												<div class="col-md-6">
													<input id="textinput" name="wb" type="text" placeholder="" class="form-control input-md">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Routing #</label>
												<div class="col-md-6">
													<input id="textinput" name="wr" type="text" placeholder="" class="form-control input-md">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Account #</label>
												<div class="col-md-6">
													<input id="textinput" name="wa" type="text" placeholder="" class="form-control input-md">
												</div>
											</div>
										</div>
									
										<!-- SPECIAL FIELDS FOR CHECK METHOD -->
										<div class="pmethods" id="5" style="display:none;">
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Street Address</label>
												<div class="col-md-6">
													<input id="textinput" name="street" type="text" placeholder="123 Circle Street Apt 3" class="form-control input-md">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">City</label>
												<div class="col-md-6">
													<input id="textinput" name="city" type="text" placeholder="My City" class="form-control input-md" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label" for="textinput">Zip Code</label>
												<div class="col-md-6">
													<input id="textinput" name="zip" type="text" placeholder="12345" class="form-control input-md">
												</div>
											</div>
											
										</div>

										<div class="form-group">
										  <label class="col-md-4 control-label" for="textinput">Requested Amount</label>  
										  <div class="col-md-4">
										  <input id="textinput" name="a" type="text" placeholder="" class="form-control input-md">
											  
										  <?php 
												$get_min = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT min_payout FROM ap_settings WHERE id=1"));
												$min_payout = $get_min['min_payout'];

												$get_balance= mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT balance FROM ap_members WHERE id=$owner"));
												$balance = $get_balance['balance'];
												$money_format = new \NumberFormatter($locale, \NumberFormatter::CURRENCY); 
												$currency_symbol = $money_format->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL); 
										   ?>
										  <span class="help-block">* Must meet minimum payout (<?php echo $money_format->formatCurrency($min_payout, $currency_symbol);?>) | <strong class="green">Your Balance: <?php echo $money_format->formatCurrency($balance, $currency_symbol);?></strong></span>  
										  </div>
										</div>

										<div class="form-group">
										  <label class="col-md-4 control-label" for="singlebutton"></label>
										  <div class="col-md-4">
											<button id="singlebutton" name="singlebutton" class="btn btn-primary">Submit Request</button>
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
								<span class="title"><?php echo $lang['MY_PAYOUT_REQUESTS'];?></span>
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
									<table id="users" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php echo $lang['PAYMENT_METHOD'];?></th>
											<th><?php echo $lang['PAYOUT_AMOUNT'];?></th>
											<th><?php echo $lang['PAYOUT_EMAIL'];?></th>
											<th><?php echo $lang['PAYOUT_STATUS'];?></th>
											<th><?php echo $lang['DATETIME'];?></th>
											<th><?php echo $lang['ACTION'];?></th>
										</tr>
									</thead>

									<tbody>
										<?php my_payout_table($start_date, $end_date, $owner); ?>
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
		
	 $(function() {
        $('#payment_methods').change(function(){
            $('.pmethods').hide();
            $('#' + $(this).val()).show();
        });
    });		
	</script>
	

	
</body>
</html>
