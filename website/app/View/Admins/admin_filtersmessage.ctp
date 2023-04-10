<?php

echo $this->Metronic->titlePage(__('Messageries filtres'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Messageries filtres'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Messageries filtres') ?></div>
            <div class="pull-left" style="clear:both">
            <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('terme', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Terme').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Créér" /></form>';
                ?>
            </div>
            
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                ?>
            </div>
        </div>
        <div class="portlet-body">

<?php
$html = '';
if(empty($filtres)) :
    $html.= __('Pas de filtre');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('FiltreMessage.id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('FiltreMessage.terme', __('terme')).'</th>';
    $html.= '<th></th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($filtres AS $filtre):

   $html.= '<tr>';
        $html.= '<td>'.$filtre['FiltreMessage']['id'].'</td>';
        $html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">'.$filtre['FiltreMessage']['terme'].'</div></td>';
		$html .= '<td>';
		
                                    $html .= $this->Metronic->getLinkButton(
                                        __('Supprimer')   ,
                                        array('controller' => 'admins', 'action' => 'delete_filtre', 'admin' => true, 'id' => $filtre['FiltreMessage']['id']),
                                        'btn red',
                                        'icon-remove'
                                    );
        $html.= '</td>';
    $html.= '</tr>';
endforeach;



$html.= '</tbody></table>';
if($this->Paginator->param('pageCount') > 1) :
    $html.= $this->Metronic->pagination($this->Paginator);
endif;


echo $html;
?>
</div>
    </div>
</div>