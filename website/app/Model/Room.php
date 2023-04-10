<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Countries $Countries
 * @property CategoryUser $CategoryUser
 * @property UserCountry $UserCountry
 * @property UserCreditHistory $UserCreditHistory
 * @property UserLang $UserLang
 * @property Planning $Planning
 * @property Favorite $Favorite
 * @property Message $Message
 */
class Room extends AppModel {
    public $primaryKey = 'id';

    public $validate = array();
    
    public $hasMany = array(
        'RoomInvite' => array(
            'className' => 'RoomInvite',
            'foreignKey' => 'room_id',
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

    public $hasOne = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
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

       public $validate = array(
        'title' => array(
            // 'alphaNumeric' => array(
            //     'rule' => 'alphaNumeric',
            //     'required' => true,
            //     'message' => 'Letters and numbers only'
            // ),
            'between' => array(
                'rule' => array('lengthBetween', 40, 50),
                'message' => 'Between 2 to 50 characters'
            )
        ),
        'slug' => array(
            // 'alphaNumeric' => array(
            //     'rule' => 'alphaNumeric',
            //     'required' => true,
            //     'message' => 'Letters and numbers only'
            // ),
            'characters' => array(
                'rule' => array('custom', '/^[a-z0-9-_]*$/i'),
                'message'  => 'Special characters are not allowed in slug'
            ),
            'between' => array(
                'rule' => array('lengthBetween', 2, 50),
                'message' => 'Between 2 to 50 characters'
            )
        ),
        'no_of_invites' => array(
            'rule' => array('minLength', '1'),
            'message' => 'Invalid no. of invites'
        ),
       // 'email' => 'email',
        'date_start' => array(
            'rule' => 'date',
            'message' => 'Enter a valid date',
            'allowEmpty' => true
        )
    );
}
