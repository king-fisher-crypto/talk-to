<?php
App::uses('AppModel', 'Model');
/**
 * UserPresentLang Model
 *
 */
class UserPresentLang extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'user_present_lang';

/**
 * Primary key field
 *
 * @var string
 */


    public $hasOne = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'id',
            'conditions' => array('User.role' => 'agent'),
            'fields' => '',
            'order' => ''
        ),
        'Lang' => array(
            'className' => 'Lang',
            'foreignKey' => 'id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );


    public function beforeSave($options = array()){

        parent::beforeSave();

        $fieldsTag = array('texte');

        //Supprime les tags HTML
        foreach ($fieldsTag as $field){
            if(isset($this->data['UserPresentLang'][$field])) $this->data['UserPresentLang'][$field] = strip_tags($this->data['UserPresentLang'][$field]);
        }
        return true;
    }

    public function hasPresentation($user_id, $lang_id){
        $tmp = $this->find('first',array(
            'conditions' => array('user_id' => $user_id, 'lang_id' => $lang_id),
            'recursive' => -1
        ));

        if(empty($tmp))
            return false;
        else
            return true;
    }
}
