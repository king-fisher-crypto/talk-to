<?php
/**
 * This is email configuration file.
 *
 * Use it to configure email transports of CakePHP.
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
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *		Mail 		- Send using PHP mail function
 *		Smtp		- Send using SMTP
 *		Debug		- Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email. Transports should be named 'YourTransport.php',
 * where 'Your' is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */
class EmailConfig {

	public $default_old = array(
		'transport' => 'Mail',
		'from' => 'contact@talkappdev.com',
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
		'returnPath' => 'undelivery@talkappdev.com',
		'additionalParameters' => '-fundelivery@talkappdev.com',
	);
	
	public $default = array(
		'transport' => 'Smtp',
		'from' => 'contact@talkappdev.com',
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
		'returnPath' => 'undelivery@talkappdev.com',
		'additionalParameters' => '-fundelivery@talkappdev.com',
		'host' => 'smtp.eu.mailgun.org',
        'port' => 587,
        'timeout' => 30,
        'username' => 'postmaster@fr.recettespi.com',
        'password' => '486e98b0ed2f5c5cdb40841a9a9f1221-c485922e-c3059f62',
	);

	
	
	/*public $smtp = array(
		'transport' => 'Smtp',
		'from' => array('jrsaban@noox.fr' => 'Spiriteo'),
		'host' => 'ns0.ovh.net',
		'port' => 587,
		'timeout' => 30,
		'username' => 'jrsaban@noox.fr',
		'password' => 'jrBs13Nx$',
		'client' => null,
		'log' => false,
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);*/

    public $smtp = array(
        'transport' => 'Smtp',
        'from' => array('no-reply@talkappdev.com' => 'no-reply@talkappdev.com'),
        'host' => 'smtp.spiriteo.com',
        'port' => 25,
        'timeout' => 30,
        'username' => 'no-reply@talkappdev.com',
        'password' => 'm2jP4FaB49',
        'client' => null,
        'log' => false,
        //'charset' => 'utf-8',
        //'headerCharset' => 'utf-8',
    );

	public $fast = array(
		'from' => 'you@localhost',
		'sender' => null,
		'to' => null,
		'cc' => null,
		'bcc' => null,
		'replyTo' => null,
		'readReceipt' => null,
		'returnPath' => null,
		'messageId' => true,
		'subject' => null,
		'message' => null,
		'headers' => null,
		'viewRender' => null,
		'template' => false,
		'layout' => false,
		'viewVars' => null,
		'attachments' => null,
		'emailFormat' => null,
		'transport' => 'Smtp',
		'host' => 'localhost',
		'port' => 25,
		'timeout' => 30,
		'username' => 'user',
		'password' => 'secret',
		'client' => null,
		'log' => true,
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);

}
