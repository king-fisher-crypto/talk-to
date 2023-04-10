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

        <!-- YOUR CONTENT GOES HERE -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
				<?php if($admin_user=='1'){?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['ADD_MARKETING_MATERIAL'];?></span>
							</div>
							<div class="panel-content">
									<form action="data/upload-data" method="post" enctype="multipart/form-data" >
											<div class="row">
												<div class="col-lg-12">
													<label class="col-md-2 control-label" for="textinput"></label>
													<div class="col-lg-10">
														<select name="media" id="mediaselect" class="selectpicker">
															<option value="link">New Link</option>
															<option value="image">New File </option>
															<option value="video">New Video (embed code)</option>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12" id="newlink" style="display:block;">
													<label class="col-md-2 control-label" for="textinput">Link URL</label>
													<div class="col-lg-10">
														<input type="text" name="customlink" value="<?php echo $main_url;?>?ref=ID" class="form-control input-md" style="width:50%;"> 
														<span class="small-text">("ID" in URL will automatically be replaced with their affiliate ID)</span>  
													</div>
													<label class="col-md-2 control-label" for="textinput">Description</label>
													<div class="col-lg-10">
														<input type="text" name="linkdes" placeholder="Use this page if you..." class="form-control input-md">
														<span class="small-text">(optional link description)</span>
													</div>
												</div>
											</div>
											<div class="row" >
												<div class="col-lg-12" id="newfile" style="display:none;">
													<label class="col-md-2 control-label" for="textinput">Link URL</label>
													<div class="col-lg-10">
														<input type="text" name="customlink" value="<?php echo $main_url;?>?ref=ID" class="form-control input-md" style="width:50%;"> 
														<span class="small-text">("ID" in URL will automatically be replaced with their affiliate ID)</span>  
													</div>
													<label class="col-md-2 control-label" for="textinput">Upload File</label>
													<div class="col-lg-10">
													 	<input type="file" name="file" size="25"  style="margin-top:10px;"/> (image, document, pdf)
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12" id="newvideo" style="display:none;">
													<label class="col-md-2 control-label" for="textinput">Embed Code</label>
													<div class="col-lg-10">
													 <textarea name="embedcode" style="width:50%;" class="form-control"><iframe width="560" height="315" src="https://www.youtube.com/embed/w3ugHP-yZXw" frameborder="0" allowfullscreen></iframe></textarea>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12">
													<label class="col-md-2 control-label" for="textinput"></label>
													<div class="col-lg-10">
														<input type="submit" name="submit" value="Add" class="btn btn-primary" />
													</div>
												</div>
											</div>
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
								<span class="title"><?php echo $lang['GENERATE_TEXT_LINKS'];?></span>
							</div>
							<div class="panel-content">
								<form class="form-horizontal" method="post" action="banners-logos">
									<fieldset>
										
										<div class="form-group">
											<label class="col-md-2 control-label" for="textinput">URL to Promote</label>
											<div class="col-md-10">
												<input id="textinput" name="url" type="text" placeholder="<?php echo $main_url;?>" class="form-control input-md">
												<span class="help-block">example: <?php echo $main_url;?>/product-page-1</span> 
											</div>
										</div>
										<div class="form-group">
										<?php if(isset($url)){
											echo '<label class="col-md-2 control-label" for="textinput">Referral Link</label>
													<div class="col-md-10">';
											if (parse_url($url, PHP_URL_QUERY)){
												echo '<input id="textinput" type="text" value="'.$url.'&ref='.$owner.'" class="form-control input-md">';
											}else{
												echo '<input id="textinput" type="text" value="'.$url.'?ref='.$owner.'" class="form-control input-md">';
												
											}
											echo '</div>';
										}?>
										</div>
										<div class="form-group">
											<div class="col-md-10">
												<input type="submit" class="btn btn-default" value="Generate Referral Link">
											</div>
										</div>
									</fieldset>
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
							<div class="panel-heading panel-warning">
								<span class="title"><?php echo $lang['MARKETING_MATERIAL'];?></span>
							</div>
							<div class="panel-content">
								<div>
									<div id="status"></div>
									<table id="users" class="row-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Media Content</th>
											<th><?php echo $lang['CODE'];?></th>
											<?php if($admin_user=='1'){ echo '<th>'.$lang['ACTION'].'</th>';}?>
										</tr>
									</thead>

									<tbody>
										<?php banner_table($owner, $domain_path, $main_url, $admin_user); ?>
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
	});
	var mediaselect = document.getElementById("mediaselect");

	mediaselect.addEventListener("change", function() {
    if(this.options[this.selectedIndex].value == 'link'){
        document.getElementById('newlink').style.display = "block";
    }else{
        document.getElementById('newlink').style.display = "none";
    }
		if(this.options[this.selectedIndex].value == 'image'){
        document.getElementById('newfile').style.display = "block";
    }else{
        document.getElementById('newfile').style.display = "none";
    }
		if(this.options[this.selectedIndex].value == 'video'){
        document.getElementById('newvideo').style.display = "block";
    }else{
        document.getElementById('newvideo').style.display = "none";
    }
	}, false);	
		
	</script>
	

	
</body>
</html>
