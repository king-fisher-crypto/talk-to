<?php
App::uses('AppHelper', 'View/Helper');

class NooxtoolsHelper extends AppHelper {
    public $helpers = array('Html','Session');

    public function displayPrice($price=0, $devise=0)
    {
        if (!$devise)$devise = $this->Session->read('Config.devise');
		
		$parts = explode('.', $_SERVER['SERVER_NAME']);
		if(sizeof($parts)) $extension = end($parts); else $extension = '';

		if($extension == 'ca')$devise='$-ca';	
			
        switch ($devise){
            case '$':
                return $devise.number_format($price, 2, ',', ' ');
            break;
			case '$-ca':
                return number_format($price, 2, ',', ' ').' '.'$';
            break;
            case 'all':
                return number_format($price, 2, ',', ' ').' '.'$-€-CHF';
            break;
            default:
                return number_format($price, 2, ',', ' ').' '.$devise;
            break;
        }



    }
    public function cleanCut($string,$length,$cutString = '...')
    {
        if(strlen($string) <= $length)
        {
            return $string;
        }
        $str = substr($string,0,$length-strlen($cutString)+1);
        return substr($str,0,strrpos($str,' ')).$cutString;
    }

    public function getAdminUrl($params=array())
    {
        if (!isset($params['controller']) || !isset($params['action']))return false;
        return $this->Html->url(array('admin' => true,'controller' => $params['controller'],'action' => $params['action']));
    }
    
}
    