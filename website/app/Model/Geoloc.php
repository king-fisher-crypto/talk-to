<?php
App::uses('AppModel', 'Model');
/**
 * Favorite Geoloc
 *
 * @property Geoloc $Geoloc
 */
class Geoloc extends AppModel {


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Domain' => array(
            'className' => 'Domain',

        ),
        'Lang' => array(
            'className' => 'Lang',
            
        )
    );

    public function beforeSave($options = array()){

        parent::beforeSave();
    }
}
