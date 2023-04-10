<?php
    echo $this->Metronic->titlePage(__('Agents'),__('Présentations audio'));
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
            'text' => __('Présentations audio '),
            'classes' => 'icon-music',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_audio', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

?>
     <div class="portlet-title">
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
               
                <?php
                    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('pseudo', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Pseudo').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';


                  
                ?>
            </div>
        </div>
    <?php

    if(empty($rows))
        echo __('Aucune présentation audio.');
    else{
        echo $this->Metronic->getSimpleTable($rows,array('pseudo' => $this->Paginator->sort('User.pseudo', __('Agent')),'presentation_actuelle' => __('Présentation audio actuelle')),
            function($row, $caller){
                return 
                $caller->Metronic->getLinkButton(
                    __('Supprimer'),
                    array('controller' => 'agents','action' => 'delete_agent_presentation', 'admin' => true, 'id' => $row['id']),
                    'btn red nx_refuselightbox',
                    'icon-remove');
            },$this);

        if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
    }