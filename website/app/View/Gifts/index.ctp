<script src='https://www.google.com/recaptcha/api.js'></script>
<?php
	echo $this->Html->css('/theme/default/css/daterangepicker', array('block' => 'css'));
	echo $this->Html->css('/assets/plugins/bootstrap-datepicker/css/datepicker', array('block' => 'css'));
	echo $this->Html->script('/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker', array('block' => 'script'));
	echo $this->Html->script('/theme/default/js/nx_datepicker', array('block' => 'script'));
?>
<div class="slider-gift">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-6 hidden-sm hidden-xs">
				<div class="slidergift-img"></div>	
			</div>
			<div class="col-sm-12 col-md-6">
				<div class="slidegift_info">	
					<?php echo $this->FrontBlock->getPageBlocTexte(409); ?>	
				</div>		
			</div>
		</div>	
	</div>
</div>
<div class="container-gift">
	<div class="container">
		<div class="page mt20 mb20">
			<div class="breadcumb-gift hidden-sm hidden-xs">
				<ul>
					<li class="step1 active"><a href="#"><?=__('1-MA E-CARTE CADEAU') ?></a></li>
					<li class="disabled"><a href="#"><?=__('2-PAIEMENT') ?></a></li>
				</ul>
			</div>
			<div class="row">
				<div class="col-sm-12 col-md-12 ">
					<span class="gift-title"><?=__('ma e-carte cadeau') ?></span>
					<img src="https://fr.spiriteo.com/media/cms_photo/image/e-carte-cadeau-spiriteo.jpg" class="giftcard-present" alt="<?=__('Spiriteo Carte Cadeau agents') ?>" />
				</div>
			</div>
			<?php echo $this->Form->create('Gift', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
														  'inputDefaults' => array(
															  'div' => '',
															 
															  'class' => 'form-control'
														  )
										));
				?>
					  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
						  <div class="col-sm-12 col-md-12 ">
						  <span class="gift_form_title"><?=__('Je sélectionne le montant') ?></span>
				      <select class="form-control form-control2" id="GiftId" name="data[Gift][id]" required>
						  <?php 
						  
						  $devise = '€';
						if($domain_id == 29)$devise = '$';
						if($domain_id == 13)$devise = 'CHF';		
						  
						  	foreach( $gifts as $key => $gift){
								$selected = '';
								if($gift['Gift']['id'] == $gift_selected)$selected = 'selected';
								echo '<option value="'.$gift['Gift']['id'].'" '.$selected.'>'.$gift['Gift']['amount'].$devise.'</option>';
								
							}?>
						</select>
				  </div>
					
				</div>
			<div class="row">
				<div class="col-sm-12 col-md-6 ">
					<span class="gift_form_title"><?=__('Je renseigne le bénéficiaire') ?></span>
					<div class="row">
					<div class="col-sm-12 col-md-6">
						<div class="form-group">
								<div class="col-sm-12 col-md-12">
							  <input type="text" class="form-control form-control2" id="GiftBeneficiaryFirstname" name="data[Gift][beneficiary_firstname]" placeholder="<?php echo __('Prénom') ?> *" required value="<?php echo $form_data['beneficiary_firstname']; ?>">
						  </div>
						</div>
						</div>
					<div class="col-sm-12 col-md-6">
						<div class="form-group ">
							<div class="col-sm-12 col-md-12">
							  <input type="text" class="form-control form-control2" id="GiftBeneficiaryLastname" name="data[Gift][beneficiary_lastname]" placeholder="<?php echo __('Nom') ?> *" required value="<?php echo $form_data['beneficiary_lastname']; ?>">
						  </div>
						</div></div></div>
						<div class="form-group">
							<div class="col-sm-12 col-md-12">
							  <input type="text" class="form-control form-control2" id="GiftBeneficiaryEmail" name="data[Gift][beneficiary_email]" placeholder="<?php echo __('Email') ?> *" required value="<?php echo $form_data['beneficiary_email']; ?>">
						  </div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 col-md-12">
							  <textarea class="form-control form-control2" id="GiftText" name="data[Gift][text]" placeholder="<?php echo __('Message personnel. ( 180 caractères maximum)') ?>" maxlength="180" rows="5"><?php echo $form_data['text']; ?></textarea>
						  </div>
						</div>
				</div>
				<div class="col-sm-12 col-md-6 ">
					<span class="gift_form_title"><?=__('J\'envoie la carte cadeau par email') ?></span>
					<div class="form-group">
						<div class="col-sm-12 col-md-12">
							<input type="radio" name="data[Gift][send_who]" id="GiftSendWho1" value="0" required> <label for="GiftSendWho1"><?=__('A mon adresse e-mail') ?></label>
							<p><?=__('Je reçois une jolie e-carte cadeau par email, je l\'imprime et l\'offre au bénéficiaire.') ?></p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12 col-md-12">
							<input type="radio" name="data[Gift][send_who]" id="GiftSendWho2" value="1" required> <label for="GiftSendWho2"><?=__('A un autre destinataire') ?></label>
							<p><?=__('J\'envoie la e-carte cadeau par email au bénéficiaire, automatiquement après ma commande, ou à la date de mon choix.') ?></p>
						</div>
					</div>
					<div class="gift-date-select">
						<div id="report" class="" style="">
							<p><?=__('Envoyer la e-carte à la date que vous souhaitez') ?> </p>
							<input class="m-wrap date-picker form-control" type="text" style="min-width:80%" name="data[Gift][send_date]" value="<?php echo $form_data['send_date_fr']; ?>" placeholder="Aujourd'hui">
							<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
				<?php
			echo '<div class="row"><div class="col-lg-3"></div><div class="col-lg-6"><div class="g-recaptcha" data-sitekey="6LdQPR0UAAAAANL3lfsdxx7qQPJbBHi965Ak8tRr"></div></div></div>';
			$hash = rtrim(strtr(base64_encode('e-carte-pdf-0'), '+_', '-|'), '='); 
				echo $this->Form->end(array('label' => __('suivant'), 'class' => 'btn btn-gift', 'before' => '<div class="col-xs-12 col-sm-6 col-md-6"><a class="btn btn-gift-preview" id="giftpreviewbtn" target="_blank" data-toggle="modal2" data-target="#preview_gift" href="/gifts/pdf-'.$hash.'" rel="nofollow">Prévisualiser la e-carte cadeau</a></div><div class="col-xs-12 col-sm-6 col-md-6">', 'after' => '</div>')); ?>
		</div>
		<p class="gift-footerlinks"><?php echo $this->FrontBlock->getPageLink(411, array('target' => '_blank', 'class' => 'nx_openinlightbox', 'style' => 'text-decoration:none'), __('Questions fréquentes')) ?> | <?php echo $this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => 'nx_openinlightbox', 'style' => 'text-decoration:none'), __('CGV')) ?></p>
	</div><!--container END-->
</div>


<!-- Modal -->
  <div class="modal fade" id="preview_gift" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?=__('Carte Cadeau Spiriteo') ?></h4>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Fermer') ?></button>
        </div>
      </div>
      
    </div>
  </div>