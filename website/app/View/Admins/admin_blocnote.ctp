<?php
    echo $this->Metronic->titlePage(__('Note'),__('Note interne'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
       1 => array(
            'text' => __('Note'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'blocnote', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
   <?php
                echo $this->Form->create('AdminNote', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span12">
					<textarea id="AdminNoteNote" class="span8" name="data[AdminNote][note]" style="width:100%;height:500px;"><?php
						echo $note['AdminNote']['note'];
						?></textarea>
                </div>
                
            </div>
<div class="row-fluid">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue rfloat save_note',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>