<?php
App::uses('AppModel', 'Model');
/**
 * Relances Model
 *
 * @property
 */
class Sponsorship extends AppModel {
    public $primaryKey = 'id';
	
	
	public function Benefit($userCreditLastHistoryID)
	{
		App::import('Model', 'UserCreditLastHistory');
        $userCreditLastHistory = new UserCreditLastHistory();
		
		$lastCom = $userCreditLastHistory->find('first', array(
							'conditions'    => array('UserCreditLastHistory.user_credit_last_history' => $userCreditLastHistoryID),
							'recursive'     => -1
						));
		//check si users_id dans sponsorship = id_customer && bonus =  ( pas encore complété )
		 $sponsorship = $this->find('first', array(
							'conditions'    => array('Sponsorship.id_customer' => $lastCom['UserCreditLastHistory']['users_id'], 'Sponsorship.bonus' => 0,
							'Sponsorship.status <' => 4),
							'recursive'     => -1
						));
		$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
		App::import('Model', 'UserIp');
		$UserIp = new UserIp();
		$check_ip = $UserIp->find('first',array(
						'conditions'    => array(
							'IP' => $ip_user,
							'user_id !=' => $lastCom['UserCreditLastHistory']['users_id'],
						),
						'recursive' => -1
					));
		if($sponsorship && $check_ip){
			$this->updateAll(array('status'=>7), array('Sponsorship.id' => $sponsorship['Sponsorship']['id']));
		}
		if($sponsorship && !$check_ip){
			App::import('Model', 'SponsorshipRule');
			$sponsorshipRule = new SponsorshipRule();
			//recup la rule
			$rule = $sponsorshipRule->find('first', array(
							'conditions'    => array('SponsorshipRule.id' => $sponsorship['Sponsorship']['id_rules']),
							'recursive'     => -1
						));
			if($rule){
				$palier = $rule['SponsorshipRule']['palier'];
				$palier_type = $rule['SponsorshipRule']['palier_type'];
				
				$is_palier = 0;
				
				switch($palier_type){
					 case 'euros':
						App::import('Model', 'Order');
						$order = new Order();
						$all_orders = $order->find('all', array(
							'conditions'    => array('Order.user_id' => $sponsorship['Sponsorship']['id_customer'],'Order.valid' =>1, 'Order.date_add >' => $sponsorship['Sponsorship']['date_add'] ),
							'recursive'     => -1
						));
						$total = 0;
						foreach($all_orders as $ord){
							$total = $total + $ord['Order']['total'];
						}
						if($total > $palier)$is_palier = 1;
						break;
					case 'seconde':
						$lastComs = $userCreditLastHistory->find('all', array(
							'conditions'    => array('UserCreditLastHistory.users_id' => $sponsorship['Sponsorship']['id_customer'], 'UserCreditLastHistory.date_start >' => $sponsorship['Sponsorship']['date_add']),
							'recursive'     => -1
						));
						$total = 0;
						foreach($lastComs as $comm){
							$total = $total + $comm['UserCreditLastHistory']['credits'];
						}
						if($total > $palier)$is_palier = 1;
						break;
						
				}
				if($is_palier){
					//ajout du bonus 
					$bonus = "'".$rule['SponsorshipRule']['data']."'";
					
					//si agent je recup categorie remuneration agent
					/*if($sponsorship['Sponsorship']['type_user'] == 'agent'){
						App::import('Model', 'User');
						$User = new User();
						$agent = $User->find('first', array(
							'conditions'    => array('User.id' => $sponsorship['Sponsorship']['user_id']), 
							'recursive'     => -1
						));
						if($agent['User']['order_cat']){
							App::import('Model', 'Cost');
							$Cost = new Cost();
							$remu = $Cost->find('first', array(
								'conditions'    => array('Cost.id' => $agent['User']['order_cat']), 
								'recursive'     => -1
							));
							if($remu['Cost']['cost']){
								$bonus = $remu['Cost']['cost'] / 10;
								$bonus = "'".$bonus."'";
							}
						}					 
					}*/
					
					$bonus_type= "'".$rule['SponsorshipRule']['data_type']."'";
					$test = $this->updateAll(array('status'=>3,'bonus'=>$bonus,'bonus_type'=>$bonus_type), array('Sponsorship.id' => $sponsorship['Sponsorship']['id']));
				}
			}
		}
		
	}

}
