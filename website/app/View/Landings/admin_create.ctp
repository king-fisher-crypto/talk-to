<?php
    echo $this->Metronic->titlePage(__('Landings'),__(($this->params['action'] == 'admin_list' ?'Les pages':'Créer une nouvelle page')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Landings'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'landings', 'action' => 'list', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    //Formulaire de création d'une page---------------------------------------------------------------------------------------------------------------

    $form = $this->Form->create('Landing', array('nobootstrap' => 1,'class' => 'panel-contenu', 'default' => 1,
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class' => 'span10',
            'after' => '</div>'
        )));

    $form.= '<div class="row-fluid"><div class="span4"><div class="form-horizontal">';

    $form.= $this->Metronic->inputActive('Landing', (isset($this->request->data['Landing']['active']) ?$this->request->data['Landing']['active']:0));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'page_category_id'              => array('label' => array('text' => __('Catégorie'), 'class' => 'control-label required'), 'required' => true, 'options' => $cat_options, 'empty' => __('Choisissez')),
        'LandingLang.0.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true),
        'LandingLang.0.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label required'), 'required' => true, 'type' => 'text', 'after' => '<p class="label label-info" style="white-space:normal">'.__('Nombre de caractères recommandé : ').Configure::read('Site.lengthMetaTitle').'</p></div>'),
        'LandingLang.0.lang_id'            => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $lang_options),
        'LandingLang.0.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label')),
        'LandingLang.0.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'after' => '<p class="label label-info" style="white-space:normal">'.__('Nombre de caractères recommandé : ').Configure::read('Site.lengthMetaKeywords').'</p></div>'),
        'LandingLang.0.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'after' => '<p class="label label-info" style="white-space:normal">'.__('Nombre de caractères recommandé : ').Configure::read('Site.lengthMetaDescription').'</p></div>')
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
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les pages CMS'). '</div>';
    $html.= '<div class="pull-right">';
    $html.= '<span class="label-search">'. __('Recherche') .'</span>';
    $html.= $this->Form->create('LandingLang', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    $html.= $this->Form->input('title', array('class' => 'input-mini  margin-left margin-right', 'type' => 'text', 'label' => __('Titre').' :', 'div' => false));
    $html.= $this->Form->input('search', array('type' => 'hidden'));
    $html.= '<input class="btn green" type="submit" value="Ok">';
    $html.= '</form>';
    $html.= $this->Form->create('LandingLang', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    $html.= $this->Form->input('category', array('class' => 'margin-left margin-right', 'options' => $cat_options, 'empty' => '------', 'label' => __('Catégorie').' :', 'div' => false));
    $html.= $this->Form->input('search', array('type' => 'hidden'));
    $html.= '<input class="btn green" type="submit" value="Ok">';
    $html.= '</form>';
    $html.= '</div></div>';
    $html.= '<div class="portlet-body">';

    if(empty($pages)) :
        $html.= __('Pas de page');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>'.$this->Paginator->sort('LandingLang.landing_id', '#').'</th>';
		$html.= '<th>'.$this->Paginator->sort('Landing.domain', 'Domain').'</th>';
        $html.= '<th>'.$this->Paginator->sort('LandingLang.name', __('Nom')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('Landing.page_category_id', __('Catégorie')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('Landing.active', __('Etat')).'</th>';
        $html.= '<th>'.__('Langue').'</th>';
        $html.= '<th></th>';
        $html.= '</tr></thead><tbody>';
        foreach($pages as $page) :

            $html.= '<tr>';
            $html.= '<td>'. $page['landing_id'] .'</td>';
			
			$name_cat = '';
			foreach($domain_select as $id => $name):
				if($id == $page['domain'])
					$name_cat = $name;
            endforeach;
			$html.= '<td>'. $name_cat .'</td>';
            $html.= '<td>'. $page['name'] .'</td>';
            $html.= '<td>'. $page['category_name'] .'</td>';
            $html.= '<td>'. $page['etat'] .'</td>';
            $html.= '<td>'. $page['langs'] .'</td>';
            $html.= '<td>';
            $html.= '<div class="btn-group margin-left">';
            $html.= '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
            $html.= '<ul class="dropdown-menu pull-right">';
            if($page['active'] == 1 && !$page['hidden_page']){
                $html.= '<li>';
             /*  $html.= $this->Html->link('<span class="icon-zoom-in"></span> '.__('Voir la page'),
                    array(
                        'language'          => $this->Session->read('Config.language'),
                        'controller'        => 'landings',
                        'action'            => 'display',
                        'admin'             => false,
                        'id'                => $page['landing_id'],
                        'link_rewrite'      => $page['link_rewrite']
                    ),
                    array('escape' => false, 'class' => 'btn green', 'target' => '_blank')
                );*/
				
					switch ($page['domain']) {
						case 19:
							$lang_cat = 'fre';
							break;
						case 11:
							$lang_cat = 'frb';
							break;
						case 13:
							$lang_cat = 'frs';
							break;
						case 22:
							$lang_cat = 'frl';
							break;
						case 29:
							$lang_cat = 'frc';
							break;
					}
				$html.= '<a target="_blank" class="btn green" href="https://'.$name_cat.'/'.$lang_cat.'/'.'voyant-medium'.'/'.$page['link_rewrite'].'-'.$page['landing_id'].'" >'.'<span class="icon-zoom-in"></span> '.__('Voir la page').'</a>';
                $html.= '</li>';
            }
            $html.= '<li>';
            $html.= $this->Metronic->getLinkButton(
                __('Modifier'),
                array('controller' => 'landings','action' => 'edit', 'admin' => true, 'id' => $page['landing_id']),
                'btn blue',
                'icon-edit');
            $html.= '</li>';
			$html.= '<li>';
            $html.= $this->Metronic->getLinkButton(
                __('Dupliquer'),
                array('controller' => 'landings','action' => 'duplicate', 'admin' => true, 'id' => $page['landing_id']),
                'btn blue',
                'icon-edit');
            $html.= '</li>';

           // if (!$page['hidden_page']){
                $html.= '<li>';
                if($page['active'])
                    $html.= $this->Metronic->getLinkButton(
                        __('Désactiver'),
                        array('controller' => 'landings','action' => 'delete', 'admin' => true, 'id' => $page['landing_id']),
                        'btn yellow',
                        'icon-remove',
                        __('Voulez-vous vraiment désactiver la page ').$page['name'].' ?');
                else
                    $html.= $this->Metronic->getLinkButton(
                        __('Activer'),
                        array('controller' => 'landings','action' => 'add', 'admin' => true, 'id' => $page['landing_id']),
                        'btn green',
                        'icon-plus',
                        __('Voulez-vous vraiment activer la page ').$page['name'].' ?');
                $html.= '</li>';



                $html.= '<li>';
                $html.= $this->Metronic->getLinkButton(
                    __('Supprimer'),
                    array('controller' => 'landings','action' => 'true_delete', 'admin' => true, 'id' => $page['landing_id']),
                    'btn red',
                    'icon-remove',
                    __('Voulez-vous vraiment supprimer définitivement la page ').$page['name'].' ?');
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
        0 => array('text'       => __('Landings'),
            'icon'       => 'icon-list',
            'content'    => $html
        ),
        1 => array('text'       => __('Nouvelle page'),
            'icon'       => 'icon-plus',
            'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['Landing']))?1:0);
                            
                            

