<?php
    App::uses('AppModel', 'Model');
    /**
     * VoucherHistory Model
     *
     */
    class VoucherHistory extends AppModel {

        /**
         * Nombre d'utilisation d'un coupon pour un client
         *
         * @param string    $code       Le code du coupon
         * @param int       $user_id    L'id du client
         * @return bool
         */
        /*
        public function getNumberUse($code, $user_id){
            if(empty($code) || empty($user_id) || !is_numeric($user_id))
                return false;

            //On compte le nombre d'utilisation du coupon par l'utilisateur
            $numberUse = $this->find('count', array(
                'conditions'    => array('VoucherHistory.code' => $code, 'VoucherHistory.user_id' => $user_id),
                'recursive'     => -1
            ));

            return $numberUse;
        }
        */
        /**
         * Nombre d'utilisation d'un coupon pour un client
         *
         * @param string    $code       Le code du coupon
         * @param int       $user_id    L'id du client
         * @return bool
         */
        public function getNumberUse($code){
            if(empty($code))
                return false;

            //On compte le nombre d'utilisation du coupon par l'utilisateur
            $numberUse = $this->find('count', array(
                'conditions'    => array('VoucherHistory.code' => $code),
                'recursive'     => -1
            ));

            return $numberUse;
        }
        public function getNumberUseForCustomer($code='', $user_id=false){
            if(empty($code) || !$user_id)
                return false;

            //On compte le nombre d'utilisation du coupon par l'utilisateur
            $numberUse = $this->find('count', array(
                'conditions'    => array('VoucherHistory.code' => $code, 'user_id' => $user_id),
                'recursive'     => -1
            ));

            return $numberUse;
        }
    }
