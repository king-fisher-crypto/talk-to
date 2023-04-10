<?php
App::uses('AppModel', 'Model');
/**
 * CustomerCredit Model
 *
 * @property Customer $Customer
 */
class Alert extends AppModel {
    public $primaryKey = 'id';

    public $belongsTo = array(
        'AlertHistory' => array(
            'className' => 'AlertHistory',
            'foreignKey' => false,
            'dependent' => false,
            'conditions' => 'AlertHistory.alerts_id = Alert.id',
            'fields' => '',
            'order' => '',
            'type'  => 'left',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
    public $hasOne = array(
        'Domain' => array(
            'className' => 'Domain',
            'foreignKey' => false,
            'dependent' => false,
            'conditions' => 'Domain.id = Alert.domain_id',
            'fields' => '',
            'order' => '',
            'type'  => 'left',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function exist($data, $media){
        if(empty($data) || empty($media))
            return false;
		

        $alert = $this->find('count', array(
            'conditions' => array(
                'Alert.agent_id'    => $data['agent_id'],
                'Alert.users_id'    => $data['user_id'],
                'Alert.media'       => $media,
			    'Alert.date_add >= ' => date("Y-m-d H:i:s", time() - (86400 * (int)Configure::read('Site.alerts.days')))
            ),
			
            'recursive' => -1
        ));

		//Si il existe une alerte pour ce media
        if($alert > 0)
            return true;
        else
            return false;
    }


    public function alertSmsToday($idAgent, $phone){
        if(empty($idAgent) || empty($phone))
            return false;

        //On récupère les id des alertes pour cet agent et numéro
        $alerts = $this->find('list', array(
            'conditions'    => array('Alert.agent_id' => $idAgent, 'Alert.phone_number' => $phone),
        ));

        //On récupère les id des alertes pour ce numéro
        $allAlerts = $this->find('list', array(
            'conditions'    => array('Alert.phone_number' => $phone),
        ));

        //Date d'aujourd'hui
       $startEnd = date('Y-m-d H:i:s');
        //$startEnd = date('Y-m-d 23:59:59');
		$dt = new DateTime($startEnd);
		$dt->modify('- 1 minutes');
		$startBegin = $dt->format('Y-m-d H:i:s');

        //S'il y a déjà des alertes d'envoyées aujourd'hui pour ce couple
        if($this->countSms($alerts, $startBegin, $startEnd) > 0)
            return false;
        //Il faut vérifier si le nombre de sms part jour n'a pas été dépassé pour ce numéro
        else if($this->countSms($allAlerts, $startBegin, $startEnd) >= Configure::read('Site.alerts.max_sms'))
            return false;
        else
            return true;
    }

    private function countSms ($idAlerts, $dateStart, $dateEnd){
        App::import('Model', 'AlertHistory');
        $alertHistory = new AlertHistory();
        $alertsHistory = $alertHistory->find('list', array(
            'fields'        => array('alerts_id', 'date_add'),
            'conditions'    => array('AlertHistory.alerts_id' => $idAlerts, 'AlertHistory.alert_type' => 'sms', 'AlertHistory.date_add >=' => $dateStart, 'AlertHistory.date_add <=' => $dateEnd),
            'recursive'     => -1
        ));

        //On compte le nombre d'sms envoyé aujourd'hui, en supprimant les doublons. (Les alertes qui ont les mêmes dates ont été envoyés dans le même sms)
        $nbrSms = count(array_unique($alertsHistory));

        return $nbrSms;
    }
	
	public function alertEmailToday($idAgent, $email){
        if(empty($idAgent) || empty($email))
            return false;

        //On récupère les id des alertes pour cet agent et numéro
        $alerts = $this->find('list', array(
            'conditions'    => array('Alert.agent_id' => $idAgent, 'Alert.email' => $email),
        ));

        //On récupère les id des alertes pour ce numéro
        $allAlerts = $this->find('list', array(
            'conditions'    => array('Alert.email' => $email),
        ));

        //Date d'aujourd'hui
       $startEnd = date('Y-m-d H:i:s');
        //$startEnd = date('Y-m-d 23:59:59');
		$dt = new DateTime($startEnd);
		$dt->modify('- 1 minutes');
		$startBegin = $dt->format('Y-m-d H:i:s');

        //S'il y a déjà des alertes d'envoyées aujourd'hui pour ce couple
        if($this->countEmail($alerts, $startBegin, $startEnd) > 0)
            return false;
        //Il faut vérifier si le nombre de sms part jour n'a pas été dépassé pour ce numéro
        else if($this->countEmail($allAlerts, $startBegin, $startEnd) >= Configure::read('Site.alerts.max_sms'))
            return false;
        else
            return true;
    }

    private function countEmail ($idAlerts, $dateStart, $dateEnd){
        App::import('Model', 'AlertHistory');
        $alertHistory = new AlertHistory();
        $alertsHistory = $alertHistory->find('list', array(
            'fields'        => array('alerts_id', 'date_add'),
            'conditions'    => array('AlertHistory.alerts_id' => $idAlerts, 'AlertHistory.alert_type' => 'email', 'AlertHistory.date_add >=' => $dateStart, 'AlertHistory.date_add <=' => $dateEnd),
            'recursive'     => -1
        ));

        //On compte le nombre d'sms envoyé aujourd'hui, en supprimant les doublons. (Les alertes qui ont les mêmes dates ont été envoyés dans le même sms)
        $nbrSms = count(array_unique($alertsHistory));

        return $nbrSms;
    }
}
