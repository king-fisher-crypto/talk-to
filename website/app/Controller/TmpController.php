<?php
App::uses('AppController', 'Controller');

class TmpController extends AppController {
    public $helpers = array(
        'Text',
        'Form' => array('className' => 'BootstrapForm'),
        'Html' => array('className' => 'CustomHtml'),
        'Session','Metronic','Nooxtools','Frontblock'
    );
    public function beforeFilter()
    {
        $this->Auth->allow('index','deleteuser');
        parent::beforeFilter();
    }
    function index()
    {
        $this->layout = false;
        


    }

}
    