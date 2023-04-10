<?php
    echo $this->Metronic->titlePage(__('Administrateur'),__('Matching administrateur'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un admin à un service'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'user_create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un service à un compte admin'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span12">
                   <div class="control-group">
                   	<label for="SupportUserId" class="control-label required"><?php echo __('Comptes admin'); ?></label>
                   	<div class="controls">
                   		<select name="data[Support][user_id]" class="span8" required="required" id="SupportUserId">
                   			<option value=""><?php echo __('Choisir'); ?></option>
                   			<?php
							foreach($admins as $admin){
								echo '<option value="'.$admin['User']['id'].'">'.$admin['User']['firstname'].' '.$admin['User']['lastname'].'</option>';
							}
							?>
                   		</select>
                   	</div>
                  </div>
                  <div class="control-group">
                   	<label for="SupportServiceId" class="control-label required">Services</label>
                   	<div class="controls">
                   		<select name="data[Support][service]" class="span8" required="required" id="SupportService">
                   			<option value=""><?php echo __('Choisir'); ?></option>
                   			<?php
							foreach($services as $service){
								$selected = '';
								echo '<option value="'.$service['SupportService']['name'].'" '.$selected.'>'.$service['SupportService']['name'].'</option>';						
							}
							?>
                   		</select>
                   	</div>
                  </div>
                     <div class="control-group">
                   	<label for="SupportServiceLevel" class="control-label required"><?php echo __('Level'); ?></label>
                   	<div class="controls">
                   		<select name="data[Support][level]" class="span8" required="required" id="SupportServiceLevel">
                   			<option value=""><?php echo __('Choisir'); ?></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
                   		</select>
                   	</div>
                  </div>
					<div class="control-group">
                   	<label for="SupportServiceIsControle" class="control-label required"><?php echo __('Surveiller ?'); ?></label>
                   	<div class="controls">
                   		<select name="data[Support][is_control]" class="span8" required="required" id="SupportServiceIsControle">
                   			<option value=""><?php echo __('Choisir'); ?></option>
							<option value="0">Non</option>
							<option value="1">Oui</option>
                   		</select>
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