<?php
	$domain = $_SERVER['SERVER_NAME'];
	$params = $this->request->params;
	$dbb_r = new DATABASE_CONFIG();
	$dbb_head = $dbb_r->default;
	$mysqli_head = new mysqli($dbb_head['host'], $dbb_head['login'], $dbb_head['password'], $dbb_head['database']);
	$result_head = $mysqli_head->query("SELECT id from domains where domain= '$domain'");
	$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
	$current_id_domain = $row_head['id'];
	$list_domain_actif = array(11,13,19,22,29,30);
	
	if($params['controller'] == 'pages' || $params['controller'] == 'landing' || $params['controller'] == 'horoscopes'){
		$this->FrontBlock->beginCachePageHtml();
	}
?>
<!DOCTYPE html>

	<head>
<?php 
	//plateforme affiliate
	if(Configure::read('Site.name') == 'Spiriteo'){
		require('../webroot/affiliate/controller/affiliate-tracking.php');
	}
	
	echo $this->Html->charset(); 
	
	$noindex = false;	
		
	if (!in_array($current_id_domain, $list_domain_actif)){
		echo '<meta name="robots" content="noindex">';	
		$noindex = true;
	}
?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	

    <title><?php echo substr($site_vars['meta_title'],0,Configure::read('Site.lengthMetaTitle')); ?></title>
<?php
	
	echo $this->Html->meta('icon');
	echo $this->Html->meta(array('name' => 'google-site-verification', 'content' => 'WhsNhCgg1w3-m0BRl6o-x9S4euDgKgvlW8Zlx2k1us4'), NULL, array('inline' => false));
  echo $this->Html->meta(array('name' => 'p:domain_verify', 'content' => '2774d111333d3b10a0330bcb19d6dc26'), NULL, array('inline' => false));
	echo $this->Html->meta(array('name' => 'msvalidate.01', 'content' => 'A8F5FF06D24597F4142688CC5FA76BB3'), NULL, array('inline' => false));
	
	if (!empty($site_vars['meta_keywords'])) echo $this->Html->meta('keywords', substr($site_vars['meta_keywords'],0,Configure::read('Site.lengthMetaKeywords')));
    if (!empty($site_vars['meta_description'])) echo $this->Html->meta('description', substr($site_vars['meta_description'],0,Configure::read('Site.lengthMetaDescription')));
	 
	//NOINDEX
    if (!empty($site_vars['robots']) && $site_vars['robots'] == 'noindex'){
		echo '<meta name="robots" content="noindex">';
		$noindex = true;
	} 
	
	if(($params['controller'] == 'users') || $params['controller'] == 'alerts' || ($params['controller'] == 'accounts' && $params['action'] == 'add_favorite') || ($params['controller'] == 'alerts' && $params['action'] == 'setnew') || ($params['controller'] == 'reviews' && !empty($params['page']) && $params['page'] > 1) || ($params['controller'] == 'home' && !empty($params['page']) && $params['page'] > 1) || ($params['controller'] == 'category' && !empty($params['page']) && $params['page'] > 1) || $params['controller'] == 'gifts' || ($params['controller'] == 'home' && $params['action'] == 'media_phone') || ($params['controller'] == 'chats' && $params['action'] == 'create_session') || ($params['controller'] == 'accounts' && $params['action'] == 'new_mail') || ($params['controller'] == 'sponsorship')){
			echo '<meta name="robots" content="noindex">';
			$noindex = true;
		}
	
	if($params['controller'] == 'products' && !substr_count($this->request->url,'acheter')){
		$noindex = true;
		echo '<meta name="robots" content="noindex">';	
	}
	if($params['controller'] == 'contacts' && !substr_count($this->request->url,'nous')){
		echo '<meta name="robots" content="noindex">';	
		$noindex = true;
	}
		
		
	if($params['controller'] == 'category' && ($params['id'] == 25)){
		echo '<meta name="robots" content="noindex">';	
		$noindex = true;
	}
	if($params['controller'] == 'accounts'  && $params['action'] == 'buycredit'){
		echo '<meta name="robots" content="noindex">';
		$noindex = true;
	}
		
	if($params['controller'] == 'accounts'  && $params['action'] == 'cart'){
		echo '<meta name="robots" content="noindex">';	
		$noindex = true;
	}
	if($params['controller'] == 'accounts'  && $params['action'] == 'buycreditpayment'){
		echo '<meta name="robots" content="noindex">';	
		$noindex = true;
	}

	if($params['controller'] == 'pages'  && $params['action'] == 'display' && $params['link_rewrite'] == 'recouvrence' ){
		echo '<meta name="robots" content="noindex">';	
		$noindex = true;
	}
	
		
	//HREFLLANG
	if(!$noindex){
		echo $this->FrontBlock->gethreflang();

	//CANONICAL
		
		switch ($params['controller']) {
				case 'agents':
					if(isset($params['tab']) && $params['tab'] != 'profil'){
						$l = str_replace('-'.$tab,'',$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
						echo '<link rel="canonical" href="https://'.$l.'" />';	
					}else{
						echo '<link rel="canonical" href="https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" />';	
					}
				break;
				case 'home':
					$ccat_url = 'https://'.$_SERVER['SERVER_NAME'].'/';
					echo '<link rel="canonical" href="'.$ccat_url.'" />';	
				break;
				case 'category':
					$ccat_url = 'https://'.$_SERVER['SERVER_NAME'].'/'.$params["language"].'/'.$params["link_rewrite"].'-'.$params["id"];
					echo '<link rel="canonical" href="'.$ccat_url.'" />';	
				break;
				case 'reviews':
					$cut_uri = explode('-',$_SERVER['REQUEST_URI']);
					$review_url = 'https://'.$_SERVER['SERVER_NAME'].$cut_uri[0].'-'.$cut_uri[1];
					echo '<link rel="canonical" href="'.$review_url.'" />';
				break;
				default:
					echo '<link rel="canonical" href="https://'.$_SERVER['SERVER_NAME'].strtok($_SERVER["REQUEST_URI"],'?').'" />';
				break;
		}
	}
	//APPLE TOUCH
	?>
<!--      <link href="https://fr.allfont.net/allfont.css?fonts=roboto-thin" rel="stylesheet" type="text/css" />-->
      
<!--		<link rel="apple-touch-icon" href="/theme/black_blue/images/touch-icon-iphone.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="/theme/black_blue/images/touch-icon-ipad.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="/theme/black_blue/images/touch-icon-iphone4.png" />-->
		
	<?php
	//<!-- GOOGLE PLUS
		echo '<link rel="author" href="https://plus.google.com/115836054092869172471"/>';       
        echo '<link rel="publisher" href="https://plus.google.com/115836054092869172471"/> ';
	
	//SCHEMA
	switch ($params['controller']) {
			case 'category':
				if(!isset($site_vars['meta_description'])) $site_vars['meta_description'] = '';
				echo '<script type="application/ld+json"> {   "@context": "https://schema.org",   "@type": "ProfessionalService",   "name": "Spiriteo",   "description": "'.$site_vars['meta_description'].'",   "url": "https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'",   "email": "contact@talkappdev.com",  "image": "https://'.$_SERVER['SERVER_NAME'].'/media/logo/default.jpg" , "telephone": "0970736456",   "address": {     "@type": "PostalAddress",     "addressRegion": "FRANCE"  } } </script>
				';
			break;
			case 'agents':
				if($params['action'] == 'display'){
					if(!isset($site_vars['meta_description'])) $site_vars['meta_description'] = '';

					$url_photo = '/media/photo'.DS.$params['agent_number'][0].DS.$params['agent_number'][1].DS.$params['agent_number'].'_fb.jpg';

					echo '<script type="application/ld+json"> {   "@context": "https://schema.org",   "@type": "ProfessionalService",   "name": "Spiriteo",   "description": "'.$site_vars['meta_description'].'",   "url": "https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'",   "email": "contact@talkappdev.com",  "image": "https://'.$_SERVER['SERVER_NAME'].$url_photo.'" , "telephone": "0970736456",   "address": {     "@type": "PostalAddress",     "addressRegion": "FRANCE"  } } </script>
					';
				}
			break;
			case 'home':
				echo '<script type="application/ld+json"> {   "@context":"https://schema.org",   "@type":"Organization",   "url":"https://'.$_SERVER['SERVER_NAME'].'",   "sameAs":[   "https://www.facebook.com/pages/Spiriteocom/411045319091075",   "https://twitter.com/spiriteo_com", "https://plus.google.com/u/0/115836054092869172471/posts"],   "name":"Spiriteo",   "logo":"https://'.$_SERVER['SERVER_NAME'].'/media/logo/default.jpg","aggregateRating": {
					"@type": "AggregateRating",
					"ratingValue": "'.number_format($cat_avisAVG,1).'",
					"bestRating": "100",
					"reviewCount": "'.$cat_ratingCount.'"
				  } } </script> ';
			break;
			case 'pages':
				if($params['link_rewrite'] == 'parrainage-client'){
					echo '<script type="application/ld+json"> {   "@context":"http://schema.org",   "@type":"Organization",   "url":"https://'.$_SERVER['SERVER_NAME'].'",   "sameAs":[   "https://www.facebook.com/pages/Spiriteocom/411045319091075",   "https://twitter.com/spiriteo_com", "https://plus.google.com/u/0/115836054092869172471/posts"],   "name":"Spiriteo",   "logo":"https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb_parrainge.jpg" } </script> ';
				}else{
					echo '<script type="application/ld+json"> {   "@context":"http://schema.org",   "@type":"Organization",   "url":"https://'.$_SERVER['SERVER_NAME'].'",   "sameAs":[   "https://www.facebook.com/pages/Spiriteocom/411045319091075",   "https://twitter.com/spiriteo_com", "https://plus.google.com/u/0/115836054092869172471/posts"],   "name":"Spiriteo",   "logo":"https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb.jpg" } </script> ';
				}
			break;	
			case 'gifts':
				if(!isset($site_vars['meta_description'])) $site_vars['meta_description'] = '';
				echo '<script type="application/ld+json"> {   "@context": "https://schema.org",   "@type": "ProfessionalService",   "name": "Spiriteo",   "description": "'.$site_vars['meta_description'].'",   "url": "https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'",   "email": "contact@talkappdev.com",  "image": "https://'.$_SERVER['SERVER_NAME'].'/media/rs/carte_cadeau.jpg" , "telephone": "0970736456",   "address": {     "@type": "PostalAddress",     "addressRegion": "FRANCE"  } } </script>
				';
			break;
			default:
				echo '<script type="application/ld+json"> {   "@context":"http://schema.org",   "@type":"Organization",   "url":"https://'.$_SERVER['SERVER_NAME'].'",   "sameAs":[   "https://www.facebook.com/pages/Spiriteocom/411045319091075",   "https://twitter.com/spiriteo_com", "https://plus.google.com/u/0/115836054092869172471/posts"],   "name":"Spiriteo",   "logo":"https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb.jpg" } </script> ';
			break;
	}
	
	//SOCIAL METADATA
		
		if(!isset($site_vars['meta_title'])){
			$site_vars['meta_title'] = '';	
		}
		if(!isset($site_vars['meta_description'])){
			$site_vars['meta_description'] = '';	
		}
		
		//<!-- Twitter Card data
		echo '<meta name="twitter:card" content="summary_large_image">';
		echo '<meta name="twitter:site" content="@spiriteo_com">';
		echo '<meta name="twitter:title" content="'.$site_vars['meta_title'].'">';
		echo '<meta name="twitter:description" content="'.$site_vars['meta_description'].'">';
		echo '<meta name="twitter:creator" content="@spiriteo_com">';
		echo '<meta name="twitter:image:src" content="https://'.$_SERVER['SERVER_NAME'].'/media/logo/default.jpg">';
		
		//<!-- Open Graph data
		echo '<meta property="og:title" content="'.$site_vars['meta_title'].'" />';
		echo '<meta property="og:type" content="website" />';
		echo '<meta property="og:url" content="https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" />';
	
		switch ($params['controller']) {
			case 'agents':
				if($params['action'] == 'display'){
					$url_photo = '/media/photo'.DS.$params['agent_number'][0].DS.$params['agent_number'][1].DS.$params['agent_number'].'_fb.jpg';
					echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].$url_photo.'" />';
					echo '<meta property="og:image:width" content="600" />';
					echo '<meta property="og:image:height" content="315" />';
				}
				break;
			case 'horoscopes':
				if($params['action'] == 'index'){
					echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/facebook_horoscope.jpg" />';
					echo '<meta property="og:image:width" content="600" />';
					echo '<meta property="og:image:height" content="315" />';
				}else{
					$cut_url = explode('/',$this->request->url);
					if($cut_url[2]){
						echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb_horoscope_'.$cut_url[2].'.jpg" />';
					}else{
						echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb.jpg" />';
					}
					echo '<meta property="og:image:width" content="600" />';
					echo '<meta property="og:image:height" content="315" />';
				}
				break;
			case 'sponsorship':
					if($params['action'] == 'parrainage_client'){
						echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/image_parrainage.jpg" />';
						echo '<meta property="og:image:width" content="600" />';
						echo '<meta property="og:image:height" content="315" />';
					}
					if($params['action'] == 'parrainage_agent'){
						echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/image_parrainage.jpg" />';
						echo '<meta property="og:image:width" content="600" />';
						echo '<meta property="og:image:height" content="315" />';
					}
				break;
			case 'pages':
				if($params['link_rewrite'] == 'parrainage-client'){
					echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb_parrainage.jpg" />';
						echo '<meta property="og:image:width" content="600" />';
						echo '<meta property="og:image:height" content="315" />';
				}else{
					echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb.jpg" />';
				echo '<meta property="og:image:width" content="600" />';
				echo '<meta property="og:image:height" content="315" />';
				}
			break;
			case 'gifts':
				echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/carte_cadeau.jpg" />';
				echo '<meta property="og:image:width" content="600" />';
				echo '<meta property="og:image:height" content="315" />';
			break;
			default:
				echo '<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/media/rs/fb.jpg" />';
				echo '<meta property="og:image:width" content="600" />';
				echo '<meta property="og:image:height" content="315" />';
			break;
		}
	
		echo '<meta property="og:description" content="'.$site_vars['meta_description'].'" />';
		echo '<meta property="og:site_name" content="Spiriteo" />';
		
	//PAIEMENT TRACKING
		if (!function_exists('affiliate_payment')){
			function affiliate_payment($total,$product_name,$is_new_customer_payment, $mode_payment){
				$sale_amount = $total;
				$product = $product_name;
				$is_new_affiliate = $is_new_customer_payment;
				$mode_payment_affiliate = $mode_payment;
				require_once(APP.'webroot/affiliate/controller/record-sale.php');
			}
		}
		
		
		$payment_validation = false;
		if($params['controller'] == 'paymentbankwire'){
			
			$currency = 'EUR';
			if($cart['product']['Product']['country_id'] == 3) $currency =  'CHF';
			if($cart['product']['Product']['country_id'] == 13) $currency =  'CAD';
			?>
            <script>
            dataLayer = [{
				'transactionId': '<?php  echo $cart['id_cart']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$cart['total_price']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>', 
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $cart['product']['Product']['credits']; ?>', 
					'name': '<?php  echo $cart['product']['ProductLang'][0]['name']; ?>', 
					'category': 'Credit', 
					'price': <?php  echo str_replace(',','.',$cart['product']['Product']['tarif']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
			if (function_exists('affiliate_payment'))
			affiliate_payment($cart['total_price'],$cart['product']['ProductLang'][0]['name'],$is_new_customer_payment, 'Virement');
		}
		if($params['controller'] == 'paymentsepa' && $params['action'] == 'submit'){
			
			$currency = 'EUR';
			if($cart['product']['Product']['country_id'] == 3) $currency =  'CHF';
			if($cart['product']['Product']['country_id'] == 13) $currency =  'CAD';
			?>
            <script>
            dataLayer = [{
				'transactionId': '<?php  echo $cart['id_cart']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$cart['total_price']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>', 
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $cart['product']['Product']['credits']; ?>', 
					'name': '<?php  echo $cart['product']['ProductLang'][0]['name']; ?>', 
					'category': 'Credit', 
					'price': <?php  echo str_replace(',','.',$cart['product']['Product']['tarif']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
			if (function_exists('affiliate_payment'))
			affiliate_payment($cart['total_price'],$cart['product']['ProductLang'][0]['name'],$is_new_customer_payment, 'Virement');
		}
		if($params['controller'] == 'paymentcoupon'){
			$currency = 'EUR';
			if($cart['product']['Product']['country_id'] == 3) $currency =  'CHF';
			if($cart['product']['Product']['country_id'] == 13) $currency =  'CAD';
			?>
            <script>
				dataLayer = [{
				'transactionId': '<?php  echo $cart['id_cart']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$cart['total_price']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>',
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $cart['product']['Product']['credits']; ?>', 
					'name': '<?php  echo $cart['product']['ProductLang'][0]['name']; ?>', 
					'category': 'Credit', 
					'price': <?php  echo str_replace(',','.',$cart['product']['Product']['tarif']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
			if (function_exists('affiliate_payment'))
			affiliate_payment($cart['total_price'],$cart['product']['ProductLang'][0]['name'],$is_new_customer_payment, 'Coupon');
		}
		if($params['controller'] == 'paymentpaypal' && $params['action'] == 'submit'){
			$currency = 'EUR';
			if($cart_datas['product']['Product']['country_id'] == 3) $currency =  'CHF';
			if($cart_datas['product']['Product']['country_id'] == 13) $currency =  'CAD';
			?>
            <script>
				dataLayer = [{
				'transactionId': '<?php  echo $cart_datas['id_cart']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$cart_datas['total_price']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>',
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $cart_datas['product']['Product']['credits']; ?>', 
					'name': '<?php  echo $cart_datas['product']['ProductLang'][0]['name']; ?>', 
					'category': 'Credit', 
					'price': <?php  echo str_replace(',','.',$cart_datas['product']['Product']['tarif']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
			if (function_exists('affiliate_payment'))
			affiliate_payment($cart_datas['total_price'],$cart_datas['product']['ProductLang'][0]['name'],$is_new_customer_payment, 'Paypal');
		}
		/*
		if($params['controller'] == 'paymenthipay' && $params['action'] == 'validation'){
			$currency = 'EUR';
			if($cart_datas['product']['Product']['country_id'] == 3) $currency =  'CHF';
			if($cart_datas['product']['Product']['country_id'] == 13) $currency =  'CAD';
			?>
            <script>
            dataLayer = [{
				'transactionId': '<?php  echo $cart_datas['id_cart']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$cart_datas['total_price']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>',
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $cart_datas['product']['Product']['credits']; ?>', 
					'name': '<?php  echo $cart_datas['product']['ProductLang'][0]['name']; ?>', 
					'category': 'Credit', 
					'price': <?php  echo str_replace(',','.',$cart_datas['product']['Product']['tarif']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
			if (function_exists('affiliate_payment'))
			affiliate_payment($cart_datas['total_price'],$cart_datas['product']['ProductLang'][0]['name'],$is_new_customer_payment, 'CB');
		}
		*/
		if(($params['controller'] == 'paymentstripe' || $params['controller'] == 'paymentbancontact' || $params['controller'] == 'paymentrequest' ) && ($params['action'] == 'submit' || $params['action'] == 'confirm_payment')){
			$currency = 'EUR';
			if($cart_datas['product']['Product']['country_id'] == 3) $currency =  'CHF';
			if($cart_datas['product']['Product']['country_id'] == 13) $currency =  'CAD';
			?>
            <script>
            dataLayer = [{
				'transactionId': '<?php  echo $cart_datas['id_cart']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$cart_datas['total_price']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>',
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $cart_datas['product']['Product']['credits']; ?>', 
					'name': '<?php  echo $cart_datas['product']['ProductLang'][0]['name']; ?>', 
					'category': 'Credit', 
					'price': <?php  echo str_replace(',','.',$cart_datas['product']['Product']['tarif']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
			if (function_exists('affiliate_payment'))
			affiliate_payment($cart_datas['total_price'],$cart_datas['product']['ProductLang'][0]['name'],$is_new_customer_payment, 'CB');
		}
		if($params['controller'] == 'paymentpaypal' && $params['action'] == 'submit_gift'){
			$currency = 'EUR';
			?>
            <script>
				dataLayer = [{
				'transactionId': '<?php  echo $order['GiftOrder']['id']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$order['GiftOrder']['amount']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>',
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $order['GiftOrder']['amount']; ?>', 
					'name': '<?php  echo 'E-Carte'; ?>', 
					'category': 'E-Carte', 
					'price': <?php  echo str_replace(',','.',$order['GiftOrder']['amount']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
		}
		/*
		if($params['controller'] == 'paymenthipay' && $params['action'] == 'validation_gift'){
			$currency = 'EUR';
			?>
            <script>
            dataLayer = [{
				'transactionId': '<?php  echo $order['GiftOrder']['id']; ?>',
				'transactionAffiliation': 'Spiriteo',
				'transactionTotal': <?php  echo str_replace(',','.',$order['GiftOrder']['amount']); ?>,
				'transactionTax': 0,
				'transactionCurrency': '<?php  echo $currency; ?>',
				'transactionShipping': 0, 
				'transactionProducts': [{
					'sku': '<?php  echo $order['GiftOrder']['amount']; ?>', 
					'name': '<?php  echo 'E-Carte'; ?>', 
					'category': 'E-Carte', 
					'price': <?php  echo str_replace(',','.',$order['GiftOrder']['amount']); ?>, 
					'quantity': 1 
				}]
			}];</script>
            <?php
			$payment_validation = true;
		}
		*/
		if($payment_validation && isset($is_new_customer_payment)){
			if($is_new_customer_payment){
				?>
				<script>
					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push({'event': 'is_new'});
				</script>
					<?php

				if(isset($delay_payment)){

					$delaypayment_label = '';
					if($delay_payment < 1){
						$delaypayment_label = 'Premier Achat 0 Jour';
					}
					if($delay_payment >= 1 and $delay_payment < 7){
						$delaypayment_label = 'Premier Achat 7 Jour';
					}
					if($delay_payment >= 7 and $delay_payment < 30){
						$delaypayment_label = 'Premier Achat 30 Jour';
					}
					if($delay_payment >= 30){
						$delaypayment_label = 'Premier Achat 30+ Jour';
					}

					?>
					<script>
						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({
							'event': 'buy_type',
							'eventCategory': 'Consommation',
							'eventAction': 'Nouveau Client',
							'eventLabel': '<?php echo $delaypayment_label; ?>'
						});
					</script>
						<?php	
				}

			}else{
				?>
				<script>
					window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({
						'event': 'buy_type',
						'eventCategory': 'Consommation',
						'eventAction': 'Client Existant',
						'eventLabel': 'Achat'
					});
				</script>
					<?php
			}
		}
		
		if($this->Session->read('Auth.User')){
			$customer = $this->Session->read('Auth.User');
		?>
			<script>
					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push({
						'event': 'Page Loading',
						user_properties: {
							id: '<?=$customer['id'] ?>',
							type: 'customer',
						  }
					});
				</script>
		<?php
		}
		
		if(Configure::read('Site.name') == 'Spiriteo'){
			$code_gtm = 'GTM-W7CCZZ8';
			switch($current_id_domain){
				case "19"://talkappdev.com
					$code_gtm = 'GTM-W7CCZZ8';
					break;
				case "11"://spiriteo.be
					$code_gtm = 'GTM-W7CCZZ8';
					break;
				case "13"://spiriteo.ch
					$code_gtm = 'GTM-W7CCZZ8';
					break;
				case "22"://spiriteo.lu
					$code_gtm = 'GTM-W7CCZZ8';
					break;
				case "29"://spiriteo.ca
					$code_gtm = 'GTM-W7CCZZ8';
					break;	
			}
		}else{
			$code_gtm = 'GTM-W7CCZZ8';
		}
		
		
		?>
        <!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?=$code_gtm ?>');</script>
        <!-- End Google Tag Manager -->
	
	<?php 
		$parts = explode('.', $_SERVER['SERVER_NAME']);
		if(sizeof($parts)) $extension = end($parts); else $extension = '';

		$css_body_login = 'login';
		if($this->Session->read('Auth.User')) $css_body_login = 'logged';
		//$this->Session->write('Auth.User.role', 'agent');
		if($this->Session->read('Auth.User') && $this->Session->read('Auth.User.role') == 'agent')		$css_body_login = 'logged login_agent';
		$userRole = $this->Session->read('Auth.User.role');
//		$userRole= 'client';
//		$userRole= 'agent';
		$css_user_statu = $userRole;
//		echo ".".$userRole."{ display:block !important}";
//		echo ".".$userRole.".flex{ display:flex !important}";
			
	?>
	
        <?php

		

		$current_domain_url = 'http://'.$domain;

        if (isset($this->params['prefix']))
            echo $this->Metronic->getCssJsLinks();

		/*	
	$this->Asset->minCSS(array(
	    '/theme/black_blue/css/bootstrap.css',
	    '/theme/black_blue/css/bootstrap.offcanvas.css',
	    '/theme/black_blue/css/animate.css',
	    '/theme/black_blue/font-awesome/css/font-awesome.min.css',
	    '/theme/black_blue/css/global.css',
	    '/theme/black_blue/css/global_responsive.css',
	    '/theme/black_blue/css/jquery.modal.min.css',
	    '/theme/black_blue/css/jquery.fileuploader.min.css',
	    '/theme/black_blue/css/fileuploader.custom.css',
	    '/theme/black_blue/fonts/fileuploader/font-fileuploader.css'
				));		
		*/	
			
	
	
	$css_ar = array(
	    'bootstrap.css',
	    'bootstrap.offcanvas.css',
	    'animate.css',
	    'font-awesome/css/font-awesome.min.css',
	    'jquery-ui.css?a='.rand(),
	    'common.css?a='.rand(),
	    'common_tablette.css?a='.rand(),
	    'common_mobile.css?a='.rand(),
	    
	   'global.css?a='.rand(),
	    'global_tablette.css?a='.rand(),
	    'global_mobile.css?a='.rand(),

		'geoff_global.css?a='.rand(),
	    'geoff_global_tablette.css?a='.rand(),
	    'geoff_global_mobile.css?a='.rand(),
	
	    'jquery.modal.min.css?a='.rand(),
	    'jquery.fileuploader.min.css',
	    'fileuploader.custom.css',
	    'font-fileuploader.css'
	    );
	
	
	if($userRole == 'client'){
	    array_push($css_ar,   
	    'global.css?a='.rand(),
	    'global_tablette.css?a='.rand(),
	    'global_mobile.css?a='.rand()
		    );
	    }
	
	
	if($userRole == 'agent'){
	    array_push($css_ar,   
	    'agent_global.css?a='.rand(),
	    'agent_tablette.css?a='.rand(),
	    'agent_mobile.css?a='.rand(),
		'geoff_agent_global.css?a='.rand(),
		'geoff_agent_tablette.css?a='.rand(),
		'geoff_agent_mobile.css?a='.rand(),
		'wissem_global.css?a='.rand(),
		'wissem_tablette.css?a='.rand(),
		'wissem_mobile.css?a='.rand(),
		'anis_agent_global.css?a='.rand(),
		'anis_agent_tablette.css?a='.rand(),
		'anis_agent_mobile.css?a='.rand()
	);
	    }
	
	     foreach ($css_ar AS $css)
                echo $this->Html->css("/theme/black_blue/css/".$css);
	
	
	
        if (isset($site_vars['css_links']))
            foreach ($site_vars['css_links'] AS $css)
                echo $this->Html->css($current_domain_url.$css);


        echo $this->fetch('meta');
	echo $this->fetch('css');

	?>
	
	
	<style>
	    
	    
	    
		.tmpPassCont {
			width: 100%;
			height: 100%;
			position: fixed;
			background: #ECECEC;
			color: #333;
			top: 0;
			z-index: 999999;
		}
	</style>
	<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
	<script type="text/javascript">
		
		let isTmpLogged = Cookies.get('isTmpLogged');
/*
		document.addEventListener("DOMContentLoaded", () => {
			if (!isTmpLogged) {
				let divTmp = document.createElement('div');
				document.title = 'dev'
				divTmp.innerHTML = `
					<h1>En cours de développement</h1>
					<input type='text' id='tmpPass' value="talkappdev-9U(&vB(D=@&Ty">
					<button id='tmpSubmit'>Entrer</button>
				`;
				divTmp.classList.add('tmpPassCont');
				document.body.appendChild(divTmp);
				document.getElementById('tmpSubmit').addEventListener('click', () =>{
					// talkappdev-9U(&vB(D=@&Ty
					console.log(window.btoa(document.getElementById('tmpPass').value), 'dGFsa2FwcGRldi05VSgmdkIoRD1AJlR5')
					if (window.btoa(document.getElementById('tmpPass').value) == 'dGFsa2FwcGRldi05VSgmdkIoRD1AJlR5') {
						Cookies.set('isTmpLogged', '1', { expires: 3 });
						window.location.reload();
					}
				})
			}
		});
	</script>
	
	
	<?php echo $this->Html->script('jquery-2.0.3.min'); ?>
	
	</head>
		
	<body ontouchstart="" class="<?=$css_body_login ?> <?=$userRole ?> body_lang_<?php echo $this->Session->read('Config.language'); ?> domain_<?php echo $extension; ?>" style="position: relative;">
	   



<?php
            echo $this->element('header');    // we can pass the view file by use of element function
?> 


	    
	<span id="site_lang" style="display:none"><?php echo $this->Session->read('Config.language'); ?></span>
        <?php
            $user = $this->Session->read('Auth.User');
            if($user){ ?>
                <input type="hidden" name="current_user_id"  value="<?= $user['id']?>">
        <?php } ?>
    	<div id="fb-root"></div>
		<?php

			$facebook_locale = explode(".",$this->Session->check('Config.lc_time')?$this->Session->read('Config.lc_time'):'');
			$facebook_locale = isset($facebook_locale['0'])?$facebook_locale['0']:$facebook_locale;
			$facebook_locale = empty($facebook_locale)?'fr_FR':$facebook_locale;
		?>
	
	    <main>
	
		<?php echo $this->fetch('content'); ?>
		
	    </main>
	
	
	
 		<?php echo $this->element('footer');
	
		?>

		<?php

	/*	
	$this->Asset->minJS(array(	
	//$this->Asset->JS(array(
			'/theme/black_blue/js/jquery-1.10.2.js',
			'/theme/black_blue/js/jquery-ui.js',
			'/theme/black_blue/js/bootstrap.js',
			'/theme/black_blue/js/bootstrap.offcanvas.js',
			'/theme/black_blue/js/moment.min.js',
			'/theme/black_blue/js/custom.js',
			'/theme/black_blue/js/listgroup.min.js',
			'/theme/black_blue/js/cookiebar.min1.js',
			'/theme/black_blue/js/main_public.js',
			'/theme/black_blue/js/jquery.countdown.js',
'/theme/black_blue/js/jquery.fileuploader.min.js',
'/theme/black_blue/js/jquery.modal.js',
'/theme/black_blue/js/font-awesome.js',
'/theme/black_blue/js/nexmo-client/dist/nexmoClient.min.js',
'/theme/black_blue/js/voice_call.js',
//'/theme/black_blue/js/font-awesome.js',
		));	
		*/
	
	$list_js = array(
	    
	//		'jquery-1.10.2.js', chargé + haut
			'/theme/black_blue/js/jquery-ui.js',
			'/theme/black_blue/js/helpers.js?a='.rand(),
			'/theme/black_blue/js/main_public.js?a='.rand(),
			'/theme/black_blue/js/jquery.modal.js?a='.rand(),



		);
/*	
		foreach ($list_js as $file)
		    {
		    	echo $this->Html->script('/theme/black_blue/js/'.$file, array('block' => 'script')); 
		    }
*/
		echo $this->Html->script($list_js, array('block' => 'script'));     
	

	if($this->Session->read('Auth.User')){
			//$this->Asset->JS(array(
			$this->Asset->minJS(array(
				'/theme/black_blue/js/chat.js',
				'/theme/black_blue/js/main.js',
				'/theme/black_blue/js/playsound.js',
				'/theme/black_blue/js/fileuploader.custom.js', 
	
				
				));
			
		}
	
//	
//        if($this->params['controller'] === 'agents' && $this->params['action'] !== 'display')
//            echo $this->Html->script('/theme/black_blue/js/agent_statusmenu3.js');
		
//		if ($this->params['controller'] === 'cards' && $this->params['action'] == 'display') {
//			echo $this->Html->css(['/dist/css/cards.css']);
//			echo $this->Html->script(['/dist/ts/cards.js']);
//		}
		
//        if ($this->Session->read('Auth.User.role') == 'client'){
//            echo $this->Html->script('/theme/black_blue/js/credit.js');
//        }

		echo $this->fetch('script');
		echo $this->fetch('script2');
    	?>
	
	</body>

<?php
	if($params['controller'] == 'pages' || $params['controller'] == 'landing' || $params['controller'] == 'horoscopes'){
		$this->FrontBlock->endCachePageHtml();
	}
?>
