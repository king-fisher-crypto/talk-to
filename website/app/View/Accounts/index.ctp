<?php  
echo $this->Html->script('/theme/default/js/account_cu', array('block' => 'script'));
?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo (empty($customer['User']['firstname']) ?$customer['User']['lastname']:$customer['User']['firstname']); ?></h1>
</section>
<div class="container">
		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb20 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
                        <h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"><?php echo __('Mon compte Client'); ?></h2>
						 <?php
							echo $this->Session->flash();
							/* titre de page */
							/*echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  'Accueil',
										'link'  =>  Router::url('/',true)
									)
								)
							));*/
						?>

					</div><!--page-header END-->
						<div class="row">
							<?php
							if($gift_order){
							?>
							<div class="col-md-12 col-sm-12 col-lg-12">
								<div class="box_account well well-account">
									<h3><span class="glyphicon glyphicon-gift"></span> <?php echo __('Ma carte cadeau') ?></h3>
									<p>solde : <?php echo $gift_order['GiftOrder']['amount']; 
	
switch ($gift_order['GiftOrder']['devise']) {
					case 'EUR':
						echo " €";
						break;
					case 'CHF':
						echo " CHF";
						break;
					case 'CAD':
						echo " $";
						break;
				} ?> à utiliser avant le <?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$gift_order['GiftOrder']['date_validity']),'%d/%m/%y %Hh%M'); ?></p>
								<h3>Votre CODE : <?php echo $gift_order['GiftOrder']['code'] ?></h3>
								</div>
							</div>
							<?php
							}
							?>
							<div class="col-md-12 col-sm-12 col-lg-12">
								<div class="box_account well well-account">
									<h3><span class="glyphicon glyphicon-info-sign"></span> <?php echo __('Mes informations') ?></h3>
										<table class="table table-striped no-border agentindextable">
											<tbody>
												<tr>
													<td class="txt-bold"><?php echo __('Mon code personnel'); ?></td>
													<td class="width_td_index" style="font-size:16px;"><?php echo $customer['User']['personal_code']; ?></td>
												</tr> 
												<tr> 
													<td class="txt-bold"><?php echo __('Crédits'); ?></td> 
													<td><?php echo $customer['User']['credit'].__(' soit ').$this->FrontBlock->secondsToHis($customer['User']['credit'], true); ?></td> 
												</tr> 
												<tr> 
													<td class="txt-bold"><?php echo __('Ma dernière communication'); ?></td> 
													<td><?php
                                    if(empty($lastCom)):
                                        echo __('Vous n\'avez eu aucune communication');
                                        echo '<p>'.__('Pour commencer, rendez-vous').' '.$this->Html->link(__('ici'), array('controller' => 'home', 'action' => 'index')).'</p>';
                                    else:
                                        echo '<span>'.__('Avec').' ';
                                        if(empty($agent)):
                                            echo $lastCom['UserCreditHistory']['agent_pseudo'];
                                        else:
                                            echo $this->Html->link($agent['User']['pseudo'], array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'agents',
                                                    'action'        => 'display',
                                                    'link_rewrite'  => strtolower($agent['User']['pseudo']),
                                                    'agent_number'  => $agent['User']['agent_number']
                                                ));
                                        endif;
                                        echo '</span><br/><span>'.__('par').' '.__($consult_medias[$lastCom['UserCreditHistory']['media']]).'</span><br/><sppan>'.__('le').' '.
                                            $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$lastCom['UserCreditHistory']['date_start']),' %d %B %Y').'</span>';
                                    endif;
                                ?></td> 
												</tr>
												<tr> 
													<td colspan="2">
                                                    	<?php echo $this->Html->link(__('Voir toutes mes communications'), array('action' => 'history'), array('class'=> 'btn btn-pink btn-pink-modified btn-small-modified mb0')); ?>
												</tr> 
											</tbody> 
										</table> 
								</div> 
								
									<?php
							if($vouchers && 1 == 2){
							?>
							
								<div class="box_account well well-account">
									<h3><span class="glyphicon glyphicon-star"></span> <?php echo __('Mes bons de réductions') ?></h3>
									<div class="row">
									<?php
									foreach($vouchers as $voucher){
										
										$label = '';
										
										$label .= '<span style="font-weight:bold;font-size:20px">'.$voucher['Voucher']['code'].'</span></br>';
										
										if($voucher['Voucher']['title']){
											$label .= $voucher['Voucher']['title'].'</br>';
										}
										
										
										if($voucher['Voucher']['validity_end'] != '0000-00-00 00:00:00')
											$label .= '<span style=";font-size:11px">Validité : '.$this->Time->format($voucher['Voucher']['validity_end'],' %d %B %Y').'</span></br>';
										
										
										echo '<div class="col-md-4 col-sm-6">';
										echo '<p style="display:block;margin:10px 0;text-align:center;">'.$this->Html->link($label, array('controller' => 'accounts', 'action' => 'usevoucher-'.$voucher['Voucher']['code']), array('title' => __('Utiliser mon bon de réduction'), 'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0', 'style' => 'display:block;width:100%;white-space:normal !important;height:105px;', 'escape'=> false)).'</p>';
										echo '</div>';
									}
								
									?>
									</div>
								</div>
							<?php
							}
							?>

								<div class="box_account well well-account">
									<h3><i class="glyphicon glyphicon-user margin_right_5"></i> <?php echo __('Mon profil') ?></h3>
									 <table class="table table-striped no-border agentindextable">
									 	<tbody> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Mon e-mail'); ?></td> 
									 			<td><?php echo $customer['User']['email']; ?></td> 
									 		</tr> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Mon adresse'); ?></td> 
									 			<td><?php
                                $fullAddress = implode(' ', array($customer['User']['address'], $customer['User']['postalcode'], $customer['User']['city']));
                                if(empty($fullAddress) || ctype_space($fullAddress)):
                                    echo __('N/D');
                                else:
                                    echo $fullAddress;
                                endif;
                            ?></td> 
									 		</tr> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Mon nom'); ?></td> 
									 			<td><?php echo (empty($customer['User']['lastname']) ?__('N/D'):$customer['User']['lastname']); ?></td> 
									 		</tr> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Mon prénom ou pseudo'); ?></td> 
									 			<td><?php echo $customer['User']['firstname']; ?></td> 
									 		</tr> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Ma date de naissance'); ?></td> 
									 			<td><?php echo (empty($customer['User']['birthdate'])
                                ?__('N/D')
                                :$this->Time->format($customer['User']['birthdate'],' %d %B %Y')
                            ); ?></td> 
									 		</tr> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Mon n° de téléphone'); ?></td> 
									 			<td><?php echo (empty($customer['User']['phone_number']) ?__('N/D'):'+'.$customer['User']['phone_number']); ?></td> 
									 		</tr> 
									 		<tr> 
									 			<td class="txt-bold"><?php echo __('Mon pays'); ?></td>
									 			<td><?php echo isset($customer['country']['0']['UserCountryLang']['name'])?$customer['country']['0']['UserCountryLang']['name']:'';  ?></td> 
									 		</tr>

									 		<tr> 
													<td colspan="2">
                                                    	<?php echo $this->Html->link(__('Modifier mon profil'), array('action' => 'profil'), array('class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0', 'escape' => false)); ?>
														<br /><br />
														<?php echo $this->Html->link(__('Supprimer mon profil'), array('action' => 'profilsendremove'), array('style' => 'float:right', 'escape' => false, 'class' => 'removetheprofil')); ?>
												</tr> 

									 	</tbody> 
									 </table> 

								</div><!--well end-->
							</div><!--col-sm-12-->
							
							

							<div class="col-md-5 col-sm-12"> 

								<div class="box_account well well-account">
									<ul class="nav nav-pills nav-stacked account_links"> 
										<li>
                      					<a href="<?php echo $this->Frontblock->getProductsLink(); ?>" class="link_account buy"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php echo __('Acheter des minutes'); ?></a>
                      <?php
                       /* echo $this->Html->link('<i class="fa fa-shopping-cart" aria-hidden="true"></i> '.__('Acheter des minutes'),
                            array('action' => 'buycredits'),
                            array('escape' => false, 'class' => 'link_account buy')
                        );*/
                        ?></li> 
										<li><?php
                        echo $this->Html->link('<i class="fa fa-phone" aria-hidden="true"></i> '.__('Consultation par téléphone'),
                            array('controller' => 'home', 'action' => 'index', '?' => array('filter' => 'phone')),
                            array('escape' => false, 'class' => 'link_account')
                        );
                        ?></li> 
										<li> <?php
                        echo $this->Html->link('<i class="fa fa-envelope" aria-hidden="true"></i> '.__('Consultation par mail'),
                            array('controller' => 'home', 'action' => 'index', '?' => array('filter' => 'email')),
                            array('escape' => false, 'class' => 'link_account')
                        );
                        ?></li> 
										<li> <?php
                        echo $this->Html->link('<i class="fa fa-comments" aria-hidden="true"></i> '.__('Consultation par chat'),
                            array('controller' => 'home', 'action' => 'index', '?' => array('filter' => 'chat')),
                            array('escape' => false, 'class' => 'link_account')
                        );
                        ?></li> 
									</ul> 
								</div> 
								
							</div>

							<div class="col-md-7 col-sm-12"> 
								<div class="box_account well well-account favoris-box">
									<h3><i class="glyphicon glyphicon-star margin_right_5"></i> <?php echo __('Mes experts favoris') ?></h3> 
									                <?php if(empty($agents)):
                    echo '<p>'.__('Vous n\'avez pas d\'experts en favoris').'</p>';
                else : ?>
                <div class="table-responsive">
                    <table class="table table-striped no-border table-mobile text-center mb0" id="availab_agent">
                        <thead class="hidden-xs text-center"> 
                        <tr>
                            <th class="text-center"><?php echo __('Expert'); ?></th>
                            <th class="text-center"><?php echo __('Disponibilité'); ?></th>
                            <th></th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($agents as $row): ?>
                            <tr>
                                <td class="veram resize-img">
                                	<?php 
									$label = '';
									 switch ($row['Agent']['agent_status']){
                                            case 'available' :
                                                $label =  '<label class="label label-success">'.__('Disponible').'</label>';
                                                break;
                                            case 'busy' :
                                                $label =  '<label class="label label-warning">'.__('Occupé').'</label>';
                                                break;
                                            case 'unavailable' :
                                                $label =  '<label class="label label-danger">'.__('Indisponible').'</label>';
                                                break;
                                        }
									
									echo $this->Html->link(
                                        $this->Html->image($row['Agent']['photo'],array('class' => 'small-profile img-responsive img-circle', 'before'=>'<span>', 'after'=>'</span>')),
                                        array(
                                            'language'      => $this->Session->read('Config.language'),
                                            'controller'    => 'agents',
                                            'action'        => 'display',
                                            'link_rewrite'  => strtolower(str_replace(' ','-',$row['Agent']['pseudo'])),
                                            'agent_number'  => $row['Agent']['agent_number']
                                        ),
                                        array('escape' => false, 'class'=>'sm-sid-photo')
                                    );
                                        echo $this->Html->link(
                                            $row['Agent']['pseudo'].'<span class="dis-m visible-xs visible-only-480">'.$label.'</span>',
                                            array(
                                                'language'      => $this->Session->read('Config.language'),
                                                'controller'    => 'agents',
                                                'action'        => 'display',
                                                'link_rewrite'  => strtolower($row['Agent']['pseudo']),
                                                'agent_number'  => $row['Agent']['agent_number']
                                            ),
                                            array('class' => 'agent-pseudo','escape' => false)
                                        );
                                    ?>
                                
				  	 				<span class="visible-xs visible-only-768 h6 mt15 pull-right">
				  	 				<a href="#"><?=$label ?></a></span>
				  	 				<div class="visible-xs mb10 cboth tact">
                                    	<?php
                                        if(in_array($row['Agent']['id'], array_keys($agentsFavorite))) :
                                            echo $this->Html->link('<i class="glyphicon glyphicon-edit margin_right_5"></i>'.__('Déposer un avis'),
                                                array('controller' => 'accounts', 'action' => 'review', 'expert' => $row['Agent']['agent_number']),
                                                array('escape' => false, 'class' => 'btn btn-pink btn-pink-modified btn-small-modified btn btn-default btn-xs mb0')
                                            );
                                        endif;
                                    ?>
				  	 				</div>
                                    
                                    
                                </td>
                                <td class="veram whitespace-normal hidden-xs">
                                    <?php

                                        echo '<a href="'.$this->Html->url(array(
                                                'language'      => $this->Session->read('Config.language'),
                                                'controller'    => 'agents',
                                                'action'        => 'display',
                                                'link_rewrite'  => strtolower($row['Agent']['pseudo']),
                                                'agent_number'  => $row['Agent']['agent_number']
                                            )
                                        );
                                        echo '">';


                                        switch ($row['Agent']['agent_status']){
                                            case 'available' :
                                                echo '<label class="label label-success">'.__('Disponible').'</label>';
                                                break;
                                            case 'busy' :
                                                echo '<label class="label label-warning">'.__('Occupé').'</label>';
                                                break;
                                            case 'unavailable' :
                                                echo '<label class="label label-danger">'.__('Indisponible').'</label>';
                                                break;
                                        }

                                        echo '</a>';
                                    ?>
                                </td>
                                <?php /*
                                <td><?php echo $this->Html->link('<i class="glyphicon glyphicon-zoom-in margin_right_5"></i>'.__('Voir'),
                                        array(
                                            'language'      => $this->Session->read('Config.language'),
                                            'controller'    => 'agents',
                                            'action'        => 'display',
                                            'link_rewrite'  => strtolower($row['Agent']['pseudo']),
                                            'agent_number'  => $row['Agent']['agent_number']
                                        ),
                                        array('escape' => false, 'class' => 'btn btn-default btn-xs')

                                    ); ?>
                                </td>
 */ ?>
                                <td class="veram hidden-xs">
                                    <?php
                                        if(in_array($row['Agent']['id'], array_keys($agentsFavorite))) :
                                            echo $this->Html->link('<i class="glyphicon glyphicon-edit margin_right_5"></i>'.__('Déposer un avis'),
                                                array('controller' => 'accounts', 'action' => 'review', 'expert' => $row['Agent']['agent_number']),
                                                array('escape' => false, 'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 btn btn-default btn-xs')
                                            );
                                        endif;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <p class="mt10"><?php echo $this->Html->link(''.__('Voir mes experts favoris'), array('action' => 'favorites'), array('class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0', 'escape' => false)); ?></p>
                <?php endif; ?>

                                 </div> 
								</div> 
							<div class="col-md-12 col-sm-12 visible-xs">
								<div class="panel-group dd-menu mb0" id="blockLoyalty">
								<?php echo $this->Frontblock->getAccountLoyalty(); ?>
								</div>
							</div> 	
						</div><!--row END-->
					</div><!--content_box END-->




				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
</div><!--row END-->
</section><!--expert-list END-->
<?php
	$page = $this->FrontBlock->getPageTextebyLang(360,$this->Session->read('Config.id_lang'));
		if($cond_cu && $page){
			?>
		
		<div id="dialog-cu" title="Validation Conditions Générales d'utilisation" style="display:none">
        	<p style="color:#5a449b;font-size:13px;"><?=__('Validez les CGU') ?></p>
			<div style="display:block;width:500px;height:400px;overflow-y: auto;margin-bottom:10px">
				<?php
				
                            if($page !== false){
                                ?>
                                <?php
								$h1 = '<h1 style="font-size:20px;">';
								$pp = str_replace('<h1 style="text-align: justify;">',$h1,$page);
								$pp = str_replace('<h1>',$h1,$page);
                                 echo $pp;
                                 ?>
                                <?php
                            }
				?>
			</div>
        </div>
        <?php
            }
		?>
</div>

