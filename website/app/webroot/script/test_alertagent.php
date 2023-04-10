<?php

//test TCHAT ALERT
$postData = array('id_alert' => 44000);
 $optionsCurl = array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_URL => 'https://api.daotec.com/spiriteo/api/agent/3356/alert_agent',
                CURLOPT_PORT => 80
            );
try {
          $ch = curl_init();
          curl_setopt_array($ch, $optionsCurl);
            $response = curl_exec($ch);
            curl_close($ch);

            $ret =  json_decode($response,true);
  var_dump($ret);
 } catch (\Stripe\Error\Base $e) {
							var_dump($e);
						}
                       

?>