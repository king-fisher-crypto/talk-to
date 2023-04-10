<?php
/**
 * Created by PhpStorm.
 * User: Noox3
 * Date: 11/02/14
 * Time: 16:14
 */

class UserValidation extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'user_validations';

    public $hasOne = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'id',
            'conditions' => array('User.role' => 'agent'),
            'fields' => '',
            'order' => ''
        )
    );

    //Vérifie si l'user à des modifications en attente
    public function enAttente($id){
        $data = $this->find('first', array(
            'conditions' => array('etat' => 0, 'users_id' => $id),
            'fields' => 'id'
        ));

        if(empty($data)) return false;
        else return $data['UserValidation']['id'];
    }

    //Vérifie si l'utilisateur à déjà un champ
    public function hasValidation($id){
        $data = $this->find('first', array(
            'conditions' => array('users_id' => $id),
            'fields' => 'id'
        ));

        if(empty($data)) return false;
        else return $data['UserValidation']['id'];
    }

    public function beforeSave($options = array()){

        parent::beforeSave();
        $fieldsTag = array('pseudo','firstname','lastname','address','postalcode','city','siret','rib','bank_name','bank_country','iban','swift','societe');

        //Supprime les tags HTML
        foreach ($fieldsTag as $field){
            if(isset($this->data['UserValidation'][$field])) $this->data['UserValidation'][$field] = strip_tags($this->data['UserValidation'][$field]);
        }
        return true;
    }

}