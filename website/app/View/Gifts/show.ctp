<div class="container">
	<div class="slider-gift"><div class="container"><div class="row"><div class="col-sm-12 col-md-6 hidden-sm hidden-xs"><div class="slidergift-img"></div></div><div class="col-sm-12 col-md-6"><div class="slidegift_info"><div id="cms_container">
		<div class="cms_text2">
			<?php
			if($pagee == 'buyer'){
				$hash = rtrim(strtr(base64_encode('e-carte-pdf-'.$gift_order['GiftOrder']['id']), '+_', '-|'), '='); 
			?>
				<h3><?php echo $user_order['User']['firstname']; ?>,</h3>
				<h3><?=__('Votre bon cadeau est prêt à être imprimé.') ?></h3>
				<br /><br />
				<h3><?=__('Validité :') ?> <?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$gift_order['GiftOrder']['date_validity']),'%d/%m/%y %Hh%M'); ?></h3>
				<br /><br />
				<a class="btn btn-gift-preview" target="_blank" href="/gifts/pdf-<?=$hash ?>"><?=__('Imprimer la e-carte cadeau') ?></a>
			<?php
			}else{
			?>
				<h3><?=__('Carte cadeau d\'une valeur de') ?> <?php echo $gift_order['GiftOrder']['amount']; ?> <?php
				
				switch ($gift_order['GiftOrder']['devise']) {
					case 'EUR':
						echo "€";
						break;
					case 'CHF':
						echo "CHF";
						break;
					case 'CAD':
						echo "$";
						break;
				}
				
				?></h3>
				<h3><?=__('A valoir sur le site Spiriteo') ?></h3>
				<p><?=__('agents en ligne privée') ?></p>
				<div class="giftinfo_sep">&nbsp;</div>
				<h3><?php echo $gift_order['GiftOrder']['beneficiary_firstname']; ?>,</h3>
					<br />
					<p><?php echo nl2br($gift_order['GiftOrder']['text']); ?></p>
					<br />
					<?php echo $user_order['User']['firstname']; ?>
					<br />
				<div class="giftinfo_sep">&nbsp;</div>
				<h3><?=__('Code Promo :') ?> <?php echo $gift_order['GiftOrder']['code']; ?></h3>
				<h3><?=__('Validité :') ?> <?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$gift_order['GiftOrder']['date_validity']),'%d/%m/%y %Hh%M'); ?></h3>
			<?php
			}
			?>
			<div class="clear"></div></div></div></div></div></div></div></div>
 </div>