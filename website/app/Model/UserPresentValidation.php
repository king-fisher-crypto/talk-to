<?php
/**
 * Created by PhpStorm.
 * User: Noox3
 * Date: 11/02/14
 * Time: 16:14
 */

class UserPresentValidation extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    //public $useTable = 'user_validations';

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
    public function enAttente($id, $idLang){
        if(empty($id) || empty($idLang)) return false;
        $data = $this->find('first', array(
            'conditions' => array('etat' => 0, 'user_id' => $id, 'lang_id' => $idLang),
            'fields' => 'id'
        ));

        if(empty($data)) return false;
        else return $data['UserPresentValidation']['id'];
    }

    //Vérifie s'il y a une présentation pour l'user et l'id lang en paramètre
    public function hasPresentation($user_id, $lang_id){
        $tmp = $this->find('first',array(
            'conditions' => array('user_id' => $user_id, 'lang_id' => $lang_id),
            'recursive' => -1
        ));

        if(empty($tmp))
            return false;
        else
            return $tmp['UserPresentValidation']['id'];
    }

    public function beforeSave($options = array()){

        parent::beforeSave();

        $fieldsTag = array('texte');

        //Supprime les tags HTML
        foreach ($fieldsTag as $field){
            if(isset($this->data['UserPresentValidation'][$field])) $this->data['UserPresentValidation'][$field] = strip_tags($this->data['UserPresentValidation'][$field]);
        }
        return true;
    }
}