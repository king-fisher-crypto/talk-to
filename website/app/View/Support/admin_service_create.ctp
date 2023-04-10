<?php
    echo $this->Metronic->titlePage(__('Service'),__('Support Services'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un service'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un service'); ?>
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
                   	<label for="SupportName" class="control-label required"><?php echo __('Nom'); ?></label>
                   	<div class="controls">
                   		<input type="text" name="data[Support][name]" class="span8" required="required" id="SupportName" />
                   	</div>
                  </div>
                </div>
            </div>
			
			 <div class="row-fluid">
                <div class="span12">
                   <div class="control-group">
                   	<label for="SupportName" class="control-label required"><?php echo __('Visibilité'); ?></label>
                   	<div class="controls">
						<select name="data[Support][who]" class="span8" required="required" id="SupportWho">
							<option value="public"><?php echo __('Publique'); ?></option>
							<option value="client"><?php echo __('Client'); ?></option>
							<option value="agent"><?php echo __('Agent'); ?></option>
						</select>
                   	</div>
                  </div>
                </div>
            </div>
			
			 <div class="row-fluid">
                <div class="span12">
                   <div class="control-group">
                   	<label for="SupportMail" class="control-label required"><?php echo __('Email'); ?></label>
                   	<div class="controls">
                   		<input type="text" name="data[Support][mail]" class="span8" required="required" id="SupportMail" />
                   	</div>
                  </div>
                </div>
            </div>
			
			 <div class="row-fluid">
                <div class="span12">
                   <div class="control-group">
                   	<label for="SupportDescription" class="control-label required"><?php echo __('Libelle selecteur'); ?></label>
                   	<div class="controls">
                   		<input type="text" name="data[Support][description]" class="span8" required="required" id="SupportDescription" />
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