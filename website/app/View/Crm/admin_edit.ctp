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
						<input id="CrmActive" name="data[Crm][active]" value="<?php echo $crmDatas['active'] ?>" type="hidden">
						<label class="control-label" for="CrmActive">Active</label>
						<div class="controls">
							<div id="uniform-PageActive" class="padding-checkbox">
								<span>
									<div id="uniform-CrmActive" class="checker">
									<span>
										<input id="CrmActive" name="data[Crm][active]" value="1" type="checkbox" <?php if($crmDatas['active']) echo 'checked' ?>>
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
								<option value="NEVER" <?php if($crmDatas['type'] == 'NEVER') echo 'selected' ?>>N'ayant jamais acheter sur le site</option>
								<option value="SINCE" <?php if($crmDatas['type'] == 'SINCE') echo 'selected' ?>>Inscrit mais n ayant pas acheter depuis</option>
								<option value="CART" <?php if($crmDatas['type'] == 'CART') echo 'selected' ?>>Panier abandonné</option>
								<option value="BUY" <?php if($crmDatas['type'] == 'BUY') echo 'selected' ?>>Achat non finalisé</option>
								<option value="VISIT" <?php if($crmDatas['type'] == 'VISIT') echo 'selected' ?>>Visite profil Expert </option>
								<option value="LOYAL" <?php if($crmDatas['type'] == 'LOYAL') echo 'selected' ?>>Bonus fidélité non utilisé</option>
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
								<option value="0.005" <?php if($crmDatas['timing'] == '0.005') echo 'selected' ?>>30 min</option>
								<option value="0.01" <?php if($crmDatas['timing'] == '0.01') echo 'selected' ?>>1 heure</option>
								<option value="0.02" <?php if($crmDatas['timing'] == '0.02') echo 'selected' ?>>2 heures</option>
								<option value="0.03" <?php if($crmDatas['timing'] == '0.03') echo 'selected' ?>>3 heures</option>
								<option value="0.04" <?php if($crmDatas['timing'] == '0.04') echo 'selected' ?>>4 heures</option>
								<option value="0.05" <?php if($crmDatas['timing'] == '0.05') echo 'selected' ?>>5 heures</option>
								<option value="0.06" <?php if($crmDatas['timing'] == '0.06') echo 'selected' ?>>6 heures</option>
								<option value="0.08" <?php if($crmDatas['timing'] == '0.08') echo 'selected' ?>>8 heures</option>
								<option value="0.10" <?php if($crmDatas['timing'] == '0.10') echo 'selected' ?>>10 heures</option>
								<option value="0.12" <?php if($crmDatas['timing'] == '0.12') echo 'selected' ?>>12 heures</option>
								<option value="1" <?php if($crmDatas['timing'] == '1') echo 'selected' ?>>1 jour</option>
								<option value="2" <?php if($crmDatas['timing'] == '2') echo 'selected' ?>>2 jours</option>
								<option value="3" <?php if($crmDatas['timing'] == '3') echo 'selected' ?>>3 jours</option>
								<option value="4" <?php if($crmDatas['timing'] == '4') echo 'selected' ?>>4 jours</option>
								<option value="5" <?php if($crmDatas['timing'] == '5') echo 'selected' ?>>5 jours</option>
								<option value="6" <?php if($crmDatas['timing'] == '6') echo 'selected' ?>>6 jours</option>
								<option value="7" <?php if($crmDatas['timing'] == '7') echo 'selected' ?>>7 jours</option>
								<option value="14" <?php if($crmDatas['timing'] == '14') echo 'selected' ?>>2 semaines</option>
								<option value="21" <?php if($crmDatas['timing'] == '21') echo 'selected' ?>>3 semaines</option>
								<option value="30" <?php if($crmDatas['timing'] == '30') echo 'selected' ?>>1 mois</option>
								<option value="45" <?php if($crmDatas['timing'] == '45') echo 'selected' ?>>1,5 mois</option>
								<option value="60" <?php if($crmDatas['timing'] == '60') echo 'selected' ?>>2 mois</option>
								<option value="90" <?php if($crmDatas['timing'] == '90') echo 'selected' ?>>3 mois</option>
								<option value="120" <?php if($crmDatas['timing'] == '120') echo 'selected' ?>>4 mois</option>
								<option value="150" <?php if($crmDatas['timing'] == '150') echo 'selected' ?>>5 mois</option>
								<option value="180" <?php if($crmDatas['timing'] == '180') echo 'selected' ?>>6 mois</option>
								<option value="210" <?php if($crmDatas['timing'] == '210') echo 'selected' ?>>7 mois</option>
								<option value="240" <?php if($crmDatas['timing'] == '240') echo 'selected' ?>>8 mois</option>
								<option value="270" <?php if($crmDatas['timing'] == '270') echo 'selected' ?>>9 mois</option>
								<option value="300" <?php if($crmDatas['timing'] == '300') echo 'selected' ?>>10 mois</option>
								<option value="330" <?php if($crmDatas['timing'] == '330') echo 'selected' ?>>11 mois</option>
								<option value="360" <?php if($crmDatas['timing'] == '360') echo 'selected' ?>>12 mois</option>
								<option value="540" <?php if($crmDatas['timing'] == '540') echo 'selected' ?>>18 mois</option>
								<option value="720" <?php if($crmDatas['timing'] == '720') echo 'selected' ?>>2 ans</option>
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
									$selected = '';
									if($crmDatas['id_mail'] == $page["Page"] ["id"]) $selected = ' selected ';
									echo '<option value="'.$page["Page"] ["id"].'" '.$selected.'>'.$page["PageLang"] ["name"].'</option>';
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
									$selected = '';
									if($crmDatas['id_cms'] == $page["Page"] ["id"]) $selected = ' selected ';
									echo '<option value="'.$page["Page"] ["id"].'" '.$selected.'>'.$page["PageLang"] ["name"].'</option>';
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
									if($crmDatas['h_start'] == $heure) $selected = ' selected ';
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
							<input id="CrmTracker" class="span8" name="data[Crm][tracker]" required="required" maxlength="200" type="text" value="<?php echo $crmDatas['tracker'] ?>">
						</div>
					</div>
				</div>
			 </div>
            <?php
                echo $this->Form->end(array(
                    'label' => __('Enregistrer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>