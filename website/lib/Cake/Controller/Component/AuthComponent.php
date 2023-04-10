<?php
/**
 * Authentication component
 *
 * Manages user logins and permissions.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller.Component
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Component', 'Controller');
App::uses('Router', 'Routing');
App::uses('Security', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeSession', 'Model/Datasource');
App::uses('BaseAuthorize', 'Controller/Component/Auth');
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

/**
 * Authentication control component class
 *
 * Binds access control with user authentication and session management.
 *
 * @package       Cake.Controller.Component
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html
 */
class AuthComponent extends Component {

/**
 * Constant for 'all'
 */
	const ALL = 'all';

/**
 * Other components utilized by AuthComponent
 *
 * @var array
 */
	public $components = array('Session', 'RequestHandler');

/**
 * An array of authentication objects to use for authenticating users. You can configure
 * multiple adapters and they will be checked sequentially when users are identified.
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'Form' => array(
 *			'userModel' => 'Users.User'
 *		)
 *	);
 * }}}
 *
 * Using the class name without 'Authenticate' as the key, you can pass in an array of settings for each
 * authentication object. Additionally you can define settings that should be set to all authentications objects
 * using the 'all' key:
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'all' => array(
 *			'userModel' => 'Users.User',
 *			'scope' => array('User.active' => 1)
 *		),
 *		'Form',
 *		'Basic'
 *	);
 * }}}
 *
 * You can also use AuthComponent::ALL instead of the string 'all'.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html
 */
	public $authenticate = array('Form');

/**
 * Objects that will be used for authentication checks.
 *
 * @var array
 */
	protected $_authenticateObjects = array();

/**
 * An array of authorization objects to use for authorizing users. You can configure
 * multiple adapters and they will be checked sequentially when authorization checks are done.
 *
 * {{{
 *	$this->Auth->authorize = array(
 *		'Crud' => array(
 *			'actionPath' => 'controllers/'
 *		)
 *	);
 * }}}
 *
 * Using the class name without 'Authorize' as the key, you can pass in an array of settings for each
 * authorization object. Additionally you can define settings that should be set to all authorization objects
 * using the 'all' key:
 *
 * {{{
 *	$this->Auth->authorize = array(
 *		'all' => array(
 *			'actionPath' => 'controllers/'
 *		),
 *		'Crud',
 *		'CustomAuth'
 *	);
 * }}}
 *
 * You can also use AuthComponent::ALL instead of the string 'all'
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#authorization
 */
	public $authorize = false;

/**
 * Objects that will be used for authorization checks.
 *
 * @var array
 */
	protected $_authorizeObjects = array();

/**
 * The name of an optional view element to render when an Ajax request is made
 * with an invalid or expired session
 *
 * @var string
 */
	public $ajaxLogin = null;

/**
 * Settings to use when Auth needs to do a flash message with SessionComponent::setFlash().
 * Available keys are:
 *
 * - `element` - The element to use, defaults to 'default'.
 * - `key` - The key to use, defaults to 'auth'
 * - `params` - The array of additional params to use, defaults to array()
 *
 * @var array
 */
	public $flash = array(
		'element' => 'default',
		'key' => 'auth',
		'params' => array()
	);

/**
 * The session key name where the record of the current user is stored. Default
 * key is "Auth.User". If you are using only stateless authenticators set this
 * to false to ensure session is not started.
 *
 * @var string
 */
	public static $sessionKey = 'Auth.User';

/**
 * The current user, used for stateless authentication when
 * sessions are not available.
 *
 * @var array
 */
	protected static $_user = array();

/**
 * A URL (defined as a string or array) to the controller action that handles
 * logins. Defaults to `/users/login`.
 *
 * @var mixed
 */
	public $loginAction = array(
		'controller' => 'users',
		'action' => 'login',
		'plugin' => null
	);

/**
 * Normally, if a user is redirected to the $loginAction page, the location they
 * were redirected from will be stored in the session so that they can be
 * redirected back after a successful login. If this session value is not
 * set, redirectUrl() method will return the URL specified in $loginRedirect.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#AuthComponent::$loginRedirect
 */
	public $loginRedirect = null;

/**
 * The default action to redirect to after the user is logged out. While AuthComponent does
 * not handle post-logout redirection, a redirect URL will be returned from AuthComponent::logout().
 * Defaults to AuthComponent::$loginAction.
 *
 * @var mixed
 * @see AuthComponent::$loginAction
 * @see AuthComponent::logout()
 */
	public $logoutRedirect = null;

/**
 * Error to display when user attempts to access an object or action to which they do not have
 * access.
 *
 * @var string|boolean Error message or boolean false to suppress flash message
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#AuthComponent::$authError
 */
	public $authError = null;

/**
 * Controls handling of unauthorized access.
 * - For default value `true` unauthorized user is redirected to the referrer URL
 *   or AuthComponent::$loginRedirect or '/'.
 * - If set to a string or array the value is used as a URL to redirect to.
 * - If set to false a ForbiddenException exception is thrown instead of redirecting.
 *
 * @var mixed
 */
	public $unauthorizedRedirect = true;

/**
 * Controller actions for which user validation is not required.
 *
 * @var array
 * @see AuthComponent::allow()
 */
	public $allowedActions = array();

/**
 * Request object
 *
 * @var CakeRequest
 */
	public $request;

/**
 * Response object
 *
 * @var CakeResponse
 */
	public $response;

/**
 * Method list for bound controller.
 *
 * @var array
 */
	protected $_methods = array();

/**
 * Initializes AuthComponent for use in the controller.
 *
 * @param Controller $controller A reference to the instantiating controller object
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->request = $controller->request;
		$this->response = $controller->response;
		$this->_methods = $controller->methods;

		if (Configure::read('debug') > 0) {
			Debugger::checkSecurityKeys();
		}
	}

/**
 * Main execution method. Handles redirecting of invalid users, and processing
 * of login form data.
 *
 * @param Controller $controller A reference to the instantiating controller object
 * @return boolean
 */
	public function startup(Controller $controller) {
		$methods = array_flip(array_map('strtolower', $controller->methods));
		$action = strtolower($controller->request->params['action']);

		$isMissingAction = (
			$controller->scaffold === false &&
			!isset($methods[$action])
		);

		if ($isMissingAction) {
			return true;
		}

		if (!$this->_setDefaults()) {
			return false;
		}

		if ($this->_isAllowed($controller)) {
			return true;
		}

		if (!$this->_getUser()) {
			return $this->_unauthenticated($controller);
		}

		if ($this->_isLoginAction($controller) ||
			empty($this->authorize) ||
			$this->isAuthorized($this->user())
		) {
			return true;
		}

		return $this->_unauthorized($controller);
	}

/**
 * Checks whether current action is accessible without authentication.
 *
 * @param Controller $controller A reference to the instantiating controller object
 * @return boolean True if action is accessible without authentication else false
 */
	protected function _isAllowed(Controller $controller) {
		$action = strtolower($controller->request->params['action']);
		if (in_array($action, array_map('strtolower', $this->allowedActions))) {
			return true;
		}
		return false;
	}

/**
 * Handles unauthenticated access attempt. First the `unathenticated()` method
 * of the last authenticator in the chain will be called. The authenticator can
 * handle sending response or redirection as appropriate and return `true` to
 * indicate no furthur action is necessary. If authenticator returns null this
 * method redirects user to login action. If it's an ajax request and
 * $ajaxLogin is specified that element is rendered else a 403 http status code
 * is returned.
 *
 * @param Controller $controller A reference to the controller object.
 * @return boolean True if current action is login action else false.
 */
	protected function _unauthenticated(Controller $controller) {
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$auth = $this->_authenticateObjects[count($this->_authenticateObjects) - 1];
		if ($auth->unauthenticated($this->request, $this->response)) {
			return false;
		}

		if ($this->_isLoginAction($controller)) {
			if (empty($controller->request->data)) {
				if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
			}
			return true;
		}

		if (!$controller->request->is('ajax')) {
			$this->flash($this->authError);
			$this->Session->write('Auth.redirect', $controller->request->here(false));
			$controller->redirect($this->loginAction);
			return false;
		}
		if (!empty($this->ajaxLogin)) {
			$controller->viewPath = 'Elements';
			echo $controller->render($this->ajaxLogin, $this->RequestHandler->ajaxLayout);
			$this->_stop();
			return false;
		}
		$controller->redirect(null, 403);
		return false;
	}

/**
 * Normalizes $loginAction and checks if current request URL is same as login action.
 *
 * @param Controller $controller A reference to the controller object.
 * @return boolean True if current action is login action else false.
 */
	protected function _isLoginAction(Controller $controller) {
		$url = '';
		if (isset($controller->request->url)) {
			$url = $controller->request->url;
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);

		return $loginAction === $url;
	}

/**
 * Handle unauthorized access attempt
 *
 * @param Controller $controller A reference to the controller object
 * @return boolean Returns false
 * @throws ForbiddenException
 * @see AuthComponent::$unauthorizedRedirect
 */
	protected function _unauthorized(Controller $controller) {
		if ($this->unauthorizedRedirect === false) {
			throw new ForbiddenException($this->authError);
		}

		$this->flash($this->authError);
		if ($this->unauthorizedRedirect === true) {
			$default = '/';
			if (!empty($this->loginRedirect)) {
				$default = $this->loginRedirect;
			}
			$url = $controller->referer($default, true);
		} else {
			$url = $this->unauthorizedRedirect;
		}
		$controller->redirect($url, null, true);
		return false;
	}

/**
 * Attempts to introspect the correct values for object properties.
 *
 * @return boolean True
 */
	protected function _setDefaults() {
		$defaults = array(
			'logoutRedirect' => $this->loginAction,
			'authError' => __d('cake', 'You are not authorized to access that location.')
		);
		foreach ($defaults as $key => $value) {
			if (!isset($this->{$key}) || $this->{$key} === true) {
				$this->{$key} = $value;
			}
		}
		return true;
	}

/**
 * Check if the provided user is authorized for the request.
 *
 * Uses the configured Authorization adapters to check whether or not a user is authorized.
 * Each adapter will be checked in sequence, if any of them return true, then the user will
 * be authorized for the request.
 *
 * @param array $user The user to check the authorization of. If empty the user in the session will be used.
 * @param CakeRequest $request The request to authenticate for. If empty, the current request will be used.
 * @return boolean True if $user is authorized, otherwise false
 */
	public function isAuthorized($user = null, CakeRequest $request = null) {
		if (empty($user) && !$this->user()) {
			return false;
		}
		if (empty($user)) {
			$user = $this->user();
		}
		if (empty($request)) {
			$request = $this->request;
		}
		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}
		foreach ($this->_authorizeObjects as $authorizer) {
			if ($authorizer->authorize($user, $request) === true) {
				return true;
			}
		}
		return false;
	}

/**
 * Loads the authorization objects configured.
 *
 * @return mixed Either null when authorize is empty, or the loaded authorization objects.
 * @throws CakeException
 */
	public function constructAuthorize() {
		if (empty($this->authorize)) {
			return;
		}
		$this->_authorizeObjects = array();
		$config = Hash::normalize((array)$this->authorize);
		$global = array();
		if (isset($config[AuthComponent::ALL])) {
			$global = $config[AuthComponent::ALL];
			unset($config[AuthComponent::ALL]);
		}
		foreach ($config as $class => $settings) {
			list($plugin, $class) = pluginSplit($class, true);
			$className = $class . 'Authorize';
			App::uses($className, $plugin . 'Controller/Component/Auth');
			if (!class_exists($className)) {
				throw new CakeException(__d('cake_dev', 'Authorization adapter "%s" was not found.', $class));
			}
			if (!method_exists($className, 'authorize')) {
				throw new CakeException(__d('cake_dev', 'Authorization objects must implement an %s method.', 'authorize()'));
			}
			$settings = array_merge($global, (array)$settings);
			$this->_authorizeObjects[] = new $className($this->_Collection, $settings);
		}
		return $this->_authorizeObjects;
	}

/**
 * Takes a list of actions in the current controller for which authentication is not required, or
 * no parameters to allow all actions.
 *
 * You can use allow with either an array, or var args.
 *
 * `$this->Auth->allow(array('edit', 'add'));` or
 * `$this->Auth->allow('edit', 'add');` or
 * `$this->Auth->allow();` to allow all actions
 *
 * @param string|array $action,... Controller action name or array of actions
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#making-actions-public
 */
	public function allow($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = $this->_methods;
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		$this->allowedActions = array_merge($this->allowedActions, $args);
	}

/**
 * Removes items from the list of allowed/no authentication required actions.
 *
 * You can use deny with either an array, or var args.
 *
 * `$this->Auth->deny(array('edit', 'add'));` or
 * `$this->Auth->deny('edit', 'add');` or
 * `$this->Auth->deny();` to remove all items from the allowed list
 *
 * @param string|array $action,... Controller action name or array of actions
 * @return void
 * @see AuthComponent::allow()
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#making-actions-require-authorization
 */
	public function deny($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = array();
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		foreach ($args as $arg) {
			$i = array_search($arg, $this->allowedActions);
			if (is_int($i)) {
				unset($this->allowedActions[$i]);
			}
		}
		$this->allowedActions = array_values($this->allowedActions);
	}

/**
 * Maps action names to CRUD operations.
 *
 * Used for controller-based authentication. Make sure
 * to configure the authorize property before calling this method. As it delegates $map to all the
 * attached authorize objects.
 *
 * @param array $map Actions to map
 * @return void
 * @see BaseAuthorize::mapActions()
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#mapping-actions-when-using-crudauthorize
 */
	public function mapActions($map = array()) {
		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}
		foreach ($this->_authorizeObjects as $auth) {
			$auth->mapActions($map);
		}
	}

/**
 * Log a user in.
 *
 * If a $user is provided that data will be stored as the logged in user. If `$user` is empty or not
 * specified, the request will be used to identify a user. If the identification was successful,
 * the user record is written to the session key specified in AuthComponent::$sessionKey. Logging in
 * will also change the session id in order to help mitigate session replays.
 *
 * @param array $user Either an array of user data, or null to identify a user using the current request.
 * @return boolean True on login success, false on failure
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#identifying-users-and-logging-them-in
 */
	public function login($user = null) {
	    
	  
	    
		$this->_setDefaults();

		if (empty($user)) {
		   // echo"<br>this->identify";exit;
			$user = $this->identify($this->request, $this->response);
			
		}
		if ($user) {
			$this->Session->renew();
			$this->Session->write(self::$sessionKey, $user);
		}
		
		//var_dump($this->loggedIn()); exit;
		
		return $this->loggedIn();
	}

/**
 * Log a user out.
 *
 * Returns the logout action to redirect to. Triggers the logout() method of
 * all the authenticate objects, so they can perform custom logout logic.
 * AuthComponent will remove the session data, so there is no need to do that
 * in an authentication object. Logging out will also renew the session id.
 * This helps mitigate issues with session replays.
 *
 * @return string AuthComponent::$logoutRedirect
 * @see AuthComponent::$logoutRedirect
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#logging-users-out
 */
	public function logout() {
		$this->_setDefaults();
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$user = $this->user();
		foreach ($this->_authenticateObjects as $auth) {
			$auth->logout($user);
		}
		$this->Session->delete(self::$sessionKey);
		$this->Session->delete('Auth.redirect');
		$this->Session->renew();
		return Router::normalize($this->logoutRedirect);
	}

/**
 * Get the current user.
 *
 * Will prefer the static user cache over sessions. The static user
 * cache is primarily used for stateless authentication. For stateful authentication,
 * cookies + sessions will be used.
 *
 * @param string $key field to retrieve. Leave null to get entire User record
 * @return mixed User record. or null if no user is logged in.
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#accessing-the-logged-in-user
 */
	public static function user($key = null) {
		if (!empty(self::$_user)) {
			$user = self::$_user;
		} elseif (self::$sessionKey && CakeSession::check(self::$sessionKey)) {
			$user = CakeSession::read(self::$sessionKey);
		} else {
			return null;
		}
		if ($key === null) {
			return $user;
		}
		return Hash::get($user, $key);
	}

/**
 * Similar to AuthComponent::user() except if the session user cannot be found, connected authentication
 * objects will have their getUser() methods called. This lets stateless authentication methods function correctly.
 *
 * @return boolean true if a user can be found, false if one cannot.
 */
	protected function _getUser() {
		$user = $this->user();
		if ($user) {
			$this->Session->delete('Auth.redirect');
			return true;
		}

		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		foreach ($this->_authenticateObjects as $auth) {
			$result = $auth->getUser($this->request);
			if (!empty($result) && is_array($result)) {
				self::$_user = $result;
				return true;
			}
		}

		return false;
	}

/**
 * Backwards compatible alias for AuthComponent::redirectUrl().
 *
 * @param string|array $url Optional URL to write as the login redirect URL.
 * @return string Redirect URL
 * @deprecated 2.3 Use AuthComponent::redirectUrl() instead
 */
	public function redirect($url = null) {
		return $this->redirectUrl($url);
	}

/**
 * Get the URL a user should be redirected to upon login.
 *
 * Pass a URL in to set the destination a user should be redirected to upon
 * logging in.
 *
 * If no parameter is passed, gets the authentication redirect URL. The URL
 * returned is as per following rules:
 *
 *  - Returns the normalized URL from session Auth.redirect value if it is
 *    present and for the same domain the current app is running on.
 *  - If there is no session value and there is a $loginRedirect, the $loginRedirect
 *    value is returned.
 *  - If there is no session and no $loginRedirect, / is returned.
 *
 * @param string|array $url Optional URL to write as the login redirect URL.
 * @return string Redirect URL
 */
	public function redirectUrl($url = null) {
		if ($url !== null) {
			$redir = $url;
			$this->Session->write('Auth.redirect', $redir);
		} elseif ($this->Session->check('Auth.redirect')) {
			$redir = $this->Session->read('Auth.redirect');
			$this->Session->delete('Auth.redirect');

			if (Router::normalize($redir) == Router::normalize($this->loginAction)) {
				$redir = $this->loginRedirect;
			}
		} elseif ($this->loginRedirect) {
			$redir = $this->loginRedirect;
		} else {
			$redir = '/';
		}
		if (is_array($redir)) {
			return Router::url($redir + array('base' => false));
		}
		return $redir;
	}

/**
 * Use the configured authentication adapters, and attempt to identify the user
 * by credentials contained in $request.
 *
 * @param CakeRequest $request The request that contains authentication data.
 * @param CakeResponse $response The response
 * @return array User record data, or false, if the user could not be identified.
 */
	public function identify(CakeRequest $request, CakeResponse $response) {
	    //var_dump($this->_authenticateObjects);exit;
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		
		foreach ($this->_authenticateObjects as $auth) {
			//var_dump($auth);echo"<br>===================";
			
			$result = $auth->authenticate($request, $response);
/*
$result =  array(
  "id"=> "326",
  "firstname"=> "DG",
  "lastname"=> "Test",
  "pseudo"=>NULL,
  "email"=> "degrefinance@protonmail.com",
  "birthdate"=>NULL,
  "address"=> "460 chemin preysssac",
  "postalcode"=> "82000",
  "city"=> "Montauban",
  "sexe"=> "0",
  "country_id"=> "1",
  "domain_id"=> "19",
  "lang_id"=>"1",
  "optin"=> "1",
  "personal_code"=> "388800",
  "last_passwd_gen"=> "2021-07-21 14:19:49",
  "forgotten_password"=> "18289985b56d75ba7101f7ad97bef398dda6ffcb",
  "emailConfirm"=>"1",
  "active"=> "1",
  "valid"=> "1",
  "deleted"=> "0",
  "payment_opposed"=> "0",
  "parent_account_opposed"=>NULL,
  "payment_blocked"=>false,
  "date_blocked"=>NULL,
  "date_add"=> "2015-09-09 12:23:23",
  "date_upd"=> "2022-02-04 13:30:27",
  "date_lastconnexion"=> "2022-11-08 14:46:01",
  "role"=>"agent",
  "countries"=>  NULL,
  "langs"=>  NULL,
  "agent_status"=> "available",
  "agent_number"=>  NULL,
  "has_photo"=> "0",
  "has_audio"=> "0",
  "has_video"=>"0",
  "credit"=> "127814",
  "credit_old"=> "0",
  "siret"=>  NULL,
  "consult_chat"=> "0",
  "consult_email"=>"0",
  "consult_phone"=>"0",
  "phone_number"=>"971529740379",
  "phone_operator"=>"",
  "phone_number2"=> "",
  "phone_operator2"=>  NULL,
  "phone_mobile"=>  NULL,
  "phone_operator3"=>  NULL,
  "phone_api_use"=> "33619770994",
  "stop_expert"=>"0",
  "date_close_account"=>  NULL,
  "creditMail"=>  NULL,
  "record"=>"0",
  "date_last_activity"=> "2022-08-18 16:37:02",
  "chat_last_activity"=> "2021-11-10 14:34:33",
  "limit_credit"=>"100000",
  "careers"=>  NULL,
  "profile"=>  NULL,
  "website"=>  NULL,
  "facebook"=>  NULL,
  "external_link"=>  NULL,
  "no_external_link"=> "0",
  "status"=> "0",
  "list_pos"=> "0",
  "order_cat"=> "1",
  "mail_price"=>"12",
  "rib"=> "",
  "bank_name"=> "",
  "bank_address"=>  NULL,
  "bank_country"=> "",
  "iban"=> "",
  "swift"=>"",
  "invoice_vat_id"=>  NULL,
  "society_type_id"=>  NULL,
  "societe_statut"=>  NULL,
  "societe"=> "",
  "societe_adress"=>  NULL,
  "societe_adress2"=>  NULL,
  "societe_cp"=>  NULL,
  "societe_ville"=>  NULL,
  "societe_pays"=>  NULL,
  "vat_num"=>  NULL,
  "vat_num_status"=>  NULL,
  "vat_num_status_reason"=>  NULL,
  "vat_num_status_reason_desc"=>  NULL,
  "vat_num_status_reason_obs"=>  NULL,
  "vat_num_proof"=>  false,
  "belgium_save_num"=>  NULL,
  "belgium_society_num"=> NULL,
  "canada_id_hst"=>  NULL,
  "spain_cif"=>  NULL,
  "luxembourg_autorisation"=>  NULL,
  "luxembourg_commerce_registrar"=>  NULL,
  "marocco_ice"=>  NULL,
  "marocco_if"=>  NULL,
  "portugal_nif"=>  NULL,
  "senegal_ninea"=>  NULL,
  "senegal_rccm"=>  NULL,
  "tunisia_rc"=>  NULL,
  "tunisia_mf"=>  NULL,
  "paypal"=>  NULL,
  "mode_paiement"=>  NULL,
  "stripe_account"=>  NULL,
  "stripe_base"=>  NULL,
  "stripe_balance"=>  NULL,
  "stripe_available"=>  NULL,
  "stripe_payout_status"=>  NULL,
  "save_bank_card"=>  true,
  "absence"=>  NULL,
  "flag_new"=>  NULL,
  "date_new"=>  NULL,
  "date_demand_doc"=>  NULL,
  "nb_consult_ajoute"=>   "0",
  "subscribe_mail"=> true,
  "alert_phone"=> "0",
  "alert_sms"=>"1",
  "alert_mail"=> "1",
  "alert_night"=>"1",
  "mail_infos"=>  NULL,
  "mail_infos_v"=>  NULL,
  "source"=>"Indefini",
  "reviews_avg"=> "0",
  "reviews_nb"=>  NULL,
  "consults_nb"=>  NULL,
  "is_come_back"=>false
  
);			    
*/

			
	if (!empty($result) && is_array($result)) { 
		//var_dump($result);
		return $result;
			}
		}
		
		return false;
	}

/**
 * Loads the configured authentication objects.
 *
 * @return mixed either null on empty authenticate value, or an array of loaded objects.
 * @throws CakeException
 */
	public function constructAuthenticate() {
	    //var_dump($this->authenticate);exit;
		if (empty($this->authenticate)) {
			return;
		}
		$this->_authenticateObjects = array();
		$config = Hash::normalize((array)$this->authenticate);
		$global = array();
		if (isset($config[AuthComponent::ALL])) {
			$global = $config[AuthComponent::ALL];
			unset($config[AuthComponent::ALL]);
		}
		foreach ($config as $class => $settings) {
			list($plugin, $class) = pluginSplit($class, true);
			$className = $class . 'Authenticate';
			App::uses($className, $plugin . 'Controller/Component/Auth');
			if (!class_exists($className)) {
				throw new CakeException(__d('cake_dev', 'Authentication adapter "%s" was not found.', $class));
			}
			if (!method_exists($className, 'authenticate')) {
				throw new CakeException(__d('cake_dev', 'Authentication objects must implement an %s method.', 'authenticate()'));
			}
			$settings = array_merge($global, (array)$settings);
			$this->_authenticateObjects[] = new $className($this->_Collection, $settings);
		}
		//var_dump($this->_authenticateObjects);exit;
		
		return $this->_authenticateObjects;
	}

/**
 * Hash a password with the application's salt value (as defined with Configure::write('Security.salt');
 *
 * This method is intended as a convenience wrapper for Security::hash(). If you want to use
 * a hashing/encryption system not supported by that method, do not use this method.
 *
 * @param string $password Password to hash
 * @return string Hashed password
 * @deprecated Since 2.4. Use Security::hash() directly or a password hasher object.
 */
	public static function password($password) {
		return Security::hash($password, null, true);
	}

/**
 * Check whether or not the current user has data in the session, and is considered logged in.
 *
 * @return boolean true if the user is logged in, false otherwise
 */
	public function loggedIn() {
		return (bool)$this->user();
	}

/**
 * Set a flash message. Uses the Session component, and values from AuthComponent::$flash.
 *
 * @param string $message The message to set.
 * @return void
 */
	public function flash($message) {
		if ($message === false) {
			return;
		}
		$this->Session->setFlash($message, $this->flash['element'], $this->flash['params'], $this->flash['key']);
	}

}
