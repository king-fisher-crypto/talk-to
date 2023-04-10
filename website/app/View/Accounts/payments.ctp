<?php
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
    ?><section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes paiements') ?></h1>
	</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mes achats') ?></h2>
						<?php
							echo $this->Session->flash();
							/* titre de page */
							echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  'Accueil',
										'link'  =>  Router::url('/',true)
									),
									1 => array(
										'name'  =>  '<span class="active">'.__('Mes achats').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

				  	<div class="box_account well well-account well-small">

				  	<div class="table-responsive">
                    <table class="table table-striped no-border pricing-table table-mobile text-center">
        				<thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center"><?php echo __('Produit'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Prix'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Crédits'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Date transaction'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Mode de'); ?> <?php echo __('paiement'); ?></th> 
				  	 		</tr> 
				  	 	</thead>
            		
            		<tbody>	
					 <?php
						foreach($gift_order as $key => $gift):  ?>
                    <tr>
                        <td>E-Carte</td>
                        <td><?php echo $gift['GiftOrder']['amount']; 
	
switch ($gift['GiftOrder']['devise']) {
					case 'EUR':
						echo " €";
						break;
					case 'CHF':
						echo " CHF";
						break;
					case 'CAD':
						echo " $";
						break;
				} ?></td>
                        <td class="pink"><a href="/gifts/show-<?php echo $gift['GiftOrder']['hash_buyer'] ?>" target="_blank">voir</a></td>

                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$gift['GiftOrder']['date_add']),' %d/%m/%y %Hh%M'); ?></td>

                        <td><?php echo '<span class="glyphicon glyphicon-credit-card"></span> '.__('Carte'); ?></td>
                    </tr>
                <?php endforeach; ?>
                    <?php foreach($payments as $key => $payment):  ?>
                    <tr>
                        <td><?php 
							
							$title = !empty($payment['UserCredit']['product_name'])?$payment['UserCredit']['product_name']:$payment['ProductLang']['name'];
							$title = str_replace('Remboursement consultation','Remboursement<br />consultation<br />',$title);
							echo str_replace('<br />',' ',$title); ?></td>
                        <?php /*<td style="text-align:right; width:90px"><?php echo $this->Nooxtools->displayPrice($payment['Product']['tarif']); ?></td>*/ ?>
                        <td><?php if(isset($payment['Order']) && isset($payment['Order']['total']))echo number_format($payment['Order']['total'],2).' '.$devises[$payment['Product']['country_id']]; ?></td>
                        <td class="pink"><?php echo $payment['UserCredit']['credits']; ?> <span class="visiblexs">crédits</span></td>

                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$payment['UserCredit']['date_upd']),' %d/%m/%y %Hh%M'); ?></td>

                        <td><?php
							
							

                            if ($payment['UserCredit']['payment_mode'] == 'bankwire'){
                                echo '<span class="glyphicon glyphicon-arrow-right"></span> '.__('Virement bancaire');
                            }elseif ($payment['UserCredit']['payment_mode'] == 'hipay'){
                                echo '<span class="glyphicon glyphicon-credit-card"></span> '.__('Carte');
							}elseif ($payment['UserCredit']['payment_mode'] == 'stripe'){
                                echo '<span class="glyphicon glyphicon-credit-card"></span> '.__('Carte');
                            }elseif ($payment['UserCredit']['payment_mode'] == 'paypal'){
                                echo '<span style="background-image:url(\'/theme/default/images/paypal.png\'); display: inline-block;height: 13px;width: 12px;"></span> '.__('PayPal');
                            }elseif ($payment['UserCredit']['payment_mode'] == 'coupon'){
                                echo '<span class="glyphicon glyphicon-arrow-right"></span> '.__('Coupon de réduction');
                            
							}elseif ($payment['UserCredit']['payment_mode'] == 'refund'){
                                echo '<span class="glyphicon glyphicon-arrow-right"></span> '.__('Remboursement');
                            }
							if(($payment['Orders']['valid'] == 3 && $payment['UserCredit']['payment_mode'] == 'hipay') || ($payment['Orders']['valid'] == 4 && $payment['UserCredit']['payment_mode'] == 'paypal') ){
								echo ' remboursé ';
							}

                            ?></td>
                    </tr>
                <?php endforeach; ?>
                    </tbody>
                    </table>
                    </div>
					<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>
				  	</div>

					</div><!--content_box END-->
				

				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>