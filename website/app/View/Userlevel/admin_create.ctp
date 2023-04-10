<?php
    echo $this->Metronic->titlePage(__('Level'),__('Matching Level administrateur'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un admin à un level'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'userlevel', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un level à un compte admin'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Userlevel', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span12">
                   <div class="control-group">
                   	<label for="UserlevelUserId" class="control-label required"><?php echo __('Comptes admin'); ?></label>
                   	<div class="controls">
                   		<select name="data[Userlevel][user_id]" class="span8" required="required" id="UserlevelUserId">
                   			<option value=""><?php echo __('Choisir'); ?></option>
                   			<?php
							foreach($admins as $admin){
								echo '<option value="'.$admin['User']['id'].'">'.$admin['User']['firstname'].'</option>';
							}
							?>
                   		</select>
                   	</div>
                  </div>
                  <div class="control-group">
                   	<label for="UserlevelLevel" class="control-label required"><?php echo __('Comptes admin'); ?></label>
                   	<div class="controls">
                   		<select name="data[Userlevel][level]" class="span8" required="required" id="UserlevelLevel">
                   			<option value=""><?php echo __('Choisir'); ?></option>
                   			<?php
							foreach($levels as $lev){
								$selected = '';
								echo '<option value="'.$lev.'" '.$selected.'>'.$lev.'</option>';						
							}
							?>
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