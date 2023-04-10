<?php
App::uses('ExtranetController', 'Controller');
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');

class RoomsController extends ExtranetController {
    protected $myRole = 'client';
    //On charge le model User pour tout le controller
    public $uses = array('Rooms','Users','RoomInvites');
    public $components = array('Paginator');
    public $helpers = array('Paginator','Time');

    public function beforeRender()
    {
        parent::beforeRender();
    }

    public function beforeFilter(){
        parent::beforeFilter();
        $user = $this->Auth->user();
    }
    



    public function index(){
        $userid = $this->Auth->user('id');

        $allrooms = $this->Rooms->find('all', array( 
            'conditions'=> array('or'=> array('Rooms.user_id'=> $userid, 'Rooms.created_by'=> $userid)),
            'recursive'=>-1,
            'fields' => array('Rooms.*, User.firstname', 'count(RoomInvite.id) as totalinvites'),
            'joins' => array(
                        array('table' => 'users',
                            'alias' => 'User',
                            'type' => 'left',
                            'conditions' => array(
                                'User.id = Rooms.user_id'
                            )
                        ),
                        array('table' => 'room_invites',
                            'alias' => 'RoomInvite',
                            'type' => 'left',
                            'conditions' => array(
                                'RoomInvite.room_id = Rooms.id'
                            )
                        )
                    ),
            'group' => array(
                    'Rooms.id'
            ),
        ));

        $user = $this->Users->find('first',array('conditions'=>array('id'=> $userid)));
        $user = isset($user['Users'])?$user['Users']:'';
                  

      
        $this->set(compact('allrooms','userid','user'));
       
    }

    public function add() {
        $getclients = '';
        $getclients = $this->Users->find('list', array(
                                'fields' => array('Users.id', 'Users.firstname'),
                                'conditions' => array('Users.role' => 'client'),
                                'recursive' => 0
        ));
        $getagents = '';
        $getagents = $this->Users->find('list', array(
                                'fields' => array('Users.id', 'Users.firstname'),
                                'conditions' => array('Users.role' => 'agent'),
                                'recursive' => 0
        ));
        
        if($this->request->is('post')){
            $requestData = $this->request->data;
            $currentUserId = $this->Auth->user('id');
            $invitedUsers = $requestData['Rooms']['invited_users'];
            $getTotalInvites = (int)$requestData['Rooms']['no_of_invites'];
            if(count($invitedUsers) > $getTotalInvites) {

                $this->Session->setFlash(__('You cant invite more than number of invites'), 'flash_error');
            } else {
            $roomSlug = $requestData['Rooms']['slug'];
            $isRoomSlugUnique = $this->Rooms->find('first',['conditions'=>['Rooms.slug'=> $roomSlug]]);
            if(isset($isRoomSlugUnique['Rooms']['id'])) {
                $this->Session->setFlash(__('Room slug is not available'), 'flash_error');
            } else {    
                unset($requestData['Rooms']['invited_users']);
                if($requestData['Rooms']['role']==1) {
                    unset($requestData['Rooms']['role']);
                    $requestData['Rooms']['user_id'] = $currentUserId;
                }

                $requestData['Rooms']['created_by'] = $currentUserId;
                $getAllUsers = $this->Users->find('all', array(
                            'conditions' => array(
                                "Users.id IN" => $invitedUsers
                            ),'fields' => array('Users.id, Users.firstname, Users.email')
                        ));
               

                //API CODE STARTS
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://192.168.2.17:3000/createRoom',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "display_name": "'.$roomSlug.'",
                        "join_approval_level": "explicit_approval"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                $responseApi = @json_decode($response);
                $apiResponseUrl ='';
                $apiResponseGuestUrl ='';
                if($responseApi && $responseApi->data->_links->guest_url->href) {
                    $apiResponseUrl = $responseApi->data->_links->host_url->href;
                    $apiResponseGuestUrl = $responseApi->data->_links->guest_url->href;
                }
                //API CODE ENDS 
                if($apiResponseUrl!='') {
                $requestData['Rooms']['room_url'] = $apiResponseUrl;

                $getRoomData = $this->Rooms->save($requestData);
                if($getRoomData){
                    $roomId = $getRoomData['Rooms']['id'];

                    $inviteData = array();

                    if(count($invitedUsers) > 0) {
                        foreach($invitedUsers as $k => $v) {
                            $arData = array();
                            $arData['room_id'] = $roomId;
                            $arData['user_id'] = $v;
                            $arData['invited_by'] = $currentUserId;
                            $arData['status'] = 0;

                            $inviteData[] = $arData;
                        }
                    }

                    if(count($inviteData) > 0) {
                        $saveManyData =  $this->RoomInvites->saveMany($inviteData);
                        $roomLink = Router::url('/', true);
                        $roomLink = $roomLink.'rooms/room/'.$roomId;
                        foreach($getAllUsers as $k=> $v) {
                            $email = $v['Users']['email'];
                            $this->sendCmsTemplateByMail(473, $this->Session->read('Config.id_lang'), $email, array(
                            'EMAIL_ADDRESS' => $email,
                            'LIEN_PWD_REINIT' => $apiResponseUrl,
                            'ROOM_LINK'=> $roomLink
                            ));
                        }

                    }
                          
                        $this->Session->setFlash(__('Room created successfully'), 'flash_success');
                    
                        $this->redirect(array('controller' => 'rooms', 'action' =>'index'));
                }else {
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde de vos données'), 'flash_error');
                }
            } else {
                $this->Session->setFlash(__('Erreur lors de la sauvegarde de vos données'), 'flash_error');
            }

            }
            }

        }
              
        $this->set(compact('getclients','getagents'));
    }

    public function getusers() {

    }

    public function room($getRoomId)
    {
        if((int)$getRoomId > 0) {
            $userid = $this->Auth->user('id');
            $room = $this->Rooms->find('all', array('conditions'=>array('Rooms.id'=> $getRoomId, 'or'=>array('Rooms.created_by'=> $userid, 'Rooms.user_id'=> $userid))));
            if($room) {
                $getclients = '';
                $this->set(compact('room','userid'));
            }
        }
    }

    public function invite($getRoomId)
    {
        if((int)$getRoomId > 0) {
            $userid = $this->Auth->user('id');
            $room = $this->Rooms->find('all', array('conditions'=>array('Rooms.id'=> $getRoomId, 'or'=>array('Rooms.created_by'=> $userid, 'Rooms.user_id'=> $userid))));
            if($room) {
                $getclients = '';
                $getclients = $this->Users->find('list', array(
                                        'fields' => array('Users.id', 'Users.firstname'),
                                        'conditions' => array('Users.role' => 'agent'),
                                        'recursive' => 0
                ));
                $allinvites = $this->RoomInvites->find('all',array('conditions'=> array('RoomInvites.room_id'=> $getRoomId),
                    'fields' => array('RoomInvites.*, User.firstname'),
                    'joins' => array(
                        array('table' => 'users',
                            'alias' => 'User',
                            'type' => 'left',
                            'conditions' => array(
                                'User.id = RoomInvites.user_id'
                            )
                        )
                    )

                ));
                if($this->request->is('post')){
                    $requestData = $this->request->data;
                    $roomData = $room[0]['Rooms'];
                          
                    $currentUserId = $this->Auth->user('id');
                    $invitedUsers = $requestData['Rooms']['invited_users'];
                    $getTotalInvites = (int)$roomData['no_of_invites'];
                          
                    if((count($invitedUsers)+count($allinvites)) > $getTotalInvites) {
                        $this->Session->setFlash(__('You cant invite more than number of invites'), 'flash_error');
                    } else {
                    unset($requestData['Rooms']['invited_users']);
                    $getAllUsers = $this->Users->find('all', array(
                            'conditions' => array(
                                "Users.id IN" => $invitedUsers
                            ),'fields' => array('Users.id, Users.firstname, Users.email')
                        ));


                        $inviteData = array();
                        if(count($invitedUsers) > 0) {
                            foreach($invitedUsers as $k => $v) {
                                $arData = array();
                                $arData['room_id'] = $getRoomId;
                                $arData['user_id'] = $v;
                                $arData['invited_by'] = $currentUserId;
                                $arData['status'] = 0;
                                $inviteData[] = $arData;
                            }
                        }
                        $saveManyData =  $this->RoomInvites->saveMany($inviteData);

                        if($saveManyData) {
                            $roomLink = Router::url('/', true);
                            $roomLink = $roomLink.'rooms/room/'.$getRoomId;
                            $apiResponseUrl  = $roomData['room_url'];
                            foreach($getAllUsers as $k=> $v) {
                                $email = $v['Users']['email'];
                                $this->sendCmsTemplateByMail(473, $this->Session->read('Config.id_lang'), $email, array(
                                'EMAIL_ADDRESS' => $email,
                                'LIEN_PWD_REINIT' => $apiResponseUrl,
                                'ROOM_LINK'=> $roomLink
                                ));

                            }

                        }
                            $this->Session->setFlash(__('Member added successfully'), 'flash_success');
                        
                            $this->redirect(array('controller' => 'rooms', 'action' =>'index'));
                    
                    }
                }

              
            $this->set(compact('room','allinvites','userid','getclients'));
            }
        }
    }

    
    public function remove($idRoom){
        $getRoom  = $this->Rooms->find('all',  array(
                'conditions'    => array('created_by' => $this->Auth->user('id'), 'id' => $idRoom),
            ));//->get();
        if( isset($getRoom[0]['Rooms']['id'])) {
            $this->Rooms->deleteAll(array('Rooms.id' => $getRoom[0]['Rooms']['id']), false);
            $this->RoomInvites->deleteAll(array('RoomInvites.room_id' =>  $getRoom[0]['Rooms']['id']), false);
            $this->Session->setFlash(__('Room deleted successfully'), 'flash_success');

             //$getRoom =  $getRoom->get();
        } else {
            $this->Session->setFlash(__('You do not have permission to delete this room'), 'flash_error');
        }

                
        $this->redirect(array('controller' => 'rooms', 'action' =>'index'));
    }


 
    
}
