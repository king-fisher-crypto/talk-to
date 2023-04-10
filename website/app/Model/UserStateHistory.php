<?php
App::uses('AppModel', 'Model');
/**
 * UserPresentLang Model
 *
 */
class UserStateHistory extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'user_state_history';

    public function beforeSave($options = array()){
        parent::beforeSave();
    }


}
