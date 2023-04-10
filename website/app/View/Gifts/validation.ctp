	<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"> <?php
        /* titre de page */
        echo __('Votre commande carte cadeau');
    ?></h1>
		<!--h1/h2 both works here-->
	</section>

<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 page-subscribe-merci">
			<?php echo $this->Session->flash(); ?>
			<div id="cms_container">
				<div class="cms_text2">
					<div class="row">
						<div class="col-sm-12 col-md-8 page-sub-merci-top" style="text-align: center,font-size:16px;">
							<img style="display: block; position: relative; right: -5px; margin-left: auto; margin-right: auto;" src="https://fr.spiriteo.com/media/cms_photo/image/merci-page-inscription.png"><br>
							<?php
								if($order && $order['GiftOrder']['send_who']){
									echo __('merci pour votre commande !<br />
									L\'envoi de votre e-carte cadeau à ').$order['GiftOrder']['beneficiary_firstname'].' '.$order['GiftOrder']['beneficiary_lastname'].__(' a bien été enregistré.<br />
Vous allez recevoir un récapitulatif de votre achat par mail.<br />
Nous vous remercions pour votre commande.<br />
');					
								}else{
									echo __('merci pour votre commande !<br />
									Vous allez recevoir un mail contenant votre carte cadeau à imprimer.<br />
Nous vous remercions pour votre achat.<br />
<br />
');					
								}
							?>
							
							<br><br>
							<p class="subscribe-btn" style="text-align: center;">
								<a class="btn btn-pink btn-pink-modified btn-small-modified mb0" style="color: #fff;" title="Spiriteo" href="https://fr.spiriteo.com/"><?=__('VOIR TOUS LES VOYANTS') ?></a></p>
						</div>
						<div class="col-sm-12 col-md-4 page-sub-merci-img">
							<img style="position: relative; float: right;" src="https://fr.spiriteo.com/media/cms_photo/image/bg-subscribe-merci.png"></div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
				
		</div>
    </section>
 </div>