<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Affiliate Pro Installation Setup</title>

    <!-- Bootstrap Core CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../assets/css/base.css" rel="stylesheet">
    <link href="../assets/css/login.css" rel="stylesheet">
    <link href="../assets/fonts/css/fa.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="container">
        <div class="row">
						<div class="col-sm-12 col-md-9 col-md-offset-1">
                <form method="post" action="new-installation" class="form-horizontal login-form">
								<fieldset>
											<legend>Affiliate Pro New Installation</legend>
											<h4>Server Requirements</h4>
											<table class="requirements-table">
												<th width="50%">Required</th>
												<th width="50%">Your Server</th>
												<tr>
													<td>PHP 5.3 or greater</td>
													<td><?php if(phpversion() < '5.3'){
																echo '<div class="req-not-met"></div>';
															}else{
																echo '<div class="req-met"></div>';
															}?> 
														PHP <?php echo phpversion();?>
													</td>
												</tr>
												<tr>
													<td>Mysqli (Mysql Improved) extension enabled</td>
													<td><?php if(function_exists('mysqli_connect')) {
																echo '<div class="req-met"></div> Mysqli extension is enabled.';
															}else{
																echo '<div class="req-not-met"></div> Mysqli extension is not enabled.';
															}?> 
													</td>
												</tr>
												<tr>
													<td>PHP intl extension (used for mutli-currency formatting)</td>
													<td>
														Please verify with your hosting provider.
													</td>
												</tr>
												<tr>
													<td>Apache mod_rewrite enabled</td>
													<td>
														Please verify with your hosting provider.
													</td>
												</tr>
											</table>
											<hr>
											<h4>Basic Setup Information</h4>
											<div class="control-group">
												<label class="control-label" for="textinput">Installation Folder</label>
												<div class="controls">
												<input id="textinput" name="install" type="text" value="affiliate-pro" class="input-xlarge" > (leave blank if uploaded to root directory)
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="textinput">Database HOST</label>
												<div class="controls">
												<input id="textinput" name="dbhost" type="text" class="input-xlarge" value="localhost">
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="textinput">Database Name</label>
												<div class="controls">
												<input id="textinput" name="dbname" type="text" class="input-xlarge">
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="textinput">Database Username</label>
												<div class="controls">
												<input id="textinput" name="dbusername" type="text" class="input-xlarge">
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="textinput">Database Password</label>
												<div class="controls">
												<input id="textinput" name="dbpassword" type="password" class="input-xlarge" >
												</div>
											</div>
											
											<!-- Submit-->
											<div class="control-group">
												<div class="controls">
												<input type="submit" class="btn btn-primary btn-block login-btn" value="Start Installation">
												</div>
											</div>
	
								</fieldset>
							</form> 
            </div>
        </div>
    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="../assets/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../assets/js/bootstrap.min.js"></script>

</body>

</html>
