<?php
App::uses('AppController', 'Controller');
App::uses('AlertsController', 'Controller');

class ApiController extends Controller {
    private $api_errors = array(
        '10' =>  'Le controlleur n\'existe pas',
        '11' =>  'L\'action demandée n\'existe pas dans le controlleur',
        '12' =>  'Reponse mal-formee. Arret',
        '13' =>  'Parametre manquant dans l\'appel',
        '14' =>  'Client introuvable',
        '15' =>  'Agent introuvable',
        '16' =>  'Statut invalide',
        '17' =>  'Erreur changement valeur',
        '18' =>  'Le compte est déjà dans ce statut',
        '19' =>  'Client ou Agent en consultation',
        '21' =>  'Aucune consultation en cours',
        '22' =>  'Code personnel du client non transmis dans la requete',
        '23' =>  'Code agent non transmis dans la requete',
        '24' =>  'Numero de telephone client non transmis dans la requete',
        '25' =>  'Numero called_number non transmis',
        '26' =>  'Timestamp manquant',
        '27' =>  'Le timestamp endconsult doit etre superieur au timestamp startconsult',
        '28' =>  'Le client n\'a pas assez de crédit sur son compte',
		'29' =>  'Numero sessionid non transmis'
    );
    private $api_method_parms = array(
        //'get-customer-getcredit'       		=>  array('sessionid','cust_personal_code', 'timestamp'),
		'post-customer-getcredit'       	=>  array('sessionid','cust_personal_code', 'timestamp'),
		'post-customer-callinfo'        	=>  array('sessionid','line','callerid','called_number','mob_info', 'timestamp'),
        'post-customer-startconsult'   		=>  array('sessionid','cust_personal_code', 'agent_number', 'phone_number', 'called_number', 'timestamp'),
        'post-customer-endconsult'     		=>  array('sessionid','cust_personal_code','timestamp'),
        'post-customer-setnewsmsalert'      =>  array('sessionid','cust_personal_code','agent_number','cust_mobilephone_number','timestamp'),
        //'get-agents-getstatus'         	=>  array('sessionid','agent_number','timestamp'),
		'post-agents-showstatus'         	=>  array('sessionid','agent_number','active','connected','timestamp'),
		'post-agents-getstatus'         	=>  array('sessionid','agent_number','timestamp'),
        'post-agents-setstatus'         	=>  array('sessionid','agent_number','statut','timestamp','accepted','reason'),
        'post-agents-setaudiotelconsult'    =>  array('agent_number','phone_number', 'called_number', 'timestamp_start', 'timestamp_end', 'sessionid'),
		'post-customer-callstop'         	=>  array('sessionid','timestamp','hungupby','usagetime','usagecost','commtime','commcost','routetime','routecost','revenue','sms','customercli'),
		'post-agents-agentalert'         	=>  array('agent','answer')
    );
    private $api_method_ready = array();
    private $api_controllers = array();
    private $api_controllers_methods = array();
    
    public function isApiMethodsReady()
    {
        foreach ($this->api_method_parms AS $k => $p){
            $tmp = explode("-",$k);
            $errors = 0;
            if (App::import('Controller', $tmp['1'])){
                $className = ucfirst(strtolower($tmp['1'])).'Controller';
                $obj = new $className();
                $action = '__api_'.$tmp['2'];
                $this->api_method_ready[$tmp['0'].'-'.$tmp['1'].'-'.$tmp['2']] = (method_exists($obj, $action))?true:false;
            }else{
                $this->api_method_ready[$tmp['0'].'-'.$tmp['1'].'-'.$tmp['2']] = false;
            }
        }
        return $this->api_method_ready;
    }
    
    public function beforeFilter()
    {
        $this->autoRender = false;
        $this->initApi();
    }
    private function initApi()
    {
		if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] == '0.0.0.0'){
			header("HTTP/1.0 500 Internal Server Error");
			die();
		}
        foreach ($this->api_method_parms AS $k => $v){
            $k = explode("-", $k);
            if (!in_array($k['1'], $this->api_controllers))
                $this->api_controllers[] = $k['1'];
            $this->api_controllers_methods[$k['1']][] = $k['2'];
        }
    }
    private function keepOnlyAskedParms()
    {

        $controller = isset($this->request->params['pass']['0'])?$this->request->params['pass']['0']:false;
        $action = isset($this->request->params['pass']['1'])?$this->request->params['pass']['1']:false;
        if (!$action || !$controller)return false;

        /* On recherche si on doit récupérer un get ou un post pour ce controller/action */
        $method = false;
        if (isset($this->api_method_parms['get-'.$controller.'-'.$action]))
            $method = 'get';
        elseif (isset($this->api_method_parms['post-'.$controller.'-'.$action]))
            $method = 'post';
        else
            $this->jsonReturn(999);
            
        $parms = array();

        switch ($method){
            case 'get':
                foreach ($this->api_method_parms[$method.'-'.$controller.'-'.$action] AS $k => $parm){
                    if (isset($this->request->params['pass'][($k+2)]))
                        $parms[$parm] = $this->request->params['pass'][($k+2)];
						
                }
                break;
            case 'post':
                foreach ($this->api_method_parms[$method.'-'.$controller.'-'.$action] AS $parm){
                    if (isset($this->request->data[$parm]))
                        $parms[$parm] = $this->request->data[$parm];
                }
            break;
        }

        return $parms;
    }
    public function callController($controller="", $action="")
    {
        if (!in_array($controller, $this->api_controllers)){
            $this->jsonReturn(999);
        }

        /* On importe le controlleur si existant */
            if (App::import('Controller', $controller) === false){
                $this->jsonReturn(10);
            }

        /* On charge le controlleur */
            $className = ucfirst(strtolower($controller)).'Controller';
            $obj = new $className();
        
        /* On vérifie que la méthode soit gérée */
            if (!in_array($action, $this->api_controllers_methods[$controller]))
                $this->jsonReturn(11);

        /* On vérifie les paramètres et on ne garde que ceux déclarés ci-dessus */
            $parms = $this->keepOnlyAskedParms();

        /* On appelle la méthode demandée */
            $action = '__api_'.$action;
            if (method_exists($obj, $action)){
                $result = call_user_func(array($obj, $action), $parms);
                
                if (!isset($result['response_code']) || !isset($result['response']))
                    $this->jsonReturn(12);
                else
                    $this->jsonReturn($result['response_code'], $result['response']);
            }else{
                $this->jsonReturn(11);   
            }
    }
    private function jsonReturn($response_code=0, $response=array())
    {
        $controller = isset($this->request->params['pass']['0'])?$this->request->params['pass']['0']:false;
        $action = isset($this->request->params['pass']['1'])?$this->request->params['pass']['1']:false;

        $response_message = isset($this->api_errors[$response_code])?$this->api_errors[$response_code]:'Erreur inconnue';
        if ($response_code === 0)$response_message = 'OK';
		$parametre = $this->keepOnlyAskedParms();
        $tmp = array('response_code'    => $response_code,
                     'response_message' => $response_message,
                     'response'         => $response,
                     'request_parms'    => $parametre
        );
		
       // if ($response_code !== 0){
            $res = '';
            foreach ($tmp AS $k => $v)
                if (is_array($v)){
                    $res.= $k.":\n";
                    foreach ($v AS $k2 => $v2)
                        $res.= '      '.$k2.' => '.$v2;
                }else
                $res.= $k.' => '.$v."\n";

            $this->loadModel('Apilog');
            $this->Apilog->create();
            $this->Apilog->save(array(
                'apiname' => $parametre['sessionid'],
                'url'     => '/'.$controller.'/'.$action,
                'result'  => $res,
                'date_add'=> "'".date("Y-m-d H:i:s")."'"
            ));
       // }
		
		/*$msg = '/'.$controller.'/'.$action.' => '.$res;
		$msg = addslashes($msg);
		$dbb_patch = new DATABASE_CONFIG();
		$dbb_connect = $dbb_patch->default;
		$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
		$mysqli_connect->query("INSERT INTO logs(msg, date_add) VALUES ('{$msg}',NOW())");
		*/

        echo json_encode($tmp);
        die();
    }
  
    public function admin_tmp()
    {
        $this->layout = '';
        $this->set('api_method_ready',$this->isApiMethodsReady());
        $this->set('controllers', $this->api_controllers_methods);
        $this->set('parms', $this->api_method_parms);
        $this->render('test');
    }
  
    public function tmp()
    {
        $this->layout = '';
        $this->set('api_method_ready',$this->isApiMethodsReady());
        $this->set('controllers', $this->api_controllers_methods);
        $this->set('parms', $this->api_method_parms);
        $this->render('test');
    }
    public function tmp2()
    {
        $this->layout = '';
        /* On ajoute le changement dans la table d'historique */

        $this->loadModel('UserStateHistory');

            $alerts = new AlertsController();
            $alerts->alertUsersForUserAvailability('1001', 'phone');


    }
}
    