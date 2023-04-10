<?php
    App::uses('AppModel', 'Model');
    /**
     * Horoscope Model
     *
     */
    class Horoscope extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * hasMany associations
         *
         * @var array
         */
        public $hasMany = array(
            'HoroscopeLang' => array(
                'className' => 'HoroscopeLang',
                'foreignKey' => 'horoscope_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        public function beforeSave($options = array()){

            parent::beforeSave();
        }

        public function hasHoroscope($date, $idSign){
            if(empty($date) || empty($idSign) || !is_numeric($idSign))
                return true;

            //On récupère les id des horoscopes qui ont cette date
            $horoscopes = $this->find('list', array(
                'fiels' => 'id',
                'conditions' => array('date_publication' => $date),
                'recursive' => -1
            ));

            //Si pas d'horoscope pour cette date
            if(empty($horoscopes))
                return false;

            //Maintenant on regarde s'il y a un horoscope pour le signe en question
            $data = $this->HoroscopeLang->find('first', array(
                'conditions' => array('horoscope_id' => $horoscopes, 'sign_id' => $idSign),
                'recursive' => -1
            ));

            //Si pas d'horoscope pour ce signe pour cette date
            if(empty($data))
                return false;

            return true;
        }

        public function getHoroscopeId($date){
            if(empty($date))
                return false;

            $horo = $this->find('first', array(
                'fields'        => array('Horoscope.id'),
                'conditions'    => array('Horoscope.date_publication' => $date),
                'recursive'     => -1
            ));

            if(empty($horo))
                return false;

            $this->id = $horo['Horoscope']['id'];
            return $horo['Horoscope']['id'];
        }
    }
