<?php

App::import('Vendor', 'Noox/ApiVonage');

class VoiceCallController extends Controller {

    protected $apiVonage;

    public function __construct($request = null, $response = null)
    {
        $this->apiVonage = new ApiVonage();
        parent::__construct($request, $response);
    }

    public function answer()
    {
        $this->log('Test to : '.$this->request->query('to'));

        $response = $this->apiVonage->connectToUser($this->request->query('to'));
        $this->response($response);
    }

    public function events()
    {
        $data = $this->request->input('json_decode', true);

        $this->log('Test events Vonage : '. $this->request->input());
        $this->response->type('application/json');
        $this->response->send();
        die();
    }

    public function call()
    {
        $requestData = $this->request->data;

         $this->apiVonage->outBoundCall($requestData['to'], $requestData['con_name']);

        $this->response->type('application/json');
        $this->response->body(json_encode([
            'status'    =>  'success'
        ]));

        $this->response->send();
        die;
    }

    public function jwt()
    {
        $requestData = $this->request->data;
        $this->loadModel('User');
        $user = $this->User->findById($requestData['id']);

        if ( empty($user)) {
            die;
        }

        if (!$user['User']['phone_api_use'] && !$user['User']['phone_number']) {
            die;
        }

        $userName = $user['User']['phone_api_use'];
        if (!$userName) {
            $userName = $user['User']['phone_number'];
            $this->apiVonage->createUser($userName, $user['User']['firstname'].' '.$user['User']['lastname']);
            $this->User->updateAll(array('phone_api_use' => $userName), array('User.id' => $user['User']['id']));

        }

        $jwt = $this->apiVonage->generateJwt($userName);

        $this->response([
            'status'    =>  'success',
            'data'      =>  $jwt,
        ]);
    }

    public function dtmf()
    {
//        $jwt = $this->apiVonage->getJwt();
//        $this->response((string)$jwt);


        $this->log('Test dtmf : '. json_encode($this->request->query));

        $response = $this->apiVonage->connectToConference($this->request->query('con_name'));
//        $response = $this->apiVonage->connectToUser('84983819089');
        $this->log('Connect user : '. json_encode($response));


        $this->response->type('application/json');
        $this->response->body(json_encode($response));
        $this->response->send();
        die();
    }

    /**
     * @param $data
     */
    public function response($data)
    {
        $this->response->type('application/json');
        $this->response->body(json_encode($data));
        $this->response->send();
        die;
    }


}