<?php
App::uses('AppModel', 'Model');
/**
 * Review Model
 *
 * @property Review $review
 */
class Review extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
    public $belongsTo = array(
        'Agent' => array(
            'className' => 'User',
            'foreignKey' => 'agent_id',
            'conditions' => '',
            'fields' => array('Agent.id','Agent.pseudo'),
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id','User.firstname','User.sexe'),
            'order' => ''
        )
    );

    public function beforeSave($options = array()){

        parent::beforeSave();
        $fieldsTag = array('content');

        //Supprime les tags HTML
        foreach ($fieldsTag as $field){
            if(isset($this->data['Review'][$field])) $this->data['Review'][$field] = strip_tags($this->data['Review'][$field]);
        }
        return true;
    }
}
