<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Membre'); ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 page-subscribe-merci">
			<?php echo $this->Session->flash(); ?>
			<?php 
				$user = $this->Session->read('Auth.User');
				$html =  $this->FrontBlock->getPageBlocTexte(351);
				$html = str_replace('#NAME#',$user['firstname'],$html);
				echo $html;
			?>
				
		</div>
    </section>
 </div>

<script>
	fbq('track', 'CompleteRegistration');
</script>
<!-- Google Code for Cr&eacute;ation Compte Membre Conversion Page --> <script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 943164839;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "L61yCP2szWIQp5vewQM"; var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript"
src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt=""
src="//www.googleadservices.com/pagead/conversion/943164839/?label=L61yCP2szWIQp5vewQM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<?php
	if(isset($_SESSION['inscription_user_id']) && $_SESSION['inscription_user_id'] && $_SESSION['inscription_user_source'] == 'landing weedoit'){
		echo '<iframe src="http://www.weedoit.fr/tracking/tracklead.php?idcpart=10761&idr='.$_SESSION['inscription_user_id'].'&email='.$_SESSION['inscription_user_email'].'" height="1" width="1" frameborder="0"></iframe>';
		unset($_SESSION['inscription_user_id']);
		unset($_SESSION['inscription_user_email']);
	}
?>