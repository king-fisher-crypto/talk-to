<?php
    echo $this->Metronic->titlePage(__('CRM'),__('Création d\'un crm'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un crm'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un crm'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Crm', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>
            <div class="row-fluid" style="display:none">
            	<div class="span12">
					<div class="control-group">
						<label class="control-label" for="CrmActive">Active</label>
						<div class="controls">
							<input id="CrmActive" name="data[Crm][active]" value="0" type="hidden">
							<div id="uniform-PageActive" class="padding-checkbox">
								<span>
									<div id="uniform-CrmActive" class="checker">
									<span>
										<input id="CrmActive" name="data[Crm][active]" value="1" type="checkbox">
									</span>
									</div>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
            
          	            <div class="row-fluid">
            	<div class="span6">
                  	<div class="control-group">
						<label class="control-label required" for="CrmType">Selection Qui ?</label>
						<div class="controls">
							<select id="CrmType" class="span8" name="data[Crm][type]" required="required" >
								<option value="">Choisir</option>
								<option value="NEVER">N'ayant jamais acheter sur le site</option>
								<option value="SINCE">Inscrit mais n ayant pas acheter depuis</option>
								<option value="CART">Panier abandonné</option>
								<option value="BUY">Achat non finalisé</option>
								<option value="VISIT">Visite profil Expert </option>
								<option value="LOYAL">Bonus fidélité non utilisé </option>
							</select>
						</div>
					</div>
				</div>
				<div class="span6">
                  	<div class="control-group">
						<label class="control-label required" for="CrmTiming">Selection Quand ?</label>
						<div class="controls">
							<select id="CrmTiming" class="span8" name="data[Crm][timing]" required="required" >
								<option value="">Choisir</option>
								<option value="0.005">30 min</option>
								<option value="0.01">1 heure</option>
								<option value="0.02">2 heures</option>
								<option value="0.03">3 heures</option>
								<option value="0.04">4 heures</option>
								<option value="0.05">5 heures</option>
								<option value="0.06">6 heures</option>
								<option value="0.08">8 heures</option>
								<option value="0.10">10 heures</option>
								<option value="0.12">12 heures</option>
								<option value="1">1 jour</option>
								<option value="2">2 jours</option>
								<option value="3">3 jours</option>
								<option value="4">4 jours</option>
								<option value="5">5 jours</option>
								<option value="6">6 jours</option>
								<option value="7">7 jours</option>
								<option value="14">2 semaines</option>
								<option value="21">3 semaines</option>
								<option value="30">1 mois</option>
								<option value="45">1,5 mois</option>
								<option value="60">2 mois</option>
								<option value="90">3 mois</option>
								<option value="120">4 mois</option>
								<option value="150">5 mois</option>
								<option value="180">6 mois</option>
								<option value="210">7 mois</option>
								<option value="240">8 mois</option>
								<option value="270">9 mois</option>
								<option value="300">10 mois</option>
								<option value="330">11 mois</option>
								<option value="360">12 mois</option>
								<option value="540">18 mois</option>
								<option value="720">2 ans</option>
							</select>
						</div>
					</div>
				</div>   
			</div>

           	<div class="row-fluid">
            	<div class="span6">
                	<div class="control-group">
						<label class="control-label required" for="CrmIdMail">Selection Email CMS</label>
						<div class="controls">
							<select id="CrmIdMail" class="span8" name="data[Crm][id_mail]" required="required" >
								<option value="">Choisir</option>
							<?php
							
								foreach($mail as $page){
									echo '<option value="'.$page["Page"] ["id"].'">'.$page["PageLang"] ["name"].'</option>';
								}
							
							?>
							</select>
						</div>
					</div>
				</div> 
				<div class="span6">
                	<div class="control-group">
						<label class="control-label required" for="CrmIdCms">Selection Page CMS<br />(texte qui apparait sur page connexion)</label>
						<div class="controls">
							<select id="CrmIdCms" class="span8" name="data[Crm][id_cms]" required="required" >
								<option value="">Choisir</option>
							<?php
							
								foreach($cms as $page){
									echo '<option value="'.$page["Page"] ["id"].'">'.$page["PageLang"] ["name"].'</option>';
								}
							
							?>
							</select>
						</div>
					</div>
				</div> 
			</div>
			 <div class="row-fluid">
            	<div class="span6">
                	<div class="control-group">
						<label class="control-label" for="CrmHStart">Heure d'envoi</label>
						<div class="controls">
							<select id="CrmHStart" class="span8" name="data[Crm][h_start]" >
								<option value="">Choisir</option>
							<?php
							$heures = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
								foreach($heures as $heure){
									$selected = '';
									echo '<option value="'.$heure.'" '.$selected.'>'.$heure.'</option>';
								}
							
							?>
							</select>
						</div>
					</div>
				</div> 
			</div>
<div class="row-fluid">
            	<div class="span12">
                	<div class="control-group">
						<label class="control-label required" for="CrmTracker">Tracker GA</label>
						<div class="controls">
							<input id="CrmTracker" class="span8" name="data[Crm][tracker]" required="required" maxlength="200" type="text">
						</div>
					</div>
				</div>
			</div>
            <?php
                echo $this->Form->end(array(
                    'label' => __('Créer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>