<?php
$url = 'https://api.daotec.com/spiriteo/api';
$port = 443;
$agent_number = 3356;
$curl = null;
$action = '/api/agent/'.$agent_number.'/deconnect';
$postData = array();

  $optionsCurl = array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_URL => $url.$action,
                CURLOPT_PORT => $port
            );

            //On execute curl
            $res = fct_curl_exec($optionsCurl);

var_dump($res);


 function fct_curl_exec($options = array()){
            if(empty($options))
                return false;

            //On attribue les options à curl
            curl_setopt_array($curl, $options);
            $response = curl_exec($curl);
            curl_close($curl);

            return json_decode($response,true);
        }


exit;
?>