<?php
function ip_details($ip) {
    $json = file_get_contents("http://ipinfo.io/{$ip}");
    $details = json_decode($json);
    return $details;
}

$details = ip_details($ip);
//echo $details->city;    
$country = $details->country; 
//echo $details->org;    
//echo $details->hostname; 
?>