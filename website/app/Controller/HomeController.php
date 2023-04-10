<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Category');
require_once 'Component/geoip.inc';

class HomeController extends AppController {
    public $helpers = array('Html', 'FrontBlock');
    public $layout = 'black_blue_root';

    public function beforeFilter(){

        parent::beforeFilter();

        $this->Auth->allow('clairagents_quality', 'advantage', 'update_agent', 'media_phone','ajaxactivity');
    }

    private function geolocDetect()
    {

        $domain_id = $this->Session->read('Config.id_domain');
        if (!in_array($domain_id, array(15,23,24)))return false;


		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if(!$ip)$ip = $this->request->clientIp(true);


		$currentDir = dirname(__FILE__);
      
     try {
					$gi = geoip_open($currentDir."/Component/GeoIP.dat", GEOIP_STANDARD);
          $country_code = geoip_country_code_by_addr($gi, $ip);//$this->freegeoip_get($ip);
          geoip_close($gi);
				}
					catch (Exception $e) {
									$datasEmail = array(
												'content' => $e->getMessage(),
												'PARAM_URLSITE' => 'https://fr.spiriteo.com'
									);
									$extractrl->sendEmail('system@web-sigle.fr','BUG GeoIp','default',$datasEmail);
			}  
      
		

        if (!$country_code)return false;

        $this->loadModel('Geoloc');
        $res = $this->Geoloc->find("first", array(
            'conditions' => array('country_code' => $country_code)
        ));
        if (empty($res)){
            $this->Geoloc->create();
            $this->Geoloc->save(array(
                'country_code' => $country_code,
                'domain_id'    => 0,
                'lang_id'      => 0
            ));
            return false;
        }

        $domain     = $res['Domain']['domain'];
        $lang_code  = $res['Lang']['language_code'];
        if (empty($domain) || empty($lang_code))return false;

		if (in_array($res['Domain']['id'], array(26)))return false;//patch us

		switch ($domain) {
			case 'ca.spiriteo.com':
			case 'ca.devspi.com':
				$lang_code = 'frc';
				break;
			case 'be.spiriteo.com':
			case 'be.devspi.com':
				$lang_code = 'frb';
				break;
			case 'lu.spiriteo.com':
			case 'lu.devspi.com':
				$lang_code = 'frl';
				break;
			case 'ch.spiriteo.com':
			case 'ch.devspi.com':
				$lang_code = 'frs';
				break;
			case 'fr.spiriteo.com':
			case 'fr.devspi.com':
				$lang_code = 'fre';
				break;
		}

        $url = 'https://'.$domain.'/';//.$lang_code;
        $this->redirect($url);

    }

	private function geolocPopupDetect()
    {
        $domain_id = $this->Session->read('Config.id_domain');


		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if(!$ip)$ip = $this->request->clientIp(true);
    if (!$ip)return false;
		$currentDir = dirname(__FILE__);
    
    try {
					$gi = geoip_open($currentDir."/Component/GeoIP.dat", GEOIP_STANDARD);
          $country_code = geoip_country_code_by_addr($gi, $ip);//$this->freegeoip_get($ip);
          geoip_close($gi);
				}
					catch (Exception $e) {
									$datasEmail = array(
												'content' => $e->getMessage(),
												'PARAM_URLSITE' => 'https://fr.spiriteo.com'
									);
									$extractrl->sendEmail('system@web-sigle.fr','BUG GeoIp','default',$datasEmail);
			}  
    
    if (!$country_code)return false;

        $this->loadModel('Geoloc');
        $res = $this->Geoloc->find("first", array(
            'conditions' => array('country_code' => $country_code)
        ));
        if (empty($res)){
            $this->Geoloc->create();
            $this->Geoloc->save(array(
                'country_code' => $country_code,
                'domain_id'    => 0,
                'lang_id'      => 0
            ));
            return false;
        }

		$domain     = $res['Domain']['domain'];
		$domain_new = $res['Domain']['id'];
        $lang_code  = $res['Lang']['language_code'];

        if (empty($domain) || empty($lang_code))return false;


		$this->loadModel('Redirection');
        $res_redir = $this->Redirection->find("first", array(
            'conditions' => array('ip' => $ip, 'domain' => $domain_id)
        ));
    if ($res_redir)return false;
		/*if (count($res_redir)){
			$date_old = new DateTime($res_redir['Redirection']['date_redir']);
			$date_new = new DateTime(date('Y-m-d H:i:s'));
        	$interval = $date_new->diff($date_old);
			$nb_jour = $interval->format('%a');
			if($nb_jour <= 90 )
				return false;
		}*/

		if($domain_id != $domain_new){

			$pays = '';
			switch ($res['Domain']['iso']) {
				case "fr":
					$pays = __('en France');
					break;
				case "be":
					$pays = __('en Belgique');
					break;
				case "lu":
					$pays = __('au Luxembourg');
					break;
				case "ch":
					$pays = __('en Suisse');
					break;
				case "ca":
					$pays = __('au Canada');
					break;
			}

			if(!$pays) return false;
			 App::import("Model", "Domain");
            $model = new Domain();
            $countries = $model->find('first', array(
                'fields' => array('Domain.domain', 'Domain.country_id', 'CountryLang.name', 'Domain.id', 'Lang.language_code'),
                'conditions' => array('Domain.active' => 1, 'Domain.id' => $domain_id),
                'joins' => array(
                    array(
                        'table' => 'countries',
                        'alias' => 'Country',
                        'type'  => 'inner',
                        'conditions' => array(
                            'Country.active = 1',
                            'Country.id = Domain.country_id'
                        )
                    ),
                    array(
                        'table' => 'country_langs',
                        'alias' => 'CountryLang',
                        'type'  => 'left',
                        'conditions' => array(
                            'CountryLang.country_id = Domain.country_id',
                            'CountryLang.id_lang = '.$this->Session->read('Config.id_lang')
                        )
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = Domain.default_lang_id')
                    )
                ),
                'order' => 'order_on_generiq_page ASC',
                'recursive' => -1
            ));

			$nom_pays = $countries["CountryLang"]["name"];

			if($this->request->isMobile()){
				$nom_pays = substr(	$countries["CountryLang"]["name"],0,2);
			}

			$html_redir = '<!-- Modal -->
<div class="modal fade modal-footer-hide" id="myModalRedir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="m-title">
             Spiriteo est aussi disponible '.$pays.'
          </h4>
      </div>
      <div class="modal-body">
	  <div class="blocklangpopup"><a href="#"><img src="/theme/default/img/flag/'.strtolower($countries["CountryLang"]["name"]).'.png" alt="">'.$nom_pays.'</a></div>
	  <div class="content">
	  	<p class="popup_redir_texte">Voulez vous aller sur '.$domain.' ?</p>
	  	<ul class="list-group-country">
			<li>
				<a class="list-group-item-country list-group-item-country-oui" href="https://'.$domain.'">
					<span class="flags">
					Oui, aller sur '.$domain.'
					</span>
					<span class="arrow pull-right">
						<img src="/theme/default/img/arrow.png">
					</span>
				</a>
			</li>
			<li>
				<a class="list-group-item-country list-group-item-country-non closepopupredir"  href="/home/redir">
				<!-- data-dismiss="modal" aria-hidden="true" -->
					<span class="flags">
					Non, rester sur '.$_SERVER['SERVER_NAME'].'
					</span>
					<span class="arrow pull-right">
						<img src="/theme/default/img/arrow.png">
					</span>
				</a>
			</li>
		</ul>
      </div></div>
      <div class="modal-footer">

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';

			return $html_redir;
		}
    }

	public function redir(){

		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if(!$ip)$ip = $this->request->clientIp(true);
    $domain_id = $this->Session->read('Config.id_domain');
		$this->loadModel('Redirection');
        $res = $this->Redirection->find("first", array(
            'conditions' => array('ip' => $ip, 'domain' =>$domain_id )
        ));
    if (!$res){
     $this->Redirection->create();
      
      $data_redir = array();
      $data_redir['Redirection'] = array(
                'ip' => $ip,
				        'domain' => $domain_id,
                'date_redir'    => date('Y-m-d H:i:s')
            );
      
         $this->Redirection->save($data_redir);
		}
		

		$this->redirect('https://'.$_SERVER['SERVER_NAME']);
	}

    private function freegeoip_get($ip=false)
    {
        if(!filter_var($ip, FILTER_VALIDATE_IP))return false;

        $country_code = $this->Session->read('myGeoIpCountryCode');
        if ($country_code)return $country_code;

        $curl = curl_init();

        $options = array(
            CURLOPT_URL            => 'https://freegeoip.net/json/'.$ip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_TIMEOUT        => 5
        );

        // Configuration des options de téléchargement
        curl_setopt_array($curl, $options);

        // Exécution de la requête
        $content = curl_exec($curl);

        // Fermeture de la session cURL
        curl_close($curl);

        $datas = json_decode($content);
        $country_code = isset($datas->country_code)?$datas->country_code:false;

        if ($country_code)
            $this->Session->write('myGeoIpCountryCode', $country_code);
        return $country_code;
    }
    public function index($page = 1)
    {
	
	
	//return;
        $this->Session->write('type_modal','login');
        $lang = $this->Session->read('lang');

        //if (!isset($this->params['language']))
          //  $this->redirect(array('controller' => 'home','action'=> 'index', 'language' => $this->Session->read('lang')));

		 /*force redirect 301 */

		 if (isset($this->params['language']) && $this->params['page'] < 2){
			 $this->response->statusCode(301);
			 $this->redirect('https://'.$_SERVER['SERVER_NAME'], array('status' => 301));
		 }

         $generiq_domains = explode(',',Configure::read('Site.id_domain_com'));



		$this->geolocDetect();
		$popup_redir = $this->geolocPopupDetect();

        if(in_array($this->Session->read('Config.id_domain'), $generiq_domains)){
            $this->layout = '';
            //Les domaines actifs du site
            $this->loadModel('Domain');
            $domains = $this->Domain->find('all', array(
                'fields'    => array('Domain.*', 'CategoryLang.meta_title'),
                'conditions' => array('Domain.active' => 1, 'Domain.domain LIKE' => '%'.Configure::read('Site.nameDomain').'%'),
                'joins'     => array(
                    array(
                        'table' => 'category_langs',
                        'alias' => 'CategoryLang',
                        'type'  => 'left',
                        'conditions' => array('CategoryLang.category_id' => 1, 'CategoryLang.lang_id = Domain.default_lang_id')
                    )
                ),
                'recursive'     => -1,
                'order'       => 'order_on_generiq_page ASC'
            ));


            foreach ($domains AS $k => $v)
                if(in_array($v['Domain']['id'], $generiq_domains))
                    unset($domains[$k]);

            $this->set(compact('domains'));
        }else{
            /* On accède au controller categories */
            $catController = new CategoryController();
            $catController->constructClasses();
            //On récupère les filtres
            $filters = $catController->initFilterProperties();
            $args = array();
            //Si on a des paramètres query, on retranscrit les vrais noms des filtres


            if(isset($this->params->query[$this->queryIndex['ajax_for_agents']])){
                foreach($this->queryIndex as $fullName => $queryName){
                    if(isset($this->params->query[$queryName]))
                        $args[$fullName] = $this->params->query[$queryName];
                }
                //Si un filtre est activé en le garde en mémoire
                if(!empty($this->params->query[$this->queryIndex['orderby']])){
                    $filters['filter_orderby'][$this->params->query[$this->queryIndex['orderby']]]['active'] = true;
                }
                if(!empty($this->params->query[$this->queryIndex['filterby']])){
                    $filters['filter_filterby'][$this->params->query[$this->queryIndex['filterby']]]['active'] = true;
                }
            }


            $this->set($filters);

            /* On récupère les datas */
				$datasForView = array();//$catController->getDatasForCategory(1, $page, $args);
				$datasForView['page']= $page;
				$datasForView['mediaChecked']= array();
				if(isset($datasForView['countAgents']))
					$this->site_vars['NbAgents'] = $datasForView['countAgents'];
				else
					$this->site_vars['NbAgents'] = 0;
				$datasForView['isMobile'] = $this->request->isMobile();
				$this->set($datasForView);


			//var_dump($datasForView);	exit;
            //On récupère la catégorie Accueil

			$idlang = $this->Session->read('Config.id_lang');
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($idlang == 1){
				if($extension == 'ca')$idlang=8;
				if($extension == 'ch')$idlang=10;
				if($extension == 'be')$idlang=11;
				if($extension == 'lu')$idlang=12;
			}

			$this->loadModel('CategoryLang');

			$categoryLang = $this->CategoryLang->find('first', array(
                'conditions'    => array('CategoryLang.category_id' => 1, 'CategoryLang.lang_id' => $idlang),
                'recursive'     => -1
            ));

			/*$category_temp = $categoryLang;

            $categoryLang = $this->CategoryLang->find('first', array(
                'conditions'    => array('CategoryLang.category_id' => 1, 'CategoryLang.lang_id' => $this->Session->read('Config.id_lang')),
                'recursive'     => -1
            ));

			$categoryLang['CategoryLang']['description'] = $category_temp['CategoryLang']['description'];
			$categoryLang['CategoryLang']['meta_title'] =$category_temp['CategoryLang']['meta_title'];
			$categoryLang['CategoryLang']['meta_keywords'] =$category_temp['CategoryLang']['meta_keywords'];
			$categoryLang['CategoryLang']['meta_description'] =$category_temp['CategoryLang']['meta_description'];*/

            /* On construit le lien de la catégorie */
            $category_link = $this->getCategoryLink($categoryLang['CategoryLang']['link_rewrite'], false, true);
            $this->set('category_link', $category_link);
			$this->set('popup_redir', $popup_redir);

			$this->set(compact('categoryLang'));

            if(!empty($categoryLang)){
                /* Metas */
                $this->site_vars['meta_title']          = $categoryLang['CategoryLang']['meta_title2'];
                $this->site_vars['meta_keywords']       = $categoryLang['CategoryLang']['meta_keywords2'];
                $this->site_vars['meta_description']    = $categoryLang['CategoryLang']['meta_description2'];
            }
			if($this->Session->read('Config.id_domain') == 15){
				$this->site_vars['meta_title']          = __('agents sérieuse par telephone, tchat, email – Spiriteo');
                $this->site_vars['meta_description']    = __('Bénéficiez de 5 minutes de agents gratuites et consultez de vrais professionnels de la agents en direct, tirage de tarot, cartomancie, astrologie et plus ');
			}

			$cat_avisAVG = 0;
			$cat_ratingCount = 0;
			$this->loadModel('Review');

			$reviews = $this->Review->find('all',array(
							'fields' => array('AVG(pourcent) as avg','count(pourcent) as nb'),
							'conditions' => array('status' => 1, 'parent_id' => NULL),
							'recursive' => -1,
							'limit' => -1
						));
			$reviews = $reviews[0][0];
			$cat_avisAVG = number_format($reviews['avg'],1);
			$cat_ratingCount = $reviews['nb'];
				
			
			/* 
			 * SOFTPEOPLE 25-6-2022
			 * AJOUT DES AGENTS DANS LA PAGE D ACCUEIL
			 */
			/*
			$conditions=['Role' => 'agent',
				    'active' => 1,
				    'valid' => 1,
				    'Agent_status' => 'available',
				    'Status' => '8'
				    ];
			
			$this->loadModel(User); 
			$agents =$this->User->find("all", ['conditions' =>$conditions]);
			$this->set('agents',$agents);
			*/
			
			/* FIN SOFTPEOPLE */ 
			
			$this->set(compact('cat_avisAVG','cat_ratingCount'));

			//$this->render('/Category/display');
        }
    }


    public function clairagents_quality(){
        //On récupère les catégories
        $this->loadModel('Category');
        //Les dernières catégories actives
        $lastCategories = $this->Category->find('list',array(
            'conditions' => array('active' => 1),
            'order' => 'date_add desc',
            'limit' => 4,
            'recursive' => -1
        ));

        //Les infos pour les dernières catégories
        $categories = $this->Category->CategoryLang->find('all',array(
            'fields' => array('CategoryLang.name', 'CategoryLang.description', 'CategoryLang.meta_keywords', 'CategoryLang.category_id', 'CategoryLang.link_rewrite'),
            'conditions' => array('category_id' => $lastCategories, 'lang_id' => $this->Session->read('Config.id_lang')),
            'recursive' => -1
        ));

        //Les métas
        $this->site_vars['meta_title'] = __('agents de qualité');
        foreach($categories as $category){
            if(empty($this->site_vars['meta_keywords']))
                $this->site_vars['meta_keywords'] = $category['CategoryLang']['meta_keywords'];
            else
                $this->site_vars['meta_keywords'].= ','.$category['CategoryLang']['meta_keywords'];
        }

        $this->set(compact('categories'));
    }

    public function advantage(){
        //Les métas
        $this->site_vars['meta_title'] = __('Vos avantages');
    }

    public function media_phone(){
        if($this->request->is('ajax')){

            $requestData = $this->request->data;
            $this->loadModel('User');
            $this->layout = '';

            //On récupère les infos de l'agent
            $agent = $this->User->getAgent($requestData['id']);

            //Si pas d'agent trouvé
            if($agent === false){
                //Retourne false, pas de lightbox
                $this->jsonRender(array('return' => false, 'msg' => __('Expert introuvable.')));
            }else{
                $this->loadModel('CountryLangPhone');
				$this->loadModel('CountryLang');
                $phones = $this->CountryLangPhone->find('first', array(
                    'conditions'    => array('country_id' => $this->Session->read('Config.id_country'), 'lang_id' => $this->Session->read('Config.id_lang')),
                    'recursive'     => -1
                ));
				$all_phones = $this->CountryLangPhone->find('all', array(
                    'conditions'    => array('lang_id' => 1),
                    'recursive'     => -1
                ));

				$countries = $this->CountryLang->find('all', array(
                    'conditions'    => array('id_lang' => 1),
                    'recursive'     => -1
                ));

                //Si pas de phones pour ce pays et cette langue
                if(empty($phones))
                    //Alors on récupère pour les Français
                    $phones = $this->CountryLangPhone->find('first', array(
                        'conditions'    => array('country_id' => 1, 'lang_id' => 1),
                        'recursive'     => -1
                    ));

                //Si toujours pas ben, erreur
                if(empty($phones))
                    $this->jsonRender(array('return' => false, 'msg' => __('Une erreur est survenue.')));

				//$type_modal = 'login';
				if($this->Session->read('type_modal'))$type_modal = $this->Session->read('type_modal');
                $this->set(compact('phones', 'agent','type_modal','all_phones','countries'));
                $response = $this->render();


                $this->set(array('title' =>__('Consultation par tél.<br />avec<span>').' '.$agent['User']['pseudo'].'</span>', 'content' => $response->body(), 'User' => $agent['User']));
				$this->loadModel('UserCountry');
				$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
                $response = $this->render('/Elements/modal_consult');
                $this->jsonRender(array('return' => true, 'html' => $response->body()));
            }
        }
        $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    public function voice_call(){
        if($this->request->is('ajax')){

            $requestData = $this->request->data;
            $this->loadModel('User');
            $this->layout = '';

            //On récupère les infos de l'user
            $user = $this->User->getByPhoneNumberApi($requestData['user_name']);

            //Si pas d'user trouvé
            if($user === false){
                //Retourne false, pas de lightbox
                $this->jsonRender(array('return' => false, 'msg' => __('Expert introuvable.')));
            }else{
                $this->set(compact('user'));
                $response = $this->render();
                $this->set(array('title' =>__('Consultation par tél.<br />avec<span>').' '.$user['User']['pseudo'].'</span>', 'content' => $response->body(), 'User' => $user['User']));
                $response = $this->render('/Elements/modal_consult');

                $this->jsonRender(array('return' => true, 'html' => $response->body()));
            }
        }
    }

    public function update_agent($idAgent){
        if($this->request->is('ajax')){
            $this->loadModel('User');
            $agent = $this->User->getAgent($idAgent);

            //Si pas d'agent
            if($agent === false)
                $this->jsonRender(array('return' => false));

            $datas = array('status' => $agent['User']['agent_status']);
            if ($agent['User']['agent_status'] == 'available')
                $datas['label'] = '<span class="icon_status"></span>'.__('Disponible');
            elseif ($agent['User']['agent_status'] == 'busy')
                $datas['label'] = '<span class="icon_status"></span>'.__('En consultation');
            elseif ($agent['User']['agent_status'] == 'unavailable')
                $datas['label'] = '<span class="icon_status"></span>'.__('Indisponible');

            if($agent['User']['agent_status'] === 'available' && $agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])){
                $datas['chat']['class'] = '';
                $datas['chat']['html'] = '<a href="'.Router::url(array('controller' => 'chats', 'action' => 'create_session', 'id' => $agent['User']['id'])).'" class="nx_chatbox"><span class="glyphicon glyphicon-play margin_right_5"></span>'.__('Consultez par chat').'</a>';
            }else{
                $datas['chat']['class'] = 'disabled';
                $datas['chat']['html'] = '<span class="disabled_media glyphicon glyphicon-play margin_right_5"></span>'.__('Consultez par chat').'';
            }

            if($agent['User']['agent_status'] === 'available' && $agent['User']['consult_email'] == 1){
            //if($agent['User']['consult_email'] == 1){
                $datas['email']['class'] = '';
                $datas['email']['html'] = '<a href="/accounts/new_mail/'.$agent['User']['id'].'" class="nx_emailbox"><span class="glyphicon glyphicon-play margin_right_5"></span>'.__('Consultez par e-mail').'</a>';
            }
            else{
                $datas['email']['class'] = 'disabled';
                $datas['email']['html'] = '<p><span class="disabled_media glyphicon glyphicon-play margin_right_5"></span>'.__('Consultez par e-mail').'</p>';
            }

            if($agent['User']['agent_status'] === 'available' && $agent['User']['consult_phone'] == 1)
                $datas['phone']['class'] = '';
            else
                $datas['phone']['class'] = 'disabled';



            $this->jsonRender(array('return' => true, 'datas' => $datas));
        }
    }

    private function agentActif($date){
        if(empty($date))
            return false;

        $dateNow = date('Y-m-d H:i:s');
        $tmstmpStart = new DateTime($date);
        $tmstmpStart = $tmstmpStart->getTimestamp();
        $tmstmpEnd = new DateTime($dateNow);
        $tmstmpEnd = $tmstmpEnd->getTimestamp();
        $sec = ($tmstmpEnd - $tmstmpStart);

        //Inactif si délai plus grand que le max autorisé
        if($sec > Configure::read('Chat.maxTimeInactif'))
            return false;

        return true;
    }
}
