<?php
    App::uses('AppModel', 'Model');
    /**
     * Voucher Model
     *
     */
    class Voucher extends AppModel {

        public function codeUnique($code){
            if(empty($code))
                return false;

            //on récupère les codes identiques
            $codes = $this->find('count', array(
                'conditions' => array('Voucher.code' => $code),
            ));

            //Si ce code existe déjà
            if($codes > 0)
                return false;
            else
                return true;
        }

        /**
         * Permet de vérifier un coupon, qu'il existe et est valide
         *
         * @param string    $code   Le code du coupon
         * @return bool
         */
        public function existAndValidVoucher($code){
			
			
			
            if(empty($code))
                return false;

            $voucher = $this->find('first', array(
                'fields'        => array('Voucher.validity_start', 'Voucher.validity_end'),
                'conditions'    => array('Voucher.active' => 1, 'Voucher.code' => $code),
                'recursive'     => -1
            ));

            if(empty($voucher))
                return false;

            //Date today
            $dateNow = date('Y-m-d H:i:s');

            //On retire le décalage horaire
           # $dateStart = CakeTime::format(Tools::dateUser(CakeSession::read('Config.timezone_user'),$voucher['Voucher']['validity_start']),'%Y-%m-%d %H:%M:00');
            #$dateEnd = CakeTime::format(Tools::dateUser(CakeSession::read('Config.timezone_user'),$voucher['Voucher']['validity_end']),'%Y-%m-%d %H:%M:00');
			$dateStart = CakeTime::format($voucher['Voucher']['validity_start'],'%Y-%m-%d %H:%M:00');
            $dateEnd = CakeTime::format($voucher['Voucher']['validity_end'],'%Y-%m-%d %H:%M:00');
			
            //Valid ??
			
            if($dateNow < $dateStart || $dateNow > $dateEnd)
                return false;

            //Coupon ok
            return true;
        }

        /**
         * Le client peut-t-il utiliser ce coupon
         *
         * @param string    $code           Le code du coupon
         * @param int       $personal_code  Le code personnel du client
         * @param int       $user_id        L'id du client
         * @param int       $product_id     L'id du produit
         * @return bool
         */
        public function rightToUse($code, $personal_code, $user_id, $product_id){
            if(empty($code) || empty($personal_code) || !is_numeric($personal_code) || !is_numeric($product_id))
                return false;
            //Le coupon est valide ?
            if(!$this->existAndValidVoucher($code))
                return false;

            //On récupère le coupon
            $voucher = $this->find('first', array(
                'fields'        => array('Voucher.population', 'Voucher.number_use', 'Voucher.product_ids','Voucher.country_ids','Voucher.number_use_by_user','Voucher.buy_only'),
                'conditions'    => array('Voucher.code' => $code),
                'recursive'     => -1
            ));

			//Normalement n'est pas vide, existAndValidVoucher fait la vérification
            if(empty($voucher))
                return false;

            /* Valable sur quelques produits ou tous */
            if (isset($voucher['Voucher']['product_ids']))
                $product_ids = explode(",", $voucher['Voucher']['product_ids']);
            else $product_ids = array();

            if ($voucher['Voucher']['product_ids'] != 'all' && !in_array($product_id, $product_ids) && !$voucher['Voucher']['buy_only'])
                return false;

            /* Valable sur quelques pays ou tous */
            if (isset($voucher['Voucher']['country_ids']))
                $country_ids = explode(",", $voucher['Voucher']['country_ids']);
            else $country_ids = array();

            if ($voucher['Voucher']['country_ids'] != 'all' && !in_array(CakeSession::read('Config.id_country'), $country_ids))
                return false;


            //Utilisation pour tout le monde et illimité
            if(empty($voucher['Voucher']['population']) && $voucher['Voucher']['number_use'] == 0 && $voucher['Voucher']['number_use_by_user'] == 0)
                return true;

            //Seuls quelques membres y ont accès
            if(!empty($voucher['Voucher']['population'])){
                //les codes personnels des ces membres
                $customers = explode(',', $voucher['Voucher']['population']);
                //si le client n'est pas parmis ceux autorisés alors false
                if(!in_array($personal_code, $customers))
                    return false;
            }



            //Utilisation limitée à un nbre d'utilisations du bon
            if($voucher['Voucher']['number_use'] != 0){
                App::import('Model', 'VoucherHistory');
                $voucherHistory = new VoucherHistory();
                //Le nombre d'utilisation
                $numberUse = $voucherHistory->getNumberUse($code, $user_id);

                //Si problème, ou si utilisation >= à la limite
                if($numberUse === false || $numberUse >= $voucher['Voucher']['number_use'])
                    return false;
            }

            //Utilisation limitée à un nombre d'utilisation par utilisateur
            if($voucher['Voucher']['number_use'] != 0){
                App::import('Model', 'VoucherHistory');
                $voucherHistory = new VoucherHistory();
                //Le nombre d'utilisation
                $numberUse = $voucherHistory->getNumberUse($code);

                //Si problème, ou si utilisation >= à la limite
                if($numberUse === false || $numberUse >= $voucher['Voucher']['number_use'])
                    return false;
            }
			

            if ($voucher['Voucher']['number_use_by_user'] != 0){
                App::import('Model', 'VoucherHistory');
                $voucherHistory = new VoucherHistory();
                //Le nombre d'utilisation
                $numberUse = $voucherHistory->getNumberUseForCustomer($code, $user_id);
                //Si problème, ou si utilisation >= à la limite
                if($numberUse === false || $numberUse >= $voucher['Voucher']['number_use_by_user'])
                    return false;
            }
			
			//User come back
            if($user_id){
                App::import('Model', 'User');
                $user_co = new User();
                if($user_co->accountComeBack($user_id))
                    return false;
            }
			
            //Tout est ok, droit d'utiliser le coupon
            return true;
        }
		
	     /**
         * Le client peut-t-il voir ce coupon
         *
         * @param string    $code           Le code du coupon
         * @param int       $product_id     L'id du produit
         * @return bool
         */
        public function rightToUsePublic($code, $product_id){
            if(empty($code) || !is_numeric($product_id))
                return false;
            //On récupère le coupon
            $voucher = $this->find('first', array(
                'fields'        => array('Voucher.population', 'Voucher.number_use', 'Voucher.product_ids','Voucher.country_ids','Voucher.number_use_by_user','Voucher.buy_only'),
                'conditions'    => array('Voucher.code' => $code),
                'recursive'     => -1
            ));

			//Normalement n'est pas vide, existAndValidVoucher fait la vérification
            if(empty($voucher))
                return false;
			 //Utilisation limitée à un nombre d'utilisation par utilisateur
            if($voucher['Voucher']['number_use'] != 0){
                App::import('Model', 'VoucherHistory');
                $voucherHistory = new VoucherHistory();
                //Le nombre d'utilisation
                $numberUse = $voucherHistory->getNumberUse($code);

                //Si problème, ou si utilisation >= à la limite
                if($numberUse === false || $numberUse >= $voucher['Voucher']['number_use'])
                    return false;
            }
            /* Valable sur quelques produits ou tous */
            if (isset($voucher['Voucher']['product_ids']))
                $product_ids = explode(",", $voucher['Voucher']['product_ids']);
            else $product_ids = array();

            if ($voucher['Voucher']['product_ids'] != 'all' && !in_array($product_id, $product_ids) && !$voucher['Voucher']['buy_only'])
                return false;
            /* Valable sur quelques pays ou tous */
            if (isset($voucher['Voucher']['country_ids']))
                $country_ids = explode(",", $voucher['Voucher']['country_ids']);
            else $country_ids = array();

            if ($voucher['Voucher']['country_ids'] != 'all' && !in_array(CakeSession::read('Config.id_country'), $country_ids))
                return false;

            //Utilisation pour tout le monde et illimité
            if(empty($voucher['Voucher']['population']) && $voucher['Voucher']['number_use'] == 0 && $voucher['Voucher']['number_use_by_user'] == 0)
                return true;
            
            //Tout est ok, droit d'utiliser le coupon
            return true;
        }
		
		 /**
         * Le client peut-t-il voir ce coupon
         *
         * @param string    $code           Le code du coupon
         * @param int       $product_id     L'id du produit
         * @return bool
         */
        public function rightToUseUser($code){
            if(empty($code))
                return false;
			
            //Le coupon est valide ?
            if(!$this->existAndValidVoucher($code))
                return false;

            //On récupère le coupon
            $voucher = $this->find('first', array(
                'fields'        => array('Voucher.population', 'Voucher.number_use', 'Voucher.product_ids','Voucher.country_ids','Voucher.number_use_by_user','Voucher.buy_only'),
                'conditions'    => array('Voucher.code' => $code),
                'recursive'     => -1
            ));

			//Normalement n'est pas vide, existAndValidVoucher fait la vérification
            if(empty($voucher))
                return false;
			
			 //Utilisation limitée à un nombre d'utilisation par utilisateur
            if($voucher['Voucher']['number_use'] != 0){
                App::import('Model', 'VoucherHistory');
                $voucherHistory = new VoucherHistory();
                //Le nombre d'utilisation
                $numberUse = $voucherHistory->getNumberUse($code);

                //Si problème, ou si utilisation >= à la limite
                if($numberUse === false || $numberUse >= $voucher['Voucher']['number_use'])
                    return false;
            }

            /* Valable sur quelques produits ou tous */
            if (isset($voucher['Voucher']['product_ids']))
                $product_ids = explode(",", $voucher['Voucher']['product_ids']);
            else $product_ids = array();

            if ($voucher['Voucher']['product_ids'] != 'all' && !in_array($product_id, $product_ids) && !$voucher['Voucher']['buy_only'])
                return false;

            /* Valable sur quelques pays ou tous */
            if (isset($voucher['Voucher']['country_ids']))
                $country_ids = explode(",", $voucher['Voucher']['country_ids']);
            else $country_ids = array();

            if ($voucher['Voucher']['country_ids'] != 'all' && !in_array(CakeSession::read('Config.id_country'), $country_ids))
                return false;


            //Utilisation pour tout le monde et illimité
            if(empty($voucher['Voucher']['population']) && $voucher['Voucher']['number_use'] == 0 && $voucher['Voucher']['number_use_by_user'] == 0)
                return true;
            
            //Tout est ok, droit d'utiliser le coupon
            return true;
        }

    }
