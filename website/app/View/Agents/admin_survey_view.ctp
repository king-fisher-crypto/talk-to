<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
	 echo $this->Html->css('/theme/default/css/planning', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agents'),__('Questionnaire'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Agents'),
            'classes' => 'icon-user-md',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
        ),
		 2 => array(
            'text' => (!isset($user['User']['pseudo']) || empty($user['User']['pseudo'])?__('Agent'):$user['User']['pseudo']),
            'classes' => 'icon-zoom-in',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
        3 => array(
            'text' => __('Questionnaire'),
            'classes' => 'icon-headphones',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true))
        )
    ));

    echo $this->Session->flash(); ?>


    <?php if(empty($survey_answers)):
        echo '<br/><br/><br/><div>'.__('Non rempli par l\'expert.').'</div>';
    else : ?>

		<table class="table table-striped table-hover table-bordered table-hover2">
                <thead>
                <tr>
                    <th width="50%">Question</th>
                    <th width="50%">Réponse</th>
                </tr>
                </thead>
			 <tbody>
				 <?php
				 foreach($survey_questions as $question){
					 
					 foreach($survey_answers as $reponse){
						if($reponse['SurveyAnswer']['question_id'] == $question['SurveyQuestion']['id']){
							?>
				 			<tr <?php if($question['SurveyQuestion']['question'] != 'planning') echo 'class="line_hover"' ?>>
				 				<td><?php 
									if($question['SurveyQuestion']['label'] == 'Pseudo')$question['SurveyQuestion']['label'] = 'Pseudo composé';
									echo $question['SurveyQuestion']['label'];
									?>
								</td>
								<td><?php 
							
									if($question['SurveyQuestion']['question'] == 'planning'){
										$intervalle = array(
													0 => 'Lundi',
													1 => 'Mardi',
													2 => 'Mercredi',
													3 => 'Jeudi',
													4 => 'Vendredi',
													5 => 'Samedi',
													6 => 'Dimanche'
												);
										$intervalle2 = array(
													0 => 'Lundi à ',
													1 => 'Mardi à ',
													2 => 'Mercredi à ',
													3 => 'Jeudi à ',
													4 => 'Vendredi à ',
													5 => 'Samedi à ',
													6 => 'Dimanche à '
												);
										
										$list = explode('#', $reponse['SurveyAnswer']['answer']);
										$plan = array();
										foreach($list as $dd){
											foreach($intervalle2 as $day => $inter){
												if(substr_count($dd,$inter) ){
													if(!is_array($plan[$day] ))$plan[$day] = array();
													$hour_txt = str_replace($inter,'',$dd);
													$hour = explode(':',$hour_txt);
													if(!is_array($plan[$day][$hour[0]] ))$plan[$day][$hour[0]] = array();
													$plan[$day][$hour[0]][$hour[1]] = 1;
												}
											}
										
										}
										?>
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
												<?php 
												

												for($i=0; $i<=6; $i++): //Pour chaque jour du planning?>
													<tr<?php echo (($i%2==0)?' class="alternate"':''); ?>>
														<td class="date_label "><?php echo __($intervalle[$i]); ?></td>
															<?php for($a=0; $a<24; $a++): //Pour chaque heure du jour
														
															if(isset($plan[$i][$a][0])) $checked = 'date_checked'; else $checked = '';
														    ?>
																<td class="date_checkbox <?=$checked ?>">&nbsp;</td>
														   <?php if(isset($plan[$i][$a][30])) $checked = 'date_checked'; else $checked = '';  ?>
																<td class="date_checkbox <?=$checked ?>">&nbsp;</td>
															<?php endfor; ?>
													</tr>
												<?php endfor; ?>
											</tbody>
										</table>
										
										<?php
									}else{
										echo $reponse['SurveyAnswer']['answer'];
									}
							
									
									?>
								</td>
				 			</tr>
				 			<?php
							break;
						} 
					 }
					 
				 }
				 
				 ?>
				 </tbody>
            </table>
			<div class="content_cms_custom" style="text-align: center">
					<?php
			
					echo '<div class="mt40">'.$this->FrontBlock->getPageBlocTexte(443).'</div>';
			
					?>
					</div>
        <?php 
    endif;