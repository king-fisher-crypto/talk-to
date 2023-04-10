<?php
App::uses('AppHelper', 'View/Helper');

class MetronicHelper extends HtmlHelper {
    var $helpers = array('Html','Form', 'Time', 'Session');
    /* Breadcrumb
     * fournir tableau de paramètres :
     * array('text'    =>   '',
     *       'link'    =>   '',
     *       'classes' =>   '',
     *       'target'  =>   '_blank'
     *      )
     */
    public function getSimpleTable($table=array(), $fieldSelection=array(), $callback=false, $caller=false, $tdAtribute=array(), $tdCallbackClass='', $callbackEdit=false, $afterField='', $editClass='', $thColspan=array())
    {
        $html = '<table class="table table-bordered table-striped">';

        $fields = array();
        foreach ($table AS $k => $v){
            foreach ($v AS $k2 => $v2)
                if (empty($fieldSelection) || in_array($k2, array_keys($fieldSelection)))
                    $fields[] = in_array($k2, array_keys($fieldSelection))?$fieldSelection[$k2]:$k2;
            break;
        }

        $html.= '<thead><tr>';
        foreach ($fields AS $v)
            $html.= '<th'.(isset($thColspan[$v])?' colspan="'.$thColspan[$v].'"':'').'>'.$v.'</th>';
        $html.= '</tr></thead>';

        $html.= '<tbody>';
        foreach ($table AS $v){
            $html.= (isset($v['id'])?'<tr id="'.$v['id'].'">':'<tr>');
            foreach ($v AS $k2 => $v2){
                if (empty($fieldSelection) || in_array($k2, array_keys($fieldSelection)))
                    $html.= '<td'.(isset($tdAtribute[$k2])
                            ?' '.$tdAtribute[$k2][0].(isset($tdAtribute[$k2][1]) && $tdAtribute[$k2][1] === 'id'
                                ?' id="'.$v['id'].'"'
                                :'')
                            :''
                        ).'>'.$v2.
                    '</td>';
                    //$html.= '<td'.(isset($tdAtribute[$k2])?' '.$tdAtribute[$k2]:'').'>'.$v2.'</td>';
                if($callbackEdit && $k2 === $afterField)
                    $html.= '<td'.(!empty($editClass)?' class="'.$editClass.'"':'').'>'.call_user_func($callbackEdit, $v, $caller).'</td>';
            }
            if ($callback)
                $html.= '<td'.(!empty($tdCallbackClass)?' class="'.$tdCallbackClass.'"':'').'>'.call_user_func($callback, $v, $caller).'</td>';

            $html.= '</tr>';
        }
        $html.= '</tbody>';

        $html.= '</table>';
        return $html;
    }
    public function getLinkButton($text="", $linkParms=array(), $classes='', $btnIcon="icon-edit", $confirmMessage=false)
    {
        $classes = explode(" ",$classes);
        if (!in_array('btn', $classes))
            $classes[] = 'btn';

        return $this->link(
                        $this->tag('span', '', array('class' => $btnIcon)).' '.$text,
                        $linkParms,
                        array('class' => $classes, 'escape' => false),
                        $confirmMessage
                        );
    }
    public function getTabs($tabs=array(), $index=0, $icon=false, $taille=12)
    {
	// FIXME: might not work. We should use a static variable instead
        $rand = rand(1111111111,99999999);

        $html = '<div class="row-fluid">';
        $html.= '<div class="span12"><div class="tabbable tabbable-custom tabbable-full-width">';
        $html.= '<ul class="nav nav-tabs">';

        foreach ($tabs AS $k => $tab){
            $html.= '<li'.($k==$index?' class="active"':'').'><a data-toggle="tab" href="#tab_'.$rand.'_'.$k.'">'.(isset($tab['icon'])?'<span class="'.$tab['icon'].'"></span> ':'').$tab['text'].'</a></li>';
        }
        $html.= '</ul>';

        $html.= '<div class="tab-content">';
        foreach ($tabs AS $k => $tab){
           $html.= '<div id="tab_'.$rand.'_'.$k.'" class="tab-pane'.($k==$index?' active':'').'">';
           $html.= $tab['content'];
           $html.= '</div>';
        }
        $html.= '</div>';


        $html.= '</div></div>';
        $html.= '</div>';
        return $html;
    }

    public function getElementSidebar($badge){

		$user_co = $this->Session->read('Auth.User');
		//admin level
		App::import("Model", "UserLevel");
		App::import("Model", "UserLevelAcl");
		$menu_auth = array();
		if (class_exists('UserLevel')) {
			$user_level = new UserLevel();
			$level = $user_level->find('first', array(
						'conditions' => array('UserLevel.user_id' => $user_co['id']),
						'recursive' => -1
					));
			$level = isset($level['UserLevel']['level']) ? $level['UserLevel']['level'] : null;
		}else{
			$user_level = '';
			$level = '';
		}
		if (class_exists('UserLevelAcl') && $level) {
			$user_level_acl = new UserLevelAcl();
			$levelauths = $user_level_acl->find('all', array(
						'conditions' => array('UserLevelAcl.level' => $level, 'UserLevelAcl.auth' => 1),
						'recursive' => -1
					));


			foreach( $levelauths as $leve){
				array_push($menu_auth,$leve['UserLevelAcl']['menu']);
			}
		}else{
			$user_level_acl = '';
		}

		if(!count($menu_auth)){

			switch ($user_co['id']) {
				case 1:
				case 323:
				case 191:
				case 198:
				case 204:
				case 495:
					$menu_auth = array('Accueil','Supervision','Note','Messagerie','Agent','Client','Avis','Paiement','Produit','Reduction','CRM','Parrainage','Export','Contenu','Slide','Template','Horoscope','Admin','Traductions');
					break;
				case 10904:
				case 16354:
					$menu_auth = array('Accueil','CRM','Contenu','Export','Reduction','Slide','Traductions');
					break;
				case 9155:
					$menu_auth = array('Accueil','CRM','Contenu','Export','Reduction','Slide','Traductions');
					break;
			}
		}

        $elements = array();
		/* Menu Accueil */
		if(in_array('Accueil',$menu_auth)){

			$children = array();
			$children[] = array(
				'text'      => __('Dashboard'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Clients'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard_customer'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard_customer', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Tunnel vente'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard_tunnel'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard_tunnel', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Achats'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard_credit'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard_credit', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Consultations'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard_consult'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard_consult', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('CRM'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard_crm'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard_crm', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('SMS'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_dashboard_sms'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'dashboard_sms', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Stats par date'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_stats'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'stats', 'admin' => true))
			);


        $elements[] = array(
            'text'      => __('Accueil'),
            'classes'   => 'icon-home',
            'link'      => $this->Html->url(array('controller' => 'admins','action' => 'index', 'admin' => true), true),
			'children'  => $children
        );
		}
		/* Fin Menu Accueil */

		/* Menu Supervision */
		if(in_array('Supervision',$menu_auth)){
		$children = array();
		$children[] = array(
            'text'      => __('Live Communications'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_livecom'?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'livecom', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Présence experts'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_consults_agent'?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'consults_agent', 'admin' => true))
        );
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Communication agents'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_com'?1:0),
				'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'com', 'admin' => true))
			);
		}
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Communication clients'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_com'?1:0),
				'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'com', 'admin' => true))
			);
		}
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Communication CA'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_com'?1:0),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'com', 'admin' => true))
			);
		}
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Achat de crédit'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_credit'?1:0),
				'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'credit', 'admin' => true))
			);
		}
        $children[] = array(
            'text'      => __('Messageries internes'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchmessages', 'admin' => true)),
            'badge'     => $badge['Message']['etat_prive'] ,
            'badge_name'=> ($badge['Message']['etat_prive'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchmessages')
        );
		$children[] = array(
            'text'      => __('Mails payants'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchmails', 'admin' => true)),
            'badge'     => $badge['Message']['etat_mail'],
            'badge_name'=> ($badge['Message']['etat_mail'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchmails')
        );
		$children[] = array(
            'text'      => __('Mails remboursés'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchlostmails', 'admin' => true)),
            'badge'     => '',
            'badge_name'=> '',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchlostmails')
        );
		$children[] = array(
            'text'      => __('Relances agent client'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchrelance', 'admin' => true)),
            'badge'     => $badge['Message']['etat_relance'],
            'badge_name'=> ($badge['Message']['etat_relance'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchrelance')
        );
		$children[] = array(
            'text'      => __('Relances refusées'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchrelancerefus', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchrelancerefus')
        );
		$children[] = array(
            'text'      => __('Filtrage messages'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'filtersmessage', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchmails')
        );
		$children[] = array(
            'text'      => __('RDV Clients'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchtrdv', 'admin' => true)),
            'badge'     => $badge['CustomerAppointment']['status'] ,
            'badge_name'=> ($badge['CustomerAppointment']['status'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchtrdv')
        );
     $children[] = array(
            'text'      => __('Croisement opposition'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchopposed', 'admin' => true)),
            'badge'     => null ,
            'badge_name'=> false,
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchopposed')
        );
		 $children[] = array(
            'text'      => __('Tchat'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'watchtchat', 'admin' => true)),
            'badge'     => $badge['Chat']['etat'] ,
            'badge_name'=> ($badge['Chat']['etat'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'watchtchat')
        );

		$children[] = array(
            'text'      => __('Callinfo'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'callinfosview', 'admin' => true)),
            'badge'     => '',
            'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'callinfosview')
        );
		$children[] = array(
            'text'      => __('Appels perdus'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'penality' && $this->params['action'] == 'admin_comlostcall'?1:0),
            'link'      => $this->Html->url(array('controller' => 'penality', 'action' => 'comlostcall', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Tchats perdus'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'penality' && $this->params['action'] == 'admin_comlosttchat'?1:0),
            'link'      => $this->Html->url(array('controller' => 'penality', 'action' => 'comlosttchat', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Email perdus'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'penality' && $this->params['action'] == 'admin_comlostmessage'?1:0),
            'link'      => $this->Html->url(array('controller' => 'penality', 'action' => 'comlostmessage', 'admin' => true))
        );
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Avis client'),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'avis', 'admin' => true)),
				'badge'     => '',
				'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'avis')
			);


		}

		$children[] = array(
            'text'      => __('Mot de passe Reset'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'newpassword', 'admin' => true)),
            'badge'     => '',
            'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'newpassword')
        );

		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Agent connecté'),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'agent_connected', 'admin' => true)),
				'badge'     => '',
				'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'agent_connected')
			);
		}
		$children[] = array(
            'text'      => __('Alertes envoyées'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'alert_send', 'admin' => true)),
            'badge'     => '',
            'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'alert_send')
        );

		$children[] = array(
            'text'      => __('SMS envoyés'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'sms_send', 'admin' => true)),
            'badge'     => '',
            'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'sms_send')
        );

		$children[] = array(
            'text'      => __('Contacter agents'),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'contact_expert', 'admin' => true)),
            'badge'     => '',
            'badge_name'=> 0,
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'contact_expert')
        );
		
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Sales reconciliation'),
				'link'      => $this->Html->url(array('controller' => 'sales', 'action' => 'index', 'admin' => true)),
				'badge'     => '',
				'badge_name'=> 0,
				'selected'  => ($this->params['controller'] == 'sales' && $this->params['action'] == 'index')
			);
			
			$children[] = array(
				'text'      => __('Logs'),
				'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'logs', 'admin' => true)),
				'badge'     => '',
				'badge_name'=> 0,
				'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'logs')
			);
		}

        $actions = array('admin_com','admin_credit','admin_credit_view','admin_com_view','admin_watchmails','admin_watchmessages','admin_watchtchat', 'admin_avis', 'admin_agent_connected', 'admin_sms_send', 'admin_alert_send','admin_comlostcall','admin_comlosttchat','admin_comlostmessage','contact_expert','watchtrdv','logs','newpassword');
       	$badgestatstotal = $badge['Chat']['etat'] + $badge['Message']['etat_mail'] + $badge['Message']['etat_prive'] + $badge['Message']['etat_relance'] + $badge['CustomerAppointment']['status'];
	    $elements[] = array(
            'text'      => __('Supervision'),
            'classes'   => 'icon-bar-chart',
            'link'      => '',
			'badge'		=> $badgestatstotal,
			'badge_name'=> ($badgestatstotal == 0 ?false:'badge-important'),
            'selected'  => (in_array($this->params['action'],$actions)?1:0),
            'children'  => $children
        );
		}
		/* Fin Menu Supervision */

		/* Menu Note */
		if(in_array('Note',$menu_auth)){
		$elements[] = array(
            'text'      => __('Note'),
            'classes'   => 'icon-pencil',
            'link'      => $this->Html->url(array('controller' => 'admins','action' => 'blocnote', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'blocnote')?1:0,
            'children'  => array()
        );
		}
		/* Fin Menu Note */


		/* Menu Support */
		if(in_array('Support',$menu_auth)){
			$children = array();
			$children[] = array(
				'text'      => __('Messagerie'),
				'classes'   => 'icon-envelope',
				'badge'     => $badge['Support']['count'],
				'badge_name'=> ($badge['Support']['count'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_message'?1:0),
				'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'message', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Ecrire'),
				'classes'   => 'icon-pencil',
				'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_write'?1:0),
				'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'write', 'admin' => true))
			);
			$children[] = array(
					'text'      => __('Traitement'),
					'classes'   => 'icon-asterisk',
					'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_user'?1:0),
					'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'treatment', 'admin' => true))
				);
			if($level != 'moderator' || $user_co['id'] == 71793){
				$children[] = array(
					'text'      => __('Classifications'),
					'classes'   => 'icon-plus-sign',
					'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_user'?1:0),
					'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'classification', 'admin' => true))
				);
				$children[] = array(
					'text'      => __('Classifications parent'),
					'classes'   => 'icon-plus-sign',
					'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_user'?1:0),
					'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'classification_parent', 'admin' => true))
				);
			}
		if($level != 'moderator'){		
				$children[] = array(
					'text'      => __('Service'),
					'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_service' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'service', 'admin' => true)),
					'classes'   => 'icon-plus-sign'
				);

				$children[] = array(
					'text'      => __('Administrateur'),
					'classes'   => 'icon-plus-sign',
					'selected'  => ($this->params['controller'] == 'support' && $this->params['action'] == 'admin_user'?1:0),
					'link'      => $this->Html->url(array('controller' => 'support', 'action' => 'user', 'admin' => true))
				);
			}
			$elements[] = array(
				'text'      => __('Support'),
				'classes'   => 'icon-envelope',
				'link'      => '',
				'badge'     => $badge['Support']['count'],
				'badge_name'=> ($badge['Support']['count'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'support' ?1:0),
				'children'  => $children
			);
		}
		/* Fin Menu Support */

		/* Menu Agent */
		if(in_array('Agent',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Les Agents'),
            'badge'     => $badge['Agent']['count'],
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true),true),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_index' && !isset($this->request->query['email']) && !isset($this->request->query['compte'])?1:0)
        );
        $children[] = array(
            'text'      => __('Email non confirmé'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true, '?' => 'email')),
            'badge'     => $badge['Agent']['email'],
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_index' && isset($this->request->query['email'])?1:0)
        );
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Validation en attente'),
				'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true, '?' => 'compte')),
				'badge'     => $badge['Agent']['compte'],
				'badge_name'=> ($badge['Agent']['compte'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_index' && isset($this->request->query['compte'])?1:0)
			);
		}
		$children[] = array(
				'text'      => __('Questionnaires'),
				'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'survey_list', 'admin' => true)),
				'badge'     => $badge['Survey']['is_respons'],
				'badge_name'=> ($badge['Survey']['is_respons'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_survey_list')
		);
        $childrenOfUnivers = array();
		if($level != 'moderator'){
			$childrenOfUnivers[] = array(
				'text'      => __('Informations'),
				'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_info', 'admin' => true)),
				'badge'     => $badge['ValidAgent']['info'],
				'badge_name'=> ($badge['ValidAgent']['info'] == 0 ?false:'badge-important'),
				'selected'  => ($this->params['controller'] == 'agents' && ($this->params['action'] == 'admin_valid_info' || $this->params['action'] == 'admin_valid_info_view')?1:0)
			);
		}
        $childrenOfUnivers[] = array(
            'text'      => __('Présentations'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_presentation', 'admin' => true)),
            'badge'     => $badge['ValidAgent']['presentation'],
            'badge_name'=> ($badge['ValidAgent']['presentation'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_valid_presentation'?1:0)
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Photos'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_photo', 'admin' => true)),
            'badge'     => $badge['ValidAgent']['photo'],
            'badge_name'=> ($badge['ValidAgent']['photo'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_valid_photo'?1:0)
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Présentations audio'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_audio', 'admin' => true)),
            'badge'     => $badge['ValidAgent']['audio'],
            'badge_name'=> ($badge['ValidAgent']['audio'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_valid_audio'?1:0)
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Présentations video'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_video', 'admin' => true)),
            'badge'     => $badge['ValidAgent']['video'],
            'badge_name'=> ($badge['ValidAgent']['video'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_valid_video'?1:0)
        );
		$childrenOfUnivers[] = array(
            'text'      => __('Infos Email'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_mailinfos', 'admin' => true)),
            'badge'     => $badge['ValidAgent']['mailinfos'],
            'badge_name'=> ($badge['ValidAgent']['mailinfos'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_valid_mailinfos'?1:0)
        );

        $fullBadge = $badge['ValidAgent']['info'] + $badge['ValidAgent']['presentation'] + $badge['ValidAgent']['photo'] + $badge['ValidAgent']['audio'] + $badge['ValidAgent']['video'] + $badge['ValidAgent']['mailinfos'];
        $actions = array('admin_valid_info','admin_valid_info_view','admin_valid_presentation','admin_valid_photo','admin_valid_audio','admin_valid_video','admin_valid_mailinfos');
        $children[] = array(
            'text'      => __('Données en attente'),
            'link'      => '',
            'badge'     => $fullBadge,
            'badge_name'=> ($fullBadge == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && in_array($this->params['action'],$actions)?1:0),
            'children'  => $childrenOfUnivers
        );
		 $children[] = array(
            'text'      => __('Info administrateurs'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'message_absent', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_message_absent'),
            'badge'     => $badge['AgentMessage']['count'],
			'badge_name'=> ($badge['AgentMessage']['count'] == 0 ?false:'badge-important'),
        );
	    if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Fichiers Audio'),
				'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true)),
				'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_record_audio'),
				'badge'     => $badge['Record']['count']
			);

			$children[] = array(
				'text'      => __('Audio archive'),
				'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'record_audio_archive', 'admin' => true)),
				'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_record_audio_archive'),
				'badge'     => $badge['Record']['count_archive']
			);
		}
		$children[] = array(
            'text'      => __('Présentations audio'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'present_audio', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_present_audio'?1:0)
        );
		$children[] = array(
            'text'      => __('Notes'),
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'notes_client', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_notes_client'),
        );
		if($level != 'moderator'){
			$childrenOfUnivers = array();
			$childrenOfUnivers[] = array(
				'text'      => __('Créer'),
				'selected'  => ($this->params['controller'] == 'loyalty' && $this->params['action'] == 'admin_create' ?1:0),
				'link'      => $this->Html->url(array('controller' => 'loyalty', 'action' => 'create', 'admin' => true)),
				'classes'   => 'icon-plus-sign'
			);
			$childrenOfUnivers[] = array(
				'text'      => __('Liste'),
				'classes'   => 'icon-list',
				'selected'  => ($this->params['controller'] == 'loyalty' && $this->params['action'] == 'admin_list' ?1:0),
				'link'      => $this->Html->url(array('controller' => 'loyalty', 'action' => 'list', 'admin' => true))
			);


			$children[] = array(
				'text'      => __('Fidélité'),
				'link'      => '',
				'selected'  => ($this->params['controller'] == 'loyalty' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
				'children'  => $childrenOfUnivers
			);
		}
		if($level != 'support'){
			if($level != 'moderator'){
				$childrenOfUnivers = array();
				$childrenOfUnivers[] = array(
					 'text'      => __('Créer'),
					'selected'  => ($this->params['controller'] == 'bonus' && $this->params['action'] == 'admin_create' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'bonus', 'action' => 'create', 'admin' => true)),
					'classes'   => 'icon-plus-sign'
				);
				$childrenOfUnivers[] = array(
					'text'      => __('Liste'),
					'classes'   => 'icon-list',
					'selected'  => ($this->params['controller'] == 'bonus' && $this->params['action'] == 'admin_list' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'bonus', 'action' => 'list', 'admin' => true))
				);


				$children[] = array(
					'text'      => __('Bonus Agent'),
					'link'      => '',
					'selected'  => ($this->params['controller'] == 'bonus' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
					'children'  => $childrenOfUnivers
				);
			}
			if($level != 'moderator'){
				$childrenOfUnivers = array();
				$childrenOfUnivers[] = array(
					'text'      => __('Créer'),
					'selected'  => ($this->params['controller'] == 'cost' && $this->params['action'] == 'admin_create' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'cost', 'action' => 'create', 'admin' => true)),
					'classes'   => 'icon-plus-sign'
				);
				$childrenOfUnivers[] = array(
					'text'      => __('Liste'),
					'classes'   => 'icon-list',
					'selected'  => ($this->params['controller'] == 'cost' && $this->params['action'] == 'admin_list' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'cost', 'action' => 'list', 'admin' => true))
				);

				$childrenOfUnivers[] = array(
					'text'      => __('Cout téléphone'),
					'classes'   => 'icon-list',
					'selected'  => ($this->params['controller'] == 'cost' && $this->params['action'] == 'admin_list_phone' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'cost', 'action' => 'list_phone', 'admin' => true))
				);


				$children[] = array(
					'text'      => __('Rémunération'),
					'link'      => '',
					'selected'  => ($this->params['controller'] == 'cost' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
					'children'  => $childrenOfUnivers
				);
			}
			if($level != 'moderator'){
				$children[] = array(
					'text'      => __('Modif Facture'),
					'classes'   => '',
					'selected'  => ($this->params['controller'] == 'user_order' && $this->params['action'] == 'admin_index')?1:0,
					'badge'     => '',
					'link'      => $this->Html->url(array('controller' => 'user_order', 'action' => 'index', 'admin' => true))
				);
			}
			if($level != 'moderator'){
				$children[] = array(
					'text'      => __('Facturation'),
					'classes'   => '',
					'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_order')?1:0,
					'badge'     => '',
					'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'admin_order', 'admin' => true))
				);
			}
			if($level != 'moderator'){
				$childrenOfUnivers = array();
				$childrenOfUnivers[] = array(
					'text'      => __('Créer'),
					'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_voucher_create' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'voucher_create', 'admin' => true)),
					'classes'   => 'icon-plus-sign'
				);
				$childrenOfUnivers[] = array(
					'text'      => __('Liste'),
					'classes'   => 'icon-list',
					'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_voucher_list' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'voucher_list', 'admin' => true))
				);


				$children[] = array(
					'text'      => __('Avoirs'),
					'link'      => '',
					'selected'  => ($this->params['controller'] == 'agents' && in_array($this->params['action'], array('admin_voucher_create', 'admin_voucher_edit', 'admin_voucher_list')) ?1:0),
					'children'  => $childrenOfUnivers
				);
			}
			if($level != 'moderator'){
				$children[] = array(
					'text'      => __('Fonds roulement'),
					'classes'   => '',
					'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_stripe_balance')?1:0,
					'badge'     => '',
					'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'admin_stripe_balance', 'admin' => true))
				);
			}
			if($level != 'moderator'){
				$children[] = array(
					'text'      => __('Primes'),
					'classes'   => '',
					'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_prime')?1:0,
					'badge'     => '',
					'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'prime', 'admin' => true))
				);
			}
			if($level != 'moderator'){
				$children[] = array(
					'text'      => __('Pénalités'),
					'classes'   => '',
					'selected'  => ($this->params['controller'] == 'penality' && $this->params['action'] == 'admin_penalities')?1:0,
					'badge'     => '',
					'link'      => $this->Html->url(array('controller' => 'penality', 'action' => 'penalities', 'admin' => true))
				);
			}
			if($level != 'moderator'){
				$childrenOfUnivers = array();
				$childrenOfUnivers[] = array(
					'text'      => __('Créer'),
					'selected'  => ($this->params['controller'] == 'vat' && $this->params['action'] == 'admin_create' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'vat', 'action' => 'create', 'admin' => true)),
					'classes'   => 'icon-plus-sign'
				);
				$childrenOfUnivers[] = array(
					'text'      => __('Liste'),
					'classes'   => 'icon-list',
					'selected'  => ($this->params['controller'] == 'vat' && $this->params['action'] == 'admin_list' ?1:0),
					'link'      => $this->Html->url(array('controller' => 'vat', 'action' => 'list', 'admin' => true))
				);

				$childrenOfUnivers[] = array(
					'text'      => __('Experts'),
					'classes'   => '',
					'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_vat')?1:0,
					'badge'     => '',
					'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'vat', 'admin' => true))
				);


				$children[] = array(
					'text'      => __('TVA'),
					'link'      => '',
					'selected'  => ($this->params['controller'] == 'vat' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
					'children'  => $childrenOfUnivers
				);
			}
		}
		$children[] = array(
            'text'      => __('Compte supprimé'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'agents' && $this->params['action'] == 'admin_agent_deleted')?1:0,
            'badge'     => '',
            'link'      => $this->Html->url(array('controller' => 'agents', 'action' => 'agent_deleted', 'admin' => true))
        );

       

        $actions = array('admin_com', 'admin_com_view','admin_agent_deleted','admin_order','admin_penalities','admin_stripe_balance');
        $elements[] = array(
            'text'      => __('Agents (experts)'),
            'classes'   => 'icon-user-md',
            'link'      => '/',
            'badge'     => ($badge['Agent']['compte'] + $fullBadge),
            'badge_name'=> (($badge['Agent']['compte'] + $fullBadge) == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'agents' && !in_array($this->params['action'],$actions)?1:0),
            'children'  => $children
        );
		}
        /* Fin menu Agent */

		/* Menu Client */
		if(in_array('Client',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Les clients'),
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'index', 'admin' => true)),
            'badge'     => $badge['Client']['count'],
            'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_index' && !isset($this->request->query['email']) && !isset($this->request->query['compte'])?1:0)
        );
        $children[] = array(
            'text'      => __('Email non confirmé'),
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'index', 'admin' => true, '?' => 'email')),
            'classes'   => '',
            'badge'     => $badge['Client']['email'],
            'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_index' && isset($this->request->query['email'])?1:0)
        );
        $children[] = array(
            'text'      => __('Compte non validé'),
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'index', 'admin' => true, '?' => 'compte')),
            'classes'   => '',
            'badge'     => $badge['Client']['compte'],
            'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_index' && isset($this->request->query['compte'])?1:0)
        );
		$children[] = array(
            'text'      => __('Compte supprimé'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_account_deleted')?1:0,
            'badge'     => '',
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'account_deleted', 'admin' => true))
        );

		$children[] = array(
            'text'      => __('Inscrits newsletter'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_account_subscribe')?1:0,
            'badge'     => '',
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'account_subscribe', 'admin' => true))
        );
			
		$children[] = array(
            'text'      => __('Points fidélité'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_account_loyalty')?1:0,
            'badge'     => '',
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'account_loyalty', 'admin' => true))
        );

        $actions = array('admin_credit','admin_credit_view','admin_com','admin_com_view','admin_account_deleted','admin_account_subscribe','admin_account_loyalty');
        $elements[] = array(
            'text'      => __('Clients'),
            'classes'   => 'icon-user',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'accounts' && !in_array($this->params['action'],$actions)?1:0),
            'children'  => $children
        );
		}
        /* Fin menu Client */

		/* Menu Avis */
		if(in_array('Avis',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Avis en publié'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'reviews' && $this->params['action'] == 'admin_index' && isset($this->request->query['online'])?1:0),
            'badge'     => $badge['Review']['online'],
            'link'      => $this->Html->url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true, '?' => 'online'))
        );
        $children[] = array(
            'text'      => __('Avis 5* en attente'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'reviews' && $this->params['action'] == 'admin_index' && !isset($this->request->query['refuse']) && !isset($this->request->query['online']) ?1:0),
            'badge'     => $badge['Review']['count'],
            'badge_name'=> ($badge['Review']['count'] == 0 ?false:'badge-important'),
            'link'      => $this->Html->url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true))
        );
		$countbad = $badge['Review']['count_bad']+$badge['Review']['count_bad2'];
		$children[] = array(
				'text'      => __('Avis <5* en attente'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'reviews' && $this->params['action'] == 'admin_index_bad'),
				'badge'     => $countbad,
				'badge_name'=> ($countbad == 0 ?false:'badge-important'),
				'link'      => $this->Html->url(array('controller' => 'reviews', 'action' => 'index_bad', 'admin' => true))
		);
		$children[] = array(
				'text'      => __('Reponses en attente'),
				'classes'   => '',
				'selected'  => ($this->params['controller'] == 'reviews' && $this->params['action'] == 'admin_index_resp'),
				'badge'     => $badge['Review']['count_resp'],
				'badge_name'=> ($badge['Review']['count_resp'] == 0 ?false:'badge-important'),
				'link'      => $this->Html->url(array('controller' => 'reviews', 'action' => 'index_resp', 'admin' => true))
		);

		if($level != 'moderator'){
        $children[] = array(
            'text'      => __('Avis refusés'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'reviews' && $this->params['action'] == 'admin_index' && isset($this->request->query['refuse'])?1:0),
            'badge'     => $badge['Review']['refuse'],
            'link'      => $this->Html->url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true, '?' => 'refuse'))
        );
		}
        $elements[] = array(
            'text'      => __('Avis clients'),
            'classes'   => 'icon-comments',
            'link'      => '',
            'badge'     => $badge['Review']['count'] + $badge['Review']['count_bad'] + $badge['Review']['count_bad2'] + $badge['Review']['count_resp'],
            'badge_name'=> (($badge['Review']['count'] + $badge['Review']['count_bad'] + $badge['Review']['count_bad2'] + $badge['Review']['count_resp']) == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'reviews'?1:0),
            'children'  => $children
        );
		}
        /* Fin menu Avis */

		/* Menu Paiement */
		if(in_array('Paiement',$menu_auth)){
        $children = array();
		$children[] = array(
            'text'      => __('Coupons'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentcoupons' && $this->params['action'] == 'admin_index' && !isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['coupon']['notactive'],
			'badge_name'=> ($badge['Order']['coupon']['notactive'] == 0 ?false:'badge-important'),
            'link'      => $this->Html->url(array('controller' => 'paymentcoupons', 'action' => 'index', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('Virements en attente'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentbankwire' && $this->params['action'] == 'admin_index' && !isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['bankwire']['notactive'],
            'badge_name'=> ($badge['Order']['bankwire']['notactive'] == 0 ?false:'badge-important'),
            'link'      => $this->Html->url(array('controller' => 'paymentbankwire', 'action' => 'index', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('Virements validés'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentbankwire' && $this->params['action'] == 'admin_index' && isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['bankwire']['active'],
            'link'      => $this->Html->url(array('controller' => 'paymentbankwire', 'action' => 'index', '?' => 'valid', 'admin' => true))
        );

		$children[] = array(
            'text'      => __('SEPA en attente'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentsepa' && $this->params['action'] == 'admin_index' && !isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['sepa']['notactive'],
            'badge_name'=> ($badge['Order']['sepa']['notactive'] == 0 ?false:'badge-important'),
            'link'      => $this->Html->url(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('SEPA validés'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentsepa' && $this->params['action'] == 'admin_index' && isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['sepa']['active'],
            'link'      => $this->Html->url(array('controller' => 'paymentsepa', 'action' => 'index', '?' => 'valid', 'admin' => true))
        );

		$children[] = array(
            'text'      => __('Stripe validés'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentstripe' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos']) && isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['paymentstripe']['active'],
            'link'      => $this->Html->url(array('controller' => 'paymentstripe', 'action' => 'index', '?' => 'valid', 'admin' => true))
        );

		$children[] = array(
            'text'      => __('Stripe opposées'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentstripe' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos']) && isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['paymentstripe']['oppose'],
            'link'      => $this->Html->url(array('controller' => 'paymentstripe', 'action' => 'index', '?' => 'oppos', 'admin' => true))
        );
        /*
        $children[] = array(
            'text'      => __('Hipay validés'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymenthipay' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos']) && isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['paymenthipay']['active'],
            'link'      => $this->Html->url(array('controller' => 'paymenthipay', 'action' => 'index', '?' => 'valid', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('Hipay erreur'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymenthipay' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos']) && !isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['paymenthipay']['notactive'],
            'badge_name'=> $badge['Order']['paymenthipay']['notactive'],
            'link'      => $this->Html->url(array('controller' => 'paymenthipay', 'action' => 'index', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Hipay opposées'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymenthipay' && $this->params['action'] == 'admin_index' && isset($this->params->query['oppos']))?1:0,
            'badge'     => $badge['Order']['paymenthipay']['oppose'],
            'link'      => $this->Html->url(array('controller' => 'paymenthipay', 'action' => 'index', '?' => 'oppos', 'admin' => true))
        );
        */

        $children[] = array(
            'text'      => __('Paypal validées'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentpaypal' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos'])  && isset($this->params->query['valid']))?1:0,
            'badge'     => $badge['Order']['paymentpaypal']['active'],
            'link'      => $this->Html->url(array('controller' => 'paymentpaypal', 'action' => 'index', '?' => 'valid', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('Paypal attente'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentpaypal' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos'])  && isset($this->params->query['pending']))?1:0,
            'badge'     => $badge['Order']['paymentpaypal']['pending'],
            'badge_name'=> $badge['Order']['paymentpaypal']['pending'],
            'link'      => $this->Html->url(array('controller' => 'paymentpaypal', 'action' => 'index', '?' => 'pending', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('Paypal erreur'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentpaypal' && $this->params['action'] == 'admin_index' && !isset($this->params->query['oppos'])  && !isset($this->params->query['valid']) && !isset($this->params->query['pending']))?1:0,
            'badge'     => $badge['Order']['paymentpaypal']['notactive'],
            'badge_name'=> $badge['Order']['paymentpaypal']['notactive'],
            'link'      => $this->Html->url(array('controller' => 'paymentpaypal', 'action' => 'index', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Paypal opposées'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'paymentpaypal' && $this->params['action'] == 'admin_index' && isset($this->params->query['oppos']) && !isset($this->params->query['valid']) && !isset($this->params->query['pending']))?1:0,
            'badge'     => $badge['Order']['paymentpaypal']['oppose'],
            'link'      => $this->Html->url(array('controller' => 'paymentpaypal', 'action' => 'index', '?' => 'oppos', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Remboursement'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'refund' && $this->params['action'] == 'admin_index')?1:0,
            'badge'     => '',
            'link'      => $this->Html->url(array('controller' => 'refund', 'action' => 'index', 'admin' => true))
        );

		$badgetotal = $badge['Order']['bankwire']['notactive'] + $badge['Order']['sepa']['notactive']  + $badge['Order']['coupon']['notactive'];
        $elements[] = array(
            'text'      => __('Paiements'),
            'classes'   => 'icon-euro',
            'link'      => '',
            'badge'     => $badgetotal,
            'badge_name'=> (($badge['Order']['bankwire']['notactive'] + $badge['Order']['sepa']['notactive']) == 0 ?false:'badge-important'),
            'selected'  => (strpos($this->params['controller'],'payment')!==false)?1:0,
            'children'  => $children
        );
		}
		/* Fin Menu Paiement */

		/* Menu Produit */
		if(in_array('Produit',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'products' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'products', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'products' && $this->params['action'] == 'admin_index' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'products', 'action' => 'index', 'admin' => true))
        );

        $elements[] = array(
            'text'      => __('Produits'),
            'classes'   => 'icon-euro',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'products' ?1:0),
            'children'  => $children
        );
		}
        /* Fin menu produit */

		/* Menu Facture */
		if(in_array('Facture',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'invoices' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'invoices', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'invoices' && $this->params['action'] == 'admin_index' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'invoices', 'action' => 'index', 'admin' => true))
        );

        $elements[] = array(
            'text'      => __('Factures'),
            'classes'   => 'icon-euro',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'invoices' ?1:0),
            'children'  => $children
        );
		}
        /* Fin menu Facture */

		/* Menu Bon cadeau */
        /*
		if(in_array('Cadeau',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'gifts' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'gifts', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'gifts' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'gifts', 'action' => 'list', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Ventes'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'gifts' && $this->params['action'] == 'admin_order' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'gifts', 'action' => 'order', 'admin' => true))
        );

        $elements[] = array(
            'text'      => __('Cartes cadeau'),
            'classes'   => 'icon-euro',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'gifts' ?1:0),
            'children'  => $children
        );
		}
        */
        /* Fin menu bon cadeau */

		/* Menu Reduction */
		if(in_array('Reduction',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'vouchers' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'vouchers', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'vouchers' && $this->params['action'] == 'admin_index' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true))
        );

		$children[] = array(
            'text'      => __('Archive'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'vouchers' && $this->params['action'] == 'admin_archive' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'vouchers', 'action' => 'archive', 'admin' => true))
        );

        $elements[] = array(
            'text'      => __('Bon de réduction'),
            'classes'   => 'icon-barcode',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'vouchers' ?1:0),
            'children'  => $children
        );
		}
        /* Fin menu Reduction */

		/* Menu CRM */
		if(in_array('CRM',$menu_auth)){
		$children = array();
        $children[] = array(
            'text'      => __('Paniers'),
            'classes'   => '',
			'badge'     => $badge['CartLoose']['count'],
            'badge_name'=> ($badge['CartLoose']['count'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'crm' && $this->params['action'] == 'admin_cart'?1:0),
            'link'      => $this->Html->url(array('controller' => 'crm', 'action' => 'cart', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Visites Agents'),
            'classes'   => '',
			'badge'     => $badge['AgentView']['count'],
            'badge_name'=> ($badge['AgentView']['count'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'crm' && $this->params['action'] == 'admin_agent_view'?1:0),
            'link'      => $this->Html->url(array('controller' => 'crm', 'action' => 'agent_view', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Stats envois'),
            'classes'   => '',
			'badge'     => $badge['CrmStat']['count'],
            'badge_name'=> ($badge['CrmStat']['count'] == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'crm' && $this->params['action'] == 'admin_sends_test'?1:0),
            'link'      => $this->Html->url(array('controller' => 'crm', 'action' => 'sends_test', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Bilan'),
            'classes'   => '',
			'badge'     => '',
            'badge_name'=> '',
            'selected'  => ($this->params['controller'] == 'crm' && $this->params['action'] == 'admin_bilan'?1:0),
            'link'      => $this->Html->url(array('controller' => 'crm', 'action' => 'bilan', 'admin' => true))
        );
		 $children[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'crm' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'crm', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'crm' && $this->params['action'] == 'admin_index' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'crm', 'action' => 'index', 'admin' => true))
        );
		$badgestatstotal = $badge['AgentView']['count'] + $badge['CartLoose']['count'] + $badge['CrmStat']['count'];
		$elements[] = array(
            'text'      => __('CRM'),
            'classes'   => 'icon-bar-chart',
            'link'      => '',
			'badge'		=> $badgestatstotal,
			'badge_name'=> ($badgestatstotal == 0 ?false:'badge-important'),
            'selected'  => ($this->params['controller'] == 'crm' ?1:0),
            'children'  => $children
        );
		}
		/* Fin menu CRM */
		/* Menu Sponsorship */
		if(in_array('Parrainage',$menu_auth)){
		$children = array();
		if($level != 'moderator'){
			$children[] = array(
				'text'      => __('Regles'),
				'classes'   => '',
				'badge'     => '',
				'badge_name'=> '',
				'selected'  => ($this->params['controller'] == 'sponsorship' && $this->params['action'] == 'admin_rules'?1:0),
				'link'      => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'rules', 'admin' => true))
			);
			$children[] = array(
				'text'      => __('Créer'),
				'selected'  => ($this->params['controller'] == 'sponsorship' && $this->params['action'] == 'admin_create' ?1:0),
				'link'      => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'create', 'admin' => true)),
				'classes'   => 'icon-plus-sign'
			);
		}
		$children[] = array(
            'text'      => __('Parrainages'),
            'classes'   => '',
            'selected'  => ($this->params['controller'] == 'sponsorship' && $this->params['action'] == 'admin_agent_view'?1:0),
            'link'      => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'parrainage_view', 'admin' => true))
        );

		$elements[] = array(
            'text'      => __('Parrainage'),
            'classes'   => 'icon-user',
            'link'      => '',

            'selected'  => ($this->params['controller'] == 'sponsorship' ?1:0),
            'children'  => $children
        );
		}
		/* Fin menu CRM */

		/* Menu Export */
		if(in_array('Export',$menu_auth)){
        $children = array();

        $children[] = array(
            'text'      => __('Nouveaux clients'),
            'classes'   => 'icon-list',
            'link'      => $this->Html->url(array('controller' => 'accounts', 'action' => 'export_new_customer', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'accounts' && in_array($this->params['action'], array('admin_export_new_customer')) ?1:0)
        );

        $elements[] = array(
            'text'      => __('Export'),
            'classes'   => 'icon-list',
            'link'      => '/',
            'selected'  => (($this->params['controller'] === 'accounts' && in_array($this->params['action'], array('admin_export_new_customer'))))?1:0,
            'children'  => $children
        );
		}
        /* Fin menu Export */

		/* Menu Contenu */
		if(in_array('Contenu',$menu_auth)){
        $children = array();
        $childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'category' && $this->params['action'] == 'admin_create')?1:0,
            'classes'   => 'icon-plus-sign',
            'link'      => $this->Html->url(array('controller' => 'category','action' => 'create', 'admin' => true))
        );
        $childrenOfUnivers[] = array(
            'text'      => 'Liste',
            'selected'  => ($this->params['controller'] == 'category' && $this->params['action'] == 'admin_list')?1:0,
            'classes'   => 'icon-list',
            'link'      => $this->Html->url(array('controller' => 'category','action' => 'list', 'admin' => true), true)
        );


        $children[] = array(
            'text'      => __('Univers'),
            'link'      => '/',
            'classes'   => 'icon-sitemap',
            'badge'     => $badge['Category']['count'],
            'selected'  => ($this->params['controller'] == 'category')?1:0,
            'children'  => $childrenOfUnivers
        );


        $childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'pages' && $this->params['action'] == 'admin_create')?1:0,
            'classes'   => 'icon-plus-sign',
            'link'      => $this->Html->url(array('controller' => 'pages','action' => 'create', 'admin' => true))
        );
        $childrenOfUnivers[] = array(
            'text'      => 'Liste',
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'pages' && $this->params['action'] == 'admin_list')?1:0,
            'link'      => $this->Html->url(array('controller' => 'pages','action' => 'list', 'admin' => true))
        );

        $childrenOfUnivers2 = array();
        $childrenOfUnivers2[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'pages' && $this->params['action'] == 'admin_create_category')?1:0,
            'classes'   => 'icon-plus-sign',
            'link'      => $this->Html->url(array('controller' => 'pages','action' => 'create_category', 'admin' => true))
        );
        $childrenOfUnivers2[] = array(
            'text'      => 'Liste',
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'pages' && $this->params['action'] == 'admin_list_category')?1:0,
            'link'      => $this->Html->url(array('controller' => 'pages','action' => 'list_category', 'admin' => true))
        );

        $childrenOfUnivers[] = array(
            'text'      => __('Catégorie'),
            'link'      => '/',
            'classes'   => '',
            'badge'     => $badge['PageCategory']['count'],
            'selected'  => ($this->params['controller'] == 'pages' && in_array($this->params['action'], array('admin_list_category', 'admin_create_category', 'admin_edit_category')))?1:0,
            'children'  => $childrenOfUnivers2
        );


        $children[] = array(
            'text'      => __('CMS'),
            'link'      => '/',
            'classes'   => 'icon-pencil',
            'badge'     => $badge['Page']['count'],
            'selected'  => ($this->params['controller'] == 'pages')?1:0,
            'children'  => $childrenOfUnivers
        );

        $childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'phones' && $this->params['action'] == 'admin_create')?1:0,
            'classes'   => 'icon-plus-sign',
            'link'      => $this->Html->url(array('controller' => 'phones','action' => 'create', 'admin' => true))
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'selected'  => ($this->params['controller'] == 'phones' && $this->params['action'] == 'admin_list')?1:0,
            'classes'   => 'icon-list',
            'link'      => $this->Html->url(array('controller' => 'phones','action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Téléphone'),
            'link'      => '/',
            'classes'   => 'icon-phone',
            'badge'     => $badge['Phone']['count'],
            'selected'  => ($this->params['controller'] == 'phones')?1:0,
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'landings' && $this->params['action'] == 'admin_create')?1:0,
            'classes'   => 'icon-plus-sign',
            'link'      => $this->Html->url(array('controller' => 'landings','action' => 'create', 'admin' => true))
        );
        $childrenOfUnivers[] = array(
            'text'      => 'Liste',
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'landings' && $this->params['action'] == 'admin_list')?1:0,
            'link'      => $this->Html->url(array('controller' => 'landings','action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Landings'),
            'link'      => '/',
            'classes'   => 'icon-pencil',
            'selected'  => ($this->params['controller'] == 'landings')?1:0,
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'subscribes' && $this->params['action'] == 'admin_create')?1:0,
            'classes'   => 'icon-plus-sign',
            'link'      => $this->Html->url(array('controller' => 'subscribes','action' => 'create', 'admin' => true))
        );
        $childrenOfUnivers[] = array(
            'text'      => 'Liste',
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'subscribes' && $this->params['action'] == 'admin_list')?1:0,
            'link'      => $this->Html->url(array('controller' => 'subscribes','action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Page inscription'),
            'link'      => '/',
            'classes'   => 'icon-pencil',
            'badge'     => null,
            'selected'  => ($this->params['controller'] == 'subscribes')?1:0,
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'redirect' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'redirect', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'redirect' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'redirect', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('SEO Redirection'),
            'classes'   => 'icon-pencil',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'redirect' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );


        $elements[] = array(
            'text'      => __('Contenus'),
            'classes'   => 'icon-pencil',
            'link'      => '/',
            'selected'  => ($this->params['controller'] == 'category' || $this->params['controller'] === 'pages' || $this->params['controller'] === 'phones' || $this->params['controller'] === 'landings' || $this->params['controller'] === 'redirect')?1:0,
            'children'  => $children
        );
		}
        /* Fin menu Contenu */

		/* Menu Slide */
		if(in_array('Slide',$menu_auth)){
        $children = array();
		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'slides' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slides', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'slides' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slides', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Slide'),
            'classes'   => 'icon-exchange',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'slides' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'slidemobiles' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slidemobiles', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'slidemobiles' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slidemobiles', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Slide Mobile'),
            'classes'   => 'icon-exchange',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'slidemobiles' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'slideprices' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slideprices', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'slideprices' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slideprices', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Slide Tarifs'),
            'classes'   => 'icon-exchange',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'slideprices' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'slidepricemobiles' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slidepricemobiles', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'slidepricemobiles' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'slidepricemobiles', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Slide Tarifs Mobile'),
            'classes'   => 'icon-exchange',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'slidepricemobiles' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );


        $elements[] = array(
            'text'      => __('Slides'),
            'classes'   => 'icon-pencil',
            'link'      => '/',
            'selected'  => ($this->params['controller'] == 'slides' || $this->params['controller'] === 'slidemobiles' || $this->params['controller'] === 'slideprices' || $this->params['controller'] === 'slidepricemobiles')?1:0,
            'children'  => $children
        );
		}
        /* Fin menu Slide */

		/* Menu Template */
		if(in_array('Template',$menu_auth)){
        $children = array();

        $children[] = array(
            'text'      => __('Top Menu'),
            'classes'   => 'icon-list',
            'link'      => $this->Html->url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'menus' && in_array($this->params['action'], array('admin_menu')) ?1:0)
        );

		$children[] = array(
            'text'      => __('Footer'),
            'classes'   => 'icon-list',
            'link'      => $this->Html->url(array('controller' => 'footers', 'action' => 'footer', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'footers' && in_array($this->params['action'], array('admin_footer')) ?1:0)
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'block' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'block', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'block' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'block', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Block'),
            'classes'   => 'icon-exchange',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'block' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'columns' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'columns', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'columns' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'columns', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Colonne'),
            'classes'   => 'icon-th-large',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'columns' && in_array($this->params['action'], array('admin_create', 'admin_edit', 'admin_list')) ?1:0),
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_create_logo' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'create_logo', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_logo' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'logo', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Logo'),
            'classes'   => 'icon-picture',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'admins' && in_array($this->params['action'], array('admin_logo', 'admin_create_logo')) ?1:0),
            'children'  => $childrenOfUnivers
        );


        $elements[] = array(
            'text'      => __('Template'),
            'classes'   => 'icon-pencil',
            'link'      => '/',
            'selected'  => ($this->params['controller'] == 'menus' || $this->params['controller'] === 'footers' || $this->params['controller'] === 'block' || $this->params['controller'] === 'columns' || ($this->params['controller'] === 'admins' && in_array($this->params['action'], array('admin_logo', 'admin_create_logo'))))?1:0,
            'children'  => $children
        );
		}
        /* Fin menu Template */

		/* Menu Horoscope */
        /*
		if(in_array('Horoscope',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'horoscopes' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'horoscopes' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'list', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Signes'),
            'classes'   => 'icon-pencil',
            'selected'  => ($this->params['controller'] == 'horoscopes' && $this->params['action'] == 'admin_signs' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'signs', 'admin' => true))
        );
		$children[] = array(
            'text'      => __('Inscriptions'),
            'classes'   => 'icon-user',
            'selected'  => ($this->params['controller'] == 'horoscopes' && $this->params['action'] == 'admin_signs_subscribe' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'signs_subscribe', 'admin' => true))
        );
        $elements[] = array(
            'text'      => __('Horoscope'),
            'classes'   => 'icon-asterisk',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'horoscopes' && in_array($this->params['action'], array('admin_list', 'admin_create', 'admin_edit', 'admin_signs', 'admin_signs_edit', 'admin_signs_subscribe')) ?1:0),
            'children'  => $children
        );
		}
        */
		/* Fin menu horoscope */

		/* Menu Traduction */
		if(in_array('Traduction',$menu_auth)){
        $children = array();
        $elements[] = array(
            'text'      => __('Traductions'),
            'classes'   => 'icon-globe',
            'selected'  => ($this->params['controller'] == 'translate' && $this->params['action'] == 'admin_index' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'translate', 'action' => 'index', 'admin' => true)),

            'children'  => false
        );
		}
        /* Fin menu Traduction */

		/* Menu jeux de carte */
      /*
    if(in_array('Card',$menu_auth)){
      $cardChildren = array();
      $cardChildren[] = array(
        'text'      => __('Créer'),
        'selected'  => ($this->params['controller'] == 'cards' && $this->params['action'] == 'admin_create')?1:0,
        'classes'   => 'icon-plus-sign',
        'link'      => $this->Html->url(array('controller' => 'cards','action' => 'create', 'admin' => true))
      );
      $cardChildren[] = array(
        'text'      => 'Liste',
        'classes'   => 'icon-list',
        'selected'  => ($this->params['controller'] == 'cards' && $this->params['action'] == 'admin_list')?1:0,
        'link'      => $this->Html->url(array('controller' => 'cards','action' => 'list', 'admin' => true))
      );

      $elements[] = array(
        'text'      => __('Jeux de cartes'),
        'link'      => '/',
        'classes'   => 'icon-barcode',
        'badge'     => isset($badge['Card']['count']) ? $badge['Card']['count'] : null,
        'selected'  => ($this->params['controller'] == 'cards')?1:0,
        'children'  => $cardChildren
      );
    }
      */
		/* end menu carte */

		/* Menu Admin */
		if(in_array('Admin',$menu_auth)){
        $children = array();
        $children[] = array(
            'text'      => __('Créer'),
            'classes'   => 'icon-plus-sign',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_subscribe' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'subscribe', 'admin' => true))
        );
        $children[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'list', 'admin' => true))
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créer'),
            'selected'  => ($this->params['controller'] == 'userlevel' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'userlevel', 'action' => 'create', 'admin' => true)),
            'classes'   => 'icon-plus-sign'
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'userlevel' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'userlevel', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Levels'),
            'classes'   => 'icon-user',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'userlevel' && in_array($this->params['action'], array('admin_list', 'admin_create')) ?1:0),
            'children'  => $childrenOfUnivers
        );

		$childrenOfUnivers = array();
        $childrenOfUnivers[] = array(
            'text'      => __('Créér'),
            'classes'   => 'icon-edit',
            'selected'  => ($this->params['controller'] == 'userlevelacl' && $this->params['action'] == 'admin_create' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'userlevelacl', 'action' => 'create', 'admin' => true))
        );
        $childrenOfUnivers[] = array(
            'text'      => __('Liste'),
            'classes'   => 'icon-list',
            'selected'  => ($this->params['controller'] == 'userlevelacl' && $this->params['action'] == 'admin_list' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'userlevelacl', 'action' => 'list', 'admin' => true))
        );

        $children[] = array(
            'text'      => __('Levels ACL'),
            'classes'   => 'icon-user',
            'link'      => '',
            'selected'  => ($this->params['controller'] == 'userlevelacl' && in_array($this->params['action'], array('admin_list', 'admin_create')) ?1:0),
            'children'  => $childrenOfUnivers
        );

        $elements[] = array(
            'text'      => __('Administrateurs'),
            'classes'   => 'icon-user',
            'selected'  => ($this->params['controller'] == 'admins' && $this->params['action'] == 'admin_subscribe' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'admins', 'action' => 'subscribe', 'admin' => true)),
            'selected'  => (($this->params['controller'] == 'admins' && in_array($this->params['action'], array('admin_list','admin_subscribe')) || $this->params['controller'] == 'userlevelacl' || $this->params['controller'] == 'userlevel') ?1:0),
            'children'  => $children
        );
/*
        $elements[] = array(
            'text'      => __('Domaines/Langues'),
            'classes'   => 'icon-user',
            'selected'  => ($this->params['controller'] == 'domains' && $this->params['action'] == 'admin_index' ?1:0),
            'link'      => $this->Html->url(array('controller' => 'domains', 'action' => 'admin_index', 'admin' => true)),
            'selected'  => ($this->params['controller'] == 'domains' && in_array($this->params['action'], array('admin_index') ?1:0)),
            'children'  => ''
        );
*/
		}
        /* Fin menu Admin */


        return $elements;
    }

    public function renderSidebar($items=array())
    {
        $html = '<ul class="page-sidebar-menu">';
        $html.= '<li><div class="sidebar-toggler hidden-phone"></div></li>';


        foreach ($items AS $item){
            $html.= $this->_renderSidebarElement($item);
        }
        $html.= '</ul>';
        return $html;
    }
    private function _renderSidebarElement($elt=array(), $isChildren=false)
    {
        if (empty($elt))return false;
        $html = '<li'.((isset($elt['selected']) && (int)$elt['selected'] == 1)?' class="active"':'').(isset($elt['id']) && !empty($elt['id'])?' id="'.$elt['id'].'"':'').'>';

        $html.= '<a href="'.$elt['link'].'">';

        if (isset($elt['classes']) && !empty($elt['classes']))
            $html.= '<i class="'.$elt['classes'].'"></i> ';


        if (!$isChildren){
            $html.= '<span class="title">'.$elt['text'].'</span>';
        }else{
            if (!empty($elt['text']))
                $html.= $elt['text'];
        }
        if (isset($elt['children']) && !empty($elt['children']))
            $html.= '<span class="arrow"></span>';
        else
            $html.= '<span class="selected"></span>';


        if (isset($elt['badge']))
            if ((int)$elt['badge']>0 && isset($elt['badge_important']))
                $html.= '<span class="badge'.(isset($elt['badge_name'])?' '.$elt['badge_name']:'').' badge-important">'.$elt['badge'].'</span>';
            elseif ((int)$elt['badge'] > 0)
                $html.= '<span class="badge'.(isset($elt['badge_name'])?' '.$elt['badge_name']:'').'">'.$elt['badge'].'</span>';


        $html.= '</a>';

        if (isset($elt['children']) && !empty($elt['children'])){
            $html.= '<ul class="sub-menu">';
            foreach ($elt['children'] AS $eltChildren)
                $html.= $this->_renderSidebarElement($eltChildren, true);
            $html.= '</ul>';
        }

        $html.= '</li>';

        return $html;
    }
    public function titlePage($title="", $title_small="")
    {
        return '<div class="row-fluid"><div class="span12"><h3 class="page-title">'.$title.(!empty($title_small)?' <small>'.$title_small.'</small>':'').'</h3></div></div>';
    }

    public function pagination($paginator){
        $html = '<div class="pagination pagination-centered"><ul>';
        if($paginator->param('pageCount') >= 3) $html.= $paginator->first(__('Début'),array('tag' => 'li'));
        if(!$paginator->param('prevPage')) $html.= '<li class="disabled"><a href="javascript:void(0);">< '.__('Précedent').'</a></li>';
        else $html.= $paginator->prev('< '.__('Précedent'),array('tag' => 'li'));
        $html.= $paginator->numbers(array('first' => 2,'last' => 2, 'separator' => false, 'tag' => 'li', 'currentTag' => 'a', 'currentClass' => 'disabled', 'modulus' => 3, 'ellipsis' => '<li><a>...</a></li>'));
        if(!$paginator->param('nextPage')) $html.= '<li class="disabled"><a href="javascript:void(0);">'.__('Suivant').' ></a></li>';
        else $html.= $paginator->next(__('Suivant').' >',array('tag' => 'li'));
        if(($paginator->param('pageCount') - $paginator->param('page')) >= 3) $html.= $paginator->last(__('Fin'),array('tag' => 'li'));
        $html.= '</ul></div>';

        return $html;
    }

    public function paginationManuelle($pageCount, $page, $url, $has_query_param = false){

        $html = '<div class="pagination pagination-centered">';
        $html.= '<ul>';
        if($page == 1){
            $html.= '<li class="disabled"><a href="javascript:void(0);">&lt; '.__('Précédent').'</a></li>';
            $html.= '<li class="disabled"><a>1</a></li>';
        }
        elseif($page >= 2){
            $html.= '<li><a href="'.$url.($has_query_param ?'&':'?').'page=1">'.__('Début').'</a></li>';
            $html.= '<li><a href="'.$url.($has_query_param ?'&':'?').'page='.($page-1).'">&lt; '.__('Précédent').'</a></li>';
            $html.= '<li><a href="'.$url.($has_query_param ?'&':'?').'page='.($page-1).'">'.($page-1).'</a></li>';
            $html.= '<li class="disabled"><a>'.$page.'</a></li>';
        }

        if($page == $pageCount)
            $html.= '<li class="disabled"><a href="javascript:void(0);">'.__('Suivant').' &gt;</a></li>';
        elseif($page < $pageCount){
            $html.= '<li><a href="'.$url.($has_query_param ?'&':'?').'page='.($page+1).'">'.($page+1).'</a></li>';
            $html.= '<li><a href="'.$url.($has_query_param ?'&':'?').'page='.($page+1).'">'.__('Suivant').' &gt;</a></li>';
            $html.= '<li><a href="'.$url.($has_query_param ?'&':'?').'page='.$pageCount.'">'.__('Fin').'</a></li>';
        }
        $html.= '</ul>';
        $html.= '</div>';

        return $html;
    }

    public function formPageCatLang($id_pageCat, $id_lang, $data){
        //Si pas d'id de la catégorie ou de la langue alors création du formulaire impossible
        if(empty($id_pageCat) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('name');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = '<div class="span4 panel-contenu">';

        $out.= $this->Form->inputs(array(
            'PageCategoryLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
            'PageCategoryLang.'.$id_lang.'.page_category_id'   => array('type' => 'hidden', 'value' => $id_pageCat),
            'PageCategoryLang.'.$id_lang.'.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name']),
        ));

        /*$out.= $this->Form->submit(__('Enregistrer'), array(
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));*/

        $out.= '</div>';

        return $out;
    }

    public function formProductLang($id_product, $id_lang, $data){
        //Si pas d'id du produit ou de la langue alors création du formulaire impossible
        if(empty($id_product) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('name','description');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = $this->Form->create('ProductLang', array('nobootstrap' => 1,'class' => 'form-horizontal panel-contenu', 'default' => 1,
                                                 'inputDefaults' => array(
                                                     'div' => 'control-group',
                                                     'between' => '<div class="controls">',
                                                     'after' => '</div>'
                                                 )));

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'product_id'    => array('type' => 'hidden', 'value' => $id_product),
            'lang_id'       => array('type' => 'hidden', 'value' => $id_lang),
            'name'                      => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name'], 'required' => true),
            'description'               => array('label' => array('text' => __('Description'), 'class' => 'control-label'), 'value' => $data['description'], 'type' => 'textarea')
        ));

        $out.= $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));

        return $out;
    }

    public function formLeftColumnLang($id_element, $id_lang, $data){
        //Si pas d'id de l'élément ou de la langue alors création du formulaire impossible
        if(empty($id_element) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('title','alt','name', 'link');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = '<div class="panel-contenu"><div class="row-fluid">';

        $out.= '<div class="span12">';

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'LeftColumnLang.'.$id_lang.'.lang_id'           => array('type' => 'hidden', 'value' => $id_lang),
            'LeftColumnLang.'.$id_lang.'.left_column_id'    => array('type' => 'hidden', 'value' => $id_element),
            'LeftColumnLang.'.$id_lang.'.title'             => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['title'], 'type' => 'text', 'class' => 'span10'),
            'LeftColumnLang.'.$id_lang.'.alt'               => array('label' => array('text' => __('Description'), 'class' => 'control-label'), 'value' => $data['alt'], 'class' => 'span10'),
            'LeftColumnLang.'.$id_lang.'.link'              => array('label' => array('text' => __('Lien'), 'class' => 'control-label'), 'value' => $data['link'], 'class' => 'span10'),
            'LeftColumnLang.'.$id_lang.'.file'              => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Largeur conseillée de l\'image').' 217px</p></div>', 'accept' => 'image/*'),
            'LeftColumnLang.'.$id_lang.'.name'              => array('type' => 'hidden', 'value' => $data['name'])
        ));

        $out.= '</div></div>';

        //S'il y a une photo
        if(!empty($data['name'])){
            $out.= $this->Html->image('/'.Configure::read('Site.pathLeftColumn').'/'.$data['name'].'?'.$this->Time->gmt());
            $out.= '<br><br>';
        }

        $out.= '</div>';

        return $out;

    }

    public function formSlideLang($id_slide, $id_lang, $data){
        //Si pas d'id du slide ou de la langue alors création du formulaire impossible
        if(empty($id_slide) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('title','alt','name', 'link');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

		$out = '<div class="panel-contenu">';
		$out.= '<fieldset><legend>&nbsp;</legend>';
				$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'legend' => false,
            'SlideLang.'.$id_lang.'.lang_id'    => array('type' => 'hidden', 'value' => $id_lang),
            'SlideLang.'.$id_lang.'.slide_id'   => array('type' => 'hidden', 'value' => $id_slide),
            'SlideLang.'.$id_lang.'.title'      => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['title'], 'type' => 'text', 'class' => 'span10'),
            'SlideLang.'.$id_lang.'.alt'        => array('label' => array('text' => __('Description'), 'class' => 'control-label'), 'value' => $data['alt'], 'class' => 'span10'),
            'SlideLang.'.$id_lang.'.link'       => array('label' => array('text' => __('Lien'), 'class' => 'control-label'), 'value' => $data['link'], 'class' => 'span10'),
				'SlideLang.'.$id_lang.'.color'       => array('label' => array('text' => __('Couleur principale texte'), 'class' => 'control-label'), 'value' => $data['color'], 'class' => 'span10', 'after' => '<p>'.__('Exemple choix : ').' #fff -> Blanc / #000 -> Noir / #5a449b -> Aubergine </p></div>'),
								));
						$out.= '</div>';
				$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'SlideLang.'.$id_lang.'.date_fin'       => array('label' => array('text' => __('Date compteur'), 'class' => 'control-label'), 'value' => $data['date_fin'], 'class' => 'span10', 'placeholder' => 'AAAA-MM-JJ HH:MM:SS'),
			'SlideLang.'.$id_lang.'.text_compteur'       => array('label' => array('text' => __('Phrase compteur'), 'class' => 'control-label'), 'value' => $data['text_compteur'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color_compteur'       => array('label' => array('text' => __('Couleur compteur'), 'class' => 'control-label'), 'value' => $data['color_compteur'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.size_compteur'       => array('label' => array('text' => __('Taille compteur'), 'class' => 'control-label'), 'value' => $data['size_compteur'], 'class' => 'span10'),

								));
						$out.= '</div>';

				$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'SlideLang.'.$id_lang.'.code1'       => array('label' => array('text' => __('Type 1'), 'class' => 'control-label'), 'value' => $data['code1'], 'class' => 'span10', 'placeholder'=> 'P / H1 / H2'),
			'SlideLang.'.$id_lang.'.titre1'       => array('label' => array('text' => __('Titre 1'), 'class' => 'control-label'), 'value' => $data['titre1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.font1'       => array('label' => array('text' => __('Font 1 (https://fonts.google.com/)'), 'class' => 'control-label'), 'value' => $data['font1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.size1'       => array('label' => array('text' => __('Taille 1'), 'class' => 'control-label'), 'value' => $data['size1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color1'       => array('label' => array('text' => __('Couleur 1'), 'class' => 'control-label'), 'value' => $data['color1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.align1'       => array('label' => array('text' => __('Alignement 1'), 'class' => 'control-label'), 'value' => $data['align1'], 'class' => 'span10', 'placeholder'=> 'left / right / center'),

								));
						$out.= '</div>';

				$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'SlideLang.'.$id_lang.'.code2'       => array('label' => array('text' => __('Type 2'), 'class' => 'control-label'), 'value' => $data['code2'], 'class' => 'span10', 'placeholder'=> 'P / H1 / H2'),
			'SlideLang.'.$id_lang.'.titre2'       => array('label' => array('text' => __('Titre 2'), 'class' => 'control-label'), 'value' => $data['titre2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.font2'       => array('label' => array('text' => __('Font 2'), 'class' => 'control-label'), 'value' => $data['font2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.size2'       => array('label' => array('text' => __('Taille 2'), 'class' => 'control-label'), 'value' => $data['size2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color2'       => array('label' => array('text' => __('Couleur 2'), 'class' => 'control-label'), 'value' => $data['color2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.align2'       => array('label' => array('text' => __('Alignement 2'), 'class' => 'control-label'), 'value' => $data['align2'], 'class' => 'span10', 'placeholder'=> 'left / right / center'),

								));
						$out.= '</div>';

				$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'SlideLang.'.$id_lang.'.code3'       => array('label' => array('text' => __('Type 3'), 'class' => 'control-label'), 'value' => $data['code3'], 'class' => 'span10', 'placeholder'=> 'P / H1 / H2'),
			'SlideLang.'.$id_lang.'.titre3'       => array('label' => array('text' => __('Titre 3'), 'class' => 'control-label'), 'value' => $data['titre3'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.font3'       => array('label' => array('text' => __('Font 3'), 'class' => 'control-label'), 'value' => $data['font3'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.size3'       => array('label' => array('text' => __('Taille 3'), 'class' => 'control-label'), 'value' => $data['size3'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color3'       => array('label' => array('text' => __('Couleur 3'), 'class' => 'control-label'), 'value' => $data['color3'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.align3'       => array('label' => array('text' => __('Alignement 3'), 'class' => 'control-label'), 'value' => $data['align3'], 'class' => 'span10', 'placeholder'=> 'left / right / center'),

								));
						$out.= '</div>';
			$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'SlideLang.'.$id_lang.'.code4'       => array('label' => array('text' => __('Type 4'), 'class' => 'control-label'), 'value' => $data['code4'], 'class' => 'span10', 'placeholder'=> 'P / H1 / H2'),
			'SlideLang.'.$id_lang.'.titre4'       => array('label' => array('text' => __('Titre 4'), 'class' => 'control-label'), 'value' => $data['titre4'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.font4'       => array('label' => array('text' => __('Font 4'), 'class' => 'control-label'), 'value' => $data['font4'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.size4'       => array('label' => array('text' => __('Taille 4'), 'class' => 'control-label'), 'value' => $data['size4'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color4'       => array('label' => array('text' => __('Couleur 4'), 'class' => 'control-label'), 'value' => $data['color4'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.align4'       => array('label' => array('text' => __('Alignement 4'), 'class' => 'control-label'), 'value' => $data['align4'], 'class' => 'span10', 'placeholder'=> 'left / right / center'),

								));
						$out.= '</div>';
			$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'SlideLang.'.$id_lang.'.code5'       => array('label' => array('text' => __('Type 5'), 'class' => 'control-label'), 'value' => $data['code5'], 'class' => 'span10', 'placeholder'=> 'P / H1 / H2'),
			'SlideLang.'.$id_lang.'.titre5'       => array('label' => array('text' => __('Titre 5'), 'class' => 'control-label'), 'value' => $data['titre5'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.font5'       => array('label' => array('text' => __('Font 5'), 'class' => 'control-label'), 'value' => $data['font5'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.size5'       => array('label' => array('text' => __('Taille 5'), 'class' => 'control-label'), 'value' => $data['size5'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color5'       => array('label' => array('text' => __('Couleur 5'), 'class' => 'control-label'), 'value' => $data['color5'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.align5'       => array('label' => array('text' => __('Alignement 5'), 'class' => 'control-label'), 'value' => $data['align5'], 'class' => 'span10', 'placeholder'=> 'left / right / center'),

								));
						$out.= '</div>';
			$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
							'SlideLang.'.$id_lang.'.titre_btn1'       => array('label' => array('text' => __('Titre bouton 1'), 'class' => 'control-label'), 'value' => $data['titre_btn1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.link_btn1'       => array('label' => array('text' => __('Lien bouton 1'), 'class' => 'control-label'), 'value' => $data['link_btn1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color_btn1'       => array('label' => array('text' => __('Couleur txt bouton 1'), 'class' => 'control-label'), 'value' => $data['color_btn1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.back_btn1'       => array('label' => array('text' => __('Couleur background bouton 1'), 'class' => 'control-label'), 'value' => $data['back_btn1'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.titre_btn2'       => array('label' => array('text' => __('Titre bouton 2'), 'class' => 'control-label'), 'value' => $data['titre_btn2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.link_btn2'       => array('label' => array('text' => __('Lien bouton 2'), 'class' => 'control-label'), 'value' => $data['link_btn2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.color_btn2'       => array('label' => array('text' => __('Couleur txt bouton 2'), 'class' => 'control-label'), 'value' => $data['color_btn2'], 'class' => 'span10'),
			'SlideLang.'.$id_lang.'.back_btn2'       => array('label' => array('text' => __('Couleur background bouton 2'), 'class' => 'control-label'), 'value' => $data['back_btn2'], 'class' => 'span10'),

								));
						$out.= '</div>';
			$out.= '<div class="span3" style="background:#fff;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								 'SlideLang.'.$id_lang.'.file'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Slide.width').'x'.Configure::read('Slide.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),
            'SlideLang.'.$id_lang.'.name'       => array('type' => 'hidden', 'value' => $data['name'])

								));
					 //S'il y a une photo
        if(!empty($data['name'])){
            $out.= $this->Html->image('/'.Configure::read('Site.pathSlide').'/'.$data['name'].'?'.$this->Time->gmt());
            $out.= '<br><br>';
        }

						$out.= '</div>';

		$out .= '</fieldset>';
		$out .= '</div>';
        return $out;

    }

	public function formSlidemobileLang($id_slide, $id_lang, $data){
        //Si pas d'id du slide ou de la langue alors création du formulaire impossible
        if(empty($id_slide) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('title','alt', 'link');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = '<div class="panel-contenu"><div class="row-fluid">';

        $out.= '<div class="span12">';

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'SlidemobileLang.'.$id_lang.'.lang_id'    => array('type' => 'hidden', 'value' => $id_lang),
            'SlidemobileLang.'.$id_lang.'.slide_id'   => array('type' => 'hidden', 'value' => $id_slide),
            'SlidemobileLang.'.$id_lang.'.title'      => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['title'], 'type' => 'text', 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.link'       => array('label' => array('text' => __('Lien slider'), 'class' => 'control-label'), 'value' => $data['link'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_code_1'        => array('label' => array('text' => __('Type Phrase 1'), 'class' => 'control-label'), 'value' => $data['font_code_1'], 'class' => 'span10'),
            'SlidemobileLang.'.$id_lang.'.alt'        => array('label' => array('text' => __('Phrase 1'), 'class' => 'control-label'), 'value' => $data['alt'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_font_mobile_1'        => array('label' => array('text' => __('Police Phrase 1'), 'class' => 'control-label'), 'value' => $data['font_font_mobile_1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_size_mobile_1'        => array('label' => array('text' => __('Taille Phrase 1'), 'class' => 'control-label'), 'value' => $data['font_size_mobile_1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_color_mobile_1'        => array('label' => array('text' => __('Couleur Phrase 1'), 'class' => 'control-label'), 'value' => $data['font_color_mobile_1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_align_mobile_1'        => array('label' => array('text' => __('Align Phrase 1'), 'class' => 'control-label'), 'value' => $data['font_align_mobile_1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_code_2'        => array('label' => array('text' => __('Type Phrase 2'), 'class' => 'control-label'), 'value' => $data['font_code_2'], 'class' => 'span10'),
			 'SlidemobileLang.'.$id_lang.'.title2'        => array('label' => array('text' => __('Phrase 2'), 'class' => 'control-label'), 'value' => $data['title2'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_font_mobile_2'        => array('label' => array('text' => __('Police Phrase 2'), 'class' => 'control-label'), 'value' => $data['font_font_mobile_2'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_size_mobile_2'        => array('label' => array('text' => __('Taille Phrase 2'), 'class' => 'control-label'), 'value' => $data['font_size_mobile_2'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_color_mobile_2'        => array('label' => array('text' => __('Couleur Phrase 2'), 'class' => 'control-label'), 'value' => $data['font_color_mobile_2'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_align_mobile_2'        => array('label' => array('text' => __('Align Phrase 2'), 'class' => 'control-label'), 'value' => $data['font_align_mobile_2'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_code_3'        => array('label' => array('text' => __('Type Phrase 3'), 'class' => 'control-label'), 'value' => $data['font_code_3'], 'class' => 'span10'),
			 'SlidemobileLang.'.$id_lang.'.title3'        => array('label' => array('text' => __('Phrase 3'), 'class' => 'control-label'), 'value' => $data['title3'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_font_mobile_3'        => array('label' => array('text' => __('Police Phrase 3'), 'class' => 'control-label'), 'value' => $data['font_font_mobile_3'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_size_mobile_3'        => array('label' => array('text' => __('Taille Phrase 3'), 'class' => 'control-label'), 'value' => $data['font_size_mobile_3'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_color_mobile_3'        => array('label' => array('text' => __('Couleur Phrase 3'), 'class' => 'control-label'), 'value' => $data['font_color_mobile_3'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.font_align_mobile_3'        => array('label' => array('text' => __('Align Phrase 3'), 'class' => 'control-label'), 'value' => $data['font_align_mobile_3'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.titre_btn1'        => array('label' => array('text' => __('Texte bouton'), 'class' => 'control-label'), 'value' => $data['titre_btn1'], 'class' => 'span10'),
            'SlidemobileLang.'.$id_lang.'.link_btn1'        => array('label' => array('text' => __('Lien bouton'), 'class' => 'control-label'), 'value' => $data['link_btn1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.color_btn1'        => array('label' => array('text' => __('Couleur texte bouton'), 'class' => 'control-label'), 'value' => $data['color_btn1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.back_btn1'        => array('label' => array('text' => __('Background bouton'), 'class' => 'control-label'), 'value' => $data['back_btn1'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.date_compteur'       => array('label' => array('text' => __('Date compteur'), 'class' => 'control-label'), 'value' => $data['date_compteur'], 'class' => 'span10', 'placeholder' => 'AAAA-MM-JJ HH:MM:SS'),
			'SlidemobileLang.'.$id_lang.'.text_compteur'       => array('label' => array('text' => __('Phrase compteur'), 'class' => 'control-label'), 'value' => $data['text_compteur'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.color_compteur'       => array('label' => array('text' => __('Couleur compteur'), 'class' => 'control-label'), 'value' => $data['color_compteur'], 'class' => 'span10'),
			'SlidemobileLang.'.$id_lang.'.size_compteur'       => array('label' => array('text' => __('Taille compteur'), 'class' => 'control-label'), 'value' => $data['size_compteur'], 'class' => 'span10'),
            'SlidemobileLang.'.$id_lang.'.file'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Slidemobile.width').'x'.Configure::read('Slidemobile.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),
            'SlidemobileLang.'.$id_lang.'.name'       => array('type' => 'hidden', 'value' => $data['name'])
        ));

        $out.= '</div></div>';

        //S'il y a une photo
        if(!empty($data['name'])){
            $out.= $this->Html->image('/'.Configure::read('Site.pathSlidemobile').'/'.$data['name'].'?'.$this->Time->gmt());
            $out.= '<br><br>';
        }

        $out.= '</div>';

        return $out;

    }

	public function formSlidepriceLang($id_slide, $id_lang, $data){
        //Si pas d'id du slide ou de la langue alors création du formulaire impossible
        if(empty($id_slide) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('title','alt', 'link');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = '<div class="panel-contenu"><div class="row-fluid">';

        $out.= '<div class="span12">';

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'SlidepriceLang.'.$id_lang.'.lang_id'    => array('type' => 'hidden', 'value' => $id_lang),
            'SlidepriceLang.'.$id_lang.'.slide_id'   => array('type' => 'hidden', 'value' => $id_slide),
            'SlidepriceLang.'.$id_lang.'.title'      => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['title'], 'type' => 'text', 'class' => 'span10'),
            'SlidepriceLang.'.$id_lang.'.alt'        => array('label' => array('text' => __('Description'), 'class' => 'control-label'), 'value' => $data['alt'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.font_font_1'        => array('label' => array('text' => __('Police phrase titre'), 'class' => 'control-label'), 'value' => $data['font_font_1'], 'class' => 'span10'),
            'SlidepriceLang.'.$id_lang.'.font_size_1'        => array('label' => array('text' => __('Taille police phrase titre'), 'class' => 'control-label'), 'value' => $data['font_size_1'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.font_color_1'        => array('label' => array('text' => __('Couleur phrase titre'), 'class' => 'control-label'), 'value' => $data['font_color_1'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.font_font_2'        => array('label' => array('text' => __('Police phrase description'), 'class' => 'control-label'), 'value' => $data['font_font_2'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.font_size_2'        => array('label' => array('text' => __('Taille police phrase description'), 'class' => 'control-label'), 'value' => $data['font_size_2'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.font_color_2'        => array('label' => array('text' => __('Couleur phrase description'), 'class' => 'control-label'), 'value' => $data['font_color_2'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.date_fin'       => array('label' => array('text' => __('Date Compteur'), 'class' => 'control-label'), 'value' => $data['date_fin'], 'class' => 'span10', 'placeholder' => 'AAAA-MM-JJ HH:MM:SS'),
			'SlidepriceLang.'.$id_lang.'.text_compteur'       => array('label' => array('text' => __('Phrase compteur'), 'class' => 'control-label'), 'value' => $data['text_compteur'], 'class' => 'span10'),
           'SlidepriceLang.'.$id_lang.'.date_fin_size'        => array('label' => array('text' => __('Taille police compteur'), 'class' => 'control-label'), 'value' => $data['date_fin_size'], 'class' => 'span10'),
			'SlidepriceLang.'.$id_lang.'.date_fin_color'        => array('label' => array('text' => __('Couleur police compteur'), 'class' => 'control-label'), 'value' => $data['date_fin_color'], 'class' => 'span10'),
            'SlidepriceLang.'.$id_lang.'.name'       => array('type' => 'hidden', 'value' => $data['name']),

			'SlidepriceLang.'.$id_lang.'.file'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Slideprice.width').'x'.Configure::read('Slideprice.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>')

		));

        $out.= '</div></div>';

        //S'il y a une photo
        if(!empty($data['name'])){
            $out.= $this->Html->image('/'.Configure::read('Site.pathSlideprice').'/'.$data['name'].'?'.$this->Time->gmt());
            $out.= '<br><br>';
        }

        $out.= '</div>';

        return $out;

    }

	public function formSlidepriceMobileLang($id_slide, $id_lang, $data){
        //Si pas d'id du slide ou de la langue alors création du formulaire impossible
        if(empty($id_slide) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('title','alt', 'link');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = '<div class="panel-contenu"><div class="row-fluid">';

        $out.= '<div class="span12">';

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'SlidepricemobileLang.'.$id_lang.'.lang_id'    => array('type' => 'hidden', 'value' => $id_lang),
            'SlidepricemobileLang.'.$id_lang.'.slide_id'   => array('type' => 'hidden', 'value' => $id_slide),
			'SlidepricemobileLang.'.$id_lang.'.name_mobile'       => array('type' => 'hidden', 'value' => $data['name_mobile']),
            'SlidepricemobileLang.'.$id_lang.'.title_mobile'      => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['title_mobile'], 'type' => 'text', 'class' => 'span10'),
			'SlidepricemobileLang.'.$id_lang.'.font_font_mobile_1'        => array('label' => array('text' => __('Police phrase 1'), 'class' => 'control-label'), 'value' => $data['font_font_mobile_1'], 'class' => 'span10'),
            'SlidepricemobileLang.'.$id_lang.'.font_size_mobile_1'        => array('label' => array('text' => __('Taille police phrase 1'), 'class' => 'control-label'), 'value' => $data['font_size_mobile_1'], 'class' => 'span10'),
			'SlidepricemobileLang.'.$id_lang.'.font_color_mobile_1'        => array('label' => array('text' => __('Couleur phrase 1'), 'class' => 'control-label'), 'value' => $data['font_color_mobile_1'], 'class' => 'span10'),
			'SlidepricemobileLang.'.$id_lang.'.date_fin'       => array('label' => array('text' => __('Date Compteur'), 'class' => 'control-label'), 'value' => $data['date_fin'], 'class' => 'span10', 'placeholder' => 'AAAA-MM-JJ HH:MM:SS'),
			'SlidepricemobileLang.'.$id_lang.'.text_compteur'       => array('label' => array('text' => __('Phrase compteur'), 'class' => 'control-label'), 'value' => $data['text_compteur'], 'class' => 'span10'),
           'SlidepricemobileLang.'.$id_lang.'.date_fin_mobile_size'        => array('label' => array('text' => __('Taille police compteur'), 'class' => 'control-label'), 'value' => $data['date_fin_mobile_size'], 'class' => 'span10'),
			'SlidepricemobileLang.'.$id_lang.'.date_fin_mobile_color'        => array('label' => array('text' => __('Couleur police compteur'), 'class' => 'control-label'), 'value' => $data['date_fin_mobile_color'], 'class' => 'span10'),


			'SlidepricemobileLang.'.$id_lang.'.file_mobile'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image mobile').' '.Configure::read('Slidepricemobile.width').'x'.Configure::read('Slidepricemobile.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),        ));

        $out.= '</div></div>';

        //S'il y a une photo
		if(!empty($data['name_mobile'])){
            $out.= $this->Html->image('/'.Configure::read('Site.pathSlideprice').'/'.$data['name_mobile'].'?'.$this->Time->gmt());
            $out.= '<br><br>';
        }

        $out.= '</div>';

        return $out;

    }

	public function formBlockLang($id_block, $id_lang, $data){
        //Si pas d'id du slide ou de la langue alors création du formulaire impossible
        if(empty($id_block) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('title','link','text1', 'text2_1', 'text2_2', 'text2_3', 'text3');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = '<div class="panel-contenu"><div class="row-fluid">';

        $out.= '<div class="span12">';

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'BlockLang.'.$id_lang.'.lang_id'    => array('type' => 'hidden', 'value' => $id_lang),
            'BlockLang.'.$id_lang.'.block_id'   => array('type' => 'hidden', 'value' => $id_block),
            'BlockLang.'.$id_lang.'.title'      => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['title'], 'type' => 'text', 'class' => 'span10'),
            'BlockLang.'.$id_lang.'.link'       => array('label' => array('text' => __('Lien'), 'class' => 'control-label'), 'value' => $data['link'], 'class' => 'span10'),
			'BlockLang.'.$id_lang.'.text1'       => array('label' => array('text' => __('Texte ligne 1'), 'class' => 'control-label'), 'value' => $data['text1'], 'class' => 'span10'),
			'BlockLang.'.$id_lang.'.text2_1'       => array('label' => array('text' => __('Texte ligne 2 partie 1'), 'class' => 'control-label'), 'value' => $data['text2_1'], 'class' => 'span10'),
			'BlockLang.'.$id_lang.'.text2_2'       => array('label' => array('text' => __('Texte ligne 2 partie 2'), 'class' => 'control-label'), 'value' => $data['text2_2'], 'class' => 'span10'),
			'BlockLang.'.$id_lang.'.text2_3'       => array('label' => array('text' => __('Texte ligne 2 partie 3'), 'class' => 'control-label'), 'value' => $data['text2_3'], 'class' => 'span10'),
			'BlockLang.'.$id_lang.'.text3'       => array('label' => array('text' => __('Texte ligne 3'), 'class' => 'control-label'), 'value' => $data['text3'], 'class' => 'span10')
        ));

        $out.= '</div></div>';

        $out.= '</div>';

        return $out;

    }


    public function formPageLang($id_page, $id_lang, $data, $isHiddenSystemPage=false, $page_params=array()){
        //Si pas d'id de la page ou de la langue alors création du formulaire impossible
        if(empty($id_page) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('meta_title','link_rewrite','meta_keywords','meta_description','content', 'name');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }



        if ($isHiddenSystemPage){
            /* Template Emails */
            $out = '<div class="panel-contenu"><div class="row-fluid">';
            $out.= '<div class="span8">';
            $out.= $this->Form->inputs(array(
                'legend' => false,
                'PageLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
                'PageLang.'.$id_lang.'.page_id'            => array('type' => 'hidden', 'value' => $id_page),
                'PageLang.'.$id_lang.'.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name']),
                'PageLang.'.$id_lang.'.meta_title'         => array('label' => array('text' => __('Objet du mail'), 'class' => 'control-label required'), 'value' => $data['meta_title'], 'type' => 'text', 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
                //'PageLang.'.$id_lang.'.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label'), 'value' => $data['link_rewrite']),
                //'PageLang.'.$id_lang.'.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'value' => $data['meta_keywords'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaKeywords').'</p></div>'),
                //'PageLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaDescription').'</p></div>')
            ));
            $out.= '<div class="tinymce"  style="width: 750px;">'.
                $this->Form->input('PageLang.'.$id_lang.'.content', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content'])).
            '</div>';

            $out.= '</div><div class="span4">';

            if (empty($page_params)){
                $out.= 'Aucune variable déclarée';
            }else{
                $out.= 'Variables disponibles pour ce template :<br/><br/>
                <table class="table table-striped table-hover table-bordered"><thead><tr><th>Variable à utiliser</th><th>Explication</th></tr></thead>';
                foreach ($page_params['page'] AS $key => $value){
                    $out.= '<tr>';
                    $out.= '<td><span class="admin_parm">##'.strtoupper($key).'##</span></td>';
                    $out.= '<td>'.$value.'</td>';
                    $out.= '</tr>';
                }
                $out.= '</table>';

                $out.= 'Variables globales :<br/><table class="table table-striped table-hover table-bordered"><thead><tr><th>Variable à utiliser</th><th>Explication</th></tr></thead>';
                foreach ($page_params['global'] AS $key => $value){
                    $out.= '<tr>';
                    $out.= '<td><span class="admin_parm">##'.strtoupper($key).'##</span></td>';
                    $out.= '<td>'.$value.'</td>';
                    $out.= '</tr>';
                }
                $out.= '</table>';
            }

            $out.= '</div></div>';
            $out.= '</div>';
        }else{
            /* Toutes les pages */
            $out = '<div class="panel-contenu"><div class="row-fluid">';
            $out.= '<div class="span4">';
            $out.= $this->Form->inputs(array(
                'legend' => false,
                'PageLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
                'PageLang.'.$id_lang.'.page_id'            => array('type' => 'hidden', 'value' => $id_page),
                'PageLang.'.$id_lang.'.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name']),
                'PageLang.'.$id_lang.'.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label required'), 'value' => $data['meta_title'], 'type' => 'text', 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
                'PageLang.'.$id_lang.'.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label'), 'value' => $data['link_rewrite']),
                'PageLang.'.$id_lang.'.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'value' => $data['meta_keywords'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaKeywords').'</span></p></div>'),
                'PageLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>'),
				'PageLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>')
            ));
            $out.= '</div><div class="span8"><div class="tinymce"  style="width: 750px;">';
            $out.= $this->Form->input('PageLang.'.$id_lang.'.content', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content']));
            $out.= '</div></div></div>';

			$out .= '<div class="panel-contenu"><div class="row-fluid">';
			$out.= '<div class="span6" style=""><h3>DESKTOP</h3>';
						$out.= $this->Form->inputs(array(
								'PageLang.'.$id_lang.'.phrase1_desktop'               => array('label' => array('text' => __('Phrase 1'), 'class' => 'control-label'), 'value' => $data['phrase1_desktop']),
							'PageLang.'.$id_lang.'.phrase2_desktop'               => array('label' => array('text' => __('Phrase 2'), 'class' => 'control-label'), 'value' => $data['phrase2_desktop']),
							'PageLang.'.$id_lang.'.btn_text'               => array('label' => array('text' => __('Btn texte'), 'class' => 'control-label'), 'value' => $data['btn_text']),
							'PageLang.'.$id_lang.'.btn_url'               => array('label' => array('text' => __('Btn URL'), 'class' => 'control-label'), 'value' => $data['btn_url']),

								));
							$out.= $this->Form->inputs(array(
								'PageLang.'.$id_lang.'.bg_desktop'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('PageDesktop.width').'x'.Configure::read('PageDesktop.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),

								));
							//S'il y a une photo
								if(!empty($data['bg_desktop'])){
									$out.= $this->Html->image('/'.Configure::read('Site.pathPageDesktop').'/'.$data['bg_desktop'].'?'.$this->Time->gmt());
									$out.= '<br><br>';
								}
					$out.= '</div>';
			$out.= '<div class="span6" style=""><h3>MOBILE</h3>';
						$out.= $this->Form->inputs(array(
								 'PageLang.'.$id_lang.'.phrase1_mobile'               => array('label' => array('text' => __('Phrase 1'), 'class' => 'control-label'), 'value' => $data['phrase1_mobile']),
							'PageLang.'.$id_lang.'.phrase2_mobile'               => array('label' => array('text' => __('Phrase 2'), 'class' => 'control-label'), 'value' => $data['phrase2_mobile'])
								));
							$out.= $this->Form->inputs(array(
								'PageLang.'.$id_lang.'.bg_mobile'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('PageMobile.width').'x'.Configure::read('PageMobile.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),


								));
							//S'il y a une photo
								if(!empty($data['bg_mobile'])){
									$out.= $this->Html->image('/'.Configure::read('Site.pathPageMobile').'/'.$data['bg_mobile'].'?'.$this->Time->gmt());
									$out.= '<br><br>';
								}

					$out.= '</div>';
			$out.= '</div></div>';

            $out.= '</div>';
        }



        return $out;
    }

	public function formLandingLang($id_page, $id_lang, $data, $isHiddenSystemPage=false, $page_params=array()){
        //Si pas d'id de la page ou de la langue alors création du formulaire impossible
        if(empty($id_page) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('meta_title','link_rewrite','meta_keywords','meta_description','content', 'name');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }



        if ($isHiddenSystemPage){
            /* Template Emails */
            $out = '<div class="panel-contenu"><div class="row-fluid">';
            $out.= '<div class="span8">';
            $out.= $this->Form->inputs(array(
                'legend' => false,
                'PageLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
                'PageLang.'.$id_lang.'.page_id'            => array('type' => 'hidden', 'value' => $id_page),
                'PageLang.'.$id_lang.'.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name']),
                'PageLang.'.$id_lang.'.meta_title'         => array('label' => array('text' => __('Objet du mail'), 'class' => 'control-label required'), 'value' => $data['meta_title'], 'type' => 'text', 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
                //'PageLang.'.$id_lang.'.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label'), 'value' => $data['link_rewrite']),
                //'PageLang.'.$id_lang.'.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'value' => $data['meta_keywords'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaKeywords').'</p></div>'),
                //'PageLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaDescription').'</p></div>')
            ));
            $out.= '<div class="tinymce"  style="width: 750px;">'.
                $this->Form->input('PageLang.'.$id_lang.'.content', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content'])).
            '</div>';

            $out.= '</div><div class="span4">';

            if (empty($page_params)){
                $out.= 'Aucune variable déclarée';
            }else{
                $out.= 'Variables disponibles pour ce template :<br/><br/>
                <table class="table table-striped table-hover table-bordered"><thead><tr><th>Variable à utiliser</th><th>Explication</th></tr></thead>';
                foreach ($page_params['page'] AS $key => $value){
                    $out.= '<tr>';
                    $out.= '<td><span class="admin_parm">##'.strtoupper($key).'##</span></td>';
                    $out.= '<td>'.$value.'</td>';
                    $out.= '</tr>';
                }
                $out.= '</table>';

                $out.= 'Variables globales :<br/><table class="table table-striped table-hover table-bordered"><thead><tr><th>Variable à utiliser</th><th>Explication</th></tr></thead>';
                foreach ($page_params['global'] AS $key => $value){
                    $out.= '<tr>';
                    $out.= '<td><span class="admin_parm">##'.strtoupper($key).'##</span></td>';
                    $out.= '<td>'.$value.'</td>';
                    $out.= '</tr>';
                }
                $out.= '</table>';
            }

            $out.= '</div></div>';
            $out.= '</div>';
        }else{
            /* Toutes les pages */
            $out = '<div class="panel-contenu">';
				$out .= '<div class="row-fluid">';

					$out.= '<div class="span6" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= '<fieldset><legend>Information générale</legend>';
							$out.= $this->Form->inputs(array(
									'legend' => false,
									'LandingLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
									'LandingLang.'.$id_lang.'.landing_id'            => array('type' => 'hidden', 'value' => $id_page),
									'LandingLang.'.$id_lang.'.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name']),
									'LandingLang.'.$id_lang.'.code_promo'      => array('label' => array('text' => __('Code Promo'), 'class' => 'control-label'), 'value' => $data['code_promo'], 'type' => 'text', 'class' => 'span10'),
								'LandingLang.'.$id_lang.'.date_compteur'      => array('label' => array('text' => __('Date compteur'), 'class' => 'control-label'), 'value' => $data['date_compteur'], 'type' => 'text', 'class' => 'span10', 'placeholder' => 'AAAA-MM-JJ HH:MM:SS'),
				'LandingLang.'.$id_lang.'.text_compteur'      => array('label' => array('text' => __('Phrase compteur'), 'class' => 'control-label'), 'value' => $data['text_compteur'], 'type' => 'text', 'class' => 'span10'),
									));
$out.= $this->Form->inputs(array(
                'LandingLang.'.$id_lang.'.template'   => array('label' => array('text' => __('Template'), 'class' => 'control-label required'), 'required' => true, 'options' => array(0=>'Défaut',1=>'Rassurance top',2=>'Rassurance bottom',3=>'L-SIZE',4=>'M-SIZE',5=>'S-SIZE',6=>'L-SIZE - TOP'), 'selected' => $data['template']),
            ));

							$out.= $this->inputCheckboxLang('LandingLang', 'show_pricetable','Afficher tableau tarif ?', 'show_pricetable', $data['show_pricetable'],$id_lang);
			$out.= $this->inputCheckboxLang('LandingLang', 'show_agents','Afficher listing agents ?', 'show_agents', $data['show_agents'],$id_lang);
			$out.= $this->inputCheckboxLang('LandingLang', 'show_reviews','Afficher avis clients ?', 'show_reviews', $data['show_reviews'],$id_lang);
						$out.= '</fieldset>';
					$out.= '</div>';
					$out.= '<div class="span6" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= '<fieldset><legend>SEO</legend>';
							$out.= $this->Form->inputs(array(
									'LandingLang.'.$id_lang.'.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label required'), 'value' => $data['meta_title'], 'type' => 'text', 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
									'LandingLang.'.$id_lang.'.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label'), 'value' => $data['link_rewrite']),
									'LandingLang.'.$id_lang.'.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'value' => $data['meta_keywords'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaKeywords').'</span></p></div>'),
									'LandingLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>'),
									));
						$out.= '</fieldset>';
					$out.= '</div>';

				$out.= '</div>';
				$out .= '<div class="row-fluid">';
					$out.= '<fieldset><legend>Slider Desktop</legend>';
					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.titre1'      => array('label' => array('text' => __('Titre'), 'class' => 'control-label'), 'value' => $data['titre1'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_1'        => array('label' => array('text' => __('Type titre'), 'class' => 'control-label'), 'value' => $data['font_type_1'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN (ne pas avoir l icone)'),
				'LandingLang.'.$id_lang.'.font_font_1'        => array('label' => array('text' => __('Police titre'), 'class' => 'control-label'), 'value' => $data['font_font_1'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_1'        => array('label' => array('text' => __('Taille police titre'), 'class' => 'control-label'), 'value' => $data['font_size_1'], 'class' => 'span10', 'placeholder' => '20'),
				'LandingLang.'.$id_lang.'.font_color_1'       => array('label' => array('text' => __('Couleur police titre'), 'class' => 'control-label'), 'value' => $data['font_color_1'], 'class' => 'span10', 'placeholder' => '#000000'),
				'LandingLang.'.$id_lang.'.font_align_1'        => array('label' => array('text' => __('Alignement titre'), 'class' => 'control-label'), 'value' => $data['font_align_1'], 'class' => 'span10', 'placeholder' => 'left / center / right'),
								));
					$out.= '</div>';
					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.titre2'      => array('label' => array('text' => __('Ligne 2'), 'class' => 'control-label'), 'value' => $data['titre2'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_2'        => array('label' => array('text' => __('Type ligne 2'), 'class' => 'control-label'), 'value' => $data['font_type_2'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN (ne pas avoir l icone)'),
				'LandingLang.'.$id_lang.'.font_font_2'        => array('label' => array('text' => __('Police ligne 2'), 'class' => 'control-label'), 'value' => $data['font_font_2'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_2'        => array('label' => array('text' => __('Taille police ligne 2'), 'class' => 'control-label'), 'value' => $data['font_size_2'], 'class' => 'span10'),
	            'LandingLang.'.$id_lang.'.font_color_2'       => array('label' => array('text' => __('Couleur police ligne 2'), 'class' => 'control-label'), 'value' => $data['font_color_2'], 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_align_2'        => array('label' => array('text' => __('Alignement ligne 2'), 'class' => 'control-label'), 'value' => $data['font_align_2'], 'class' => 'span10', 'placeholder' => 'left / center / right'),
								));
					$out.= '</div>';
					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.titre3'      => array('label' => array('text' => __('Ligne 3'), 'class' => 'control-label'), 'value' => $data['titre3'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_3'        => array('label' => array('text' => __('Type ligne 3'), 'class' => 'control-label'), 'value' => $data['font_type_3'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN (ne pas avoir l icone)'),
				'LandingLang.'.$id_lang.'.font_font_3'        => array('label' => array('text' => __('Police ligne 3'), 'class' => 'control-label'), 'value' => $data['font_font_3'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_3'        => array('label' => array('text' => __('Taille police ligne 3'), 'class' => 'control-label'), 'value' => $data['font_size_3'], 'class' => 'span10'),
	            'LandingLang.'.$id_lang.'.font_color_3'       => array('label' => array('text' => __('Couleur police ligne 3'), 'class' => 'control-label'), 'value' => $data['font_color_3'], 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_align_3'        => array('label' => array('text' => __('Alignement ligne 3'), 'class' => 'control-label'), 'value' => $data['font_align_3'], 'class' => 'span10', 'placeholder' => 'left / center / right'),
								));
					$out.= '</div>';
					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.titre4'      => array('label' => array('text' => __(''), 'class' => 'control-label'), 'value' => $data['titre4'], 'type' => 'textarea', 'tinymce' => false, 'id' => 'textaligne'.$id_lang, 'label' => 'Ligne 4', 'div' => false, 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_4'        => array('label' => array('text' => __('Type ligne 4'), 'class' => 'control-label'), 'value' => $data['font_type_4'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN (ne pas avoir l icone)'),
				'LandingLang.'.$id_lang.'.font_font_4'        => array('label' => array('text' => __('Police ligne 4'), 'class' => 'control-label'), 'value' => $data['font_font_4'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_4'        => array('label' => array('text' => __('Taille police ligne 4'), 'class' => 'control-label'), 'value' => $data['font_size_4'], 'class' => 'span10'),
	            'LandingLang.'.$id_lang.'.font_color_4'       => array('label' => array('text' => __('Couleur police ligne 4'), 'class' => 'control-label'), 'value' => $data['font_color_4'], 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_align_4'        => array('label' => array('text' => __('Alignement ligne 4'), 'class' => 'control-label'), 'value' => $data['font_align_4'], 'class' => 'span10', 'placeholder' => 'left / center / right'),
								));
					$out.= '</div>';

					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.btn1_txt'      => array('label' => array('text' => __('Texte bouton'), 'class' => 'control-label'), 'value' => $data['btn1_txt'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.btn1_url'  => array('label' => array('text' => __('Url bouton'), 'class' => 'control-label'), 'value' => $data['btn1_url'], 'type' => 'text', 'class' => 'span10'),
                'LandingLang.'.$id_lang.'.btn1_bg'  => array('label' => array('text' => __('Background bouton'), 'class' => 'control-label'), 'value' => $data['btn1_bg'], 'type' => 'text', 'class' => 'span10'),
                'LandingLang.'.$id_lang.'.btn1_color'  => array('label' => array('text' => __('Coleur bouton'), 'class' => 'control-label'), 'value' => $data['btn1_color'], 'type' => 'text', 'class' => 'span10'),

                        ));
					$out.= '</div>';

					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.btn2_txt'      => array('label' => array('text' => __('Texte bouton 2'), 'class' => 'control-label'), 'value' => $data['btn2_txt'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.btn2_url'      => array('label' => array('text' => __('Url bouton 2'), 'class' => 'control-label'), 'value' => $data['btn2_url'], 'type' => 'text', 'class' => 'span10'),

								));
					$out.= '</div>';
			$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.size_compteur'      => array('label' => array('text' => __('Taille compteur'), 'class' => 'control-label'), 'value' => $data['size_compteur'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.color_compteur'      => array('label' => array('text' => __('Couleur compteur'), 'class' => 'control-label'), 'value' => $data['color_compteur'], 'type' => 'text', 'class' => 'span10'),


								));
					$out.= '</div>';
					$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.file'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Landingslide.width').'x'.Configure::read('Landingslide.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),
            'LandingLang.'.$id_lang.'.slide'       => array('type' => 'hidden', 'value' => $data['slide'])

								));
							//S'il y a une photo
								if(!empty($data['slide'])){
									$out.= $this->Html->image('/'.Configure::read('Site.pathLandingSlide').'/'.$data['slide'].'?'.$this->Time->gmt());
									$out.= '<br><br>';
								}
					$out.= '</div>';
				$out.= '</fieldset>';
				$out.= '</div>';
				$out .= '<div class="row-fluid">';
					$out.= '<fieldset><legend>Slider Mobile</legend>';
						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.titre_mobile'      => array('label' => array('text' => __('Titre mobile'), 'class' => 'control-label'), 'value' => $data['titre_mobile'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_titre_mobile'        => array('label' => array('text' => __('Type titre'), 'class' => 'control-label'), 'value' => $data['font_type_titre_mobile'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN'),
				'LandingLang.'.$id_lang.'.font_font_titre_mobile'        => array('label' => array('text' => __('Police titre'), 'class' => 'control-label'), 'value' => $data['font_font_titre_mobile'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_titre_mobile'        => array('label' => array('text' => __('Taille police titre'), 'class' => 'control-label'), 'value' => $data['font_size_titre_mobile'], 'class' => 'span10', 'placeholder' => '20'),
				'LandingLang.'.$id_lang.'.font_color_titre_mobile'       => array('label' => array('text' => __('Couleur police titre'), 'class' => 'control-label'), 'value' => $data['font_color_titre_mobile'], 'class' => 'span10', 'placeholder' => '#000000'),
				'LandingLang.'.$id_lang.'.font_align_titre_mobile'        => array('label' => array('text' => __('Alignement titre'), 'class' => 'control-label'), 'value' => $data['font_align_titre_mobile'], 'class' => 'span10', 'placeholder' => 'left / center / right'),

								));
						$out.= '</div>';
						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.ligne2_mobile'      => array('label' => array('text' => __('Ligne 2 mobile'), 'class' => 'control-label'), 'value' => $data['ligne2_mobile'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_ligne2_mobile'        => array('label' => array('text' => __('Type ligne 2'), 'class' => 'control-label'), 'value' => $data['font_type_ligne2_mobile'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN'),
				'LandingLang.'.$id_lang.'.font_font_ligne2_mobile'        => array('label' => array('text' => __('Police ligne 2'), 'class' => 'control-label'), 'value' => $data['font_font_ligne2_mobile'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_ligne2_mobile'        => array('label' => array('text' => __('Taille police ligne 2'), 'class' => 'control-label'), 'value' => $data['font_size_ligne2_mobile'], 'class' => 'span10', 'placeholder' => '20'),
				'LandingLang.'.$id_lang.'.font_color_ligne2_mobile'       => array('label' => array('text' => __('Couleur police ligne 2'), 'class' => 'control-label'), 'value' => $data['font_color_ligne2_mobile'], 'class' => 'span10', 'placeholder' => '#000000'),
				'LandingLang.'.$id_lang.'.font_align_ligne2_mobile'        => array('label' => array('text' => __('Alignement ligne 2'), 'class' => 'control-label'), 'value' => $data['font_align_ligne2_mobile'], 'class' => 'span10', 'placeholder' => 'left / center / right'),

								));
						$out.= '</div>';
						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.ligne3_mobile'      => array('label' => array('text' => __(''), 'class' => 'control-label'), 'value' => $data['ligne3_mobile'], 'type' => 'textarea', 'tinymce' => false, 'id' => 'textalignemobile'.$id_lang, 'label' => 'Ligne 3 mobile', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.font_type_ligne3_mobile'        => array('label' => array('text' => __('Type ligne 3'), 'class' => 'control-label'), 'value' => $data['font_type_ligne3_mobile'], 'class' => 'span10', 'placeholder' => 'H1 / H2 / H3 / P / SPAN'),
				'LandingLang.'.$id_lang.'.font_font_ligne3_mobile'        => array('label' => array('text' => __('Police ligne 3'), 'class' => 'control-label'), 'value' => $data['font_font_ligne3_mobile'], 'class' => 'span10', 'placeholder' => 'font google'),
				'LandingLang.'.$id_lang.'.font_size_ligne3_mobile'        => array('label' => array('text' => __('Taille police ligne 3'), 'class' => 'control-label'), 'value' => $data['font_size_ligne3_mobile'], 'class' => 'span10', 'placeholder' => '20'),
				'LandingLang.'.$id_lang.'.font_color_ligne3_mobile'       => array('label' => array('text' => __('Couleur police ligne 3'), 'class' => 'control-label'), 'value' => $data['font_color_ligne3_mobile'], 'class' => 'span10', 'placeholder' => '#000000'),
				'LandingLang.'.$id_lang.'.font_align_ligne3_mobile'        => array('label' => array('text' => __('Alignement ligne 3'), 'class' => 'control-label'), 'value' => $data['font_align_ligne3_mobile'], 'class' => 'span10', 'placeholder' => 'left / center / right'),

								));
						$out.= '</div>';
						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.file_mobile'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label required'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Landingslidemobile.width').'x'.Configure::read('Landingslidemobile.height').'</p><p><a target="_blank" href="http://optimizilla.com/">Merci d\'optimiser les images avant !</a></p></div>'),
            'LandingLang.'.$id_lang.'.slide_mobile'       => array('type' => 'hidden', 'value' => $data['slide_mobile'])

								));
								//S'il y a une photo
								if(!empty($data['slide_mobile'])){
									$out.= $this->Html->image('/'.Configure::read('Site.pathLandingSlide').'/'.$data['slide_mobile'].'?'.$this->Time->gmt());
									$out.= '<br><br>';
								}
						$out.= '</div>';
						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.size_compteur_mobile'      => array('label' => array('text' => __('Taille compteur mobile'), 'class' => 'control-label'), 'value' => $data['size_compteur_mobile'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.color_compteur_mobile'      => array('label' => array('text' => __('Couleur compteur mobile'), 'class' => 'control-label'), 'value' => $data['color_compteur_mobile'], 'type' => 'text', 'class' => 'span10'),
								));
						$out.= '</div>';

						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.url_logged_mobile'      => array('label' => array('text' => __('Lien (connecté)'), 'class' => 'control-label'), 'value' => $data['url_logged_mobile'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.url_nologged_mobile'      => array('label' => array('text' => __('Lien (non connecté)'), 'class' => 'control-label'), 'value' => $data['url_nologged_mobile'], 'type' => 'text', 'class' => 'span10'),
								));
						$out.= '</div>';

						$out.= '<div class="span3" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
							$out.= $this->Form->inputs(array(
								'LandingLang.'.$id_lang.'.btn_mobile_txt'      => array('label' => array('text' => __('Bouton texte'), 'class' => 'control-label'), 'value' => $data['btn_mobile_txt'], 'type' => 'text', 'class' => 'span10'),
				'LandingLang.'.$id_lang.'.btn_mobile_url'      => array('label' => array('text' => __('Bouton URL'), 'class' => 'control-label'), 'value' => $data['btn_mobile_url'], 'type' => 'text', 'class' => 'span10'),
                                'LandingLang.'.$id_lang.'.btn_mobile_bg'  => array('label' => array('text' => __('Background bouton'), 'class' => 'control-label'), 'value' => $data['btn_mobile_bg'], 'type' => 'text', 'class' => 'span10'),
                                'LandingLang.'.$id_lang.'.btn_mobile_color'  => array('label' => array('text' => __('Coleur bouton'), 'class' => 'control-label'), 'value' => $data['btn_mobile_color'], 'type' => 'text', 'class' => 'span10'),

                            ));
						$out.= '</div>';


					$out.= '</fieldset>';
				$out.= '</div>';

			$out .= '<div class="row-fluid">';
					$out.= '<fieldset><legend>Texte Desktop</legend>';
		$out .= '<div class="row-fluid">';
			$out.= '<div class="span6"><p>Contenu</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.content', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content']));
						$out.= '</div>';
			     $out.= '</div>';
			$out.= '<div class="span6"><p>Texte en avant</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.content_preview', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_preview'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content_preview']));
						$out.= '</div>';
					$out.= '</div>';$out.= '</div>';
					$out.= '</fieldset>';
				$out.= '</div>';

			$out .= '<div class="row-fluid">';
					$out.= '<fieldset><legend>Texte Mobile</legend>';
		$out .= '<div class="row-fluid">';
			$out.= '<div class="span6"><p>Contenu</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.content_mobile', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_mobile'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content_mobile']));
						$out.= '</div>';
			     $out.= '</div>';
			$out.= '<div class="span6"><p>Texte en avant</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.content_preview_mobile', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_preview_mobile'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content_preview_mobile']));
						$out.= '</div>';
					$out.= '</div>';$out.= '</div>';
					$out.= '</fieldset>';
				$out.= '</div>';

            $out .= '<div class="row-fluid">';
            $out.= '<fieldset><legend>Texte 2</legend>';
            $out .= '<div class="row-fluid">';
            $out.= '<div class="span6"><p>Contenu</p>';
            $out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.content_2', array('type' => 'textarea', 'tinymce' => true, 'id' => 'conten_2'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content_2']));
            $out.= '</div>';
            $out.= '</div>';

            $out.= '<div class="span6"><p>Texte 2 mobile</p>';
            $out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.content_2_mobile', array('type' => 'textarea', 'tinymce' => true, 'id' => 'content_2_mobile'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content_2_mobile']));
            $out.= '</div>';
            $out.= '</div>';$out.= '</div>';
            $out.= '</fieldset>';
            $out.= '</div>';


            $out .= '<div class="row-fluid">';
					$out.= '<fieldset><legend>Texte Rassurance</legend>';
		$out .= '<div class="row-fluid">';
			$out.= '<div class="span4"><p>block 1</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.reassurance_1', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_reassurance_1'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['reassurance_1']));
						$out.= '</div>';
			     $out.= '</div>';
			$out.= '<div class="span4"><p>block 2</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.reassurance_2', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_reassurance_2'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['reassurance_2']));
						$out.= '</div>';
			     $out.= '</div>';
			$out.= '<div class="span4"><p>block 3</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('LandingLang.'.$id_lang.'.reassurance_3', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_reassurance_3'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['reassurance_3']));
						$out.= '</div>';
			     $out.= '</div>';$out.= '</div>';
					$out.= '</fieldset>';
				$out.= '</div>';

			$out.= '</div>';
        }



        return $out;
    }

	public function formSubscribeLang($id_page, $id_lang, $data){
        //Si pas d'id de la page ou de la langue alors création du formulaire impossible
        if(empty($id_page) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('timing','block1','block2','block3','intro1', 'intro2', 'intro3', 'intro4', 'intro5');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }


		 	$out = '<div class="panel-contenu">';
				$out .= '<div class="row-fluid">';

					$out.= '<div class="span12" style="background:#eee;margin-bottom:20px;padding-left:5px;">';
						$out.= '<fieldset><legend>Information générale</legend>';
							$out.= $this->Form->inputs(array(
									'legend' => false,
									'SubscribeLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
									'SubscribeLang.'.$id_lang.'.subscribe_id'            => array('type' => 'hidden', 'value' => $id_page),
									'SubscribeLang.'.$id_lang.'.timing'               => array('label' => array('text' => __('Timing text intro en ms'), 'class' => 'control-label required'), 'value' => $data['timing']),
									'SubscribeLang.'.$id_lang.'.intro1'               => array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_intro1'.$id_lang,'label' => array('text' => __('Texte 1'), 'class' => 'control-label'), 'value' => $data['intro1']),
								    'SubscribeLang.'.$id_lang.'.intro2'               => array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_intro2'.$id_lang,'label' => array('text' => __('Texte 2'), 'class' => 'control-label'), 'value' => $data['intro2']),
								'SubscribeLang.'.$id_lang.'.intro3'               => array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_intro3'.$id_lang,'label' => array('text' => __('Texte 3'), 'class' => 'control-label'), 'value' => $data['intro3']),
								'SubscribeLang.'.$id_lang.'.intro4'               => array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_intro4'.$id_lang,'label' => array('text' => __('Texte 4'), 'class' => 'control-label'), 'value' => $data['intro4']),
								'SubscribeLang.'.$id_lang.'.intro5'               => array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_intro5'.$id_lang,'label' => array('text' => __('Texte 5'), 'class' => 'control-label'), 'value' => $data['intro5']),
									));

						$out.= '</fieldset>';
					$out.= '</div>';
				$out.= '</div>';

				$out .= '<div class="row-fluid">';
					$out.= '<fieldset><legend>Texte Colonne Droite</legend>';
		$out .= '<div class="row-fluid">';
			$out.= '<div class="span4"><p>block 1</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('SubscribeLang.'.$id_lang.'.block1', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_block1'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['block1']));
						$out.= '</div>';
			     $out.= '</div>';
			$out.= '<div class="span4"><p>block 2</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('SubscribeLang.'.$id_lang.'.block2', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_block2'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['block2']));
						$out.= '</div>';
			     $out.= '</div>';
			$out.= '<div class="span4"><p>block 3</p>';
						$out.= '<div class="tinymce"  style="">';
            $out.= $this->Form->input('SubscribeLang.'.$id_lang.'.block3', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_block3'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['block3']));
						$out.= '</div>';
			     $out.= '</div>';$out.= '</div>';
					$out.= '</fieldset>';
				$out.= '</div>';


			$out.= '</div>';
		return $out;
	}

	public function formDuplicateLangs($object,$id_lang){
		$out = '<div class="panel-contenu"><div class="row-fluid">';
            $out.= '<fieldset><legend>Copier le contenu Fr vers </legend><div class="span8">';
				$out.= $this->inputCheckboxLang($object, 'duplicate_belgique','Belgique', 'duplicate_belgique', $data['duplicate_belgique'],$id_lang);
			$out.= $this->inputCheckboxLang($object, 'duplicate_canada','Canada', 'duplicate_canada', $data['duplicate_canada'],$id_lang);
		$out.= $this->inputCheckboxLang($object, 'duplicate_luxembourg','Luxembourg', 'duplicate_luxembourg', $data['duplicate_luxembourg'],$id_lang);
		$out.= $this->inputCheckboxLang($object, 'duplicate_suisse','Suisse', 'duplicate_suisse', $data['duplicate_suisse'],$id_lang);
			$out.= '</div></fieldset></div></div>';
		return $out;
	}

    public function formHoroscopeLang($id_lang, $idHoro = false, $data = array()){
        if($idHoro !== false && empty($data))
            $data['content'] = '';
        elseif(isset($this->request->data['HoroscopeLang'][$id_lang]['content']))
            $data['content'] = $this->request->data['HoroscopeLang'][$id_lang]['content'];

        $out = $this->Form->input('HoroscopeLang.'.$id_lang.'.lang_id', array('type' => 'hidden', 'value' => $id_lang));
        $out.= '<div class="tinymce"  style="width: 750px;">';

        $out.= $this->Form->input('HoroscopeLang.'.$id_lang.'.content', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => (isset($data['content']) && !empty($data['content']) ?$data['content']:false)));

        $out.= '</div>';

        return $out;
    }

    public function formCategoryLang($id_cat, $id_lang, $data, $activeCat){
        //Si pas d'id de la catégorie ou de la langue alors création du formulaire impossible
        if(empty($id_cat) || empty($id_lang))
            return __('Erreur dans la création du formulaire');
        //Si on a aucune donnée, on initialise les champs
        if(empty($data)){
            $fields = array('name','link_rewrite','cat_rewrite','meta_title','meta_keywords','meta_description','meta_title2','meta_keywords2','meta_description2','description');
            foreach($fields as $field){
                $data[$field] = '';
            }
        }

        $out = $this->Form->create('Category', array('nobootstrap' => 1,'class' => 'form-horizontal panel-contenu', 'default' => 1,
            'inputDefaults' => array(
                'div' => 'control-group',
                'between' => '<div class="controls">',
                'class' => 'span10',
                'after' => '</div>'
            )));

        $out.= '<div class="row-fluid"><div class="span4">';

        $out.= $this->inputActive('Category', $activeCat);

        $out.= $this->Form->inputs(array(
            'legend' => false,
            'CategoryLang.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
            'CategoryLang.category_id'        => array('type' => 'hidden', 'value' => $id_cat),
            'CategoryLang.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'),'required' => true, 'value' => $data['name']),
            'CategoryLang.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label'),
                                                       'allowEmpty' => false,
                                                       'value' => $data['link_rewrite']
            ),
			'CategoryLang.cat_rewrite'       => array('label' => array('text' => __('Parent Lien réécrit'), 'class' => 'control-label'),
                                                       'allowEmpty' => true,
                                                       'value' => $data['cat_rewrite']
            ),

            'CategoryLang.meta_title2'         => array('label' => array('text' => __('Méta titre category'), 'class' => 'control-label'), 'value' => $data['meta_title2'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
            'CategoryLang.meta_keywords2'      => array('label' => array('text' => __('Méta mots-clés category'), 'class' => 'control-label'), 'value' => $data['meta_keywords2'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaKeywords').'</span></p></div>'),
            'CategoryLang.meta_description2'   => array('label' => array('text' => __('Méta description category'), 'class' => 'control-label'), 'value' => $data['meta_description2'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>')
	    ));

		/*
		 'CategoryLang.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label'), 'value' => $data['meta_title'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
            'CategoryLang.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'value' => $data['meta_keywords'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaKeywords').'</span></p></div>'),
            'CategoryLang.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>'),
		*/

        $out.= '</div><div class="tinymce"  style="width: 750px;">';

        $out.= $this->Form->input('CategoryLang.description', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['description']));

        $out.= '</div></div>';


        $out.= $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue save',
            'div' => array('class' => 'controls')
        ));

        return $out;
    }

    public function breadCrumb($elements=array())
    {
        if (empty($elements))return false;
        $html = '';

        foreach ($elements AS $k => $elt){
            $html.= '<li>';

            if (isset($elt['classes'])&&!empty($elt['classes'])) $html.= '<i class="'.$elt['classes'].'"></i>';

            $html.= '<a href="'.(empty($elt['link'])?'#':$elt['link']).'" '.((isset($elt['target'])&&!empty($elt['target']))?" target=\"".$elt['target']."\"":"").'>';
            $html.= (isset($elt['text'])&&!empty($elt['text']))?$elt['text']:'Aucun texte pour ce lien';
            $html.= '</a>';

            if (($k+1)<count($elements))
                $html.= "<i class=\"icon-angle-right\"></i>";

            $html.= '</li>';
        }

        return '<div class="row-fluid"><ul class="breadcrumb">'.$html.'</ul></div>';
    }

    public function inputsAdminEdit($inputs){
        //Si pas d'inputs alors, on renvoie rien
        if(empty($inputs)) return '';
        //On initialise la variable de sortie
        $out = '';

        $i = 0;
        foreach($inputs as $nameInput => $optionsInput){
            if($i%2 == 0) $out.= '<div class="row-fluid">';
            $out.= $this->Form->input($nameInput,$optionsInput);
            if($i%2 != 0) $out.= '</div>';
            $i++;
        }

        if($i%2 != 0) $out.= '</div>';

        return $out;
    }

    public function inputActive($model, $active=0, $class=''){
        $out = '<div class="control-group ' . $class . '"><label for="'.$model.'Active" class="control-label">'.__('Active').'</label>'
            .'<div class="controls"><input type="hidden" name="data['.$model.'][active]" id="'.$model.'Active_" value="0">'
            .'<div class="padding-checkbox" id="uniform-'.$model.'Active"><span>'
            .'<input type="checkbox"'. ($active == 1 ?' checked="checked"':'') .' name="data['.$model.'][active]" value="1" id="'.$model.'Active"></span></div></div></div>';

        return $out;
    }

    public function inputFooter($model, $active=0){
        $out = '<div class="control-group"><label for="'.$model.'Footer" class="control-label">'.__('Footer').'</label>'
            .'<div class="controls"><input type="hidden" name="data['.$model.'][footer]" id="'.$model.'Footer_" value="0">'
            .'<div class="padding-checkbox" id="uniform-'.$model.'Footer"><span>'
            .'<input type="checkbox"'. ($active == 1 ?' checked="checked"':'') .' name="data['.$model.'][footer]" value="1" id="'.$model.'Footer"></span></div></div></div>';

        return $out;
    }

    public function inputCheckbox($model, $idInput, $label, $nameData, $active=0, $id_lang = 0){
        $out = '<div class="control-group"><label for="'.$model.$idInput.'" class="control-label">'.__($label).'</label>'
            .'<div class="controls"><input type="hidden" name="data['.$model.']['.$nameData.']" id="'.$model.$idInput.'_" value="0">'
            .'<div class="padding-checkbox" id="uniform-'.$model.$idInput.'"><span>'
            .'<input type="checkbox"'. ($active == 1 ?' checked="checked"':'') .' name="data['.$model.']['.$nameData.']" value="1" id="'.$model.$idInput.'"></span></div></div></div>';

        return $out;
    }
	public function inputCheckboxLang($model, $idInput, $label, $nameData, $active=0, $id_lang = 1){
        $out = '<div class="control-group"><label for="'.$model.$idInput.'" class="control-label">'.__($label).'</label>'
            .'<div class="controls"><input type="hidden" name="data['.$model.']['.$id_lang.']['.$nameData.']" id="'.$model.$idInput.'_" value="0">'
            .'<div class="padding-checkbox" id="uniform-'.$model.$idInput.'"><span>'
            .'<input type="checkbox"'. ($active == 1 ?' checked="checked"':'') .' name="data['.$model.']['.$id_lang.']['.$nameData.']" value="1" id="'.$model.$idInput.'"></span></div></div></div>';

        return $out;
    }

    public function getDateInput($medias = array()){
        if($this->Session->check('Date')){
            $dateStart = $this->Session->read('Date.start');
            $dateEnd = $this->Session->read('Date.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else
            $valDate = '';

        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }


        $out = '<form action="/admin/admins/date_range" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        $out.= '<div class="input-append input-preprend">';
        $out.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $out.= '<input class="m-wrap date-range" type="text" style="border-right: 1px solid #e5e5e5 !important" name="date" value="'.$valDate.'" autocomplete="off">';
        $out.= '</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
        $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }

	public function getDateInputClient($medias = array()){
        if($this->Session->check('DateClient')){
            $dateStart = $this->Session->read('DateClient.start');
            $dateEnd = $this->Session->read('DateClient.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else{
			/*$valDate = date('d-m-Y');
            $valDate.= ' au ';
            $valDate.= date('d-m-Y');*/
		}


        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }


        $out = '<form action="/admin/admins/date_range" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        $out.= '<div class="input-append input-preprend">';
        $out.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $out.= '<input class="m-wrap date-range" type="text" style="border-right: 1px solid #e5e5e5 !important" name="dateclient" value="'.$valDate.'" autocomplete="off">';
        $out.= '</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
        $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }

	public function getDateInputCom($medias = array()){
        if($this->Session->check('Date')){
            $dateStart = $this->Session->read('Date.start');
            $dateEnd = $this->Session->read('Date.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else{
			/*$valDate = date('d-m-Y');
            $valDate.= ' au ';
            $valDate.= date('d-m-Y');*/
		}


        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }


        $out = '<form action="/admin/admins/date_range" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        $out.= '<div class="input-append input-preprend">';
        $out.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $out.= '<input class="m-wrap date-range" type="text" style="border-right: 1px solid #e5e5e5 !important" name="date" value="'.$valDate.'" autocomplete="off">';
        $out.= '</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
        $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }

	public function getDateInputComTranche($medias = array()){
        if($this->Session->check('Date')){
            $dateStart = $this->Session->read('Date.start');
            $dateEnd = $this->Session->read('Date.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else{
			/*$valDate = date('d-m-Y');
            $valDate.= ' au ';
            $valDate.= date('d-m-Y');*/
		}


        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }

		if($this->Session->check('type_export')){
			$type_export = $this->Session->read('type_export.value');
		}


        $out = '<form action="/admin/admins/date_range" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        $out.= '<div class="input-append input-preprend">';
        $out.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $out.= '<input class="m-wrap date-range" type="text" style="border-right: 1px solid #e5e5e5 !important" name="date" value="'.$valDate.'" autocomplete="off">';
        $out.= '</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
		$out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Type') .'</span>';
            $out.= '<select name="type_export" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            $out.= '<option value="par_date"'. ($type_export == 'par_date' ?' selected':'') .'>'.__('Par dates').'</option>';
			$out.= '<option value="par_periode"'. ($type_export == 'par_periode' ?' selected':'') .'>'.__('Par periode').'</option>';
            $out.= '</select>';
            $out.= '</div>';
        $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }
	
	public function getDateInputComNbr($medias = array()){
        if($this->Session->check('Date')){
            $dateStart = $this->Session->read('Date.start');
            $dateEnd = $this->Session->read('Date.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else{
			/*$valDate = date('d-m-Y');
            $valDate.= ' au ';
            $valDate.= date('d-m-Y');*/
		}


        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }

		if($this->Session->check('ConsultTotal')){
			$consult_total = $this->Session->read('ConsultTotal.value');
		}


        $out = '<form action="/admin/admins/date_range" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        $out.= '<div class="input-append input-preprend">';
        $out.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $out.= '<input class="m-wrap date-range" type="text" style="border-right: 1px solid #e5e5e5 !important" name="date" value="'.$valDate.'" autocomplete="off">';
        $out.= '</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
		$out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Total') .'</span>';
            $out.= '<select name="consult_total" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
			$out.= '<option value=""'. ($consult_total == '' ?' selected':'') .'>'.__('Tous').'</option>';
            $out.= '<option value="1"'. ($consult_total == '1' ?' selected':'') .'>0</option>';
			$out.= '<option value="11"'. ($consult_total == '11' ?' selected':'') .'>'.__('< 10').'</option>';
			$out.= '<option value="21"'. ($consult_total == '21' ?' selected':'') .'>'.__('< 20').'</option>';
			$out.= '<option value="51"'. ($consult_total == '51' ?' selected':'') .'>'.__('< 50').'</option>';
			$out.= '<option value="101"'. ($consult_total == '101' ?' selected':'') .'>'.__('< 100').'</option>';
            $out.= '</select>';
            $out.= '</div>';
        $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }

	public function getDateInputStats($medias = array()){
        if($this->Session->check('DateStats')){
            $dateStart = $this->Session->read('DateStats.start');
            $dateEnd = $this->Session->read('DateStats.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else{

			$delai_max_live = date('Y-m-d 23:59:59');
			$dx = new DateTime($delai_max_live);
			$dx->modify('last day of this month');
			$delai_max = $dx->format('d-m-Y');


			$valDate = date('01-m-Y');
            $valDate.= ' au ';
            $valDate.= $delai_max;
		}


        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }


        $out = '<form action="/admin/admins/date_range_stats" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        $out.= '<div class="input-append input-preprend">';
        $out.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $out.= '<input class="m-wrap date-range" type="text" style="border-right: 1px solid #e5e5e5 !important" name="date" value="'.$valDate.'"  autocomplete="off">';
        $out.= '</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
        $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }


	public function getDateInputFront($medias = array()){
        if($this->Session->check('Date')){
            $dateStart = $this->Session->read('Date.start');
            $dateEnd = $this->Session->read('Date.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else
            $valDate = '';

        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }

        $out = ' <form action="/agents/date_range" class="form-inline" style="position:relative" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        //$out.= '<div class="input-append input-preprend">';
        //$out.= '<span class="add-on" style="height: 35px;background-color: #e5e5e5;border: 1px solid #e5e5e5;display: inline-block;font-size: 14px;font-weight: normal;line-height: 24px;min-width: 16px;padding: 4px 5px;text-align: center;text-shadow: 0 1px 0 #ffffff;width: auto;"><i class="icon-calendar"></i></span>';
        //$out.= '<input class="m-wrap date-range" type="text" style="margin-top:-3px;height: 35px;width:190px;border: 1px solid #e5e5e5; font-size:14px;text-align:center;" name="date" value="'.$valDate.'">';
        //$out.= '</div>';
		$out.= '					<div id="reportrange" class="" style="">';
		$out.= '				    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;';
		$out.= '				    <input class="m-wrap date-range form-control" type="text" style="min-width:80%" name="date" value="'.$valDate.'"  autocomplete="off"><!--<b class="caret"></b>-->';
		$out.= '				</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
      //  $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }

	public function getDateInputBigFront($medias = array()){
        if($this->Session->check('Date')){
            $dateStart = $this->Session->read('Date.start');
            $dateEnd = $this->Session->read('Date.end');

            $valDate = $this->Time->format($dateStart, '%d-%m-%Y');
            $valDate.= ' au ';
            $valDate.= $this->Time->format($dateEnd, '%d-%m-%Y');
        }else
            $valDate = '';

        if(!empty($medias)){
            $medias = array_merge(array('all' => 'Tous'), $medias);

            $selected = 'all';
            if($this->Session->check('Media'))
                $selected = $this->Session->read('Media.value');
        }


        $out = '<form action="/agents/date_range" class="form-inline" method="post" accept-charset="utf-8" id="form-date-range">';
        $out.= '<div style="display:none;"><input type="hidden" name="_method" value="POST"></div>';
        //$out.= '<div class="input-append input-preprend">';
        //$out.= '<span class="add-on" style="height: 35px;background-color: #e5e5e5;border: 1px solid #e5e5e5;display: inline-block;font-size: 14px;font-weight: normal;line-height: 24px;min-width: 16px;padding: 4px 5px;text-align: center;text-shadow: 0 1px 0 #ffffff;width: auto;"><i class="icon-calendar"></i></span>';
        //$out.= '<input class="m-wrap date-range" type="text" style="margin-top:-3px;height: 35px;width:190px;border: 1px solid #e5e5e5; font-size:14px;text-align:center;" name="date" value="'.$valDate.'">';
        //$out.= '</div>';
		$out.= '					<div id="reportrange" class="" style="">';
		$out.= '				    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;';
		$out.= '				    <input class="m-wrap date-range form-control" type="text" style="min-width:100%" name="date" value="'.$valDate.'"  autocomplete="off"><!--<b class="caret"></b>-->';
		$out.= '				</div>';
        if(!empty($medias)){
            $out.= '<div class="input-append input-preprend margin-left">';
            $out.= '<span class="add-on">'. __('Media') .'</span>';
            $out.= '<select name="media" class="m-wrap" style="border-right: 1px solid #e5e5e5 !important">';
            foreach($medias as $val => $label){
                $out.= '<option value="'.$val.'"'. ($selected == $val ?' selected':'') .'>'.__($label).'</option>';
            }
            $out.= '</select>';
            $out.= '</div>';
        }
      //  $out.= '<button class="btn green margin-left" type="submit">Ok</button>';
        $out.= '</form>';

        return $out;
    }

	 /* form card lang */
	public function formCardLang($cardId, $id_lang, $data, $isHiddenSystemPage=false, $page_params=array()){
		//Si pas d'id de la page ou de la langue alors création du formulaire impossible
		if(empty($cardId) || empty($id_lang))
			return __('Erreur dans la création du formulaire');
		//Si on a aucune donnée, on initialise les champs
		if(empty($data)){
			$fields = array('meta_title','link_rewrite','meta_keywords','meta_description','content', 'name');
			foreach($fields as $field){
				$data[$field] = '';
			}
		}


			/* Toutes les pages */
			$out = '<div class="panel-contenu"><div class="row-fluid">';
			$out.= '<div class="span4">';
			$out.= $this->Form->inputs(array(
				'legend' => false,
				'CardLang.'.$id_lang.'.lang_id'            => array('type' => 'hidden', 'value' => $id_lang),
				'CardLang.'.$id_lang.'.card_id'            => array('type' => 'hidden', 'value' => $cardId),
				'CardLang.'.$id_lang.'.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data['name']),
				'CardLang.'.$id_lang.'.bg_page'       => array('label' => array('text' => __('Background page (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file'),
			));

			if(!empty($data['bg_page'])){
				$out.=$this->Html->image('/'.Configure::read('Site.cardBgPage').'/'.$data['bg_page'].'?'.$this->Time->gmt());
			}

			$out.= $this->Form->inputs(array(		'CardLang.'.$id_lang.'.bg_card'       => array('label' => array('text' => __('Background card (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file'),
			));

			if(!empty($data['bg_card'])){
				$out.=$this->Html->image('/'.Configure::read('Site.cardBg').'/'.$data['bg_card'].'?'.$this->Time->gmt());
			}

		$out.= $this->Form->inputs(array(		'CardLang.'.$id_lang.'.video'       => array('label' => array('text' => __('Vidéo step 2'), 'class' => 'control-label'), 'type' => 'file'),
		));

		if(!empty($data['video'])){
			$out.='<video width="30%" height="30%" controls autoplay="true" muted>
					<source src="'.'/'.Configure::read('Site.videoCard').'/'.$data['video'].'?'.$this->Time->gmt().'" type="video/mp4">
				</video>';
		}


		$out.= $this->Form->inputs(array(	'CardLang.'.$id_lang.'.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label required'), 'value' => $data['meta_title'], 'type' => 'text', 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaTitle2').'</span></p></div>'),
				'CardLang.'.$id_lang.'.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label'), 'value' => $data['link_rewrite']),
				'CardLang.'.$id_lang.'.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'value' => $data['meta_keywords'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaKeywords').'</span></p></div>'),
				'CardLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>'),
				'CardLang.'.$id_lang.'.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'value' => $data['meta_description'], 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').'<span class="decompte">'.Configure::read('Site.lengthMetaDescription2').'</span></p></div>')
			));

			$out.= '</div><div class="span8"> <h3>'. __("Contenu").'</h3><div class="tinymce"  style="width: 750px;">';
			$out.= $this->Form->input('CardLang.'.$id_lang.'.content', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['content']));
			$out.= '</div></div>';

		$out.= '<div class="span8"> <h3>'. __("Texte explicatif").'</h3><div class="tinymce"  style="width: 750px;">';
		$out.= $this->Form->input('CardLang.'.$id_lang.'.text_explicatif', array('type' => 'textarea', 'tinymce' => true, 'id' => 'texta_s'.$id_lang, 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data['text_explicatif']));
		$out.= '</div></div></div>';

			$out .= '<div class="panel-contenu"><div class="row-fluid">';
			$out.= '</div>';
			$out.= '</div>';

			$out.= '</div>';

		return $out;
	}

	/* form rule Card */
	public function formRuleCards($cardId, $data, $page_params=array(),$cardOption){
		//Si pas d'id de la page ou de la langue alors création du formulaire impossible
		if(empty($cardId) )
			return __('Erreur dans la création du formulaire');
		//Si on a aucune donnée, on initialise les champs
		if(empty($data)){
			$fields = array('name','card_number','card_id');
			foreach($fields as $field){
				$data[$field] = '';
			}
		}

		$out = '<div class="panel-contenu"><div class="row-fluid">';
		$out.= '<div class="span4">';

		$out.= $this->Form->inputs(array(
			'legend' => false,
			'name'   => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data[0]['RuleCard']['name'], 'required' => true),
			'card_number' => array('label' => array('text' => __('Nombre des cartes'), 'class' => 'control-label'), 'value' => $data[0]['RuleCard']['card_number'], 'type' => 'number'),
			'number_card_drawing' => array('label' => array('text' => __('Nombre des cartes tirer'), 'class' => 'control-label'), 'value' => $data[0]['RuleCard']['number_card_drawing'], 'type' => 'number'),
			'display_mode' => array('label' => array('text' => __("Mode D'affichage"), 'class' => 'control-label required'), 'options' => array('Ligne'=>'Ligne','Cercle'=>'Cercle','Etoile'=>'Etoile','Pyramide'=>'Pyramide'), 'value' => $data[0]['RuleCard']['display_mode'], 'required' => true),
			'card_id'     => array('label' => array('text' => __('Choisir card'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $cardOption, 'value' => $data[0]['RuleCard']['card_id']),

		));

		$out.= '</div>';

		$out.= '</div>';

		return $out;
	}

	/* form rule Card */
	public function formItemCard($cardId, $data, $page_params=array(),$cardOption){
		//Si pas d'id de la page ou de la langue alors création du formulaire impossible
		if(empty($cardId) )
			return __('Erreur dans la création du formulaire');
		//Si on a aucune donnée, on initialise les champs
		if(empty($data)){
			$fields = array('name','image','card_id');
			foreach($fields as $field){
				$data[$field] = '';
			}
		}
		$out = '<div class="panel-contenu"><div class="row-fluid">';
		$out.= '<div class="span4">';

		$out.= $this->Form->inputs(array(
			'legend' => false,
			'name'   => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'value' => $data[0]['CardItem']['name'], 'required' => true),
			'image'       => array('label' => array('text' => __('Image Carte (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file'),
		));

		if(!empty($data[0]['CardItem']['image'])){
			$out.= $this->Html->image('/'.Configure::read('Site.cardItem').'/'.$data[0]['CardItem']['image'].'?'.$this->Time->gmt());
			$out.= '<br><br>';
		}

		$out.= $this->Form->inputs(array(
			'card_id'     => array('label' => array('text' => __('Choisir card'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $cardOption, 'value' => $data[0]['CardItem']['card_id']),
		));


		$out.= '</div><div class="span8"> <h3>'. __("Description").'</h3><div class="tinymce"  style="width: 750px;">';
		$out.= $this->Form->input('description', array('type' => 'textarea', 'tinymce' => true, 'id' => 'textacard', 'label' => false, 'div' => false, 'between' => false, 'after' => false, 'value' => $data[0]['CardItem']['description']));
		$out.= '</div></div>';

		$out.= '</div>';

		return $out;
	}



    public function getCssJsLinks()
    {
        $assets = $this->css('/assets/plugins/bootstrap/css/bootstrap.min.css');
        $assets.= $this->css('/assets/plugins/bootstrap/css/bootstrap-responsive.min.css');
        $assets.= $this->css('/assets/plugins/font-awesome/css/font-awesome.min.css');
        $assets.= $this->css('/assets/css/style-metro.css');
        $assets.= $this->css('/assets/css/style.css');
        $assets.= $this->css('/assets/css/style-responsive.css');
        $assets.= $this->css('/assets/css/themes/default.css');
        $assets.= $this->css('/assets/plugins/uniform/css/uniform.default.css');
        $assets.= $this->css('/assets/plugins/select2/select2_metro.css');
        $assets.= $this->css('/assets/css/pages/login.css');
        $assets.= $this->script('/assets/plugins/jquery-1.10.1.min.js');
        $assets.= $this->script('/assets/plugins/jquery-migrate-1.2.1.min.js');
        $assets.= $this->script('/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js');
        $assets.= $this->script('/assets/plugins/bootstrap/js/bootstrap.min.js');
        $assets.= $this->script('/assets/plugins/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js');
        $assets.= $this->script('/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js');
        $assets.= $this->script('/assets/plugins/jquery.blockui.min.js');
        $assets.= $this->script('/assets/plugins/jquery.cookie.min.js');
        $assets.= $this->script('/assets/plugins/uniform/jquery.uniform.min.js');
        $assets.= $this->script('/assets/plugins/jquery-validation/dist/jquery.validate.min.js');
        $assets.= $this->script('/assets/plugins/select2/select2.min.js');
        $assets.= $this->script('/assets/scripts/app.js');
        $assets.= $this->script('/assets/scripts/login.js');
        return $assets;
    }
}
