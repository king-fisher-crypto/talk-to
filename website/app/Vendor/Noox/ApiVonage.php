<?php

use Vonage\Voice\NCCO\NCCO;

require_once VENDORS.DS."autoload.php";

class  ApiVonage {


    protected $client;

    public function __construct()
    {
        $basic  = new \Vonage\Client\Credentials\Basic(Configure::read('Site.vonage.key'), Configure::read('Site.vonage.secret'));
        $keypair = new \Vonage\Client\Credentials\Keypair(
            file_get_contents(ROOT.DS.'private.key'),
            Configure::read('Site.vonage.application_id')
        );

        $this->client = new \Vonage\Client(new \Vonage\Client\Credentials\Container([$basic, $keypair]));
    }

    public function sendSms($phone, $content)
    {
        $response = $this->client->sms()->send(
            new \Vonage\SMS\Message\SMS($phone, 'agents', $content)
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            return 1;
        } else {
            return 0;
        }
    }


    public function outBoundCall($to, $conversationName)
    {
        //        $url = Configure::read('Site.baseUrlFull').'/webhooks/dtmf';
       $url = 'https://721a-2405-4803-d3e1-4fe0-5dbf-65db-7c40-5f21.ngrok.io/webhooks/dtmf';

        $outboundCall = new \Vonage\Voice\OutboundCall(
            new \Vonage\Voice\Endpoint\Phone($to),
            new \Vonage\Voice\Endpoint\Phone(Configure::read('Site.vonage.number'))
        );
        $input = new \Vonage\Voice\NCCO\Action\Input();
        $input->setEnableDtmf(true);
        $input->setEventWebhook(new \Vonage\Voice\Webhook($url.'?con_name='.$conversationName, 'GET'));
        $ncco = new NCCO();
        $ncco->addAction(new \Vonage\Voice\NCCO\Action\Talk('You have a call. Please press star to be connected'))
            ->addAction($input);

        $outboundCall->setNCCO($ncco);

        $this->client->voice()->createOutboundCall($outboundCall);

    }

    public function connectToUser($userName)
    {
        $userToConnect = new \Vonage\Voice\Endpoint\App($userName);
        $action = new \Vonage\Voice\NCCO\Action\Connect($userToConnect);
        $action->setFrom(Configure::read('Site.vonage.number'));
        $ncco = new \Vonage\Voice\NCCO\NCCO();
        $ncco ->addAction($action);

        return $ncco->toArray();

    }

    public function connectToConference($conversationName)
    {
        $convo = new \Vonage\Voice\NCCO\Action\Conversation($conversationName);
        $ncco = new \Vonage\Voice\NCCO\NCCO();
        $ncco->addAction($convo);

        return $ncco->toArray();
    }

    public function connectToPhone($phone)
    {
        $talk =  new \Vonage\Voice\NCCO\Action\Talk('Please wait while we connect you');
        $userToConnect = new \Vonage\Voice\Endpoint\Phone($phone);
        $action = new \Vonage\Voice\NCCO\Action\Connect($userToConnect);
        $action->setFrom(Configure::read('Site.vonage.number'));
        $ncco = new \Vonage\Voice\NCCO\NCCO();
        $ncco->addAction($talk)
            ->addAction($action);

        return $ncco->toArray();

    }

    /**
     * @param $email
     * @param $name
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Vonage\Client\Exception\Exception
     */
    public function createUser($userName, $displayName ='')
    {
        $jwt = $this->client->generateJwt();
        $client = new GuzzleHttp\Client([
            'verify' => false
        ]);
        $headers = [
            'Authorization' => 'Bearer ' . $jwt,
            'Accept'        => 'application/json',
        ];
        $data = array(
            'name' => $userName,
            'display_name' => $displayName,
        );

          $client->post( 'https://api.nexmo.com/beta/users', [
             'headers' => $headers,
             'json'    => (object) $data
         ]);

    }

    public function getJwt(){
        return $this->client->generateJwt();
    }

    public function getUser($id)
    {
        $jwt = $this->client->generateJwt();
        $client = new GuzzleHttp\Client();
        $headers = [
            'Authorization' => 'Bearer ' . $jwt,
            'Accept'        => 'application/json',
        ];

        return $client->get( 'https://api.nexmo.com/beta/users/'.$id, [
            'headers' => $headers
        ]);

    }

    public function generateJwt($userName)
    {
        $claims = [
            'acl' => [
                'paths' => [
                    '/*/users/**' => (object) [],
                    '/*/conversations/**' => (object) [],
                    '/*/sessions/**' => (object) [],
                    '/*/devices/**' => (object) [],
                    '/*/image/**' => (object) [],
                    '/*/media/**' => (object) [],
                    '/*/applications/**' => (object) [],
                    '/*/push/**' => (object) [],
                    '/*/knocking/**' => (object) [],
                    '/*/legs/**' => (object) [],
                ]
            ],
            'sub' => $userName,
            'application_id' => Configure::read('Site.vonage.application_id')

        ];

        $token = $this->client->generateJwt($claims);

        return (string)$token;
    }

}
