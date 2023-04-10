<?php

//ini_set('display_errors', 1); 
//error_reporting(E_ALL); 
//ini_set("memory_limit",-1);
//set_time_limit ( 0 );

//ini_set('session.gc_maxlifetime', 60*24*60*60); // 2 mois

define('TIMEZONE', 'UTC');
date_default_timezone_set(TIMEZONE);

/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
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
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 * 	0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 * 	1: Errors and warnings shown, model caches refreshed, flash messages halted.
 * 	2: As in 1, but also with full debug messages and SQL output.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */

Configure::write('debug',0);
//Configure::write('debug', (isset($_SERVER) && !empty($_SERVER["REMOTE_ADDR"]) &&  $_SERVER["REMOTE_ADDR"] == "90.76.64.237")?2:0);

Configure::write('Site.maintenance', 0);

/* Variables Site */
    Configure::write('Site.name', 'talkappdev');
    Configure::write('Site.nameDomain', 'talkto_php');
    Configure::write('Site.baseUrlFull', 'https://talkto_php.local');

	Configure::write('Site.utc_dec', 2);    //Decalage horaire FR
	Configure::write('Site.chat_dec', 7200); //3600   //Decalage horaire FR

    Configure::write('Site.timeMinPass', 1800);    //Durée minimum en sec entre chaque génération de mot de passe

	Configure::write('Site.emailsAdmins', array('system@web-sigle.fr')); 
	
	Configure::write('Site.pathAdmin', '/voynce/app/webroot/');
    Configure::write('Site.pathPhoto', 'media/photo');    //Chemin pour les photos des voyants à partir du dossier webroot
	Configure::write('Site.pathPhotoAdmin', '/voynce/app/webroot/media/photo');    //Chemin pour les photos des voyants à partir du dossier webroot
	Configure::write('Site.pathDocumentAdmin', '/voynce/app/webroot/media/documents');
	Configure::write('Site.pathDocument', '/media/documents');
    Configure::write('Site.pathPresentation', 'media/presentation');    //Chemin pour les présentations audios des voyants à partir du dossier webroot
	Configure::write('Site.pathPresentationAdmin', '/voynce/app/webroot/media/presentation');    //Chemin pour les présentations audios des voyants à partir du dossier webroot
    Configure::write('Site.pathPhotoValidation', 'media/validation/photo');    //Chemin pour les photos en attente des voyants à partir du dossier webroot
	Configure::write('Site.pathPhotoValidationAdmin', '/voynce/app/webroot/media/validation/photo');
    Configure::write('Site.pathPresentationValidation', 'media/validation/presentation');    //Chemin pour les présentations audios en attente des voyants à partir du dossier webroot
    Configure::write('Site.pathPresentationValidationAdmin', '/voynce/app/webroot/media/validation/presentation');
	Configure::write('Site.pathInscriptionMediaUpload', '/voynce/app/webroot/media/inscription');
    Configure::write('Site.pathInscriptionMedia', 'media/inscription');    //Chemin pour les photos et les présentations audio des voyants en attente de validation à partir du dossier webroot
    Configure::write('Site.pathInscriptionMediaAdmin', '/voynce/app/webroot/media/inscription');
	Configure::write('Site.pathAttachment', 'media/attachment');    //Chemin pour les pieces jointes des messages
	Configure::write('Site.pathSupport', 'media/support');    //Chemin pour les pieces jointes des messages support
	Configure::write('Site.pathSupportAdmin', '/voynce/app/webroot/media/support');    //Chemin pour les pieces jointes des messages support
	Configure::write('Site.pathChatLive', 'media/chat_live');//Chemin pour les pieces jointes des tchats
	Configure::write('Site.pathChatLiveAdmin', '/voynce/app/webroot/media/chat_live');//Chemin pour les pieces jointes des tchats
	Configure::write('Site.pathChatArchiveAdmin', '/voynce/app/webroot/media/chat_archive');//Chemin pour les pieces jointes des tchats pour consulter historique
	Configure::write('Site.pathChatArchive', 'media/chat_archive');//Chemin pour les pieces jointes des tchats pour consulter historique
    Configure::write('Site.pathLogo', 'media/logo');    //Chemin pour les logos des sites
    Configure::write('Site.pathPhotoCMS', 'media/cms_photo');    //Chemin pour les photos des pages cms
    Configure::write('Site.pathExport', '/voynce/app/tmp/export');    //Chemin pour les exports csv
    Configure::write('Site.pathLogHipay', TMP.'/logs/hipay/');    //Chemin pour les logs HiPay
    Configure::write('Site.pathMenu', 'files/');    //Chemin pour le fichier qui stocke le menu
    Configure::write('Site.pathSlide', 'media/slide');    //Chemin pour les images des slides
	Configure::write('Site.pathSlidemobile', 'media/slidemobile');    //Chemin pour les images des slides
	Configure::write('Site.pathLandingSlide', 'media/slidelanding');    //Chemin pour les images des slides
	Configure::write('Site.pathPageDesktop', 'media/slidepagedesktop');    //Chemin pour les images des slides
	Configure::write('Site.pathPageMobile', 'media/slidepagemobile');    //Chemin pour les images des slides
    Configure::write('Site.pathSlideprice', 'media/slideprice');    //Chemin pour les images des slides
    Configure::write('Site.pathLeftColumn', 'media/column');    //Chemin pour les images des éléments de la colonne
    Configure::write('Site.defaultSlide', 'media/slide/default.jpg');    //Chemin pour l'image par défaut d'un slide
    Configure::write('Site.defaultLogo', 'media/logo/default.jpg');    //Chemin pour le logo par défaut
    Configure::write('Site.defaultImage', 'theme/default/images/avatar.jpg');    //Chemin pour l'image par défaut pour un User
    Configure::write('Site.loadingImage', 'theme/default/images/ajax-loader-photo.gif');    //Chemin pour l'image de chargement
    Configure::write('Site.pathRecord', 'media/records');    //Chemin pour l'image par défaut pour un User
	Configure::write('Site.pathRecordCron', '/var/www/calltode/www/app/webroot/media/records');    //Chemin pour l'image par défaut pour un User
	Configure::write('Site.pathRecordArchive', 'media/records_archive');    //Chemin pour l'image par défaut pour un User
    Configure::write('Site.pathZodiac', 'theme/default/images/zodiac');    //Chemin pour les icônes des signes du zodiaque
    Configure::write('Site.pathHoroscope', 'media/horoscopes');
	Configure::write('Site.cardImages', 'app/webroot/media/cardImages');    //Chemin pour les images des jeu de cartes
	Configure::write('Site.cardItemImages', 'app/webroot/media/cardItemImages');

    Configure::write('Site.alerts.delay_between_alerts_second', 1); // Délai entre chaque alerte pour un même agent / client
    Configure::write('Site.alerts.days', 5); // Nombre de jours de fonctionnement des alertes d'un utilisateur
    Configure::write('Site.alerts.max_sms', 1); // Nombre maximum de sms par jour pour un numero

    Configure::write('Site.maxSizeAudio', 2000000);    //Taille maximum pour un fichier audio en octets

    Configure::write('Site.photoDim.h', 190); //Hauteur de l'avatar d'un voyant
    Configure::write('Site.photoDim.w', 190); //Largeur de l'avatar d'un voyant
    Configure::write('Site.photoListing.h', 95); //Hauteur de l'avatar d'un voyant pour le listing
    Configure::write('Site.photoListing.w', 95); //Largeur de l'avatar d'un voyant pour le listing

    Configure::write('Site.limitAgentPage', 35); //Nombre d'agents affiché par page
    Configure::write('Site.limitReviewPage', 40); //Nombre d'avis affiché par page
    Configure::write('Site.limitPlanning', 15); //L'intervalle de jour max pour le planning d'un agent
    Configure::write('Site.limitMessagePage', 15); //Nombre de messages par page pour agent et client
    Configure::write('Site.limitReviewAgent', 50); //Nombre d'avis pour la fiche agent
    Configure::write('Site.limitStatistique', 15); //Le nombre de statistique pour admin_view
    Configure::write('Site.limitFileAudio', 15); //Le nombre de fichier audio pour admin_record_audio (Agent => Enregistrement audio)

    Configure::write('Site.lengthMetaTitle', 200); //Le nombre de caractère max pour la balise meta_title
	Configure::write('Site.lengthMetaTitle2', 77); //Le nombre de caractère max pour la balise meta_title
    Configure::write('Site.lengthMetaKeywords', 200); //Le nombre de caractère max pour la balise meta_keywords
    Configure::write('Site.lengthMetaDescription', 500); //Le nombre de caractère max pour la balise meta_description
	Configure::write('Site.lengthMetaDescription2', 207); //Le nombre de caractère max pour la balise meta_description
    Configure::write('Site.lengthCatDescription', 820); //Le nombre de caractère max pour la description d'un univers (category)

    Configure::write('Site.timerAgentMenuStatus', 30000); //Le timer pour refresh le status de l'agent dans son extranet
    Configure::write('Site.timerMessagerie', 30000); //Le timer pour refresh la messagerie

    Configure::write('Site.secondePourUnCredit', 1);  //Le nombre de seconde pour 1 crédit

    Configure::write('Site.creditPourUnMail', 900);  //Le nombre de crédit pour l'envoi d'un mail par défaut
    Configure::write('Site.debitMailQuestion', true);  //Si on débite lors de l'envoi du question par mail
    Configure::write('Site.previewMail', 16);  //Le nombre de caractères que l'on affiche pour la preview d'un mail
	Configure::write('Site.maxMessagePerMinutes', 5);  //Le nombre de message privé toute les 5 minutes

    Configure::write('Site.unlimitedReview', true); //Pouvoir posté autant de fois que l'on veut un avis sur un agent

    //Configure::write('Site.urlApi', 'https://voyance-dev.interne.biz'); //L'url de l'api
    //Configure::write('Site.portApi', 2290); //Le port de l'api
    Configure::write('Site.urlApi', 'https://api.daotec.com/spiriteo'); //L'url de l'api https://api.daotec.com/spiriteo/api/
    Configure::write('Site.portApi', 443); //Le port de l'api
    Configure::write('Site.smsUrlApi', 'https://api.daotec.com/spiriteo'); //L'url de l'api
    Configure::write('Site.smsPortApi', 443); //Le port de l'api

    Configure::write('Site.catBlocTexteID', 9); //L'id de la catégorie de bloctexte
    Configure::write('Site.menuDelimiter', '##'); //Le délimiteur qui permet de créer des catégories pour le top menu
    Configure::write('Site.agentslistForCategory', array(7,8)); //Les catégories dont les pages auront l'élément agentslist

    Configure::write('nomCachePlanning', 'planning'); //Le nom de la config du cache pour les planning agents
    Configure::write('nomCacheNavigation', 'navigation'); //Le nom de la config du cache pour les menus
    Configure::write('nomCacheFooter', array('footer_block_1_', 'footer_block_2_', 'footer_block_3_', 'footer_block_4_')); //Le nom des caches pour le footer, pour le controller page


    Configure::write('Chat.creditMinPourChat', 60);  //Le nombre de crédit minimum qu'il faut avoir pour une consultation par chat
    Configure::write('Chat.maxDisplay', 50);  //Le nombre de message visible sur le chat
    Configure::write('Chat.consultStartAnswer', true);  //Bool pour savoir à quel moment la consultation d'un chat commence. (true => l'envoie d'une réponse, false => premières lettres tapées)
    Configure::write('Chat.maxTimeInactif', 16);  //Le temps en secondes max après lequel un agent est considéré inactif pour le chat
	Configure::write('Chat.maxDelayInactif', 240);  //Le temps en secondes max après lequel un agent est considéré inactif pour le chat
    Configure::write('Chat.maxTimeCloseChat', 20);  //L'intervalle de temps en secondes entre la dernière activité du chat et la date de maintenant avant fermeture du chat

    Configure::write('Admin.id', 1); //L'id de l'admin fantôme
    Configure::write('Guest.id', 2); //L'id de l'invite fantôme
    Configure::write('Site.id_domain_com', '15,12,20,23,24,10,14,16,17,18,21,25,26'); //L'id du domaine .com

    Configure::write('Logo.width', 213); //Largeur pour un logo
    Configure::write('Logo.height', 65); //Hauteur pour un logo

    Configure::write('Slide.width', 1920); //Largeur pour un slide
    Configure::write('Slide.height', 441); //Hauteur pour un slide

	Configure::write('Slidemobile.width', 746); //Largeur pour un slide
    Configure::write('Slidemobile.height', 250); //Hauteur pour un slide

	Configure::write('LandingSlide.width', 1920); //Largeur pour un slide
    Configure::write('LandingSlide.height', 560); //Hauteur pour un slide


    Configure::write('Slideprice.width', 1920); //Largeur pour un slide
    Configure::write('Slideprice.height', 250); //Hauteur pour un slide
	Configure::write('Slidepricemobile.width', 450); //Largeur pour un slide
    Configure::write('Slidepricemobile.height', 125); //Hauteur pour un slide

	Configure::write('HoroscopeTerme.width', 265); //Largeur 
    Configure::write('HoroscopeTerme.height', 160); //Hauteur 
	Configure::write('HoroscopePubDesktop.width', 674); //Largeur 
    Configure::write('HoroscopePubDesktop.height', 200); //Hauteur 
	Configure::write('HoroscopePubMobile.width', 580); //Largeur 
    Configure::write('HoroscopePubMobile.height', 600); //Hauteur
	Configure::write('HoroscopePubSidebarTop.width', 274); //Largeur 
    Configure::write('HoroscopePubSidebarTop.height', 201); //Hauteur
	Configure::write('HoroscopePubSidebarBottom.width', 274); //Largeur 
    Configure::write('HoroscopePubSidebarBottom.height', 201); //Hauteur

    Configure::write('Cron.clearAppointment', 2); //Supprime les rdv des agents x jours avant aujourd'hui
    Configure::write('Cron.saveMessageHistory', 31); //Sauvegarde les messages d'hier
    Configure::write('Cron.clearCreditHistory', 365000); //Supprime l'historique de 15 jours

	Configure::write('Site.utcDec', array(
	'0101' => 1,'0102' => 1,'0103' => 1,'0104' => 1,'0105' => 1,'0106' => 1,'0107' => 1,'0108' => 1,'0109' => 1,'0110' => 1,'0111' => 1,'0112' => 1,'0113' => 1,'0114' => 1,'0115' => 1,'0116' => 1,'0117' => 1,'0118' => 1,'0119' => 1,'0120' => 1,'0121' => 1,'0122' => 1,'0123' => 1,'0124' => 1,'0125' => 1,'0126' => 1,'0127' => 1,'0128' => 1,'0129' => 1,'0130' => 1,'0131' => 1,'0201' => 1,'0202' => 1,'0203' => 1,'0204' => 1,'0205' => 1,'0206' => 1,'0207' => 1,'0208' => 1,'0209' => 1,'0210' => 1,'0211' => 1,'0212' => 1,'0213' => 1,'0214' => 1,'0215' => 1,'0216' => 1,'0217' => 1,'0218' => 1,'0219' => 1,'0220' => 1,'0221' => 1,'0222' => 1,'0223' => 1,'0224' => 1,'0225' => 1,'0226' => 1,'0227' => 1,'0228' => 1,'0229' => 1,'0301' => 1,'0302' => 1,'0303' => 1,'0304' => 1,'0305' => 1,'0306' => 1,'0307' => 1,'0308' => 1,'0309' => 1,'0310' => 1,'0311' => 1,'0312' => 1,'0313' => 1,'0314' => 1,'0315' => 1,'0316' => 1,'0317' => 1,'0318' => 1,'0319' => 1,'0320' => 1,'0321' => 1,'0322' => 1,'0323' => 1,'0324' => 1,'0325' => 1,'0326' => 1,'0327' => 1,'0328' => 1,'0329' => 2,'0330' => 2,'0331' => 2,'0401' => 2,'0402' => 2,'0403' => 2,'0404' => 2,'0405' => 2,'0406' => 2,'0407' => 2,'0408' => 2,'0409' => 2,'0410' => 2,'0411' => 2,'0412' => 2,'0413' => 2,'0414' => 2,'0415' => 2,'0416' => 2,'0417' => 2,'0418' => 2,'0419' => 2,'0420' => 2,'0421' => 2,'0422' => 2,'0423' => 2,'0424' => 2,'0425' => 2,'0426' => 2,'0427' => 2,'0428' => 2,'0429' => 2,'0430' => 2,'0501' => 2,'0502' => 2,'0503' => 2,'0504' => 2,'0505' => 2,'0506' => 2,'0507' => 2,'0508' => 2,'0509' => 2,'0510' => 2,'0511' => 2,'0512' => 2,'0513' => 2,'0514' => 2,'0515' => 2,'0516' => 2,'0517' => 2,'0518' => 2,'0519' => 2,'0520' => 2,'0521' => 2,'0522' => 2,'0523' => 2,'0524' => 2,'0525' => 2,'0526' => 2,'0527' => 2,'0528' => 2,'0529' => 2,'0530' => 2,'0531' => 2,'0601' => 2,'0602' => 2,'0603' => 2,'0604' => 2,'0605' => 2,'0606' => 2,'0607' => 2,'0608' => 2,'0609' => 2,'0610' => 2,'0611' => 2,'0612' => 2,'0613' => 2,'0614' => 2,'0615' => 2,'0616' => 2,'0617' => 2,'0618' => 2,'0619' => 2,'0620' => 2,'0621' => 2,'0622' => 2,'0623' => 2,'0624' => 2,'0625' => 2,'0626' => 2,'0627' => 2,'0628' => 2,'0629' => 2,'0630' => 2,'0701' => 2,'0702' => 2,'0703' => 2,'0704' => 2,'0705' => 2,'0706' => 2,'0707' => 2,'0708' => 2,'0709' => 2,'0710' => 2,'0711' => 2,'0712' => 2,'0713' => 2,'0714' => 2,'0715' => 2,'0716' => 2,'0717' => 2,'0718' => 2,'0719' => 2,'0720' => 2,'0721' => 2,'0722' => 2,'0723' => 2,'0724' => 2,'0725' => 2,'0726' => 2,'0727' => 2,'0728' => 2,'0729' => 2,'0730' => 2,'0731' => 2,'0801' => 2,'0802' => 2,'0803' => 2,'0804' => 2,'0805' => 2,'0806' => 2,'0807' => 2,'0808' => 2,'0809' => 2,'0810' => 2,'0811' => 2,'0812' => 2,'0813' => 2,'0814' => 2,'0815' => 2,'0816' => 2,'0817' => 2,'0818' => 2,'0819' => 2,'0820' => 2,'0821' => 2,'0822' => 2,'0823' => 2,'0824' => 2,'0825' => 2,'0826' => 2,'0827' => 2,'0828' => 2,'0829' => 2,'0830' => 2,'0831' => 2,'0901' => 2,'0902' => 2,'0903' => 2,'0904' => 2,'0905' => 2,'0906' => 2,'0907' => 2,'0908' => 2,'0909' => 2,'0910' => 2,'0911' => 2,'0912' => 2,'0913' => 2,'0914' => 2,'0915' => 2,'0916' => 2,'0917' => 2,'0918' => 2,'0919' => 2,'0920' => 2,'0921' => 2,'0922' => 2,'0923' => 2,'0924' => 2,'0925' => 2,'0926' => 2,'0927' => 2,'0928' => 2,'0929' => 2,'0930' => 2,'1001' => 2,'1002' => 2,'1003' => 2,'1004' => 2,'1005' => 2,'1006' => 2,'1007' => 2,'1008' => 2,'1009' => 2,'1010' => 2,'1011' => 2,'1012' => 2,'1013' => 2,'1014' => 2,'1015' => 2,'1016' => 2,'1017' => 2,'1018' => 2,'1019' => 2,'1020' => 2,'1021' => 2,'1022' => 2,'1023' => 2,'1024' => 2,'1025' => 2,'1026' => 1,'1027' => 1,'1028' => 1,'1029' => 1,'1030' => 1,'1031' => 1,'1101' => 1,'1102' => 1,'1103' => 1,'1104' => 1,'1105' => 1,'1106' => 1,'1107' => 1,'1108' => 1,'1109' => 1,'1110' => 1,'1111' => 1,'1112' => 1,'1113' => 1,'1114' => 1,'1115' => 1,'1116' => 1,'1117' => 1,'1118' => 1,'1119' => 1,'1120' => 1,'1121' => 1,'1122' => 1,'1123' => 1,'1124' => 1,'1125' => 1,'1126' => 1,'1127' => 1,'1128' => 1,'1129' => 1,'1130' => 1,'1201' => 1,'1202' => 1,'1203' => 1,'1204' => 1,'1205' => 1,'1206' => 1,'1207' => 1,'1208' => 1,'1209' => 1,'1210' => 1,'1211' => 1,'1212' => 1,'1213' => 1,'1214' => 1,'1215' => 1,'1216' => 1,'1217' => 1,'1218' => 1,'1219' => 1,'1220' => 1,'1221' => 1,'1222' => 1,'1223' => 1,'1224' => 1,'1225' => 1,'1226' => 1,'1227' => 1,'1228' => 1,'1229' => 1,'1230' => 1,'1231' => 1
	));

    Configure::write('Email.logo', 'https://www.calltode.com/media/logo/email.png'); //Le logo pour les mails

    Configure::write('Categories.hidden_for_system', array(12));

	Configure::write('Review.no_send_email', array(29837,39308,39890));

    Configure::write('Stripe.countries', array(1,2,3,4,60,145,186));

    Configure::write('Site.vonage.key', '704d5778');

    Configure::write('Site.vonage.secret', 'O8p5OiG8pKd6DLYa');

    Configure::write('Site.vonage.application_id', 'dcd30f17-ed8a-424b-a9a6-9e72bed46905');
    Configure::write('Site.vonage.number', '33644631340');

/* Cache */
    Cache::config('planning', array(
        'engine' => 'File',
        'duration'=> '+1 hours',
        'path' => CACHE,
        'prefix' => 'cake_court_'
    ));

    Cache::config('navigation', array(
        'engine' => 'File',
        'duration'=> '+1 hour',
        'path' => CACHE,
        'prefix' => 'cake_court_'
    ));

    Cache::config('layout_element', array(
        'engine' => 'File',
        'duration'=> '+1 hour',
        'path' => CACHE,
        'prefix' => 'cake_court_'
    ));

    Cache::config('lang_langid', array(
        'engine' => 'File',
        'duration'=> '+24 hours',
        'path' => CACHE,
        'prefix' => 'cake_court_'
    ));


Cache::config('request_long', array(
        'engine' => 'File',
        'duration'=> '+1 minutes',
        'path' => CACHE,
        'prefix' => 'cake_request_'
    ));


Cache::config('request_short', array(
        'engine' => 'File',
        'duration'=> '+8 seconds',
        'path' => CACHE,
        'prefix' => 'cake_request_'
    ));


/**
 * Configure the Error handler used to handle errors for your application. By default
 * ErrorHandler::handleError() is used. It will display errors using Debugger, when debug > 0
 * and log errors with CakeLog when debug = 0.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle errors. You can set this to any callable type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `level` - integer - The level of errors you are interested in capturing.
 * - `trace` - boolean - Include stack traces for errors in log files.
 *
 * @see ErrorHandler for more information on error handling and configuration.
 */
	Configure::write('Error', array(
		'handler' => 'ErrorHandler::handleError',
		'level' => E_ALL & ~E_DEPRECATED,
		'trace' => true
	));

/**
 * Configure the Exception handler used for uncaught exceptions. By default,
 * ErrorHandler::handleException() is used. It will display a HTML page for the exception, and
 * while debug > 0, framework errors like Missing Controller will be displayed. When debug = 0,
 * framework errors will be coerced into generic HTTP errors.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle exceptions. You can set this to any callback type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `renderer` - string - The class responsible for rendering uncaught exceptions. If you choose a custom class you
 *   should place the file for that class in app/Lib/Error. This class needs to implement a render method.
 * - `log` - boolean - Should Exceptions be logged?
 * - `skipLog` - array - list of exceptions to skip for logging. Exceptions that
 *   extend one of the listed exceptions will also be skipped for logging.
 *   Example: `'skipLog' => array('NotFoundException', 'UnauthorizedException')`
 *
 * @see ErrorHandler for more information on exception handling and configuration.
 */
	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'ExceptionRenderer',
		'log' => true
	));

/**
 * Application wide charset encoding
 */
	Configure::write('App.encoding', 'UTF-8');

/**
 * To configure CakePHP *not* to use mod_rewrite and to
 * use CakePHP pretty URLs, remove these .htaccess
 * files:
 *
 * /.htaccess
 * /app/.htaccess
 * /app/webroot/.htaccess
 *
 * And uncomment the App.baseUrl below. But keep in mind
 * that plugin assets such as images, CSS and JavaScript files
 * will not work without URL rewriting!
 * To work around this issue you should either symlink or copy
 * the plugin assets into you app's webroot directory. This is
 * recommended even when you are using mod_rewrite. Handling static
 * assets through the Dispatcher is incredibly inefficient and
 * included primarily as a development convenience - and
 * thus not recommended for production applications.
 */
	//Configure::write('App.baseUrl', env('SCRIPT_NAME'));

/**
 * To configure CakePHP to use a particular domain URL
 * for any URL generation inside the application, set the following
 * configuration variable to the http(s) address to your domain. This
 * will override the automatic detection of full base URL and can be
 * useful when generating links from the CLI (e.g. sending emails)
 */
	//Configure::write('App.fullBaseUrl', 'http://example.com');

/**
 * Web path to the public images directory under webroot.
 * If not set defaults to 'img/'
 */
	//Configure::write('App.imageBaseUrl', 'img/');

/**
 * Web path to the CSS files directory under webroot.
 * If not set defaults to 'css/'
 */
	//Configure::write('App.cssBaseUrl', 'css/');

/**
 * Web path to the js files directory under webroot.
 * If not set defaults to 'js/'
 */
	//Configure::write('App.jsBaseUrl', 'js/');

/**
 * Uncomment the define below to use CakePHP prefix routes.
 *
 * The value of the define determines the names of the routes
 * and their associated controller actions:
 *
 * Set to an array of prefixes you want to use in your application. Use for
 * admin or other prefixed routes.
 *
 * 	Routing.prefixes = array('admin', 'manager');
 *
 * Enables:
 *	`admin_index()` and `/admin/controller/index`
 *	`manager_index()` and `/manager/controller/index`
 *
 */
	//Configure::write('Routing.prefixes', array('admin'));

	
	Configure::write('Routing.prefixes', array('admin'));
/**
 * Turn off all caching application-wide.
 *
 */
	//Configure::write('Cache.disable', true);

/**
 * Enable cache checking.
 *
 * If set to true, for view caching you must still use the controller
 * public $cacheAction inside your controllers to define caching settings.
 * You can either set it controller-wide by setting public $cacheAction = true,
 * or in each action using $this->cacheAction = true.
 *
 */
	Configure::write('Cache.check', true);
	
	/*Cache::config('short', array(
    'engine' => 'File',
    'duration'=> '+5 minutes',
    'probability'=> 100,
    'path' => CACHE,
    'prefix' => 'cache_short_'
));*/

Cache::config('short', array(
    'engine' => 'File',
    'duration'=> '+5 minutes',
    'probability'=> 100,
    'path' => CACHE,
    'prefix' => 'cache_short_'
));

/**
 * Enable cache view prefixes.
 *
 * If set it will be prepended to the cache name for view file caching. This is
 * helpful if you deploy the same application via multiple subdomains and languages,
 * for instance. Each version can then have its own view cache namespace.
 * Note: The final cache file name will then be `prefix_cachefilename`.
 */
	//Configure::write('Cache.viewPrefix', 'prefix');

/**
 * Session configuration.
 *
 * Contains an array of settings to use for session configuration. The defaults key is
 * used to define a default preset to use for sessions, any settings declared here will override
 * the settings of the default config.
 *
 * ## Options
 *
 * - `Session.cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'
 * - `Session.timeout` - The number of minutes you want sessions to live for. This timeout is handled by CakePHP
 * - `Session.cookieTimeout` - The number of minutes you want session cookies to live for.
 * - `Session.checkAgent` - Do you want the user agent to be checked when starting sessions? You might want to set the
 *    value to false, when dealing with older versions of IE, Chrome Frame or certain web-browsing devices and AJAX
 * - `Session.defaults` - The default configuration set to use as a basis for your session.
 *    There are four builtins: php, cake, cache, database.
 * - `Session.handler` - Can be used to enable a custom session handler. Expects an array of callables,
 *    that can be used with `session_save_handler`. Using this option will automatically add `session.save_handler`
 *    to the ini array.
 * - `Session.autoRegenerate` - Enabling this setting, turns on automatic renewal of sessions, and
 *    sessionids that change frequently. See CakeSession::$requestCountdown.
 * - `Session.ini` - An associative array of additional ini values to set.
 *
 * The built in defaults are:
 *
 * - 'php' - Uses settings defined in your php.ini.
 * - 'cake' - Saves session files in CakePHP's /tmp directory.
 * - 'database' - Uses CakePHP's database sessions.
 * - 'cache' - Use the Cache class to save sessions.
 *
 * To define a custom session handler, save it at /app/Model/Datasource/Session/<name>.php.
 * Make sure the class implements `CakeSessionHandlerInterface` and set Session.handler to <name>
 *
 * To use database sessions, run the app/Config/Schema/sessions.php schema using
 * the cake shell command: cake schema create Sessions
 *
 */

/* AVANT 31/12/2014
	Configure::write('Session', array(
		'defaults' => 'php'
	));
*/
Configure::write('Session', array(
   // 'cookieTimeout' => 0,
    
    'defaults' => 'php'
));

/**
 * A random string used in security hashing methods.
 */
	Configure::write('Security.salt', '09OsixUZ39DjSNZEIGR72DCBSJKSPOZ0292384539FjlkdsfhjF321QWCAPZoeJIZ82$EFZKY');

/**
 * A random numeric string (digits only) used to encrypt/decrypt strings.
 */
	Configure::write('Security.cipherSeed', '0293748500329844957316349675750129390001232615374950274851846294179023741');

/**
 * Apply timestamps with the last modified time to static assets (js, css, images).
 * Will append a query string parameter containing the time the file was modified. This is
 * useful for invalidating browser caches.
 *
 * Set to `true` to apply timestamps when debug > 0. Set to 'force' to always enable
 * timestamping regardless of debug value.
 */
	//Configure::write('Asset.timestamp', true);

/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
	//Configure::write('Asset.filter.css', 'css.php');

/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JsHelper::link().
 */
	//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');

/**
 * The class name and database used in CakePHP's
 * access control lists.
 */
	Configure::write('Acl.classname', 'DbAcl');
	Configure::write('Acl.database', 'default');

/**
 * Uncomment this line and correct your server timezone to fix
 * any date & time related errors.
 */
	//date_default_timezone_set('UTC');

	Cache::config('default', array(
       'engine' => 'File', //[required]
       //'duration' => 3600, //[optional]
       'duration' => 1, //[optional]
       'probability' => 100, //[optional]
       'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
       'prefix' => 'cake_', //[optional]  prefix every cache file with this string
       'lock' => false, //[optional]  use file locking
       'serialize' => true, //[optional]
       'mask' => 0664, //[optional]
   ));
   
   Cache::config('layout_elements', array(
       'engine' => 'File', //[required]
      // 'duration' => 600, //[optional]
       'duration' => 1, //[optional]
       'probability' => 100, //[optional]
       'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
       'prefix' => 'cake_', //[optional]  prefix every cache file with this string
       'lock' => false, //[optional]  use file locking
       'serialize' => true, //[optional]
       'mask' => 0664, //[optional]
   ));

	
/**
 *
 * Cache Engine Configuration
 * Default settings provided below
 *
 * File storage engine.
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'File', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
 * 		'prefix' => 'cake_', //[optional]  prefix every cache file with this string
 * 		'lock' => false, //[optional]  use file locking
 * 		'serialize' => true, //[optional]
 * 		'mask' => 0664, //[optional]
 *	));
 *
 * APC (http://pecl.php.net/package/APC)
 **/
	/* Cache::config('default', array(
		'engine' => 'Apc', //[required]
		'duration' => 3600, //[optional]
		'probability' => 100, //[optional]
 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
	));*/
/*
 * Xcache (http://xcache.lighttpd.net/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Xcache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
 *		'user' => 'user', //user from xcache.admin.user settings
 *		'password' => 'password', //plaintext password (xcache.admin.pass)
 *	));
 *
 * Memcache (http://www.danga.com/memcached/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Memcache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 * 		'servers' => array(
 * 			'127.0.0.1:11211' // localhost, default port 11211
 * 		), //[optional]
 * 		'persistent' => true, // [optional] set this to false for non-persistent connections
 * 		'compress' => false, // [optional] compress data in Memcache (slower, but uses less memory)
 *	));
 *
 *  Wincache (http://php.net/wincache)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Wincache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 */

/**
 * Configure the cache handlers that CakePHP will use for internal
 * metadata like class maps, and model schema.
 *
 * By default File is used, but for improved performance you should use APC.
 *
 * Note: 'default' and other application caches should be configured in app/Config/bootstrap.php.
 *       Please check the comments in bootstrap.php for more info on the cache engines available
 *       and their settings.
 */
$engine = 'File';
if (ini_get('apc.enabled') && php_sapi_name() !== 'cli') {
	//if($_SERVER["REMOTE_ADDR"] != "90.87.234.107")
	$engine = 'Apc';
	//if($_SERVER["REMOTE_ADDR"] == "90.87.229.193"){
		$info = apcu_cache_info ();
		if(is_array($info) && $info["mem_size"]){
			if($info["mem_size"] > 5000000){
				apcu_clear_cache();
				//mail ( 'system@web-sigle.fr', 'apcu_clear_cache' , $info["mem_size"]);
			}
		}
	//}
}

// In development mode, caches should expire quickly.
//$duration = '+999 days';
//$duration_core = '+1 days';
$duration = '+15 minutes';
$duration_core = '+15 minutes';
if (Configure::read('debug') > 0) {
	$duration = '+10 seconds';
	$duration_core = '+10 seconds';
}

// Prefix each application on the same server with a different string, to avoid Memcache and APC conflicts.
$prefix = 'myappcall_';

/**
 * Configure the cache used for general framework caching. Path information,
 * object listings, and translation cache files are stored with this configuration.
 */
Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration_core
));

/**
 * Configure the cache for model and datasource caches. This cache configuration
 * is used to store schema descriptions, and table listings in connections.
 */
Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));
