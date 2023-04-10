<?php
App::uses('AppModel', 'Model');
/**
 * Domain Model
 *
 */
class Domain extends AppModel {
    
    public $hasAndBelongsToMany  = array(
        'Lang' => array(
            'className' => 'Lang',
            'joinTable' => 'domain_langs',
            'foreignKey' => 'domain_id',
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
    
}
