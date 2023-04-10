<?php
/*
Deploy
 > ROOT/app/Plugin/CakeSmarty/Controller/Component/CakeSmartyComponent.php

ROOT/lib/Cake/Controller/AppController.php
 > public $components = array('CakeSmarty.CakeSmarty');

ROOT/app/Config/bootstrap.php
 > CakePlugin::load('CakeSmarty');

*/
App::import('Vendor', 'Smarty', array('file' => 'smarty'.DS.'Smarty.class.php'));



class CakeSmartyComponent extends Component {
    public $components = array('RequestHandler', 'Session');
    private $ext = ".tpl";
    private $smarty;

    public function __construct(ComponentCollection $collection, $settings = array()) {
        return false;
    }
    
    public function startup($controller){
        $controller->ext = $this->ext;
    }

    public function shutdown($controller) {
        $this->smarty = new Smarty();
        $this->smarty->compile_check = true;
        $this->smarty->debugging = false;
        $this->smarty->template_dir = APP . 'View' . DS . $controller->viewPath.DS;
        $this->smarty->plugins_dir = VENDORS . 'smarty'.DS . 'plugins' . DS;
        $this->smarty->compile_dir = TMP . 'smarty' . DS . 'templates_c' . DS;
        $this->smarty->cache_dir = TMP.'smarty' . DS . 'cache' . DS;
        $this->smarty->error_reporting = 'E_ALL & ~E_NOTICE';

        foreach($controller->viewVars as $viewVarKey=>$viewVarValue){
            $this->smarty->assign($viewVarKey, $viewVarValue);
        }

        $controller->ext = $this->ext;
        
        $out = $this->smarty->fetch($controller->view . $controller->ext);

        $controller->response->body($out);
    }
}