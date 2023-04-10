<?php
    App::uses('AppModel', 'Model');
    /**
     * HoroscopeLang Model
     *
     */
    class HoroscopeLang extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'Horoscope' => array(
                'className' => 'Horoscope',
                'foreignKey' => 'horoscope_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        public function beforeSave($options = array()){

            parent::beforeSave();
        }
    }
