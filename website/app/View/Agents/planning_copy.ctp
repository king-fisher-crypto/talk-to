<?php
echo $this->Html->script('/theme/default/js/planning', array('block' => 'script'));
echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
?>
<?php 
    $intervalleMinute = array('0','30');
    ?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mon Planning') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"><?php echo __('Mon Planning') ?></h2>
							 <?php
						echo $this->Session->flash();
						/* titre de page */
						echo $this->element('title', array(
							'breadcrumb' => array(
								0   =>  array(
									'name'  =>  'Accueil',
									'link'  =>  Router::url('/',true)
								),
								1   =>  array(
									'name'  =>  __('Mon Planning'),
									'link'  =>  ''
								)
							)
						));
		?>

						</div><!--page-header END-->

						

						

						

						<div class="schedule-desktop hidden-xs">

						<p><?php echo __('Veuillez indiquer vos plages horaires de disponibilité pour être consulté :'); ?></p>
<?php if(isset($planning)): ?>
        <?php echo $this->Form->create('Agent',array('action' => 'planning', 'id' => 'AgentPlanningForm', 'nobootstrap' => 1, 'class' => 'form-horizontal', 'default' => 1)); ?>
<table id="planning">
            <thead>
                <tr>
                    <th></th>
                    <?php for($i=0; $i<24; $i++): ?>
                        <th colspan="2" class="date_column"><?php echo $i."h"; ?></th>
                    <?php endfor; ?>

                </tr>
            </thead>
            <tbody>
                <?php for($i=0; $i<Configure::read('Site.limitPlanning'); $i++): //Pour chaque jour du planning?>
                    <tr<?php echo (($i%2==0)?' class="alternate"':''); ?> date="<?php echo $intervalle[$i]['date']; ?>">
                        <td class="date_label"><?php echo __($intervalle[$i]['label']); ?></td>
                        <?php if(empty($planning) || !isset($planning[$intervalle[$i]['date']])): //Si le planning est vide ou si il n'y a aucun horaire pour ce jour?>
                            <?php for($a=0; $a<24; $a++): //Pour chaque heure du jour?>
                                <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 0) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => 0)); ?></td>
                                <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 30) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => 30)); ?></td>
                            <?php endfor; ?>
                        <?php else: ?>
                            <?php if($planning[$intervalle[$i]['date']][0]['type'] === 'fin') array_shift($planning[$intervalle[$i]['date']]); //Si la première tranche horaire est du type fin alors on le shift?>
                            <?php if(empty($planning[$intervalle[$i]['date']])): //S'il n'y a plus d'horaire pour le jour en cours?>
                                <?php for($a=0; $a<24; $a++): //Pour chaque heure du jour?>
                                    <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 0) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => 0)); ?></td>
                                    <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 30) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => 30)); ?></td>
                                <?php endfor; ?>
                            <?php else :
                                $inIntervalle = false; //Variable qui indique si on est dans un intervalle où l'agent est dispo?>
                                <?php for($a=0; $a<24; $a++): //Pour chaque heure du jour?>
                                    <?php if(empty($planning[$intervalle[$i]['date']])): //S'il n'y a plus d'horaire pour le jour en cours?>
                                        <?php if($inIntervalle): //Dans un intervalle, l'agent est dispo?>
                                            <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 0) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('checked' => true, 'hiddenField' => false, 'h' => $a, 'm' => 0)); ?></td>
                                            <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 30) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('checked' => true, 'hiddenField' => false, 'h' => $a, 'm' => 30)); ?></td>
                                        <?php else: //En dehors d'un intervalle, l'agent n'est pas dispo?>
                                            <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 0) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => 0)); ?></td>
                                            <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, 30) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => 30)); ?></td>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <?php foreach($intervalleMinute as $min): //Soit 0 ou 30 min?>
                                            <?php if(isset($planning[$intervalle[$i]['date']][0])                           //S'il y a toujours un horaire
                                                && $a == $planning[$intervalle[$i]['date']][0]['H']                         //Si l'heure en cours == à l'heure de l'horaire
                                                && $min == $planning[$intervalle[$i]['date']][0]['Min']                     //Si les minutes en cours == aux minutes de l'horaire
                                                && strcmp($planning[$intervalle[$i]['date']][0]['type'],'debut') == 0){     //Si l'horaire est de type:debut

                                                    $inIntervalle = true;
                                                    //On retire l'horaire du jour en cours
                                                    array_shift($planning[$intervalle[$i]['date']]);

                                            } ?>
                                            <?php if(isset($planning[$intervalle[$i]['date']][0])                           //S'il y a toujours un horaire
                                                && $a == $planning[$intervalle[$i]['date']][0]['H']                         //Si l'heure en cours == à l'heure de l'horaire
                                                && $min == $planning[$intervalle[$i]['date']][0]['Min']                     //Si les minutes en cours == aux minutes de l'horaire
                                                && strcmp($planning[$intervalle[$i]['date']][0]['type'],'fin') == 0){       //Si l'horaire est de type:fin

                                                $inIntervalle = false;
                                                //On retire l'horaire du jour en cours
                                                array_shift($planning[$intervalle[$i]['date']]);

                                            } ?>
                                            <?php if($inIntervalle): //Dans un intervalle, l'agent est dispo?>
                                                <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, $min) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('checked' => true, 'hiddenField' => false, 'h' => $a, 'm' => $min)); ?></td>
                                            <?php else: //En dehors d'un intervalle, l'agent n'est pas dispo?>
                                                <td class="date_checkbox<?php echo ($this->FrontBlock->hasAppointment($appointments, $intervalle[$i]['date'], $a, $min) ?' appointment':''); ?>"><?php echo $this->Form->checkbox('planning', array('hiddenField' => false, 'h' => $a, 'm' => $min)); ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <?php echo $this->Form->input('Agent.intervalle.0',array('type' => 'hidden', 'value' => $intervalle[0]['date'])); ?>
        <?php echo $this->Form->input('Agent.intervalle.1',array('type' => 'hidden', 'value' => $intervalle[Configure::read('Site.limitPlanning')-1]['date'])); ?>
        
        <div class="text-legend mt20">
			  	<div class="list-legend">
			  	<ul class="list-inline pull-right">
					<li class="disponible"><?php echo __('Je suis disponible'); ?> <span></span></li>
					<li class="indisponible"><?php echo __('Je suis indisponible'); ?> <span></span></li>
					<li class="consultation"><?php echo __('Demande de consultation'); ?> <span></span></li>
				</ul>
				<div class="clearfix"></div>
				</div>
			</div>

        <?php echo $this->Form->end(array('label' => __('Enregistrer'), 'href' => $this->Html->url(array('controller' => 'agents', 'action' => 'modalLoading')), 'class' => 'btn btn-pink btn-pink-modified', 'div' => array('class' => 'form-group'))); ?>
</div>
<div class="schedule-mobile visible-xs">
							<div class="nalert nalert-warning text-center" role="alert">
								<i class="fa fa-exclamation-triangle fa-2x mb10" aria-hidden="true"></i>
								<p><?php echo __('Pour indiquer ou modifier vos horaires de disponibilité, veuillez vous connecter à votre planning depuis un ordinateur.'); ?></p>
							</div>
						</div>
    <?php else: ?>
        <?php echo __('Erreur dans le chargement de votre planning. Veuillez cliquez '. $this->Html->link(__('ici'),array('controller' => 'agents', 'action' => 'planning')) .'.') ?>
    <?php endif; ?>
    
					</div><!--content_box END-->
			</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
			<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->
</div>