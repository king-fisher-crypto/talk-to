<?php
App::uses('AppModel', 'Model');
/**
 * Planning Model
 *
 * @property User $User
 */
class Planning extends AppModel {


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * hasOne associations
     *
     * @var array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function agent_planning($idAgent,$dateDebut,$dateFin, $noCache=false){
		
		$parts = explode('.', $_SERVER['SERVER_NAME']);
		if(sizeof($parts)) $extension = end($parts); else $extension = '';
		
        $cacheKey = 'planning-'.$idAgent.'-'.implode("",$dateDebut).'-'.implode("",$dateFin);

        if(Cache::read($cacheKey, Configure::read('nomCachePlanning')) !== false && !$noCache && $extension != 'ca'){
            return unserialize(Cache::read($cacheKey, Configure::read('nomCachePlanning')));
        }else{
            $planning = $this->_get_planning($idAgent,$dateDebut,$dateFin);
            $planning = $this->restructurePlanning($planning);
			if($extension != 'ca')
            Cache::write($cacheKey, serialize($planning), Configure::read('nomCachePlanning'));
            return $planning;
        }
    }

    public function _get_planning($idAgent,$dateDebut,$dateFin){
        $data = $this->find('all',array(
            'conditions' => $this->getConditions($idAgent,$dateDebut,$dateFin),
            'order' => array('Planning.A ASC', 'Planning.M ASC', 'Planning.J ASC', 'Planning.H ASC', 'Planning.Min ASC', 'Planning.type DESC'),
            'recursive' => -1
        ));
		
		$parts = explode('.', $_SERVER['SERVER_NAME']);
		if(sizeof($parts)) $extension = end($parts); else $extension = '';
		if($extension == 'ca'){;	
							
				$dataNew = array();
				foreach($data as $appoint){
					
					$appo = $appoint["Planning"];
					$appointNew = array();
					$appointNew["Planning"] = array();
					$timestamp = mktime($appo["H"],$appo["Min"],0,$appo["M"],$appo["J"],$appo["A"]) - 21600;
					$appo["H"] = date('H',$timestamp);
					$appo["Min"] = date('I',$timestamp);
					$appo["M"] = date('m',$timestamp);
					$appo["J"] = date('d',$timestamp);
					$appo["A"] = date('Y',$timestamp);
					$appointNew["Planning"]=$appo;
					array_push($dataNew,$appointNew);
				}
				
				$data = $dataNew;
			}
		
		
        return $data;
    }

    public function restructurePlanning($planning){
        $data = array();
        if(!empty($planning)){
            foreach($planning as $horaire){
                //Date du jour en cours
                $dateJour = $horaire['Planning']['J'].'-'. $horaire['Planning']['M'] .'-'.$horaire['Planning']['A'];
                //On save les datas de l'horaire
                $saveHoraire = array(
                    'type' =>  $horaire['Planning']['type'],
                    'H' => $horaire['Planning']['H'],
                    'Min' => $horaire['Planning']['Min']
                );

                //S'il y a aucune donnée dans la date du jour
                if(empty($data[$dateJour]))
                    $data[$dateJour]= array();
                //On met à la suite
                array_push($data[$dateJour],$saveHoraire);
            }
        }

        return $data;
    }

    public function getConditions($idAgent,$dateDebut,$dateFin){
        $conditions = array(
            'Planning.user_id' => $idAgent,
            'OR' => array(
                array('Planning.J' => $dateDebut['J'], 'Planning.M' => $dateDebut['M'], 'Planning.A' => $dateDebut['A']),
                array('Planning.J' => $dateFin['J'], 'Planning.M' => $dateFin['M'], 'Planning.A' => $dateFin['A']),
                array(
                    array('Planning.A' => $dateDebut['A']),
                    array('Planning.A' => $dateFin['A']),
                    array(
                        'OR' => array(
                            array(
                                array('Planning.M' => $dateDebut['M']),
                                array('Planning.M' => $dateFin['M']),
                                array('Planning.J >=' => $dateDebut['J']),
                                array('Planning.J <=' => $dateFin['J'])
                            ),
                            array('Planning.M' => $dateDebut['M'], 'Planning.M !=' => $dateFin['M'], 'Planning.J >=' => $dateDebut['J']),
                            array('Planning.M' => $dateFin['M'], 'Planning.M !=' => $dateDebut['M'], 'Planning.J <=' => $dateFin['J']),
                            array('Planning.M >' => $dateDebut['M'], 'Planning.M <' => $dateFin['M'])
                        )
                    )
                ),
                array(
                    'OR' => array(
                        array('Planning.A >' => $dateDebut['A']),
                        array('Planning.A <' => $dateFin['A'])
                    ),
                    array(
                        'OR' => array(
                            array('Planning.A' => $dateDebut['A'], 'Planning.M' => $dateDebut['M'], 'Planning.J >=' => $dateDebut['J']),
                            array('Planning.A' => $dateDebut['A'], 'Planning.M >' => $dateDebut['M']),
                            array('Planning.A' => $dateFin['A'], 'Planning.M <' => $dateFin['M']),
                            array('Planning.A' => $dateFin['A'], 'Planning.M' => $dateFin['M'], 'Planning.J <=' => $dateFin['J']),
                            array('Planning.A >' => $dateDebut['A'], 'Planning.A <' => $dateFin['A'])
                        )
                    )
                )
            )

            /* Ancienne version fausse */
            /*'OR' => array(
                array(
                    array('Planning.J' => $dateDebut['J'], 'Planning.M' => $dateDebut['M'], 'Planning.A' => $dateDebut['A']),
                    array('Planning.J' => $dateFin['J'], 'Planning.M' => $dateFin['M'], 'Planning.A' => $dateFin['A'])
                ),
                array(
                    array('Planning.J >=' => $dateDebut['J'], 'Planning.M' => $dateDebut['M'], 'Planning.A' => $dateDebut['A']),
                    array('Planning.J <=' => $dateFin['J'], 'Planning.M' => $dateFin['M'], 'Planning.A' => $dateFin['A'])
                ),
                array(
                    array('Planning.A' => $dateDebut['A']),
                    array('Planning.A' => $dateFin['A']),
                    array(
                        'OR' => array(
                            array('Planning.M' => $dateDebut['M'], 'Planning.J >=' => $dateDebut['J']),
                            array('Planning.M' => $dateFin['M'], 'Planning.J <=' => $dateFin['J']),
                            array('Planning.M >' => $dateDebut['M'], 'Planning.M <' => $dateFin['M'])
                        )
                    )
                ),
                array(
                    array('Planning.A >' => $dateDebut['A'], 'Planning.A <' => $dateFin['A']),
                    array(
                        'OR' => array(
                            array('Planning.A' => $dateDebut['A'], 'Planning.M' => $dateDebut['M'], 'Planning.J >=' => $dateDebut['J']),
                            array('Planning.A' => $dateDebut['A'], 'Planning.M >' => $dateDebut['M']),
                            array('Planning.A' => $dateFin['A'], 'Planning.M <' => $dateFin['M']),
                            array('Planning.A' => $dateFin['A'], 'Planning.M' => $dateFin['M'], 'Planning.J <=' => $dateFin['J']),
                            array('Planning.A >' => $dateDebut['A'], 'Planning.A <' => $dateFin['A'])
                        )
                    )
                )
            )*/
        );

        return $conditions;
    }


    public function getFirstDispo($idAgent, $dateNow){
        if(empty($idAgent))
            return array();

        $data = $this->find('first', array(
            'conditions' => array(
                'Planning.user_id'  => $idAgent,
                'Planning.type'     => 'debut',
                'OR' => array(
                    array(
                        array('Planning.J' => $dateNow['J'], 'Planning.M' => $dateNow['M'], 'Planning.A' => $dateNow['A']),
                        array(
                            'OR' => array(
                                array('Planning.H' => $dateNow['H'], 'Planning.Min >' => $dateNow['Min']),
                                array('Planning.H >' => $dateNow['H'])
                            )
                        )
                    ),
                    array('Planning.A' => $dateNow['A'], 'Planning.M' => $dateNow['M'], 'Planning.J >' => $dateNow['J']),
                    array('Planning.A' => $dateNow['A'], 'Planning.M >' => $dateNow['M']),
                    array('Planning.A >' => $dateNow['A'])
                )
            ),
            'recursive' => -1
        ));

        return $data;
    }

}
