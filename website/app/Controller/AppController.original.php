<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('Folder', 'Utility');
App::import('Vendor', 'Noox/Tools');
App::uses('CakeTime', 'Utility');
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */

class AppController extends Controller {
    protected $init_mail_parameters_done = false;
    protected $nosubscribe = false;
    protected $mail_vars = array();
    protected $consult_medias = array(
        'phone'     =>      'Téléphone',
        'chat'      =>      'Chat',
        'email'     =>      'E-mail'
    );
    protected $nx_roles = array('admin','agent','client');
    protected $nx_allowed_agent_statuses = array('busy','available','unavailable');
    public $current_id_lang = 0;
    //Permet de faire correspondre les noms des parametres query avec le nom des filtres pour la pagination des agents
    public $queryIndex = array('ajax_for_agents' => 'afa','id_category' => 'cat','term' => 't','orderby' => 'o','filterby' => 'f','media' => 'm');
    public $allowed_mime_types = array(
        'Image' => array('image/jpeg','image/gif','image/png'),
        'Audio' => array('audio/mp3', 'audio/mpeg'),
        'Video' => array('video/mp4'),
		'Document' => array('image/jpeg','image/gif','image/png','application/msword','application/rtf','application/vnd.ms-excel','application/vnd.oasis.opendocument.text','application/vnd.oasis.opendocument.spreadsheet','text/plain','text/csv','text/tsv','application/csv','application/txt')
    );
	public $helpers = array(
        'Text',
        'Form' => array('className' => 'BootstrapForm'),
        'Html' => array('className' => 'CustomHtml'),
        'Session','Metronic','Nooxtools','Frontblock','MinifyHtml.MinifyHtml','Asset'
    );

	public $components = array(
	    //'DebugKit.Toolbar',
	    'Session',
	    'Cookie',
	    'Auth' => array(
	        'authorize'     => 'Controller',
	        'loginAction'   =>  array(
                'controller' => 'users',
                'action'     => 'login',
                'admin'      => false,
                'membre'     => false
            ),
            'authError' => 'Pensiez-vous réellement que vous étiez autorisés à voir cela ?',
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'User',
                    'fields' => array('username' => 'email', 'password' => 'passwd'),
                    'scope'  => array('User.active = 1','User.deleted = 0', 'User.emailConfirm = 1')
                )
            ),
            'logoutRedirect' => array('controller' => 'home', 'action' => 'index')
        )
	);
	public $site_vars = array('meta_title'  =>  '', 'css_links' => array());
	
	
	public $layout = 'black_blue';

    protected $logos_ssl_hosting = array(
        11 => 'https://secure.SecureLogo.com/images/spiriteo_3.jpg',
        13 => 'https://secure.securelogo.com/images/spiriteo_2.jpg',
        14 => 'https://secure.SecureLogo.com/images/spiriteo_6.jpg',
        19 => 'https://secure.securelogo.com/images/spiriteo.jpg',
        21 => 'https://secure.securelogo.com/images/spiriteo_7.jpg',
        22 => 'https://secure.securelogo.com/images/spiriteo_4.jpg',
        29 => 'https://secure.securelogo.com/images/spiriteo_5.jpg'
    );

    protected function jsonRender($array=array())
    {
		//header('Content-Encoding: deflate, gzip');
        header('Content-type: text/json');
        header('Content-type: application/json');
		//header('Transfer-Encoding: gzip');
        echo json_encode($array);
        die();
    }

    protected function declareCssLink($css_link=false)
    {
        if (!$css_link)return false;
        if (!in_array($css_link, $this->site_vars['css_links']))
            $this->site_vars['css_links'][] = $css_link;
    }
	function beforeRender()
	{
		if(Configure::read('Site.maintenance')){
			echo '<body style="background: #61439C;padding:40px;"><p style="text-align: center;color:#fff;font-size:25px"><img src="https://fr.spiriteo.com/theme/default/img/logo.png" alt="Devspi" class="img-responsive"><br /><br /><br />Le site est en maintenance.<br /><br />De retour dans moins d\'une heure.<br /><br />Merci pour votre compréhension,<br />L\'équipe Spiriteo</p></body>';exit;
		}
		$user = $this->Auth->user();
		if(!empty($user) && $user['role'] === 'admin'){
			$this->loadModel('UserLevel');
			  $level = $this->UserLevel->find('first', array(
				'conditions'    => array('user_id' => $this->Auth->user('id')),
				'recursive'     => -1
			));
			$this->set('user_level', isset($level['UserLevel']['level']) ? $level['UserLevel']['level'] : null);
			$this->saveAdminLog();
			$this->checkSupportFil();
		}
		$this->checkBadLink();
		$this->checkRedirect();
		$this->checkActivate();



		$in_page = false;
		if($this->params['controller'] == 'agents' && $this->params['action'] == 'profil' )$in_page = true;
		 if (!empty($user) && $user['role'] === 'agent' && !$in_page){
			 $this->checkCGV();
			 $this->checkIban();
		 }
		$in_page = false;
		if($this->params['controller'] == 'agents' && $this->params['action'] == 'vatnum' )$in_page = true;
		if($this->params['controller'] == 'agents' && $this->params['action'] == 'profil' )$in_page = true;
		if (!empty($user) && $user['role'] === 'agent' && !$in_page)
			 $this->checkVAT();

        if(isset($this->params['prefix']) && $this->params['prefix'] === 'admin' ){
            if(!empty($this->layout))
                $this->layout = 'admin_layout';
            //Configure::write('debug', 2);
            setlocale (LC_ALL, "fr_FR.utf8");
            $this->Session->write('Config.language','fre');
            //Configure::write('debug', 2);
        }
		$query_data = $this->params->query;


		//check crm click
		/*$this->loadModel('CrmStat');
		if($query_data['i']){
				$this->CrmStat->id = $query_data['i'];
				$this->CrmStat->saveField('click', 1);
		}*/

        $this->consult_medias = array(
            'phone'     =>      __('Téléphone'),
            'chat'      =>      __('Chat'),
            'email'     =>      __('E-mail')
        );
        $this->consult_medias_for_filters = array(
        'phone'     =>      __('agents par Téléphone'),
        'chat'      =>      __('agents par Chat'),
        'email'     =>      __('agents par E-mail')
    );
        $this->set('consult_medias', $this->consult_medias);
        $this->set('consult_medias_for_filters', $this->consult_medias_for_filters);
        $this->set('isNoox', $this->isNoox());

        /* On passe l'utilisateur courant à la vue */
        if ($this->layout == 'admin_layout'){
            $this->current_user = $this->Auth->user();
            $badge = $this->updateBadge();
            $this->set(array('current_user' => $this->current_user, 'badge' => $badge));
        }

        /* On passe les variables de site à la vue */
        if (empty($this->site_vars['meta_title']))
            $this->site_vars['meta_title'] = Configure::read('Site.name');


        $this->set('site_vars', $this->site_vars);

        parent::beforeRender();
    }
    private function _getCountryByDomain()
    {
        if ($this->Session->check('Config.id_country') && $this->Session->check('Config.default_iso_lang')) return true;

        /* On récupère le domaine */
            $pieces = parse_url(Router::url('/', true));
            $host = $pieces['host'];

        /* Et sa configuration en db */
            $this->loadModel('Domain');
            $this->loadModel('Country');

            $domain = $this->Domain->findByDomain($host);
          //  var_dump($host); die;

            if (!empty($domain)){
                //Timezone et devise de l'utilisateur
                $countryInfo = $this->Country->find('first', array(
                    'fields' => array('timezone', 'devise', 'devise_iso'),
                    'conditions' => array('Country.id' => $domain['Domain']['country_id']),
                    'recursive' => -1
                ));
            }

            if (!isset($domain['Domain']['active']) || (int)$domain['Domain']['active'] != 1){
                /* Domaine désactivé, ou domaine introuvable, on génère une erreur */
                    $this->layout = '';
                    throw new NotFoundException(__('Invalid domain'));
                    return true;
            }

            $id_country = isset($domain['Domain']['country_id'])?(int)$domain['Domain']['country_id']:1;
            $this->set('id_country', $id_country);

        /* On récupère l'iso de la langue par défaut */
            $this->loadModel('Lang');
            $lang = $this->Lang->findByIdLang((isset($domain['Domain']['default_lang_id']) && (int)$domain['Domain']['default_lang_id']>0)?(int)$domain['Domain']['default_lang_id']:1);
            if (empty($lang))
                throw new NotFoundException(__('Invalid lang'));

        /* On sauve les 5 valeurs en Session */
            $this->Session->write('Config.id_country', $id_country);
            $this->Session->write('Config.id_domain', $domain['Domain']['id']);
            $this->Session->write('Config.default_iso_lang', isset($lang['Lang']['language_code'])?$lang['Lang']['language_code']:false);
            $this->Session->write('Config.timezone_user', $countryInfo['Country']['timezone']);
            $this->Session->write('Config.devise', $countryInfo['Country']['devise']);
            $this->Session->write('Config.devise_iso', $countryInfo['Country']['devise_iso']);

        return $id_country;
    }
    protected function destroySessionAndCookie()
    {
       // $this->Cookie->time = 0;
        $this->Session->destroy();
    }
    function beforeFilter() {

        parent::beforeFilter();

        if (isset($this->request->query['nosubscribe'])){
            $this->nosubscribe = true;

            $this->Auth->loginAction = array(
                'controller' => 'users',
                'action'     => 'login',
                'admin'      => false,
                'membre'     => false,
                '?' => array('nosubscribe' => 1)
            );

        }
        $this->set('nosubscribe', $this->nosubscribe);

       // $this->Cookie->type('rijndael');

        if (isset($this->params->query['destroy'])){
          // debug($this->Cookie->read());
           //debug($this->Session->read());

           $this->destroySessionAndCookie();

          // debug($this->Cookie->read());
           //debug($this->Session->read());
           //die("Cookies nettoyés");
        }

        /* Localisation du site demandé */
            $this->_getCountryByDomain();

        /* Gestion du switch langues */
            $this->_setLanguage();



        /* On cherche un cookie pour logguer l'utilisateur porteur */
            $this->_logByCookie();

        $user = $this->Session->read('Auth.User');

		/* surcharge timezone pour agent */
            $this->_getTimezone();

		/* set source */
		if (empty($user))
			$this->SaveSource();

        /* On charge le credit depuis la bdd pour mettre à jour la session */

		if($user){
		    $this->loadModel('User');
            $row = $this->User->find("first", array(
                'conditions' => array('id' => (int)$user['id']),
                'fields'     => 'credit',
                'recursive'  => -1
            ));
            if (isset($row['User']['credit'])){
                $this->Session->write('Auth.User.credit', $row['User']['credit']);
            }
		}
        /* On prépare les paramètres globaux de mail */
           $this->initEmailParameters();

       if(isset($user['role']) && !empty($user) && $user['role'] == 'client' && $user['valid'] == 0 && !$this->userValid($this->params)){
            if($this->request->is('ajax'))
                $this->jsonRender(array('return' => 'usernovalid', 'msg' => __('Veuillez activer votre compte')));

            $this->Session->setFlash(__('Complétez votre profil.'),'flash_info');
            $this->redirect(array('controller' => 'accounts', 'action' => 'profil'));
        }

        //if (isset($user['role']))
            if((empty($user) || $user['role'] !== 'admin') && isset($this->params['admin']) && $this->params['admin'])
            $this->redirect(array('controller' => 'admins', 'action' => 'login', 'admin' => false),false);

        $this->Auth->allow('index','display','subscribe', 'displayUnivers','subscribe_merci','subscribe_agent_merci','reviews_post', 'widgetlisting', 'widgetbottomlisting', 'promolive', 'redir', 'reviewutile', 'validcgv', 'cart_buy', 'track', 'login','unsubscribe');

        /* Jours pour traduction */

    }
    private function allowedLang($language_code=false, &$config=false)
    {
        if (!$language_code)return false;
        $config = $this->getWithoutContextConfigDomain();
        $lang_codes_allowed = array();
        foreach ($config['Lang'] AS $lang){
            $lang_codes_allowed[] = $lang['language_code'];
        }
        return in_array($language_code, $lang_codes_allowed);


        $config = $this->Session->read('Config');
        if (!isset($config['id_lang']) || !isset($config['domain_langs']))return false;
        return (in_array($config['id_lang'], array_keys($config['domain_langs'])));
    }
    protected function getWithoutContextConfigDomain()
    {
        /* On récupère le domaine */
        $pieces = parse_url(Router::url('/', true));
        $host = $pieces['host'];


        /* Et sa configuration en db */
        $this->loadModel('Domain');
        $this->loadModel('Country');
        return $this->Domain->findByDomain($host);
    }
    protected function return404($die=true)
    {

        $this->response->statusCode(404);
        throw new NotFoundException();
        $this->layout = 'default';
        $this->response->send();
        if ($die)die();
    }
    public function initEmailParameters()
    {
        if ($this->init_mail_parameters_done)return false;

		$routs = Router::url('/', true);
		if(substr_count($routs,'localhost'))
			$routs = 'https://fr.spiriteo.com/';

        $vars = array(
            'SITE_NAME'     => Configure::read('Site.name'),
            'PARAM_URLSITE' => $routs
        );
        Configure::write('Email.template.with_footer', true);
        Configure::write('Email.template.vars', $vars);
    }

    private function userValid($params){
        if (in_array($params['controller'], array('home','category','alert','reviews', 'agents')))
            return true;
        if($params['controller'] == 'accounts' && in_array($params['action'], array('profil','editAccountInfos', 'editAccountCompte', 'editAccountDetails')))
            return true;
        if($params['controller'] == 'products' && in_array($params['action'], array('tarif')))
            return true;

        return false;
    }

    private function _logByCookie()
    {
        if (!$this->Auth->loggedIn() && $this->Cookie->check('user_remember')) {
            $this->Cookie->httpOnly = true;

             $user = $this->Cookie->read('user_remember');
             if ($user && !$this->Auth->login($user)) {
                    $this->redirect('/users/logout'); // destroy session & cookie
             }else{
				 $this->loadModel('User');
				 $datas = $this->User->find('first', array(
					 	//'fields' => array('role'),
						'conditions' => array('email' => $user['email'],'passwd' => $user['passwd']),
						'recursive' => -1
					));
				 $this->Session->write('Auth.User', $datas['User']);
			 }
         }
    }
    private function _getIdLangByIso($iso=false)
    {
        if (!$iso)return false;

        $this->loadModel('Lang');
        $row = $this->Lang->findByLanguageCode($iso);

        $locale = $row['Lang']['lc_time'];

        $this->Session->write('Config.lc_time', $locale);

        return isset($row['Lang']['id_lang'])?(int)$row['Lang']['id_lang']:false;
    }
    function _setLanguage() {
        if ($this->Cookie->read('lang') && !$this->Session->check('Config.language')) {
            $this->Session->write('Config.language', $this->Cookie->read('lang'));
            $this->Session->write('Config.id_lang', $this->_getIdLangByIso($this->Cookie->read('lang')));
        }
        else if (isset($this->params['language']) && ($this->params['language'] !=  $this->Session->read('Config.language'))) {
            if ($this->allowedLang($this->params['language'], $config)){
                $this->Session->write('Config.language', $this->params['language']);
                $this->Session->write('Config.id_lang', $this->_getIdLangByIso($this->params['language']));
                $this->Cookie->write('lang', $this->params['language'], false);
            }else{
                $this->layout = false;
                throw new NotFoundException();
            }
        }elseif (!isset($this->params['language']) && !$this->Session->check('Config.language') && !$this->Cookie->check('lang')){
            /* On cherche la langue par défaut */
            if ($this->Session->check('Config.default_iso_lang')){
                $this->Session->write('Config.language', $this->Session->read('Config.default_iso_lang'));
                $this->Session->write('Config.id_lang', $this->_getIdLangByIso($this->Session->read('Config.default_iso_lang')));
                $this->Cookie->write('lang', $this->Session->read('Config.default_iso_lang'), false);
            }

        }

        /* Localisation dates / heures */
        $locale = $this->Session->check('Config.lc_time')?$this->Session->read('Config.lc_time'):'';
        if (!empty($locale)){
			if($locale == 'fr_CA.utf8') $locale = 'fr_FR.utf8';
            setlocale(LC_ALL, $locale);
        }


        if (!$this->Session->read('Config.id_lang')){
            $this->Session->destroy();
            $this->Cookie->destroy();
            $this->redirect(array(
                'controller' => 'home',
                'action' => 'index'
            ));
        }


        if (!$this->Session->check('Config.domain_langs')){
            $this->loadModel('DomainLang');
            $rows = $this->DomainLang->query('SELECT
                 l.name, l.id_lang
                                      FROM domain_langs AS dl
                                      INNER JOIN langs AS l ON l.id_lang = dl.lang_id
                                      INNER JOIN domains AS d ON d.id = dl.domain_id
                                      WHERE d.country_id = '.(int)$this->Session->read('Config.id_country').'
                                      AND   dl.domain_id = '.(int)$this->Session->read('Config.id_domain').'
                                      AND   l.active = 1');

            $langs = array();
            if (!empty($rows))
                foreach ($rows AS $row)
                    $langs[$row['l']['id_lang']] = $row['l']['name'];
            $this->Session->write('Config.domain_langs', $langs);
        }
        $tmp = $this->Session->read('Config.lc_time');
        $tmp = explode(".", $tmp);
        $tmp = explode("_", $tmp['0']);
        $tmp = $tmp['0'];
        $this->set('html_doc_lang', $tmp);
    }

    //override redirect
    public function redirect( $url, $flag = true, $status = 301, $exit = true ) {
        if ($flag === true && is_array($url) && !isset($url['language']) && $this->Session->check('Config.language')) {
            $url['language'] = $this->Session->read('Config.language');
        }
        parent::redirect($url,$status,$exit);
    }

    public function isAuthorized($user){
        foreach ($this->nx_roles AS $role){
            if (empty($this->request->params[$role]) && $this->request->params['controller'] !== $role)
                return true;
            else return (bool)($user['role'] === $role);
        }
        return false;
    }

    public function isNoox()
    {
		if(isset($this->request))
        return ($this->request->clientIp() === '109.190.94.104'
        //|| $this->request->clientIp() === '109.190.3.116' /* Olivier */
        //|| $this->request->clientIp() === '127.0.0.1' /* Localhost */
	    //|| $this->request->clientIp() === '217.128.245.224'
	    //|| $this->request->clientIp() === '85.68.140.15'
	    //|| $this->request->clientIp() === '89.94.157.113'
	    //|| $this->request->clientIp() === '90.11.188.113'
        );
		else
			return '';
    }

    /**
     * Send a email.
     *
     * @param string $config A string of configuration email.
     * @param string $to A string of destination address.
     * @param string $subject A string of email subject.
     * @param string $template A string of email template.
     * @param array $viewVars A array with variables to be used in the view.
     *
     * @return bool
     */
    public function sendEmail($to, $subject = 'Sans objet', $template = 'default', $viewVars = array(), $config = 'default', $sender = 'no-reply@talkappdev.com', $reply = 'no-reply@talkappdev.com') {
        if (empty($to))return false;
        //On rajoute l'url de la page d'accueil au viewVars
        $viewVars = array_merge($viewVars, array('urlSite' => Router::url(array('controller' => 'home', 'action' => 'index', 'admin' => false),true)));

        $Email = new CakeEmail();
        $Email->config($config);
		$Email->from(array($sender => 'Spiriteo'));
		$Email->replyTo($reply);
		$Email->sender(array($sender => 'Spiriteo'));
        $Email->to($to);
        $Email->subject(__($subject));
        $Email->template($template);
        $Email->viewVars($viewVars);
        $Email->emailFormat('both');
		return $Email->send();

    }

	public function sendEmailWithAttachment($to, $subject = 'Sans objet', $template = 'default', $viewVars = array(), $config = 'default', $sender = 'no-reply@talkappdev.com', $reply = 'contact@talkappdev.com', $attachment = array()) {
        if (empty($to))return false;
        //On rajoute l'url de la page d'accueil au viewVars
        $viewVars = array_merge($viewVars, array('urlSite' => Router::url(array('controller' => 'home', 'action' => 'index', 'admin' => false),true)));

        $Email = new CakeEmail();
        $Email->config($config);
		$Email->from(array($sender => 'Spiriteo'));
		$Email->replyTo($reply);
		$Email->sender(array($sender => 'Spiriteo'));
        $Email->to($to);
        $Email->subject(__($subject));
        $Email->template($template);
        $Email->viewVars($viewVars);
        $Email->emailFormat('both');
		if(count($attachment))
			$Email->attachments($attachment);
		return $Email->send();

    }

    /**
     * Vérifie si le chargement du fichier a réussi.
     *
     * @param array $params Les paramètre du fichier reçu depuis l'input file
     *
     * @return bool
     */
    public function isUploadedFile($params) {
        //Si aucun paramètre
        if(empty($params))
            return false;
        if ((isset($params['error']) && $params['error'] == 0) ||
            (!empty( $params['tmp_name']) && $params['tmp_name'] != 'none')
        ) {
            return is_uploaded_file($params['tmp_name']);
        }
        return false;
    }

    /**
     * Permet de sauvegarder les photos de l'agent. Classique et listing
     *
     * @param array $data Les données de l'agent et de la photo source
     * @param string $namePath Le nom de la variable Configure (Pour récupérer le chemin du dossier
     * @param bool $inscription En mode inscription
     * @param int $idUser L'id de l'user
     *
     * @return bool false en cas d'erreur sinon true
     */
    public function savePhoto($data, $namePath, $inscription=false, $idUser=0){
        if($inscription){
            //On récupère le chemin du dossier où stocker les imagees (Cela crée également le dossier si besoin)
            $folder = $this->nameFolderMedia($idUser,Configure::read($namePath),true);
        }else{
            //On récupère le chemin du dossier où stocker les imagees (Cela crée également le dossier si besoin)
            $folder = $this->nameFolderMedia($data['agent_number'],Configure::read($namePath));
        }
        //S'il y a une erreur dans la création|récupération du dossier

        if(empty($folder))
            return false;
        //On récupère le type de l'image
        $type = explode('/',$data['photo']['type']);
        $type = $type[1];

        //Image classique
        $flag = Tools::imageCropAndResized($data['photo']['tmp_name'],$folder.DS.($inscription ?$idUser:$data['agent_number']).'.jpg',$type,$data['crop']['x'],
            $data['crop']['y'],$data['crop']['h'],$data['crop']['w'],Configure::read('Site.photoDim.h'),Configure::read('Site.photoDim.w'));
//var_dump($flag);exit;
        if(!$flag)
            return false;

        //Image listing
        $flag = Tools::imageCropAndResized($data['photo']['tmp_name'],$folder.DS.($inscription ?$idUser:$data['agent_number']).'_listing.jpg',$type,$data['crop']['x'],
            $data['crop']['y'],$data['crop']['h'],$data['crop']['w'],Configure::read('Site.photoListing.h'),Configure::read('Site.photoListing.w'));

        if(!$flag)
            return false;

        return true;
    }

    /**
     * Permet de sauvegarder les présentations audio de l'agent.
     *
     * @param array $data Les données de l'agent et de la présentation audio source
     * @param string $namePath Le nom de la variable Configure (Pour récupérer le chemin du dossier
     * @param bool $inscription En mode inscription ou pas
     * @param int $idUser L'id de l'user
     *
     * @return bool false en cas d'erreur sinon true
     */
    public function saveAudio($data, $namePath, $inscription=false, $idUser=0){
        if($inscription){
            //On récupère le chemin du dossier où stocker les présentations audio (Cela crée également le dossier si besoin)
            $folder = $this->nameFolderMedia($idUser,Configure::read($namePath),true);
        }else{
            //On récupère le chemin du dossier où stocker les présentations audio (Cela crée également le dossier si besoin)
            $folder = $this->nameFolderMedia($data['agent_number'],Configure::read($namePath));
        }
        //S'il y a une erreur dans la création|récupération du dossier
        if(empty($folder))
            return false;
        //On crée le fichier audio
        $file = new File($data['audio']['tmp_name']);
        //On le copie dans le dossier
        $file->copy($folder.DS.($inscription ?$idUser:$data['agent_number']).'.mp3');
        return true;
    }

    /**
     * Permet de sauvegarder les fichiers du voyant.
     *
     * @param array $requestData Tableau avec le fichier à enregistrer
     * @param string $format Le nom du format (audio || photo)
     * @param bool $validation Booleen pour savoir si la sauvegarde se fait en attente ou pas
     * @param bool $inscription Booleen pour savoir si la sauvegarde se fait en inscription ou pas
     * @param int $idUser L'id de l'user
     * @return bool
     */
    public function saveFile($requestData, $format, $validation = false, $inscription = false, $idUser=0){

        switch ($format){
            case 'audio' :
                if($inscription){
                    $path = 'Site.pathInscriptionMediaUpload';
                    return $this->saveAudio($requestData, $path, true, $idUser);
                }
                $path = ($validation ?'Site.pathPresentationValidation':'Site.pathPresentation');
                return $this->saveAudio($requestData, $path);
                break;
            case 'video' :
                if($inscription){
                    $path = 'Site.pathInscriptionMediaUpload';
                    return $this->saveVideo($requestData, $path, true, $idUser);
                }
                $path = ($validation ?'Site.pathPresentationVideoValidation':'Site.pathPresentationVideo');
                return $this->saveVideo($requestData, $path);
                break;
            case 'photo' :
                if($inscription){
                    $path = 'Site.pathInscriptionMediaUpload';
                    return $this->savePhoto($requestData, $path, true, $idUser);
                }
                $path = ($validation ?'Site.pathPhotoValidation':'Site.pathPhoto');
				//var_dump($path);
				//var_dump($requestData);exit;
                return $this->savePhoto($requestData, $path);
                break;
        }
        return false;
    }

    /**
     * Permet de sauvegarder les présentations video de l'agent.
     *
     * @param array $data Les données de l'agent et de la présentation video source
     * @param string $namePath Le nom de la variable Configure (Pour récupérer le chemin du dossier
     * @param bool $inscription En mode inscription ou pas
     * @param int $idUser L'id de l'user
     *
     * @return bool false en cas d'erreur sinon true
     */
    public function saveVideo($data, $namePath, $inscription=false, $idUser=0){
        if($inscription){
            //On récupère le chemin du dossier où stocker les présentations video (Cela crée également le dossier si besoin)
            $folder = $this->nameFolderMedia($idUser,Configure::read($namePath),true);
        }else{
            //On récupère le chemin du dossier où stocker les présentations video (Cela crée également le dossier si besoin)
            $folder = $this->nameFolderMedia($data['agent_number'],Configure::read($namePath));
        }
        //S'il y a une erreur dans la création|récupération du dossier
        if(empty($folder))
            return false;
        //On crée le fichier video
        $file = new File($data['video']['tmp_name']);
        //On le copie dans le dossier
        $file->copy($folder.DS.($inscription ?$idUser:$data['agent_number']).'.mp4');
        return true;
    }

    public function ajaxgetcredit()
    {
        $this->layout = false;
		App::uses('FrontblockHelper', 'View/Helper');
		$fbH = new FrontblockHelper(new View());
        $this->loadModel('User');
        /* On récupère l'id client */
        $user = $this->Session->read('Auth.User');
        if (empty($user))return false;

        $credit = $this->User->field('credit', array('id' => $user['id']));
		$credit_txt = $fbH->secondsToHis(($credit * Configure::read('Site.secondePourUnCredit')), true);
        $this->set(compact('credit','credit_txt'));
    }
    public function ajaxactivity()
    {
        $this->layout = false;

		if(session_id()){
			$user = $this->Session->read('Auth.User');
			if(!empty($user) && $user['role'] == 'agent'){

				$this->loadModel('UserConnexion');
				if(!$user['id'])$user['id'] = $this->Auth->user('id');
				$connexion = $this->UserConnexion->find('first', array(
						'conditions' => array('user_id' => $user['id'], 'session_id' =>  session_id()),
					    'order' => 'id DESC',
						'recursive' => -1
					));
				if($connexion['UserConnexion']['id']){
					$this->UserConnexion->id = $connexion['UserConnexion']['id'];
					$this->UserConnexion->saveField('date_lastactivity', date('Y-m-d H:i:s'));
				}else{
					$connex = array(
								'user_id'          	=> $this->Auth->user('id'),
								'session_id'        => session_id(),
								'date_connexion'    => date('Y-m-d H:i:s'),
								'date_lastactivity' => date('Y-m-d H:i:s'),
								'status'       		 => '',
								'who'       		 => $this->User->id,
								'mail'            	=> $user['consult_email'],
								'tchat'      		=> $user['consult_chat'],
								'phone'    			=> $user['consult_phone']
							);
					$this->UserConnexion->create();
					$this->UserConnexion->save($connex);
				}

				//MAJ de la date d'activité
				//if(!$this->request->isMobile()){
                	$this->User->id = $this->Auth->user('id');
                	$this->User->saveField('date_last_activity', date('Y-m-d H:i:s'));
				//}
			}
			if(!empty($user) && $user['role'] == 'client'){
				$this->User->id = $this->Auth->user('id');
                $this->User->saveField('date_last_activity', date('Y-m-d H:i:s'));
			}
		}

        $this->loadModel('User');
        $busy_agents = $this->User->find('count', array(
            'conditions' => array(
                'role' => 'agent',
                'deleted' => 0,
                'agent_status' => 'busy',
                'active' => 1
            ),
            'recursive' => -1
        ));
        $this->set(compact('busy_agents'));
    }


    /**
     * @param int   $id     L'id du client
     * @param int   $credit Le crédit qu'il faut débiter ou créditer
     * @param bool  $debit  Si on débite ou crédite
     * @return mixed
     */
    public function updateCredit($id, $credit, $debit=true, $zeroIfNegatif=false){
        $this->loadModel('User');
        //Credit actuel du customer
        $this->User->id = $id;
        $current_credit = (int)$this->User->field('credit');
        //Aucune valeur retournée pour cet id
        if($current_credit === false)
            return false;

        //Si c'est un debit de credit
        if($debit){
            $current_credit -= (int)$credit;
        }else   //Sinon c'est un credit de crédit
            $current_credit += (int)$credit;
        //Cas qui ne devrait pas arriver.
        if($current_credit < 0)
            if ($zeroIfNegatif)
                $current_credit = 0;
            else
                return false;

        //On save le nouveau crédit
        $this->User->saveField('credit', $current_credit);
        //Et on retourne la valeur du nouveau crédit
        return $current_credit;
    }

    /**
     * Met à jour les badges pour le menu Admin
     */
    private function updateBadge(){
        $datas = array();

		$this->loadModel('SupportAdmin');
		$user_co = $this->Session->read('Auth.User');
		$service_list = $this->SupportAdmin->find('all',array(
								'conditions' => array('user_id' => $user_co['id']),
									'recursive' => -1,
								));
		$services_list = array(0);

		foreach($service_list as $serv){
			array_push($services_list,$serv['SupportAdmin']['service_id']);
		}


		//Liste des badges. Menu => array(nom du badge => conditions)
        $list = array(
            'Client' => array(
                'count' => array('User.deleted' => 0, 'User.role' => 'client'),
                'email' => array('User.deleted' => 0, 'User.role' => 'client', 'User.emailConfirm' => 0),
                'compte' => array('User.deleted' => 0, 'User.role' => 'client', 'User.firstname' => null,
                                  'OR' => array(
                                      array('User.active' => 0),
                                      array('User.valid' => 0)
                                  )
                )
            ),
            'Agent' => array(
                'count' => array('User.deleted' => 0, 'User.role' => 'agent'),
                'email' => array('User.deleted' => 0, 'User.role' => 'agent', 'User.emailConfirm' => 0),
                'compte' => array('User.deleted' => 0, 'User.role' => 'agent', 'User.date_lastconnexion' => null,
                    'OR' => array(
                        array('User.active' => 0),
                        array('User.valid' => 0)
                    )
                )
            ),
            'ValidAgent' => array(
                'info' => array('UserValidation.etat' => 0),
                'presentation' => array('UserPresentValidation.etat' => 0),
                'photo' => Configure::read('Site.pathPhotoValidation').'/[0-9]/[0-9]/*_listing.jpg',
                'audio' => Configure::read('Site.pathPresentationValidation').'/[0-9]/[0-9]/*.mp3',
                'video' => Configure::read('Site.pathPresentationVideoValidation').'/[0-9]/[0-9]/*.mp4',
				'mailinfos' => array('User.mail_infos !=' => ''),
            ),
            'Record' => array(
                'count' => array('Record.archive' => 0),
				'count_archive' => array('Record.archive' => 1,'Record.deleted' => 0)
            ),
			'Survey' => array(
                'is_respons' => array('Survey.is_respons' => 1, 'Survey.is_valid' => 0, 'Survey.status' => 0),
            ),
            'Category' => array(
                'count' => array()
            ),
            'Page' => array(
                'count' => array()
            ),
            'Review' => array(
                'count' => array('Review.status' => -1,'Review.rate' => 5,'Review.parent_id' => NULL),
				'count_bad' => array('Review.status' => -1,'Review.rate <' => 5,'Review.parent_id' => NULL),
				'count_bad2' => array('Review.status' => -2,'Review.parent_id' => NULL),
				'count_resp' => array('Review.status' => -1,'Review.parent_id >' => 0),
                'online' => array('Review.status' => 1),
                'refuse' => array('Review.status' => 0)
            ),
            'PageCategory' => array(
                'count' => array()
            ),
            'Phone' => array(
                'count' => array()
            ),
			'Chat' => array(
				'etat' => array('Chat.etat' => 0)
            ),
            'Message' => array(
                //'count' => array('Message.admin_read_flag' => 0, 'Message.to_id' => array($this->Auth->user('id'), Configure::read('Admin.id')))
                'count' => array('Message.admin_read_flag' => 0, 'Message.to_id' => Configure::read('Admin.id'),'Message.etat !=' => 2, 'Message.deleted' => 0),
				'etat_prive' => array('Message.etat' => 2, 'Message.private' => 1, 'Message.deleted' => 0),
				'etat_mail' => array('Message.etat' => 2, 'Message.private' => 0, 'Message.deleted' => 0),
			'etat_relance' => array('Message.private' => 2, 'Message.etat' => 2, 'Message.deleted' => 0),
            ),
			'CrmStat' => array(
                //'count' => array('Message.admin_read_flag' => 0, 'Message.to_id' => array($this->Auth->user('id'), Configure::read('Admin.id')))
                'count' => array('CrmStat.date >' => date('Y-m-d').' 00:00:00', 'CrmStat.view' => 1),
            ),
			'CartLoose' => array(
                //'count' => array('Message.admin_read_flag' => 0, 'Message.to_id' => array($this->Auth->user('id'), Configure::read('Admin.id')))
                'count' => array('CartLoose.date_cart > ' => date('Y-m-d').' 00:00:00', 'CartLoose.status <' => 1),
            ),
			'AgentView' => array(
                //'count' => array('Message.admin_read_flag' => 0, 'Message.to_id' => array($this->Auth->user('id'), Configure::read('Admin.id')))
                'count' => array('AgentView.date_view > ' => date('Y-m-d').' 00:00:00'),
            ),
			'CustomerAppointment' => array(
				'status' => array('CustomerAppointment.status' => 0)
            ),
			'Support' => array(
                'count' => array('Support.status' => 0,'Support.service_id IN' => $services_list),
            ),
			'AgentMessage' => array(
                'count' => array('AgentMessage.status !=' => 'Vu'),
            ),
        );

        //On charge les models
        $this->loadModel('User');
        $this->loadModel('Category');
        $this->loadModel('Page');
        $this->loadModel('Review');
        $this->loadModel('UserValidation');
        $this->loadModel('UserPresentValidation');
        $this->loadModel('PageCategory');
        $this->loadModel('CountryLangPhone');
        $this->loadModel('Message');
		$this->loadModel('Chat');
		$this->loadModel('CrmStat');
		$this->loadModel('CartLoose');
		$this->loadModel('AgentView');
		$this->loadModel('CustomerAppointment');
		$this->loadModel('Survey');
		$this->loadModel('Record');
		$this->loadModel('Support');
		$this->loadModel('AgentMessage');

        //Pour chaque menu
        foreach($list as $menu => $params){
            //Pour chaque badge
            foreach($params as $badge => $conditions){
                //Selon le menu
                switch ($menu){
                    case 'Client' :
                    case 'Agent' :
                        $datas[$menu][$badge] = $this->User->find('count',array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
					 case 'Survey' :
                        $datas[$menu][$badge] = $this->Survey->find('count',array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
					case 'Support' :
                        $datas[$menu][$badge] = $this->Support->find('count',array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
                    case 'Category' :
                        $datas[$menu][$badge] = $this->Category->find('count',array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
                    case 'Page' :
                        $datas[$menu][$badge] = $this->Page->find('count',array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
                    case 'Review' :
                        $datas[$menu][$badge] = $this->Review->find('count',array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
                    case 'ValidAgent' :
                        switch($badge){
                            case 'info' :
                                $datas[$menu][$badge] = $this->UserValidation->find('count',array(
                                    'conditions' => $conditions,
                                    'recursive' => -1
                                ));
                                break;
                            case 'presentation' :
                                $datas[$menu][$badge] = $this->UserPresentValidation->find('count',array(
                                    'conditions' => $conditions,
                                    'recursive' => -1
                                ));
                                break;
							case 'mailinfos' :
                                $datas[$menu][$badge] = $this->User->find('count',array(
                                    'conditions' => $conditions,
                                    'recursive' => -1
                                ));
                                break;
                            case 'photo' :
                            case 'audio' :
                            case 'video' :
                                $datas[$menu][$badge] = count(glob($conditions));
                                break;
                        }
                        break;
                    case 'Record' :
                        $datas[$menu][$badge] = $this->Record->find('count',array(
                                    'conditions' => $conditions,
                                    'recursive' => -1
                                ));
                        break;
                    case 'PageCategory' :
                        $datas[$menu][$badge] = $this->PageCategory->find('count',array(
                            'conditions' => $conditions,
                            'recursive'  => -1
                        ));
                        break;
                    case 'Phone' :
                        $datas[$menu][$badge] = $this->CountryLangPhone->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
					case 'Chat' :
                        $datas[$menu][$badge] = $this->Chat->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
                    case 'Message' :
                        /*
                        select
count(*)
from messages
where to_id = 1 and admin_read_flag = 0
group by parent_id
*/
/*
                        $datas[$menu][$badge] = $this->Message->find('count', array(
                            'conditions' => array(
                                'to_id' => Configure::read('Admin.id'),
                                'admin_read_flag' => 0,
                                'isnull(parent_id)',
                                'archive' => 0
                            ),

                            'recursive' => -1
                        ));*/

						if($badge == 'count' && !empty(Configure::read('Admin.id'))){
                            
							$row = $this->Message->query('
                            select count(*) AS cnt from (
                                select * from (
                                    SELECT
                                        if(isnull(parent_id), id, parent_id) as thread_id, 
                                        messages.*
                                        from messages
                                        where to_id = '.(int)Configure::read('Admin.id').' and etat != 2 and archive = 0
                                        order by date_add desc
                                ) as rows1
                                group by "thread_id"
							) as rows2
							where rows2.admin_read_flag = 0 and rows2.etat != 2 and rows2.archive = 0
							');

							$datas[$menu][$badge]  = isset($row['0']['0']['cnt'])?(int)$row['0']['0']['cnt']:0;
                            
						}else{
								$datas[$menu][$badge] = $this->Message->find('count',array(
								'conditions' => $conditions,
								'recursive'  => -1
							));
						}
                        break;
					case 'CrmStat' :
                        $datas[$menu][$badge] = $this->CrmStat->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
						case 'AgentView' :
                        $datas[$menu][$badge] = $this->AgentView->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
						case 'CartLoose' :
                        $datas[$menu][$badge] = $this->CartLoose->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
						case 'CustomerAppointment':
                        $datas[$menu][$badge] = $this->CustomerAppointment->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
					case 'AgentMessage':
                        $datas[$menu][$badge] = $this->AgentMessage->find('count', array(
                            'conditions' => $conditions,
                            'recursive' => -1
                        ));
                        break;
                }
            }
        }


        $this->loadModel('Order');
		$datas['Order']['coupon']['notactive'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'coupon','Order.valid'   => 0
            )
        ));

		$datas['Order']['coupon']['notactive'] += $this->Order->find('count', array(
            'conditions' => array(
                			'Order.payment_mode' => 'coupon','Order.valid'   => 1,'Order.product_id'   => NULL,'Order.total <'   => 0
            				),
			 'joins' => array(
                array('table' => 'gift_orders',
                      'alias' => 'Gift',
                      'type' => 'inner',
                      'conditions' => array(
                          'Gift.code = Order.voucher_code',
                          'Gift.beneficiary_id is NULL',
						  'Gift.valid = 1'
                      )
                ),
            ),
            'recursive' => -1,

        ));

        $datas['Order']['bankwire']['active'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'bankwire',
                'Order.valid'        => 1
            )
        ));
        $datas['Order']['bankwire']['notactive'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'bankwire',
                'Order.valid'        => 0
            )
        ));
		$datas['Order']['sepa']['active'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'sepa',
                'Order.valid'        => 1
            )
        ));
        $datas['Order']['sepa']['notactive'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'sepa',
                'Order.valid'        => 0
            )
        ));
		$datas['Order']['paymentstripe']['active'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'stripe',
                'Order.valid'        => 1
            )
        ));
		$datas['Order']['paymentstripe']['oppose'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'stripe',
                'Order.valid'        => 2
            )
        ));
        /*
        $datas['Order']['paymenthipay']['active'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'hipay',
                'Order.valid'        => 1
            )
        ));
        $datas['Order']['paymenthipay']['notactive'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'hipay',
                'Order.valid'        => 0
            )
        ));
		$datas['Order']['paymenthipay']['oppose'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'hipay',
                'Order.valid'        => 2
            )
        ));
        */
        $datas['Order']['paymentpaypal']['active'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'paypal',
                'Order.valid'        => 1
            )
        ));
        $datas['Order']['paymentpaypal']['notactive'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'paypal',
                'Order.valid'        => 0
            )
        ));
        $datas['Order']['paymentpaypal']['pending'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'paypal',
                'Order.valid'        => 2
            )
        ));
		$datas['Order']['paymentpaypal']['oppose'] = $this->Order->find('count', array(
            'conditions' => array(
                'Order.payment_mode' => 'paypal',
                'Order.valid'        => 3
            )
        ));


        return $datas;
    }

    /**
     * Generating link.
     *
     * @param string $controller A string of controller name.
     * @param string $action A string of action name.
     * @param array $param A array of link parameters.
     * @return string
     */
    public function linkGenerator($controller, $action, $param = array()) {
        return Router::url(array(
                'controller' => $controller,
                'action' => $action,
                '?' => $param,
                'admin' => false
            ),
            true
        );
    }

    /**
     * Hash le mot de passe.
     *
     * @param string $pass Le mot de passe à encrypter
     * @return string
     */
    public function hashMDP($pass){
        if (empty($pass))
            return '';

        $passwordHasher = new SimplePasswordHasher();
        return $passwordHasher->hash($pass);
    }

    /**
     * Permet de générer le chemin du dossier pour stocker le media de l'agent.
     *
     * @param int $agent_number Le code de l'agent
     * @param string $path Le chemin de base du dossier
     * @param bool $inscription En mode inscription
     *
     * @return string Le chemin du dossier
     */
    private function nameFolderMedia($agent_number,$path, $inscription=false){
        if(empty($agent_number) || empty($path))
            return '';

        if($inscription)
            $dirpath = $path.DS.$agent_number;
        else
            $dirpath = $path.DS.$agent_number[0].DS.$agent_number[1];

        $folder = new Folder($dirpath,true,0755);
        if (!is_dir($folder->path)) {
            if (!Folder::isAbsolute($dirpath)) {
                $dirpath = ROOT . '/app/webroot/' . $dirpath;
            }
            $folder = new Folder($dirpath,true,0755);
        }
        $dirpath = $folder->path;

        if (!is_dir($dirpath))
            return '';
        return $dirpath;
    }

    /**
     * Permet de vérifier que l'id fournie renvoie bien un enregistrement, et un champ avec une certaine valeur
     *
     * @param $model        string      Le nom du model
     * @param $id           int         L'id de l'enregistrement (ex: l'avis ou la présentation, l'agent)
     * @param $field        string      Le nom du champ à récupérer
     * @param $fieldValue   int|string  La valeur que le champ récupérer doit avoir
     * @param $msg          string      Le message en cas d'erreur
     * @param $url          string      Le chemin absolu de la page sur laquelle il faut rediriger
     * @param $cond         array       Les conditions pour la méthode "field"
     * @param $ajax         boolean     Si on est dans une requete ajax ou pas
     */
    protected function checkEntite($model, $id, $field, $fieldValue, $msg, $url, $cond = array(), $ajax = false){
        //On charge le model
        $this->loadModel($model);
        if(empty($cond)){
            $this->$model->id = $id;
            $checkField = $this->$model->field($field);
        }else
            $checkField = $this->$model->field($field, $cond);

        //Si aucune donnée pour cet id ou la valeur n'est pas celle de fieldValue
        if($checkField === false || $checkField != $fieldValue){
            $this->Session->setFlash(__($msg),'flash_warning');
            if($ajax)
                $this->jsonRender(array('url' => $url));
            else
                $this->redirect($url);
        }
    }

    /**
     * Permet de modifier une zone texte du type (présentation, avis)
     *
     * @param $model        string  Le nom du model de l'entite
     * @param $id           int     L'id de l'entite (ex: l'avis ou la présentation)
     * @param $url          string  L'url de l'action
     * @param $field        array   Les noms des champs
     * @param $form         array   Les datas pour le formulaire
     * @param $titleModal   string  Le titre de la modal
     * @param $message      array   Les différents messages
     */
    protected function editEntite($model, $id, $url, $field, $form, $titleModal, $message){
        //La vue pour l'edition
        $this->getViewModal('/Elements/admin_edit',$form,$titleModal,$model,$field,$id);

        //On modifie l'entite
        if($this->request->is('post') && !empty($this->request->data)){
            if(!$this->$model->updateAll(array($model.'.'.$field['name'] => $this->$model->value($this->request->data[$form['model']]['content'])),array($model.'.'.$field['primary'] => $id))){
                $id = false;
                $msg = __($message['error']);
            }else
                $msg = __($message['success']);

            if (!isset($this->request->data['isAjax']) || (int)$this->request->data['isAjax'] != 1){
                $this->Session->setFlash(__($message['success']),'flash_success');
                $this->redirect($url);
            }else
                // Retour JSON
                $this->jsonRender(array('id' => $id, 'msg' => $msg, 'content' => $this->request->data[$form['model']]['content']));
        }
    }

	protected function editEntiteReview($model, $id, $url, $field, $form, $titleModal, $message){
        //La vue pour l'edition
        $this->getViewModalReview('/Elements/admin_edit',$form,$titleModal,$model,$field,$id);
        //On modifie l'entite
        if($this->request->is('post') && !empty($this->request->data)){
            if(!$this->$model->updateAll(array($model.'.'.$field['name'] => $this->$model->value($this->request->data[$form['model']]['content']),$model.'.'.$field['rate'] => $this->$model->value($this->request->data[$form['model']]['rate']),$model.'.'.$field['pourcent'] => $this->$model->value($this->request->data[$form['model']]['pourcent']),$model.'.'.$field['date_add'] => $this->$model->value($this->request->data[$form['model']]['date_add'])),array($model.'.'.$field['primary'] => $id))){
                $id = false;
                $msg = __($message['error']);
            }else{
				$this->loadModel('User');
				$this->loadModel('Review');
				$MatchPourcent = array(
			100	=> 5,
99	=> 4.95,
98	=> 4.90,
97	=> 4.85,
96	=> 4.80,
95	=> 4.75,
94	=> 4.70,
93	=> 4.65,
92	=> 4.60,
91	=> 4.55,
90	=> 4.50,
89	=> 4.45,
88	=> 4.40,
87	=> 4.35,
86	=> 4.30,
85	=> 4.25,
84	=> 4.20,
83	=> 4.15,
82	=> 4.10,
81	=> 4.05,
80	=> 4,
79	=> 3.95,
78	=> 3.90,
77	=> 3.85,
76	=> 3.80,
75	=>3.75,
74	=> 3.70,
73	=> 3.65,
72	=> 3.60,
71	=> 3.55,
70	=> 3.50,
69	=> 3.45,
68	=> 3.40,
67	=> 3.35,
66	=> 3.30,
65	=> 3.25,
64	=> 3.20,
63	=> 3.15,
62	=> 3.10,
61	=> 3.05,
60	=> 3.00,
59	=> 2.95,
58	=> 2.90,
57	=> 2.85,
56	=> 2.80,
55	=> 2.75,
54	=> 2.70,
53	=> 2.65,
52	=> 2.60,
51	=> 2.55,
50	=> 2.5,
49	=> 2.45,
48	=> 2.40,
47	=> 2.35,
46	=> 2.3,
45	=> 2.25,
44	=> 2.2,
43	=> 2.15,
42	=> 2.1,
41	=> 2.05,
40	=> 2,
39	=> 1.95,
38	=> 1.90,
37	=> 1.85,
36	=> 1.80,
35	=> 1.75,
34	=> 1.70,
33	=> 1.65,
32	=> 1.60,
31	=> 1.55,
30	=> 1.50,
29	=> 1.45,
28	=> 1.40,
27	=> 1.35,
26	=> 1.30,
25	=> 1.25,
24	=> 1.20,
23	=> 1.15,
22	=> 1.10,
21	=> 1.05,
20	=> 1,
19	=> 0.95,
18	=> 0.90,
17	=> 0.85,
16	=> 0.80,
15	=> 0.75,
14	=> 0.70,
13	=> 0.65,
12	=> 0.60,
11	=> 0.55,
10	=> 0.50,
9	=> 0.45,
8	=> 0.40,
7	=> 0.35,
6	=> 0.30,
5	=> 0.25,
4	=> 0.20,
3	=> 0.15,
2	=> 0.10,
1	=> 0.05
		);

				$infoReview = $this->Review->find('first',array(
					'fields' => array('Agent.pseudo','Agent.agent_number','Agent.id','Agent.domain_id','Agent.lang_id', 'User.email','User.firstname', 'User.lang_id', 'Review.parent_id'),
					'conditions' => array('review_id' => $id)
				));

				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $infoReview['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}

				if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);

					$this->User->id = $infoReview['Agent']['id'];
					$this->User->saveField('reviews_avg', $avg);
					$this->User->saveField('reviews_nb', $nb);
				}

				$msg = __($message['success']);
			}


            if (!isset($this->request->data['isAjax']) || (int)$this->request->data['isAjax'] != 1){
                $this->Session->setFlash(__($message['success']),'flash_success');
                $this->redirect($url);
            }else
                // Retour JSON
                $this->jsonRender(array('id' => $id, 'msg' => $msg, 'content' => $this->request->data[$form['model']]['content'], 'rate' => $this->request->data[$form['model']]['rate'], 'pourcent' => $this->request->data[$form['model']]['pourcent'], 'date_add' => $this->request->data[$form['model']]['date_add'], 'send_mail' => $this->request->data[$form['model']]['send_mail']));
        }
    }

    /**
     * @param string        $element        L'element qu'il faut call
     * @param array         $form           Les données pour le formulaire
     * @param string        $titleModal     Le titre de la modal
     * @param bool          $model          Le nom du model
     * @param array         $field          Les noms des champs pour le model
     * @param int           $id             L'id du model
     */
    protected function getViewModal($element, $form, $titleModal, $model=false, $field=array(), $id=0){
        //On vient chercher la vue
        if(empty($this->request->data)){
            //Si c'est pour une édition
            if($model !== false){
                //On récupère le texte
                $texte = $this->$model->field($field['name'], array($field['primary'] => $id));
                //Les variables pour l'element
                $this->set(array(
                    'content' => $texte,
                    'title' => $form['title']
                ));
            }
            //Les variables pour l'element
            $this->set(array(
                'model' => $form['model'],
                'note' => $form['note']
            ));

            //Pour la modal en js
            if($this->request->is('ajax')){
                //Désactive le layout
                $this->layout = '';
                //Récupère le formulaire
                $content = $this->render($element);
                $this->set(array('title' => $titleModal, 'content' => $content));
                $this->render('/Elements/admin_modal');
            }else  //Pour la vue, sans JS
                $this->render($element);
        }
    }
	protected function getViewModalReview($element, $form, $titleModal, $model=false, $field=array(), $id=0){
        //On vient chercher la vue
        if(empty($this->request->data)){
            //Si c'est pour une édition
            if($model !== false){
                //On récupère le texte
                $texte = $this->$model->field($field['name'], array($field['primary'] => $id));
                //Les variables pour l'element
                $this->set(array(
                    'content' => $texte,
                    'title' => $form['title']
                ));
				$rate = $this->$model->field($field['rate'], array($field['primary'] => $id));
				$pourcent = $this->$model->field($field['pourcent'], array($field['primary'] => $id));
				$date_add = $this->$model->field($field['date_add'], array($field['primary'] => $id));
                //Les variables pour l'element
                $this->set(array(
                    'rate' => $rate,
					'pourcent' => $pourcent,
					'date_add' => $date_add,
                ));
            }
            //Les variables pour l'element
            $this->set(array(
                'model' => $form['model'],
                'note' => $form['note']
            ));

            //Pour la modal en js
            if($this->request->is('ajax')){
                //Désactive le layout
                $this->layout = '';
                //Récupère le formulaire
                $content = $this->render($element);
                $this->set(array('title' => $titleModal, 'content' => $content, 'rate' => $rate));
                $this->render('/Elements/admin_modal');
            }else  //Pour la vue, sans JS
                $this->render($element);
        }
    }

    /**
     * Permet de refuser une modification et d'envoyer un email pour avertir l'user
     *
     * @param $model        string  Le nom du model de l'entite
     * @param $id           int     L'id de l'entite
     * @param $url          string  L'url de l'action
     * @param $field        array   Les noms des champs
     * @param $form         array   Les datas pour le formulaire
     * @param $titleModal   string  Le titre de la modal
     * @param $message      array   Les différents messages
     * @param $email        array   Les paramètres du mail
     * @param $datasEmail   array   Les variables pour l'email
     * @param $saveAdmin    bool    Pour savoir, si on save l'id de l'admin
     */
    protected function refuseEntite($model, $id, $url, $field, $form, $titleModal, $message, $email, $datasEmail, $saveAdmin=true){
        //On récupère la vue
        $this->getViewModal('/Elements/admin_refuse',$form,$titleModal);

        //On refuse l'entite et on envoie l'email
        if($this->request->is('post') && !empty($this->request->data)){
            //On indique que l'entite est refusé
            if($this->$model->updateAll(array($model.'.'.$field['name'] => $field['value']),array($model.'.'.$field['primary'] => $id))){
                //On save l'id admin si on doit le faire
                if($saveAdmin)
                    $this->$model->updateAll(array($model.'.'.(isset($field['admin']) && !empty($field['admin'])?$field['admin']:'admin_id') => $this->Auth->user('id')),array($model.'.'.$field['primary'] => $id));
                //On récupère le mail de l'agent
                $emailUser = $this->getEmail($model,$id,$field['foreign'],$field['primary']);
                //Envoie de l'email
                //$this->sendEmail($emailUser,$email['subject'],$email['template'], array('data' => $datasEmail));

                /* On charge les données Agent */
                    $this->$model->id = $id;
                    $this->$model->recursive = -1;
                    $tmp = $this->$model->read();

                    $user_id = 0;
                    if ($model == 'UserValidation') $field = 'users_id'; else $field = 'user_id';
                    $user_id = (int)$tmp[$model][$field];

                    $this->loadModel('User');
                    $this->User->id = $user_id;
                    $this->User->recursive = -1;
                    $user = $this->User->read();


                $this->sendCmsTemplateByMail($datasEmail['cms_id'], $user['User']['lang_id'], $emailUser, array(
                    'REFUS_REASON' => !empty($datasEmail['motif'])?__('Pour le motif suivant :').'<br/>'.$datasEmail['motif']:'',
                    'ADMIN_EMAIL'  => $datasEmail['emailAdmin']
                ));
                $this->Session->setFlash(__($message['success']),'flash_success');
            }else
                $this->Session->setFlash(__($message['error']),'flash_warning');

            if (!isset($this->request->data['isAjax']) || (int)$this->request->data['isAjax'] != 1)
                $this->redirect($url);
            else
                // Retour JSON
                $this->jsonRender(array('url' => $url));
        }
    }
	protected function refuseEntiteWithoutSave($model, $id, $url, $field, $form, $titleModal, $message, $email, $datasEmail, $saveAdmin=true){
        //On récupère la vue
        $this->getViewModal('/Elements/admin_refuse',$form,$titleModal);


        if($this->request->is('post') && !empty($this->request->data)){

			$this->User->id = $id;
			$agent = $this->User->read();
		$text_avis =  $agent['User']['mail_infos'];
			if($this->User->saveField('mail_infos', '')){
            //On indique que l'entite est refusé
                //On save l'id admin si on doit le faire



                    $this->loadModel('User');
                    $this->User->id = $id;
                    $this->User->recursive = -1;
                    $user = $this->User->read();


                $this->sendCmsTemplateByMail($datasEmail['cms_id'], $user['User']['lang_id'], $user['User']['email'], array(
                    'TEXT_REPONSE' => !empty($datasEmail['motif'])?__('Pour le motif suivant :').'<br/>'.$datasEmail['motif']:'',
                    'ADMIN_EMAIL'  => $datasEmail['emailAdmin'],
					'TEXT_AVIS'  => $text_avis
                ));
                $this->Session->setFlash(__($message['success']),'flash_success');
            }else
                $this->Session->setFlash(__($message['error']),'flash_warning');

            if (!isset($this->request->data['isAjax']) || (int)$this->request->data['isAjax'] != 1)
                $this->redirect($url);
            else
                // Retour JSON
                $this->jsonRender(array('url' => $url));
        }
    }
    protected function getCartTokenFromCartDatas($cart_datas=false)
    {
        if (!$cart_datas || !is_array($cart_datas) || empty($cart_datas))return false;
        return base64_encode(serialize($cart_datas));
    }
    protected function clearSessionCart()
    {
        $this->Session->delete('User.cart');
        $this->Session->delete('User.id_cart');

    }
    /**
     * Permet de récupérer l'email d'un user à partir d'un autre model
     *
     * @param $model        string  Le nom du model qui contient la clé étrangère
     * @param $id           int     L'id de l'enregistrement du model
     * @param $foreignField string  Le nom de la clé étrangère qui fait référence à l'id de l'user
     * @param $primaryField string  Le nom de la clé primaire du model
     * @return mixed
     */
    protected function getEmail($model, $id, $foreignField, $primaryField){
        //On charge les models
        $this->loadModel($model);

        //On récupère l'id de l'user
        $this->User->id = $this->$model->field($foreignField, array($primaryField => $id));

        return $this->User->field('email');
    }

    /**
     * @param string    $phone  Le numéro de téléphone à tester
     * @param int       $limit  Le minimum de caractères attendu
     * @param int       $idUser L'id de l'user
     * @return bool|mixed|string
     */
    protected function checkPhoneNumber($phone, $limit, $idUser = 0){
        //Numéro valide ??
        $phone = $this->phoneNumberValid($phone, $limit);

        if($phone === false)
            return false;

        //Ce numéro est-il déjà enregistré ?
        $count = $this->User->find('count', array(
            'conditions' => array('User.phone_number' => $phone, 'User.deleted' => 0, 'User.role' => 'agent', 'User.id !=' => $idUser),
            'recursive' => -1
        ));


        if($count > 0){
            $this->Session->setFlash(__('Ce numéro de téléphone est déjà enregistré dans notre base de donnée expert, veuillez contacter le Support'), 'flash_warning');
            return false;
        }

        return $phone;
    }

    /**
     * @param string    $phone  Le numéro de téléphone à tester
     * @param int       $limit  Le minimum de caractères attendu
     * @return bool|mixed|string
     */
    protected function phoneNumberValid($phone, $limit){
        $result = preg_match('/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/',$phone);
        //On vérifie le numero de téléphone
        if(strlen($phone) < $limit || $result === false || $result === 0){
            $this->Session->setFlash(__('Numéro de téléphone invalide.'),'flash_warning');
            return false;
        }

        //On vérifie en détails le numéro de téléphone
        //S'il y a le signe '+'
        if($phone[0] === '+')
            $phone = substr($phone,1);
        $phone = str_replace(array('-',' ','|'),'',$phone);

        return $phone;
    }

    protected function paginatorParams(){
        if(isset($this->params['page']))
            $this->params['named'] += array('page' => $this->params['page']);
    }

    /*protected function checkMeta($data, $nameForm){
        $templateMeta = array(
            array(
                'field' => 'meta_title',
                'nameConfig' => 'Site.lengthMetaTitle',
            ),
            array(
                'field' => 'meta_keywords',
                'nameConfig' => 'Site.lengthMetaKeywords'
            ),
            array(
                'field' => 'meta_description',
                'nameConfig' => 'Site.lengthMetaDescription'
            )
        );

        //Les metas
        foreach($templateMeta as $meta){
            if(empty($data[$meta['field']])) continue;

            //Encodage du texte en HTML, pour compter le nombre exacte de caractère
            $htmlMeta = htmlentities($data[$meta['field']]);

            if($meta['field'] === 'meta_title'){
                $prefixeTitle = htmlentities(' - '.Configure::read('Site.name'));
                $maxLength = (int)Configure::read($meta['nameConfig']) - (int)strlen($prefixeTitle);
                if(strlen($htmlMeta) > $maxLength){
                    $this->Session->setFlash(__('La balise "titre" est trop grande. Vous avez droit à').' '.$maxLength.' '.__('caractères avec l\'encodage HTML. Exemple : la lettre "é" = "&eacute;" en HTML.'),'flash_warning');
                    return false;
                }
            }else{
                if(strlen($htmlMeta) > (int)Configure::read($meta['nameConfig'])){
                    $this->Session->setFlash(__('La balise').' "'.__($nameForm[$meta['field']]).__('" est trop grande. Vous avez droit à').' '.Configure::read($meta['nameConfig']).' '.__('caractères avec l\'encodage HTML. Exemple : la lettre "é" = "&eacute;" en HTML.') ,'flash_warning');
                    return false;
                }
            }
        }

        return true;
    }*/
    protected function getUniqReference()
    {
        return strtoupper(self::passwdGen(15));
    }
    /**
     * Random password generator
     *
     * @param integer $length Desired length (optional)
     * @param string $flag Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC)
     * @return string Password
     */
    public static function passwdGen($length = 8, $flag = 'ALPHANUMERIC')
    {
        switch ($flag)
        {
            case 'NUMERIC':
                $str = '0123456789';
                break;
            case 'NO_NUMERIC':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        for ($i = 0, $passwd = ''; $i < $length; $i++)
            $passwd .= self::substr($str, mt_rand(0, self::strlen($str) - 1), 1);
        return $passwd;
    }
    public static function strlen($str, $encoding = 'UTF-8')
    {
        if (is_array($str))
            return false;
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen'))
            return mb_strlen($str, $encoding);
        return strlen($str);
    }
    public static function substr($str, $start, $length = false, $encoding = 'utf-8')
    {
        if (is_array($str))
            return false;
        if (function_exists('mb_substr'))
            return mb_substr($str, (int)$start, ($length === false ? self::strlen($str) : (int)$length), $encoding);
        return substr($str, $start, ($length === false ? self::strlen($str) : (int)$length));
    }

	public function getCmsPageMail($cms_id=0, $lang_id=0, $langByDefaultIfTranslationDoesntExists=true)
    {
        if (!$cms_id)return false;
        if (!$lang_id)$lang_id = $this->Session->read('Config.id_lang');

        //On récupère la page
        App::import("Model", "Page");
        $model = new Page();
        $conditions = array(
            'fields' => array('PageLang.content', 'PageLang.meta_title'),
            'conditions' => array('Page.id' => $cms_id, 'active' => 1),
            'joins' => array(
                array(
                    'table' => 'page_langs',
                    'alias' => 'PageLang',
                    'conditions' => array(
                        'PageLang.page_id = Page.id',
                        'PageLang.lang_id = '.$lang_id
                    )
                )
            ),
            'recursive' => -1
        );
        $page = $model->find('first', $conditions);

        if (!empty($page))return $page;
               return false;
    }

    public function getCmsPage($cms_id=0, $lang_id=0, $langByDefaultIfTranslationDoesntExists=true)
    {
        if (!$cms_id)return false;
        if (!$lang_id)$lang_id = $this->Session->read('Config.id_lang');

        //On récupère la page
        App::import("Model", "Page");
        $model = new Page();
        $conditions = array(
            'fields' => array('PageLang.content', 'PageLang.meta_title'),
            'conditions' => array('Page.id' => $cms_id),
            'joins' => array(
                array(
                    'table' => 'page_langs',
                    'alias' => 'PageLang',
                    'conditions' => array(
                        'PageLang.page_id = Page.id',
                        'PageLang.lang_id = '.$lang_id
                    )
                )
            ),
            'recursive' => -1
        );
        $page = $model->find('first', $conditions);

        if (!empty($page))return $page;
        if (empty($page) && ($langByDefaultIfTranslationDoesntExists == true) && ($lang_id != 1)){
            return $this->getCmsPage($cms_id, 1, false);
        }

        return false;
    }

    public function isSystemNonPublicPage($cms_category_id=0)
    {
        if (empty($cms_category_id))return false;
        //if ($this->Session->read('Auth.User.role') == 'admin')return false;
        return in_array($cms_category_id,Configure::read('Categories.hidden_for_system'));
    }




    public function sendCmsTemplateByMail($cms_id=0, $id_lang=0, $mailTo=false, $vars=array(), $unsubscribe = false)
    {

        if (!$mailTo)
            $mailTo = (isset($this->cart_datas['user']['email'])&& !empty($this->cart_datas['user']['email']))?$this->cart_datas['user']['email']:false;
        if (!$mailTo)return false;

		$this->initEmailParameters();


        $page = $this->getCmsPageMail($cms_id, $id_lang);
		if(!$page)return false;
        $withFooter = Configure::read('Email.template.with_footer');

        if ($withFooter)
            $commonFooter = $this->getCmsPage(197, $id_lang);

        if (empty($page))
            return false;

        if (!empty($vars)){
            $this->mail_vars = array_merge($this->mail_vars, $vars);
        }

        /* On ajoute les variables de conf : */
        $conf = Configure::read('Email.template.vars');
        if (is_array($conf) && !empty($conf))
            $this->mail_vars = array_merge($this->mail_vars, $conf);

        /* On récupère la langue */
            $this->loadModel('Lang');
            $this->Lang->id = $id_lang;
            $langCode = $this->Lang->field('language_code');

        /* Locales */
            $locale = $this->Lang->field('lc_time');
            if (empty($locale)){
                $locale = CakeSession::check('Config.lc_time')?CakeSession::read('Config.lc_time'):'';
            }

            if (!empty($locale))
                setlocale(LC_ALL, $locale);

		$routs = Router::url('/', true);
		if(substr_count($routs,'localhost'))
			$routs = 'https://fr.spiriteo.com/';

        $this->mail_vars['PARAM_URLSITE'] = $routs;/*Router::url(array(
            'controller' => 'home', 'action' => 'index', 'admin' => false,
            'language' => $langCode
        ), true);*/

        //$title   = '['.Configure::read('Site.name').'] '.$this->treatMailDatas($page['PageLang']['meta_title']);
        $title   = $this->treatMailDatas($page['PageLang']['meta_title']);
        $content = $this->treatMailDatas($page['PageLang']['content']);





        $parms = array();
        $parms['content'] = $content;
        $parms = array_merge($parms, $this->mail_vars);
        $parms['FOOTER_HTML'] = ($withFooter)?$this->treatMailDatas($commonFooter['PageLang']['content']):'';

		if($unsubscribe){
			$url_domain = Router::url('/', true);
			if(substr_count( $url_domain, 'localhost')){
				$url_domain = str_replace('localhost','fr.spiriteo.com',$url_domain);
			}
			if($url_domain == 'http://')$url_domain = 'http://fr.spiriteo.com/';
			if($url_domain == 'https://')$url_domain = 'https://fr.spiriteo.com/';
			$parms['FOOTER_HTML'] .= '<br /><br /><p style="text-align:center"><a href="'.$url_domain.'crm/unsubscribe?m='.$mailTo.'">'.__('cliquez ici pour vous désinscrire.').'</a></p>';
		}

		$this->loadModel('User');
		if(is_object($this->request))
			$query = $this->request->query;
		else
			$query = '';

		$conditions = array(
							'User.email' => $mailTo,
						);

		$user_mail = $this->User->find('first',array('conditions' => $conditions));
		if($user_mail && $user_mail['User']['subscribe_mail']){
			$this->sendEmail($mailTo, $title, 'default', $parms);
			return true;
		}else{
			return false;
		}
    }

	public function sendCmsTemplatePublic($cms_id=0, $id_lang=0, $mailTo=false, $vars=array())
    {

        if (!$mailTo)return false;

		$this->initEmailParameters();

		$pixel = '';
		if(isset($vars['PIXEL'])){
			$pixel = $vars['PIXEL'];
		}

        $page = $this->getCmsPageMail($cms_id, $id_lang);
		if(!$page)return false;
        $withFooter = Configure::read('Email.template.with_footer');

        if ($withFooter)
            $commonFooter = $this->getCmsPage(197, $id_lang);

        if (empty($page))
            return false;

        if (!empty($vars)){
            $this->mail_vars = array_merge($this->mail_vars, $vars);
        }

        /* On ajoute les variables de conf : */
        $conf = Configure::read('Email.template.vars');
        if (is_array($conf) && !empty($conf))
            $this->mail_vars = array_merge($this->mail_vars, $conf);

        /* On récupère la langue */
            $this->loadModel('Lang');
            $this->Lang->id = $id_lang;
            $langCode = $this->Lang->field('language_code');

        /* Locales */
            $locale = $this->Lang->field('lc_time');
            if (empty($locale)){
                $locale = CakeSession::check('Config.lc_time')?CakeSession::read('Config.lc_time'):'';
            }

            if (!empty($locale))
                setlocale(LC_ALL, $locale);

		$routs = Router::url('/', true);
		if(substr_count($routs,'localhost'))
			$routs = 'https://fr.spiriteo.com/';

        $this->mail_vars['PARAM_URLSITE'] = $routs;/*Router::url(array(
            'controller' => 'home', 'action' => 'index', 'admin' => false,
            'language' => $langCode
        ), true);*/

        //$title   = '['.Configure::read('Site.name').'] '.$this->treatMailDatas($page['PageLang']['meta_title']);
        $title   = $this->treatMailDatas($page['PageLang']['meta_title']);
        $content = $this->treatMailDatas($page['PageLang']['content']);


        $parms = array();
        $parms['content'] = $content;
		$parms['PIXEL'] = $pixel;
        $parms = array_merge($parms, $this->mail_vars);
        $parms['FOOTER_HTML'] = ($withFooter)?$this->treatMailDatas($commonFooter['PageLang']['content']):'';


		$this->sendEmail($mailTo, $title, 'default', $parms);
		return true;
    }

	public function sendBodyTemplatePublic($body, $id_lang=0, $mailTo=false, $vars=array())
    {

        if (!$mailTo)return false;

		$this->initEmailParameters();

		$pixel = '';
		if(isset($vars['PIXEL'])){
			$pixel = $vars['PIXEL'];
		}

        $page = $body;
		if(!$page)return false;
        $withFooter = Configure::read('Email.template.with_footer');

        if ($withFooter)
            $commonFooter = $this->getCmsPage(197, $id_lang);

        if (empty($page))
            return false;

        if (!empty($vars)){
            $this->mail_vars = array_merge($this->mail_vars, $vars);
        }

        /* On ajoute les variables de conf : */
        $conf = Configure::read('Email.template.vars');
        if (is_array($conf) && !empty($conf))
            $this->mail_vars = array_merge($this->mail_vars, $conf);

        /* On récupère la langue */
            $this->loadModel('Lang');
            $this->Lang->id = $id_lang;
            $langCode = $this->Lang->field('language_code');

        /* Locales */
            $locale = $this->Lang->field('lc_time');
            if (empty($locale)){
                $locale = CakeSession::check('Config.lc_time')?CakeSession::read('Config.lc_time'):'';
            }

            if (!empty($locale))
                setlocale(LC_ALL, $locale);

		$routs = Router::url('/', true);
		if(substr_count($routs,'localhost'))
			$routs = 'https://fr.spiriteo.com/';

        $this->mail_vars['PARAM_URLSITE'] = $routs;/*Router::url(array(
            'controller' => 'home', 'action' => 'index', 'admin' => false,
            'language' => $langCode
        ), true);*/

        //$title   = '['.Configure::read('Site.name').'] '.$this->treatMailDatas($page['PageLang']['meta_title']);
        $title   = $this->treatMailDatas($page['PageLang']['meta_title']);
        $content = $this->treatMailDatas($page['PageLang']['content']);


        $parms = array();
        $parms['content'] = $content;
		$parms['PIXEL'] = $pixel;
        $parms = array_merge($parms, $this->mail_vars);
        $parms['FOOTER_HTML'] = ($withFooter)?$this->treatMailDatas($commonFooter['PageLang']['content']):'';


		$this->sendEmail($mailTo, $title, 'default', $parms);
		return true;
    }




    protected function treatMailDatas($datas=false)
    {
        $tmp = array();

        foreach ($this->mail_vars AS $k => $v){
            $newKey = '##'.strtoupper($k).'##';
            $tmp[$newKey] = $v;
        }
        return str_replace(array_keys($tmp), array_values($tmp), $datas);
    }
    protected function getActiveLangs()
    {
        $session_lang_id = $this->Session->read('Config.id_lang');


        if (!$session_lang_id){
            $this->loadModel('Lang');
            $langs = $this->Lang->find("list", array(
                'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
                'conditions'    => array('Lang.active' => 1),
                'recursive' => -1
            ));
            return $langs;
        }

        $result = Cache::read('langs_in_langid_'.$session_lang_id, 'lang_langid');

        if ($result !== false){
            return $result;
        }

        $this->loadModel('Lang');
        $rows = $this->Lang->find("all", array(
            'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
            'conditions'    => array('Lang.active' => 1),
            'recursive' => 1
        ));

        $langs = array();
        foreach ($rows AS $row){
            foreach ($row['LangLang'] AS $l){
                $langs[$row['Lang']['id_lang']] = array($row['Lang']['language_code'] => false);
                if ($l['in_lang_id']== $session_lang_id){
                    $langs[$row['Lang']['id_lang']] = array(
                        $row['Lang']['language_code'] => $l['name']
                    );
                    break;
                }
                if ($langs[$row['Lang']['id_lang']][$row['Lang']['language_code']] === false){
                    $langs[$row['Lang']['id_lang']][$row['Lang']['language_code']] = $row['Lang']['name'];
                }
            }
        }

        Cache::write('langs_in_langid_'.$session_lang_id, $langs, 'lang_langid');
        return $langs;

    }
    public function getCmsPageLink($cms_link_rewrite=false, $langage_code=false, $full=false)
    {
        if (empty($cms_link_rewrite))return false;

        if (empty($langage_code)){
            $langage_code = $this->request->params['language'];
        }
        if (empty($langage_code))return false;

        $seo_words_from_lang_code = Configure::read('Routing.pages');
		//$request_seo = $this->request;
		//$params_seo = $request_seo->params;
		//$link_seo = $params_seo["link_rewrite"];

		App::import("Model", "Lang");
        $model = new Lang();
		$r = $model->find('first',array(
            'conditions' => array('Lang.language_code' => $langage_code),
            'fields' => array('Lang.id_lang'),
            'recursive' => -1
        ));

		$id_lang = $r['Lang']['id_lang'];

		App::import("Model", "PageLang");
        $model = new PageLang();
		$r = $model->find('first',array(
            'conditions' => array('PageLang.link_rewrite' => $cms_link_rewrite, 'PageLang.lang_id' => $id_lang),
            'fields' => array('PageLang.page_id'),
            'recursive' => -1
        ));
		$id_page = $r['PageLang']['page_id'];

		App::import("Model", "Page");
        $model = new Page();
		$r = $model->find('first',array(
            'conditions' => array('Page.id' => $id_page),
            'fields' => array('Page.page_category_id'),
            'recursive' => -1
        ));
		$id_page_category = $r['Page']['page_category_id'];

		App::import("Model", "PageCategory");
        $model = new PageCategory();
		$r = $model->find('first',array(
            'conditions' => array('PageCategory.id' => $id_page_category),
            'fields' => array('PageCategory.id_parent'),
            'recursive' => -1
        ));
		$id_page_category_parent = $r['PageCategory']['id_parent'];
		App::import("Model", "PageCategoryLang");
        $model = new PageCategoryLang();
		if($id_page_category_parent){
			$r = $model->find('first',array(
				'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category_parent, 'PageCategoryLang.lang_id' => $id_lang),
				'fields' => array('PageCategoryLang.name'),
				'recursive' => -1
			));
			$name_page_category =  '';
			if(array_key_exists('PageCategoryLang', $r)){
				$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
			}
			$seo_word = isset($name_page_category)?$name_page_category:$seo_words_from_lang_code[$langage_code];
			$r = $model->find('first',array(
				'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category, 'PageCategoryLang.lang_id' => $id_lang),
				'fields' => array('PageCategoryLang.name'),
				'recursive' => -1
			));
			$name_page_category =  '';
			if(array_key_exists('PageCategoryLang', $r)){
				$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
			}

			$seo_word2 = isset($name_page_category)?$name_page_category:$seo_words_from_lang_code[$langage_code];
			 $opt  = array(
				'language' => $langage_code,
				'controller' => 'pages',
				'action' => 'display',
				'admin' => false,
				'link_rewrite' => $cms_link_rewrite,
				'seo_word' => $seo_word.'/'.$seo_word2,
			);
		}else{
			$r = $model->find('first',array(
				'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category, 'PageCategoryLang.lang_id' => $id_lang),
				'fields' => array('PageCategoryLang.name'),
				'recursive' => -1
			));
			$name_page_category =  '';
			if(array_key_exists('PageCategoryLang', $r)){
			//if(is_array($r['PageCategoryLang'])){
				$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
			}
			//$seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'page';
			$seo_word = isset($name_page_category)?$name_page_category:$seo_words_from_lang_code[$langage_code];
			$seo_word2 = '';
			if($seo_word){
				 $opt  = array(
					'language' => $langage_code,
					'controller' => 'pages',
					'action' => 'display',
					'admin' => false,
					'link_rewrite' => $cms_link_rewrite,
					'seo_word' => $seo_word
				);
			}else{
				$opt  = array(
					'language' => $langage_code,
					'controller' => 'pages',
					'action' => 'display',
					'admin' => false,
					'link_rewrite' => $cms_link_rewrite,
				);
			}
		}



		$url = Router::url($opt, $full);
        return $url;
    }
    public function getCategoryLink($category_link_rewrite=false, $langage_code=false, $full=false)
    {
        if (empty($category_link_rewrite))return false;
        if (empty($langage_code) && isset($this->request->params['language'])){
            $langage_code = $this->request->params['language'];
        }
        if (empty($langage_code))return false;
        $seo_words_from_lang_code = Configure::read('Routing.categories');

        //$seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'category';

		App::import("Model", "Lang");
        $model = new Lang();
		$r = $model->find('first',array(
            'conditions' => array('Lang.language_code' => $langage_code),
            'fields' => array('Lang.id_lang'),
            'recursive' => -1
        ));

		$id_lang = $r['Lang']['id_lang'];

		App::import("Model", "CategoryLang");
        $model = new CategoryLang();
		$r = $model->find('first',array(
            'conditions' => array( 'CategoryLang.lang_id' => $id_lang),
            'fields' => array('CategoryLang.cat_rewrite'),
            'recursive' => -1
        ));
		$cat_rewrite = $r['CategoryLang']['cat_rewrite'];

		$seo_word = isset($cat_rewrite)?$cat_rewrite:$seo_words_from_lang_code[$langage_code];

        $opt  = array(
            'language' => $langage_code,
            'controller' => 'category',
            'action' => 'displayUnivers',
            'admin' => false,
            'link_rewrite' => $category_link_rewrite,
            'seo_word' => $seo_word
        );

        return Router::url($opt, $full);
    }

	public function getReviewsLink($langage_code=false, $full=false)
    {
        if (empty($langage_code)){
            $langage_code = $this->request->params['language'];
        }
        if (empty($langage_code))return false;
        $seo_words_from_lang_code = Configure::read('Routing.reviews');

        $seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'avis-clients';

        return '/'.$langage_code.'/'.$seo_word;
    }

	public function getProductsLink($langage_code=false, $full=false)
    {
        if (empty($langage_code)){
            $langage_code = $this->request->params['language'];
        }
        if (empty($langage_code))return false;
        $seo_words_from_lang_code = Configure::read('Routing.products');

        $seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'tunnel-1-choisissez-le-nombre-de-minutes-a-acheter';

        return '/'.$langage_code.'/'.$seo_word;
    }

	public function getContactsLink($langage_code=false, $full=false)
    {
        if (empty($langage_code)){
            $langage_code = $this->request->params['language'];
        }
        if (empty($langage_code))return false;
        $seo_words_from_lang_code = Configure::read('Routing.contacts');

        $seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'contacts';

        return '/'.$langage_code.'/'.$seo_word;
    }

	 public function getHoroscopesLink($link_rewrite=false, $langage_code=false, $full=false)
		{

			if (empty($link_rewrite)){
				$tab_params = explode('/',$this->request->url);
				$link_rewrite = $tab_params[2];
			}
			return $link_rewrite;
		}
	public function getHoroscopeLink($idSign=false, $langage_code=false, $full=false)
		{
			if (empty($idSign))return false;
			if (empty($langage_code)){
				$langage_code = $this->request->params['language'];
			}
			if (empty($langage_code))return false;

			$seo_words_from_lang_code = Configure::read('Routing.horoscopes');

			$seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'horoscopes';

			App::import("Model", "Lang");
			$model = new Lang();
			$r = $model->find('first',array(
				'conditions' => array('Lang.language_code' => $langage_code),
				'fields' => array('Lang.id_lang'),
				'recursive' => -1
			));

			$id_lang = $r['Lang']['id_lang'];


			/*$linkr = $model->HoroscopeSign->find('first', array(
					'fields' => array('link_rewrite'),
					'conditions' => array('lang_id' => $id_lang, 'sign_id' => $idSign),
					'recursive' => -1
				));
			$link_rewrite = $linkr['HoroscopeSign']['link_rewrite'];*/
			$dbb_r = new DATABASE_CONFIG();
			$dbb_route = $dbb_r->default;
			$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
			$result_link = $mysqli_conf_route->query("SELECT link_rewrite from horoscope_signs where lang_id = '{$id_lang}' AND sign_id = '{$idSign}'");
			$row_routing_page = $result_link->fetch_array(MYSQLI_ASSOC);
			$link_rewrite = $row_routing_page['link_rewrite'];
			$mysqli_conf_route->close();
			return '/'.$langage_code.'/'.$seo_word.'/'.$link_rewrite;
		}

	public function slugify($str)
	{

		$str = strip_tags($str);
		$str = $this->remove_accents($str);
		$str = preg_replace('/[\r\n\t ]+/', ' ', $str);
		$str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
		$str = strtolower($str);
		$str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
		$str = htmlentities($str, ENT_QUOTES, "utf-8");
		$str = str_replace("&amp;", 'et', $str);
		$str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
		$str = str_replace(' ', '-', $str);
		$str = rawurlencode($str);
		$str = str_replace('%', '-', $str);
		return $str;
	}

	public function remove_accents($string) {
	   if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;

		 if ($this->seems_utf8($string)) {
		   $chars = array(
			// Decompositions for Latin-1 Supplement
			chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
			chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
			chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
			chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
			chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
			chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
			chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
			chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
			chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
			chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
			chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
			chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
			chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
			chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
			chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
			chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Decompositions for Latin Extended-B
			chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
			chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '',
			// Vowels with diacritic (Vietnamese)
			// unmarked
			chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
			chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
			// grave accent
			chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
			chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
			chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
			chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
			chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
			chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
			chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
			// hook
			chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
			chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
			chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
			chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
			chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
			chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
			chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
			chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
			chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
			chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
			chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
			chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
			// tilde
			chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
			chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
			chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
			chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
			chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
			chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
			chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
			chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
			// acute accent
			chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
			chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
			chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
			chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
			chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
			chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
			// dot below
			chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
			chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
			chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
			chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
			chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
			chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
			chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
			chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
			chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
			chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
			chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
			chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
			// Vowels with diacritic (Chinese, Hanyu Pinyin)
			chr(201).chr(145) => 'a',
			// macron
			chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
			// acute accent
			chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
			// caron
			chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
			chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
			chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
			chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
			chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
			// grave accent
			chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
			);

			// Used for locale-specific rules
				$chars[ chr(195).chr(134) ] = 'Ae';
				$chars[ chr(195).chr(166) ] = 'ae';
				$chars[ chr(195).chr(152) ] = 'Oe';
				$chars[ chr(195).chr(184) ] = 'oe';
				$chars[ chr(195).chr(133) ] = 'Aa';
				$chars[ chr(195).chr(165) ] = 'aa';

			$string = strtr($string, $chars);
		} else {
			$chars = array();
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars = array();
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}
		return $string;
	}

	public function seems_utf8($str) {
		/*mbstring_binary_safe_encoding();
		$length = strlen($str);
		reset_mbstring_encoding();
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; // 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; // 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; // 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; // 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; // 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; // 1111110b
			else return false; // Does not match any model
			for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}*/
		return true;
	}
	public function remove_emoji($text){
			  return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);
	}

	public function checkRedirect(){
		//verifie Redirect
		$this->loadModel('Redirect');
		$current_url = $this->here;
		$url = $this->Redirect->find('first',array(
							'conditions' => array('old' => $current_url, 'domain_id' => (int)$this->Session->read('Config.id_domain')),
							'recursive' => -1,
						));
		if(isset($url['Redirect']) && $url['Redirect']['new']){
			if($url['Redirect']['type'] == 301)
			header("Status: 301 Moved Permanently", false, 301);
			if($url['Redirect']['type'] == 302)
			header("Status: 301 Found", false, 302);
			header("Location: ".$url['Redirect']['new']);
			exit();
		}
	}

	private function _getTimezone()
    {

		$user = $this->Session->read('Auth.User');

		if(!empty($user) && $user['role'] == 'agent'){
			$this->loadModel('User');
			$country_agent = $this->User->field('country_id', array('id' => $user['id']));
			$domain_agent = $this->User->field('domain_id', array('id' => $user['id']));

			$this->loadModel('UserCountry');

			$cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.country_id'),
						'conditions' => array('UserCountry.id' => $country_agent),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));
			$countryInfo_agent = null;
			if($cc_infos && $cc_infos['CountryLang']['country_id']){
						$this->loadModel('Country');
						$countryInfo_agent = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $cc_infos['CountryLang']['country_id']),
							'recursive' => -1
						));
			}else{
						$this->loadModel('Domain');
						$domainInfo = $this->Domain->find('first', array(
							'fields' => array('country_id'),
							'conditions' => array('Domain.id' => $domain_agent),
							'recursive' => -1
						));

				if($domainInfo){
					$this->loadModel('Country');
					$countryInfo_agent = $this->Country->find('first', array(
								'fields' => array('timezone', 'devise', 'devise_iso'),
								'conditions' => array('Country.id' => $domainInfo['Domain']['country_id']),
								'recursive' => -1
							));
				}
			}

			if($countryInfo_agent && $countryInfo_agent['Country']['timezone']){
				$this->Session->write('Config.timezone_user', $countryInfo_agent['Country']['timezone']);
			}
		}
    }

	public function addUserCreditPrice($user_credit_id){
		if($user_credit_id){
			$this->loadModel('UserCredit');
			$userCredit = $this->UserCredit->find('first',array(
							'fields' => array('UserCredit.id,UserCredit.users_id,UserCredit.credits,Order.product_credits,Order.total,Order.currency,Order.total_euros'),
							'conditions' => array('UserCredit.id' => $user_credit_id),
							 'joins' => array(
									array(
										'table' => 'orders',
										'alias' => 'Order',
										'type'  => 'left',
										'conditions' => array(
											'Order.id = UserCredit.order_id',
										)
									)
								),
								'recursive' => -1
						));

			$devise = 1;
			//if($userCredit['Order']['currency'] == '$') $devise = 0.67;
			//if($userCredit['Order']['currency'] == 'CHF') $devise = 0.895858;

			$price = ($userCredit['Order']['total'] / $userCredit['UserCredit']['credits']) * $devise;
			
			
			
			$devise = '€';
			if($userCredit['Order']['currency'])$devise = $userCredit['Order']['currency'];
			
			if($devise == '€')
				$price_euros = $price;
			else
				$price_euros = 0;

			$this->loadModel('UserCreditPrice');
            $this->UserCreditPrice->create();
            $this->UserCreditPrice->save(array(
                'id_user_credit'    => $userCredit['UserCredit']['id'],
                'user_id' => $userCredit['UserCredit']['users_id'],
                'price' => $price,
				'price_euros' => $price_euros,
				'devise' => $devise,
                'seconds'   => $userCredit['UserCredit']['credits'],
				'seconds_left'   => $userCredit['UserCredit']['credits'],
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd'   => date('Y-m-d H:i:s'),
            ));
		}
		return '';
	}

	public function calcCAComm($user_credit_history, $is_admin = false){
		if($user_credit_history){
			$this->loadModel('UserCreditHistory');
			$UserCreditHistory = $this->UserCreditHistory->find('first',array(
							'fields' => array('UserCreditHistory.user_id','UserCreditHistory.credits','UserCreditHistory.type_pay','UserCreditHistory.domain_id','UserCreditHistory.seconds'),
							'conditions' => array('UserCreditHistory.user_credit_history' => $user_credit_history),
								'recursive' => -1
						));
			if($UserCreditHistory){
				
				$tmp_comm = $UserCreditHistory['UserCreditHistory']['credits'];
				$user_id =  $UserCreditHistory['UserCreditHistory']['user_id'];
				$this->loadModel('UserCreditPrice');
				$lastCredits = $this->UserCreditPrice->find('all', array(
					'fields'        => array('UserCreditPrice.*'),
					'conditions'    => array('UserCreditPrice.user_id' => $user_id, 'UserCreditPrice.status' => 0),
					'order'         => 'UserCreditPrice.id asc',
					'recursive'     => 1
				));
				
				$ca = 0;
				$ca_euros = 0;
				$ca_ids = array();
				$ca_currency = '';
				
				if($is_admin && !$lastCredits){
					
					$lastCredits = $this->UserCreditPrice->find('all', array(
						'fields'        => array('UserCreditPrice.*'),
						'conditions'    => array('UserCreditPrice.user_id' => $user_id, 'UserCreditPrice.status' => 1),
						'order'         => 'UserCreditPrice.id desc',
						'recursive'     => 1,
						'limit' => 1
					));

					foreach($lastCredits as &$last){
						$last['UserCreditPrice']['seconds_left'] = $tmp_comm;
					}
				}

        		if($lastCredits){
					foreach($lastCredits as $last){
						$left = $last['UserCreditPrice']['seconds_left'];
						$diff = $left - $tmp_comm;
						$ca_currency = $last['UserCreditPrice']['devise'];
						if($tmp_comm > 0){
							if($diff <= 0){
								//update user credit pour le cloturer
								$tmp_comm = $tmp_comm - $left;

								$this->UserCreditPrice->id = $last['UserCreditPrice']['id'];
								$this->UserCreditPrice->saveField('date_upd', date('Y-m-d H:i:s'));
								$this->UserCreditPrice->saveField('seconds_left', 0);
								$this->UserCreditPrice->saveField('status', 1);
								if($left > 0){
								  $ca += $last['UserCreditPrice']['price'] * $left;
								  $ca_euros += $last['UserCreditPrice']['price_euros'] * $left;
								  $ca_tab = array();
								  $ca_tab['id'] = $last['UserCreditPrice']['id'];
								  $ca_tab['seconds'] = $left;
								  //$ca_ids .= $last['UserCreditPrice']['id'].'_';
								  array_push($ca_ids,$ca_tab);
								}
							  }else{

								//update diff sur ce user credit et on quit
								$this->UserCreditPrice->id = $last['UserCreditPrice']['id'];
								$this->UserCreditPrice->saveField('date_upd', date('Y-m-d H:i:s'));
								$this->UserCreditPrice->saveField('seconds_left', $diff);

								$ca += $last['UserCreditPrice']['price'] * $tmp_comm;
								$ca_euros += $last['UserCreditPrice']['price_euros'] * $tmp_comm;
								$ca_tab = array();
								$ca_tab['id'] = $last['UserCreditPrice']['id'];
								$ca_tab['seconds'] = $tmp_comm;
								//$ca_ids .= $last['UserCreditPrice']['id'].'_';
								array_push($ca_ids,$ca_tab);
								break;
						     }
						}
					}

					//update ca
					if($is_admin)
						$this->UserCreditHistory->id = $user_credit_history;
					else
						$this->UserCreditHistory->user_credit_history = $user_credit_history;
					$this->UserCreditHistory->saveField('ca', $ca);
					$this->UserCreditHistory->saveField('ca_euros', $ca_euros);
					$this->UserCreditHistory->saveField('ca_ids', serialize($ca_ids));
					$this->UserCreditHistory->saveField('ca_currency', $ca_currency);
				}else{

				  if($UserCreditHistory['UserCreditHistory']['type_pay'] == 'pre'){
					   //load dernier achat du client
					  $this->loadModel('Order');
					  $lastOrder = $this->Order->find('first', array(
						'fields'        => array('Order.*'),
						'conditions'    => array('Order.user_id' => $user_id, 'Order.valid' => 1, 'Order.product_credits >' => 0),
						'order'         => 'Order.id desc',
						'recursive'     => 1
					  ));
					 if($lastOrder){
						$cost = $lastOrder['Order']['total'] / ($lastOrder['Order']['product_credits'] + $lastOrder['Order']['voucher_credits']);
						$cost_euros = $lastOrder['Order']['total_euros'] / ($lastOrder['Order']['product_credits'] + $lastOrder['Order']['voucher_credits']);
						$ca = $cost * $tmp_comm; 
						$ca_euros = $cost_euros * $tmp_comm; 
						$ca_currency = $lastOrder['Order']['currency'];
					   if($is_admin)
						  $this->UserCreditHistory->id = $user_credit_history;
						else
						  $this->UserCreditHistory->user_credit_history = $user_credit_history;
						$this->UserCreditHistory->saveField('ca', $ca);
						$this->UserCreditHistory->saveField('ca_euros', $ca_euros);
						$this->UserCreditHistory->saveField('ca_ids', $ca_ids);
						$this->UserCreditHistory->saveField('ca_currency', $ca_currency);
					 }
				   }
				}
				if($UserCreditHistory['UserCreditHistory']['type_pay'] == 'aud'){
					$country_id = 1;
					$ca_currency = '€';
					switch ($UserCreditHistory['UserCreditHistory']['domain_id']) {
							case '19':
								$country_id = 1;
								$ca_currency = '€';
								break;
							case '13':
								$country_id = 3;
								$ca_currency = 'CHF';
								break;
							case '11':
								$country_id = 4;
								$ca_currency = '€';
								break;
							case '22':
								$country_id = 5;
								$ca_currency = '€';
								break;
							case '29':
								$country_id = 13;
								$ca_currency = '$';
								break;
						}
					$this->loadModel('CountryLangPhone');
					$phoneCountry = $this->CountryLangPhone->find('first', array(
						'fields'        => array('CountryLangPhone.surtaxed_minute_cost'),
						'conditions'    => array('CountryLangPhone.country_id' => $country_id, 'CountryLangPhone.lang_id' => 1),
						'recursive'     => 1
					));
					$ca = 0;
					$ca_ids = '';
					if($phoneCountry){
						$cost_second = $phoneCountry['CountryLangPhone']['surtaxed_minute_cost'] / 60;
						$ca = $UserCreditHistory['UserCreditHistory']['seconds'] * $cost_second; 
						if($is_admin)
							$this->UserCreditHistory->id = $user_credit_history;
						else
							$this->UserCreditHistory->user_credit_history = $user_credit_history;
						$this->UserCreditHistory->saveField('ca', $ca);
						$this->UserCreditHistory->saveField('ca_currency', $ca_currency);
					}
				}
				
			}
		}
		if($is_admin)return $ca;
		else return '';
	}

	public function checkCGV(){
		$this->loadModel('Cgv');
		$check_cgv = $this->Cgv->find('first',array(
							'conditions'    => array(
								'user_id' => $this->Auth->user('id'),
							),
							'order' => "date_valid DESC",
							'recursive' => -1
						));
		$cgv_a_valide = false;

		if(count($check_cgv)){
			$dd_valid = $check_cgv['Cgv']['date_valid'];
			$this->loadModel('Page');
			$pagecgv = $this->Page->find('first',array(
								'conditions'    => array(
									'id' => 245,
								),
								'recursive' => -1
							));
			$dd_cgv = $pagecgv['Page']['date_upd'];

			$ddvalid = str_replace(' ','',$dd_valid);
			$ddvalid = str_replace('-','',$ddvalid);
			$ddvalid = str_replace(':','',$ddvalid);

			$ddcgv = str_replace(' ','',$dd_cgv);
			$ddcgv = str_replace('-','',$ddcgv);
			$ddcgv = str_replace(':','',$ddcgv);

			if($ddcgv > $ddvalid)$cgv_a_valide = true;

		}else{
			$cgv_a_valide = true;
		}

		if($cgv_a_valide)
			$this->redirect(array('controller' => 'agents', 'action' => 'profil'));
	}

	public function checkVAT(){

		$user = $this->User->find('first',array(
                    'conditions' => array('id' => (int)$this->Auth->user('id')),
                    'recursive' => -1
                ));
		$vat_a_valide = false;
		if($user['User']['vat_num_status'] == 'invalide' && !$user['User']['vat_num_status_reason']){
			$vat_a_valide = true;
		}
		//if($vat_a_valide)
		//	$this->redirect(array('controller' => 'agents', 'action' => 'vatnum'));
	}

	public function checkIban(){

		$user = $this->User->find('first',array(
                    'conditions' => array('id' => (int)$this->Auth->user('id')),
                    'recursive' => -1
                ));


		//begin consult
		$this->loadModel('UserCreditHistory');
        $lastCom = $this->UserCreditHistory->find('first', array(
            'fields'        => array('UserCreditHistory.*'),
            'conditions'    => array('UserCreditHistory.agent_id' => $user['User']['id']),
			'order'         => 'UserCreditHistory.date_start asc',
            'recursive'     => 1
        ));
		if($lastCom){
			//delay 24hour before check iban
			$date_com = new DateTime($lastCom['UserCreditHistory']['date_start']);
			$date_com->modify('+ 24 hour');
			if($date_com->format('Ymd') >= date('Ymd'))unset($lastCom);
		}

		$iban = false;
		if(!$lastCom || $user['User']['iban'] || $user['User']['rib']){
			$iban = true;
		}

		if(!$iban){
			$this->Session->setFlash(__('Merci de saisir votre IBAN ou RIB afin de continuer à travailler sur Spiriteo, en cliquant sur l\'onglet " Modifier votre profil " votre compte sera ensuite débloqué.'), 'flash_warning');
			$this->redirect(array('controller' => 'agents', 'action' => 'profil'));
		}

	}

	public function checkActivate(){

		if($this->Auth->user('id')){
      $this->loadModel('User');
			$user = $this->Session->read('Auth.User');
			$active = $this->User->field('active', array('id' => $user['id']));

			if(!empty($user) && $user['role'] == 'agent' && !$active){
				$this->destroySessionAndCookie();
				$this->Auth->logout();
				$this->Session->delete('Auth.User');
  				$this->Session->delete('User');
				$this->Cookie->destroy();
				$this->redirect('/');
			}
		}

	}

	public function saveAdminLog(){
		$user = $this->Session->read('Auth.User');
		$this->loadModel('AdminLog');

		$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$list_obj = explode('/',$url);
		foreach($list_obj as $ob){
			$object = str_replace('_',' ',$ob);
		}

		//save acces
		$requestData = array();
		$requestData['AdminLog'] = array();
		$requestData['AdminLog']['user_id'] 	= $user['id'];
		$requestData['AdminLog']['object'] 		= ucfirst($object);
		$requestData['AdminLog']['url'] 		= $url;
		$requestData['AdminLog']['type'] 		= 'access';
		$requestData['AdminLog']['data'] 		= '';
		$requestData['AdminLog']['date_add'] 	= date('Y-m-d H:i:s');
		$this->AdminLog->create();
        $this->AdminLog->save($requestData);

		 if($this->request->is('post')){
         	$requestData = $this->request->data;
			$data = json_encode($requestData);
			$requestData = array();
			$requestData['AdminLog'] = array();
			$requestData['AdminLog']['user_id'] 	= $user['id'];
			$requestData['AdminLog']['object'] 		= ucfirst($object);
			$requestData['AdminLog']['url'] 		= $url;
			$requestData['AdminLog']['type'] 		= 'submit';
			$requestData['AdminLog']['data'] 		= $data;
			$requestData['AdminLog']['date_add'] 	= date('Y-m-d H:i:s');
			$this->AdminLog->create();
			$this->AdminLog->save($requestData);
		 }
	}

	public function checkBadLink(){
		$uri = $_SERVER['REQUEST_URI'];
		if(substr_count($uri,'link_rewrite'))
			$this->redirect('/');
		if(substr_count($uri,'seo_word'))
			$this->redirect('/');
		if(substr_count($uri,'display') && !substr_count($uri,'category'))
			$this->redirect('/');

	}

	public function checkSupportFil(){

		$in_support = true;
		if($this->params['controller'] != 'support')$in_support = false;
		if($this->params['controller'] == 'support' && $this->params['action'] != 'admin_fil' )$in_support = false;

		if(!$in_support && $this->params['controller'] == 'support' && $this->params['action'] == 'admin_message'){
			$this->loadModel('Support');
			$user = $this->Session->read('Auth.User');
			$support = $this->Support->find('all',array(
					'conditions' => array('user_live' => $user['id']),
					'recursive' => -1,
				));
			if($support){
				foreach($support as $supp){
					$this->Support->id = $supp['Support']['id'];
					$this->Support->saveField('user_live', NULL);
				}
			}
		}
	}

	public function SaveSource(){

		$cookies = $_COOKIE;
		$query_data = $this->params->query;
		$source = '';

		if(array_key_exists('ap_ref_tracking',$cookies)){
			$affiliate_key = $cookies['ap_ref_tracking'];
			switch ($affiliate_key) {
				case "6":
					$source = 'SOS Voyants';
					break;
			}
		}

		/*if (isset($cookies["__utma"]) and isset($cookies["__utmz"])) {

			list($domain_hash,$timestamp, $session_number, $campaign_numer, $campaign_data) = preg_split('[\.]', $_COOKIE["__utmz"],5);
			$campaign_data = parse_str(strtr($campaign_data, "|", "&"));

			$source = 'SEO '.$utmcsr;

			if($utmccn && !array_key_exists('ap_ref_tracking',$cookies)){
				$source = 'Spiriteo Google Ads '.$utmccn;
			}

			if($utmccn && array_key_exists('ap_ref_tracking',$cookies)){
				$source = 'SOS Voyants Google Ads';//.$utmccn;
			}

		}*/

		if(!$source && isset($query_data['gclid']) && $query_data['gclid'] && isset($query_data['campaign']) && $query_data['campaign']){
			$source = 'Google DSA';
		}

		if(!$source && isset($query_data['gclid']) && $query_data['gclid']){
			$source = 'Google Ads';
		}



		if(!$source && isset($query_data['utm_source']) && $query_data['utm_source']){
			switch ($query_data['utm_source']) {
				case "facebook":
				case "Facebook":
				case "fb":
				case "FB":
					$source = 'Facebook';
					break;
				case "bing":
				case "Bing":
					$source = 'Bing';
					break;
				case "blog":
				case "Blog":
					$source = 'Blog';
					break;
				case "google":
				case "Google":
					$source = 'Google';
					break;
			}
		}

		if(!$source){

			$ref = '';
			if(isset($_SERVER['HTTP_REFERER']))
			$ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
			if($ref ){

				if(substr_count($ref,'google'))$source = 'Google';
				if(substr_count($ref,'bing'))$source = 'Bing';
				if(substr_count($ref,'sosvoyants.fr'))$source = 'SOS Voyants Fr';
				if(substr_count($ref,'sosvoyants') && !$source)$source = 'SOS Voyants';
				if(substr_count($ref,'blog.spiriteo'))$source = 'Blog';
				if(!$source){
					$source = $ref;
				}
			}
		}

		if(!$source){
			$source = 'Direct';
		}

		if(empty($this->Cookie->read('customer_s')))
			$this->Cookie->write('customer_s', $source, false, '1 year');
	}
}
