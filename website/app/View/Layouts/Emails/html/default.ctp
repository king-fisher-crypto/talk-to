<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title_for_layout; ?></title>
	<style>
		a{
			color:#7f6faa;
		}
	</style>
</head>
<?php
	$content = $this->fetch('content');
	$is_content_with_img = false;
	if(substr_count($content,'sponsor') || substr_count($content,'crm')|| substr_count($content,'support'))
		$is_content_with_img = true;
	
	$padding = 10;
	$border = '1px solid #7F6FAA';
	
	if($is_content_with_img){
		$padding = 0;
		$border = 'none';
	}
	
	?>
<body>
	<div align="center">
		<table style="width:600px;border-top:<?=$border ?>;border-left:<?=$border ?>;border-right:<?=$border ?>;" width="600" cellspacing="0" cellpadding="0" border="0">
 			<tbody>
				<tr>
  					<td>
						<a href="<?php echo $PARAM_URLSITE; ?>"><img src="<?php echo Configure::read('Email.logo'); ?>" alt="Spiriteo" width="598" border="0" hspace="0" vspace="0" /></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div align="center">
		<table style="width:600px;border-left:<?=$border ?>;border-right:<?=$border ?>;" width="600" cellspacing="0" cellpadding="<?=$padding ?>" border="0">
 			<tbody>
				<tr>
  					<td class="mail_container">
						<?php
                        	
                        	echo $content;
                        ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php if (isset($FOOTER_HTML) && !empty($FOOTER_HTML)): ?>
	<?php echo $FOOTER_HTML; ?>
	<?php endif; ?>
	<?php if (isset($PIXEL) && !empty($PIXEL)): ?>
	<?php echo $PIXEL; ?>
	<?php endif; ?>

</body>
</html>