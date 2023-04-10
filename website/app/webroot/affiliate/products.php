<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
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
							<?php if($admin_user=='1'){?>
							<div class="row">
							<!-- Start Panel -->
								<div class="col-lg-12">
									<div class="alert alert-warning">
										<strong>Important Admin Message!</strong> If you are using a shopping cart such as opencart or woocommerce, follow the integration instructions for your shopping cart <a href="documentation">here</a>. 
										This section is primarly used for those who do NOT use a shopping cart.
									</div>
									<div class="panel">
										<div class="panel-heading panel-warning">
											<span class="title"><?php echo $lang['ADD_PRODUCT'];?></span>
										</div>
										<div class="panel-content">
												<form action="data/add-product" method="post" enctype="multipart/form-data">
												<fieldset>
													<div class="row">
														<div class="form-group padding-b-10">
															<label class="col-md-2 control-label" for="textinput"><?php echo $lang['PRODUCT_IMG'];?></label>
															<div class="col-md-10">
																<input type="file" name="file" size="25" style="float:left; margin-left:20px;"><br><Br>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<label class="col-md-2 control-label" for="textinput"><?php echo $lang['PRODUCT_NAME'];?></label>
															<div class="col-md-10">
																<input id="textinput" name="name" type="text" placeholder="My Product" class="form-control input-md" required>
															</div>
														</div>
													</div>	
													<div class="row">	
														<div class="form-group">
															<label class="col-md-2 control-label" for="textinput"><?php echo $lang['PRICE'];?></label>
															<div class="col-md-10">
																<input id="textinput" name="price" type="text" placeholder="19.95" class="form-control input-md short-input" required> 
															</div>
														</div>
													</div>	
													<div class="row">		
														<div class="form-group">
															<label class="col-md-2 control-label" for="textinput"><?php echo $lang['COMISSION'];?></label>
															<div class="col-md-10">
																<input id="textinput" name="commission" type="text" placeholder="10" class="form-control input-md short-input">
																<span class="help-block ss-text">Leave blank to use default</span> 
															</div>
														</div>
													</div>		
													<div class="row">		
														<div class="form-group">
															<label class="col-md-2 control-label" for="textinput"><?php echo $lang['RECURRING'];?></label>
															<div class="col-md-10">
																<select name="recurring" class="selectpicker">
																	<option value="">Non-Recurring</option>
																	<option value="daily">Daily</option>
																	<option value="weekly">Weekly</option>
																	<option value="biweekly">Bi-Weekly</option>
																	<option value="monthly">Monthly</option>
																	<option value="quarterly">Quarterly</option>
																	<option value="semiannually">Semi-Annually</option>
																	<option value="annually">Annually</option>
																</select>
															</div>
														</div>	
													</div>		
													<div class="row">		
														<div class="form-group">
															<label class="col-md-2 control-label" for="textinput"><?php echo $lang['RECURRING_FEE'];?></label>
															<div class="col-md-10">
																<input id="textinput" name="recurring_fee" type="text" placeholder="10" class="form-control input-md short-input">
																<span class="help-block ss-text">Leave blank if non-recurring</span> 
															</div>
														</div>	
													</div>	
													<div class="row">		
														<div class="form-group">
															<div class="col-md-10">
																<input type="submit" name="submit" value="<?php echo $lang['ADD_PRODUCT'];?>" class="btn btn-primary" />
															</div>
														</div>
													</div>	
												</fieldset>
											</form>
										</div>
									</div>
								</div>
								<!-- End Panel -->
							</div>			
							<?php } ?>
							<div class="row">
							<!-- Start Panel -->
								<div class="col-lg-12">
									<div class="panel">
										<div class="panel-heading panel-warning">
											<span class="title"><?php echo $lang['PRODUCTS'];?></span>
										</div>
										<div class="panel-content">
											<div>
												<div id="status"></div>
												<table id="users" class="row-border" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th><?php echo $lang['PRODUCT_IMG'];?></th>
														<th><?php echo $lang['PRODUCT_NAME'];?></th>
														<th><?php echo $lang['PRICE'];?></th>
														<th><?php echo $lang['COMMISSION'];?></th>
														<th><?php echo $lang['RECURRING'];?></th>
														<th><?php echo $lang['RECURRING_FEE'];?></th>
														<?php if($admin_user=='1'){?><th><?php echo $lang['INTEGRATION'];?></th><?php }else{ echo '<th>Affiliate Code</th>';}?>
														<?php if($admin_user=='1'){?><th><?php echo $lang['ACTION'];?></th><?php }?>
													</tr>
												</thead>

												<tbody>
													<?php if($admin_user=='1'){products_table($domain_path);}else{products_table($domain_path, $owner);} ?>
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
					
	<!-- Modal -->
	<div class="modal fade" id="openmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">PHP Integration Code</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<div id="load-integration">Loading...</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['CLOSE'];?></button>
				</div>
			</div>
		</div>
	</div>				
	<script>
	$(document).ready(function() {
    $('#users').DataTable();
	});
		
	$(document).on("click", ".open-integration", function () {
     var id = $(this).data('id');
			$.ajax({	
			type: "POST",
			url: "data/load-integration-code.php",
			data: 'id='+id,	
			dataType: "html",	
			success: function(msg){
				if(parseInt(msg)!=0)	
				{
					$('#load-integration').html(msg);	
					$('#loading').css('visibility','hidden');
				}
			}
		});
	});	
		
	</script>
	

	
</body>
</html>
