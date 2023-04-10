<?php

App::import('Vendor', 'Noox/ApiVonage');

    class Api {


        /*
        private $port = 2290;*/
        private $url = 'https://api.daotec.com/spiriteo';//'http://spiriteo.daotec.com';
        private $port = 80;
        private $curl = null;
        private $schema = array(
            'agent_create' => array(),
            'agent_update' => array(),
            'agent_deactivate' => array(),
            'agent_activate' => array(),
            'agent_connect' => array(),
            'agent_deconnect' => array(),
			'agent_alertAgent' => array()
        );

        public function __construct(){
            //Init de curl
            $this->url = Configure::read('Site.urlApi');
            $this->port = Configure::read('Site.portApi');
            $this->curl = curl_init();
        }

        private function curl_exec($options = array()){
            if(empty($options))
                return false;

            //On attribue les options à curl
            curl_setopt_array($this->curl, $options);
            $response = curl_exec($this->curl);
            curl_close($this->curl);

            return json_decode($response,true);
        }

        private function postToApi($action = '', $postData = array()){
            if(empty($action))
                return false;
            //Les options pour php curl
            $optionsCurl = array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_URL => $this->url.$action,
                CURLOPT_PORT => $this->port
            );

            //On execute curl
            $res = $this->curl_exec($optionsCurl);

            if (empty($res) && !substr_count($action, 'sms')){
                $logs = array();
                foreach ($postData AS $k => $v)
                    $logs[] = $k.' => '.$v;
                $logs = implode(", ",$logs);

                $datas = array(
                    'msg' => 'Erreur appel API téléphonie (classe api.php): (url:'.$this->url.$action.' port:'.$this->port.') action:'.$action.' '.$logs.' => PAS DE RETOUR',
                    'date_add' => date("Y-m-d H:i:s")
                );
               // CakeLog::write('error', $datas['msg']);
            }
			
            return $res;
        }

        private function getFromApi($action = ''){
            if(empty($action))
                return false;
            //Les options pour php curl
            $optionsCurl = array(
                CURLOPT_POST => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $this->url.$action,
                CURLOPT_PORT => $this->port
            );
            //On execute curl
            return $this->curl_exec($optionsCurl);
        }

        public function createAgent($agent_number, $phone_number){
            //Check les paramètres
            if(empty($agent_number) || empty($phone_number) || !is_numeric($agent_number) || !is_string($phone_number))
                return false;

            //Check le numéro de tel
          //  if($phone_number[0] !== '0')
           //     $phone_number = '00'.$phone_number;

            //Init les datas à envoyer
            $postData = array(
                'agent_number' => (int)$agent_number,
                'agent_redial_number' => $phone_number
            );
            //On execute la requete
            return $this->postToApi('/api/agent', $postData);
        }

        public function updateAgent($agent_number, $phone_number){
            //Check les paramètres
            if(empty($agent_number) || empty($phone_number) || !is_numeric($agent_number) || !is_string($phone_number))
                return false;

            //Check le numéro de tel
           // if($phone_number[0] !== '0')
             //   $phone_number = '00'.$phone_number;

            //Init les datas à envoyer
            $postData = array('agent_redial_number' => $phone_number);
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number, $postData);
        }

        public function connectAgent($agent_number){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/connect');
        }

        public function deconnectAgent($agent_number){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/deconnect');
        }

        public function deactivateAgent($agent_number){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/deactivate');
        }

        public function activateAgent($agent_number){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/activate');
        }

        public function startRecordingAgent($agent_number){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/recording/start');
        }

        public function stopRecordingAgent($agent_number){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/recording/stop');
        }

        public function sendSms($phone, $content){
            //Check les paramètres
            if(empty($phone) || !is_numeric($phone) || empty($content) || !is_string($content))
                return false;

            $apiVonage = new ApiVonage();

            return $apiVonage->sendSms($phone, $content);

            //On modifie le port
            //$this->url = 'http://api-visionweb.dom';
            //$this->port = 80;
//            $this->url = Configure::read('Site.smsUrlApi');
//            $this->port = Configure::read('Site.smsPortApi');
//            //On execute la requete
//			$ret = $this->postToApi('/sms/SendTop/'.$phone.'/'.$content);
//
//			if(is_numeric($ret))
//            	return $ret;
//			else
//				return 1;
        }

        //-------------------------------------------------------------------A SUPPRIMER-----------------------------------------------------------
        public function deleteAgent($agent_number){
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;

            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/delete');
        }
        //-------------------------------------------------------------------------------------------------------------------------------------
		public function alertAgent($agent_number, $idalert){
            //Check les paramètres
            if(empty($agent_number) || !is_numeric($agent_number))
                return false;
            $postData = array('id_alert' => $idalert);
            //On execute la requete
            return $this->postToApi('/api/agent/'.$agent_number.'/alert_agent', $postData);
        }

    }