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
            'link' => $this->Html->url(array('controller' => 'userlevelacl', 'action' => 'edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Modification d\'un level'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Userlevelacl', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
			
			
				$level = array();
				$menu = array();
				$auth = array();
				foreach($levels as $l){
					if(!in_array($l['Userlevelacl']['level'],$level)){
						array_push($level,$l['Userlevelacl']['level']);
					}
					if(!in_array($l['Userlevelacl']['menu'],$menu)){
						array_push($menu,$l['Userlevelacl']['menu']);
					}
					if(!is_array($auth[$l['Userlevelacl']['level']]))
					$auth[$l['Userlevelacl']['level']] = array();
					if(!is_array($auth[$l['Userlevelacl']['level']][$l['Userlevelacl']['menu']]))
					$auth[$l['Userlevelacl']['level']][$l['Userlevelacl']['menu']] = array();
					
					$auth[$l['Userlevelacl']['level']][$l['Userlevelacl']['menu']] = $l['Userlevelacl']['auth'];
				}
			
            ?>

            <div class="row-fluid">
               
               <?php 
				foreach($level as $lev){
				?>
                <div class="span4">
                  <fieldset>
                  	<legend><?php 
					switch ($lev) {
						case 'editor':
							echo "Editeur";
							break;
						case 'admin':
							echo "Administrateur";
							break;
					}
					 ?></legend>
                  
                  <?php 
				foreach($menu as $men){
				?>
                   <div class="control-group">
                   	<label for="Userlevelaclmenu" class="control-label"><?php echo $men; ?></label>
                   	<div class="controls">
                   		<input type="hidden" name="data[Userlevelacl][<?php echo $men; ?>]" id="'.$model.$idInput.'_" value="0">
            			<div class="padding-checkbox" id="uniform-'.$model.$idInput.'">
            				<span>
            					<input type="checkbox" <?php if($auth[$lev][$men] == 1) echo ' checked="checked"';else echo ''; ?> name="data[Userlevelacl][<?php echo $men; ?>]" value="1" id="Userlevelmenu<?php echo $men; ?>">
                  			</span>
                  		</div>
                   	</div>
                  </div>
                  
                  <?php 
				}
				?>
                  
                  </fieldset>
                </div>
                <?php 
				}
				?>
            </div>
			<input type="hidden" name="data[Userlevelacl][level]" value="<?php echo $lev; ?>" id="Userlevelacllevel">
            <?php
                echo $this->Form->end(array(
                    'label' => __('Modifier'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>