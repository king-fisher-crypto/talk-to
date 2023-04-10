<?php
    echo $this->Metronic->titlePage(__('Subscribe'),__(($this->params['action'] == 'admin_list' ?'Les pages':'Créer une nouvelle page')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Page inscription'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    //Formulaire de création d'une page---------------------------------------------------------------------------------------------------------------

    $form = $this->Form->create('Subscribe', array('nobootstrap' => 1,'class' => 'panel-contenu', 'default' => 1,
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class' => 'span10',
            'after' => '</div>'
        )));

    $form.= '<div class="row-fluid"><div class="span4"><div class="form-horizontal">';

    $form.= $this->Metronic->inputActive('Subscribe', (isset($this->request->data['Subscribe']['active']) ?$this->request->data['Subscribe']['active']:0));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'SubscribeLang.0.lang_id'            => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $lang_options),
    ));

    $form.= '</div></div><div class="span5"><div class="tinymce">';

  //  $form.= $this->Form->input('LandingLang.0.content', array('type' => 'textarea', 'tinymce' => true, 'label' => false, 'div' => false, 'between' => false, 'after' => false));

    $form.= '</div></div>';

$form.= '<div class="row-fluid"><div class="span3" style="padding-left:20px;">
                    <p>'.__('Sélectionner les domaines où le slide sera visible').'</p>
                    <div class="row-fluid">
                        <div class="span12">';
                                foreach($domain_select as $id => $name):
                                    $form.=  $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false));
                                endforeach;
                        $form.= '</div>
                    </div>
                </div></div>';

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    //Listing des pages---------------------------------------------------------------------------------------------------------------------------------------

    $html = '<div class="row-fluid">';
    $html.= '<div class="portlet box blue">';
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les pages d\'inscriptions'). '</div>';
    $html.= '<div class="pull-right">';
    $html.= '</div></div>';
    $html.= '<div class="portlet-body">';

    if(empty($pages)) :
        $html.= __('Pas de page');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>'.$this->Paginator->sort('SubscribeLang.subscribe_id', '#').'</th>';
		$html.= '<th>'.$this->Paginator->sort('Subscribe.domain', 'Domain').'</th>';
        $html.= '<th>'.$this->Paginator->sort('Subscribe.active', __('Etat')).'</th>';
        $html.= '<th>'.__('Langue').'</th>';
        $html.= '<th></th>';
        $html.= '</tr></thead><tbody>';
        foreach($pages as $page) :

            $html.= '<tr>';
            $html.= '<td>'. $page['subscribe_id'] .'</td>';
			
			$name_cat = '';
			foreach($domain_select as $id => $name):
				if($id == $page['domain'])
					$name_cat = $name;
            endforeach;
			$html.= '<td>'. $name_cat .'</td>';
            $html.= '<td>'. $page['etat'] .'</td>';
            $html.= '<td>'. $page['langs'] .'</td>';
            $html.= '<td>';
            $html.= '<div class="btn-group margin-left">';
            $html.= '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
            $html.= '<ul class="dropdown-menu pull-right">';
             $html.= '<li>';
            $html.= $this->Metronic->getLinkButton(
                __('Modifier'),
                array('controller' => 'subscribes','action' => 'edit', 'admin' => true, 'id' => $page['subscribe_id']),
                'btn blue',
                'icon-edit');
            $html.= '</li>';
			

           // if (!$page['hidden_page']){
                $html.= '<li>';
                if($page['active'])
                    $html.= $this->Metronic->getLinkButton(
                        __('Désactiver'),
                        array('controller' => 'subscribes','action' => 'delete', 'admin' => true, 'id' => $page['subscribe_id']),
                        'btn yellow',
                        'icon-remove',
                        __('Voulez-vous vraiment désactiver la page ').' ?');
                else
                    $html.= $this->Metronic->getLinkButton(
                        __('Activer'),
                        array('controller' => 'subscribes','action' => 'add', 'admin' => true, 'id' => $page['subscribe_id']),
                        'btn green',
                        'icon-plus',
                        __('Voulez-vous vraiment activer la page ').' ?');
                $html.= '</li>';



                $html.= '<li>';
                $html.= $this->Metronic->getLinkButton(
                    __('Supprimer'),
                    array('controller' => 'subscribes','action' => 'true_delete', 'admin' => true, 'id' => $page['subscribe_id']),
                    'btn red',
                    'icon-remove',
                    __('Voulez-vous vraiment supprimer définitivement la page ').' ?');
                $html.= '</li>';
           // }


            $html.= '</ul>';
            $html.= '</div>';
            $html.= '</td>';
            $html.= '</tr>';
        endforeach;

        $html.= '</tbody></table>';
        if($this->Paginator->param('pageCount') > 1) :
            $html.= $this->Metronic->pagination($this->Paginator);
        endif;
    endif;

    $html.= '</div></div></div>';

    $tabs = array(
        0 => array('text'       => __('Subscribes'),
            'icon'       => 'icon-list',
            'content'    => $html
        ),
        1 => array('text'       => __('Nouvelle page'),
            'icon'       => 'icon-plus',
            'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['Subscribe']))?1:0);
                            
                            

