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
class RoomInvite extends AppModel {
    public $primaryKey = 'id';

    public $validate = array();
    

}
