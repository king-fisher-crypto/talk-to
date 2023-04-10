<?php
    App::uses('AppModel', 'Model');
    /**
     * Product Model
     *
     * @property ProductLang $ProductLang
     */
    class Product extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * hasOne associations
         *
         * @var array
         */
        public $belongsTo = array(
            'Country' => array(
                'className' => 'Country',
                'foreignKey' => 'country_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        /**
         * hasMany associations
         *
         * @var array
         */
        public $hasMany = array(
            'ProductLang' => array(
                'className' => 'ProductLang',
                'foreignKey' => 'product_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        public function beforeSave($options = array()){
            parent::beforeSave();
        }
    }
