<?php
App::uses('AppController', 'Controller');
App::uses('ExtranetController', 'Controller');
App::uses('AlertsController', 'Controller');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::import('Vendor', 'Noox/Api');

class AdvertisingController extends ExtranetController {
    public function admin_index()
    {
        
    }
}