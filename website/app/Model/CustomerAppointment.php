<?php
    App::uses('AppModel', 'Model');
    /**
     * CustomerAppointment Model
     *
     */
    class CustomerAppointment extends AppModel {

        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'Agent' => array(
                'className' => 'User',
                'foreignKey' => 'agent_id',
                'conditions' => '',
                'fields' => array('Agent.id','Agent.pseudo','Agent.agent_number'),
                'order' => ''
            ),
            'User' => array(
                'className' => 'User',
                'foreignKey' => 'user_id',
                'conditions' => '',
                'fields' => array('User.id'),
                'order' => ''
            )
        );

        public function hasAppointment($agent_id, $dateAppointment){
            return $this->find('first',array(
                'conditions' => array(
                    'agent_id'  => $agent_id,
                    'A'         => $dateAppointment['A'],
                    'M'         => $dateAppointment['M'],
                    'J'         => $dateAppointment['J'],
                    'H'         => $dateAppointment['H'],
                    'Min'         => $dateAppointment['Min']
                )
            ));
        }

        public function appointments($idAgent, $dateDebut, $dateFin){
            $appointments = $this->find('all',array(
                'conditions' => $this->getConditionsValid($idAgent,$dateDebut,$dateFin),
                'order' => array('CustomerAppointment.A ASC', 'CustomerAppointment.M ASC', 'CustomerAppointment.J ASC', 'CustomerAppointment.H ASC', 'CustomerAppointment.Min ASC'),
                'recursive' => -1
            ));
			
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($extension == 'ca'){;	
							
				$appointmentsNew = array();
				foreach($appointments as $appoint){
					
					$appo = $appoint["CustomerAppointment"];
					$appointNew = array();
					$appointNew["CustomerAppointment"] = array();
					$timestamp = mktime($appo["H"],$appo["Min"],0,$appo["M"],$appo["J"],$appo["A"]) - 21600;
					$appo["H"] = date('H',$timestamp);
					$appo["Min"] = date('I',$timestamp);
					$appo["M"] = date('m',$timestamp);
					$appo["J"] = date('d',$timestamp);
					$appo["A"] = date('Y',$timestamp);
					$appointNew["CustomerAppointment"]=$appo;
					array_push($appointmentsNew,$appointNew);
				}
				
				$appointments = $appointmentsNew;
			}
			

            return $this->restructureAppointment($appointments);
        }

        public function restructureAppointment($appointments){
            $data = array();
            if(!empty($appointments)){
                foreach($appointments as $appointment){
                    //Date du jour en cours
                    $dateJour = $appointment['CustomerAppointment']['J'].'-'. $appointment['CustomerAppointment']['M'] .'-'.$appointment['CustomerAppointment']['A'];
                    //On save les datas de l'horaire
                    $saveHoraire = array(
                        'user_id' => $appointment['CustomerAppointment']['user_id'],
                        'H' => $appointment['CustomerAppointment']['H'],
                        'Min' => $appointment['CustomerAppointment']['Min']
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

        public function restructureAppointmentV2($appointments){
            $data = array();
            if(!empty($appointments)){
                foreach($appointments as $appointment){
                    //Date du jour en cours
                    $dateJour = $appointment['CustomerAppointment']['J'].'-'. $appointment['CustomerAppointment']['M'] .'-'.$appointment['CustomerAppointment']['A'];
                    //On save les datas de l'horaire
                    $saveHoraire = array(
                        'firstname' => $appointment['User']['firstname'],
						'id' => $appointment['CustomerAppointment']['id'],
						'valid' => $appointment['CustomerAppointment']['valid'],
						'status' => $appointment['CustomerAppointment']['status'],
                        'H' => $appointment['CustomerAppointment']['H'],
                        'Min' => $appointment['CustomerAppointment']['Min'],
						'user_utc' => $appointment['CustomerAppointment']['user_utc'],
						'agent_utc' => $appointment['CustomerAppointment']['agent_utc'],
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
		
		public function restructureAppointmentClientV2($appointments){
            $data = array();
            if(!empty($appointments)){
				
				App::import('Controller', 'Extranet');
            	$app = new ExtranetController;
				
                foreach($appointments as $appointment){
					
					 $photo = $app->mediaAgentExist($appointment['Agent']['agent_number'],'Image');
					//Pas de photo, photo par défaut
					if($photo === false)
						$photo = '/'.Configure::read('Site.defaultImage');
					else
						$photo = '/'.$photo;
					
                    //Date du jour en cours
                    $dateJour = $appointment['CustomerAppointment']['J'].'-'. $appointment['CustomerAppointment']['M'] .'-'.$appointment['CustomerAppointment']['A'];
                    //On save les datas de l'horaire
                    $saveHoraire = array(
                        'pseudo' => $appointment['Agent']['pseudo'],
						'agent_number' => $appointment['Agent']['agent_number'],
						'photo' => $photo,
						'id' => $appointment['CustomerAppointment']['id'],
						'valid' => $appointment['CustomerAppointment']['valid'],
						'reponse' => $appointment['CustomerAppointment']['txt'],
						'status' => $appointment['CustomerAppointment']['status'],
                        'H' => $appointment['CustomerAppointment']['H'],
                        'Min' => $appointment['CustomerAppointment']['Min'],
						'user_utc' => $appointment['CustomerAppointment']['user_utc'],
						'agent_utc' => $appointment['CustomerAppointment']['agent_utc']
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
                'CustomerAppointment.agent_id' => $idAgent,
                'OR' => array(
                    array('CustomerAppointment.J' => $dateDebut['J'], 'CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.A' => $dateDebut['A']),
                    array('CustomerAppointment.J' => $dateFin['J'], 'CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.A' => $dateFin['A']),
                    array(
                        array('CustomerAppointment.A' => $dateDebut['A']),
                        array('CustomerAppointment.A' => $dateFin['A']),
                        array(
                            'OR' => array(
                                array(
                                    array('CustomerAppointment.M' => $dateDebut['M']),
                                    array('CustomerAppointment.M' => $dateFin['M']),
                                    array('CustomerAppointment.J >=' => $dateDebut['J']),
                                    array('CustomerAppointment.J <=' => $dateFin['J'])
                                ),
                                array('CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.M !=' => $dateFin['M'], 'CustomerAppointment.J >=' => $dateDebut['J']),
                                array('CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.M !=' => $dateDebut['M'], 'CustomerAppointment.J <=' => $dateFin['J']),
                                array('CustomerAppointment.M >' => $dateDebut['M'], 'CustomerAppointment.M <' => $dateFin['M'])
                            )
                        )
                    ),
                    array(
                        'OR' => array(
                            array('CustomerAppointment.A >' => $dateDebut['A']),
                            array('CustomerAppointment.A <' => $dateFin['A'])
                        ),
                        array(
                            'OR' => array(
                                array('CustomerAppointment.A' => $dateDebut['A'], 'CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.J >=' => $dateDebut['J']),
                                array('CustomerAppointment.A' => $dateDebut['A'], 'CustomerAppointment.M >' => $dateDebut['M']),
                                array('CustomerAppointment.A' => $dateFin['A'], 'CustomerAppointment.M <' => $dateFin['M']),
                                array('CustomerAppointment.A' => $dateFin['A'], 'CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.J <=' => $dateFin['J']),
                                array('CustomerAppointment.A >' => $dateDebut['A'], 'CustomerAppointment.A <' => $dateFin['A'])
                            )
                        )
                    )
                )
            );
            return $conditions;
        }
		public function getConditionsValid($idAgent,$dateDebut,$dateFin){

            $conditions = array(
                'CustomerAppointment.agent_id' => $idAgent,
				'CustomerAppointment.valid >=' => 0,
                'OR' => array(
                    array('CustomerAppointment.J' => $dateDebut['J'], 'CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.A' => $dateDebut['A']),
                    array('CustomerAppointment.J' => $dateFin['J'], 'CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.A' => $dateFin['A']),
                    array(
                        array('CustomerAppointment.A' => $dateDebut['A']),
                        array('CustomerAppointment.A' => $dateFin['A']),
                        array(
                            'OR' => array(
                                array(
                                    array('CustomerAppointment.M' => $dateDebut['M']),
                                    array('CustomerAppointment.M' => $dateFin['M']),
                                    array('CustomerAppointment.J >=' => $dateDebut['J']),
                                    array('CustomerAppointment.J <=' => $dateFin['J'])
                                ),
                                array('CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.M !=' => $dateFin['M'], 'CustomerAppointment.J >=' => $dateDebut['J']),
                                array('CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.M !=' => $dateDebut['M'], 'CustomerAppointment.J <=' => $dateFin['J']),
                                array('CustomerAppointment.M >' => $dateDebut['M'], 'CustomerAppointment.M <' => $dateFin['M'])
                            )
                        )
                    ),
                    array(
                        'OR' => array(
                            array('CustomerAppointment.A >' => $dateDebut['A']),
                            array('CustomerAppointment.A <' => $dateFin['A'])
                        ),
                        array(
                            'OR' => array(
                                array('CustomerAppointment.A' => $dateDebut['A'], 'CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.J >=' => $dateDebut['J']),
                                array('CustomerAppointment.A' => $dateDebut['A'], 'CustomerAppointment.M >' => $dateDebut['M']),
                                array('CustomerAppointment.A' => $dateFin['A'], 'CustomerAppointment.M <' => $dateFin['M']),
                                array('CustomerAppointment.A' => $dateFin['A'], 'CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.J <=' => $dateFin['J']),
                                array('CustomerAppointment.A >' => $dateDebut['A'], 'CustomerAppointment.A <' => $dateFin['A'])
                            )
                        )
                    )
                )
            );
            return $conditions;
        }
		public function getConditionsClient($idClient,$dateDebut,$dateFin){

            $conditions = array(
                'CustomerAppointment.user_id' => $idClient,
                'OR' => array(
                    array('CustomerAppointment.J' => $dateDebut['J'], 'CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.A' => $dateDebut['A']),
                    array('CustomerAppointment.J' => $dateFin['J'], 'CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.A' => $dateFin['A']),
                    array(
                        array('CustomerAppointment.A' => $dateDebut['A']),
                        array('CustomerAppointment.A' => $dateFin['A']),
                        array(
                            'OR' => array(
                                array(
                                    array('CustomerAppointment.M' => $dateDebut['M']),
                                    array('CustomerAppointment.M' => $dateFin['M']),
                                    array('CustomerAppointment.J >=' => $dateDebut['J']),
                                    array('CustomerAppointment.J <=' => $dateFin['J'])
                                ),
                                array('CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.M !=' => $dateFin['M'], 'CustomerAppointment.J >=' => $dateDebut['J']),
                                array('CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.M !=' => $dateDebut['M'], 'CustomerAppointment.J <=' => $dateFin['J']),
                                array('CustomerAppointment.M >' => $dateDebut['M'], 'CustomerAppointment.M <' => $dateFin['M'])
                            )
                        )
                    ),
                    array(
                        'OR' => array(
                            array('CustomerAppointment.A >' => $dateDebut['A']),
                            array('CustomerAppointment.A <' => $dateFin['A'])
                        ),
                        array(
                            'OR' => array(
                                array('CustomerAppointment.A' => $dateDebut['A'], 'CustomerAppointment.M' => $dateDebut['M'], 'CustomerAppointment.J >=' => $dateDebut['J']),
                                array('CustomerAppointment.A' => $dateDebut['A'], 'CustomerAppointment.M >' => $dateDebut['M']),
                                array('CustomerAppointment.A' => $dateFin['A'], 'CustomerAppointment.M <' => $dateFin['M']),
                                array('CustomerAppointment.A' => $dateFin['A'], 'CustomerAppointment.M' => $dateFin['M'], 'CustomerAppointment.J <=' => $dateFin['J']),
                                array('CustomerAppointment.A >' => $dateDebut['A'], 'CustomerAppointment.A <' => $dateFin['A'])
                            )
                        )
                    )
                )
            );
            return $conditions;
        }

    }
