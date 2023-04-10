<?php 
include('auth/startup.php');
include('data/data-functions.php');
//SITE SETTINGS
list($meta_title, $meta_description, $site_title, $site_email) = all_settings();
//REDIRECT ADMIN
if($admin_user!='1'){header('Location: dashboard');}
$payout_id = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
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
				<?php include('assets/comp/payout-stat-boxes.php');?>
				<div class="row">
				<!-- Start Panel -->
					<div class="col-lg-12">
						<div class="panel">
							<div class="panel-heading panel-primary">
								<span class="title">Payout Information</span>
							</div>
							<div class="panel-content">
								<ul>
									<?php payouts_additional($payout_id);?>
								</ul>
								<br><a href="payouts" class="btn btn-default">Go back</a>	
							</div>
						</div>
					</div>
					<!-- End Panel -->
				</div>
            </div>
        </div>
        <!-- End Page Content -->

	</div><!-- End Main Wrapper  -->
   
  <?php include('assets/comp/footer.php'); ?>
	
	<script>
	$(document).ready(function() {
    $('#users').DataTable();
	} );
		
	$(function(){
	//acknowledgement message
    var message_status = $("#status");
    $("span[contenteditable=true]").blur(function(){
        var field_userid = $(this).attr("id") ;
        var value = $(this).text() ;
        $.post('data/update-user.php' , field_userid + "=" + value, function(data){
            if(data != '')
			{
				message_status.show();
				message_status.html(data);
				//hide the message
				setTimeout(function(){message_status.hide()},3000);
			}
        });
    });
	});		
	</script>
	

	
</body>
</html>
