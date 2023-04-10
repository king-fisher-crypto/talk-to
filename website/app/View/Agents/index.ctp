<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mon compte') ?></h1>
</section>
 <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb20 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"><?php echo __('Mes informations') ?></h2>
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
										'name'  =>  '<span class="active">'.__('Mes informations').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

						<div class="row">
							<div class="col-md-12 col-sm-12">
								<div class="box_account well well-account">
									<!--<h3><?php echo __('Mes informations') ?></h3>-->
            <table class="table table-striped no-border agentindextable">
                <tbody>
                <tr>
                    <td class="txt-bold"><?php echo __('Ma photo'); ?></td>
                    <td class="width_td_index"><?php echo $this->Html->image($this->FrontBlock->getAvatar($customer['User'], true)); ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon pseudo'); ?></td>
                    <td><?php echo $customer['User']['pseudo']; ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon code'); ?></td>
                    <td><?php echo $customer['User']['agent_number']; ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Ma dernière communication') ?></td>
                    <td>
                        <?php
                            if(empty($lastCom)):
                                echo __('Vous n\'avez eu aucune communication');
                            else:
                                echo '<span>'.__('Avec').' '.$lastCom['User']['firstname'].'</span><br/>'.
                                    '<span>'.__('par').' '.__($consult_medias[$lastCom['UserCreditHistory']['media']]).'</span><br/><span>'.__('le').' '.
                                    $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$lastCom['UserCreditHistory']['date_start']),' %d %B %Y').'</span>';
                            endif;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $this->Html->link(__('Voir toutes mes communications'), array('action' => 'history'),array('class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 ')); ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mes univers'); ?></td>
                    <td>
                        <ul>
                            <?php foreach($univers as $row): ?>
                                <li><?php echo $row['CategoryLang']['name']; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="block_inl"><?php echo $this->Html->link(__('Voir ma fiche expert'), array(
                    'language'      => $this->Session->read('Config.language'),
                    'controller'    => 'agents',
                    'action'        => 'display',
                    'link_rewrite'  => strtolower(str_replace(' ','-',$customer['User']['pseudo'])),
                    'agent_number'  => $customer['User']['agent_number']
                ), array('class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0')); ?>
            </p>
								</div> 

								<div class="box_account well well-account">
									            <h3><i class="glyphicon glyphicon-user margin_right_5"></i><?php echo __('Mon profil') ?></h3>
            <table class="table table-striped no-border agentindextable">
                <tbody>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon e-mail') ?></td>
                    <td><?php echo $customer['User']['email']; ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon adresse') ?></td>
                    <td>
                        <?php
                            $fullAddress = implode(' ', array($customer['User']['address'], $customer['User']['postalcode'], $customer['User']['city']));
                            if(empty($fullAddress) || ctype_space($fullAddress)):
                                echo __('N/D');
                            else:
                                echo $fullAddress;
                            endif;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon nom'); ?></td>
                    <td><?php echo (empty($customer['User']['lastname']) ?__('N/D'):$customer['User']['lastname']); ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon prénom'); ?></td>
                    <td><?php echo $customer['User']['firstname']; ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Ma date de naissance'); ?></td>
                    <td><?php echo (empty($customer['User']['birthdate'])
                            ?__('N/D')
                            :$this->Time->format($customer['User']['birthdate'],' %d %B %Y')
                        ); ?>
                    </td>

                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon n° de téléphone'); ?></td>
                    <td><?php echo $customer['User']['phone_number'];  ?></td>
                </tr>
                <tr>
                    <td class="txt-bold"><?php echo __('Mon pays'); ?></td>

                    <td><?php echo isset($customer['country']['0']['UserCountryLang']['name'])?$customer['country']['0']['UserCountryLang']['name']:'';  ?></td>
                </tr>
                </tbody>
            </table>
            <p class="block_inl" style="margin-top:10px;"><?php echo $this->Html->link('<i class="glyphicon glyphicon-user margin_right_5"></i> '.__('Modifier mon profil'), array('action' => 'profil'), array('class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0', 'escape' => false)); ?></p>
			<!--<p class="block_inl" style="margin-top:10px;"><?php echo $this->Html->link(''.__('Supprimer mon profil'), array('action' => 'profilsendremove'), array('style' => 'float:right', 'escape' => false)); ?></p>-->

								</div><!--well end-->
							</div><!--col-sm-12-->

						</div><!--row END-->
					</div><!--content_box END-->




				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>