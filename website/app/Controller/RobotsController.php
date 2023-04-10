<?php
App::uses('AppController', 'Controller');

class RobotsController extends AppController {
    public $components = array('RequestHandler');
    public $layout = false;
    public function beforeRender()
    {
        $this->RequestHandler->renderAs($this, 'text');
        parent::beforeRender();

    }
    public function index()
    {
        $this->layout = null;
        
		$generiq_domains = explode(',',Configure::read('Site.id_domain_com'));
		$domain_id = $this->Session->read('Config.id_domain');
		if(in_array($domain_id,$generiq_domains) && $domain_id != 15){
			$this->set('sitemap_url', Router::url(array('controller' => 'sitemap','action' => 'index'), true));
			$this->set('content_robot', 'User-agent: *
Disallow: /');
		}else{
			if($domain_id != 15)
			$this->set('sitemap_url', Router::url(array('controller' => 'sitemap','action' => 'index'), true));
			$this->set('content_robot', 'User-agent: *
Disallow: /agents
Disallow: /home/ajaxactivity
Disallow: /users/login* 
Disallow: /users/passwdforget*
Disallow: /users/subscribe*
');
			
		}
    }
}